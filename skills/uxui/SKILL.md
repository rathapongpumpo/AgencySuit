# SKILL: AgencySuit UX/UI

Use this skill ONLY when a task creates or changes visible UI, layout, navigation, forms, empty states, cards, dialogs, sheets, or responsive behavior.

Do NOT read this skill for backend-only tasks.

## 1. Design objective

AgencySuit is a mobile-first work tool for solo real-estate agents.

The interface must feel:
- calm
- professional
- fast
- compact without feeling cramped
- obvious without needing a tutorial
- designed for one-hand mobile use
- like a focused productivity app, NOT an admin dashboard

It must NOT look like:
- an AI website builder
- a Bootstrap admin template
- a generic SaaS landing page
- an Excel/Google Sheets wrapper
- a desktop CRM squeezed onto mobile
- a decorative Dribbble concept that is slow to use

## 2. Reference direction

Use these as pattern references, not copies.

### Linear Mobile
Use for:
- compact mobile workflows
- prioritizing work that needs attention now
- bottom navigation / quick access
- quick creation
- calm information hierarchy

### Apple Reminders / iOS productivity patterns
Use for:
- follow-up/task simplicity
- familiar mobile hierarchy
- sheets and contextual actions
- date/time actions
- comfortable touch interaction

### Airbnb mobile
Use selectively for:
- property/listing card hierarchy
- image + title + important metadata
- progressive input
- location-oriented content

AgencySuit is not a consumer marketplace, so property cards must be denser and more operational than Airbnb.

### Notion Calendar / modern calendar apps
Use for:
- date/time selection
- upcoming appointment readability
- compact schedule hierarchy

### Google Maps mobile
Use selectively for:
- location labels
- place hierarchy
- quick location/navigation actions

### HubSpot mobile / lightweight CRM patterns
Use ONLY for:
- contact action patterns
- activity/timeline concepts

Do NOT copy enterprise CRM density or terminology.

## 3. Official design foundations

Follow the spirit of:
- Apple Human Interface Guidelines
- Material Design 3

AgencySuit product rules override generic design-system conventions when they conflict.

## 4. AgencySuit visual direction

Visual personality:
"Calm mobile productivity tool for a working professional."

Prefer:
- neutral light surfaces
- strong readable text
- restrained accent color
- subtle dividers
- modest corner radii
- efficient whitespace
- icon use only when it improves scanning
- one dominant action per screen

Avoid:
- decorative gradients
- glassmorphism
- neon
- giant decorative shapes
- oversized hero headings inside the app
- colorful icon grids
- excessive shadows
- every section inside a card
- excessive rounded containers
- illustration-heavy empty states

### Suggested token behavior

- app background: near-neutral light gray
- main content surfaces: white
- borders/dividers: subtle neutral gray
- primary text: near-black
- secondary text: medium neutral gray
- destructive: red only when needed
- success: green only for meaningful positive status
- one restrained primary accent across the product

Radius:
- controls: roughly 8–10px
- cards/sheets: roughly 12–16px
- use pill shapes only for chips/tags or controls that actually behave like pills

Shadows:
- avoid on ordinary containers
- reserve for clearly floating/layered UI

## 5. Typography

Thai readability is mandatory.

Rules:
- Body text must remain easy to read on 360px screens.
- Avoid very thin weights.
- Avoid light gray text for important information.
- Use size + weight + spacing for hierarchy, not color alone.
- Keep to about 4–5 text hierarchy levels per screen.

Typical range:
- Page title: 24–28px, semibold/bold
- Section title: 17–20px, semibold
- Primary card text: 15–17px
- Body: 14–16px
- Secondary/meta: 12–14px

Do not use 10–11px for essential information.
Prefer the existing project font stack; do not add a font dependency merely for decoration.

## 6. Mobile geometry

Primary viewports:
- 360x800
- 390x844
- 412x915

Rules:
- No horizontal page scroll.
- Main horizontal page padding: generally 16px.
- Dense subareas may use 12px only when readability remains good.
- Practical minimum touch target: about 44–48px.
- Primary buttons: normally at least 48px high.
- Inputs: normally about 48–52px high.
- Bottom navigation must remain comfortably tappable.
- Content must not be hidden behind fixed bottom navigation.
- Respect safe-area spacing where supported.

