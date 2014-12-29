<!-- base.view_insert v1.0.2-->
<div class="row">
<?php
if($oAnchorDown) $oAnchorDown->show();

if($oScrumbs) $oScrumbs->show();
//si hay tabs se aplica fix-fortabs
if($oTabs)
{    
?>    
    <article class="span12 data-block datablock-fix-fortabs">
<?php
}
//etiqueta comun article
else 
{    
?>
    <article class="span12 data-block">
<?php
}
//si se usan tabs hay que añadir la clase <article class="span12 data-block datablock-fix-fortabs">
if($oTabs) $oTabs->show();
?>
        <div class="data-container">

<section class="tab-content">                
    <!-- Tab #static -->
    <div id="basic" class="tab-pane active">
        <div class="row-fluid">
            <div class="span12">
<!--                
<div data-provides="fileupload" class="fileupload fileupload-exists">
    <input type="hidden" value="" name="">
    <div style="width: 50px; height: 50px;" class="fileupload-new thumbnail">
        <img src="http://www.placehold.it/50x50/EFEFEF/AAAAAA">
    </div>
    <div style="width: 50px; height: 50px; line-height: 50px;" class="fileupload-preview fileupload-exists thumbnail">
            <img src="">
    </div>
    <span class="btn btn-file">
        <span class="fileupload-new">Select image</span>
        <span class="fileupload-exists">Change</span>
        <input type="file" name="">
    </span>
    <a data-dismiss="fileupload" class="btn fileupload-exists" href="#">Remove</a>
</div>
-->
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
<!--/base.view_insert -->