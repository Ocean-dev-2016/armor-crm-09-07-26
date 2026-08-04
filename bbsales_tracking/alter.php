raj_cooling_crm clone to mahadev_casting(11-07-2023 naveen)


//11-07-2023 aakruti
create new folder for import customer (bbsales_tracking/sheet_import/uploads/customer)
create excel sheet(../customer_import_new)

//13-07-2023 hitisha
UPDATE `customer_type` SET `isDelete` = '1' WHERE `customer_type`.`id` = 1;
UPDATE `customer_type` SET `isDelete` = '1' WHERE `customer_type`.`id` = 2;
UPDATE `customer_type` SET `isDelete` = '1' WHERE `customer_type`.`id` = 3;
UPDATE `customer_type` SET `name` = 'Contractor' WHERE `customer_type`.`id` = 4;
UPDATE `customer_type` SET `name` = 'Trader' WHERE `customer_type`.`id` = 6;

17-07-2023 Naveen
ALTER TABLE `executive` ADD `remark` VARCHAR(250) NOT NULL AFTER `update_password_flag`;
terms_condition => live ma chadavanu che
ALTER TABLE `quotation_detail` ADD `terms_condition_id` INT(11) NOT NULL AFTER `attn_email`;

17-07-2023 Dinesh
ALTER TABLE `executive` ADD `booking_place` TEXT NOT NULL AFTER `remark`, ADD `booking_pincode` INT(6) NOT NULL AFTER `booking_place`;

ALTER TABLE `executive` ADD `transport_by_id` INT(10) NOT NULL AFTER `booking_pincode`, ADD `transporter_id` INT(10) NOT NULL AFTER `transport_by_id`;


17-07-2023 Parth
ALTER TABLE `orders` ADD `booking_place` TEXT NOT NULL AFTER `type_of_company`, ADD `booking_pincode` INT(6) NOT NULL AFTER `booking_place`;

ALTER TABLE `orders` ADD `lr_image` TEXT NOT NULL AFTER `booking_pincode`, ADD `lr_number` TEXT NOT NULL AFTER `lr_image`;

ALTER TABLE `orders` CHANGE `status` `status` INT(11) NOT NULL DEFAULT '0' COMMENT '0=pending,1=completed,2=dispatched,3=cancelled,4=Account approve, 5=lr pending, 6 = order complate';

17-07-2023 aakruti
ALTER TABLE `visit` ADD `product_name` VARCHAR(255) NOT NULL AFTER `type_of_company`;


18-07-2023 Naveen

ALTER TABLE `orders` ADD `terms_condition_id` INT(11) NOT NULL AFTER `po_date`;

18-07-2023 hitisha

ALTER TABLE `cart_detail` ADD `terms_condition_id` INT NOT NULL AFTER `type_of_company`;
ALTER TABLE `company_master` ADD `prefix` VARCHAR(255) NOT NULL AFTER `india_mart_api_key`;
api table

18-07-2023 Dinesh
ALTER TABLE `product_weight_price` ADD `minimum_selling_price` DOUBLE NOT NULL AFTER `outer_unit`;


18-07-2023 aakruti
import excel sheet updated (../customer_import_new)

19-07-2023 Dinesh
area nu table chadavanu che

19-07-2023 aakruti
ALTER TABLE `orders` CHANGE `status` `status` INT(11) NOT NULL DEFAULT '0' COMMENT '0=pending,1=completed,2=dispatched,3=cancelled,4=Account approve, 5=dispatch, 6 = order complate,7=lr_pending';


22-07-2023 NAVEEN
ALTER TABLE `orders` ADD `pdf_attachment` VARCHAR(300) NOT NULL AFTER `lr_number`;

24-07-2023 NAVEEN
ALTER TABLE `company_master` ADD `footer_image_path` VARCHAR(300) NOT NULL AFTER `image_path`;

define("FOOTER","images/header/"); => line no.: 491
define("FOOTER_A","../images/header/");
define("FOOTER_T","../images/header/tempImg/");
define("FOOTER_IMAGE_WIDTH","933");
define("FOOTER_IMAGE_HEIGHT","145");

24-07-2023 hitisha
ALTER TABLE `orders` ADD `max_dispatch_date` DATE NOT NULL AFTER `pdf_attachment`;

