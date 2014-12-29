<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.0
 * @name ApphelperTableCheck
 * @file apphelper_tablecheck.php
 * @date 18-04-2014 20:04 (SPAIN)
 * @observations: Tabla que ordena checks por columnas
 */
include_once("helper_table.php");
include_once("helper_table_td.php");
include_once("helper_table_tr.php");
include_once("helper_table_tr.php");
include_once("helper_check.php");

class ApphelperTableCheck  
{
    protected $sTitle;
    protected $arColsLabels;
    protected $arData;
    protected $sChecksName;
    protected $arChecked;
    protected $sSectionId;
    
    /**
     * 
     * @param type $arOptions array("id"=>,"code"=>,"description"=>...)
     * @param type $sChecksName
     */
    public function __construct($arOptions,$sChecksName) 
    {
        $this->arData = $arOptions;
        $this->sChecksName = $sChecksName;
    }
    
    public function get_html() 
    {
        $oTable = new HelperTable();
        if($this->sSectionId)
            $oTable->set_id($this->sSectionId);
        $arObjRows = array();
        
        //TITULO
        $oAuxRow = new HelperTableTr();
        $oAuxTd = new HelperTableTd();
        $arObjTds = array();

        if($this->sTitle) 
        {
            $oAuxTd->set_colspan(3);
            
            $oAuxTd->set_innerhtml($this->sTitle);
            $oAuxTd->add_style("text-align:left;background:black;color:white;font-weight:bold;padding-left:3px;height:20px;");
            
            $arObjTds[] = $oAuxTd;
            $oAuxRow->set_attr_rownumber(1);
            $oAuxRow->set_objtds($arObjTds);
            $arObjRows[] = $oAuxRow;
        }
        
        //SUBTITULO
        $oAuxRow = new HelperTableTr();
        $arObjTds = array();
        foreach($this->arColsLabels as $iCol=>$sLabel)
        {
            if($iCol>0)
            {    
                $oAuxTd = new HelperTableTd();
                if($iCol==1)
                    $oAuxTd->set_colspan(2);
                $oAuxTd->set_innerhtml($sLabel);
                $oAuxTd->add_style("padding:5px;border-bottom:1px solid;font-weight:bold;");
                if($iCol==2)
                    $oAuxTd->add_style("border-left:1px solid;");
                $arObjTds[] = $oAuxTd;
            } 
        }
        $oAuxRow->set_attr_rownumber(2);
        $oAuxRow->set_objtds($arObjTds);
        $arObjRows[] = $oAuxRow;
        
        //CHECKS
        //bug($this->arData);die;
        $iRowsCheck = 0;
        foreach($this->arData as $arOption)
        {
            $sId = $arOption["id"];
            $sCode = $arOption["code"];
            $sDescription = $arOption["description"];
            
            $oAuxRow = new HelperTableTr();
            $arObjTds = array();
            
            //CHECKBOX
            //$oCheck = new HelperCheckbox($arOptions, $name, $arValuesToCheck, $arValuesDisabled, $class, $extras, $isGrouped, $oLegend, $oFieldset)
            $oCheck = new HelperCheckbox(array($sId=>""));
            $sCheckId = $this->sChecksName."_".$iRowsCheck;
            
            $oCheck->set_id($sCheckId);
            $oCheck->set_name($this->sChecksName);
            if(in_array($sId,$this->arChecked))
                $oCheck->set_values_to_check(array($sId));
            
            $oAuxTd = new HelperTableTd();
            $oAuxTd->add_inner_object($oCheck);
            $oAuxTd->add_style("padding:4px;border-bottom:1px solid;");
            $arObjTds[] = $oAuxTd;
            
            //CODIGO
            $oAuxTd = new HelperTableTd();
            $oAuxTd->set_innerhtml($sCode);
            $oAuxTd->add_style("padding:5px;border-bottom:1px solid;");
            $arObjTds[] = $oAuxTd;

            //DESCRIPTION
            $oAuxTd = new HelperTableTd();
            $oAuxTd->set_innerhtml($sDescription);
            $oAuxTd->add_style("padding:2px;padding-left:5px;border-left:1px solid;border-bottom:1px solid;");
            $arObjTds[] = $oAuxTd;
            
            $oAuxRow->set_attr_rownumber($sId);
            $oAuxRow->set_objtds($arObjTds);
            $arObjRows[] = $oAuxRow;
            
            $iRowsCheck++;
        }//fin foreach options
        
        //bug($arObjRows);
        $oTable->add_style("border:1px solid black;border-collapse:collapse; font-size:15px;");
        $oTable->add_style("margin-top:15px;");
        $oTable->add_style("margin-bottom:15px;");
        $oTable->set_objrows($arObjRows);
        
        return $oTable->get_html();
    }
    
    public function set_sectionid($value){$this->sSectionId = $value;}
    public function set_title($value){$this->sTitle = $value;}
    public function set_cols_labels($arValue){$this->arColsLabels = $arValue;}
    public function set_data($arValue){$this->arData = $arValue;}
    public function set_checksname($value){$this->sChecksName = $value;}
    public function set_values_to_check($arValues){$this->arChecked = $arValues;}
}