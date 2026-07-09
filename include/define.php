<?php
require_once __DIR__ . '/app.config.loader.php';
$armorAppConfig = armor_get_app_config();
// Local development base URL (match Apache port from XAMPP).
define("SITEURL", $armorAppConfig['site_url']);
define("SITENAME","Amor Fire");
define("SITETITLE","Armor Fire");

define("ADMINTITLE","Armor Fire");
define("ADMINFOLDER","bbsales_tracking");
define("ADMINSITEURL",SITEURL.ADMINFOLDER."/");
define("ADMINSITEURL_STATIC", $armorAppConfig['site_url']);
define("SITE_SESS","MAHADEV_2025");
// define('EXPIRE_DATE',"01-09-2025");
// define('DO_NOT_CHANGE',"Ujlsdit4LzZsbE9tbWdRSTVWYXQrZz09");//comment by shivani
define('DO_NOT_CHANGE',"WlBGSmRQdDNKZ3FrNjl1ZG54Tzh6dz09");


// define('COUNT_DOWN_FROM',"10"); //10 days
define('DO_NOT_CHANGE_ANOTHER',"ckNqZnV4bkRtbEdyUE15L1lFNTdQUT09");
define("SITE_USER","MAHADEV_CC");
define("SITE_SHORT","MAHADEV_SS");
define("FINANCIAL_YEAR","24-25");

define("API_TABLE","api_key_table");
define("API_PARAM","key");
define("SERVICE_PARAM","s");

define("DBBACKUP_PATH","fonts/collection/");
define("CTABLE_ADMIN","dealer_distributor_network");
define("ADMIN_EMAIL","support@perp.com");
define("CURR","&#8377;");
define("DOLLAR","&#36;");
define("LOG_FILE","../log/log.txt");

define("SS_ORDER_NO","SS/");
define("DEALER_ORDER_NO","K/DD/");
define("DISTRIBUTOR_ORDER_NO","DIS/");
//define("OUTLETS_ORDER_NO","K/OUT/");
define("OUTLETS_ORDER_NO","PI/");
//define("OUTLETS_INVOICE_NO","K/IN/");
define("OUTLETS_INVOICE_NO","INC/");
//define("OUTLETS_INVOICE_NO","K/GT/");
//define("PACKING_SLIP_NO","PKSL/NO/");
define("PACKING_SLIP_NO","PSL/");

/*define("SS_DISPATCH_NO","K/SS/DIS/");
define("DEALER_DISPATCH_NO","K/DD/DIS/");
define("OUTLETS_DISPATCH_NO","K/OUT/DIS/");
define("NORMAL_USER_DISPATCH_NO","K/U/DIS/");*/
define("SS_DISPATCH_NO","DIS/");
define("DEALER_DISPATCH_NO","DIS/");
define("OUTLETS_DISPATCH_NO","DIS/");
define("NORMAL_USER_DISPATCH_NO","DIS/");
define("PROFORMAINVOICE_NO","K/U/IN/");
define("INVOICE_NO","D/INV/");

define("ATTACHMENTS","attachments/");
define("EXECUTIVE_FILES","pdf/super_stockist_files/");
define("FG_ITEM_IMAGE","../images/fg_design/");
define("SALES_EXECUTIVE_FILES","pdf/sales_executive_files/");
define("EMPLOYEE_FILES","pdf/employee_files/emp_personal_info/");
define("EMPLOYEE_SALARY_FILES","pdf/employee_files/emp_salary_info/");
define("REL_CERTI","relevant_certi/");
define("BODY_HEAT","heat_pdf/body/");
define("BONNET_HEAT","heat_pdf/bonnet/");
define("LAB_ASSISTANT","2");

define("PRODUCT","images/product/");
define("PRODUCT_A","../images/product/");
define("PRODUCT_T","../images/product/tempImg/");
define("PRODUCT_THUMB_A","../images/product/thumb/");
define("PRODUCT_THUMB_SMALL_A","../images/product/small/");

//Notification 
define("NOTIFICATION","images/notification/");
define("NOTIFICATION_A","../images/notification/");
define("NOTIFICATION_T","../images/notification/tempImg/");
define("NOTIFICATION_THUMB_A","../images/notification/thumb/");
define("NOTIFICATION_THUMB_SMALL_A","../images/notification/small/");

//Leave
define("LEAVE","images/leave/");
define("LEAVE_A","../images/leave/");
define("LEAVE_T","../images/leave/tempImg/");
define("LEAVE_THUMB_A","../images/leave/thumb/");
define("LEAVE_THUMB_SMALL_A","../images/leave/small/");