//24-07-2023 (added shivani)
ALTER TABLE `no_order_inquiry` CHANGE `prospect_date` `prospect_to_inquiry_date` DATETIME NOT NULL;
ALTER TABLE `no_order_inquiry` CHANGE `lead_date` `lead_date` DATETIME NOT NULL COMMENT 'convert inquity to lead date';

Live time datatype check


//25-07-2023 Dinesh
firebase-messaging-sw.js live karvani che (LOGO CHANGE KARELO CHE)

//25-07-2023 NAVEEN (LIVE DONE)
ALTER TABLE `orders` CHANGE `pdf_attachment` `pdf_attachment` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;

//25-07-2023 aakruti (LIVE DONE)
UPDATE `customer_type` SET `name` = 'Trader + Contractor', `isDelete` = '0' WHERE `customer_type`.`id` = 7;

UPDATE `customer_type` SET `name` = 'MEP Consultant\r\n \r\n ', `isDelete` = '0' WHERE `customer_type`.`id` = 9;

INSERT INTO `customer_type` (`id`, `name`, `isDelete`, `created_by`, `created_by_type`, `modified_by`, `modified_by_type`, `created_date`, `modified_date`, `display_order`) VALUES (NULL, 'Builder\r\n \r\n ', '0', '1', '0', '', '', '2022-04-23 00:00:00', '', '0');

INSERT INTO `customer_type` (`id`, `name`, `isDelete`, `created_by`, `created_by_type`, `modified_by`, `modified_by_type`, `created_date`, `modified_date`, `display_order`) VALUES (NULL, 'Brand Approval Visit\r\n \r\n ', '0', '1', '0', '', '', '2022-04-23 00:00:00', '', '0');


26-07-2023 -Parth (live done)
ALTER TABLE `sales_executive` ADD `tradercontractor_view_flag` INT NOT NULL AFTER `deepfreezscheme_flag`, ADD `tradercontractor_insert_flag` INT NOT NULL AFTER `tradercontractor_view_flag`, ADD `tradercontractor_update_flag` INT NOT NULL AFTER `tradercontractor_insert_flag`, ADD `tradercontractor_delete_flag` INT NOT NULL AFTER `tradercontractor_update_flag`, ADD `mep_consultant_view_flag` INT NOT NULL AFTER `tradercontractor_delete_flag`, ADD `mep_consultant_insert_flag` INT NOT NULL AFTER `mep_consultant_view_flag`, ADD `mep_consultant_update_flag` INT NOT NULL AFTER `mep_consultant_insert_flag`, ADD `mep_consultant_delete_flag` INT NOT NULL AFTER `mep_consultant_update_flag`, ADD `builder_view_flag` INT NOT NULL AFTER `mep_consultant_delete_flag`, ADD `builder_insert_flag` INT NOT NULL AFTER `builder_view_flag`, ADD `builder_update_flag` INT NOT NULL AFTER `builder_insert_flag`, ADD `builder_delete_flag` INT NOT NULL AFTER `builder_update_flag`, ADD `brand_approval_visit_view_flag` INT NOT NULL AFTER `builder_delete_flag`, ADD `brand_approval_visit_insert_flag` INT NOT NULL AFTER `brand_approval_visit_view_flag`, ADD `brand_approval_visit_update_flag` INT NOT NULL AFTER `brand_approval_visit_insert_flag`, ADD `brand_approval_visit_delete_flag` INT NOT NULL AFTER `brand_approval_visit_update_flag`;


27-07-2023 Dinesh (live done)
ALTER TABLE `complain_service` ADD `sr_no` INT NOT NULL AFTER `service_no`;

