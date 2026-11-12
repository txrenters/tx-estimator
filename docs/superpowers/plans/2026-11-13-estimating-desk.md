# Estimating Desk Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Recreate the Crystal Creek Estimating Desk (same look, screens, copy and pricing math) as a multi-company Laravel app with roles, approvals, revisions, company settings/logos and an admin overview.

**Architecture:** Server-rendered Inertia v3 + Vue 3 pages inside a new `DeskLayout` that ports the original stylesheet. All business data hangs off `companies`; a `CurrentCompany` service (session-backed for admins, pinned for managers/estimators) plus Policies enforce scoping server-side. Pricing math lives twice — `App\Support\Pricing` (authoritative, stored totals) and `resources/js/lib/pricing.ts` (live UI) — both pinned to one shared JSON fixture.

**Tech Stack:** Laravel 13, Fortify, Inertia v3, Vue 3 + TS, Tailwind v4 (only for layout utilities; desk look comes from ported CSS), Wayfinder, Pest 5, Vitest (new dev dep), SQLite.

**Spec:** `docs/superpowers/specs/2026-11-13-estimating-desk-design.md`. Reference assets: `docs/superpowers/reference/crystal-creek/` (`page-1XroWPtX.js` = source of truth for seed data, copy, pricing; `index.D_o7GGtd.css` = source of truth for styling; `cc-builder.png`, `sections.json`).

**Repo note:** the project is not a git repository. "Checkpoint" steps mean: run the full checks (`php artisan test --compact`, `npm run types:check`, `vendor/bin/pint --dirty`, `graphify update .`). Do not `git init`/commit unless the user asks.

## Global Constraints

- Roles exactly: `superadmin`, `admin`, `manager`, `estimator`. Managers/estimators have a non-null `company_id`; admins/superadmins have `company_id = null`.
- Statuses stored as `draft|pending|approved|declined`, displayed as `Draft`, `Pending approval`, `Approved`, `Declined`.
- Service types (exact, ordered): `HVAC repair`, `HVAC replacement`, `Pest plan`, `Pest treatment`, `Roofing`, `Siding`, `Make-ready`, `Cleaning`, `Plumbing`, `General maintenance`.
- Catalog groups: HVAC = HVAC repair/replacement; Pest Control = Pest plan/treatment; Roofing = Roofing; Maintenance = Siding, Make-ready, Cleaning, Plumbing, General maintenance (matches original counts 3/5/1/5).
- Pricing defaults: `{markup:30, discount:0, trip:0, authorization:250, manager:1000, target:25}`.
- Companies (name, short, color, tone): Crystal Creek Air/Air/#287f9d/#e9f4f8; Crystal Creek Pest Control/Pest Control/#39835a/#eaf4ed; Crystal Creek Roofing/Roofing/#9f6336/#fbf0e4; Texas Home Maintenance Pros/Maintenance/#62579b/#efedf8. Location "Greater Houston, TX", currency USD.
- Estimate number: `EST-` + 6 uppercase hex chars, unique.
- Money: USD, `Intl.NumberFormat('en-US',{style:'currency',currency:'USD'})`; dates `en-US` short month, `America/Chicago`.
- UI copy is copied verbatim from the original (`sections.json` / bundle) wherever the screen exists there.
- Registration stays disabled. No data import from the live site.
- Every server query for company data goes through `CurrentCompany` + policies; never trust a `company_id` from the request without authorizing it.

## Review Focus

1. **Cross-company ID tampering** — a manager/estimator hitting `/estimates/{id}` (edit, update, status, proposal, duplicate, revisions), `/templates/{id}` or `/compare?ids[]=` with another company's id gets 403/404, never data. Tests in Tasks 6, 7, 10, 11.
2. **Admin in "All companies" creating things** — creating an estimate/template with no current company must require an explicit, valid `company_id`; the builder shows the Company select. Test in Task 7.
3. **Zero vs missing prices** — `fixed: 0` is a real sell price (not "no fixed price"), `fixed: null` falls back to markup; qty 0 lines are ignored for the "incomplete" flag; an estimate with no lines totals $0.00 with margin "—". Fixture cases in Task 4.
4. **Deactivated or orphaned users** — an inactive user cannot log in or use an existing session; a company with users/estimates cannot be deleted (FK restrict). Tests in Tasks 2 and 13.
5. **Logo uploads** — non-image or >2MB files are rejected; logos are only ever rendered via `<img>`, replacing a logo deletes the old file. Test in Task 12.

---

## File Structure

