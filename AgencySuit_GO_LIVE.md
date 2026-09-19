# AgencySuit — Go Live Master Plan

> Single Source of Truth สำหรับ AI Agent ก่อนนำ AgencySuit ขึ้นใช้งานจริง
>
> Project: `C:\Projects\AgencySuit`  
> Stack: Laravel + Blade + MySQL  
> Hosting: Hostinger  
> Production DB: `194.59.164.72 / propagent / propagent`  
> **ห้ามเก็บ password, API key, OAuth secret, webhook secret หรือ `.env` ใน Git**

---

## 1) Launch Mode

กำหนดก่อนเริ่ม ห้าม Agent เลือกเอง:

```text
LAUNCH_MODE = PAID
```

หรือ

```text
LAUNCH_MODE = FREE_BETA
```

### PAID
ต้องผ่าน Payment, Pricing, Refund, Pro entitlement และ Legal/Trust ทั้งหมด

### FREE_BETA
ข้าม Payment ได้ชั่วคราว แต่ต้องระบุ Beta ชัดเจน และห้ามมี checkout ปลอม

---

## 2) Go Live Flow

```text
Final Development
→ Developer Self-check
→ Tester Full Regression
→ PASS
→ Commit + Push Git
→ Backup Production
→ Deploy Hostinger
→ Production Migration
→ Production Config/Cache
→ Production Smoke Test
→ Audit Agent
→ PASS
→ SEO/Analytics/Search Console
→ Enable Live Payment (PAID)
→ Final Purchase Test
→ GO LIVE
→ Monitor 24h
→ Review 7d
```

ถ้า Gate ใด FAIL = **หยุด Go Live**

---

## 3) Core Product Must Pass

Production ต้องทำ flow นี้ได้ครบ:

```text
Register/Login
→ Property
→ Property Photos
→ Client
→ Matching
→ Share Property
→ Follow-up
→ Today
→ Appointment
→ Deal
→ Close Deal
→ Commission
→ Feedback
→ Logout/Login
→ Data persists
```

Checklist:

- [ ] Email Register/Login/Logout
- [ ] Forgot/Reset Password
- [ ] Google Login
- [ ] Guest protection
- [ ] Property create/edit/view/status
- [ ] Property photos + primary image
- [ ] Client create/edit/view
- [ ] Property ↔ Client Matching
- [ ] Share Property
- [ ] Follow-up
- [ ] Today page
- [ ] Appointment
- [ ] Deal + stages
- [ ] Commission
- [ ] Feedback
- [ ] Free limits
- [ ] Pro entitlement
- [ ] Data persistence

---

## 4) UX/UI Release Gate

Target: `360x800`, `390x844`, `412x915`

- [ ] No horizontal overflow
- [ ] Bottom nav ไม่บัง content
- [ ] Keyboard ไม่บัง CTA
- [ ] Touch target ~44–48px+
- [ ] Primary CTA ชัด
- [ ] Quick Add ทุก action ใช้งานจริง
- [ ] Form ไม่ยาวโดยไม่จำเป็น
- [ ] Optional fields ไม่ถูกบังคับ
- [ ] Validation error อยู่ใกล้ field/action
- [ ] Empty/loading/success/error states ครบ
- [ ] ไม่มี mobile table
- [ ] ไม่มี generic admin dashboard
- [ ] ไม่มี fake analytics
- [ ] ไม่มี placeholder/lorem ipsum
- [ ] ไม่มี dead button/dead link

---

## 5) Production Environment

Production `.env` ตั้งบน Hostinger เท่านั้น:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://YOURDOMAIN.com

