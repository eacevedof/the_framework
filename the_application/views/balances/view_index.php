<!--base.view_index-->

<div class="row">
<?php
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
<?php
if($oOpButtons) $oOpButtons->show();
?>               
            <section class="tab-content">                
                <!-- Tab #static -->
                <div class="tab-pane active" id="static">
<?php
include_once("elem_messagedivs.php");
/*#DFF0D8 green
 * #FCF8E3 yellow
 * #F2DEDE red
 * #D9EDF7 blue
 */
if($oAlert) $oAlert->show();
if($oTableList) $oTableList->show();
if($oJavascript) 
{
    //bugg();
    $oJavascript->show_fn_closeme();
    $oJavascript->show_fn_multiassign();
    $oJavascript->show_fn_singleassign_window();
    $oJavascript->show_fn_setfocus();
    $oJavascript->show_fn_resetfilters();
    $oJavascript->show_fn_postback();
    $oJavascript->show_fn_entersubmit();
}
?>
    <!-- Data block -->
    <script type="text/javascript" src="/js/highchart/js/highcharts.js" view="index"></script>
    <script type="text/javascript" src="/js/highchart/js/modules/exporting.js" view="index"></script>
    
    <article class="span11 data-block" style="margin: 0;">
        <div class="data-container">
            <header>
                <h2><?php tr("tr_he_viw_h2grproducts");?></h2>
            </header>
            <section>
<?php
if($oGraphic)
{    
    $oGraphic->get_javascript();
    $oGraphic->get_container();
}
?>
            </section>
            <footer class="warning">
                <p><?php tr("tr_he_viw_infogrproducts");?></p>
            </footer>
        </div>
    </article>
    <!-- /Data block -->
                </div>
            </section>
        </div>
    </article>
</div>
<!--/base.view_index-->