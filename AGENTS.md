# AgencySuit — Shared Agent Context

This file is the single source of truth for all agents working on AgencySuit.
Every agent MUST read this file before touching code, testing, deployment, or auditing.

## 1. Project identity

- Product: AgencySuit
- Product type: Mobile-first web application for solo real-estate agents
- Primary user: Independent real-estate agent working alone
- Main goal: Replace scattered notes, Excel/Sheets, chat history, and memory with a simple daily workflow
- Project path on Windows: `C:\Projects\AgencySuit`
- Git remote: `https://github.com/rathapongpumpo/AgencySuit.git`
- Stack: PHP Laravel + MySQL
- UI: Mobile-first responsive web app; desktop is secondary
- Production/shared DB host: `194.59.164.72`
- Production/shared DB name: `propagent`
- Production/shared DB username: `propagent`
- DB password: NEVER store in Git, source code, documentation, logs, screenshots, prompts, or test fixtures. Read from `.env` only.

## 2. Product positioning

AgencySuit is NOT a complex enterprise CRM.

Positioning:

> ทรัพย์ ลูกค้า นัดดู และสิ่งที่ต้องตามวันนี้ อยู่ในที่เดียว  
> สำหรับนายหน้าอสังหาฯ ที่ทำงานคนเดียว ใช้ง่ายบนมือถือ ไม่ต้องใช้ Excel และไม่ต้องจ่าย CRM รายเดือน

Product principles:

1. Mobile-first, one-hand friendly.
2. Minimum typing.
3. Prefer tap/select/chips/defaults over free text.
4. Quick actions should normally require no more than 5 essential inputs.
5. Optional information must not block the workflow.
6. The system should remember and surface work; the user should not need to search menus for tasks.
7. Avoid CRM jargon where plain Thai is clearer.
8. No spreadsheet-like tables on mobile.
9. One clear primary action per screen.
10. Never add complexity merely because a CRM normally has that feature.

## 3. Main navigation

Primary mobile navigation has only 4 top-level destinations:

1. วันนี้
2. ทรัพย์
3. ลูกค้า
4. เพิ่มเติม

Contextual features such as matching, follow-up, appointment, deal, and commission should appear inside the relevant workflow instead of becoming top-level menu items.

A global `+` Quick Add action should expose:

- เพิ่มลูกค้า
- เพิ่มทรัพย์
- นัดดู
- ติดตาม

## 4. Authentication

V1 supported login methods:

1. Google login — preferred quick login
2. Email + password — fallback

Do not add LINE Login, phone OTP, Apple Login, or other providers in V1 unless the product owner explicitly changes scope.

Security rules:

- Passwords use Laravel's secure password hashing.
- OAuth secrets live in `.env`.
- Never expose OAuth client secrets to frontend code.
- Provide logout.
- Password reset should work for email/password users.
- A user must never access another user's data by changing IDs/URLs.

## 5. Core workflow

The complete V1 business loop is:

`สมัคร/ล็อกอิน → เพิ่มทรัพย์ → เพิ่มลูกค้า → Match → แชร์ทรัพย์ → Follow-up → นัดดู → เปลี่ยนสถานะดีล → ปิดดีล → คำนวณ Commission`

V1 is complete only when this entire loop works correctly on mobile.

## 6. Today page

The Today page is the product's operational home, not a KPI dashboard.

It should surface:

- Today's appointments
- Overdue follow-ups
- Follow-ups due today
- Important owner/client actions
- Relevant new property/client matches when available

The page should answer: "วันนี้ต้องทำอะไรต่อ?"

Do not put large analytics dashboards above daily actions.

## 7. Properties

Property quick-add should request only essential information first.

Recommended essential fields:

- Transaction type: ขาย / เช่า
- Project/property name
- Price
- Bedroom count
- Location
- At least one photo is encouraged but should not unnecessarily block saving if the product owner has not made it mandatory

Additional details are optional and editable later:

- Bathroom
- Size
- Floor
- Unit number
- Direction
- Owner
- Owner phone
- Commission
- Furniture/appliances
- Notes
- Other details

Property statuses should be selectable, e.g.:

- พร้อมขาย/พร้อมเช่า
- จองแล้ว
- ขายแล้ว/ปล่อยแล้ว
- พักประกาศ

Property list on mobile uses cards, not tables.

## 8. Clients

Client quick-add should be extremely short.

Recommended essential fields:

- Name/nickname
- ซื้อ / เช่า
- Budget range
- Preferred locations

Optional later:

- Phone
- LINE ID / contact note
- Bedrooms
- Minimum size
- BTS/MRT preference
- Other requirements
- Notes

The client page should make it easy to:

- Call
- Open/share through LINE where supported by browser/device
- View matching properties
- Set next follow-up
- View recent activity

## 9. Matching

V1 matching does NOT require AI.

Use deterministic rules/weighted scoring so results are predictable and testable.

Current working weighting:

- Price: 30%
- Location: 25%
- Property/transaction type: 10%
- Bedrooms: 10%
- Size: 10%
- BTS/MRT: 10%
- Other conditions: 5%

Treat these percentages as configurable business rules, not scattered magic numbers.

The system should automatically calculate relevant matches after property/client requirement changes.

## 10. Sharing properties

An agent can select one or more properties and share them with a client.

V1 should use browser/native sharing or a generated shareable presentation/link where feasible.

Do NOT introduce LINE OA API/webhook integration in V1.

## 11. Follow-up

Follow-up UX should favor quick choices:

- พรุ่งนี้
- 3 วัน
- 7 วัน
- เลือกวันที่

Due/overdue follow-ups surface automatically on Today.

