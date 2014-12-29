<!--base.view_update v1.0.2-->
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
<?php
//errorson();
//Si ha habido un error
if($oOpButtons) $oOpButtons->show();
if($oAlert) $oAlert->show();
if($oForm) $oForm->show();
if($oJavascript) 
{
    $oJavascript->show_check_before_save();
    $oJavascript->show_fn_singleassign_window();
    $oJavascript->show_fn_setfocus();
    $oJavascript->show_fn_postback();
    $oJavascript->show_fn_enterupdate();
}
?>
            </div>
        </div>
    </div>
</section>
        </div><!--/data-container-->
    </article>
</div>
<!--/base.view_update-->