## 7. Navigation

Top-level destinations are fixed:
1. วันนี้
2. ทรัพย์
3. ลูกค้า
4. เพิ่มเติม

Global Quick Add may expose:
- เพิ่มทรัพย์
- เพิ่มลูกค้า
- นัดดู
- ติดตาม

Rules:
- Do not add another top-level tab without approval.
- Do not use a desktop sidebar as mobile primary navigation.
- Do not bury primary workflows in hamburger menus.
- Match, Appointment, Follow-up, Deal, Commission are contextual flows, not top-level tabs.

## 8. Today screen

Today is not a dashboard.

It answers:
"วันนี้ต้องทำอะไรต่อ?"

Priority:
1. urgent/due now
2. appointments today
3. follow-ups
4. useful opportunities/matches
5. secondary/completed items only if helpful

Prefer a clean vertical action list.

Avoid:
- KPI tiles
- revenue cards above work
- dashboard grids
- decorative analytics

Empty state:
- short explanation
- one primary CTA
- optional one-line guidance

## 9. Property list

Mobile uses compact operational cards/list items.

Information priority:
1. property/project name
2. sale/rent type/status
3. price
4. bedrooms / size / floor if known
5. location / BTS-MRT if known
6. availability state
7. match count if useful

Image:
- one useful thumbnail if available
- do not let image dominate scanning
- avoid marketplace-style giant photo cards by default

Hide low-frequency actions in context menu/detail page.
Never use a spreadsheet/table as the main mobile property list.

## 10. Client list

Information priority:
1. name/nickname
2. buy/rent
3. budget
4. preferred area
5. next follow-up / last contact
6. match count if useful

Quick actions may include:
- call
- LINE/share/contact
- follow-up

Do not render empty labels for missing optional data.

## 11. Forms

AgencySuit uses progressive disclosure.

Quick Add:
- normally <= 5 essential fields

Prefer:
- segmented controls
- chips
- numeric keypad for prices/budgets
- follow-up presets
- autocomplete for known project/location values

Avoid:
- 15–30-field forms
- optional metadata marked required
- asking for information already known
- giant dropdowns when search/autocomplete is better

Optional data:
- put under "เพิ่มรายละเอียด" or later detail editing
- never block initial save unless truly essential

Multi-step forms:
- only when they reduce cognitive load
- normally 2–3 short steps maximum
- do not create a wizard merely to look modern

## 12. Buttons and actions

Every screen needs one clear primary action.

Hierarchy:
- Primary: strongest
- Secondary: quieter
- Tertiary/text: supporting
- Destructive: clearly separated

Avoid:
- 3 equally prominent buttons side-by-side
- unclear icon-only actions
- excessive pill buttons

Prefer plain Thai:
- เพิ่มทรัพย์
- บันทึก
- นัดดู
- ติดตาม
- ส่งให้ลูกค้า

## 13. Bottom sheets, dialogs, menus

Prefer bottom sheet for:
- Quick Add
- short filters
- contextual actions
- status selection
- follow-up presets

Use a page/full-screen flow for:
- longer editing
- property/client details
- flows requiring several fields

Avoid:
- modal inside modal
- sheet inside sheet
- confirmations for harmless actions

Use confirmation for destructive/irreversible actions.

## 14. Empty states

Every empty state answers:
1. What is this?
2. Why does it matter?
3. What should user do now?

Bad:
"ไม่มีข้อมูล"

Better:
"ยังไม่มีทรัพย์ในระบบ"
"เพิ่มทรัพย์แรก เพื่อเริ่มจับคู่กับลูกค้า"
[เพิ่มทรัพย์]

No decorative illustration by default.

## 15. Loading / success / error

Loading:
- preserve layout where practical
- avoid full-page spinner for small actions
- disable duplicate submit while saving

Success:
- brief confirmation
- continue workflow automatically when obvious

Error:
- plain Thai
- validation near relevant field
- preserve user-entered data
- never expose Laravel exception/stack trace

## 16. Accessibility and usability

- Maintain readable contrast.
- Do not communicate state by color alone.
- Inputs need visible labels or accessible names.
- Icons need labels/accessibility text when meaning is not universal.
- Keep focus states.
- Error messages must be understandable.
- Do not crowd tap targets.
- Respect browser text scaling reasonably.

