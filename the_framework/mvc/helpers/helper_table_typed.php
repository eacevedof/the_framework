<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.10
 * @name HelperTableTyped
 * @date 29-10-2013 15:09 (SPAIN)
 * @file helper_table_typed.php
 * @requires:
 */
include_once("helper_table_basic.php");
include_once("helper_anchor.php");
include_once("helper_input_text.php");
class HelperTableTyped extends HelperTableBasic
{
    protected $arColumnsAnchor;
    protected $arColumnsInputText;
    
    protected $arColumnsRadio;
    
    protected $arColumnsSelect;
    //protected $arSelectOptions;
    protected $arColumnsRaw;
    protected $isColumnButtonUpdate;
    protected $isColumnButtonInsert;
    
    public function __construct($arRows,$arColumns=array(),$sFormId="frmList",$sModule="")
    {
        //1:table,innert,classes,extra,style
        //2:arDataRows,arColumns,sFormId,_idprefix,_id,sModule,sUrlDel,sUrlUp
        parent::__construct();
        $this->lower_fieldnames($arRows);
        $this->arDataRows = $arRows;
        $this->iNumRows = count($arRows);
        //bug($arRows);
        $this->arColumns = $arColumns;
        $this->iNumCols = count($arColumns);
        $this->sFormId = $sFormId;
        $this->_idprefix = "tbl";
        $this->_id = $sModule;
        $this->sMergeGlue = ",";
    }

    public function get_html()
    {
        $this->useThead = true;
        $this->useTfoot =true;
        
        $sHtmlToReturn = "";
        $oFieldset = new HelperFieldset();
        $oForm = new HelperForm($this->sFormId);
        //@TODOTEMPLATE
        $oForm->add_class("form-horizontal");
        //@TODOTEMPLATE
        $oForm->add_style("margin:0;padding:0;border:0;");
        
        $sHtmlToReturn .= $oForm->get_opentag();
        $sHtmlToReturn .= $oFieldset->get_opentag();
        //Los campos que se mostrarán antes del listado
        $sHtmlToReturn .= $this->get_fields_as_string();
        $sHtmlToReturn .= $oFieldset->get_closetag();
        //Barra de navegacion por paginas
        if($this->isPaginateBar)
            $sHtmlToReturn .= $this->build_paginate_bar();
        //crea la etiqueta table
        $sHtmlToReturn .= $this->get_opentag();
        //Filas.  Se carga en la propiedad arObjTrs de tfw_helper se utiliza el metodo tr_as_string
        //para imprimir en formato cadena el array de objetos de tipo Tr
        $this->load_array_object_tr();
        $sHtmlToReturn .= $this->get_html_rows();
        //Fin Filas
        $sHtmlToReturn .= $this->get_closetag();
        $sHtmlToReturn .= $this->build_hidden_fields();
        $sHtmlToReturn .= $oForm->get_closetag();
        //Fin formulario
        $sHtmlToReturn .= $this->build_js();
        return $sHtmlToReturn;
    }   
    
    protected function build_cell_content($arRow,$sFieldName,$iNumRow,$iNumColumn)
    {
        $sTdInner = "";
        if($iNumColumn===0)
        {    
            $sTdInner .= $this->build_hidden_rowchange($iNumRow);            
            $sTdInner .= $this->build_hidden_row($iNumRow);
            $sTdInner .= $this->build_hidden_keys($arRow,$iNumRow);
            $sTdInner .= $this->build_hidden_columns($arRow,$iNumRow);
            if($this->arHiddenColumns)
                $sTdInner .= $this->build_extra_hidden($iNumRow);            
        }
        
        switch($sFieldName)
        {
            //columna delete single
            case "delete":
                $sTdInner .= $this->build_delete_button($arRow);
            break;
            case "quarantine":
                $sTdInner .= $this->build_quarantine_button($arRow);
            break;        
            case "detail"://detail
                $sTdInner .= $this->build_detail_button($arRow);
            break;
            case "butinsert"://ejecuta js: save_new
                $sTdInner .= $this->build_new_button($iNumRow,$iNumColumn);
            break;
            case "butupdate"://ejecuta js: save_edit
                $sTdInner .= $this->build_edit_button($iNumRow,$iNumColumn);
            break;        
            case "multipick":
                $sTdInner .= $this->build_multiple_button($arRow,$iNumRow);
            break;
            case "singlepick":
                $sTdInner .= $this->build_single_button($arRow,$iNumRow);
                //bug($sTdInner,"tdinner");
            break;                 
            default:
                //Columnas de datos. Puede ser texto plano o en un control
                $sTdInner = "";
                if(in_array($sFieldName,array_keys($this->arColumnsAnchor)))
                    $sTdInner = $this->build_anchor_cell_content($arRow,$sFieldName,$iNumRow,$iNumColumn);
                elseif(in_array($sFieldName,array_keys($this->arColumnsInputText)))
                    $sTdInner = $this->build_inputtext_cell_content($arRow,$sFieldName,$iNumRow,$iNumColumn);
                elseif(in_array($sFieldName,array_keys($this->arColumnsSelect)))
                    $sTdInner = $this->build_select_cell_content($arRow,$sFieldName,$iNumRow,$iNumColumn);
                elseif(in_array($sFieldName,array_keys($this->arColumnsRaw)))
                    $sTdInner = $this->build_raw_cell_content($arRow,$sFieldName,$iNumRow,$iNumColumn);                
                else
                    $sTdInner = $this->get_fieldvalue_by_name($arRow,$sFieldName);
            break;
        }//fin switch fieldname
        return $sTdInner;
    }

