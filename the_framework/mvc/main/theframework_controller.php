<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.3.1
 * @name TheFrameworkController
 * @file theframework_controller.php 
 * @date 24-10-2014 20:47 (SPAIN)
 * @observations: core library.
 * @requires: component_permission.php
 */
import_component("permission");
class TheFrameworkController extends TheFramework
{ 
    /**
     * @var ComponentPermission
     */
    protected $oPermission;    
    /**
     * @var TheApplicationView 
     */
    protected $oView;
    //Name in table base_module
    protected $sModuleName;

    protected $arFilters;
    //Que se realizará despues de CREATE, UPDATE, DELETE
    protected $arAfterSuccessCUD; 
    protected $sCurrentOperation;

    protected $iItemsPerPage;
    protected $sTrLabelPrefix;
    
    public function __construct()
    {
        //clientbrowser,ismovildevice,isconsolecalled,ispermalink
        parent::__construct();
        //creates post[selpage] = get[page]
        //esta linea me crea el array post lo que evita que se recargue desde sesion los filtros
        //al cambiar de módulo
        //$this->set_post_get_page();        
        $this->oSessionUser = $this->get_session("oSessionUser");
        //$this->arFilters = array("field_name"=>array("controlid"=>"","searchconfig"=>array("operator"=>"like","value"=>"get_value")));
        $this->arFilters = array();
        //bug($this->oPermission);
        $this->arAfterSuccessCUD["insert"] = "insert";
        $this->arAfterSuccessCUD["update"] = "update";
        $this->arAfterSuccessCUD["quarantine"] = "get_list";
        $this->arAfterSuccessCUD["delete"] = "get_list";
        $this->sCurrentOperation = $this->get_get("view");
        $this->iItemsPerPage = 45;
    }
    
    /**CUD: CREATE: insert, UPDATE: update, quarantine DELETE: delete
     * Utiliza $this->sCurrentOperation = $this->get_get("view");
     * Utiliza variables $this->sCurrentOperation["operation"]="view_to_go";
     * @param array|string in csv $mxGetParamExclude Params not to use in redirection
     */
    protected function go_to_after_succes_cud($mxGetParamExclude=NULL)
    {
        //TODO Falta hacerlo compatible con mvc
        if(is_string($mxGetParamExclude)) 
            $mxGetParamExclude = explode(",",$mxGetParamExclude);
        
        if($mxGetParamExclude==NULL) 
            $mxGetParamExclude = array();
        
        $arExclude = array("controller","partial","method");
        $arExclude = array_merge($arExclude,$mxGetParamExclude);
        //bug($arExclude);DIE;
        $arParams = array();
       
        $sGoView = $this->arAfterSuccessCUD[$this->sCurrentOperation];
        if($this->isPermaLink)
        {
            foreach($_GET as $sKey=>$sValue)
                if(!in_array($sKey,$arExclude))
                    $arParams[$sKey] = $sValue;

            $arParams["view"] = $sGoView;
            
            //si se ha borrado y se ha configurado ir al detalle se cambia para ir al listado
            if($this->get_get("view")=="delete" && $sGoView=="update")
                $arParams["view"] = "get_list";

            //si se va a ir al listado o al formulario de inserción se quita el parametro id
            if($arParams["view"]=="get_list" || $arParams["view"]=="insert")
                unset($arParams["id"]);

            $sUrl = "/".implode("/",$arParams)."/";
            //bug($sUrl,"url after succes_cud");die;
        }
        else 
        {
            foreach($_GET as $sKey=>$sValue)
                if(!in_array($sKey,$arExclude))
                    $arParams[$sKey] = "$sKey=$sValue";

            $arParams["view"] = "view=$sGoView";

            if($this->get_get("view")=="delete" && $sGoView=="update")
                $arParams["view"] = "view=get_list";

            if($arParams["view"]=="view=get_list" || $arParams["view"]=="view=insert")
                unset($arParams["id"]);

            $sUrl = "?".implode("&",$arParams);
        }
        //bug($sUrl,"url"); die;
        $this->go_to_url($sUrl);
    }//go_to_after_succes_cud