DB_CONNECTION=mysql
DB_HOST=194.59.164.72
DB_DATABASE=propagent
DB_USERNAME=propagent
DB_PASSWORD=<SERVER SECRET ONLY>
```

ตรวจ:

- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] `APP_URL` เป็น HTTPS domain จริง
- [ ] Production APP_KEY ตั้งแล้ว
- [ ] `.env` / `.env.testing` ไม่อยู่ Git
- [ ] ไม่มี secret ใน JS bundle/repository
- [ ] timezone / locale ถูกต้อง
- [ ] session cookie secure เมื่อใช้ HTTPS

---

## 6) Domain / DNS / HTTPS

- [ ] Domain จดแล้ว
- [ ] DNS ชี้ Hostinger ถูกต้อง
- [ ] เลือก canonical host: `domain.com` หรือ `www.domain.com`
- [ ] อีกแบบ redirect 301 ไป canonical
- [ ] HTTP → HTTPS
- [ ] SSL valid
- [ ] ไม่มี mixed content
- [ ] Google OAuth callback ใช้ production HTTPS URL
- [ ] Payment webhook ใช้ production HTTPS URL

---

## 7) Laravel Production Deploy

ก่อน Deploy:

- [ ] Tester PASS
- [ ] Working tree clean
- [ ] มี approved commit SHA
- [ ] Backup Production DB

Commands ตาม environment ที่รองรับ:

```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize
```

ถ้ามี frontend build:

```bash
npm ci
npm run build
```

ตรวจ:

- [ ] PHP version/extension รองรับ Laravel
- [ ] Migration PASS
- [ ] Storage/log/temp upload writable
- [ ] Public root ชี้ Laravel `/public` ถูกต้อง
- [ ] 404 ถูกต้อง
- [ ] 500 ไม่เปิด stack trace

**ห้าม `php artisan migrate:fresh` บน Production**

---

## 8) Database / Backup / Restore

- [ ] Automatic DB backup รายวัน
- [ ] Retention อย่างน้อย 7 วัน
- [ ] Manual backup ก่อน migration สำคัญ
- [ ] Backup uploaded photos/storage
- [ ] Backup ไม่อยู่ที่เดียวกับ production อย่างเดียว
- [ ] มี documented restore procedure
- [ ] ทดสอบ restore อย่างน้อย 1 ครั้งก่อน commercial launch
- [ ] Migration status ถูกต้อง
- [ ] ไม่มี test data ที่ควรลบ

User A ต้องเข้าถึงข้อมูล User B ไม่ได้ทุก entity:

- [ ] Property
- [ ] Photo
- [ ] Client
- [ ] Follow-up
- [ ] Appointment
- [ ] Deal
- [ ] Commission
- [ ] Feedback/private data

**Cross-user leakage = NO GO**

---

## 9) Property Image Production Check

- [ ] JPG/JPEG/PNG/WebP
- [ ] Invalid MIME rejected
- [ ] Corrupt image rejected
- [ ] Oversize rejected
- [ ] Primary image deterministic
- [ ] Change primary
- [ ] Delete primary fallback
- [ ] Ownership isolation
- [ ] Private storage
- [ ] Production GD/equivalent ตรวจจริง
- [ ] ถ้าไม่มี GD fallback ไม่ fatal

ห้ามอ้างว่ามี thumbnail/optimization ถ้า Production ไม่ได้ทำจริง

---

## 10) Authentication / Email

### Auth
- [ ] Email register/login/logout
- [ ] Wrong password handling
- [ ] Forgot password
- [ ] Reset password
- [ ] Google OAuth production client
- [ ] Redirect URI production ถูกต้อง
- [ ] Existing email ไม่ duplicate
- [ ] OAuth cancel/failure ไม่ 500

### Transactional Email
- [ ] SMTP/API production configured
- [ ] From name/email ถูกต้อง
- [ ] Reset email เข้า Inbox
- [ ] Reset link เป็น Production HTTPS
- [ ] SPF
- [ ] DKIM
- [ ] DMARC ตามความเหมาะสม
- [ ] ไม่มี dev URL ใน email

Password Reset ใช้ไม่ได้ = **NO GO**

---

## 11) Security Gate

- [ ] CSRF protection
- [ ] Secure password hashing
- [ ] Session regeneration after login
- [ ] Session invalidation after logout
- [ ] Login rate limit
- [ ] Password-reset rate limit
- [ ] Feedback/abusable endpoint rate limit
- [ ] Authorization Policies ครบ
- [ ] Upload MIME + size validation
- [ ] No directory traversal
- [ ] No SQL injection pattern
- [ ] No unsafe mass assignment
- [ ] No secret in source/JS/log
- [ ] No debug toolbar/test route
- [ ] Error page ไม่ expose secret

Headers ตามความเหมาะสม:

- [ ] HSTS หลัง HTTPS stable
- [ ] `X-Content-Type-Options: nosniff`
- [ ] `Referrer-Policy`
- [ ] `Permissions-Policy`
- [ ] CSP ถ้าทดสอบแล้วว่าไม่ทำเว็บพัง

---

## 12) Privacy / Trust / Legal

Public site ต้องมี:

- [ ] Privacy Policy
- [ ] Terms of Service
- [ ] Refund Policy (PAID)
- [ ] Contact
- [ ] Pricing
- [ ] FAQ

Privacy ต้องอธิบายอย่างน้อย:

- เก็บข้อมูลอะไร
- ใช้ข้อมูลทำอะไร
- Property/Client data
- Authentication providers
- Analytics
- Payment provider
- Retention/deletion
- Contact

ถ้าใช้ non-essential cookies/analytics ให้ตรวจ consent requirement ตามตลาดจริง

**Agent ห้ามเขียน claim ว่า “PDPA compliant” หรือ “legal compliant” โดยไม่มี human/legal review**

---

## 13) Free / Pro Rules

ขั้นต่ำปัจจุบัน:

```text
FREE
Properties: 10
Clients: 5
Photos/property: 3
Active deals: 1
```

- [ ] Limits มาจาก centralized config
- [ ] Limit ไม่ลบข้อมูลเดิม
- [ ] Pro limits/entitlements กำหนดชัด
- [ ] Pricing หน้า Landing/Pricing/Upgrade/Checkout ตรงกัน
- [ ] Lifetime/subscription ระบุชัด
- [ ] ไม่มี hidden fee

---

## 14) Lemon Squeezy — PAID Only

ใช้ Test Mode ก่อน Live Mode เสมอ

### Store
- [ ] Store created
- [ ] Store approved/activated
- [ ] Payout configured
- [ ] Store currency/price final
- [ ] Product created
- [ ] Variant created

### Test Mode
- [ ] Test checkout
- [ ] Success redirect
- [ ] Cancel/fail behavior
- [ ] ส่ง `user_id` เป็น custom checkout data
- [ ] Webhook received
- [ ] Verify `X-Signature`
- [ ] Duplicate webhook idempotent
- [ ] User upgrade เป็น Pro ครั้งเดียว
- [ ] Logout/Login แล้วยัง Pro

Lifetime/one-time:

- [ ] `order_created` → Pro
- [ ] `order_refunded` → defined behavior
- [ ] Order ID เก็บในระบบ
- [ ] Refund handling มี implementation หรือ documented manual process

### Live Mode
- [ ] Live API key
- [ ] Live Product/Variant ID
- [ ] Live checkout URL
- [ ] Live webhook endpoint
- [ ] Live signing secret
- [ ] `.env` production updated
- [ ] config cache refreshed
- [ ] Final real payment E2E test ถ้าทำได้

Lemon Squeezy รองรับ THB และประเทศไทยสำหรับ bank payouts ตามเอกสารปัจจุบัน แต่ Agent ต้องตรวจค่าใน Dashboard จริงก่อน Launch

---

## 15) Public Website Structure

ขั้นต่ำ:

```text
/
/features
/pricing
/faq
/privacy
/terms
/contact
/login
/register
```

แนะนำ:

```text
/guides
/blog
```

Private App routes เช่น `/today`, `/properties`, `/clients`, `/deals`, `/settings` ต้องไม่เป็น SEO landing pages

---

# SEO CHECKLIST

## 16) SEO Architecture

**Public marketing pages = indexable**  
**Private application pages = authentication protected + non-indexable ตามความเหมาะสม**

- [ ] Public pages HTTP 200
- [ ] Private routes require auth
- [ ] Private/auth utility pages ใช้ `noindex` ตามความเหมาะสม
- [ ] Canonical URLs ถูกต้อง
- [ ] HTTP/WWW duplicate redirect ถูกต้อง
- [ ] Search/filter URLs ไม่สร้าง duplicate index จำนวนมาก

`robots.txt` ไม่ใช่ security และไม่ควรใช้แทน Authentication

---

## 17) robots.txt

Production ต้องมี `/robots.txt`

Concept:

```text
User-agent: *
Disallow: /today
Disallow: /properties
Disallow: /clients
Disallow: /settings