    protected function get_operation_columns()
    {
        $arColumns = parent::get_operation_columns();
        if($this->isColumnButtonUpdate) $arColumns["butupdate"] = "Save";
        if($this->isColumnButtonInsert) $arColumns["butinsert"] = "New";  
        return $arColumns;
    }
    
    protected function build_anchor_cell_content($arRow,$sFieldName,$iNumRow,$iNumColumn)
    {
        $sCellPos = $iNumRow."_$iNumColumn";
        $arAnchorData = $this->get_anchor_data($arRow,$sFieldName);
        //bug($arAnchorData,"build_anchor_cell_content");
        $oAnchor = new HelperAnchor();
        
        $sHref = $arAnchorData["href"];
        $sHref = str_replace("%cellpos%","'$sCellPos'",$sHref);
        if(!$arAnchorData["external"])
            if($this->isPermaLink) 
                $sHref = "/".$sHref;
            else
                $sHref = "index.php?".$sHref;
            
        $oAnchor->set_href($sHref);
        
        $sTarget = $arAnchorData["target"];
        if(!$sTarget) $sTarget = "self";
        $oAnchor->set_target($sTarget);
        $oAnchor->add_extras("cellpos",$sCellPos);
        
        $sClass = $arAnchorData["class"];
        if($sClass) $oAnchor->add_class($sClass);
        
        $sInnerHtml = $arAnchorData["innerhtml"];
        $sClassIcon = $arAnchorData["icon"];
        //@TODOTEMPLATE
        if($sClassIcon) $sInnerHtml = "<span class=\"$sClassIcon\"></span> $sInnerHtml";
        $oAnchor->set_innerhtml($sInnerHtml);
        return $oAnchor->get_html();
    }
   
    protected function build_inputtext_cell_content($arRow,$sFieldName,$iNumRow,$iNumColumn)
    {
        $oInputText = new HelperInputText();
        $sCellPos = $iNumRow."_$iNumColumn";
        $oInputText->add_extras("cellpos",$sCellPos);
        $sCellPos = "$sFieldName"."_$iNumRow"."_$iNumColumn";
        $oInputText->set_id("txt$sCellPos");
        $oInputText->set_name("txt$sCellPos");
        
        //PROPERTIES
        $arProperties = $this->arColumnsInputText[$sFieldName];
        //bug($arProperties);
        if($arProperties["class"]) $oInputText->add_class($arProperties["class"]);
        //@TODOTEMPLATE
        else $oInputText->add_class("input-small");
        if($arProperties["onclick"]) $oInputText->set_js_onclick($arProperties["onclick"]);
        if($arProperties["onfocus"]) $oInputText->set_js_onfocus($arProperties["onfocus"]);
        if($arProperties["readonly"]) $oInputText->readonly();
        $oInputText->set_value($this->get_fieldvalue_by_name($arRow,$sFieldName));
        return $oInputText->get_html();
    }
    
    protected function build_select_cell_content($arRow,$sFieldName,$iNumRow,$iNumColumn)
    {
        $oSelect = new HelperSelect();
        //$oInputText->add_style("margin:0");
        $sCellPos = $iNumRow."_$iNumColumn";
        $oSelect->add_extras("cellpos",$sCellPos);
        $sCellPos = "$sFieldName"."_$iNumRow"."_$iNumColumn";
        $oSelect->set_id("sel$sCellPos");
        $oSelect->set_name("sel$sCellPos");
        $oSelect->set_options($this->get_select_options($sFieldName));
        $oSelect->set_value_to_select($this->get_fieldvalue_by_name($arRow,$sFieldName));
        return $oSelect->get_html();
    }    