    public function load_pagetitle()
    {
        //bugpg();
        $sMethod = $this->get_get("method");
        $sPageTitle = $this->get_trlabel("entities");        
        
        switch($sMethod)
        {
            case "get_list":
                $sPageTitle .= " - ";
                $sPageTitle .= $this->get_trlabel("tr_title_list",0);
            break;
        
            case "insert":
                $sPageTitle .= " - ";
                $sPageTitle .= $this->get_trlabel("tr_title_insert",0);                
            break;
        
            case "update":
                $sPageTitle .= " - ";
                $sPageTitle .= $this->get_trlabel("tr_title_update",0);                
            break;
        
            default:             
            break;
        }
        $this->oView->set_page_title($sPageTitle);
    }
    
    //**********************************
    //             SETS
    //**********************************
    protected function reset_filter(){$this->arFilters=array();}
    
    protected function set_filter($sFieldName,$sControlId,$arSearchConfig)
    {
        $arTemp = array();
        if($sControlId) $arTemp["controlid"] = $sControlId;
        if($arSearchConfig) $arTemp["searchconfig"] = $arSearchConfig;
        $this->arFilters[$sFieldName] = $arTemp;
    }
    
    protected function set_filter_value($sFieldName,$sFieldValue)
    {
        $this->arFilters[$sFieldName]["searchconfig"]["value"] = $sFieldValue;
    }
    
    protected function remove_filter($sFieldName){unset($this->arFilters[$sFieldName]);}
    
    /**
     * @param string $value el prefijo de traduccion <tr_mod_>
     */
    protected function set_trlabel_prefix($value){$this->sTrLabelPrefix = $value;}

    //**********************************
    //             GETS
    //********************************** 
    protected function get_filter_fieldnames(){return array_keys($this->arFilters);}
    protected function get_filter_controls_id()
    {
        $arIds = array();
        foreach($this->arFilters as $arFilter)
        {
            $sId = $arFilter["controlid"];
            if($sId) $arIds[] = $sId;
        }
        return $arIds;
    }
    
    /**
     * Transforma un array de configuracion de formato de campo en otro 
     * array que es entendido por el modelo para aplicar filtros
     * @param array $arFormats array(fieldname=>format) 
     * @param array $arMapping array(fieldname=>fieldnamereplace) Para remplazo 
     * @return array i=>array(fieldname=>array(operator=>op,value=>val))
     */
    protected function get_filter_searchconfig($arFormats=array())
    {
        //bug($this->arFilters);
        $arSearchConfig = array();
        foreach($this->arFilters as $sFieldName => $arFilter)
        {
            //bug($arFilter);
            if($arFilter["searchconfig"])
            {    
                if($arFormats)
                {
                    $sValue = $arFilter["searchconfig"]["value"];
                    $arFilter["searchconfig"]["value"] = $this->format_value($arFormats,$sFieldName,$sValue);
                }
                $arSearchConfig[$sFieldName] = $arFilter["searchconfig"];
            }
        }
        //bug($arSearchConfig);die; 
        //return array tipo: (fieldname=>array(operator=>op,value=>val))
        return $arSearchConfig;        
    }
    
    protected function get_trlabel($sName,$usePrefix=TRUE)
    {
        $sTrLabel = $sName;
        if($usePrefix)
            $sTrLabel = $this->sTrLabelPrefix.$sName;
        
        $sTrLabel = get_tr($sTrLabel);
        //$sTrLabel = utf8_decode($sTrLabel);
        return $sTrLabel;
    }    
    
    protected function get_file_content($sPathFile)
    { 
        if(is_file($sPathFile))
        { 
            $oReader = fopen($sPathFile,"r");
            $iFileSize = filesize($sPathFile);
            //pr($iFileSize);
            $sContent = fread($oReader,$iFileSize);
            fclose($oReader);
        }
        else 
        {    
            $sMessage = "get_file_content: File: $sPathFile not a file";
            $this->add_error($sMessage);
        }
        return $sContent;
    }      
    //**********************************
    // OVERRIDE TO PUBLIC IF NECESSARY
    //**********************************

}//end theframework_controller
