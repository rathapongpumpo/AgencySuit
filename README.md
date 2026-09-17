# AgencySuit Agent Pack

Files:

- `AGENTS.md` — shared product/process truth for all agents
- `skills/developer/SKILL.md` — Developer role
- `skills/tester/SKILL.md` — Tester / release gate
- `skills/auditor/SKILL.md` — production audit role
- `skills/uxui/SKILL.md` — AgencySuit mobile UX/UI rules
- `COMMANDS.md` — copy-paste prompts + common commands
- `TEST_SCENARIOS.md` — master scenario catalog owned by Tester

Recommended placement inside project:

```text
C:\Projects\AgencySuit\
├─ AGENTS.md
├─ COMMANDS.md
├─ TEST_SCENARIOS.md
└─ skills\
   ├─ developer\SKILL.md
   ├─ tester\SKILL.md
   └─ auditor\SKILL.md
```

Important:
- Do not add the production DB password to these files.
- Tester should create `.env.testing` using a separate MySQL test DB.
- Audit requires the production URL and expected approved Git SHA after deploy.

## Local development

1. Run `composer install` and `npm install`.
2. Copy `.env.example` to `.env`, generate an app key with `php artisan key:generate`, and set the local MySQL database in `.env`.
3. Create a separate local test database (for example, `agencysuit_test`) and keep `.env.testing` pointed at `127.0.0.1` and that database.
4. Run `php artisan migrate` for local development, then `php artisan test` for automated tests.
5. Run `npm run build` for the frontend bundle.

Automated tests must never use the production/shared database (`194.59.164.72` / `propagent`). The database safety guard aborts tests and destructive commands when those values are configured.

## Release flow

Routine code changes are pushed to GitHub. Hostinger website deployment is performed separately; apply a Hostinger migration only when a release includes new database migrations.
