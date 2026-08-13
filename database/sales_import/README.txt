Sales data import (schema-safe)
================================

Source dump: database/kxznassm_armorfire_crm.sql
Temp DB used: kxznassm_armorfire_crm_src
Local target: kxznassm_armorfire_crm (XAMPP port 3307)
Cut-off date: 2026-08-13 (today inclusive)

IMPORTANT
- Local / live TABLE STRUCTURE was NOT changed.
- Only common columns were copied from dump.
- Extra local fields were kept (defaults / existing structure).
- Dump-only fields were ignored.
- Do NOT import full dump CREATE/ALTER on live.

Local already imported. Counts:
- sales_executive: 85
- visit: 19485
- attendance: 16097
- quotation_detail: 2468
- quotation_product_item: 13309
- orders: 2005
- order_product_item: 8516
- expense: 8392
- proforma_invoice_* / meeting_*: 0 in dump (empty)

Skipped (not present in dump):
- visit_consultant_form
- visit_high_rate_form
- visit_high_rate_form_item

LIVE IMPORT (after backup)
1. Prefer phpMyAdmin / mysql CLI on live.
2. Run SQL files one by one from this folder (order recommended):

   1) sales_executive.sql
   2) visit.sql
   3) attendance.sql
   4) quotation_detail.sql
   5) quotation_product_item.sql
   6) orders.sql
   7) order_product_item.sql
   8) expense.sql

3. Each file TRUNCATEs that table then INSERTs dump data (common columns only).
4. If live has same extra columns as local, they stay; values become DEFAULT for reloaded rows.

Re-run local sync anytime:
  C:\xampp56\php\php.exe database\sync_sales_from_dump.php