Avoid complex task metadata unless required later.

## 12. Appointments

Agent can create an appointment from client/property context.

Core fields:

- Client
- Property
- Date
- Time
- Short note (optional)

Appointment should surface on Today.

## 13. Deals

Keep deal progression simple.

Current working stages:

- ลูกค้าใหม่
- คุยแล้ว
- นัดดู
- เจรจา
- จอง
- สัญญา
- ปิดดีล

On mobile, use simple status progression/selectors instead of desktop-first drag-and-drop Kanban as the primary interaction.

## 14. Commission

On close:

- Sale/rent amount
- Commission type/percentage or amount
- Co-agent split if relevant
- Final expected/earned amount

History should remain associated with the deal.

## 15. Feedback

Feedback exists in V1.

Entry points:

- เพิ่มเติม → ส่งความคิดเห็น
- Optional contextual CTA on suitable screens

Feedback form should stay short:

Type:
- ใช้งานยาก
- เจอปัญหา
- อยากให้เพิ่ม

Message:
- Short text, target maximum 300 characters

System metadata stored automatically:

- user_id
- feedback type
- message
- current page/route
- created_at
- status

Suggested status:
- new
- reviewed
- planned
- done

Do not ask the logged-in user to re-enter name/email just to submit feedback.

## 16. Free vs paid

The free plan must demonstrate the full core workflow rather than disabling the core product.

Current working defaults:

FREE
- 10 properties
- 5 clients
- 3 photos per property
- 1 active deal
- Matching: enabled
- Follow-up: enabled
- Appointments: enabled
- Sharing: enabled
- Closing a deal: enabled
- Limited historical/advanced reporting

PAID / PRO
- Higher limits
- Full history
- Advanced search/filter where applicable
- Export/backup if implemented
- Revenue/commission dashboard if implemented

IMPORTANT:
- Limits MUST be centralized/configurable.
- Do not hard-code plan limits across controllers/views.
- Do not add ads.
- Do not make users pay merely to experience the product's core loop.
- Existing user data must not disappear when a limit is reached.

## 17. Image/storage rules

Do not store image binary data in MySQL.

Store image metadata/path in DB and image files in configured storage.

Validate:
- MIME type
- file size
- image dimensions where relevant

Prefer browser/server optimization and thumbnails to avoid uploading multi-megabyte originals unnecessarily.

Never trust file extension alone.

## 18. Multi-tenant data isolation

Every business record belongs to a user/account.

All agents must treat user data isolation as a critical requirement.

Examples:
- User A must never load, edit, delete, export, or infer User B's properties.
- Never fetch by raw ID without ownership authorization.
- Use Laravel policies/scopes/authorization consistently.

IDOR/data leakage is a release-blocking defect.

## 19. Out of scope for V1

Do not implement without explicit product-owner approval:

- AI chatbot
- AI content generation
- LINE OA inbox/webhook
- Facebook/WhatsApp unified inbox
- Team accounts/roles
- Assign lead to employees
- Employee KPI
- Agency HR
- Website builder
- Auto-post to property portals
- E-signature
- Full contract generation
- Marketing automation
- Accounting suite
- Rental property management
- Complex enterprise reporting

## 20. Git and release workflow

Required flow:

`Developer → Tester → Git push → Deploy → Production Audit`

### Developer
- Works locally.
- Implements/fixes.
- Runs basic developer checks.
- Updates handoff notes.
- MUST NOT push to remote unless the product owner explicitly overrides this rule.

### Tester
- Reviews the change locally.
- Uses a LOCAL/TEST MySQL database, never production/shared DB.
- Executes automated + scenario tests.
- Writes/updates test scenarios and test cases.
- If release gate passes, Tester may commit/push to Git.
- If it fails, return exact defects to Developer and do not push.

### Deploy
- Deployment occurs only after Tester passes.
- Deployment method depends on Hostinger configuration and may be manual or automated.
- Never assume deployment succeeded without evidence.

### Audit
- Runs only after deployment to the real host.
- Verifies deployed revision/environment/functionality.
- Uses safe test data/account.
- Does not perform destructive production experiments.
- Reports PASS / PASS WITH ISSUES / FAIL.

## 21. Database environment rules

PRODUCTION/SHARED DB:
- Host: `194.59.164.72`
- Database: `propagent`
- Username: `propagent`
- Password: provided separately through environment secret

LOCAL TESTING:
- MUST use separate local/test MySQL database, e.g. `agencysuit_test`
- `.env.testing` should point only to the test DB
- Never run `migrate:fresh`, destructive seeders, or test transactions against production/shared DB
- Before any destructive DB command, verify host/database name

Recommended safety guard:
- Automated tests should abort if DB host equals `194.59.164.72` or DB name equals `propagent`.

## 22. Secret handling

Never commit:
- `.env`
- database password
- OAuth client secret
- SMTP password
- production API tokens
- session/app keys
- private keys

`.env.example` contains placeholders only.

Before any push, inspect staged diff for secrets.

## 23. Definition of Done for V1

V1 is not done merely because pages exist.

A new user on a mobile viewport must be able to:

1. Sign up/login.
2. Add first property quickly.
3. Add first client quickly.
4. See a useful match.
5. Share/select a property for client.
6. Set follow-up.
7. Create appointment.
8. See follow-up/appointment on Today.
9. Move deal through status.
10. Close deal.
11. See correct commission result.
12. Submit short feedback.
13. Hit free limits without losing existing data.
14. Upgrade state/paid entitlements must be technically supportable even if payment integration is not yet live.
15. Log out and back in without losing data.

No release if cross-user data isolation fails.
