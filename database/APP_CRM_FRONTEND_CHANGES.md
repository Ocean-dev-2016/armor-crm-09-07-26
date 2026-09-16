# Armor CRM — App Side Changes (Frontend / Android Developer Document)

**Project:** Armor CRM Mobile App (Android)  
**Date:** 16-Sep-2026  
**Backend Reference:** `APP_CRM_BACKEND_CHANGES.md`

---

## 1. Overview

5 UI flows to implement. Each section covers: **Screen**, **Fields**, **Validation**, **API calls**, and **Navigation**.

---

## 2. Feature Summary

| # | Feature | Screen / Popup | APIs Used |
|---|---------|----------------|-----------|
| 1 | Morning Daily Plan | Punch IN popup | 236, 12, 20 |
| 2 | Evening Completion | Punch OUT popup | 237, 238, 20 |
| 3 | Visit Start Type | Visit Start screen | 239, 75 |
| 4 | Visit Complete Q&A | Visit Stop popup | 122 |
| 5 | PI/Quotation Popup | Submit popup | 240, 233/234, 165 |

---

## 3. Feature 1 — Morning Daily Plan (Punch IN)

### 3.1 Trigger
User taps **Punch IN** button on Attendance screen.

### 3.2 UI Flow

```
Punch IN tap
    → Open "Daily Plan" popup/dialog (full screen or bottom sheet)
    → User fills questions
    → Validate
    → If order/approval filled → show Customer dropdown
    → Submit plan (API 236)
    → On success → call Punch IN (API 20)
    → Show success message
```

### 3.3 Screen: Daily Plan Popup

**Title:** `Today's Plan` / `આજનો પ્લાન`

#### Question 1 — Expected Order Amount

| UI Element | Type | Field Key | Label (Gujarati) | Label (English) |
|------------|------|-----------|------------------|-----------------|
| Label | Text | — | શું આજે કેટલા રૂપિયાના ઓર્ડર આવશે? | How much order amount expected today? |
| Input | Number (decimal) | `expected_order_amount` | Amount (₹) | Amount (₹) |
| Button | OK | — | OK | OK |

#### Question 2 — Expected Approvals

| UI Element | Type | Field Key | Label (Gujarati) | Label (English) |
|------------|------|-----------|------------------|-----------------|
| Label | Text | — | શું આજે કેટલા Approval આવશે? | How many approvals expected today? |
| Input | Number (integer) | `expected_approval_count` | Count | Count |
| Button | OK | — | OK | OK |

#### Question 3 — Expected Project Details

| UI Element | Type | Field Key | Label (Gujarati) | Label (English) |
|------------|------|-----------|------------------|-----------------|
| Label | Text | — | આજે કેટલા project ની Detail આવશે? | How many project details expected today? |
| Input | Number (integer) | `expected_project_detail_count` | Count | Count |
| Button | OK | — | OK | OK |

#### Footer Buttons

| Button | Action |
|--------|--------|
| **Submit Plan** | Validate + call API 236 |
| **Cancel** | Close popup (Punch IN not done) |

### 3.4 Validation Rules (App-side)

| Rule | Error Message |
|------|---------------|
| At least 1 of 3 fields must be filled and > 0 | `કૃપા કરીને ઓછામાં ઓછું એક target ભરો` / `Please fill at least one target` |
| Order amount must be numeric ≥ 0 | `Invalid amount` |
| Approval count must be integer ≥ 0 | `Invalid count` |
| Project detail count must be integer ≥ 0 | `Invalid count` |

### 3.5 Conditional — Customer Dropdown

**Show when:** `expected_order_amount > 0` OR `expected_approval_count > 0`

| UI Element | Type | Field Key | Description |
|------------|------|-----------|-------------|
| Label | Text | — | Select Customer |
| Dropdown | Searchable spinner | `customer_id` | Shows assigned customers only |
| Display format | — | — | `[ client_code - customer_name ]` e.g. `C001 - ABC Traders` |
| Search box | Text input | `search` | Filters dropdown list |

**API for dropdown:** `get_customer` (12)  
**Params:** `sales_executive_id`, optional `search`

**Multi-select rule:**
- If both order AND approval targets filled → allow selecting customer for each target type
- Minimum 1 customer required when dropdown visible

**customers array to send:**
```json
[
  {"customer_id": "101", "target_type": "order"},
  {"customer_id": "205", "target_type": "approval"}
]
```