    protected function build_raw_cell_content($arRow,$sFieldName,$iNumRow,$iNumColumn)
    {
        $mxColumn = $this->arColumnsRaw[$sFieldName];
        if(is_string($mxColumn))
            $this->replace_tagnames($mxColumn,$arRow);
        elseif(is_object($mxColumn))
        {
            if(method_exists($mxColumn,"get_html"))
            {
                $mxColumn = $mxColumn->get_html();
                $this->replace_tagnames($mxColumn,$arRow);
            }
        }
        $mxColumn = str_replace("%numrow%",$iNumRow,$mxColumn);
        $mxColumn = str_replace("%numcolum%",$iNumColumn,$mxColumn);
        //bug($mxColumn,"stringed"); bug($arRow,"row");die;
        return $mxColumn;
    }
    
    protected function build_new_button($iNumRow,$iNumColumn)
    {
        $oButton = new HelperButtonBasic();
        //$oInputText->add_style("margin:0");
        $sCellPos = "$iNumRow"."_$iNumColumn";
        $oButton->set_id("butInsert$sCellPos");
        $oButton->set_innerhtml("Save");
        $oButton->set_js_onclick("alert('new');");
        //@TODOTEMPLATE
        $oButton->add_class("btn btn-alt btn-success");
        $oButton->add_extras("cellpos",$sCellPos);
        return $oButton->get_html();
    }    
    
    protected function build_edit_button($iNumRow,$iNumColumn)
    {
        $oButton = new HelperButtonBasic();
        //$oInputText->add_style("margin:0");
        $sCellPos = "$iNumRow"."_$iNumColumn";
        $oButton->set_id("butUpdate$sCellPos");
        $oButton->set_innerhtml("Save");
        //@TODOTEMPLATE
        $oButton->add_class("btn btn-alt btn-success");
        $oButton->set_js_onclick("alert('TODO: Hi! I gonna save you');");        
        $oButton->add_extras("cellpos",$sCellPos);
        return $oButton->get_html();
    }
    
    protected function get_anchor_data($arRow,$sFieldName)
    {
        $arAnchorData = array("href"=>"#","innerhtml"=>"");
        $arConfigData = $this->arColumnsAnchor[$sFieldName];
        //Pruebo extraer un valor de la columna guardada en href
        $arAnchorData["href"] = $this->get_fieldvalue_by_name($arRow,$arConfigData["href"]);
        if(!$arAnchorData["href"]) $arAnchorData["href"]=$arConfigData["href"];
        
        //Pruebo extraer un valor de la columna guardada en href
        $arAnchorData["innerhtml"] = $this->get_fieldvalue_by_name($arRow,$arConfigData["innerhtml"]);
        if(!$arAnchorData["innerhtml"]) $arAnchorData["innerhtml"]=$arConfigData["innerhtml"];
        
        if($arConfigData["external"]) $arAnchorData["external"] = $arConfigData["external"];
        if($arConfigData["target"]) $arAnchorData["target"] = $arConfigData["target"];
        
        if($arConfigData["class"]) $arAnchorData["class"] = $arConfigData["class"];
        
        if($arConfigData["icon"]) $arAnchorData["icon"] = $arConfigData["icon"];
        return $arAnchorData;
    }//fin get_anchor_data
    
    protected function replace_tagnames(&$sValue,$arRow)
    {
        $arTagNames = array();
        //busca todas las coincidencias %value%
        preg_match_all("/%[a-z,A-Z,\_]+%/",$sValue,$arTagNames);
        $arTagNames = $arTagNames[0];
        foreach($arTagNames as $i=>$sTag)
            $arTagNames[$i] = str_replace("%","",$sTag);
        
        foreach($arTagNames as $sFieldName)
        {
            $sTmpFind = "%$sFieldName%";
            $sFieldValue = $this->get_fieldvalue_by_name($arRow,$sFieldName);
            if($sFieldName!==NULL)
                $sValue = str_replace($sTmpFind,$sFieldValue,$sValue);
        }
    }
    
    protected function get_select_options($sFieldName){return $this->arColumnsSelect[$sFieldName];}
    //**********************************
    //             SETS
    //**********************************
    /** Tanto innerhtml como href se pueden recuperar desde un campo concreto añadiendole el nombre del campo a estas claves
     * @param array $arColumns ej: array("url_lines"=>array("href"=>"value or fieldname","innerhtml"=>"value or fieldname")
     */
    public function set_column_anchor($arColumns){$this->arColumnsAnchor = $arColumns;}
    public function set_column_text($arColumns){$this->arColumnsInputText = $arColumns;}
    public function set_column_select($arColumns){$this->arColumnsSelect = $arColumns;}
    public function set_insert_button($isOn=TRUE){$this->isColumnButtonInsert = $isOn;}
    public function set_update_button($isOn=TRUE){$this->isColumnButtonUpdate = $isOn;}
    public function set_column_raw($arColumns){$this->arColumnsRaw = $arColumns;}
    //**********************************
    //             GETS
    //**********************************
    
}
