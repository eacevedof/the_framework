<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.0
 * @name AppComponentBalances
 * @file appcomponent_involved.php   
 * @date 08-08-2014 14:08 (SPAIN)
 * @observations: 
 *      
 * @requires:
 */
import_appmain("component");
class AppComponentBalances extends TheApplicationComponent
{
    
    public function __construct()
    {
    }
    
    public function get_total($sType="incomes")
    {
        $arFieldNames = array();
        $arTotals = array();
        $arSum = array();
        
        if($sType=="incomes")
        {
            //$arFieldNames = array("subtotal_cash","subtotal_creditcard","subtotal_check");
            $arFieldNames = array("txtSubtotalCash","txtSubtotalCreditcard","txtSubtotalCheck");
            $arTotals = array("subtotal"=>0,"total_in"=>0);
        
            foreach($arFieldNames as $sFieldName)
            {    
                //bug($sFieldName);
                if($this->is_inpost($sFieldName))
                    $arSum[] = (float)$this->get_post($sFieldName);
            }
            //bug($arSum);
            $arTotals["subtotal"] = array_sum($arSum);
            $arTotals["total_in"] = ($arTotals["subtotal"] - (float)$this->get_post("txtSubtotalExclude"));
        }
        //outcomes
        else
        {
            //bugp();die;
            $arFieldNames = array("txtSubtotalBusiness","txtSubtotalOwn");
            $arTotals = array("subtotal"=>0,"total_pending"=>0);
            foreach($arFieldNames as $sFieldName)
            {    
                //bug($sFieldName);
                if($this->is_inpost($sFieldName))
                    $arSum[] = (float)$this->get_post($sFieldName);
            }
            $arTotals["subtotal"] = array_sum($arSum);
            $arTotals["total_pending"] = $arTotals["subtotal"] - (float)$this->get_post("txtTotalOut");
        }
        //bug($arTotals);
        return $arTotals;
    }
    
    public function build_graphic($arData)
    {        
        $arLabelsX = array();
        $arFigures = array();
        $arObjSeries = array(); 
        
        //etiquetas en x
        foreach($arData as $i=>$arRow)
            $arLabelsX[] = $arRow["period"]; //08/2014
  
        foreach($arData as $i=>$arRow)
            $arFigures[] = $arRow["sum_in"];
          
        $arObjSeries[] = new ComponentSerie("Incomes",$arFigures);
        
        $arFigures=array();
        foreach($arData as $i=>$arRow)
            $arFigures[] = $arRow["sum_out"];
          
        $arObjSeries[] = new ComponentSerie("Outcomes",$arFigures); 
        
        $arFigures=array();
        foreach($arData as $i=>$arRow)
            $arFigures[] = $arRow["sum_total"];
        
        $arObjSeries[] = new ComponentSerie("Profits",$arFigures); 
        
        $oGraphic = new ComponentBaseline($arLabelsX, $arObjSeries);
        
        $oGraphic->set_titulo("Totals per Month");
        $oGraphic->set_subtitulo("Totals: ".$this->get_post("datInDateStart")." - ".$this->get_post("datInDateEnd"));
        $oGraphic->set_titulo_eje_y("Afl.");
        $oGraphic->set_unit(" Afl.");
                
        return $oGraphic;
    }
    
    public function get_resume($arTotals)
    {
        $fTaxBBo = 1.5;
        $sHtml = "";
        $sHtml .= "<p><br/>";
        $sHtml .= " <span><b>RESUME:</b></span><br/>";
        $sHtml .= " <b><span style=\"color:blue\">Incomes:</span></b> <span>".$arTotals["sum_in"]."</span> ";
        $sHtml .= " <b><span style=\"color:black\"> | Outcomes:</span></b> <span>".$arTotals["sum_out"]."</span> ";
        
        $sColorTotal = "#8bbc21";
        if(((float)$arTotals["sum_total"])<0)
            $sColorTotal = "red";    
        
        $sHtml .= "<b><span style=\"color:$sColorTotal;\"> | Profits:</span></b> <span style=\"color:$sColorTotal\">".$arTotals["sum_total"]."</span>";
        
        $fBBo = (float)$arTotals["sum_in"];
        $fBBo = ($fTaxBBo * $fBBo)/100;
        
        $sHtml .= "<b><span style=\"color:#B25F83\"> | Estimated BBO:</span></b> <span style=\"color:#B25F83\">".dbbo_numeric2($fBBo)."</span> ";
        $sHtml .= "</p>";        
        return $sHtml;
    }
    
}//AppComponentBalances
