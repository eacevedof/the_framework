<script type="text/javascript" src="/js/highchart/js/highcharts.js" view="dashboard"></script>
<script type="text/javascript" src="/js/highchart/js/modules/exporting.js" view="dashboard"></script>

<div class="row">
<?php
include_once("elem_messagedivs.php");
/*#DFF0D8 green
 * #FCF8E3 yellow
 * #F2DEDE red
 * #D9EDF7 blue
 */
if($oAlert) $oAlert->show();
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
    <article class="span12 data-block">
        <div class="data-container">
            <header>
                <h2><?php tr("tr_he_viw_totalsale"); echo $fTotalSold;?></h2>
                <?php
if($oForm) $oForm->show();
                ?>
            </header>
            <footer class="info">
                <p><?php tr("tr_he_viw_infototal");?></p>
            </footer>            
        </div>        
    </article>
    <!-- Data block -->
    <article class="span6 data-block">
        <div class="data-container">
            <header>
                <h2><?php tr("tr_he_viw_h2customers");?></h2>
            </header>
            <section>
<?php
if($oTableCustomers)$oTableCustomers->show();
?>                
            </section>
            <footer class="warning">
                <p><?php tr("tr_he_viw_infocustomers");?></p>
            </footer>
        </div>
    </article>
    <!-- /Data block -->

    <!-- Data block -->
    <article class="span6 data-block">
        <div class="data-container">
            <header>
                <h2><?php tr("tr_he_viw_h2products");?></h2>
            </header>
            <section>
<?php
if($oTableProducts)$oTableProducts->show();
?>                
            </section>
            <footer class="warning">
                <p><?php tr("tr_he_viw_infoproducts");?></p>
            </footer>
        </div>
    </article>
    <!-- /Data block -->
    <!-- Data block -->
    <article class="span12 data-block">
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