Backend
- `app/Enums/Role.php`, `app/Enums/EstimateStatus.php`
- `config/estimating.php` — service types, catalog groups, pricing defaults.
- `app/Models/Company.php`, `ServiceTemplate.php`, `Estimate.php`, `EstimateRevision.php`, `EstimateActivity.php`; `User.php` (modify)
- `app/Support/CurrentCompany.php`, `app/Support/Pricing.php`, `app/Support/EstimateNumber.php`
- `app/Http/Middleware/EnsureUserIsActive.php`, `HandleInertiaRequests.php` (modify)
- `app/Policies/EstimatePolicy.php`, `ServiceTemplatePolicy.php`, `CompanyPolicy.php`, `UserPolicy.php`
- Controllers in `app/Http/Controllers/`: `CurrentCompanyController`, `EstimateController`, `EstimateStatusController`, `EstimateProposalController`, `ServiceTemplateController`, `CompareController`, `PricingDefaultsController`, `CompanySettingsController`, `CompanyController`, `UserController`, `OverviewController`
- Form requests in `app/Http/Requests/`: `SaveEstimateRequest`, `SaveTemplateRequest`, `PricingDefaultsRequest`, `CompanySettingsRequest`, `StoreCompanyRequest`, `SaveUserRequest`
- Actions in `app/Actions/Estimates/`: `SaveEstimate`, `TransitionEstimate`
- `routes/web.php` (rewrite)
- Migrations: `create_companies_table`, `add_role_and_company_to_users_table`, `create_service_templates_table`, `create_estimates_table`, `create_estimate_revisions_table`, `create_estimate_activities_table`
- Seeders: `CompanySeeder`, `ServiceTemplateSeeder` (data in `database/seeders/data/templates.php`), `DatabaseSeeder` (modify)

Frontend
- `resources/css/desk.css` (port of original CSS), imported from `resources/css/app.css`
- `resources/js/layouts/DeskLayout.vue`; `resources/js/components/desk/`: `DeskSidebar.vue`, `DeskTopbar.vue`, `CompanyMark.vue`, `StatTile.vue`, `StatusPill.vue`, `LineItemsEditor.vue`, `PricingFields.vue`, `PricingSummary.vue`, `RevisionPanel.vue`, `TemplateCatalog.vue`
- `resources/js/lib/pricing.ts`, `resources/js/lib/format.ts`, `resources/js/types/desk.ts`
- Pages: `estimates/Index.vue`, `estimates/Builder.vue`, `estimates/Proposal.vue`, `templates/Index.vue`, `templates/Edit.vue`, `Compare.vue`, `Calculator.vue`, `PricingDefaults.vue`, `Overview.vue`, `company/Settings.vue`, `companies/Index.vue`, `users/Index.vue`
- Delete: `pages/Dashboard.vue`, `pages/Welcome.vue` (and their routes/tests)
- Tests: `tests/Feature/Desk/*.php`, `tests/fixtures/pricing-cases.json`, `tests/Unit/PricingTest.php`, `resources/js/lib/pricing.test.ts`

---

### Task 1: Roles, companies and users schema

**Files:**
- Create: `app/Enums/Role.php`, `app/Models/Company.php`, `database/migrations/2026_11_13_000001_create_companies_table.php`, `..._000002_add_role_and_company_to_users_table.php`, `database/factories/CompanyFactory.php`, `database/seeders/CompanySeeder.php`, `config/estimating.php`
- Modify: `app/Models/User.php`, `database/factories/UserFactory.php`, `database/seeders/DatabaseSeeder.php`
- Test: `tests/Feature/Desk/RolesTest.php`

**Interfaces:**
- Produces: `enum Role: string { Superadmin='superadmin'; Admin='admin'; Manager='manager'; Estimator='estimator'; label(): string; isGlobal(): bool }`
- `companies`: id, name, slug (unique), short_name, location, currency (default `USD`), color, tone, logo_path (nullable), default_scope (text, nullable), default_terms (text, nullable), pricing_defaults (json), timestamps.
- `users` adds: `role` (string, default `estimator`), `company_id` (nullable FK → companies, `restrictOnDelete`), `is_active` (bool, default true).
- `Company`: casts `pricing_defaults` array; `users()`, `logoUrl(): ?string` (`Storage::disk('public')->url`).
- `User`: casts `role` → `Role`; `company()`; `isGlobal(): bool` (superadmin|admin); `canApprove(): bool` (not estimator); add `role`,`company_id`,`is_active` to Fillable.
- `UserFactory` states: `superadmin()`, `admin()`, `manager(Company $c)`, `estimator(Company $c)`, `inactive()`.
- `config('estimating.service_types')` (list), `config('estimating.catalog_groups')` (`['HVAC'=>[...], 'Pest Control'=>[...], 'Roofing'=>[...], 'Maintenance'=>[...]]`), `config('estimating.pricing_defaults')`.
- `CompanySeeder` seeds the four Global-Constraint companies with default pricing; `DatabaseSeeder` seeds companies, templates (Task 5), a superadmin from `env('DESK_SUPERADMIN_EMAIL')`/`env('DESK_SUPERADMIN_PASSWORD')` (skip if unset), and in `local` env a demo manager + estimator for Crystal Creek Air.

- [ ] **Step 1: Write failing tests** — `seeder creates the four companies with original colors` (assert 4 rows; Air color `#287f9d`; pricing_defaults equals config); `manager requires a company` (DB: deleting a company that has a user throws `QueryException`); `global roles` (`User::factory()->admin()->make()->isGlobal()` true, estimator false; `canApprove` false only for estimator).
- [ ] **Step 2: Run** `php artisan test --compact --filter=RolesTest` → FAIL (class/table missing).
- [ ] **Step 3: Implement** the enum, config, migrations, models, factory states, seeders as specified.
- [ ] **Step 4: Run** `php artisan test --compact --filter=RolesTest` → PASS; `php artisan migrate:fresh --seed` succeeds.
- [ ] **Step 5: Checkpoint.**

