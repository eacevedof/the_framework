<!--modulebuilder.view_parsesql v1.0.0-->
<div class="row">
<?php
if($oScrumbs) $oScrumbs->show();
?>    
    <article class="span12 data-block">
        <div class="data-container">

<section class="tab-content">                
    <!-- Tab #static -->
    <div id="basic" class="tab-pane active">
        <div class="row-fluid">
            <div class="span12">
<?php
//errorson();
//INSERT
if($oOpButtons) $oOpButtons->show();
if($oAlert) $oAlert->show();
if($oForm) $oForm->show();
if($oJavascript) 
{
    $oJavascript->show_check_before_save();
    $oJavascript->show_fn_singleassign_window();
    $oJavascript->show_fn_setfocus();
    $oJavascript->show_fn_postback();
    $oJavascript->show_fn_enterinsert();
    $oJavascript->show_fn_entersubmit();
}
?>    
            </div>
        </div>
    </div>
</section>
        </div><!--/data-container-->
    </article>
</div>