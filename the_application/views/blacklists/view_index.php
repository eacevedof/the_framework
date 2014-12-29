<?php
//bugp();
//include_once("apphelper_actionsbar.php");
import_apphelper("actionsbar");
$oActionsBar = new ApphelperActionsbar("frmSearch");
$oActionsBar->set_h2("Hola!");

?>
<script>
//var arResetFields = ["idn","fecha","hora"];
//TfwFieldValidate.reset(arResetFields);

</script>
<div class="row">
    <article class="span12 data-block">
        <div class="data-container">
<?php 
//$oActionsBar->show();
?>
<header>
    <h2>Opc.</h2>
    <ul class="data-header-actions">
    <li>
    <a target="_blank" href="<?php echo $this->build_url("blacklists",NULL,"updating");?>" class="btn btn-alt btn-inverse">
    Lanzar actualización</a>
    </li>
    <li>
    <a target="_self" href="javascript:reset_filters();" class="btn">
    <span class="awe-magic"></span></a>
    </li>    
    <li>
    <a target="_self" href="javascript:TfwControl.form_submit();" class="btn">
    <span class="awe-refresh"></span></a>
    </li>                    
    </ul>
</header>
            <section class="tab-content">                
                <!-- Tab #static -->
                <div class="tab-pane active" id="static">
                    <div class="pagination pagination-centered" style="margin:0">
<?php
//dfd
?>
                    </div>
<?php
$oTableBasic->show();
?>
                    <div class="pagination pagination-centered">
<?php
//echo $sButtonsHtml;
?>
                    </div>
                    
                </div>
            </section>
        </div>
    </article>

</div>