### Task 2: Current company, active users, shared props

**Files:**
- Create: `app/Support/CurrentCompany.php`, `app/Http/Middleware/EnsureUserIsActive.php`, `app/Http/Controllers/CurrentCompanyController.php`, `resources/js/types/desk.ts`
- Modify: `app/Http/Middleware/HandleInertiaRequests.php`, `bootstrap/app.php`, `app/Providers/FortifyServiceProvider.php` (reject inactive users at login via `Fortify::authenticateUsing`), `routes/web.php`
- Test: `tests/Feature/Desk/CurrentCompanyTest.php`

**Interfaces:**
- `CurrentCompany` (scoped singleton): `get(): ?Company` — pinned users always their company; global users the session `current_company_id` company or null (= All companies); `set(?Company $c): void` (global users only, else `AuthorizationException`); `companyForWrite(?int $requestedId): Company` — pinned → own company; global → current company, else the requested id (validated exists), else `ValidationException` on `company_id`; `visibleCompanyIds(): ?array` (null = all).
- Route `POST /current-company` name `current-company.update`, body `company_id` (nullable int). Redirects back.
- `EnsureUserIsActive` on the `web` auth group: inactive → logout, redirect to `login` with error `Your account has been deactivated.`
- Shared props (`desk`): `{ user: {id,name,email,role,roleLabel,companyId}, companies: CompanySummary[] (all for global users, own only otherwise), currentCompany: CompanySummary|null, can: {switchCompany, viewOverview, manageUsers, manageCompany, approve, editCatalog} , serviceTypes: string[], statuses: {value,label}[] }` where `CompanySummary = {id,name,short,color,tone,location,logoUrl}`. Remove `sidebarOpen`.
- `desk.ts` exports these TS types.

- [ ] **Step 1: Write failing tests** — `admin can switch company and back to all`; `manager cannot switch company` (403); `manager's current company is always their own even with a forged session value`; `inactive user is logged out`; `inactive user cannot log in`; `shared props list only own company for estimator` (`assertInertia` on `desk.companies` count 1).
- [ ] **Step 2: Run** `--filter=CurrentCompanyTest` → FAIL.
- [ ] **Step 3: Implement** as above.
- [ ] **Step 4: Run** → PASS.
- [ ] **Step 5: Checkpoint.**

### Task 3: Desk shell and design port

**Files:**
- Create: `resources/css/desk.css`, `resources/js/layouts/DeskLayout.vue`, `components/desk/DeskSidebar.vue`, `DeskTopbar.vue`, `CompanyMark.vue`, `StatTile.vue`, `StatusPill.vue`, `resources/js/lib/format.ts`, `public/favicon.svg` (copy original gem)
- Modify: `resources/css/app.css`, `resources/js/app.ts` (default layout → `DeskLayout`; `auth/*` stays `AuthLayout`; `settings/*` → `[DeskLayout, SettingsLayout]`), `resources/views/app.blade.php` (title "Crystal Creek Estimating Desk" fallback, favicon), `routes/web.php` (`/` → redirect `estimates.index` for auth, `login` for guests), auth layout styling to the desk palette
- Delete: `pages/Dashboard.vue`, `pages/Welcome.vue`, `tests/Feature/DashboardTest.php` (update any test referencing `dashboard` route; Fortify `home` config → `/estimates`)
- Test: `tests/Feature/Desk/ShellTest.php`

**Interfaces:**
- `desk.css`: port the original stylesheet's rules (tokens, `.shell`, `.sidebar`, `.wordmark`, `.side-group`, `.company-dot`, `.sidebar-foot`, `.workspace-avatar`, `.topbar`, `.crumb`, `.private`, `.content`, `.page-heading`, `.panel`, `.section`, `.stats`, tables, buttons `.button/.secondary/.text-button`, inputs, `.attention`, `.total-row`, `.margin-row`, responsive/mobile-menu rules) — scoped under `.desk` root class so starter-kit settings components keep working. Keep class names identical to the original so markup can mirror it.
- `DeskLayout` props via `defineOptions({ layout: ... })` pages pass `{ crumb: string }`; renders sidebar, topbar (`Workspace › {crumb}`, right side: current company name or `All companies` with the green dot, then user menu reusing `UserMenuContent`), `<slot/>`, footer `Crystal Creek Estimating Desk` / `{location} • USD`.
- Sidebar WORKSPACE items in order: (global only) `Overview`; `Estimates`, `Service templates`, `Compare estimates`, `Calculator`, `Pricing defaults`; (manageUsers) `Users`; (manageCompany) `Company settings`; (global) `Companies`. COMPANIES group only when `can.switchCompany`: `All companies` + each company (CompanyMark + name), active one highlighted, click posts `current-company.update`. Footer badge: pinned users → their company mark/name/location; global → `CC` avatar, `Service operations`, `Greater Houston, TX`. Mobile menu toggle as original.
- `CompanyMark` props `{company: CompanySummary, size?: 'dot'|'badge'}` — logo `<img>` if `logoUrl`, else colored dot / initials badge.
- `format.ts`: `money(n:number):string`, `date(iso:string):string` per Global Constraints.