### 3.6 API Call Sequence

**Step 1 — Save Plan**
```
POST service/service_sales_executive.php
service=236 (save_daily_plan)

Params:
  sales_id
  expected_order_amount
  expected_approval_count
  expected_project_detail_count
  customers (JSON string)
```

**Step 2 — Punch IN (on plan success)**
```
POST service/service_sales_executive.php
service=20 (add_attendance)

Params:
  sales_id
  type=In
  lat, lng, imei, app_address, date_time
  image_path (if camera capture)
```

### 3.7 Success / Error Handling

| Response | App Action |
|----------|------------|
| Plan saved + Punch IN success | Show "Welcome! Attendance submitted" → go to dashboard |
| Plan validation fail | Show error on popup, stay on popup |
| Punch IN fail (late etc.) | Show server ack_msg |

---

## 4. Feature 2 — Evening Completion (Punch OUT)

### 4.1 Trigger
User taps **Punch OUT** button.

### 4.2 UI Flow

```
Punch OUT tap
    → Call get_daily_plan_status (237)
    → If completion_submitted=0 → open "Daily Completion" popup
    → Show morning targets (read-only)
    → User fills actual achieved values
    → Submit completion (API 238)
    → On success → call Punch OUT (API 20)
    → If completion already done → directly Punch OUT
```

### 4.3 Screen: Daily Completion Popup

**Title:** `Today's Completion` / `આજે કેટલું complete થયું`

#### Read-only Section (Morning Targets)

| Label | Value Source |
|-------|-------------|
| Expected Order Amount | `result.expected_order_amount` |
| Expected Approvals | `result.expected_approval_count` |
| Expected Project Details | `result.expected_project_detail_count` |
| Selected Customers | `result.customers[].display_label` |

#### Input Section (Actual Completion)

| UI Element | Type | Field Key | Label | Show When |
|------------|------|-----------|-------|-----------|
| Input | Number (decimal) | `actual_order_amount` | Actual Order Amount (₹) | Morning order amount was set |
| Input | Number (integer) | `actual_approval_count` | Actual Approvals | Morning approval count was set |
| Input | Number (integer) | `actual_project_detail_count` | Actual Project Details | Morning project detail was set |

**Note:** Only show input fields that had a morning target. Value can be 0 but field must not be empty.

#### Footer Buttons

| Button | Action |
|--------|--------|
| **Submit & Punch Out** | API 238 → then API 20 |
| **Cancel** | Close popup |

### 4.4 Validation Rules

| Rule | Error Message |
|------|---------------|
| All visible actual fields must be filled | `Please fill all completion fields` |
| Values must be ≥ 0 | `Invalid value` |
| Cannot punch out if plan not submitted today | `Daily plan not found. Contact admin.` |

### 4.5 API Call Sequence

**Step 1 — Check Status**
```
POST service/service_sales_executive.php
service=237 (get_daily_plan_status)

Params:
  sales_id
  plan_date (optional, default today)
```

**Step 2 — Save Completion**
```
POST service/service_sales_executive.php
service=238 (save_daily_plan_completion)

Params:
  sales_id
  daily_plan_id
  actual_order_amount
  actual_approval_count
  actual_project_detail_count
```

**Step 3 — Punch OUT**
```
POST service/service_sales_executive.php
service=20 (add_attendance)

Params:
  sales_id
  type=Out
  lat, lng, imei, app_address, date_time
  image_path (if required)
```

### 4.6 Block Punch OUT Screen State

If API 20 returns `require_completion=1`:
- Do NOT close attendance screen
- Open completion popup automatically
- Show message: `Punch out કરતા પહેલાં completion submit કરવું જરૂરી છે`

---

## 5. Feature 3 — Visit Start Type (19 Points)

### 5.1 Trigger
User taps **Start Visit** (before existing visit start form).

### 5.2 UI Flow

```
Start Visit tap
    → Call get_visit_start_types (239) — cache locally after first load
    → Show "Select Visit Type" screen/dialog
    → User selects exactly 1 option (radio list or searchable dropdown)
    → Validate selection
    → Continue to existing visit start flow (customer, location, etc.)
    → Call Add_visit (75) with visit_start_type_id
```

### 5.3 Screen: Visit Start Type Selection

**Title:** `Select Visit Type` / `Visit Start Type પસંદ કરો`

**Subtitle:** `Total 19 options — select one before starting visit`

