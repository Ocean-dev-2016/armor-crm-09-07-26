# Armor CRM — App Side Changes (Backend Developer Document)

**Project:** Armor CRM Mobile App  
**Date:** 16-Sep-2026  
**Stack:** PHP 5.6, MySQL/MariaDB  
**API Base:** `service/service_sales_executive.php`, `service/service_visit.php`, `service/service_quotation.php`

---

## 1. Overview

4 new business flows are required on the mobile app. Backend must support:

| # | Feature | Trigger | Backend Action |
|---|---------|---------|----------------|
| 1 | Daily Plan (Morning) | Punch IN | Save expected targets + optional customers |
| 2 | Daily Completion (Evening) | Punch OUT | Block OUT until completion submitted |
| 3 | Visit Start Type | Visit Start | Mandatory 1-of-19 type selection |
| 4 | Visit Complete Questions | Visit Stop | Save 5 Yes/No answers |
| 5 | PI/Quotation Questionnaire | Quotation Submit | Save popup answers + link forms |

---

## 2. API Summary

### 2.1 New APIs (5)

| Service No. | Service Name | File | Method |
|-------------|--------------|------|--------|
| **271** | `save_daily_plan` | `service_sales_executive.php` | POST |
| **272** | `get_daily_plan_status` | `service_sales_executive.php` | GET/POST |
| **273** | `save_daily_plan_completion` | `service_sales_executive.php` | POST |
| **274** | `get_visit_start_types` | `service_visit.php` | GET/POST |
| **275** | `save_quotation_questionnaire` | `service_quotation.php` | POST |

### 2.2 Modified APIs (5)

| Service No. | Service Name | File | Change |
|-------------|--------------|------|--------|
| **12** | `get_customer` | `service_sales_executive.php` | Add `search` param for dropdown |
| **20** | `add_attendance` | `service_sales_executive.php` | Punch IN/OUT validation |
| **75** | `Add_visit` | `service_visit.php` | Require `visit_start_type_id` |
| **122** | `update_visit` | `service_visit.php` | Accept visit completion answers |
| **165** | `create_quotation` | `service_quotation.php` | Require questionnaire before submit |

### 2.3 Reused APIs (No change — App only)

| Service No. | Service Name | Use |
|-------------|--------------|-----|
| **233** | `save_visit_consultant_form` | PI popup → Need Approval |
| **234** | `save_visit_high_rate_form` | PI popup → Price High |
| **235** | `get_visit_high_rate_products` | High Rate form products |

---

## 3. Database Changes

### 3.1 New Tables (6)

Run migration: `database/APP_CRM_DB_MIGRATION.sql`

---

#### Table 1: `daily_plan`

Morning punch-in targets (savare data).

| Column | Type | Null | Description |
|--------|------|------|-------------|
| `id` | INT(11) AUTO_INCREMENT | NO | Primary key |
| `sales_id` | INT(11) | NO | `sales_executive.id` |
| `plan_date` | DATE | NO | Business date (Y-m-d) |
| `expected_order_amount` | DECIMAL(15,2) | YES | Q1: Expected order amount (â‚¹) |
| `expected_approval_count` | INT(11) | YES | Q2: Expected approval count |
| `expected_project_detail_count` | INT(11) | YES | Q3: Expected project detail count |
| `attendance_in_id` | INT(11) | YES | Link to `attendance.id` (Punch IN) |
| `created_date` | DATETIME | NO | Record created |
| `isDelete` | TINYINT(1) | NO | Default 0 |
| `isActive` | TINYINT(1) | NO | Default 1 |

**Indexes:**
- UNIQUE (`sales_id`, `plan_date`, `isDelete`) — one plan per sales person per day
- INDEX (`plan_date`)

**Validation (backend):**
- At least 1 of 3 expected fields must be > 0
- If `expected_order_amount` > 0 OR `expected_approval_count` > 0 → at least 1 row in `daily_plan_customer` required

---

#### Table 2: `daily_plan_customer`

Customers linked to morning plan (when order/approval target filled).

| Column | Type | Null | Description |
|--------|------|------|-------------|
| `id` | INT(11) AUTO_INCREMENT | NO | Primary key |
| `daily_plan_id` | INT(11) | NO | FK → `daily_plan.id` |
| `customer_id` | INT(11) | NO | FK → `executive.id` |
| `target_type` | ENUM('order','approval') | NO | Which target this customer is for |
| `created_date` | DATETIME | NO | Record created |
| `isDelete` | TINYINT(1) | NO | Default 0 |

