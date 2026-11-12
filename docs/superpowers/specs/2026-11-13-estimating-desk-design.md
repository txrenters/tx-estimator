# Estimating Desk — Design

Date: 2026-11-13
Status: draft for approval

## Intent

Recreate the Crystal Creek Estimating Desk (https://crystal-creek-estimating-desk.shrewdpath4.chatgpt.site)
inside this Laravel + Inertia + Vue app, **looking and behaving the same**, but turned into a real
multi-company product: real accounts, data saved on the server, each company its own organization, and
role-based access.

The original is a single-browser tool for four sister companies (Crystal Creek Air, Crystal Creek Pest
Control, Crystal Creek Roofing, Texas Home Maintenance Pros). Our version keeps the same screens and
workflow and adds organizations, roles, approvals, company settings (logo) and an overall dashboard.

## Goals

1. Visual parity with the original: same layout, sidebar, colours, typography, card/table styles, copy.
2. Feature parity: Estimates list + stats, Estimate builder, Proposal view, Service templates,
   Compare estimates, Calculator, Pricing defaults.
3. Companies are organizations; all business data is scoped to a company.
4. Roles: superadmin, admin, manager, estimator — with the access rules below.
5. Admin/superadmin can switch into any company and see an overall cross-company dashboard.
6. Company settings, including an uploaded logo.

## Non-goals

- Public sign-up (registration is disabled; users are created by admins/managers).
- Billing, customer portal, e-signature, emailing proposals, tax calculation, inventory.
- Mobile app. (The web UI must still work at phone width, as the original does.)

## Anti-goals (failure even if it "works")

- A manager or estimator ever seeing another company's data.
- A design that drifts visibly from the original (different palette, starter-kit look, different copy).
- Pricing totals that disagree with the original's calculation for the same inputs.

## Reference material (in repo)

`docs/superpowers/reference/crystal-creek/`:
- `index.D_o7GGtd.css` — the original's full stylesheet (tokens on `:root`: `--paper #f5f7f5`,
  `--ink #162721`, `--muted #5c6b64`, `--line #e0e7e2`, `--green #17664e`, `--sidebar #102c25`,
  `--soft #eaf4ee`, `--radius 13px`; Arial/Helvetica). Port these classes/values.
- `page-1XroWPtX.js` — the original app bundle (minified React). Contains the **14 seed templates with
  line items and notes**, the service-type list, status list, default pricing values and the pricing math.
  Treat it as the source of truth for seed data and calculations.
- `index.html`, `favicon.svg` (gem logo), `cc-builder.png` (builder screenshot), `sections.json`
  (text of every screen).

## Domain

### Companies (organizations)
Fields: name, slug, location label (e.g. "Greater Houston, TX"), currency (USD), accent colour (the
sidebar dot), logo (uploaded image, optional), proposal defaults (scope/terms text, optional).
Seed the four original companies with the original dot colours.

### Users & roles
- `superadmin` — global. Everything an admin can do, plus create/edit/remove admins and superadmins.
- `admin` — global. Switch to any company, overall dashboard, create companies, create users in any
  company, approve any estimate, edit everything.
- `manager` — belongs to exactly one company. Full control within it: approve estimates, edit any
  estimate, manage that company's users (managers/estimators), templates, pricing defaults, settings/logo.
- `estimator` — belongs to exactly one company. Sees all estimates in their company; creates estimates;
  edits only their own estimates while Draft or Pending approval; can submit for approval; cannot approve
  or decline; read-only on templates, pricing defaults, settings. Can use templates, compare, calculator.

A user has one role. Managers/estimators have a `company_id`; admins/superadmins don't (null).
No self-registration (already disabled). Users created by admin/manager get a password-set link
(Fortify password reset flow).

### Current company
Admins/superadmins choose a "current company" (stored in session) or "All companies". Managers/
estimators are always pinned to their own company. Every query for estimates/templates/defaults goes
through this scope; policies enforce it server-side.

### Service types
Same fixed list as original: HVAC repair, HVAC replacement, Pest plan, Pest treatment, Roofing, Siding,
Make-ready, Cleaning, Plumbing, General maintenance. Grouped in the catalog as HVAC / Pest Control /
Roofing / Maintenance as in the original.

### Estimates
Belongs to a company and creator. Fields as the original builder: number (`EST-XXXXXX`), title, status
(Draft, Pending approval, Approved, Declined), service type, customer/owner, service address, line items,
pricing adjustments (cost markup %, subtotal discount %, trip charge $, authorization limit $, manager
review threshold $, target gross margin %), proposal scope, proposal terms & exclusions, internal notes.
Plus: approved_by / approved_at, timestamps.

Line items: item, qty, unit, labor/unit, materials/unit, fixed sell/unit (nullable), ordering.