| UI Element | Type | Field Key | Description |
|------------|------|-----------|-------------|
| List | Radio / Single-select | `visit_start_type_id` | 19 items from API |
| Search | Text filter | `local_search` | Client-side filter on name |
| Item display | Text | — | `sort_order. display_name` |

### 5.4 Visit Type List (Display in App)

| # | Display Name |
|---|-------------|
| 1 | New Customer - Fresh Visit |
| 2 | Repeat Customer Order Visit |
| 3 | Old Customer Order Visit |
| 4 | Old Customer Payment Visit |
| 5 | New MEP Approval Visit |
| 6 | Repeat MEP Approval Visit |
| 7 | MEP - Add Project/Contractor Details |
| 8 | New Government Approval Visit |
| 9 | Repeat Government Approval - Tender Name Add |
| 10 | Repeat Government Contractor Approval Visit |
| 11 | CPWD/PWD Approval Visit |
| 12 | CPWD/PWD Tender Name Add Visit |
| 13 | CPWD/PWD Contractor Details Add Visit |
| 14 | New Corporate Approval Visit |
| 15 | Repeat Corporate Approval |
| 16 | Corporate Project/Contractor Detail Visit |
| 17 | New Developer Approval |
| 18 | Repeat Developer Approval |
| 19 | Repeat Developer Contract/Project Detail Visit |

### 5.5 Validation

| Rule | Error Message |
|------|---------------|
| Must select 1 type | `Please select visit start type` |
| Cannot proceed without selection | Block Next/Start button |

### 5.6 API Calls

**Load types (once per session or daily cache):**
```
POST service/service_visit.php
service=239 (get_visit_start_types)
```

**Start visit (existing — add new param):**
```
POST service/service_visit.php
service=75 (Add_visit)

New param:
  visit_start_type_id

Existing params:
  user_id, customer_id, inquiry_id, latitude, longitude,
  remark, app_address, purpose_id, company_id, flag
```

### 5.7 UX Notes

- Store selected `visit_start_type_id` in visit session/state until visit ends
- Show selected type name on active visit banner (optional)
- Do not allow changing type after visit started

---

## 6. Feature 4 — Visit Complete Questions

### 6.1 Trigger
User completes CRM visit and taps **Stop Visit** / **End Visit**.

### 6.2 UI Flow

```
Stop Visit tap
    → Open "Visit Summary" popup with 5 Yes/No questions
    → User answers all 5
    → Continue to existing visit stop flow (remark, followup date, etc.)
    → Call update_visit (122) with completion flags + existing params
```

### 6.3 Screen: Visit Complete Questions Popup

**Title:** `Visit Result` / `Visit Complete`

**Instruction:** `Answer all questions before completing visit`

| # | Question (English) | Question (Gujarati) | Field Key | Input Type |
|---|-----------------|---------------------|-----------|------------|
| 1 | Did an order come in this visit? | શું આ visit માં order આવ્યો? | `order_came` | Yes / No (Radio) |
| 2 | Did an approval come in this visit? | શું આ visit માં approval આવ્યું? | `approval_came` | Yes / No |
| 3 | Did project details come in this visit? | શું આ visit માં project details આવી? | `project_detail_came` | Yes / No |
| 4 | Did contract details come in this visit? | શું આ visit માં contract details આવી? | `contract_detail_came` | Yes / No |
| 5 | Did payment come in this visit? | શું આ visit માં payment આવ્યું? | `payment_came` | Yes / No |

**Values to send:** `1` = Yes, `0` = No

#### Footer Buttons

| Button | Action |
|--------|--------|
| **Next / Continue** | Validate all answered → proceed to existing stop visit UI |
| **Cancel** | Close popup (visit not stopped) |

### 6.4 Validation

| Rule | Error Message |
|------|---------------|
| All 5 questions must be answered | `Please answer all questions` |
| Each answer must be 0 or 1 | — |

### 6.5 API Call

**Existing visit stop API — add 5 new params:**
```
POST service/service_visit.php
service=122 (update_visit)

New params:
  order_came=0|1
  approval_came=0|1
  project_detail_came=0|1
  contract_detail_came=0|1
  payment_came=0|1

Existing params (unchanged):
  id / visit_id, user_id, stop_latitude, stop_longitude,
  stop_remark, note, date, remark_code, reason_code,
  stop_app_address, customer_id, inquiry_id, visit_type, etc.
```

### 6.6 Integration with Existing Visit Stop