Sitemap: https://YOURDOMAIN.com/sitemap.xml
```

หมายเหตุ: `Disallow` ไม่รับประกันว่า URL จะไม่ถูก index ถ้ามี link จากที่อื่น

---

## 18) XML Sitemap

สร้าง `/sitemap.xml`

ใส่เฉพาะ canonical public indexable URLs:

- Home
- Features
- Pricing
- FAQ
- Public Guide/Blog posts

ห้ามใส่:

- Login/Register
- Today
- Private Property/Client pages
- Account/Settings

ตรวจ:

- [ ] HTTP 200
- [ ] Valid XML
- [ ] HTTPS absolute URLs
- [ ] Canonical URLs only
- [ ] robots.txt references sitemap
- [ ] Submit Google Search Console

---

## 19) SEO Metadata

ทุก Public Page ต้องมี unique:

- [ ] `<title>`
- [ ] Meta description
- [ ] Canonical
- [ ] OG title
- [ ] OG description
- [ ] OG image
- [ ] Favicon
- [ ] Brand/site name consistency

ห้าม title แบบ `Home` และห้าม description ซ้ำทุกหน้า

ตัวอย่าง direction (ต้องแทนชื่อ Brand จริง):

```text
Home: [Brand] — ระบบจัดการทรัพย์และลูกค้าสำหรับนายหน้าอสังหา
Features: ระบบจัดการทรัพย์ ลูกค้า และ Follow-up สำหรับนายหน้า | [Brand]
Pricing: ราคา [Brand] — เริ่มใช้ฟรีสำหรับนายหน้าอสังหา
```

---

## 20) SEO Keyword Research — Thailand V1

Seed keywords:

```text
โปรแกรมนายหน้าอสังหา
ระบบนายหน้าอสังหา
CRM นายหน้าอสังหา
โปรแกรมเก็บข้อมูลทรัพย์
ระบบจัดการทรัพย์
ระบบจัดการลูกค้านายหน้า
โปรแกรมจัดการลูกค้าอสังหา
โปรแกรมจัดการ listing อสังหา
จัดการทรัพย์นายหน้า
ระบบ follow up ลูกค้าอสังหา
โปรแกรมจับคู่ลูกค้ากับทรัพย์
```

ก่อนทำ Content:

- [ ] ตรวจ Google SERP จริง
- [ ] ระบุ search intent
- [ ] ดูคู่แข่งหน้าแรก
- [ ] แต่ละหน้าแก้ pain ชัด
- [ ] CTA ไปทดลองใช้ฟรี
- [ ] ไม่ keyword stuffing
- [ ] ไม่สร้างบทความ AI บาง/ซ้ำจำนวนมาก

---

## 21) Minimum SEO Content

Landing page ต้องตอบเร็ว:

- ระบบคืออะไร
- สำหรับใคร
- แก้ pain อะไร
- ทดลองใช้ฟรีอย่างไร

Feature content อย่างน้อย:

- จัดเก็บทรัพย์
- จัดการลูกค้า
- Matching
- Follow-up
- นัดดู
- Deal/Commission

FAQ อย่างน้อย:

- ใช้ฟรีได้แค่ไหน
- ต้องติดตั้งแอปไหม
- ใช้บนมือถือได้ไหม
- ข้อมูลปลอดภัยอย่างไร
- ย้ายจาก Excel ได้ไหม
- ราคา/จ่ายครั้งเดียวหรือรายเดือน
- Refund อย่างไร (PAID)

Recommended Guides:

1. วิธีจัดการทรัพย์สำหรับนายหน้าอสังหาที่ทำงานคนเดียว
2. วิธี Follow-up ลูกค้าอสังหาไม่ให้หลุด
3. ข้อจำกัดของ Excel สำหรับจัดการทรัพย์
4. วิธีจับคู่ลูกค้ากับทรัพย์ให้เร็วขึ้น
5. CRM สำหรับนายหน้าอสังหาจำเป็นไหม

---

## 22) Structured Data

Public site พิจารณา JSON-LD:

- [ ] `Organization`
- [ ] `SoftwareApplication`

SoftwareApplication fields ตามข้อมูลจริง เช่น:

- name
- operatingSystem = Web
- applicationCategory
- description
- offers / price / currency

- [ ] Validate JSON-LD
- [ ] Google Rich Results Test
- [ ] ไม่มี fake rating/review

---

## 23) Technical SEO

- [ ] Semantic HTML
- [ ] 1 clear H1 ต่อหน้า
- [ ] Heading hierarchy ถูกต้อง
- [ ] Crawlable `<a href>` internal links
- [ ] Images มี appropriate alt
- [ ] Logo alt
- [ ] No broken links
- [ ] 404 return HTTP 404
- [ ] Permanent redirect ใช้ 301/308
- [ ] No duplicate homepage variants
- [ ] Canonical correct
- [ ] robots correct
- [ ] sitemap correct

---

## 24) Core Web Vitals / Performance

Google “Good” targets:

- LCP ≤ 2.5s
- INP < 200ms
- CLS < 0.1

Checklist:

- [ ] Optimize landing images
- [ ] WebP/AVIF ตามความเหมาะสม
- [ ] Set image width/height
- [ ] Lazy load below-fold images
- [ ] ลด render-blocking assets
- [ ] Production JS/CSS build
- [ ] ไม่มี huge unused JS
- [ ] Fonts ไม่ block เกินจำเป็น
- [ ] Property thumbnails optimized
- [ ] Browser caching
- [ ] gzip/brotli ถ้า Host รองรับ

Tools:

- Chrome Lighthouse
- PageSpeed Insights
- Search Console Core Web Vitals

ไม่ต้องไล่ Lighthouse 100 ถ้าทำ UX แย่ลง

---

## 25) Google Search Console

หลัง Domain จริงพร้อม:

- [ ] Add Domain Property
- [ ] DNS Verification
- [ ] Submit sitemap
- [ ] Inspect Home
- [ ] Inspect Features
- [ ] Inspect Pricing
- [ ] Check indexing
- [ ] Check HTTPS
- [ ] Check Core Web Vitals
- [ ] Check Manual Actions
- [ ] Check Security Issues

หลัง Go Live ติดตาม:

- indexed pages
- impressions
- clicks
- CTR
- queries
- crawl/index errors

---

## 26) Analytics / Conversion

อย่างน้อยวัด:

- [ ] Landing view
- [ ] Register click
- [ ] Registration success
- [ ] Login success
- [ ] First Property
- [ ] First Client
- [ ] First Match
- [ ] Free limit reached
- [ ] Upgrade viewed
- [ ] Checkout started
- [ ] Purchase completed
- [ ] Feedback submitted

Funnel:

```text
Landing
→ Register
→ First Property
→ First Client
→ Match
→ Free Limit
→ Upgrade
→ Purchase
```

ถ้าใช้ GA4:

- [ ] Production Measurement ID
- [ ] Event ไม่ยิงซ้ำ
- [ ] ห้ามส่ง PII เช่น email/phone/client name
- [ ] Exclude test/audit traffic ถ้าทำได้

---

## 27) Social Preview / Trust

- [ ] OG image จริง
- [ ] OG title/description
- [ ] Favicon
- [ ] Touch icon
- [ ] Facebook preview
- [ ] LINE link preview

Landing trust:

- [ ] “ใช้ฟรี” ถ้าเป็นจริง
- [ ] “ไม่ต้องใส่บัตร” ถ้าเป็นจริง
- [ ] ราคาโปร่งใส
- [ ] Contact จริง
- [ ] Privacy/Terms/Refund
- [ ] Screenshot ระบบจริง
- [ ] Testimonial เฉพาะของจริง

**ห้าม fake testimonial/review**

---

## 28) Support / Monitoring

- [ ] Support email
- [ ] Contact/Feedback path
- [ ] Laravel production logging
- [ ] Logs ไม่ public
- [ ] Logs ไม่เก็บ password/token
- [ ] Log rotation/retention
- [ ] Failed webhook traceable
- [ ] 500 traceable internally
- [ ] Health endpoint เช่น `/up` ไม่ expose secret
- [ ] Optional external uptime monitor: Home/Login/Health

---

## 29) Final Tester Release Gate

Tester ต้องรัน:

- [ ] Clean test DB migrations
- [ ] Full `php artisan test`
- [ ] Full E2E
- [ ] Mobile 360/390/412
- [ ] Build PASS
- [ ] Pint PASS
- [ ] Secret scan PASS
- [ ] No Sev-1
- [ ] No Sev-2
- [ ] No data leak
- [ ] `TEST_SCENARIOS.md` updated

Tester เท่านั้น Push release ที่ผ่าน Gate

บันทึก:

```text
RELEASE_SHA=<exact SHA>
```

---

## 30) Deploy / Production Audit

ก่อน Deploy:

- [ ] DB backup timestamp
- [ ] Storage backup status
- [ ] Current production SHA
- [ ] Target SHA
- [ ] Rollback method

หลัง Deploy ตรวจ:

```bash
php artisan about
php artisan migrate:status
```

- [ ] Target SHA deployed
- [ ] Migration success
- [ ] APP_DEBUG false
- [ ] Assets correct
- [ ] Production URL 200
- [ ] Login works
- [ ] Upload works
- [ ] Password reset works
- [ ] Google login works

Audit Agent ทำ Production E2E ด้วยข้อมูลขึ้นต้น `[AUDIT]`

```text
Login
→ Property
→ Photo
→ Client
→ Match
→ Share
→ Follow-up
→ Today
→ Appointment
→ Deal
→ Commission
→ Feedback
→ Logout/Login
```

Audit result:

```text
PASS
PASS WITH ISSUES
FAIL
```

**FAIL = ห้าม Go Live**

---

## 31) SEO Gate ก่อนเปิด Index

- [ ] Final domain
- [ ] Final brand
- [ ] Titles/meta final
- [ ] Landing copy final
- [ ] Privacy/Terms final
- [ ] Canonical final
- [ ] sitemap final
- [ ] robots final
- [ ] private pages noindex/auth protected
- [ ] Structured data tested
- [ ] PageSpeed checked
- [ ] Search Console configured

อย่า submit sitemap ตอนเว็บยังเป็น staging/placeholder

---

## 32) Launch Day

1. [ ] Enable Public Landing
2. [ ] Verify robots/sitemap
3. [ ] Submit sitemap
4. [ ] Request index Home
5. [ ] Enable Live Checkout (PAID)
6. [ ] Final payment E2E (PAID)
7. [ ] Verify Pro activation
8. [ ] Verify receipt/email
9. [ ] Verify analytics
10. [ ] Verify feedback
11. [ ] Check logs
12. [ ] Check uptime

เมื่อทั้งหมดผ่านจึงประกาศ:

```text
GO LIVE
```

---

## 33) First 24 Hours

Monitor:

- [ ] 5xx
- [ ] Login/OAuth failures
- [ ] Email failures
- [ ] Upload failures
- [ ] DB errors
- [ ] Slow pages
- [ ] Checkout failures
- [ ] Webhook failures
- [ ] Duplicate Pro activation
- [ ] Feedback
- [ ] Mobile issues

24 ชั่วโมงแรกหลีกเลี่ยง feature ใหม่ ยกเว้น critical bug fix

---

## 34) First 7 Days / 30 Days SEO

7-day review:

- Registrations
- First Property rate
- First Client rate
- Matching usage
- Free limit reached
- Upgrade views
- Purchases
- Feedback/support
- Search Console indexing

30-day SEO:

- [ ] Search Console weekly
- [ ] Index status
- [ ] Queries
- [ ] CTR
- [ ] Core Web Vitals
- [ ] Broken pages
- [ ] Sitemap status
- [ ] เพิ่ม useful guide 1–2 ชิ้น/สัปดาห์ถ้าทำได้
- [ ] ใช้คำถามจริงจากลูกค้าเป็นหัวข้อ
- [ ] Internal links ไป Landing/Features/Pricing
- [ ] ไม่ mass-generate thin AI content

---

## 35) NO-GO Conditions

ห้าม Go Live ถ้ามีข้อใดข้อหนึ่ง:

- Cross-user data leak
- `APP_DEBUG=true`
- Secret/password ใน Git
- Migration fail
- Login fail
- Password reset fail
- Google Login พังทั้งที่เปิดให้ใช้
- Property/Client data loss
- Upload ทำให้ 500
- Free limit ลบข้อมูล
- Payment สำเร็จแต่ Pro ไม่ activate
- Webhook signature ไม่ verify
- Checkout ยังเป็น Test Mode ขณะขายจริง
- HTTPS/SSL error
- Core mobile flow ใช้ไม่ได้
- ไม่มี backup
- ไม่รู้วิธี restore
- ไม่มี Privacy/Terms สำหรับ commercial launch
- Production Audit = FAIL

---

## 36) AI Agent Rules

ทุก Agent ต้อง:

1. อ่านไฟล์นี้ก่อนงาน Go Live
2. ห้าม mark PASS ถ้าไม่ได้ตรวจจริง
3. ถ้าตรวจไม่ได้ใช้ `NOT VERIFIED`
4. ห้ามสร้าง credential เอง
5. ห้ามใช้ destructive Production DB command
6. ห้ามลด Acceptance Criteria เพื่อให้ผ่าน
7. ห้าม disable test เพื่อ release
8. ห้ามซ่อน defect
9. ห้าม deploy untested SHA
10. PASS ต้องมี Evidence

ตัวอย่าง Evidence:

```text
PASS — php artisan test: 128 tests / 620 assertions
PASS — Production URL: HTTP 200
PASS — sitemap.xml: HTTP 200 / valid XML
PASS — Search Console Domain Property verified
```

---

## 37) Required Go Live Report

```text
# Go Live Readiness Report

