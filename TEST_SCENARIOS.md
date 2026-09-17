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

## TEST-003 — Mobile App Shell execution (2026-09-16)

| ID | Scope | Actual result | Result | Evidence |
|---|---|---|---|---|
| SHELL-001 | Login to Today | Email registration/login reaches `/today`. | PASS | Browser smoke; `AuthenticationTest` |
| SHELL-002 | Protected destinations | Guests are redirected from Today, Properties, Clients, and More. | PASS | `MobileAppShellTest` |
| SHELL-003 | Bottom navigation | Today, Properties, Clients, and More links are present and route correctly. | PASS | Browser smoke |
| SHELL-004 | Quick Add | Opens and closes; shows exactly เพิ่มทรัพย์, เพิ่มลูกค้า, นัดดู, ติดตาม. | PASS | Browser smoke |
| SHELL-005 | Empty states | Today, Properties, and Clients provide one clear empty-state CTA; no CRUD routes exist. | PASS | Browser smoke; route inspection |
| SHELL-006 | Logout | Logout returns to Login; protected Today then redirects to Login. | PASS | Browser smoke; `AuthenticationTest` |
| SHELL-007 | Mobile/UX gate | 360×800, 390×844, 412×915 render without observed horizontal overflow; no tables, KPI grids, gradients, or glassmorphism. | PASS | Playwright screenshots; UX/UI review |
| SEC-SHELL-001 | Secret check | No local `.env` files or assigned credentials are staged. | PASS | Staged review |

### TEST-003 release-gate outcome

- Scope: TASK-003 Mobile App Shell plus Authentication regression only; full product regression was not run.
- Automated evidence: `php artisan test tests/Feature/AuthenticationTest.php tests/Feature/MobileAppShellTest.php` passed 15 tests / 82 assertions on MySQL `agencysuit_test`.
- Non-blocking: Quick Add dialog renders from the top edge rather than as a bottom-aligned sheet; its four future actions are intentionally non-interactive until their respective form tasks.

## TEST-004 — Property Core execution (2026-09-16)

| ID | Scope | Actual result | Result | Evidence |
|---|---|---|---|---|
| PROP-001 | Quick Add minimum fields | Quick Add → เพิ่มทรัพย์ opens `/properties/create`; form has 5 logical required fields: type, name, price, bedrooms, location. | PASS | Browser smoke; `PropertyTest` |
| PROP-002 | Required-field validation | Blank form is invalid in browser; server rejects invalid type/name/price/bedrooms/location and stores no record. | PASS | Browser validity check; `PropertyTest` |
| PROP-003 | Property list/detail/edit | Authenticated user can create, list, open detail, and edit core fields with success feedback. | PASS | Browser smoke; `PropertyTest` |
| PROP-004 | Status change | Status selector changes available → reserved and persists/display labels correctly. | PASS | Browser smoke; `PropertyTest` |
| SEC-001/002 | Guest + cross-user isolation | Guest property routes redirect to login; another user cannot view/edit/status-update the property and list excludes it. | PASS | `PropertyTest` |
| PLAN-001/002/003 | Centralized free limit | Limit is read from `config/plans.php` (`free.limits.properties = 10`); item 11 is blocked with clear message and existing records remain. | PASS | `PropertyTest`; config inspection |
| MOB-001/002/003/004/005/006/008 | Mobile property UX | Property list, detail, and edit render at 360×800, 390×844, 412×915 with no horizontal overflow; cards and CTAs are tappable. | PASS | Playwright screenshots/metrics |
| UX-004 | UX/UI anti-pattern gate | No table, dashboard/KPI grid, gradient, glassmorphism, oversized image, owner/matching/client feature, or extra top-level navigation introduced. | PASS | UX/UI review; route/view inspection |
| SEC-008/009 | Secret + DB safety | No env/credential files staged; tests/migration use local Docker MySQL only (`127.0.0.1:3306/agencysuit_test`). | PASS | Staged review; `migrate:status` |

### TEST-004 release-gate outcome

- Scope: TASK-004 Property Core plus Authentication/App Shell regression; full product regression was not run.
- Automated evidence: `php artisan test tests/Feature/PropertyTest.php tests/Feature/AuthenticationTest.php tests/Feature/MobileAppShellTest.php` passed 24 tests / 139 assertions.
- Build/style evidence: `npm run build` PASS; `vendor\\bin\\pint --test` PASS.
- Migration evidence: Docker `mysql:8.4` container `agencysuit-mysql-test` on `127.0.0.1:3306`; migration `2026_09_16_000004_create_properties_table` is Ran.
- Developer Handoff file was not present in the repository; release decision is based on the actual working-tree diff and executed checks.