Current flow has remark/reason selection (A–G codes). New popup should appear:

**Recommended order:**
1. Visit Complete 5 Questions (NEW)
2. Existing Remark/Reason selection
3. Conditional forms (High Rate, Consultant) if applicable
4. Final update_visit API call with ALL data

---

## 7. Feature 5 — PI / Quotation Submit Popup

### 7.1 Trigger
User creates PI or Quotation on app and taps **Submit** button.

### 7.2 UI Flow

```
Submit tap (PI/Quotation)
    → Open "Product Range" popup (do NOT submit yet)
    → Q1: Customer knows full range?
    → If Yes → save questionnaire → create_quotation (165)
    → If No → show reason options
        → Reason 1: No product info → save → submit
        → Reason 2: Price high → open High Rate form (234) → save → submit
        → Reason 3: Need approval → open Consultant form (233) → save → submit
```

### 7.3 Screen: Quotation Questionnaire Popup

**Title:** `Before Submit` / `Submit પહેલાં`

#### Question 1

| UI Element | Type | Field Key | Label |
|------------|------|-----------|-------|
| Label | Text | — | Does customer know about our company's full range of products? |
| Options | Yes / No Radio | `knows_full_range` | Yes = 1, No = 0 |

#### Question 2 (Show only if knows_full_range = No)

| UI Element | Type | Field Key | Label |
|------------|------|-----------|-------|
| Label | Text | — | If customer does not purchase from full range, what is the reason? |
| Option 1 | Radio | `not_buying_reason=1` | Customer doesn't have product information |
| Option 2 | Radio | `not_buying_reason=2` | Price feels too high |
| Option 3 | Radio | `not_buying_reason=3` | Customer needs MEP / Government approval |

### 7.4 Conditional Navigation

| Reason Selected | Next Screen | API |
|----------------|-------------|-----|
| 1 — No product info | Save questionnaire → Submit quotation | 240 → 165 |
| 2 — Price high | Open **High Rate Form** (existing UI) | 234 → 240 → 165 |
| 3 — Need approval | Open **Need Approval / Consultant Form** (existing UI) | 233 → 240 → 165 |
| knows_full_range = Yes | Direct submit | 240 → 165 |

### 7.5 High Rate Form (Existing — Reuse)

**API:** `save_visit_high_rate_form` (234)

**When opened from Quotation (not visit):**
- Pass `quotation_id` instead of/in addition to `visit_id`
- After form save → get `high_rate_form_id` from response
- Pass to questionnaire API

**Key fields (existing):**
- `customer_name`, `payment_option`, `payment_remark`
- `items` (JSON array of products with rates)

**Products list API:** `get_visit_high_rate_products` (235)

### 7.6 Consultant / Need Approval Form (Existing — Reuse)

**API:** `save_visit_consultant_form` (233)

**When opened from Quotation:**
- Pass `quotation_id`
- After form save → get `consultant_form_id` from response

**Key fields (existing):**
- `firm_name`, `address`, `city`, `state`, `pincode`
- `contact_person`, `mobile`, `email`
- `approval_type` (1=Private, 2=Government)
- `reason_code` (C1/C2)

### 7.7 API Call Sequence

**Step 1 — Save Questionnaire**
```
POST service/service_quotation.php
service=240 (save_quotation_questionnaire)

Params:
  quotation_id / cart_id
  sales_executive_id
  customer_id
  knows_full_range (0|1)
  not_buying_reason (1|2|3) — if knows_full_range=0
  high_rate_form_id — if reason=2
  consultant_form_id — if reason=3
  remark (optional)
```

**Step 2 — Submit Quotation**
```
POST service/service_quotation.php
service=165 (create_quotation)

New param:
  questionnaire_id

Existing params:
  customer_id, sales_executive_id, shipping_address,
  billing_address, quotation_date, terms, etc.
  + product body JSON
```

### 7.8 Validation

| Rule | Error Message |
|------|---------------|
| Q1 must be answered | `Please select Yes or No` |
| If No → reason must be selected | `Please select a reason` |
| If reason=2 → High Rate form must be completed | `Please complete High Rate form` |
| If reason=3 → Consultant form must be completed | `Please complete Approval form` |

---

## 8. Screen Map (All New UI)

