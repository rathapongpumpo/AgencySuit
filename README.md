# AgencySuit

Mobile-first Laravel application for independent real-estate agents.

## Local setup

1. Install PHP 8.3+, Composer, Node.js, and local MySQL.
2. Run `composer install` and `npm install`.
3. Copy `.env.example` to `.env`, run `php artisan key:generate`, then set only local MySQL credentials. The default local database name is `agencysuit`.
4. Create a separate local test database named `agencysuit_test` and copy `.env.testing.example` to `.env.testing`. Set its local credentials there if they are different:

   ```sql
   CREATE DATABASE agencysuit_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
5. Run `php artisan optimize:clear`, `php artisan test`, and `npm run build`.

Never run automated tests, `migrate:fresh`, `db:wipe`, or other destructive development commands against `194.59.164.72` or the `propagent` database. The application blocks these configurations; this is a safety net, not permission to use the shared database.

`.env` and `.env.testing` are local-only and must never be committed. `.env.example` and `.env.testing.example` contain no credentials and are safe to track.

## Google Login

Set `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, and `GOOGLE_REDIRECT_URI` only in the local or production `.env`; register the same callback URL in Google Cloud. If these values are blank, the Google button returns to Login with a clear message and email login remains available.

## Agent pack

Files:

- `AGENTS.md` — shared product/process truth for all agents
- `skills/developer/SKILL.md` — Developer role
- `skills/tester/SKILL.md` — Tester / release gate
- `skills/auditor/SKILL.md` — production audit role
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
