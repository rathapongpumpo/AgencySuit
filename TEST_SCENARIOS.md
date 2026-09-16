# AgencySuit — Master Test Scenarios

This document is owned by Tester and must evolve with the product.

## Test Case Template

| Field | Value |
|---|---|
| ID | e.g. AUTH-001 |
| Feature | |
| Preconditions | |
| Test data | |
| Steps | |
| Expected result | |
| Actual result | |
| Result | PASS / FAIL |
| Severity if failed | Sev-1 / Sev-2 / Sev-3 / Sev-4 |
| Evidence | |

---

## Severity

- Sev-1 Critical: security/data loss/system unusable
- Sev-2 High: core business flow broken, no reasonable workaround
- Sev-3 Medium: partial feature/UX issue with workaround
- Sev-4 Low: cosmetic/minor

---

## Baseline scenario catalog

### AUTH
- AUTH-001 Email registration
- AUTH-002 Valid email/password login
- AUTH-003 Invalid password
- AUTH-004 Logout
- AUTH-005 Password reset
- AUTH-006 Protected route while logged out
- AUTH-007 Google login success
- AUTH-008 Google login cancel/failure
- AUTH-009 Existing email/account collision handling

### SECURITY / TENANCY
- SEC-001 User B cannot view User A property by URL ID
- SEC-002 User B cannot edit User A property
- SEC-003 User B cannot delete User A property
- SEC-004 User B cannot access User A client
- SEC-005 User B cannot access User A deal/commission
- SEC-006 Matching never crosses users
- SEC-007 Invalid upload rejected
- SEC-008 Production secrets absent from Git diff
- SEC-009 Tests abort on production DB configuration

### PROPERTY
- PROP-001 Quick add minimum fields
- PROP-002 Required field validation
- PROP-003 Edit optional fields later
- PROP-004 Property status change
- PROP-005 Mobile property cards
- PROP-006 Image upload valid file
- PROP-007 Image upload invalid MIME
- PROP-008 Free property limit
- PROP-009 Existing records retained at limit

### CLIENT
- CLT-001 Quick add minimum fields
- CLT-002 Add optional details later
- CLT-003 Edit requirement
- CLT-004 Free client limit
- CLT-005 Contact actions shown when data exists

### MATCHING
- MAT-001 Exact/high match
- MAT-002 Price boundary
- MAT-003 Location mismatch
- MAT-004 Bedroom mismatch
- MAT-005 Size boundary
- MAT-006 Score recalculated after edit
- MAT-007 Deterministic same-input score
- MAT-008 No cross-user results

### SHARE
- SHR-001 Share one property
- SHR-002 Share multiple properties
- SHR-003 Browser without native share gets fallback
- SHR-004 Shared data belongs to correct client/properties

### FOLLOW-UP
- FUP-001 Tomorrow
- FUP-002 3 days
- FUP-003 7 days
- FUP-004 Custom date
- FUP-005 Due today appears on Today
- FUP-006 Overdue appears correctly
- FUP-007 Completed follow-up leaves pending list

### APPOINTMENT
- APT-001 Create appointment
- APT-002 Appointment appears on Today
- APT-003 Edit appointment
- APT-004 Invalid/missing date-time handling

### DEAL
- DEAL-001 Create/associate deal
- DEAL-002 Change valid stage
- DEAL-003 Free active-deal limit
- DEAL-004 Close deal
- DEAL-005 Closed deal retained in history

### COMMISSION
- COM-001 Percentage calculation
- COM-002 Co-agent split
- COM-003 Rounding
- COM-004 Zero/invalid value handling
- COM-005 Correct deal association

### FEEDBACK
- FB-001 Submit "ใช้งานยาก"
- FB-002 Submit "เจอปัญหา"
- FB-003 Submit "อยากให้เพิ่ม"
- FB-004 300-char boundary
- FB-005 Route/page metadata
- FB-006 User ID attached automatically
- FB-007 Success state prevents confusing duplicate submit

### PLAN / LIMITS
- PLAN-001 Free limits sourced from central config
- PLAN-002 Limit message is clear
- PLAN-003 Limit does not delete existing data
- PLAN-004 Core workflow available within free allowance