ALTER TABLE `complain_service` ADD `type_of_product` VARCHAR(210) NOT NULL AFTER `servicemen`, ADD `product` VARCHAR(210) NOT NULL AFTER `type_of_product`, ADD `state_city` VARCHAR(210) NOT NULL AFTER `product`, ADD `site_address` TEXT NOT NULL AFTER `state_city`, ADD `contractor` VARCHAR(210) NOT NULL AFTER `site_address`, ADD `test_date` DATE NOT NULL AFTER `contractor`, ADD `tested_pressure` VARCHAR(210) NOT NULL AFTER `test_date`, ADD `is_issues_testing` INT NOT NULL AFTER `tested_pressure`, ADD `last_maintenance_date` DATE NOT NULL AFTER `is_issues_testing`, ADD `specifications` VARCHAR(210) NOT NULL AFTER `last_maintenance_date`, ADD `root_of_issue` VARCHAR(210) NOT NULL AFTER `specifications`, ADD `current_scenario` TEXT NOT NULL AFTER `root_of_issue`, ADD `conclusion` TEXT NOT NULL AFTER `current_scenario`, ADD `resolution` INT NOT NULL AFTER `conclusion`;
ALTER TABLE `complain_service` ADD `site_name` VARCHAR(210) NOT NULL AFTER `site_address`;
ALTER TABLE `complain_service` ADD `product_type` VARCHAR(210) NOT NULL AFTER `specifications`;

28-07-2023 hitisha (live done)

define("OFFLINE_VISIT_LIMIT","20");

29-07-2023 Dinesh (live done)
ALTER TABLE `complain` CHANGE `product_id` `product_id` VARCHAR(500) NOT NULL;
ALTER TABLE `complain` ADD `product_sub_category` VARCHAR(500) NOT NULL AFTER `product_id`;

29-07-2023 Parth (live done)
- Api table update

31-07-2023 Hitisha (Live Done)
-api table add

ALTER TABLE `no_order_inquiry` ADD `purchasing_from` VARCHAR(255) NOT NULL AFTER `top_category_id`;
ALTER TABLE `executive` ADD `purchasing_from` VARCHAR(250) NOT NULL AFTER `transporter_id`;

31-07-2023 Dinesh (Live Done)

ALTER TABLE `complain_service` ADD `invoice_no` VARCHAR(210) NOT NULL AFTER `contact_person_name`, ADD `invoice_date` DATE NOT NULL AFTER `invoice_no`;

ALTER TABLE `complain_service` ADD `mt_fire_hydrant` INT(10) NOT NULL COMMENT 'Maintenance Test Fire Hydrant' AFTER `is_issues_testing`, ADD `mt_rrl` INT(10) NOT NULL COMMENT 'Maintenance Test RRL' AFTER `mt_fire_hydrant`, ADD `mt_hose_reel_drum` INT(10) NOT NULL COMMENT 'Maintenance Test Hose Reel Drum' AFTER `mt_rrl`, ADD `mt_branch_pipe` INT(10) NOT NULL COMMENT 'Maintenance Test Branch Pipe' AFTER `mt_hose_reel_drum`, ADD `mt_inlet` INT(10) NOT NULL COMMENT 'Maintenance Test Inlet' AFTER `mt_branch_pipe`, ADD `mt_new` INT(10) NOT NULL COMMENT 'Maintenance Test New' AFTER `mt_inlet`;

ALTER TABLE `complain_service` CHANGE `type_of_product` `type_of_product` VARCHAR(500) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL, CHANGE `product` `product` VARCHAR(500) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;

ALTER TABLE `complain` ADD `executive_type` VARCHAR(210) NOT NULL AFTER `customer_requirement`;


01-08-2023 hitisha (Live Done)

define("VISIT_START_IMAGE_FLAG","1");
define("VISIT_STOP_IMAGE_FLAG","0");

02-08-2023 Dinesh (Live Done)
define("GRAND_TOTAL_COLOR","#669B49");

04-08-2023 NAVEEn
api table live karvu

04-08-2023 aakruti (Live Done)
Update excel sheet(\\24.24.25.232\xampp\htdocs\mahadev_casting\customer_import_new.xlsx)

//09-08-2023 aakruti (live done)
ALTER TABLE `visit` ADD `firm_name` VARCHAR(255) NOT NULL AFTER `product_name`;
ALTER TABLE `visit` ADD `client_name` VARCHAR(255) NOT NULL AFTER `firm_name`;
ALTER TABLE `visit` ADD `contact_number` VARCHAR(255) NOT NULL AFTER `client_name`;

//11-08-2023 aakruti (live done)
Update excel sheet(\\24.24.25.232\xampp\htdocs\mahadev_casting\crm_sample_sheet.xlsx)


16-08-2023 -Parth (Live Done)
change in define
define("DEALER_QUOTATION_NO","PRO/"); TO define("DEALER_QUOTATION_NO","QT/");