**Indexes:**
- INDEX (`daily_plan_id`)
- INDEX (`customer_id`)

---

#### Table 3: `daily_plan_completion`

Evening completion (punch out pela submit).

| Column | Type | Null | Description |
|--------|------|------|-------------|
| `id` | INT(11) AUTO_INCREMENT | NO | Primary key |
| `daily_plan_id` | INT(11) | NO | FK → `daily_plan.id` |
| `actual_order_amount` | DECIMAL(15,2) | YES | Actual order amount achieved |
| `actual_approval_count` | INT(11) | YES | Actual approvals achieved |
| `actual_project_detail_count` | INT(11) | YES | Actual project details achieved |
| `attendance_out_id` | INT(11) | YES | Link to `attendance.id` (Punch OUT) |
| `submitted_at` | DATETIME | NO | When completion was submitted |
| `isDelete` | TINYINT(1) | NO | Default 0 |

**Indexes:**
- UNIQUE (`daily_plan_id`, `isDelete`) — one completion per plan
- INDEX (`submitted_at`)

**Validation (backend):**
- Can only submit if `daily_plan` exists for today
- Fields filled in morning plan should have matching actual values (can be 0 but not NULL if morning had value)

---

#### Table 4: `visit_start_type_master`

19 visit start types (seed data).

| Column | Type | Null | Description |
|--------|------|------|-------------|
| `id` | INT(11) AUTO_INCREMENT | NO | Primary key |
| `code` | VARCHAR(10) | NO | e.g. VST01 |
| `name` | VARCHAR(255) | NO | English display name |
| `display_name` | VARCHAR(300) | NO | Full label for app dropdown |
| `sort_order` | INT(11) | NO | Display order 1–19 |
| `isActive` | TINYINT(1) | NO | Default 1 |
| `isDelete` | TINYINT(1) | NO | Default 0 |

**Seed:** 19 rows — see Section 3.3

**Note:** Do NOT reuse `purpose_master` table. That is for visit purpose, not visit start type.

---

#### Table 5: `visit_completion_answer`

5 Yes/No questions when CRM visit completes.

| Column | Type | Null | Description |
|--------|------|------|-------------|
| `id` | INT(11) AUTO_INCREMENT | NO | Primary key |
| `visit_id` | INT(11) | NO | FK → `visit.id` |
| `order_came` | TINYINT(1) | NO | 0=No, 1=Yes |
| `approval_came` | TINYINT(1) | NO | 0=No, 1=Yes |
| `project_detail_came` | TINYINT(1) | NO | 0=No, 1=Yes |
| `contract_detail_came` | TINYINT(1) | NO | 0=No, 1=Yes |
| `payment_came` | TINYINT(1) | NO | 0=No, 1=Yes |
| `created_date` | DATETIME | NO | Record created |
| `isDelete` | TINYINT(1) | NO | Default 0 |

**Indexes:**
- UNIQUE (`visit_id`, `isDelete`)

---

#### Table 6: `quotation_submit_questionnaire`

PI/Quotation submit popup answers.

| Column | Type | Null | Description |
|--------|------|------|-------------|
| `id` | INT(11) AUTO_INCREMENT | NO | Primary key |
| `quotation_id` | INT(11) | NO | FK → `quotation_detail.id` |
| `knows_full_range` | TINYINT(1) | NO | 0=No, 1=Yes — customer knows full product range? |
| `not_buying_reason` | TINYINT(1) | YES | NULL if knows_full_range=1. Values: 1=No product info, 2=Price high, 3=Need MEP/Govt approval |
| `high_rate_form_id` | INT(11) | YES | FK → `visit_high_rate_form.id` (if reason=2) |
| `consultant_form_id` | INT(11) | YES | FK → `visit_consultant_form.id` (if reason=3) |
| `remark` | TEXT | YES | Optional free text |
| `created_date` | DATETIME | NO | Record created |
| `isDelete` | TINYINT(1) | NO | Default 0 |

**Indexes:**
- INDEX (`quotation_id`)

---

### 3.2 Alter Existing Tables

#### `visit` table — add column

| Column | Type | Null | Description |
|--------|------|------|-------------|
| `visit_start_type_id` | INT(11) | YES | FK → `visit_start_type_master.id` |

Add after `purpose_id` or at end of table.

#### `quotation_detail` table — add column (optional, for quick lookup)

| Column | Type | Null | Description |
|--------|------|------|-------------|
| `questionnaire_id` | INT(11) | YES | FK → `quotation_submit_questionnaire.id` |

