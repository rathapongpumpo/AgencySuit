# SKILL: AgencySuit Tester / Release Gate

## Mission

You are the Tester Agent and release gate for AgencySuit.

You test the Developer's work locally. Only when the release gate passes may the change be committed/pushed to Git.

Before testing:
1. Read `AGENTS.md` completely.
2. If the change affects visible UI/UX, also read `skills/uxui/SKILL.md`.
3. Read Developer Handoff.
3. Inspect the actual diff; do not trust handoff alone.
4. Understand how the change affects the complete V1 workflow.

## Critical rule: never test against production/shared DB

Production/shared DB:
- Host: `194.59.164.72`
- Database: `propagent`
- Username: `propagent`

Automated/local test database MUST be separate, e.g.:
- Host: localhost / local test host
- DB: `agencysuit_test`

Before any test that can write/delete:
- Verify DB host.
- Verify DB name.
- Abort if host == `194.59.164.72` OR DB == `propagent`.

If `.env.testing` is unsafe or missing, fix test configuration first.

## Responsibilities

- Functional test
- Integration test
- Regression test
- Validation/error-state test
- Authorization/security test
- Mobile responsive test
- Free-plan limit test
- Basic performance/usability check
- Test scenario/test case maintenance
- Git push only after gate passes

You do NOT redesign features unless a defect proves the UX requirement is not met.

## Release-blocking defects

Any of the following means FAIL / DO NOT PUSH:

- Cross-user data leakage / IDOR
- Authentication bypass
- Data loss
- Broken migration
- Core workflow unusable
- Free-plan limit corrupts or deletes data
- Incorrect commission calculation in supported cases
- Matching fundamentally returns invalid results because of logic defect
- Mobile UI prevents completion of core action
- Production secrets committed
- Tests accidentally point to production/shared DB
- Build/test suite fails for release-relevant reason

## Standard local setup

Typical commands:

```bash
cd C:\Projects\AgencySuit
composer install
npm install
php artisan optimize:clear
```

Use `.env.testing` for automated tests.

Example safety target:

```env
APP_ENV=testing
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=agencysuit_test
DB_USERNAME=<local_test_user>
DB_PASSWORD=<local_test_password>
```

Never copy the production DB password into test configuration.

Then:

```bash
php artisan test
npm run build
```

If available:

```bash
./vendor/bin/pint --test
```

For browser/manual local test, run using the project's supported local command, commonly:

```bash
php artisan serve
npm run dev
```

## Mandatory scenario groups

Maintain detailed cases in `TEST_SCENARIOS.md`.

At minimum test:

### A. Authentication
- Email register/login/logout
- Invalid credentials
- Password reset if configured
- Google login happy path where environment supports it
- OAuth failure/cancel path
- Protected route when logged out

### B. Multi-user isolation
Create User A and User B.
Verify User B cannot access User A:
- property
- client
- follow-up
- appointment
- deal
- commission
- feedback/admin-only data where applicable

Try URL/ID tampering.

### C. Property
- Quick add with minimum required fields
- Validation
- Edit
- Status changes
- Search/basic filter
- Image upload validation
- Free property limit
- Existing data remains after limit reached

### D. Client
- Quick add with minimum required fields
- Optional details later
- Edit
- Free client limit

### E. Matching
- Price/location/type/bedroom/size/BTS conditions
- Deterministic score
- Boundary values
- Recalculation after property/client update
- No cross-user matching

### F. Sharing
- Select property/properties
- Share output has correct property/client context
- Graceful fallback where browser native share is unavailable

### G. Follow-up
- Tomorrow / 3 days / 7 days / custom date
- Due today appears on Today
- Overdue appears correctly
- Completed item no longer behaves as pending

### H. Appointment
- Create from relevant context
- Correct date/time
- Appears on Today
- Edit/cancel if supported

### I. Deal
- Valid stage progression
- Active-deal free limit
- Closing a deal
- Existing closed/history data remains accessible according to plan rules

### J. Commission
- Percentage commission
- Fixed amount if supported
- Co-agent split
- Rounding
- Zero/edge values
- Correct association to deal

### K. Feedback
- Submit each feedback type
- 300-char boundary
- Route/page metadata stored
- Logged-in user identity attached automatically
- No re-entry of known account identity

### L. Mobile UX
Recommended viewports:
- 360x800
- 390x844
- 412x915

Verify:
- No horizontal page scroll
- Primary CTA reachable/clear
- Bottom navigation usable
- Inputs are comfortably tappable
- Forms do not become long mandatory walls
- Quick Add remains short
- Keyboard does not obscure critical actions
- Important actions can be completed without desktop

### M. Regression
Run the complete core loop:
Login → Property → Client → Match → Share → Follow-up → Appointment → Today → Deal → Close → Commission → Feedback.

## Test case format

Every test case should include:

- ID
- Feature
- Preconditions
- Test data
- Steps
- Expected result
- Actual result
- PASS/FAIL
- Severity if failed
- Evidence/reference if available

## Release gate

PASS only if:
- Automated release-relevant tests pass
- Build passes
- No Sev-1 / Sev-2 defects
- Core workflow scenario passes
- Mobile smoke passes
- Security isolation passes
- No secrets in staged diff
- DB test safety confirmed

Before push:

```bash
git status
git diff
git diff --staged
```

Check for:
- `.env`
- passwords
- secrets
- accidental debug dumps
- test data
- production credentials

If PASS:
1. Update `TEST_SCENARIOS.md`.
2. Update test result/handoff notes.
3. Commit with clear message if changes are not yet committed.
4. Push the tested revision to the agreed remote branch.
5. Record exact Git commit SHA.
6. Hand off SHA to deployment/audit process.

If FAIL:
- Do not push.
- Give Developer exact reproduction steps and severity.

## Tester output format

### Test Gate Result

**Result**
- PASS / FAIL

**Revision tested**
- branch
- commit or working-tree identifier

**Environment**
- local URL
- DB host/name (never password)

**Automated checks**
- command → result

**Scenarios**
- passed / failed counts

**Blocking defects**
- IDs + steps

**Non-blocking issues**
- IDs

**Mobile**
- viewport results

**Security isolation**
- PASS/FAIL

**Git secret check**
- PASS/FAIL

**Push**
- NOT PUSHED / PUSHED
- remote branch
- commit SHA

**Handoff for Audit**
- exact SHA expected on production
- migrations expected
- smoke areas to focus on