- [ ] **Step 1: Write failing tests** — `guest visiting / is redirected to login`; `user visiting / is redirected to estimates`; `login redirects to estimates`.
- [ ] **Step 2: Run** `--filter=ShellTest` → FAIL.
- [ ] **Step 3: Implement**; temporary `estimates.index` route rendering an empty `estimates/Index.vue` with the page heading so the shell is visible (Task 6 fills it).
- [ ] **Step 4: Run** tests → PASS; `npm run build` OK; visually compare `/estimates` and `/settings/profile` against `cc-builder.png` / the live site (sidebar, topbar, typography, colours) at 1440px and 390px wide.
- [ ] **Step 5: Checkpoint.**

### Task 4: Pricing engine (PHP + TS parity)

**Files:**
- Create: `app/Support/Pricing.php`, `resources/js/lib/pricing.ts`, `tests/fixtures/pricing-cases.json`, `tests/Unit/PricingTest.php`, `resources/js/lib/pricing.test.ts`
- Modify: `package.json` (add `vitest` dev dep, script `"test:js": "vitest run"`)

**Interfaces:**
- Line shape: `{name:string, qty:number, unit:string, labor:number, materials:number, fixed:number|null}`; pricing shape = Global Constraints defaults keys.
- PHP: `Pricing::calculate(array $lines, array $pricing): array` → `['cost','subtotal','discount','total','incomplete','margin']` (floats; margin float, 0 when total ≤ 0). TS: `calculate(lines, pricing): PricingResult` same keys.
- Algorithm (exact port of the original `N`, `P`, `F`):
```
round2(x) = Math.round((x + 2**-52) * 100) / 100
lineSell(l, markup) = round2(l.qty * (l.fixed ?? (l.labor + l.materials) * (1 + markup/100)))
cost = round2(Σ round2(l.qty * (l.labor + l.materials)))
subtotal = round2(Σ lineSell(l, markup))
discount = round2(subtotal * discount/100)
total = round2(subtotal - discount + trip)
incomplete = any l: l.qty > 0 && l.labor + l.materials == 0
margin = total > 0 ? (total - cost) / total * 100 : 0
```
  PHP must use the same rounding (`round(($x + PHP_FLOAT_EPSILON) * 100) / 100` with `PHP_ROUND_HALF_UP` semantics matching JS `Math.round` for positives; document that negative inputs are rejected by validation).
- Also export `flags(result, pricing)` (TS + PHP `Pricing::flags`) → `{belowTarget: !incomplete && total>0 && margin<target, aboveAuthorization: total>authorization, aboveManagerReview: total>manager}`.

- [ ] **Step 1: Write fixture** with ≥8 cases incl.: empty lines; single fixed 1800 line; markup 30 on labor 100 + materials 50 qty 2 (= 390.00); fixed `0` (sell 0, not markup); qty 0 zero-cost line (not incomplete); discount 10 + trip 75; rounding edge (labor 0.105 qty 3); total below target margin. Each with expected outputs computed by running the original JS function (copy `N/P/F` into a throwaway node script to produce them).
- [ ] **Step 2: Write tests** — PHP `PricingTest` and `pricing.test.ts` iterate the fixture and assert all keys (margin to 6 dp) and flags.
- [ ] **Step 3: Run** `php artisan test --compact --filter=PricingTest` and `npm run test:js` → FAIL.
- [ ] **Step 4: Implement** both.
- [ ] **Step 5: Run** both → PASS. Checkpoint.

### Task 5: Service templates + pricing defaults

**Files:**
- Create: migration `create_service_templates_table`, `app/Models/ServiceTemplate.php`, factory, `database/seeders/ServiceTemplateSeeder.php`, `database/seeders/data/templates.php`, `app/Policies/ServiceTemplatePolicy.php`, `app/Http/Controllers/ServiceTemplateController.php`, `PricingDefaultsController.php`, `app/Http/Requests/SaveTemplateRequest.php`, `PricingDefaultsRequest.php`, `components/desk/LineItemsEditor.vue`, `PricingFields.vue`, `TemplateCatalog.vue`, pages `templates/Index.vue`, `templates/Edit.vue`, `PricingDefaults.vue`
- Test: `tests/Feature/Desk/TemplatesTest.php`, `PricingDefaultsTest.php`