Revision history (parity with the original's "History" button / "Revision history" panel): every save
stores a snapshot of the estimate and its lines in `estimate_revisions` (who, when). The panel lists
revisions; "Load revision" puts that snapshot into the editor unsaved — saving it then goes through the
normal permission and status rules (so restoring cannot bypass approved→draft). Approve/decline also
record a revision. *(My call on the details.)*

Actions: save, duplicate (used by Compare "price another option"), delete (draft only for estimators),
open Proposal (print-friendly page showing company logo/name, customer, scope, line items at sell price,
total, terms — no internal costs or notes).

### Pricing math
Must match the original bundle exactly. Summary (verify against the bundle):
- Line sell/unit = fixed sell if set, else (labor + materials) × (1 + markup%).
- Line sell total = qty × sell/unit; Direct cost = Σ qty × (labor + materials).
- Line subtotal = Σ sell totals; Discount = subtotal × discount%; Total = subtotal − discount + trip.
- Gross margin = (total − direct cost) / total, compared to target; the "enter direct costs" notice and
  authorization/manager-review flags behave as in the original.
Compute on the client for live feedback **and** on the server when saving (server is authoritative for
stored totals used in lists/dashboards).

### Approval flow
- Estimator saves Draft → "Submit for approval" sets Pending approval.
- Manager (own company) or admin/superadmin can set Approved or Declined; records who/when.
- Authorization limit and manager review threshold stay **advisory flags** as in the original (shown on
  the estimate and list); they do not block anything.
- Editing an Approved estimate (manager/admin only) returns it to Draft.
- *My call:* an estimator may withdraw their own Pending estimate back to Draft, and may edit their own
  Declined estimate — saving it returns it to Draft so it can be resubmitted.
- *My call:* a manager/admin editing a Pending estimate leaves it Pending.
- Submit / approve / decline / withdraw / revert-to-draft are written to an `estimate_activities` log
  (who, what, when, from→to status). The Overview's "recent activity" reads this log.

### Service templates
Per company. Seed the 14 original templates (from the bundle) into the company the bundle assigns each one
(company index 0–3 → Air, Pest Control, Roofing, Texas Home Maintenance Pros). The bundle wins — e.g.
"Hardie lap siding replacement" (`m1`) belongs to Texas Home Maintenance Pros, not Roofing. "Edit rates" edits line items/rates; "Use template"
starts a new estimate prefilled. *My call:* the template's scope/terms text is used; if empty, fall back
to the company's default proposal text. Managers/admins create/edit/delete; estimators read and use.

### Pricing defaults
Per company (original was global "across all four companies"; we scope it). Values: markup 30, discount
0, trip 0, authorization limit 250, manager review threshold 1000, target margin 25 (original defaults —
verify in bundle). New estimates copy them; saved estimates keep their own values.

### Compare estimates
Pick up to three saved estimates (from current company scope; admins in "All companies" may compare
across companies) and show them side-by-side like the original.

### Calculator
Client-only, identical to original (base qty, unit cost, waste %, adjusted qty, total).

### Company settings
Manager (own company) and admin/superadmin. Name, location label, accent colour, logo upload
(public disk; png/jpg/svg/webp, ≤2MB), default proposal scope/terms text. Logo shows in the sidebar
company list (replacing the dot when present), the sidebar footer workspace badge for pinned users, and
the Proposal header.

### Users management
- Admin/superadmin: Users screen listing all users, create/edit (name, email, role, company), deactivate.
  Only superadmin may assign admin/superadmin.
- Manager: same screen limited to their company and to manager/estimator roles.

## Screens & navigation

Same shell as the original: dark sidebar (wordmark, WORKSPACE group, COMPANIES group, footer badge),
white topbar with breadcrumb and right-hand status, content area, footer line.

Sidebar WORKSPACE: Estimates, Service templates, Compare estimates, Calculator, Pricing defaults — plus
for admin/superadmin an **Overview** item (overall dashboard) and **Users**; for managers **Users** and
**Company settings**. COMPANIES group: shown to admin/superadmin only (click = switch current company;
an "All companies" option). Managers/estimators see no company switcher; their footer badge shows their
company's logo/initials and location.

Topbar right side: the original's "Private workspace" pill becomes the current company name / "All
companies"; plus a user menu (profile, security, log out) reusing the existing starter-kit settings pages,
restyled to match.

### Overall dashboard (admin/superadmin)
Stat tiles in the original's tile style, across all companies: total estimates, pending approval,
approved value, declined, average gross margin. A per-company table (estimates, pending, approved value,
margin), a "pending approval" queue across companies (approve from here), and recent activity.
Clicking a company switches into it.

### Estimates home (all roles)
Exactly the original: heading, stat tiles (for current scope), filter row (company — only when "All
companies", service, status), table, empty state, "Start from a template" catalog panel.

## Technical choices (my call, building on what exists)

- Laravel 12/13 + Fortify + Inertia v3 + Vue 3 + Tailwind v4 + Wayfinder, as already installed.
- Port the original CSS as a scoped stylesheet/Tailwind theme tokens so pages match pixel-closely;
  the existing starter-kit app layout is replaced by the Estimating Desk shell. Auth pages restyled to the
  same palette.
- SQLite/whatever DB the project already uses; migrations for companies, users(role, company_id),
  estimates, estimate_lines, service_templates, template_lines, pricing_defaults (or columns on companies).
- Authorization via Policies + a current-company resolver middleware; all scoping server-side.
- Pest feature tests for: tenant isolation per role, approval permissions, pricing math parity,
  company switching, logo upload.
- Seeders: four companies, 14 templates, one superadmin (credentials from env), demo manager + estimator.

## Left to the builder

- Exact table/column naming, component breakdown, route names.
- Whether pricing defaults live on `companies` or their own table.
- Estimate number generation scheme (must look like `EST-` + 6 hex chars).
- Approved-estimate edit behaviour (see Approval flow).
- Icon set (original uses lucide-style icons; lucide-vue-next is fine).