```
Attendance Screen
├── Punch IN → [Daily Plan Popup] → Punch IN API
└── Punch OUT → [Daily Completion Popup] → Punch OUT API

Visit Module
├── Start Visit → [Visit Type Selection] → Existing Start → Add_visit API
└── Stop Visit → [5 Yes/No Popup] → Existing Remark Flow → update_visit API

Quotation Module
└── Submit → [Full Range Popup] → [High Rate / Consultant Form if needed] → create_quotation API
```

---

## 9. Local State / Storage Recommendations

| Key | Store | Purpose |
|-----|-------|---------|
| `daily_plan_id` | Session / SharedPref | Link completion to morning plan |
| `visit_start_type_id` | Visit session | Until visit ends |
| `visit_start_type_name` | Visit session | Display on active visit |
| `questionnaire_id` | Quotation draft session | Before final submit |
| `visit_start_types_cache` | Local cache 24h | Avoid repeated API 239 calls |

---

## 10. Error Messages (User-facing)

| Scenario | English | Gujarati |
|----------|---------|----------|
| No daily target filled | Please fill at least one target | કૃપા કરીને ઓછામાં ઓછું એક target ભરો |
| Customer required | Please select customer | Customer પસંદ કરો |
| Completion required | Submit completion before punch out | Punch out પહેલાં completion submit કરો |
| Visit type required | Please select visit start type | Visit type પસંદ કરો |
| Visit questions incomplete | Please answer all visit questions | બધા પ્રશ્નોના જવાબ આપો |
| Questionnaire required | Complete questionnaire before submit | Submit પહેલાં questionnaire પૂર્ણ કરો |
| High rate form required | Please complete High Rate form | High Rate form ભરો |
| Approval form required | Please complete Approval form | Approval form ભરો |

---

## 11. API Quick Reference (Frontend)

| Action | Service # | Service Name | File |
|--------|-----------|--------------|------|
| Save morning plan | 236 | save_daily_plan | service_sales_executive.php |
| Get plan status | 237 | get_daily_plan_status | service_sales_executive.php |
| Save evening completion | 238 | save_daily_plan_completion | service_sales_executive.php |
| Punch IN/OUT | 20 | add_attendance | service_sales_executive.php |
| Get customers | 12 | get_customer | service_sales_executive.php |
| Get visit types | 239 | get_visit_start_types | service_visit.php |
| Start visit | 75 | Add_visit | service_visit.php |
| Stop visit | 122 | update_visit | service_visit.php |
| Save questionnaire | 240 | save_quotation_questionnaire | service_quotation.php |
| Submit quotation | 165 | create_quotation | service_quotation.php |
| High Rate form | 234 | save_visit_high_rate_form | service_visit.php |
| Consultant form | 233 | save_visit_consultant_form | service_visit.php |
| High Rate products | 235 | get_visit_high_rate_products | service_visit.php |

---

## 12. Testing Checklist (App / QA)

### Punch IN
- [ ] Popup opens on Punch IN
- [ ] Can submit with only 1 field filled
- [ ] Customer dropdown hidden when only project detail filled
- [ ] Customer dropdown shown when order filled
- [ ] Customer search works
- [ ] Only assigned customers shown
- [ ] Display format: `CODE - Name`
- [ ] Punch IN succeeds after plan submit

### Punch OUT
- [ ] Completion popup opens if not submitted
- [ ] Morning targets shown read-only
- [ ] Only relevant actual fields shown
- [ ] Punch OUT blocked until completion submitted
- [ ] Direct Punch OUT if already completed

### Visit Start
- [ ] 19 types loaded
- [ ] Search/filter works
- [ ] Cannot start without selection
- [ ] Selected type sent in Add_visit

### Visit Complete
- [ ] 5 questions popup on stop visit
- [ ] All 5 mandatory
- [ ] Yes/No values sent correctly
- [ ] Existing remark flow still works after popup

### Quotation
- [ ] Popup opens on Submit (not before)
- [ ] Yes → direct submit works
- [ ] No + reason 1 → submit works
- [ ] No + reason 2 → High Rate form opens
- [ ] No + reason 3 → Consultant form opens
- [ ] Submit blocked if form incomplete

---

## 13. Implementation Order (Frontend)

1. Visit Start Type screen (simplest — standalone)
2. Visit Complete 5 questions popup
3. Daily Plan Punch IN popup + customer dropdown
4. Daily Completion Punch OUT popup
5. Quotation questionnaire popup + form integration

---

**Document Version:** 1.0  
**Related:** `APP_CRM_BACKEND_CHANGES.md`, `APP_CRM_DB_MIGRATION.sql`