**Interfaces:**
- `service_templates`: id, company_id (FK cascade), key (nullable, e.g. `t1`), title, service_type, scope (text), terms (text, nullable), lines (json), timestamps.
- `templates.php` returns the 14 templates transcribed exactly from the bundle `j=[...]` array (key, title, company index → company by seed order, service, scope, lines with `k(name, unit='job', fixed=null)` defaults qty 1/labor 0/materials 0, terms). Include `p0–p2` generated tiers with their tier-specific terms (10/15/20%).
- Routes: `GET /templates` `templates.index` (filters `service`, and `company` for global users in All companies); `GET /templates/create`, `POST /templates`, `GET /templates/{template}/edit`, `PUT /templates/{template}`, `DELETE /templates/{template}`. `GET /pricing-defaults` `pricing-defaults.edit`, `PUT /pricing-defaults` `pricing-defaults.update`.
- Policy: view/use → anyone who can see the company; create/update/delete → manager of that company or global. Pricing defaults update → same; requires a current company (global user in All companies sees the page with a "Choose a company" notice and disabled form — *builder may instead apply to all companies; spec says per company, so do not*).
- Validation: lines array min 1; each `name` required string ≤200, `qty` numeric ≥0, `unit` required ≤20, `labor`/`materials` numeric ≥0, `fixed` nullable numeric ≥0. Pricing defaults: all six numeric ≥0, `discount` and `markup` ≤1000, `target` ≤100.
- Templates page copy and cards exactly as original ("SERVICE CATALOG", "Service templates", "Reuse scopes and update unit rates as your pricing changes.", "New template", cards with service tag, title, scope, `Edit rates` / `Use template`). Estimators don't see `New template`/`Edit rates`. `Use template` → `estimates.create?template={id}` (Task 7).
- `TemplateCatalog` (used on Estimates home): "SERVICE CATALOG / Start from a template / View all templates" with group rows and `{n} templates`.
- `LineItemsEditor` props `{modelValue: Line[], markup: number, readonly?: boolean}` — columns `Item, Qty, Unit, Labor / unit, Materials / unit, Fixed sell / unit, Sell total`, remove-row button, `Add line`; placeholder `Work or material`.
- `PricingFields` props `{modelValue: Pricing}` — six fields with `%`/`$` prefixes, labels from Global Constraints order: Cost markup, Subtotal discount, Trip charge, Authorization limit, Manager review threshold, Target gross margin.

- [ ] **Step 1: Write failing tests** — `seeder creates 14 templates in the bundle's companies` (Air 3, Pest 5, Roofing 1, Maintenance 5; `m1` siding in Texas Home Maintenance Pros; `t1` first line fixed 1800); `estimator sees own company templates only`; `estimator cannot update a template` (403); `manager cannot edit another company's template` (403); `manager updates rates`; `invalid line rejected` (negative qty → error on `lines.0.qty`); `manager saves pricing defaults` (stored on company); `estimator cannot update pricing defaults`; `admin in all companies cannot save defaults without choosing a company`.
- [ ] **Step 2: Run** → FAIL.
- [ ] **Step 3: Implement.**
- [ ] **Step 4: Run** → PASS; compare templates page against the live site.
- [ ] **Step 5: Checkpoint.**

### Task 6: Estimates model, policy and Estimates home

**Files:**
- Create: migration `create_estimates_table`, `app/Enums/EstimateStatus.php`, `app/Models/Estimate.php`, factory, `app/Support/EstimateNumber.php`, `app/Policies/EstimatePolicy.php`, `app/Http/Controllers/EstimateController.php` (index only here), page `estimates/Index.vue`
- Test: `tests/Feature/Desk/EstimatesIndexTest.php`, `EstimatePolicyTest.php`

**Interfaces:**
- `enum EstimateStatus: string { Draft='draft'; Pending='pending'; Approved='approved'; Declined='declined'; label(): string }`.
- `estimates`: id, company_id (FK restrict), user_id (creator, FK restrict), number (unique), title, status (default draft), service_type, client (nullable), address (nullable), lines (json), pricing (json), scope/terms/notes (text nullable), cost/subtotal/discount/total (decimal 12,2), margin (decimal 8,4 nullable — null when incomplete or total 0), incomplete (bool), approved_by (nullable FK users), approved_at (nullable), timestamps. Index (company_id, status).
- `Estimate::scopeVisibleTo(Builder $q, User $u, ?Company $current)`; `applyTotals(): void` (fills totals from `Pricing::calculate`).
- `EstimateNumber::generate(): string` — `EST-` + 6 uppercase hex, retry on collision.
- `EstimatePolicy`: `view` (same company or global); `update` (global; manager of company; estimator if creator and status in draft|pending|declined); `delete` (global/manager; estimator if creator and draft); `transition(User, Estimate, string $action)` — `submit` (can update and status draft), `withdraw` (creator or approver and status pending), `approve`/`decline` (`canApprove()` + same company/global, status pending).
- `GET /estimates` `estimates.index` props: `stats {total, pending, approvedValue, templates}` for current scope; `estimates` (paginated 25: id, number, title, company short/color, status, total, updated_at, flags); `filters {company, service, status}`; `catalog` groups with counts. Page reproduces original: eyebrow `GREATER HOUSTON OPERATIONS` (pinned users: company location uppercased), heading `Estimating desk` (or current company name when one is selected, as original), description, `New estimate`, four tiles (`Total estimates/Estimates in this view`, `Pending approval/Awaiting a decision`, `Approved value/Total approved estimates`, `Service templates/Reusable scopes and rates`), `Estimates` panel with `Compare` button, filters (company filter only when global & All companies), table `Estimate | Company | Status | Total | Updated`, empty state `No estimates yet` / `Start with a service template or build an estimate from scratch.` / `Browse templates`, footer `{n} estimates · USD`, and `TemplateCatalog`.

