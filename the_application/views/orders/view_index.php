<!-- view_index -->
<div class="row">
<?php
if($oScrumbs) $oScrumbs->show();

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
<?php
if($oOpButtons) $oOpButtons->show();
?>               
            <section class="tab-content">                
                <!-- Tab #static -->
                <div class="tab-pane active" id="static">
<?php
if($oAlert) $oAlert->show();
if($oTableList) $oTableList->show();
/**
 * @type HelperJavascript
 */
if($oJavascript) 
{
    $oJavascript->show_fn_singleassign_window();
    $oJavascript->show_fn_setfocus();
    $oJavascript->show_fn_resetfilters();
    $oJavascript->show_fn_postback();
    $oJavascript->show_fn_entersubmit();
}
?>
                </div>
            </section>
        </div>
    </article>

</div>
<!-- /view_index -->