## 17. Anti-pattern blacklist

Do NOT produce these unless explicitly approved:
- dashboard with 6–12 statistic cards
- gradient hero inside authenticated app
- glassmorphism nav
- giant "Welcome back" hero
- left desktop sidebar on mobile
- horizontal CRM tables
- Kanban as primary mobile deal UI
- giant property image taking most of first screen
- every block inside rounded white card
- feature icon tile grids
- colorful status everywhere
- excessive badges
- tiny secondary text
- 20-field add forms
- nested modals
- decorative charts with no action value
- stock illustrations
- fake analytics
- lorem ipsum
- admin-template copying
- arbitrary emoji navigation
- excessive animation

## 18. Avoid the "AI-generated UI" look

Common bad AI-builder output:
- giant title + subtitle
- gradient
- 3–4 feature cards
- generic icon circles
- too many rounded rectangles
- weak information hierarchy
- badge soup
- "modern" styling that ignores workflow

AgencySuit should instead be:
- content first
- restrained
- consistent
- scannable
- task-oriented
- minimally decorative

## 19. UI contract before coding

For any NEW screen, Developer writes only 5 lines before coding:

- User goal:
- Primary action:
- Required information:
- Reference pattern:
- Intentionally omitted:

Example:
User goal: See what needs action today.
Primary action: Open the most urgent item.
Required information: time, person/property, action state.
Reference pattern: Linear Mobile inbox + Apple Reminders.
Intentionally omitted: KPI charts, revenue cards, filters.

No design essay.

## 20. Small-model implementation rule

Never prompt:
"make it beautiful" or "make it modern."

Translate UI into:
- information priority
- max required fields
- navigation structure
- component type
- empty/loading/error states
- viewport
- anti-patterns

If ambiguous:
- choose the simplest pattern already defined here
- do not invent a new visual language

## 21. UI task workflow

When UI changes:
1. Read relevant section in AGENTS.md.
2. Read this skill.
3. Inspect only relevant existing UI files/components.
4. Write the 5-line UI contract.
5. Implement 360–390px first.
6. Expand responsively.
7. Check empty/loading/error/success.
8. Run relevant tests.
9. Inspect 360x800, 390x844, 412x915.
10. Do not redesign unrelated screens.

## 22. UI handoff checklist

Developer Handoff adds only:

### UI Check
- Reference pattern:
- 360px: PASS/FAIL
- 390px: PASS/FAIL
- 412px: PASS/FAIL
- Horizontal scroll: YES/NO
- Required fields count:
- Anti-pattern check: PASS/FAIL

## 23. Tester UI gate

FAIL a UI change when:
- core action cannot be completed at 360px
- horizontal page scroll exists in core flow
- fixed nav/keyboard blocks critical action
- required form is unnecessarily long
- primary action is unclear
- mobile UI regresses into table/spreadsheet behavior
- generic dashboard cards replace intended workflow
- text/contrast materially hurts usability
- destructive action is too easy to trigger
- essential loading/error state is missing and creates duplicate/dangerous behavior

Pure taste alone is not a blocker unless it violates this skill or explicit requirement.

## 24. V1 screen reference map

- Login: simple modern productivity login
- Today: Linear Mobile inbox + Apple Reminders
- Property list: Airbnb hierarchy adapted to denser operational use
- Property Quick Add: progressive native-mobile input
- Client list: lightweight contact/CRM list
- Client Quick Add: short native contact-style form
- Follow-up: Apple Reminders quick scheduling
- Appointment: modern calendar/native calendar patterns
- Deal status: simple progress/status, NOT mobile Kanban
- More: native settings list
- Feedback: short bottom sheet/simple form

Do not copy branding, proprietary artwork, or exact layouts.

## 25. External reference sources

- Apple HIG: https://developer.apple.com/design/human-interface-guidelines/
- Material Design 3: https://m3.material.io/
- Linear Mobile: https://linear.app/mobile
- Linear Mobile redesign: https://linear.app/changelog/2025-10-16-mobile-app-redesign
- Mobbin reference library: https://mobbin.com/

Routine tasks should NOT browse these every time. This skill is intended to carry the recurring design rules.