for change in old data (

UPDATE quotation_detail SET quotation_no = REPLACE(quotation_no, 'PRO/', 'QT/') WHERE quotation_no LIKE 'PRO/%';
) aa nathi karyu live

17-08-2023 -Parth (Live Done)
customer_import_new.xlsx live karvani 6e


18-08-2023 Dinesh (Live Done)

ALTER TABLE `order_product_item` ADD `order_item_brand_id` INT NOT NULL AFTER `order_qty`;
page table update
new table order_item_brand_master
api table update
ALTER TABLE `cart_item` ADD `order_item_brand_id` INT NOT NULL AFTER `item_order_unit`;

//19-08-2023 (added by shivani) (Live Done)

customer_import_new.xlsx in root

19-08-2023 Dinesh (Live Done)
ALTER TABLE `quotation_product_item` ADD `order_item_brand_id` INT NOT NULL AFTER `order_qty`;

24-08-2023 Naimish (live done)
APi Table Live kravu

25-08-2023 Dinesh (live done)
raw data excel update karvani che ====> \\24.24.25.232\xampp\htdocs\mahadev_casting\inquiry_import_new.xlsx

29-08-2023 Dinesh (live done)
ALTER TABLE `no_order_inquiry` ADD `pincode` VARCHAR(250) NOT NULL AFTER `billing_address`;

raw data excel update karvani che ====> \\24.24.25.232\xampp\htdocs\mahadev_casting\inquiry_import_new.xlsx

Customer Import sheet excel update karvani che ====> \\24.24.25.232\xampp\htdocs\mahadev_casting\customer_import_new.xlsx


31-08-2023 Dinesh (before live contact me)
SELECT booking_pincode,zip FROM executive
339 rows affected.

UPDATE executive SET booking_pincode = executive.zip WHERE booking_pincode = '';
262 rows affected. (Query took 0.0129 seconds.)

UPDATE executive SET zip = executive.booking_pincode WHERE zip = '';
37 rows affected. (Query took 0.0055 seconds.)


01-09-2023 aakruti (live done)
ALTER TABLE `sales_executive` ADD `am_id` INT NOT NULL AFTER `so_id`;
create new table customer_vs_phone_no


02-09-2023 NAVEEN (live done)
api table live karvanu che

ALTER TABLE `customer_vs_phone_no` ADD `name` VARCHAR(50) NOT NULL AFTER `phone_no`;

02-09-2023 Dinesh (live done)
raw data excel update karvani che ====> \\24.24.25.232\xampp\htdocs\mahadev_casting\inquiry_import_new.xlsx

12-09-2023 -Parth (Live Done)
- API table Update

13-09-2023 aakruti (Live Done)
=>api table update


14-09-2023 Naveen (live done)
ALTER TABLE `my_route` ADD `no_order_inq_id` INT(11) NOT NULL AFTER `customer_id`;

15-09-2023 Dinesh (live done)
new table
no_order_inquiry_status_timeline

16-09-2023 Dinesh (Live Done)
function.class.php live karvani

4-11-2023 aakruti (live done)
ALTER TABLE `sales_executive` ADD `weekday` VARCHAR(255) NOT NULL AFTER `type_of_company`;
=>form file live karvi
\\24.24.25.232\xampp\htdocs\mahadev_casting\bbsales_tracking\form


//09-11-2023 (added by shivani) (INFORM ME BEFORE LIVE) (LIVE DONE)

ALTER TABLE `followup` ADD `inquiry_created_by` INT NOT NULL COMMENT 'sales id of inquiry created by' AFTER `response_update_flag`, ADD `inquiry_assign_to` INT NOT NULL COMMENT 'sales id of inquiry assign by' AFTER `inquiry_created_by`;


http://24.24.25.232/mahadev_casting/bbsales_tracking/followup_inquiry_assign_to_update_script.php
script run krvani


//29-11-2023 aakruti (live done)
form file live karvi
\\24.24.25.232\xampp\htdocs\mahadev_casting\bbsales_tracking\form

02-12-2023 aakruti (live done by Nilesh)
ALTER TABLE `sales_executive` ADD `create_order_approve_flag` INT NOT NULL AFTER `weekday`;