### MOBILE
- MOB-001 360x800
- MOB-002 390x844
- MOB-003 412x915
- MOB-004 No horizontal scroll
- MOB-005 Bottom nav reachable
- MOB-006 Quick Add short and usable
- MOB-007 Keyboard does not hide critical CTA
- MOB-008 Property/client cards readable
- MOB-009 Today action priority readable

### FULL REGRESSION
- E2E-001 Login → Property → Client → Match → Share → Follow-up → Appointment → Today → Deal → Close → Commission → Feedback

---

## TEST-001 — Project Foundation execution (2026-09-16)

| ID | Feature | Preconditions | Steps | Expected result | Actual result | Result | Evidence |
|---|---|---|---|---|---|---|---|
| FOUND-001 | Laravel application boots | Dependencies installed | `php artisan test`; load `/` locally | App boots and foundation page responds | 8 tests / 17 assertions passed; `GET /` returned 200 | PASS | `php artisan test`; local HTTP smoke |
| FOUND-002 | Test environment uses MySQL test DB | MySQL 8.4 container `agencysuit-mysql-test`; `.env.testing` and PHPUnit target `127.0.0.1/agencysuit_test` | Confirm bound port; migrate; run migration status and suite | Separate local MySQL is available and test DB can be migrated/tested | Container is bound to `127.0.0.1:3306`; `agencysuit_test` exists; all three baseline migrations ran in batch 1; suite passed. | PASS | Docker MySQL 8.4; `php artisan migrate --env=testing --force`; `php artisan test` |
| FOUND-003 | Production-host safety guard | Unit test suite | Run guard test for `194.59.164.72` | Automated tests abort | Guard unit test passed; application service invokes it while unit tests run | PASS | `DatabaseSafetyGuardTest` |
| FOUND-004 | Production-database safety guard | Unit test suite | Run guard test for `propagent` | Automated tests abort | Guard unit test passed; destructive `migrate:fresh` case passed | PASS | `DatabaseSafetyGuardTest` |
| FOUND-005 | `.env` not tracked | Repository working tree | Inspect `git status`, `git ls-files`, `.gitignore` | Local env files excluded from Git | `.env` and `.env.testing` are ignored; no files are currently tracked | PASS | Git inspection |
| FOUND-006 | Frontend build | Node dependencies installed | `npm run build` | Production bundle builds | Build passed; optional `fontaine` warning only | PASS | `npm run build` |
| FOUND-007 | Mobile base layout 360×800 | Local server at `127.0.0.1:8000` | Browser smoke at 360×800 | No horizontal overflow | Guest foundation page: 360px scroll width / 360px client width; static authenticated Blade includes all four required labels | PASS | Local browser smoke; `ApplicationBootTest` |
| FOUND-008 | Mobile base layout 390×844 | Local server at `127.0.0.1:8000` | Browser smoke at 390×844 | No horizontal overflow | Guest foundation page: 390px scroll width / 390px client width; authenticated layout has no browser route in this foundation | PASS | Local browser smoke; scope limitation recorded |
| FOUND-009 | Mobile base layout 412×915 | Local server at `127.0.0.1:8000` | Browser smoke at 412×915 | No horizontal overflow | Guest foundation page: 412px scroll width / 412px client width; authenticated layout verified only as a Blade fixture | PASS | Local browser smoke; `ApplicationBootTest` |
| FOUND-010 | Free plan centralized config | Source tree | Inspect config and references | Limits are centralized, not duplicated | `config/plans.php` defines 10 properties, 5 clients, 3 photos/property, 1 active deal; no duplicate enforcement code exists yet | PASS | Source inspection |
| FOUND-011 | No production secrets in Git | Repository working tree | Inspect tracked/candidate files and secret patterns | No password/key/token is commit candidate | No tracked files; `.env` files ignored; scan found documentation placeholders only | PASS | Git and candidate-file scan |

### TEST-001 release-gate outcome

- Result: **PASS LOCALLY — ready for Git staging/push review**.
- Resolved blocker: **TEST-001-DB-001** — local MySQL 8.4 container is now isolated at `127.0.0.1:3306`, and its `agencysuit_test` database was migrated successfully. No production/shared database was contacted.
- Coverage limitation: authenticated layout has no safe browser-accessible route; only the Blade fixture/test verifies its placeholder labels. This is not treated as E2E evidence.
- Non-blocking: Vite emits an optional `fontaine` fallback-optimization warning while building successfully.
