<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.3
 * @name PartialProducts
 * @file partial_products.php 
 * @date 04-10-2014 18:01 (SPAIN)
 * @observations: KUZETA - Añadir a lineas de pedidos
 */
import_appcontroller("ajax");
import_model("product,order_head,order_line,order_promotion");
import_helper("javascript");
class PartialProducts extends ControllerAjax
{

    public function __construct()
    {
        //crea oLog, oView, oSession, sClientBrowser y guarda en session
        parent::__construct();
        $this->set_ajax();
    }
   
    
    public function get_list()
    {
        echo "partial_products -> get_list(): :)";
    }
    
    public function addto_orderlines()
    {
        $arReturn = array("rowclass"=>"success");
        
        $oJavascript = new HelperJavascript();
        //bugpg();
        /* ["hidid_0"]=>
    string(2) "10"
    ["hidid_product_0_0"]=>
    string(2) "26"
    ["hidid_order_head_0_0"]=>
    string(2) "10"
    ["txtnum_items_0_5"]=>
    string(1) "0"
    ["txtdiscount_0_6"]=>
    string(4) "0.00"*/
        
        //DATOS FROM POST
        $iRow = $this->get_post_numrow();
        $arReturn["numrow"] = $iRow;
        $idLine = $this->get_post("hidid_$iRow");
        $idProduct = $this->get_post("hidid_product_$iRow"."_0");
        $idOrderHead = $this->get_post("hidid_order_head_$iRow"."_0");
        $iNumItems = $this->get_post("txtnum_items_$iRow"."_4");
        $iNumItems = (int)$iNumItems;

        //CARGA DATOS LINEA
        $oModelOrderLine = new ModelOrderLine();
        $oModelOrderLine->set_id($idLine);
        $oModelOrderLine->load_by_id();        
        
        //CARGA DATOS CABECERA
        $oModelOrderHead = new ModelOrderHead();
        $oModelOrderHead->set_id($idOrderHead);
        $oModelOrderHead->load_by_id();
        
        $arNotModify = array("DELIVERY"=>8,"CANCELED"=>9,"VISIT"=>7);
        //$arNotModify = array();
        if(in_array($oModelOrderHead->get_id_type_validate(),$arNotModify))
        {
            //TODO: Mensaje no puede modificar lineas para este pedido
            $iNumItems = $oModelOrderLine->get_num_items();
            $arReturn["rowclass"] = "warning";
            $arReturn["divAlert"] = array("strAlert"=>"Orderline not saved!","spnAlert"=>"Order status has changed");
        }
        //Pedido válido para modificación
        else 
        {
            $idTypePayment = $oModelOrderHead->get_id_type_payment();
            $oModelOrderPayment = new ModelOrderPromotion();
            $oModelOrderPayment->set_id_type_payment($idTypePayment);
            $oModelOrderPayment->load_by_id_payment();
            //$this->log_custom($oModelOrderPayment);
            
            //CARGA PRODUCTO INSERTADO EN LA LINEA
            $oModelProduct = new ModelProduct();
            $oModelProduct->set_id($idProduct);
            $oModelProduct->load_by_id();
            $fProductPrice = $oModelProduct->get_price_custom();
            if($oModelOrderHead->get_id_type_payment()=="4" //COD
                    || $oModelOrderHead->get_id_type_payment()=="5" )
                $fProductPrice = $oModelProduct->get_price_wholesale();
            
            $fLineAmount = (float)$fProductPrice *(int)$iNumItems;
            //$this->log_custom("product price: $fProductPrice, numitems: $iNumItems, lineamount:$fLineAmount");
            
            if($oModelOrderLine->is_free())
            {
                $fLineAmount = 0.00;
                //comprobar que la cantidad sea la idonea
                $iFreeItems = $oModelOrderLine->get_free_items();
                $iSoldItems = $oModelOrderLine->get_sold_items();
                //cada cuantas unidades se regala
                $iIntervalUnits = $oModelOrderPayment->get_units_for_free();
                //las unidades de regalo por unidades 
                $iFreePerInterval = $oModelOrderPayment->get_units_free();
                //Lo que corresponde a los items vendidos
                $iFreePerSolditems = floor($iSoldItems/$iIntervalUnits) * $iFreePerInterval;
                
                if($iFreePerSolditems<($iNumItems+$iFreeItems))
                {   
                    $iNumItems = $oModelOrderLine->get_num_items();
                    $arReturn["divAlert"] = array("strAlert"=>"Line not saved!","spnAlert"=>"Max free units:$iFreePerSolditems");
                    $arReturn["rowclass"] = "error";
                }
                else//Regalo q correspone >= a lo insertado
                {
                    $oModelOrderLine->set_num_items($iNumItems);
                    $oModelOrderLine->set_amount($fLineAmount);
                    $oModelOrderLine->autoupdate();
                    $arReturn["divAlertSuccess"] = array("strAlertSuccess"=>"Line saved!","spnAlertSuccess"=>"Sold items: $iSoldItems | Free Items: $iFreePerSolditems");
                    $arReturn["rowclass"] = "success"; 
                }
            }
            //no es gratuito
            else 
            {
                $oModelOrderLine->set_num_items($iNumItems);
                $oModelOrderLine->set_amount($fLineAmount);
                $oModelOrderLine->autoupdate();
                
                //comprobar que la cantidad sea la idonea
                $iFreeItems = $oModelOrderLine->get_free_items();
                $iSoldItems = $oModelOrderLine->get_sold_items();
                //cada cuantas unidades se regala
                $iIntervalUnits = $oModelOrderPayment->get_units_for_free();
                //las unidades de regalo por unidades 
                $iFreePerInterval = $oModelOrderPayment->get_units_free();
                //Lo que corresponde a los items vendidos
                $iFreePerSolditems = floor($iSoldItems/$iIntervalUnits) * $iFreePerInterval;
                //$arReturn["divMessage"] = "freeitems:$iFreeItems - solditems:$iSoldItems - intervalunits:$iIntervalUnits - ifreeperinterval:$iFreePerInterval";
                $arReturn["divAlertSuccess"] = array("strAlertSuccess"=>"Line saved!","spnAlertSuccess"=>"Sold items: $iSoldItems | Free Items: $iFreePerSolditems");
                $arReturn["rowclass"] = "success";                
            }
            
            //ACTUALIZA CABECERA
            if($iSoldItems>=$oModelOrderPayment->get_min_units())
                $oModelOrderHead->set_discount($oModelOrderPayment->get_min_units_discount());
            //carga los descuentos
            $oModelOrderHead->load_amounts();
            $oModelOrderHead->autoupdate();
            $oModelOrderHead->load_by_id();
            
            //RETURN TO VIEW
            $arReturn["txtnum_items_$iRow"."_4"] = $iNumItems;
            $arReturn["txtamount_$iRow"."_5"] = float_roundstr($fLineAmount);
            $sTotal = "TOTAL: ".float_roundstr($oModelOrderHead->get_amount_total())." | Tot. Items: $iSoldItems | Free allowed: $iFreePerSolditems";
            $arReturn["order_amount_total"] = $sTotal;
        }//Fin pedido valido para modificación
        
        $sJson = $oJavascript->array_to_json($arReturn);
        //$this->log_custom($sJson);
        echo $sJson;
    }    
}
/*DivMessages
 * ["divMessage","divAlert","divAlertError","divAlertSuccess","divAlertInfo"]
 * row classes
tr.success > td {background-color: #DFF0D8!important;}
tr.error > td {background-color: #F2DEDE!important;}
tr.warning > td {background-color: #FCF8E3!important;}
tr.info > td {background-color: #C6DBE6!important;}
 */