ALTER TABLE `sales_executive` ADD `quotation_approve_flag` INT NOT NULL AFTER `create_order_approve_flag`;

ALTER TABLE `sales_executive` ADD `chain_wise_view_order_history_flag` INT NOT NULL AFTER `quotation_approve_flag`;

form file live karvi
\\24.24.25.232\xampp\htdocs\mahadev_casting\bbsales_tracking\form


02-12-2023 Naveen (live done by smit)

\\24.24.25.232\xampp\htdocs\mahadev_casting => order_import_new.xlsx => new exceal file mukel

04-12-2023 aakruti (live done by smit)
form file live karvi
\\24.24.25.232\xampp\htdocs\mahadev_casting\bbsales_tracking\form

//04-12-2023 (added by shivani) (live done by smit)

\\24.24.25.232\xampp\htdocs\mahadev_casting\bbsales_tracking\sheet_import\uploads
create new folder - orders

ALTER TABLE `price_list` ADD `is_premium` INT NOT NULL AFTER `pricelist_slug`;

18-12-2023 aakruti (live done by Nilesh)
=>create new table import_order_history

19-12-2023 aakruti (live done by Nilesh)
ALTER TABLE `orders` CHANGE `entry_flag` `entry_flag` INT(11) NOT NULL COMMENT '1=>sales,2=>customer,3=>web_sales,4=>web_customer,5=>import_order';


27-12-2023 (Rahul) (live done by Nilesh)
add table - licence key

28-12-2023(Rahul) (live done by Nilesh)
=>bbsales_tracking
=>api_document_design
- connect.php - file live karvani che


18-01-2024 aakruti (live done by smit)
ALTER TABLE `customer_vs_phone_no` ADD `ref_table` VARCHAR(255) NOT NULL AFTER `name`;
form file live karvi

ALTER TABLE `customer_vs_phone_no` CHANGE `customer_id` `customer_id` INT(11) NOT NULL COMMENT 'ref_table:executive=>cusromer_id,no_order_inquiry=>inquiry_id';

23-01-2024 aakruti (Live done by smit)
update excel sheet inquiry_import_new
\\24.24.25.232\xampp\htdocs\mahadev_casting\inquiry_import_new

24-01-2024 aakruti (Live done by smit)
update excel sheet customer_import_new
\\24.24.25.232\xampp\htdocs\mahadev_casting\customer_import_new

30-04-2024 (added by shivani) (Live done by Smit)
api_table

19-07-2024 Dinesh (live Done by Nilesh)

script run karvani che
database connection kari ne
http://192.168.1.232/mahadev_casting/service/truncate_column_script.php


//20-09-2024
\\192.168.1.232\xampp\htdocs\mahadev_casting\bbsales_tracking\sheet_import\uploads\invoice

new folder create

customer_invoice_outstanding.xlsx in root directory

page_table
manually_invoice_outstanding_import - new table


--------------live done by smit ---------------------------

//03-10-2024 (added by shivani) (live Done by Nilesh)
customer_invoice_outstanding.xlsx - repalce sample file

ALTER TABLE `manually_invoice_outstanding_import` ADD `sales_id` INT NOT NULL AFTER `balance_amt`, ADD `mobile_no1` VARCHAR(200) NOT NULL AFTER `sales_id`, ADD `mobile_no2` VARCHAR(200) NOT NULL AFTER `mobile_no1`, ADD `email` VARCHAR(150) NOT NULL AFTER `mobile_no2`, ADD `detail` TEXT NOT NULL AFTER `email`, ADD `pdc_check_no` VARCHAR(100) NOT NULL AFTER `detail`, ADD `pdc_date` DATE NOT NULL AFTER `pdc_check_no`, ADD `pdc_exp_date` DATE NOT NULL AFTER `pdc_date`, ADD `pdc_amount` DOUBLE NOT NULL AFTER `pdc_exp_date`, ADD `security_chq_no` VARCHAR(100) NOT NULL AFTER `pdc_amount`;

ALTER TABLE `manually_invoice_outstanding_import` ADD `security_chq_amt` DOUBLE NOT NULL AFTER `security_chq_no`;


//08-05-2025 Keval

New Table banavel -> sales_vs_plan
api_table -> update karvu

