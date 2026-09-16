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
