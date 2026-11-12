# Crystal Creek reference + running the desk

This folder holds the original Crystal Creek Estimating Desk assets we recreated:
`index.D_o7GGtd.css` (stylesheet, ported to `resources/css/desk.css`), `page-1XroWPtX.js`
(app bundle: seed templates, copy, pricing math), `favicon.svg`, `cc-builder.png`, `sections.json`.

## Setup

```bash
php artisan migrate --seed      # 4 companies + 14 templates
php artisan storage:link        # company logos are served from the public disk
```

Seeding a real superadmin: set `DESK_SUPERADMIN_EMAIL` and `DESK_SUPERADMIN_PASSWORD` in `.env`
before `db:seed`. In the `local` environment the seeder also creates demo accounts
(password `password`): `admin@example.com` (superadmin), `manager@example.com` and
`estimator@example.com` (Crystal Creek Air).

Public registration is disabled; people are invited from **Users** and receive a set-password email,
so mail must be configured.

## Roles

| Role | Scope | Can |
|---|---|---|
| Superadmin | all companies | everything, incl. managing admins/superadmins |
| Admin | all companies | switch companies, overview, approve, manage companies and managers/estimators |
| Manager | one company | approve, edit any estimate, templates, pricing defaults, company settings/logo, users |
| Estimator | one company | see company estimates, create, edit own drafts/pending/declined, submit for approval |

## Tests

`php artisan test` (Pest) and `npm run test:js` (Vitest). Pricing parity with the original is pinned
by `tests/fixtures/pricing-cases.json`, generated from the original JS functions.
