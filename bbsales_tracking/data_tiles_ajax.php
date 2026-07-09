<!-- attendance details Start from here -->
<?php
if($db->checkUserPermission(593,$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'],'view'))
{
?> 
<div class="col-md-6 col-sm-6" id="attendance-data">
</div>
<?php
}
?> 
<!-- attendance details end  from here -->

<!-- visit Details Start from here -->
<?php
if($db->checkUserPermission(577,$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'],'view'))
{
    ?> 
    <div class="col-md-6 col-sm-6" id="visit-data">
    </div>
    <?php
}
?> 
<!-- visit Details end  from here -->

<!-- Quotation  details start from here -->
<?php
if($db->checkUserPermission(607,$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'],'view'))
{
    ?> 
    <div class="col-md-6 col-sm-6" id="quotation-data">
    </div>
 <?php
}
?> 
<!-- Quotation  details End from here -->

<!-- order details starts from here -->
<?php
if($db->checkUserPermission(565,$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'],'view'))
{
    ?> 
    <div class="col-md-6 col-sm-6" id="order-data">
    </div>
    <?php
}
?> 
<!-- Orders details End from here -->

<!-- followup details Start from here -->
<?php
if($db->checkUserPermission(583,$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'],'view'))
{
    ?> 
    <div class="col-md-6 col-sm-6" id="followup-data">
    </div> 
    <?php
}
?> 
<!-- followup details end from here -->

<!-- Inquiry details starts from here -->
<?php
if($db->checkUserPermission(572,$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'],'view'))
{
    ?> 
    <div class="col-md-6 col-sm-6" id="inquiry-data">
    </div>  
    <?php
}
?>
<!-- Inquiry details End from here -->

<!-- leave details Start from here -->
<?php
if($db->checkUserPermission(594,$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'],'view'))
{
    ?> 
    <div class="col-md-6 col-sm-6" id="leave-data">
    </div>
    <?php
}
?> 
<!-- leave details end  from here -->

<!-- Lead details Start from here -->
<?php
if($db->checkUserPermission(620,$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'],'view'))
{
    ?> 
    <div class="col-md-6 col-sm-6" id="lead-data">
    </div>
    <?php
}
?>  
<!-- Lead details End from here -->

<!-- expense details Start from here -->
<?php
if($db->checkUserPermission(592,$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'],'view'))
{
    ?> 
    <div class="col-md-6 col-sm-6" id="expense-data">
    </div>
    <?php
}
?> 
<!-- expense details end  from here -->

<!-- prospect details Start from here -->
<?php
if($db->checkUserPermission(621,$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'],'view'))
{
    ?> 
    <div class="col-md-6 col-sm-6" id="prospect-data">
    </div> 

 <?php
}
?>
<!-- prospect details End from here -->

<!-- Complain details Start from here -->
<?php
if($db->checkUserPermission(581,$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'],'view'))
{
    ?> 
    <div class="col-md-6 col-sm-6" id="complain-data">
    </div>
    <?php
}
?> 
<!-- Complain details end  from here -->