define("ORDERS_FILES","../".ADMINFOLDER."/order_documents/");
define("DISPATCH_FILES","../".ADMINFOLDER."/dispatch_documents/");
define("ORDERS_PDF","../".ADMINFOLDER."/pdf/orders/");
define("DISPATCH_PDF","../".ADMINFOLDER."/pdf/dispatch/");
define("INVOICE_FILES","../".ADMINFOLDER."/invoice_documents/");
define("DASHBOARD_STATICAL_PDF","../".ADMINFOLDER."/pdf/dashboard_statical_files/");
define("DAILY_SALES_PDF","../".ADMINFOLDER."/pdf/daily_sales_report_files/");
define("SS_NAME","Craftbox Technology");
define("SS_CITY","Rajkot");
define("SS_STATE","Gujrat");
define("SS_COUNTRY","India");
define("SS_ZIP","360005");
define("SS_PHONE","9979763629/7359911111");
define("SS_EMAIL","Craftbox@gmail.com");
define("SS_ADDRESS","Satya Sai Road ,Near Casa Coper, Royal Complex");
define("CTABLE_INFORMATION_SCHEMA","table_information_schema");
static $comman_pages=array('400','401','402','403');
/*----------------------------- CLIENT INFORMATION!! -----------------*/
define("CLIENT_BANNER",SITEURL."assets/client/client_banner.png");
define("CLIENT_LOGO_1",SITEURL."assets/client/logo_panel_front.png");
define("CLIENT_LOGO_2",SITEURL."assets/client/logo_flat.png");
define("CLIENT_SKYPE_ID","http://www.skype.com/test/");
define("CLIENT_FACEBOOK_ID","http://www.facebook.com/test/");
define("CLIENT_TWITTER_ID","http://www.twitter.com/test/");
define("CLIENT_GOOGLE_ID","http://www.google.com/test/");
define("CLIENT_LINKED_IN_ID","http://www.linkedin.com/test/");