## TEST-005 — Property Images execution (2026-09-17)

| ID | Scope | Actual result | Result | Severity / Evidence |
|---|---|---|---|---|
| IMG-001 | Primary image and thumbnail ordering | Uploading JPG + PNG + WebP stores the first file as `is_primary=true`, but the detail relation has no explicit ordering. The UI displayed the third uploaded image as “ภาพหลัก”; list/detail selection can therefore use a non-primary image. | FAIL | Sev-2; browser detail `/properties/25` showed “ภาพหลัก” on รูป 3 while DB primary was photo id 13; `Property` photo queries have no `ORDER BY`. |
| IMG-002 | Invalid/corrupt/oversized feedback | Corrupt image and >4 MB image were rejected and existing photos remained intact, but the detail page displayed no actionable validation message after the redirect. | FAIL | Sev-3; browser upload attempts on `/properties/26` showed 0/3 with no error text. |
| IMG-003 | Valid formats and GD fallback | Real JPG/PNG/WebP upload succeeded with GD disabled; no fatal occurred, metadata/path were stored, and `thumbnail_path` remained null rather than claiming a generated thumbnail. | PASS | PHP `gd=off`; DB/storage inspection; browser preview. |
| IMG-004 | Limit and data retention | Automated test blocks the fourth photo from centralized free limit 3 while retaining existing records. | PASS | `PropertyPhotoTest`. |
| IMG-005 | Isolation/private storage | User isolation tests pass; photo rows contain metadata/path only and files are under `storage/app/private/properties/{user}/{property}`. | PASS | `PropertyPhotoTest`; migration/config inspection. |
| MOB-IMG-001 | Mobile image UI | 360×800, 390×844, and 412×915 showed no horizontal overflow; upload/preview controls fit the viewport. | PASS | Playwright viewport metrics and snapshots. |
| ENV-IMG-001 | PHP temp upload | `php artisan serve` could not create upload temp files in this local environment. With a writable `upload_tmp_dir`/`sys_temp_dir`, the same app upload succeeded; classified as local environment-only and non-blocking. | PASS (non-blocking) | Direct PHP server with writable temp directory; no application change made. |

### TEST-005 release-gate outcome

- Scope: TASK-005 Property Images plus Property and Authentication/App Shell regression; full product regression was not run.
- Automated evidence: `php artisan test tests/Feature/PropertyPhotoTest.php tests/Feature/PropertyTest.php tests/Feature/AuthenticationTest.php tests/Feature/MobileAppShellTest.php` passed 32 tests / 176 assertions on local Docker MySQL `agencysuit_test`.
- Build/style evidence: `npm run build` PASS; `vendor\\bin\\pint --test` PASS; secret-pattern check PASS.
- GD note: local PHP has GD disabled; safe original-file fallback passed. Production deployment should verify GD availability before expecting generated thumbnails.
- Developer Handoff file was not present in the repository; decision is based on the actual working-tree diff and executed checks.
- **Release decision: FAIL — do not commit or push. Return IMG-001 and IMG-002 to Developer.**

## RETEST TEST-005 — Property Images (2026-09-17)

| ID | Scope | Actual result | Result | Evidence |
|---|---|---|---|---|
| IMG-001-R | Primary/order consistency | Three real JPG/PNG/WebP uploads put the first photo first and primary after upload and reload. List and detail used the same primary; changing primary reordered both; deleting it selected the oldest remaining photo deterministically. | PASS | Browser flow; DB `is_primary`; list/detail thumbnail URLs. |
| IMG-002-R | Validation/error feedback | Corrupt JPEG, invalid MIME, and >4 MB uploads were rejected with clear alert text beside the uploader; existing photos remained intact. | PASS | Browser flow at local test DB; 2/3 count preserved after failures. |
| IMG-003-R | Mobile error state | Uploader/error state measured with no horizontal overflow at 360×800, 390×844, and 412×915. | PASS | Playwright viewport metrics. |

### RETEST TEST-005 release-gate outcome

- Automated evidence: `php artisan test tests/Feature/PropertyPhotoTest.php tests/Feature/PropertyTest.php tests/Feature/AuthenticationTest.php tests/Feature/MobileAppShellTest.php` passed 35 tests / 192 assertions on local Docker MySQL `agencysuit_test`.
- Build/style evidence: `npm run build` PASS; `vendor\\bin\\pint --test` PASS; secret-pattern check PASS.
- GD remains disabled locally; original-file fallback is safe, with `thumbnail_path` null and private storage paths under the user/property directory. Production should verify GD at deploy.
- PHP temp-upload issue remains local-environment-only; testing used a writable temp directory without changing application behavior.
- Full regression was not run. **Release decision: PASS.**