---

### 3.3 Seed Data — 19 Visit Start Types

| sort_order | code | name |
|------------|------|------|
| 1 | VST01 | New Customer - Fresh Visit |
| 2 | VST02 | Repeat Customer Order Visit |
| 3 | VST03 | Old Customer Order Visit |
| 4 | VST04 | Old Customer Payment Visit |
| 5 | VST05 | New MEP Approval Visit |
| 6 | VST06 | Repeat MEP Approval Visit |
| 7 | VST07 | MEP - Add Project/Contractor Details |
| 8 | VST08 | New Government Approval Visit |
| 9 | VST09 | Repeat Government Approval - Tender Name Add |
| 10 | VST10 | Repeat Government Contractor Approval Visit |
| 11 | VST11 | CPWD/PWD Approval Visit |
| 12 | VST12 | CPWD/PWD Tender Name Add Visit |
| 13 | VST13 | CPWD/PWD Contractor Details Add Visit |
| 14 | VST14 | New Corporate Approval Visit |
| 15 | VST15 | Repeat Corporate Approval |
| 16 | VST16 | Corporate Project/Contractor Detail Visit |
| 17 | VST17 | New Developer Approval |
| 18 | VST18 | Repeat Developer Approval |
| 19 | VST19 | Repeat Developer Contract/Project Detail Visit |

---

## 4. API Details (Request / Response / Validation)

**Common params (all APIs):**
- `api_key` — required
- `service` — service number or name

---

### 4.1 NEW — `save_daily_plan)

**File:** `service/service_sales_executive.php`

**When:** Before or with Punch IN

**Request Parameters:**

| Param | Type | Required | Description |
|-------|------|----------|-------------|
| `sales_id` | int | YES | Sales executive ID |
| `plan_date` | string | NO | Default: today (Y-m-d) |
| `expected_order_amount` | decimal | NO* | Expected order amount in â‚¹ |
| `expected_approval_count` | int | NO* | Expected approval count |
| `expected_project_detail_count` | int | NO* | Expected project detail count |
| `customers` | JSON string | NO** | Array of `{customer_id, target_type}` |

`*` At least 1 of the 3 expected fields must be provided and > 0  
`**` Required if `expected_order_amount > 0` OR `expected_approval_count > 0`

**customers JSON example:**
```json
[
  {"customer_id": "101", "target_type": "order"},
  {"customer_id": "205", "target_type": "approval"}
]
```

**Success Response:**
```json
{
  "ack": 1,
  "ack_msg": "Daily plan saved successfully.",
  "developer_msg": "daily_plan insert success",
  "daily_plan_id": "15",
  "result": {
    "id": "15",
    "sales_id": "5",
    "plan_date": "2026-09-16",
    "expected_order_amount": "50000.00",
    "expected_approval_count": "2",
    "expected_project_detail_count": null
  }
}
```

**Error Responses:**

| ack_msg | Condition |
|---------|-----------|
| At least one target is required | All 3 fields empty or 0 |
| Customer selection required | Order/approval filled but no customers |
| Daily plan already submitted | Plan exists for sales_id + plan_date |
| Invalid customer | customer_id not assigned to sales_id |

**Backend Logic:**
1. Validate sales_id exists and is active
2. Check duplicate plan for same date
3. Validate min 1 target
4. If order/approval target → validate customers array
5. Verify each customer_id belongs to sales executive (`executive.seid = sales_id`)
6. Insert `daily_plan` + `daily_plan_customer` rows in transaction

---

### 4.2 NEW — `get_daily_plan_status` (272)

**File:** `service/service_sales_executive.php`

**When:** App open, before Punch OUT screen, after Punch IN

**Request Parameters:**

| Param | Type | Required | Description |
|-------|------|----------|-------------|
| `sales_id` | int | YES | Sales executive ID |
| `plan_date` | string | NO | Default: today |

**Success Response (plan exists, completion pending):**
```json
{
  "ack": 1,
  "ack_msg": "Daily plan found.",
  "plan_submitted": 1,
  "completion_submitted": 0,
  "can_punch_out": 0,
  "result": {
    "daily_plan_id": "15",
    "plan_date": "2026-09-16",
    "expected_order_amount": "50000.00",
    "expected_approval_count": "2",
    "expected_project_detail_count": "1",
    "customers": [
      {
        "customer_id": "101",
        "customer_code": "C001",
        "customer_name": "ABC Traders",
        "display_label": "C001 - ABC Traders",
        "target_type": "order"
      }
    ]
  }
}
```

