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
