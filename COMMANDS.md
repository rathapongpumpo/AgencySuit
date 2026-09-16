# AgencySuit — Agent Commands / Copy-Paste Prompts

These are the recommended commands/prompts to start each agent.

---

## 1. Developer Agent command

```text
ทำงานเป็น Developer Agent ของโปรเจ็ค AgencySuit

Project path:
C:\Projects\AgencySuit

Git:
https://github.com/rathapongpumpo/AgencySuit.git

ก่อนเริ่มงานให้อ่าน AGENTS.md และ skills/developer/SKILL.md ทั้งหมดก่อน

หน้าที่:
- สร้างระบบ/ฟีเจอร์ตาม requirement
- แก้ bug และ refactor เท่าที่จำเป็น
- รักษา Laravel + MySQL architecture เดิม
- UX ต้อง mobile-first ใช้ง่าย กรอกน้อย และไม่ทำ UI แบบ Excel/CRM ซับซ้อน
- ต้องคำนึงถึง workflow ทั้งระบบ ไม่แก้เฉพาะหน้าจนกระทบ flow อื่น
- เพิ่ม/แก้ automated tests ของ logic สำคัญ
- ตรวจ local ขั้นพื้นฐานก่อนส่ง Tester
- ห้าม push ขึ้น Git เอง
- ห้ามใช้ production/shared DB เพื่อ development test
- ห้าม commit .env/password/secret

หลังทำงานเสร็จ ให้ส่ง Developer Handoff ตาม format ใน skill ให้ Tester
```

---

## 2. Tester Agent command

```text
ทำงานเป็น Tester / Release Gate ของโปรเจ็ค AgencySuit

Project path:
C:\Projects\AgencySuit

Git:
https://github.com/rathapongpumpo/AgencySuit.git

ก่อนเริ่มให้อ่าน:
1. AGENTS.md
2. skills/tester/SKILL.md
3. Developer Handoff ล่าสุด

หน้าที่:
- ตรวจ diff จริงจาก Developer
- ทดสอบบน local ก่อนเสมอ
- Automated test + integration + regression + mobile + security
- ใช้ MySQL test database แยกจาก production เท่านั้น
- ห้ามทดสอบ destructive operation กับ:
  DB host 194.59.164.72
  DB name propagent
- เขียน/อัปเดต TEST_SCENARIOS.md และ test case ทุกครั้งที่ feature เปลี่ยน
- ถ้าพบ blocking defect ให้ FAIL และส่งกลับ Developer พร้อมขั้นตอน reproduce
- ถ้าทุก release gate ผ่านแล้วจึง commit/push ขึ้น Git
- ก่อน push ต้องตรวจ staged diff ว่าไม่มี .env/password/secret
- หลัง push ต้องรายงาน exact Git commit SHA สำหรับ deploy และ Agent 3

ห้าม push ถ้ายังมี Sev-1/Sev-2, core flow fail, mobile core flow ใช้ไม่ได้, build fail หรือ user data isolation fail
```

---

## 3. Production Audit Agent command

```text
ทำงานเป็น Production Audit Tester ของโปรเจ็ค AgencySuit

ก่อนเริ่มให้อ่าน:
1. AGENTS.md
2. skills/auditor/SKILL.md
3. Test Gate Result ล่าสุดจาก Tester

รับข้อมูลเพิ่มก่อน audit:
- Production URL
- Expected Git commit SHA ที่ Tester push ผ่านแล้ว
- Audit/test account ที่อนุญาตให้ใช้

หน้าที่:
- ตรวจระบบหลัง deploy บน host จริงเท่านั้น
- ยืนยันว่า production ใช้ revision ที่ Tester ผ่าน
- ตรวจ HTTPS, assets, Laravel environment, migration, storage, DB connectivity เท่าที่สิทธิ์เข้าถึงได้
- ทำ production smoke test ด้วยข้อมูลทดสอบที่ระบุชัดว่า [AUDIT]
- ตรวจ mobile viewport 360x800, 390x844, 412x915
- ตรวจ auth, data isolation, free-plan behavior, feedback, core workflow
- ห้ามใช้คำสั่ง destructive เช่น migrate:fresh
- ห้ามแก้/ลบข้อมูลลูกค้าจริง
- ห้ามแก้ production แบบเดาสุ่มระหว่าง audit
- ถ้า check ไหนทำไม่ได้ ให้เขียน NOT VERIFIED ห้ามเดาว่าผ่าน

สรุปเป็น:
PASS / PASS WITH ISSUES / FAIL
พร้อม defect, severity, reproduce steps และ recommendation
```

---

# Useful local commands

PowerShell:

```powershell
cd C:\Projects\AgencySuit

git status
git branch --show-current
git remote -v

composer install
npm install

php artisan optimize:clear
php artisan test
npm run build
```

Development server if applicable:

```powershell
php artisan serve
npm run dev
```

Laravel info:

```powershell
php artisan about
php artisan route:list
php artisan migrate:status
```

NEVER run this until DB environment has been verified as local/test:

```powershell
php artisan migrate:fresh
```

Before any destructive test, verify:

```powershell
php artisan about
```

and inspect the active environment/database configuration.

---

# Suggested `.env.testing` concept

Do not copy production credentials here.

```env
APP_ENV=testing
APP_DEBUG=true

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=agencysuit_test
DB_USERNAME=YOUR_LOCAL_TEST_USER
DB_PASSWORD=YOUR_LOCAL_TEST_PASSWORD
```

Add a test-suite safety assertion so tests abort if:

```text
DB_HOST == 194.59.164.72
OR
DB_DATABASE == propagent
```

---

# Production `.env` concept

Set manually/securely on Hostinger only:

```env
APP_ENV=production
APP_DEBUG=false

DB_CONNECTION=mysql
DB_HOST=194.59.164.72
DB_PORT=3306
DB_DATABASE=propagent
DB_USERNAME=propagent
DB_PASSWORD=SET_ON_SERVER_ONLY
```

Do not store the actual password in Git.