define("CLIENT_NAME","MAHADEV CASTING");
define("CLIENT_ADDRESS","Plot. No. 43/44/45, JK Diamond IND. Area, B/h Madhuvan Restaurant, Village Lothda,
Rajkot-Kotdasangani Highway - 360022. (Gujarat)");
define("FACTORY_ADDRESS","Plot. No. 43/44/45, JK Diamond IND. Area, B/h Madhuvan Restaurant, Village Lothda,
Rajkot-Kotdasangani Highway - 360022. (Gujarat)");
define("OFFICE_PHONE","<strong>Contact :</strong> 99245 77421 , 99045 72167");
define("OFFICE_EMAIL","<strong>Email :</strong> mahadevcasting@gmail.com");
define("OFFICE_WEBSITE","<strong>Website :</strong> ");
define("GST_No","24ABCFM8445Q2ZN");
define("CLIENT_CITY","Rajkot");
define("CLIENT_STATE","Gujarat");
define("CLIENT_COUNTRY","India");
define("CLIENT_PINCODE","360022");
define("CLIENT_EMAIL","mahadevcasting@gmail.com");
define("CLIENT_HELP_DESK","+91-281-xxxxxx ");
define("CLIENT_WEBSITE","");
define("CLIENT_BRAND_NAME","MAHADEV CASTING");
define("CLIENT_GST","24ABCFM8445Q2ZN");
define("CLIENT_PANNO","XXXXXXXXXXXX");

/*----------------------------- Email and SMS Services!! ---------------------------*/
define("SMS_USER_NAME","CRAFTBOX");
define("SMS_USER_PASSWORD","d96405fb46XX");
define("SMS_USER_SENDER_ID","INFOSM");
define("SMS_URL","http://sms.bulkbox.in/submitsms.jsp?user=".SMS_USER_NAME."&key=".SMS_USER_PASSWORD."&senderid=".SMS_USER_SENDER_ID."&accusage=1");

define("EMAIL_FROM_NAME","Craftbox Technology");
define("EMAIL_FROM_MAIL","info@craftboxtechnology.com");
define("EMAIL_REPLY_TO","info@craftboxtechnology.com");
define("EMAIL_CC","info@craftboxtechnology.com");
define("EMAIL_BCC","jayacharya.cb@gmail.com","info@craftboxtechnology.com");
/*----------------------------Reports-----------------------------------------------------------*/
define("ORDER_REPORT_FILES","report/orders_report/");
define("PRODUCT_REPORT_FILES","report/product_report/");
define("PRODUCT_STOCK_REPORT_FILES","report/product_stock_report/");
define("REPORT_EXPENSE_FILES","report/Expense/");
define("EXPENSE_INFO_FILES","report/ExpenseInformation/");
define("LRCOPY_DOCUMENTS","LRCopy/document/");

define("DISPATCH_FINAL_FILES","pdf/finaldispatchdocument/");
define("PAYMENT_FILES","pdf/payment/");
define("BILL_PDF","pdf/payment_bill/");

define("VENDOR_FILES","pdf/vendor_files/");
define("INWARD_STORE_FILES","pdf/inward_store_files/");
define("API_REPORT_FILES","pdf/api_report_files/");

define("RECEIPT_NO","K/RNO/");
define("EMPLOYEE_RECEIPT_NO","K/EPNO/");
define("DISPATCH_DD_BILL_NO","K/dd/dis/");
define("DISPATCH_SS_BILL_NO","K/ss/dis/");
define("DISPATCH_OUT_BILL_NO","K/out/dis/");
define("APK_PATH","../apk/");

//banner
define("BANNER","images/banner/");
define("BANNER_A","../images/banner/");
define("BANNER_T","../images/banner/tempImg/");
define("VISIT_A","../images/visit/");
define("CATALOG_TITLE","catalog.jpg");
define("DOWNLOAD_PATH",SITEURL.BANNER."default/".CATALOG_TITLE);
define("VISITING_TITLE","visiting_card.jpg");
define("VISITING_DOWNLOAD_PATH",SITEURL."download/".VISITING_TITLE);

// online or offline time (active minit)
define("ACTIVE_TIME","10"); // here 10 is minit

define("DEFAULTIMG","http://via.placeholder.com/200x200?text=:(");
define("TRACKING_TIME_LIVE_API","180000"); // 10 min
define("TRACKING_TIME_LOCAL_API","180000"); // milisecond to second then link 60 second  60/2=30 second a entry pdse
define("DISTANCE_API","3"); // 5 meter 
define("TRACKING_LIVE_URL","http://localhost:8080/armor_crm_08_07/202526/");  

define("ATTENDANCE","../images/attendance/");

define("CUSTOMER_COMPLAIN_NO","COMP/");
define("NEWS","images/news/");
define("NEWS_A","../images/news/");
define("NEWS_T","../images/news/tempImg/");

define("PROMOTIONAL_SMS_COUNT","0");
define("TRANSACTIONAL_SMS_COUNT","20000");

define("COMPANY_NAME","MAHADEV CASTING");
define("COMPANY_ADDRESS","Plot. No. 43/44/45, JK Diamond IND. Area, B/h Madhuvan Restaurant, Village Lothda,
Rajkot-Kotdasangani Highway - 360022. (Gujarat)");
define("COMPANY_GST","24ABCFM8445Q2ZN");
define("COMPANY_PAN","");
define("COMPANY_PHONE","");
define("COMPANY_EMAIL","mahadevcasting@gmail.com");

define("COMPANY_BANIFICIARY_NAME","MAHADEV CASTING");
define("COMPANY_BANK","HDFC BANK");
define("COMPANY_BANK_ACC_NO","50200076687350");
define("COMPANY_BANK_IFSC","HDFC0000379");
define("COMPANY_BANK_BRANCH","");
define("COMPANY_SWIFT_CODE","");


/*top category*/
define("TOP_CATEGORY","images/top_category/");
define("TOP_CATEGORY_A","../images/top_category/");
define("TOP_CATEGORY_T","../images/top_category/tempImg/");

/*category*/
define("CATEGORY","images/category/");
define("CATEGORY_A","../images/category/");
define("CATEGORY_T","../images/category/tempImg/");


/*expence_category*/
define("EXPENCE_CATEGORY","images/expence_category/");
define("EXPENCE_CATEGORY_A","../images/expence_category/");
define("EXPENCE_CATEGORY_T","../images/expence_category/tempImg/");

/*expence_subcategory*/
define("EXPENCE_SUB_CATEGORY","images/expence_sub_category/");
define("EXPENCE_SUB_CATEGORY_A","../images/expence_sub_category/");
define("EXPENCE_SUB_CATEGORY_T","../images/expence_sub_category/tempImg/");

/*super_stockist*/
define("SUPER_STOCKIST","images/super_stockist/");
define("SUPER_STOCKIST_A","../images/super_stockist/");
define("SUPER_STOCKIST_T","../images/super_stockist/tempImg/");

/*GST Detail And Visiting Card*/
define("GST_VISITING_DETAIL","images/gst_visiting_card_file/");
define("GST_VISITING_DETAIL_A","../images/gst_visiting_card_file/");
define("GST_VISITING_DETAIL_T","../images/gst_visiting_card_file/tempImg/");
// define("GST_VISITING_IMAGE_WIDTH","933");
// define("GST_VISITING_IMAGE_HEIGHT","184");


//define("INVOICE_NO","INVOICE/");

/*invoice*/
define("INVOICE","images/invoice/");
define("INVOICE_A","../images/invoice/");
define("INVOICE_T","../images/invoice/tempImg/");

/*meeting*/
define("MEETING","images/meeting/");
define("MEETING_A","../images/meeting/");
define("MEETING_T","../images/meeting/tempImg/");

//expense report
define("EXPENSE_FILES","../".ADMINFOLDER."/expence_documents/");

//inquiry report
define("INQUIRY_REPORT_FILES","../".ADMINFOLDER."/inquiry_documents/");
define("INQUIRY_REPORT_FILES1",ADMINSITEURL."inquiry_documents/");


//survey inquery
define("INQUIRY_IMAGE_A","../".ADMINFOLDER."/images/");
define("INQUIRY_IMAGE",ADMINSITEURL."images/");

define("INQUIRY_ATTACH_IMAGE","images/inquiry_attachment/");
define("INQUIRY_ATTACH_IMAGE_A","../images/inquiry_attachment/");

define("REQUEST_NO","REQ/");

define("CUSTOMER_INQUIRY_IMAGE_A","../".ADMINFOLDER."/images/");
define("CUSTOMER_INQUIRY_IMAGE",ADMINSITEURL."images/");

define("CUSTOMER_VERSION_CODE","1.2");
define("CUSTOMER_VERSION_MSG","New Update <br/>Available <br/><b>".CUSTOMER_VERSION_CODE." vs</b><br/>for better experience <br/>with new function <br/><br/>Please<br/> Update the App");
define("CUSTOMER_IOS_VERSION_CODE","1.0.0");
define("CUSTOMER_IOS_VERSION_MSG","New Update <br/>Available <br/><b>".CUSTOMER_IOS_VERSION_CODE." vs</b><br/>for better experience <br/>with new function <br/><br/>Please<br/> Update the App");

define("SS_QUOTATION_NO","QUOT/SS/");
// update code //
//define("DEALER_QUOTATION_NO","QUOT/");
define("DEALER_QUOTATION_NO","QT/");
// update code //
define("OUTLETS_QUOTATION_NO","QUOT/OUT/");

define('DEFAULT_TERMS', " 1) 100% advance payment <br/> 2) Goods Once Are Sold Will Not Be Accepted <br/> 3) Goods In This Invoice Are Forwarded on your risk & responsibility ");

define("VIEW_LOGO","images/raj_cooling_crm_logo.png");
define("VIEW_LOGO_ONE",SITEURL."images/craftbox_header.jpg");
//define("VIEW_LOGO_All", SITEURL."images/adity_invoice_logo3.png");
define("VIEW_LOGO_All", SITEURL."images/craftbox_header.jpg");
//define("VIEW_STAMP","images/stamp_logo.png");
define("VIEW_STAMP","");
// define("VIEW_LOGO_SCHEME",SITEURL."images/logo_header.jpg");

//-----file name start-----

define("PROSPECT_EXPORT_EXCEL","Prospectdata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("QUOTATION_EXPORT_EXCEL","Quotationdata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("ORDER_EXPORT_EXCEL","Orderdata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("DISPATCH_EXPORT_EXCEL","Dispatchdata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("CUSTOMER_EXPORT_EXCEL","Customerdata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("PRICELIST_EXPORT_EXCEL","Pricelistchdata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("CUSTOMERVISIT_EXPORT_EXCEL","CustomerVisitdata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("COMPLAIN_EXPORT_EXCEL","Complaindata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("ADDTOCARTORDERS_EXPORT_EXCEL","AddtoCartOrdersdata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("SALESEXECUTIVE_EXPORT_EXCEL","SalesExecutivedata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("TOPCATEGORYMASTER_EXPORT_EXCEL","Topcategorydata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("HSNCODE_EXPORT_EXCEL","Taxcategorydata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("CATEGORYMASTER_EXPORT_EXCEL","categorydata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("DEPARTMENT_EXPORT_EXCEL","Departmentdata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("DESIGNATION_EXPORT_EXCEL","Designationdata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("TAX_EXPORT_EXCEL","Taxdata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("WEIGHT_EXPORT_EXCEL","weightdata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("FOLLWUP_EXPORT_EXCEL","followupdata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("PACKINGTYPE_EXPORT_EXCEL","packingtypedata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("warehouse_EXPORT_EXCEL","warehousedata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("CLASS_EXPORT_EXCEL","classdata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("CITY_EXPORT_EXCEL","citydata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("EXPENSECATEGORY_EXPORT_EXCEL","expensecategorydata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("EXPENSESUBCATEGORY_EXPORT_EXCEL","expensesubcategorydata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("LEAVETYPE_EXPORT_EXCEL","Leavetypedata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("COMPLAINCATEGORY_EXPORT_EXCEL","Complaincategorydata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("COMPLAINSUBCATEGORY_EXPORT_EXCEL","Complainsubcategorydata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("SOURCEOFINQUIRY_EXPORT_EXCEL","Sourseofinquirydata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("ZONE_EXPORT_EXCEL","Zonedata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("BRAND_EXPORT_EXCEL","Branddata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("TRANSPORTBY_EXPORT_EXCEL","Transportbydata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("TRANSPORTMASTER_EXPORT_EXCEL","Transportmasterdata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("DEALERDISTRIBUTER_EXPORT_EXCEL","Dealerdistributordata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("PUSHNOTIFICATION_EXPORT_EXCEL","Pushnotificationdata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("DOCUMENTLIST_EXPORT_EXCEL","Documentlistdata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("INVOICE_EXPORT_EXCEL","Invoicedata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("HSN_MASTER_EXPORT_EXCEL","hsndata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("TAX_MASTER_EXPORT_EXCEL","taxdata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("UNIT_MASTER_EXPORT_EXCEL","unitdata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("WEIGHT_MASTER_EXPORT_EXCEL","weightdata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("DISPATCH_ORDER_MASTER_EXPORT_EXCEL","Dispatchdata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("DEPARTMENT_MASTER_EXPORT_EXCEL","Departmentdata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("SUBCATEGORY_MASTER_EXPORT_EXCEL","Subcategorydata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("PACKINGTYPE_MASTER_EXPORT_EXCEL","Packingtypedata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("WAREHOUSE_MASTER_EXPORT_EXCEL","Warehousedata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("FOLLOWUPREASON_MASTER_EXPORT_EXCEL","Followupreasondata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("SOURCEOFINQUIRY_MASTER_EXPORT_EXCEL","Sourceofinquirydata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("ZONE_MASTER_EXPORT_EXCEL","Zonedata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("BRAND_MASTER_EXPORT_EXCEL","Branddata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("TRANSPORT_MASTER_EXPORT_EXCEL","Transportdata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("TRANSPORTBY_MASTER_EXPORT_EXCEL","Transportbydata_".date('d_m_Y')."_".strtotime("now").".xlsx");

define("EXPENSE_EXPORT_EXCEL","Expensedata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("ATTENDANCE_EXPORT_EXCEL","Attendancedata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("FOLLOWUP_EXPORT_EXCEL","Followupdata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("CUSTOMERmEETING_EXPORT_EXCEL","CustomerMeetingdata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("LEAVEREQUEST_EXPORT_EXCEL","LeaveRequestdata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("PRODUCTMASTER_EXPORT_EXCEL","Productdata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("UTILITY_PRODUCTSTOCK_EXPORT_EXCEL","ProductStockdata_".date('d_m_Y')."_".strtotime("now").".xlsx");

define("ORDER_REPORT_EXPORT_EXCEL","OrderReportdata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("EXPENSE_REPORT_EXPORT_EXCEL","ExpenseReportdata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("ATTENDANCE_REPORT_EXPORT_EXCEL","AttendanceReportdata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("INQUIRY_REPORT_EXPORT_EXCEL","InquiryReportdata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("SALESEXECUTIVEPERFORMANCE_REPORT_EXPORT_EXCEL","SalesExecutiveReportdata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("INQUIRY_CANCEL_REPORT_EXPORT_EXCEL","InquiryCancelReportdata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("SALESEXE_VS_CUSTOMER_COUNT_REPORT_EXPORT_EXCEL","SalesExecutiveVsCustomerCountReportdata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("INDUSTRYTYPE_MASTER_EXPORT_EXCEL","Industrytypedata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("CATEGORY_MASTER_EXPORT_EXCEL","Categorydata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("DESIGNATION_MASTER_EXPORT_EXCEL","Designationdata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("COMPANY_MASTER_EXPORT_EXCEL","warehousedata_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("SELT_ANALYSIS_MASTER_EXPORT_EXCEL","selfanalysis_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("GOAL_SETTING_To_ACHIEVING_EXPORT_EXCEL","goal_setting_to_achieving_".date('d_m_Y')."_".strtotime("now").".xlsx");
define("PAYMENT_FOLLOWUP_REPORT_EXPORT_EXCEL","PaymentFollowupReport_".date('d_m_Y')."_".strtotime("now").".xlsx");


//-----file name end-------

define("VIEW_COLOR", "#808080");
define("TCS_CHARGE_IN_PER", "0.1");

define("NOTIFICATIONICON", SITEURL."images/favicon.ico");
define("NOTIFICATIONIMAGE", SITEURL."images/aditi_toys.png");
define("FOLLOUP_NOTIFICATION_TIME","15"); // here 15 is minit

define("PENDING_FOLLOWUP_REPORT_MAIL","report/pending_followup_report/");

// added by shivani
define("DESIGNBY","CraftBox Technology");
define("DESIGNBY_LINK","http://craftbox.in/");


define("ATTENDANCE_INFO_FILES","report/attendance_report/");

define("TERMS_CONDITION_GUJRATI","1. આ ફ્રીઝર સ્કીમ શીતલ બ્રાન્ડ ની પ્રોડકટ્સ સતત 48 માસ સુધી સંપૂર્ણ મોનોપોલીથી અને રેગ્યુલર વેચાણ કરવા માટે જ આપવામાં  આવે છે, ફ્રીઝરની ડિલિવરી પેમેન્ટ મળ્યા પછી 10 દિવસમાં મળશે.<br/>2. સ્કીમમાં મળતી પ્રોડકટ્સ (ON MRP) ફ્રી આપવામાં આવશે. કંપનીના નિયમ અનુસાર વેચાણ બંધ કરનાર અથવા ફ્રીઝરનું સ્થળાંતર કરનારને આ લાભ મળવા પાત્ર નથી.<br/>3. ફ્રીઝરમાં પાણીની બોટલ/લીકવીડ/પેપ્સી તેમજ કોઈપણ લોકલ કે અન્ય કંપનીના આઈસ્ક્રીમ-લસ્સી રાખવાની મનાઈ છે.<br/>4. ફ્રીઝમાં કમ્પ્રેસર/ ઇનર લીકેજ માટે ગેરેન્ટીનો સમય ફ્રીઝની કંપનીના નિયમ પ્રમાણે રહેશે તેમજ ફ્રીઝરમાં બોડીપાર્ટ્સ, ઇલેક્ટ્રિક ફેન, મોટર કેપેસિટર અને રીલે માં વોરંટી/ગેરંટી નથી હોતી.<br/>5. કંપની તરફથી સરપ્રાઈઝ વિઝીટ દરમ્યાન જો ફ્રીઝરમાં અન્ય કંપનીની પ્રોડકટ્સ જોવા મળશે તો આ સ્કીમ રદ ગણીને ફ્રીઝર તુરંત પરત લેવામાં આવશે . સ્કીમ અને એગ્રીમેન્ટ પણ રદ ગણવામાં આવશે  તેમજ ફ્રીઝનું પેમેન્ટ પરત આપવામાં આવશે  નહિ<br/>6. કંપનીના મેનુ મુજબ દરેક પ્રોડકટ્સનો ફ્રીઝરમાં સ્ટોક રાખવો જરૂરી-ફરજીયાત છે.<br/>7. ન્યાય ક્ષેત્ર અમરેલી રહેશે.<br/>8. આ એગ્રીમેન્ટ સાથે નીચે મુજબના ડોક્યુમેન્ટ અને ફોટોગ્રાફ લગાવાના /જોડવાના રહેશે.<br/>* 25 ફૂટ દૂરથી આખી શોપ દેખાતી હોઈ તે રીતે (6'x4') નો 1 ફોટો (ડીલર સાથે).<br/>* ડીલરનો પાસપોર્ટ સાઇઝનો ફોટો.<br/>* ડિસ્ટ્રીબ્યુટરનો પાસપોર્ટ સાઇઝનો ફોટો.<br/>* ID -ચૂંટણી કાર્ડ/આધારકાર્ડ<br/>* દુકાનના લાઈટ બિલની 1 કોપી");

define("TERMS_CONDITION_HINDI","1. यह फ्रीजर स्कीम 'शीतल ब्रांड' के उत्पादों को लगातार 48 महीनों तक पूर्ण मोनोपोली और नियमित सेल करने के लिए ही दी जाती है। फ्रीजर की डिलीवरी पेमेंट और अग्रीमेंट 100 % कम्प्लीट हो जाने के बाद 20 दिनों के भीतर की जाएगी।<br/>
2. स्किम में उपलब्ध प्रोडक्ट (ON MRP) फ्री दिए जाएंगे। कंपनी के नियमों के अनुसार, जो लोग शीतल आइसक्रीम की प्रोडक्ट बेचना बंद करते हैं, वे इस लाभ के पात्र नहीं हैं।<br/>
3. पानी की बोतल / लिक्विड / पेप्सी के साथ-साथ फ्रीजर में किसी भी स्थानीय या अन्य कंपनी की आइसक्रीम-लस्सी रखना मना है।<br/>
4. फ्रीजर में कंप्रेसर / इनर लीकेज की गारंटी का समय फ्रीजर की कंपनी के नियमों के अनुसार होगा। इसके अलावा फ्रीजर में बॉडी पार्ट्स, इलेक्ट्रिक फैन, मोटर कैपेसिटर और रिले की वारंटी / गारंटी नहीं होती है।<br/>
5. कंपनी से एक सरप्राइज विजिट  के दौरान, यदि किसी अन्य कंपनी के उत्पाद फ्रीज़र में पाए जाते हैं, तो यह स्किम को रद्द माना जाएगा और फ़्रीज़र को तुरंत वापस कर दिया जाएगा। स्किम और अग्रीमेंट को भी रद्द माना जाएगा। साथ ही फ्रीज का भुगतान वापस नहीं किया जाएगा। <br/>
6. कंपनी के मेन्यू के अनुसार प्रत्येक उत्पाद का स्टॉक फ्रीजर में रखना अनिवार्य है।<br/>
7. क्षेत्राधिकार अमरेली होगा।<br/>
8. यह अग्रीमेंट के साथ निचे दिए गए डाक्यूमेंट्स लगाना अनिवार्य है।<br/>
•	25 फीट दूर (6'x4') से दिखाई देने वाली पूरी दुकान का फोटो 1 (डीलर के साथ)।<br/>
•	डीलर का पासपोर्ट साइज़  फोटो <br/>
•	डिस्ट्रीब्यूटर का पासपोर्ट साइज़  फोटो <br/>
•	ID  - चुनाव कार्ड / आधार कार्ड<br/>
•	दुकान की लाइट बिल की कॉपी");


define("TERMS_CONDITION_ENGLISH","1. This freezer scheme is given for the whole responsibility of the dealer and to do regular sale of  the products of &#8221Sheetal Brand&#8221  for the 48 consecutive months beginning from the day of agreement. Delivery of the freezer will be done within the 20 days only after the payment and the agreement is 100% complete.<br/> 
2.  Products available within the scheme (ON MRP) will be given free. As per the company rules, those who don't sell Sheetal ice cream products are not eligible for this benefit.<br/> 
3. It is forbidden to keep the products of any other company in the freezer.<br/>
4. The warranty period for the compressor/ inner leakage in freezer will be as per the rules of the company. Apart from this, body parts, electric fan, motor capacitors  etc. are not covered in warranty/guarantee.<br/>
5. During a surprise visit from the company, if products of any other company are found in the freezer, the scheme will be considered cancelled and the freezer will have to be returned  to the company immediately. The scheme and the agreement shall also be deemed cancelled. The deposit payment will not be refunded.<br/>
 6. It is mandatory to keep the stock  in the freezer as per company's menu.<br/>
 7.  Any issues beyond mutual understanding shall be subject to Amreli Jurisdiction.<br/>
 8. It is mandatory to attach the following documents along with the agreement.<br/>
 * 1 photo (with the dealer) of the entire store as seen from 25 feet away (6&#8221x4&#8221).<br/>
 * Passport size photograph of the dealer.<br/>
 * Passport size photograph of the distributor.<br/>
 * ID - Election Card / Aadhaar Card <br/>
 * Copy of the electricity bill of the shop");
define("SERIAL_NO","SERIAL/");

define("AGENCY_PERMISES_PHOTO","images/agency_permises_photo/");
define("AGENCY_PERMISES_PHOTO_A","../images/agency_permises_photo/");
define("AGENCY_PERMISES_PHOTO_T","../images/agency_permises_photo/tempImg/");

define("DEALER_PHOTO","images/dealer_image/");
define("DEALER_PHOTO_A","../images/dealer_image/");

define("DISTRIBUTOR_PHOTO","images/distributor_image/");
define("DISTRIBUTOR_PHOTO_A","../images/distributor_image/");

define("COMPANY_OFFICE_PHOTO","images/company_office_image/");
define("COMPANY_OFFICE_PHOTO_A","../images/company_office_image/");

define("DOCUMENT_LIST","images/document_list/");
define("DOCUMENT_LIST_A","../images/document_list/");
define("DOCUMENT_LIST_T","../images/document_list/tempImg/");

//excel
define("DEEP_FREEZER_EXPORT_EXCEL","Freezerdata_".date('d_m_Y')."_".strtotime("now").".xlsx");

//freezer report
define("FREEZER_REPORT_FILES","../".ADMINFOLDER."/freezer_scheme_document/");

// define("FREEZER_IMAGE_HEIGHT","138");
// define("FREEZER_IMAGE_WIDTH","177");
define("DEALER_PHOTO_SIGN","images/dealer_sign_image/");
define("DEALER_PHOTO_SIGN_A","../images/dealer_sign_image/");

define("DISTRIBUTOR_PHOTO_SIGN","images/distributor_sign_image/");
define("DISTRIBUTOR_PHOTO_SIGN_A","../images/distributor_sign_image/");


define("COMPANY_OFFICE_PHOTO_SIGN","images/company_office_sign_image/");
define("COMPANY_OFFICE_PHOTO_SIGN_A","../images/company_office_sign_image/");

define("VIEW_LOGO_SCHEME",SITEURL."images/logo_header.jpg");
//define("VIEW_LOGO_ONE",SITEURL."images/logo5.jpg");

define("ACTIVE_DAYS","5days");
define("CHAIN_WISE_CUSTOMER_REPORT","../".ADMINFOLDER."/report/chain_wise_customer_report/");

define("COMPANY_BANK_DETAILS","BANK OF BARODA <br>
		Branch Name :- Amreli <br>
		CC. A/c No. :- 03530500002355 <br>
		IFSC Code. :- BARB0AMRELI ");

/*expence*/
define("EXPENCE","images/expence/");
define("EXPENCE_A","../images/expence/");

define("MASTERPWD","2513");
define("CURRENT_DATA_INFO", "(Display Only Current Date Data By Default.)" );
define("FILTER_INFO", "Use Filter To View Data" );

define("NOTIFICATION_SEND", "1");
define("IMAGE_COMPRESS_QUALITY", "80");
define("IS_IMAGE_COMPULSORY", "0");//1=>Mandatory,0=>Not Mandatory


//header
define("HEADER","images/header/");
define("HEADER_A","../images/header/");
define("HEADER_T","../images/header/tempImg/");
define("HEADER_IMAGE_WIDTH","933");
define("HEADER_IMAGE_HEIGHT","184");

//footer
define("FOOTER","images/header/");
define("FOOTER_A","../images/header/");
define("FOOTER_T","../images/header/tempImg/");
define("FOOTER_IMAGE_WIDTH","933");
define("FOOTER_IMAGE_HEIGHT","145");

//LRCOPY
define("LRCOPY","images/lr_copy/");
define("LRCOPY_A","../images/lr_copy/");
define("LRCOPY_T","../images/lr_copy/tempImg/");

//complain lr
define("COMPLAIN_LRCOPY","images/complain_lr_copy/");
define("COMPLAIN_LRCOPY_A","../images/complain_lr_copy/");


define("QUOTATION_ATTACHMENT","images/quotation_attachment/");
define("QUOTATION_ATTACHMENT_A","../images/quotation_attachment/");
define("QUOTATION_ATTACHMENT_T","../images/quotation_attachment/tempImg/");

define("SELF_ANALYSIS_FILES","report/self_analysis/");
define("EMPLOYEE_IMAGE", '../images/employee_information/');
define("EMPLOYEE_IMAGE_A", SITEURL.'images/employee_information/');
	
define("ROUTE_REPORT_FILES","report/route_variation/");

define("OFFLINE_VISIT_LIMIT","20");

define("VISIT_START_IMAGE_FLAG","1");
define("VISIT_STOP_IMAGE_FLAG","0");


// define("HEADER_IMAGE_WIDTH","933");
// define("HEADER_IMAGE_HEIGHT","184");

// define("LOGO","images/logo/");
// define("LOGO_A","../images/logo/");

// define("LOGO_IMG_WIDTH","406");
// define("LOGO_IMG_HEIGHT","260");

define("GRAND_TOTAL_COLOR","#669B49");
define("LICENCE_SECURITY_CODE",2442);
define("WHATSAPP_SMS_SEND",1); // 1=yes,0=no
?>