<!-- NO BASE view_index-->
<script type="text/javascript" src="../the_framework/html/js/tfw/main/js_the_framework_ajax.js" in="nobase.view_index"></script>
<script type="text/javascript" src="../the_framework/html/js/tfw/main/js_the_framework_eftect.js" in="nobase.view_index"></script>
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
    $oJavascript->show_fn_multiassign();
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
<script type="text/javascript" in="view_index">
function row_ajax_save(sCellPosition,isPermaLink)
{
    var arFieldIds = [];
    var arPosition = sCellPosition.split("_");
    var iRow = arPosition[0];
    var isPermaLink = isPermaLink || 0;
    //var iCol = arPosition[1];
    //bug(arPosition);
    arFieldIds.push("hidRow_"+iRow+"_0");
    arFieldIds.push("hidid_"+iRow);
    arFieldIds.push("hidid_product_"+iRow+"_0");
    arFieldIds.push("hidid_order_head_"+iRow+"_0");
    arFieldIds.push("txtnum_items_"+iRow+"_4");

    var sUrl = "index.php?module=ajax&section=products&view=addto_orderlines";
    if(isPermaLink)
        sUrl = "/ajax/products/addto_orderlines";
    TfwAjax.send_input_by_post(sUrl,arFieldIds,on_ajaxresponse);
}

function set_values(arObjPairs)
{
    var mxTemp = null;
    var oControl = null;
    
    for(var sProperty in arObjPairs)
    {
        mxTemp = arObjPairs[sProperty];
        if(mxTemp)
        {
            if(TfwCore.is_object(mxTemp))
            { 
                //bug(mxTemp,"is object property: "+sProperty);
                set_values(mxTemp);
            }
            else if(TfwCore.is_string(mxTemp))
            {
                oControl = document.getElementById(sProperty);
                if(!TfwCore.is_null(oControl))
                {   
                    if(oControl.value!=undefined) 
                        oControl.value = mxTemp;
                    else if(oControl.innerHTML!=undefined)
                        oControl.innerHTML = mxTemp;
                }
            }//else 
        }//not null
    }//for arObjPairs
}

function on_ajaxresponse(sJsonResponse)
{
    var oControl = null;
    var arObjPairs = JSON.parse(sJsonResponse);
    var arMessageDivs = ["divMessage","divAlert","divAlertError","divAlertSuccess","divAlertInfo"];

    //Asigna .value o .innerHTML segun sea el caso.
    set_values(arObjPairs);

    for(var sProperty in arObjPairs)
    {
        if(TfwCore.is_inarray(sProperty,arMessageDivs))
        {
            oControl = document.getElementById(sProperty);
            if(oControl)
            {
                oControl.style.display = "block";
                TfwEffect.fadeout(6,oControl);
            }
        }
    }
    
    if(arObjPairs["numrow"])
    {
        var iNumRow = arObjPairs["numrow"];
        oControl = TfwCore.get_elements_by_attribute("rownumber",iNumRow);
        if(oControl)
            TfwEffect.blink(oControl,arObjPairs["rowclass"],10);
    }
    
    if(arObjPairs["order_amount_total"])
    {
        var oControl = TfwCore.get_elements_by_attribute("alert","strong");
        if(oControl)
            oControl.innerHTML = arObjPairs["order_amount_total"];    
    }
}
</script>
<!--/NOBASE-->