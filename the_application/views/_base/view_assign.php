<div class="row">
    <article class="span12 data-block">
        <div class="data-container">
<?php
if($oOpButtons) $oOpButtons->show();
?>               
            <section class="tab-content">                
                <!-- Tab #static -->
                <div class="tab-pane active" id="static">
<?php
if($oAlert) $oAlert->show();
if($oTableAssign) $oTableAssign->show();
if($oJavascript)
{
    $oJavascript->show_fn_closeme();
    $oJavascript->show_fn_singleassign_window();
    $oJavascript->show_fn_setfocus();
    $oJavascript->show_fn_resetfilters();
    $oJavascript->show_fn_entersubmit();
    $oJavascript->show_fn_postback();
}
?>
                </div>
            </section>
        </div>
    </article>

</div>