09-05-2025 Keval
ALTER TABLE `sales_executive` ADD `monthlyorder_planner_view` INT(11) NOT NULL AFTER `brand_approval_visit_delete_flag`, ADD `monthlyorder_planner_add` INT(11) NOT NULL AFTER `monthlyorder_planner_view`, ADD `monthlyorder_planner_edit` INT(11) NOT NULL AFTER `monthlyorder_planner_add`, ADD `monthlyorder_planner_delete` INT(11) NOT NULL AFTER `monthlyorder_planner_edit`;

17-05-2025 Keval
page_table -> update karvu

27-08-2025 Dinesh
ALTER TABLE `executive` ADD `turnover` VARCHAR(255) NOT NULL AFTER `purchasing_from`, ADD `turnover_year` VARCHAR(255) NOT NULL AFTER `turnover`;

28-08-2025 Dinesh
ALTER TABLE `quotation_detail` ADD `client_code` VARCHAR(255) NOT NULL AFTER `customer_name`;

UPDATE quotation_detail SET quotation_detail.client_code = (SELECT client_code FROM executive WHERE isDelete=0 AND id=quotation_detail.customer_id);

ALTER TABLE `quotation_detail` ADD `customer_flag` TINYINT NOT NULL AFTER `client_code`;

29-08-2025 Dinesh
ALTER TABLE `orders` ADD `client_code` VARCHAR(255) NOT NULL AFTER `customer_name`, ADD `customer_flag` TINYINT NOT NULL AFTER `client_code`;
UPDATE orders SET orders.client_code = (SELECT client_code FROM executive WHERE isDelete=0 AND id=orders.customer_id);
ALTER TABLE `cart_detail` ADD `client_code` VARCHAR(255) NOT NULL AFTER `customer_name`, ADD `customer_flag` TINYINT NOT NULL AFTER `client_code`;
UPDATE cart_detail SET cart_detail.client_code = (SELECT client_code FROM executive WHERE isDelete=0 AND id=cart_detail.customer_id);
ALTER TABLE `visit` ADD `mobile_no` VARCHAR(255) NOT NULL AFTER `name`;

----------- Raw Data,Inquiry,Lead Remove in Application -------
UPDATE 11sales_executive SET prospact_view_flag = 0, prospact_insert_flag = 0, prospact_update_flag = 0, prospact_delete_flag = 0, survey_customer_insert_flag = 0, survey_customer_view_flag = 0, survey_customer_update_flag = 0, survey_customer_delete_flag = 0, customer_leads_insert_flag = 0, customer_leads_view_flag = 0, customer_leads_update_flag = 0, customer_leads_delete_flag = 0
----------- Raw Data,Inquiry,Lead Remove in Application -------

08-09-2025 Dinesh
ALTER TABLE `visit` ADD `email_id` VARCHAR(255) NOT NULL AFTER `mobile_no`;

-- update query --
UPDATE `1customer_type` SET `name` = 'Customer' WHERE `customer_type`.`id` = 7;
UPDATE `customer_type` SET `isDelete` = '01' WHERE `customer_type`.`id` = 6;
UPDATE `customer_type` SET `name` = 'Government Office' WHERE `customer_type`.`id` = 4;
-- update query --

09-09-2025 Dinesh
api_table --> update karvanu che

04-10-2025 Dinesh
visit_designation ---> new table aakhu add karvanu che

api_table

ALTER TABLE `visit` ADD `designation` INT NOT NULL AFTER `email_id`;

UPDATE 1sales_executive SET customer_insert_flag = 0, customer_update_flag = 0, customer_delete_flag=0;

----------- Live Done ----------------

09-07-2026
ALTER TABLE `executive` ADD `channel_partner_flag` TINYINT NOT NULL DEFAULT 0 AFTER `turnover_year`;

// Channel Partner Customer module â€” run on live after deploy:
// https://armor-crm.oceanhub.co.in/db_sync.php?key=armor_cp_sync_2026
// Creates: channel_partner_customer table, executive.channel_partner_flag, page_table URLs, api_table 223-228
// 04-08-2026 — Optional Address on Channel Partner Customer
ALTER TABLE `channel_partner_customer` ADD `address` TEXT NULL DEFAULT NULL COMMENT 'Optional customer address' AFTER `pincode`;