**Success Response (completion done — can punch out):**
```json
{
  "ack": 1,
  "plan_submitted": 1,
  "completion_submitted": 1,
  "can_punch_out": 1,
  "result": {
    "daily_plan_id": "15",
    "completion": {
      "actual_order_amount": "45000.00",
      "actual_approval_count": "1",
      "actual_project_detail_count": "1"
    }
  }
}
```

**No plan today:**
```json
{
  "ack": 1,
  "plan_submitted": 0,
  "completion_submitted": 0,
  "can_punch_out": 0,
  "result": null
}
```

---

### 4.3 NEW — `save_daily_plan_completion` (273)

**File:** `service/service_sales_executive.php`

**When:** Before Punch OUT (mandatory)

**Request Parameters:**

| Param | Type | Required | Description |
|-------|------|----------|-------------|
| `sales_id` | int | YES | Sales executive ID |
| `daily_plan_id` | int | YES | From get_daily_plan_status |
| `actual_order_amount` | decimal | NO* | Actual achieved |
| `actual_approval_count` | int | NO* | Actual achieved |
| `actual_project_detail_count` | int | NO* | Actual achieved |

`*` Must fill actual value for each field that was set in morning plan (can be 0)

**Success Response:**
```json
{
  "ack": 1,
  "ack_msg": "Daily completion saved. You can now punch out.",
  "developer_msg": "daily_plan_completion insert success",
  "completion_id": "8",
  "can_punch_out": 1
}
```

**Error Responses:**

| ack_msg | Condition |
|---------|-----------|
| Daily plan not found | Invalid daily_plan_id |
| Completion already submitted | Duplicate for same plan |
| Please fill completion for all morning targets | Missing actual field |

**Backend Logic:**
1. Load daily_plan by id + sales_id
2. Check completion not already exists
3. Validate actual fields match morning plan fields
4. Insert `daily_plan_completion`
5. Return `can_punch_out: 1`

---

### 4.4 MODIFY — `add_attendance` (20)

**File:** `service/service_sales_executive.php`  
**Existing params:** `sales_id`, `type` (In/Out), `lat`, `lng`, `imei`, `app_address`, `date_time`, `image_path`, `auto_out_flag`

**New behaviour:**

