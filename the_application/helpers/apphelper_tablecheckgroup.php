<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.2
 * @name ApphelperTableCheckGroup
 * @file apphelper_tablecheckgroup.php
 * @date 19-04-2014 15:03 (SPAIN)
 * @observations: Tabla que ordena checks por columnas
 */

import_helper("table,table_td,table_tr,check");
class ApphelperTableCheckGroup  
{
    protected $sTitle;
    protected $sLabel;
    protected $iChecksPerTr = 4;
    
    protected $arData;
    protected $sChecksName;
    protected $arChecked;
    protected $sSectionId;
    
    /**
     * 
     * @param type $arOptions array("id"=>"description".)
     * @param type $sChecksName
     */
    public function __construct($arOptions,$sChecksName) 
    {
        $this->arData = $arOptions;
        $this->sChecksName = $sChecksName;
    }
    
    public function get_html() 
    {
        $oTableMain = new HelperTable();
        if($this->sSectionId)
            $oTableMain->set_id($this->sSectionId);
        $arObjRows = array();
        
        //TITULO
        $oAuxRow = new HelperTableTr();
        $oAuxTd = new HelperTableTd();
        $arObjTds = array();

        if($this->sTitle) 
        {
            $oAuxTd->set_colspan(2);
            $oAuxTd->set_innerhtml($this->sTitle);
            $oAuxTd->add_style("text-align:left;background:black;color:white;font-weight:bold;padding-left:3px;height:20px;");
            $arObjTds[] = $oAuxTd;
            
            $oAuxRow->set_attr_rownumber(1);
            $oAuxRow->set_objtds($arObjTds);
            $arObjRows[] = $oAuxRow;
        }


        //ETIQUETA IZQUIERDA
        $oAuxRow = new HelperTableTr();
        $arObjTds = array();
        $oAuxTd = new HelperTableTd();
        if($this->sLabel) 
        {
            $oAuxTd->set_innerhtml($this->sLabel);
            $oAuxTd->add_style("padding:5px;border-bottom:1px solid;font-weight:bold;");
            $arObjTds[] = $oAuxTd;
        }        

        //CHECKS DERECHA
        $arObjTds[] = $this->get_checks_built();
        
        $oAuxRow->set_attr_rownumber(2);
        $oAuxRow->set_objtds($arObjTds);
        $arObjRows[] = $oAuxRow;

        //bug($arObjRows);
        $oTableMain->add_style("border:1px solid black;border-collapse:collapse; font-size:15px;");
        $oTableMain->add_style("margin-top:15px;");
        $oTableMain->add_style("margin-bottom:15px;");
        $oTableMain->set_objrows($arObjRows);
        
        return $oTableMain->get_html();
    }
    
    protected function get_checks_built()
    {
        //Lo que se devolvera
        $oTd = new HelperTableTd();
        $oTd->add_style("border-left:1px solid black");
        
        //Tabla embebida en el td
        $oTable = new HelperTable();
        $arObjRows = array();
        $arObjTds = array();
        
        //CHECKS
        //bug($this->arData);die;
        $oAuxRow = new HelperTableTr();
        $arObjTds = array();
        
        $iRowsCheck = 0;
        $iTdPosition = 0;
        $iTotOptions = count($this->arData);
        
        foreach($this->arData as $sId=>$sDescription)
        {
            $iTdPosition++;
            //DESCRIPTION
            $oAuxTd = new HelperTableTd();
            $oAuxTd->set_innerhtml("<p>$sDescription</p>");
            $oAuxTd->add_style("text-align:right;padding-left:5px;");
            $arObjTds[] = $oAuxTd;
            
            //CHECKBOX
            $oCheck = new HelperCheckbox(array($sId=>""));
            $sCheckId = $this->sChecksName."_".$iRowsCheck;
            $oCheck->set_id($sCheckId);
            $oCheck->set_name($this->sChecksName);
            
            //automarcado
            if(in_array($sId,$this->arChecked))
                $oCheck->set_values_to_check(array($sId));
            
            $oAuxTd = new HelperTableTd();
            $oAuxTd->add_inner_object($oCheck);
            $oAuxTd->add_style("padding:4px;padding-right:15px;");
            $arObjTds[] = $oAuxTd;
            
            if($this->is_lasttd($iTdPosition)||($iTdPosition==$iTotOptions))
            {
                $oAuxRow->set_attr_rownumber($iRowsCheck);
                $oAuxRow->set_objtds($arObjTds);
                $arObjRows[] = $oAuxRow;
                
                $oAuxRow = new HelperTableTr();
                $arObjTds = array();                
            }
            $iRowsCheck++;
        }//fin foreach options    
        
        $oTable->set_objrows($arObjRows);
        $oTd->add_inner_object($oTable);
        return $oTd;
    }
    
    protected function is_lasttd($iTdPosition){return ($iTdPosition%$this->iChecksPerTr==0);}
    
    public function set_sectionid($value){$this->sSectionId = $value;}
    public function set_title($value){$this->sTitle = $value;}
    public function set_label($value){$this->sLabel=$value;}
    public function set_data($arValue){$this->arData = $arValue;}
    public function set_checksname($value){$this->sChecksName = $value;}
    public function set_checks_per_row($iValue){$this->iChecksPerTr=$iValue;}
    public function set_values_to_check($arValues){$this->arChecked = $arValues;}
}