Launch Mode:
Domain:
Target SHA:

Product: PASS / FAIL
Full Test: PASS / FAIL
Tests / Assertions:
Mobile 360 / 390 / 412:
Security: PASS / FAIL
Production Environment: PASS / FAIL
Database Backup: PASS / FAIL
Authentication: PASS / FAIL
Email: PASS / FAIL
Payments: PASS / FAIL / N/A
SEO: PASS / FAIL
Analytics: PASS / FAIL
Legal / Trust: PASS / FAIL
Production Audit: PASS / PASS WITH ISSUES / FAIL

Blocking Issues:
- ...

Non-blocking Issues:
- ...

Final Decision:
GO LIVE / NO GO
```

Agent ห้ามตอบ `GO LIVE` ถ้ายังมี Blocking Issue

---

## 38) Official References

Laravel Deployment  
https://laravel.com/framework/docs/deployment

Google SEO Starter Guide  
https://developers.google.com/search/docs/fundamentals/seo-starter-guide

Google Crawling & Indexing  
https://developers.google.com/search/docs/crawling-indexing

Google Canonicalization  
https://developers.google.com/search/docs/crawling-indexing/canonicalization

Google Page Experience  
https://developers.google.com/search/docs/appearance/page-experience

Google Core Web Vitals  
https://developers.google.com/search/docs/appearance/core-web-vitals

Google SoftwareApplication Structured Data  
https://developers.google.com/search/docs/appearance/structured-data/software-app

Lemon Squeezy Developer Guide  
https://docs.lemonsqueezy.com/guides/developer-guide

Lemon Squeezy Testing & Going Live  
https://docs.lemonsqueezy.com/guides/developer-guide/testing-going-live

Lemon Squeezy Webhooks  
https://docs.lemonsqueezy.com/help/webhooks

Lemon Squeezy Custom Data  
https://docs.lemonsqueezy.com/help/checkout/passing-custom-data

Lemon Squeezy Currencies  
https://docs.lemonsqueezy.com/help/payments/currencies

Hostinger Laravel Deployment  
https://www.hostinger.com/tutorials/how-to-deploy-laravel/

---

# Final Rule

**เว็บเปิดได้ ≠ พร้อมขาย**

Go Live ต้องผ่านพร้อมกัน:

```text
Product
+ Security
+ Infrastructure
+ Backup/Restore
+ Authentication/Email
+ Payment (ถ้าขาย)
+ SEO
+ Analytics
+ Trust/Legal
+ Production Audit
```