#### Punch IN (`type=In`)
- **Option A (recommended):** App calls `save_daily_plan) first, then `add_attendance` (20)
- **Option B:** Accept plan fields in same request and save internally
- After successful IN insert → update `daily_plan.attendance_in_id = attendance.id`

**New optional params (Option B only):**

| Param | Type | Description |
|-------|------|-------------|
| `daily_plan_id` | int | If plan already saved separately |
| `expected_order_amount` | decimal | Inline plan save |
| `expected_approval_count` | int | Inline plan save |
| `expected_project_detail_count` | int | Inline plan save |
| `customers` | JSON | Inline customer list |

**Block IN if:** No daily plan for today (when feature flag enabled)

#### Punch OUT (`type=Out`)
- Before insert, call internal check:
  ```php
  // Pseudocode
  $plan = getTodayPlan($sales_id);
  if (!$plan) → ack=0, "Please submit daily plan first"
  $completion = getCompletion($plan['id']);
  if (!$completion) → ack=0, "Please submit daily completion before punch out"
  ```
- After successful OUT insert → update `daily_plan_completion.attendance_out_id = attendance.id`

**New error response (Punch OUT blocked):**
```json
{
  "ack": 0,
  "ack_msg": "Please submit daily completion before punch out.",
  "developer_msg": "daily_plan_completion missing",
  "require_completion": 1,
  "daily_plan_id": "15"
}
```

---

### 4.5 MODIFY — `get_customer` (12)

**File:** `service/service_sales_executive.php`  
**Existing param:** `sales_executive_id`

**New optional param:**

| Param | Type | Description |
|-------|------|-------------|
| `search` | string | Filter by `client_code` OR `cname` OR `company_name` (LIKE) |

**Response enhancement — add display fields:**

Each customer in `result[]` should include:
```json
{
  "id": "101",
  "client_code": "C001",
  "cname": "ABC Traders",
  "company_name": "ABC Traders Pvt Ltd",
  "display_label": "C001 - ABC Traders",
  "phone": "9876543210"
}
```

**Backend change in `include/class.executive.php` → `getCustomer()`:**
- Add search WHERE clause when `search` param present
- Add `display_label = client_code + ' - ' + cname` in response loop

**Filter rule (existing — keep):**
- Only customers where `executive.seid = sales_executive_id`
- Only active, non-deleted, non-channel-partner

---

### 4.6 NEW — `get_visit_start_types` (274)

**File:** `service/service_visit.php`

**Request:** No extra params (optional `sales_id` for logging)

**Success Response:**
```json
{
  "ack": 1,
  "ack_msg": "Visit start types fetched.",
  "result": [
    {
      "id": "1",
      "code": "VST01",
      "name": "New Customer - Fresh Visit",
      "display_name": "1. New Customer - Fresh Visit",
      "sort_order": "1"
    }
  ]
}
```

**Backend:** `SELECT * FROM visit_start_type_master WHERE isDelete=0 AND isActive=1 ORDER BY sort_order ASC`

---

### 4.7 MODIFY — `Add_visit` (75)

**File:** `service/service_visit.php` + `include/class.visit.php`

**New required param:**

| Param | Type | Required | Description |
|-------|------|----------|-------------|
| `visit_start_type_id` | int | YES | Selected from 19 types |

**Existing params (unchanged):**
- `user_id`, `customer_id`, `inquiry_id`, `latitude`, `longitude`, `remark`, `app_address`, `purpose_id`, `company_id`, `flag`

**Validation:**
- `visit_start_type_id` must exist in `visit_start_type_master` and be active
- Block visit start if empty

**Error:**
```json
{
  "ack": 0,
  "ack_msg": "Please select visit start type.",
  "developer_msg": "visit_start_type_id missing or invalid"
}
```

**Backend change in `class.visit.php` → `AddVisit()`:**
- Add `visit_start_type_id` to insert columns array

---

### 4.8 MODIFY — `update_visit` (122)

**File:** `service/service_visit.php` + `include/class.visit.php`

**New params (visit complete questions):**

| Param | Type | Required | Description |
|-------|------|----------|-------------|
| `order_came` | int (0/1) | YES | Did order come in this visit? |
| `approval_came` | int (0/1) | YES | Did approval come? |
| `project_detail_came` | int (0/1) | YES | Did project details come? |
| `contract_detail_came` | int (0/1) | YES | Did contract details come? |
| `payment_came` | int (0/1) | YES | Did payment come? |

**Backend logic (on visit stop success):**
1. Insert/update `visit_completion_answer` for this `visit_id`
2. All 5 fields mandatory (0 or 1 only)

**Error:**
```json
{
  "ack": 0,
  "ack_msg": "Please answer all visit completion questions.",
  "developer_msg": "visit completion answer missing"
}
```

---

### 4.9 NEW — `save_quotation_questionnaire` (275)

**File:** `service/service_quotation.php`

**When:** After PI/Quotation popup, before final `create_quotation` (165)

**Request Parameters:**

| Param | Type | Required | Description |
|-------|------|----------|-------------|
| `quotation_id` | int | YES* | Existing draft quotation ID |
| `cart_id` | int | ALT | Same as quotation_id if draft |
| `sales_executive_id` | int | YES | Sales person |
| `customer_id` | int | YES | Customer |
| `knows_full_range` | int (0/1) | YES | Customer knows full product range? |
| `not_buying_reason` | int | NO** | 1=No info, 2=Price high, 3=Need approval |
| `high_rate_form_id` | int | NO*** | Required if reason=2 |
| `consultant_form_id` | int | NO*** | Required if reason=3 |
| `remark` | string | NO | Free text |

`*` quotation must exist (draft status)  
`**` Required if `knows_full_range=0`  
`***` Required based on reason selected

**Success Response:**
```json
{
  "ack": 1,
  "ack_msg": "Questionnaire saved.",
  "questionnaire_id": "22",
  "can_submit_quotation": 1
}
```

---

### 4.10 MODIFY — `create_quotation` (165)

**File:** `service/service_quotation.php` + `include/quotation.class.php`

**New optional/required param:**

| Param | Type | Required | Description |
|-------|------|----------|-------------|
| `questionnaire_id` | int | YES | From save_quotation_questionnaire (275) |

**Validation before final submit:**
- If `knows_full_range=0` and `not_buying_reason=2` → `high_rate_form_id` must exist
- If `knows_full_range=0` and `not_buying_reason=3` → `consultant_form_id` must exist
- Update `quotation_detail.questionnaire_id`

**Error:**
```json
{
  "ack": 0,
  "ack_msg": "Please complete quotation questionnaire before submit.",
  "developer_msg": "questionnaire_id missing",
  "require_questionnaire": 1
}
```

---

### 4.11 REUSE — High Rate Form (234) for PI Popup

When PI popup reason = Price High (2), App opens existing High Rate form.

**Note for PI flow:** Form can be saved with `quotation_id` context instead of `visit_id` if visit not active. Backend may need to accept optional `quotation_id` param in service 234 (minor extension).

**Suggested new optional param on service 234:**

| Param | Type | Description |
|-------|------|-------------|
| `quotation_id` | int | Link form to quotation instead of visit |

---

### 4.12 REUSE — Consultant Form (233) for PI Popup

When PI popup reason = Need Approval (3), App opens existing Consultant form.

**Suggested new optional param on service 233:**

| Param | Type | Description |
|-------|------|-------------|
| `quotation_id` | int | Link form to quotation instead of visit |

---

## 5. Files to Create / Modify

### 5.1 New Files

| File | Purpose |
|------|---------|
| `database/APP_CRM_DB_MIGRATION.sql` | All CREATE TABLE + ALTER + seed |
| `include/class.daily_plan.php` | Daily plan + completion logic |
| `include/class.visit_start_type.php` | Visit start type master (optional) |
| `include/class.quotation_questionnaire.php` | Quotation popup logic (optional) |

### 5.2 Modify Files

| File | Changes |
|------|---------|
| `service/service_sales_executive.php` | APIs 271, 272, 273 + modify 12, 20 |
| `service/service_visit.php` | API 274 + modify 75, 122 |
| `service/service_quotation.php` | API 275 + modify 165 |
| `include/class.visit.php` | AddVisit + UpdateVisit — new fields |
| `include/class.executive.php` | getCustomer — search + display_label |
| `include/quotation.class.php` | AddQuotationApi — questionnaire check |

### 5.3 Register Services

Add new service numbers in API config / service list comments at top of each service file.

---

## 6. Business Rules Summary

| Rule | Where Enforced |
|------|----------------|
| Morning: min 1 of 3 targets required | save_daily_plan) |
| Morning: customer required if order/approval target set | save_daily_plan) |
| Customer must belong to logged-in sales person | save_daily_plan), get_customer (12) |
| One daily plan per sales person per day | DB UNIQUE + API check |
| Punch OUT blocked without completion | add_attendance (20) |
| Visit start blocked without type selection | Add_visit (75) |
| Visit stop blocked without 5 answers | update_visit (122) |
| Quotation submit blocked without questionnaire | create_quotation (165) |
| Price high → High Rate form required | save_quotation_questionnaire (275) |
| Need approval → Consultant form required | save_quotation_questionnaire (275) |

---

## 7. Implementation Order

1. Run `APP_CRM_DB_MIGRATION.sql` on tenant DB
2. Create `class.daily_plan.php`
3. Implement APIs 271, 272, 273
4. Modify API 20 (attendance validation)
5. Modify API 12 (customer search)
6. Implement API 274 + modify API 75
7. Modify API 122 (visit completion)
8. Implement API 275 + modify API 165
9. Extend APIs 233, 234 for quotation_id (optional)
10. Test all flows with Postman / App

---

## 8. Testing Checklist (Backend)

- [ ] Punch IN without plan → blocked (if enforced)
- [ ] Punch IN with only project detail target → allowed, no customer needed
- [ ] Punch IN with order target but no customer → blocked
- [ ] Punch IN with order target + customer → allowed
- [ ] Duplicate plan same day → blocked
- [ ] Punch OUT without completion → blocked
- [ ] Punch OUT after completion → allowed
- [ ] Visit start without visit_start_type_id → blocked
- [ ] Visit start with valid type → allowed
- [ ] Visit stop without 5 answers → blocked
- [ ] Visit stop with all answers → saved in visit_completion_answer
- [ ] Quotation submit without questionnaire → blocked
- [ ] Quotation with full range=Yes → submit allowed
- [ ] Quotation with reason=Price high but no form → blocked
- [ ] get_customer search returns only assigned customers

---

## 9. Web Admin (Future — Optional)

Not in current App scope, but backend tables support future reports:

- Daily Plan vs Completion Report (by sales person, by date)
- Visit Start Type Analysis Report
- Visit Completion Answer Report
- Quotation Questionnaire Report (full range awareness)

---

**Document Version:** 1.0  
**Related:** `APP_CRM_FRONTEND_CHANGES.md`, `APP_CRM_DB_MIGRATION.sql`
