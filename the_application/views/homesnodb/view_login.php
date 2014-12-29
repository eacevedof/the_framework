<!-- homesnodb.view_login -->
<!-- Section - Main page container -->
<section class="container login" role="main">

<?php
if($oAlert) $oAlert->show();

//Botones
foreach($arLinks as $oAnchor)
    $oAnchor->show();
?>    
    
    <h1><?php //$oAnchor->show();?></h1>
    
    <div class="data-block">
<?php
if($oForm) $oForm->show();
if($oJavascript) 
{
    $oJavascript->show_check_before_save();
    $oJavascript->show_fn_singleassign_window();
    $oJavascript->show_fn_setfocus();
    $oJavascript->show_fn_postback();
    $oJavascript->show_fn_enterinsert();
}
?>
    </div>
</section>
<!-- /Section - Main page container -->
<script src="js/huraga/bootstrap/bootstrap-tooltip.js" type="text/javascript"></script>
<script type="text/javascript" in="homesnodb.view_login">
Huraga.logo_login("<?php echo APP_LOGIN_LOGO_FILENAME; ?>");
</script>
<!-- /homesnodb.view_login -->