- [ ] **Step 1: Write failing tests** — policy matrix (dataset over role × same/other company × status × creator for update/delete/approve); `estimator sees all estimates of own company and none of others`; `admin all-companies sees everything, filtered by company param`; `stats reflect scope` (approved value sums only approved totals); `filters by status and service`.
- [ ] **Step 2: Run** → FAIL.
- [ ] **Step 3: Implement.**
- [ ] **Step 4: Run** → PASS.
- [ ] **Step 5: Checkpoint.**

### Task 7: Estimate builder (create, edit, save, duplicate, delete)

**Files:**
- Create: `app/Actions/Estimates/SaveEstimate.php`, `app/Http/Requests/SaveEstimateRequest.php`, `components/desk/PricingSummary.vue`, page `estimates/Builder.vue`
- Modify: `EstimateController` (create, store, edit, update, destroy, duplicate), `routes/web.php`
- Test: `tests/Feature/Desk/EstimateBuilderTest.php`

**Interfaces:**
- Routes: `GET /estimates/create` (`?template=`, `?company=` for global users), `POST /estimates`, `GET /estimates/{estimate}/edit`, `PUT /estimates/{estimate}`, `DELETE /estimates/{estimate}`, `POST /estimates/{estimate}/duplicate`.
- `SaveEstimate::handle(User $by, ?Estimate $estimate, array $data, Company $company): Estimate` — fills fields, recomputes totals (`applyTotals`), applies status rules (estimator saving own Declined → Draft; anyone editing Approved → Draft; Pending edited by approver stays Pending; status field itself is never mass-assigned — changes go through Task 8), records revision (Task 9 hook: call `$estimate->recordRevision($by)` — implement as no-op stub here, filled in Task 9), all in a transaction.
- Create prefill: from template (title, service, scope, terms — template's text or company default if empty — lines) else one blank line `{name:'',qty:1,unit:'job',labor:0,materials:0,fixed:null}`; pricing = company `pricing_defaults`; service default for company index as original (`[HVAC repair, Pest plan, Roofing, General maintenance]` by company seed order — store as `companies.default_service_type`? *No:* derive from first template service of that company, falling back to `HVAC repair`).
- Validation: title required (message `Enter an estimate title before saving.`), service_type in list, client/address ≤255, lines as Task 5, pricing as Task 5, scope/terms/notes ≤10000, company_id (global users only) exists.
- Builder page mirrors original: `Back to estimates`, number eyebrow, heading (`New estimate` or title), description, `Proposal` (disabled until saved) + `Save estimate`; panels `Estimate details` (Estimate title placeholder `e.g. Evaporator coil replacement`, Approval status shown read-only as `StatusPill` with workflow buttons from Task 8, Company select (global users; disabled for pinned), Service type, Customer or owner, Service address placeholder `Street, city, ZIP`), `Line items` (+ copy `Markup applies to unit costs. A fixed sell price overrides markup for that line.`), `Pricing adjustments`, `Scope, terms and notes` (Proposal scope, Proposal terms and exclusions, Internal notes placeholder `Approval details, job conditions, vendor quotes…`); right column sticky `PricingSummary` using `pricing.ts` live (Direct cost, Line subtotal, Discount `−$x`, Trip charge, Estimate total, Gross margin `—`/`x.x%`, `Target n%` + ` · Below target`, attention notes for incomplete / `Above the $250.00 authorization limit.` / manager review text from bundle, footer note `Authorization flags are advisory. Margin is before overhead and tax.`). Read-only rendering when `!can.update`. Duplicate creates `"{title} (copy)"`, Draft, new number, creator = current user.

- [ ] **Step 1: Write failing tests** — `estimator creates estimate in own company even if company_id forged`; `admin in all companies must supply company_id` (error); `server totals ignore client-sent totals` (post bogus `total`, stored total equals Pricing); `create from template prefills lines and template terms, falling back to company default terms`; `estimator cannot edit others' estimate`; `editing approved estimate returns it to draft`; `estimator saving own declined estimate returns to draft`; `duplicate copies lines with (copy) title as draft`; `estimator cannot delete pending estimate`; `cannot open another company's estimate` (403).
- [ ] **Step 2: Run** → FAIL.
- [ ] **Step 3: Implement.**
- [ ] **Step 4: Run** → PASS; manual check builder vs `cc-builder.png` side by side.
- [ ] **Step 5: Checkpoint.**

### Task 8: Approval workflow and activity log

**Files:**
- Create: migration `create_estimate_activities_table`, `app/Models/EstimateActivity.php`, `app/Actions/Estimates/TransitionEstimate.php`, `app/Http/Controllers/EstimateStatusController.php`
- Modify: `Estimate` (`activities()`), `SaveEstimate` (log `reverted_to_draft` when status auto-changes), `estimates/Builder.vue` (workflow buttons)
- Test: `tests/Feature/Desk/ApprovalTest.php`

**Interfaces:**
- `estimate_activities`: id, estimate_id (cascade), company_id, user_id, action (`submitted|withdrawn|approved|declined|reverted_to_draft`), from_status, to_status, created_at.
- Route `POST /estimates/{estimate}/status` `estimates.status`, body `action` in `submit|withdraw|approve|decline`.
- `TransitionEstimate::handle(User $by, Estimate $e, string $action): Estimate` — authorizes via `EstimatePolicy::transition`, sets status, `approved_by/approved_at` on approve (cleared on any other transition), logs activity, records revision.
- Builder buttons by state: Draft → `Submit for approval` (creator/approver); Pending → `Approve`, `Decline` (approvers), `Withdraw` (creator); others none. Flash toasts via existing `flashToast`.

- [ ] **Step 1: Write failing tests** — `estimator submits draft → pending, activity logged`; `estimator cannot approve` (403); `manager approves own-company pending, sets approved_by`; `manager cannot approve other company` (403); `admin approves any`; `cannot approve a draft` (403); `withdraw returns to draft and clears nothing else`; `invalid action` (422).
- [ ] **Step 2: Run** → FAIL.
- [ ] **Step 3: Implement.**
- [ ] **Step 4: Run** → PASS.
- [ ] **Step 5: Checkpoint.**

### Task 9: Revision history

**Files:**
- Create: migration `create_estimate_revisions_table`, `app/Models/EstimateRevision.php`, `components/desk/RevisionPanel.vue`
- Modify: `Estimate::recordRevision(User $by): EstimateRevision`, `EstimateController::edit` (props `revisions`: latest 50 `{id, created_at, user name, status, total, snapshot}`), `estimates/Builder.vue` (`History` button opening panel)
- Test: `tests/Feature/Desk/RevisionsTest.php`

**Interfaces:**
- `estimate_revisions`: id, estimate_id (cascade), user_id, snapshot (json: title, status, service_type, client, address, lines, pricing, scope, terms, notes, total), created_at.
- Panel copy as in the bundle (`History`, `Revision history`, `Load revision`); loading copies snapshot fields (not status/number) into the unsaved form and shows a notice; saving goes through normal update rules.

- [ ] **Step 1: Write failing tests** — `each save records a revision with snapshot`; `approve and decline record revisions`; `edit page exposes revisions newest first`; `revisions of another company's estimate are not exposed` (403 on edit).
- [ ] **Step 2: Run** → FAIL. **Step 3: Implement.** **Step 4: Run** → PASS. **Step 5: Checkpoint.**

### Task 10: Proposal page

**Files:**
- Create: `app/Http/Controllers/EstimateProposalController.php`, page `estimates/Proposal.vue` (layout: none — standalone print page)
- Test: `tests/Feature/Desk/ProposalTest.php`

**Interfaces:**
- `GET /estimates/{estimate}/proposal` `estimates.proposal` (policy `view`). Props: company (name, logoUrl, location), estimate public fields, line items with computed sell/unit and sell total only, subtotal, discount, trip, total, scope, terms, date.
- Layout/copy follows the original's proposal view in the bundle (search `Proposal` in `page-1XroWPtX.js`); `Print` button calls `window.print()`; print CSS hides controls.
- Must not include labor, materials, cost, margin, internal notes.

- [ ] **Step 1: Write failing tests** — `proposal shows sell prices and hides costs and notes` (assertInertia props lack `notes`, lines lack `labor`/`materials`); `proposal includes company logo url`; `other company's proposal is forbidden`.
- [ ] **Step 2–5:** run → FAIL, implement, run → PASS, checkpoint.

### Task 11: Compare estimates and Calculator

**Files:**
- Create: `app/Http/Controllers/CompareController.php`, pages `Compare.vue`, `Calculator.vue`
- Test: `tests/Feature/Desk/CompareTest.php`

**Interfaces:**
- `GET /compare?ids[]=` `compare.index` — options: visible saved estimates (current scope); selected: up to 3 visible ids (extra/foreign ids silently dropped). Rows exactly as original: Company, Service, Approval, Direct cost, Markup, Discount, Trip charge, Total, Gross margin (`Needs pricing` when incomplete or total 0, else `x.x%`), Margin target, Options (`Open`, `Duplicate`). Copy: `PRICING REVIEW`, `Compare estimates`, `Compare up to three saved estimates. Duplicate an estimate to price another option.`, `Choose estimates`, empty `Save an estimate to start a comparison.`
- `GET /calculator` `calculator` — client-only: `QUICK TOOLS`, `Quantity calculator`, `Calculate a line total with optional waste or overage.`, fields Base quantity, Unit cost ($), Waste or overage (%); outputs Adjusted quantity = qty × (1 + waste/100), Calculated total = round2(adjusted × unit cost) (verify formula in bundle).

- [ ] **Step 1: Write failing tests** — `compare caps at three`; `compare drops other company's ids`; `calculator page renders`.
- [ ] **Step 2–5:** run → FAIL, implement, run → PASS, checkpoint.

### Task 12: Company settings, logos and Companies admin

**Files:**
- Create: `app/Policies/CompanyPolicy.php`, `app/Http/Controllers/CompanySettingsController.php`, `CompanyController.php`, `app/Http/Requests/CompanySettingsRequest.php`, `StoreCompanyRequest.php`, pages `company/Settings.vue`, `companies/Index.vue`
- Test: `tests/Feature/Desk/CompanySettingsTest.php`

**Interfaces:**
- `GET /company/settings`, `POST /company/settings` (multipart; Inertia form with `_method` not needed) `company-settings.*` — acts on `CurrentCompany` (global users must have one selected). Fields: name, short_name, location, color (hex), logo (nullable `image|mimes:png,jpg,jpeg,webp,svg|max:2048`), remove_logo (bool), default_scope, default_terms. Stores logo at `public` disk `logos/{company_id}/{uuid}.{ext}`, deletes previous. Run `php artisan storage:link` in setup notes.
- `GET /companies`, `POST /companies` (global only): list with counts (users, estimates), create (name, short_name, location, color; slug auto; pricing defaults from config). No delete in UI.
- `CompanyPolicy`: `update` → global or manager of it; `create`/`viewAny` → global.

- [ ] **Step 1: Write failing tests** (use `Storage::fake('public')`, `UploadedFile::fake()->image('logo.png')`) — `manager uploads logo, url shared in desk props`; `replacing logo deletes old file`; `pdf rejected`; `3MB image rejected`; `estimator cannot open settings` (403); `manager cannot edit other company`; `admin creates company with default pricing`; `manager cannot create company`.
- [ ] **Step 2–5:** run → FAIL, implement, run → PASS, checkpoint.

### Task 13: User management

**Files:**
- Create: `app/Policies/UserPolicy.php`, `app/Http/Controllers/UserController.php`, `app/Http/Requests/SaveUserRequest.php`, page `users/Index.vue`
- Test: `tests/Feature/Desk/UsersTest.php`

**Interfaces:**
- Routes `GET /users`, `POST /users`, `PUT /users/{user}`, `PATCH /users/{user}/active` (toggle). Index lists users visible to the actor (global: all, filter by company; manager: own company).
- Create: name, email (unique), role, company_id (required for manager/estimator, must be null for admin/superadmin). New user gets random password, `email_verified_at` = now, and a password reset link is sent (`Password::sendResetLink`).
- Rules: superadmin can assign any role; admin can assign manager/estimator only and cannot edit admins/superadmins; manager can create/edit manager/estimator in own company only and cannot move users to other companies; nobody can deactivate themselves; the last active superadmin cannot be demoted/deactivated.

- [ ] **Step 1: Write failing tests** — dataset of actor × target role × company for allowed/forbidden; `new user receives reset link` (`Notification::fake`); `manager role requires company`; `admin role must not have company`; `cannot deactivate self`; `cannot remove last superadmin`.
- [ ] **Step 2–5:** run → FAIL, implement, run → PASS, checkpoint.

### Task 14: Overview dashboard

**Files:**
- Create: `app/Http/Controllers/OverviewController.php`, page `Overview.vue`
- Test: `tests/Feature/Desk/OverviewTest.php`

**Interfaces:**
- `GET /overview` `overview` (global only; 403 otherwise; also the post-login landing for global users). Props: tiles `{total, pending, approvedValue, declined, averageMargin}` across all companies (averageMargin = mean of non-null margins, `—` if none); `companies[]` `{id,name,short,color,logoUrl,estimates,pending,approvedValue,averageMargin}`; `pendingQueue` (oldest first, 20: number, title, company, total, flags, submitted at) with inline `Approve`/`Decline` (posting Task 8 route); `activity` (latest 20 from `estimate_activities` with user + estimate number). Clicking a company row posts `current-company.update` then visits `estimates.index`. Styled with the same tile/panel/table classes; eyebrow `ALL COMPANIES`, heading `Overview`.

- [ ] **Step 1: Write failing tests** — `manager cannot view overview`; `totals span all companies`; `pending queue lists pending only, oldest first`; `activity shows latest transitions`; `admin login lands on overview, manager on estimates`.
- [ ] **Step 2–5:** run → FAIL, implement, run → PASS, checkpoint.

### Task 15: Final polish and verification

**Files:** auth pages/layout styling, `README` section in `AGENTS.md`? (no — add `docs/superpowers/reference/crystal-creek/README.md` noting env vars `DESK_SUPERADMIN_EMAIL/PASSWORD` and `storage:link`)

- [ ] **Step 1:** Restyle `AuthLayout` + auth pages to the desk palette (gem wordmark, green primary button) without changing behaviour; existing auth tests stay green.
- [ ] **Step 2:** Run `php artisan wayfinder:generate --with-form`, `php artisan test --compact`, `npm run test:js`, `npm run types:check`, `npm run build`, `vendor/bin/pint --dirty`, `graphify update .` — all pass.
- [ ] **Step 3:** Manual walk-through with seeded users (superadmin, Air manager, Air estimator) at 1440px and 390px: every screen vs the live site; estimator→submit→manager approve→overview shows it; logo upload shows in sidebar + proposal.
