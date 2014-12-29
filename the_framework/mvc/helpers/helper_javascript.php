<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.5
 * @name HelperJavascript
 * @file helper_javascript.php
 * @date 29-10-2014 21:58 (SPAIN)
 * @observations core library
 */
class HelperJavascript extends TheFrameworkHelper
{
    protected $sSubpathPlugin;
    protected $sSubpathJs;
    protected $sSubpathExt;
    protected $sSaveAction;
    protected $sFocusId;
    protected $arFilters;
    protected $sFormId;
    
    protected $arPathfiles = array("type"=>"custom|jquery","filename");
    protected $arJsLines = array();
    
    protected $arValidate = array();
    protected $arTmpJs;
    
    public function __construct($sSubPathExt="", $arSubPathFiles=array())
    {
        $this->arPathfiles = $arSubPathFiles;
        $this->sSubpathJs = TFW_SUBPATH_JSDS;
        $this->sSubpathPlugin = TFW_SUBPATH_PLUGINDS;
        $this->sSubpathExt = $sSubPathExt;
        $this->sSaveAction = "insert";
        $this->sFocusId = "frmList";
        $this->sFormId = "frmList";
    }
    
    private function add_filesrc($sFileName,$sType,$sPath)
    {
        if(!strpos($sFileName,".js")) $sFileName .= ".js";
        if(!empty($sType)) $sPath .= "$sType/";
        $sPath .= $sFileName;
        if(!in_array($sPath, $this->arPathfiles))
            $this->arPathfiles[] = $sPath; 
    }
    
    private function html_lines_between_tags()
    {
        $sLines = "";
        foreach($this->arJsLines as $sLine)
            $sLines .= $sLine ."\n";
        return $sLines;
    }
    
    /**
     * Devuelve inner_html si existiera unido a las lineas añadidas 
     * al array de lineas con add_js_line() formando todos es to "any content"
     * @return string tipo <script..>...any content ...</script>
     */
    public function get_html()
    {
        $sJs = $this->get_opentag();
        if($this->_inner_html) $sJs.=$this->_inner_html;
        $sJs .= $this->html_lines_between_tags();
        $sJs .= $this->get_closetag();
        return $sJs;
    }
    
    public function show_tag_links(){echo $this->get_html_tag_links();}
            
    //**********************************
    //             SETS
    //**********************************
    public function add_tfw_filesrc($sFileName,$sType="custom"){$this->add_filesrc($sFileName,$sType,$this->sSubpathJs);}
    //public function set_path_files($arPaths){$this->arPathfiles = $arPaths;}
    public function add_js_line($sScriptLine){$this->arJsLines[] = $sScriptLine;}
    public function add_path_file($sFilePath){$this->arPathfiles[] = $sFilePath;}
    public function add_plug_filesrc($sFolderName,$sFileName)
    {
        $sPath = $this->sSubpathPlugin.$sFolderName."/js/";
        $this->add_filesrc($sFileName,null,$sPath); 
    }
    
    public function add_ext_filesrc($sFileName,$sSubPath="")
    {
         if(empty($this->sSubpathExt))
            if(empty($sSubPath))$sSubPath = "js/";
        else
            $sSubPath = $this->sSubpathExt;
        $this->add_filesrc($sFileName,null,$sSubPath);
    }
    
    public function set_validate_config($arValidate){$this->arValidate=$arValidate;}
    public function set_updateaction($sAction="update"){$this->sSaveAction=$sAction;}
    
    //**********************************
    //             GETS
    //**********************************
    public function get_opentag($attrib=""){return "<script type=\"text/javascript\"$attrib>\n";}
    public function get_closetag(){return "</script>\n";}
    
    /**
     * devuelve un tag script con atributo src
     * @param string $sSrcPath
     * @return string <script type=\"text/javascript\" src=\"$sSrcPath\"></script>
     */
    private function get_html_tag_script_link($sSrcPath)
    {
        $sScriptTag = "";
        if($sSrcPath) $sScriptTag = "<script type=\"text/javascript\" src=\"$sSrcPath\"></script>\n";
        return $sScriptTag;
    }
    
    /**
     * Para poder cargar estos scripts se debe de añadir las rutas con addfile
     * Devuelve varios tags de tipo script src
     * @return string de tipo <script src=...></script> <script ...></script>
     */
    public function get_html_tag_links()
    {
        $sLinks = "";
        foreach($this->arPathfiles as $sSrcPath)
            $sLinks .= $this->get_html_tag_script_link($sSrcPath);
        return $sLinks;
    }
    
    public function check_before_save($arValidate=array(),$sFormId="frmInsert")
    {
        if(!$arValidate)$arValidate = $this->arValidate;
        //$arFieldsConfig["first_name"] = array("label"=>tr_first_name,"length"=>100,"type"=>array("required"),"id"=>"");
        //var oTmpField = null;//TfwField(sId,iLen,sLabel,arValidate)
        //consturir esto: arObjFields
        //TfwFieldValidate.checkfields($arFieldsConfig);
        $arJsObj = array();
        $arJsObj[] = "var arObjFields = [];";
        foreach($arValidate as $arData)
        {
            $sControlId = $arData["id"];
            if(!$sControlId) $sControlId = $arData["controlid"];
            $iLen = $arData["length"];
            $sLabel = $arData["label"];
            //arObjFields.push(new TfwField('',100,'Last Name',[''required'']));
            //si se ha definido una longitud se valida por este dato
            if($iLen) $arData["type"][]="length";
            $sArValidate = $this->get_string_array($arData["type"]);
            $sJsObj = "new TfwField('$sControlId',$iLen,'$sLabel',$sArValidate)";
            $arJsObj[] = "arObjFields.push($sJsObj);";
        }
        if($this->sSaveAction=="update") $sFormId="frmUpdate";
        
        
        $arJsObj[] = 
        "function $this->sSaveAction()
         {
            var sFormId = \"$sFormId\";
            var oForm = document.getElementById(sFormId);
            if(oForm)
            {
                var objError = TfwFieldValidate.checkfields(arObjFields);
                if(objError.message==\"\")
                {
                    var oHidAction = document.getElementById(\"hidAction\");
                    if(oHidAction)oHidAction.value=\"$this->sSaveAction\";
                    oForm.submit();
                }
                else
                    alert(objError.message);
            }
         }
        ";
        return $this->get_opentag(" helper=\"javascript\"").implode("\n",$arJsObj).$this->get_closetag();
    }
    
    public function get_string_array($arValues)
    {
        $arVals = array();
        foreach($arValues as $sValue)
        {
            if(is_array($sValue)) 
            {    
                $sValue = $this->get_string_array($sValue);
                $isArray=true;
            }
            else
                $sValue = str_replace("'","\'",$sValue);
            $arVals[] = $sValue;
        }
        
        if($isArray)  return "[".implode(",",$arVals)."]";
        return "['".implode("','",$arVals)."']";
    }
    
    protected function get_parent_url()
    {
        $arUrl = array();
        if($this->isPermaLink)
        {
            $sParam = $this->get_get("module");
            if($sParam)$arUrl[]=$sParam;
            $sParam = $this->get_get("section");
            if($sParam)$arUrl[]=$sParam;
            $sParam = $this->get_get("view");
            if($sParam)$arUrl[]=$sParam;
            return implode("/",$arUrl);    
        }
        else
        {
            $sParam = $this->get_get("module");
            //$sParam = $_GET["module"];
            if($sParam)$arUrl[]="parentmodule=$sParam";
            $sParam = $this->get_get("section");
            if($sParam)$arUrl[]="parentsection=$sParam";
            $sParam = $this->get_get("view");
            if($sParam)$arUrl[]="parentview=$sParam";
            return implode("&",$arUrl);            
        }
    }
    
    protected function convert_to_urlparams($arForParams)
    {
        $arUrl = array();
        foreach($arForParams as $arParams)
            foreach($arParams as $sFieldName=>$sValue)
                $arUrl[] = "$sFieldName=$sValue";
        return implode("&",$arUrl);
    }
    
    public function array_to_json($array)
    {
        //bug($array,"array");
        //'{"result":true,"count":1}',
        $arPairs = array();
        foreach($array as $key=>$mxValue)
        { 
            if(is_array($mxValue))
            {
                $mxValue = $this->array_to_json($mxValue);
                $arPairs[] = "\"$key\":$mxValue";
            }else
                $arPairs[] = "\"$key\":\"$mxValue\"";
        }
        $sJson = implode(",",$arPairs);
        $sJson = "{".$sJson."}";
        //bug($sJson);
        return $sJson;
    }

    protected function get_return_multiurl()
    {
        $arUrl = array();
        if($this->isPermaLink)
        {
            $sParam = $this->get_get("returnmodule");
            if($sParam)$arUrl[]=$sParam;
            $sParam = $this->get_get("returnsection");
            if($sParam)$arUrl[]=$sParam;
            $sParam = $this->get_get("returnview");
            if($sParam)$arUrl[]=$sParam;

            $sParam = $this->get_get("parentmodule");
            if($sParam)$arUrl[]=$sParam;
            $sParam = $this->get_get("parentsection");
            if($sParam)$arUrl[]=$sParam;
            $sParam = $this->get_get("parentview");
            if($sParam)$arUrl[]=$sParam;

            $sParam = (int)$this->get_get("close"); 
            if($sParam)$arUrl[]=$sParam;        
            return implode("/",$arUrl);            
        }
        else
        {
            $sParam = $this->get_get("returnmodule");
            if($sParam)$arUrl[]="module=$sParam";
            $sParam = $this->get_get("returnsection");
            if($sParam)$arUrl[]="section=$sParam";
            $sParam = $this->get_get("returnview");
            if($sParam)$arUrl[]="view=$sParam";

            $sParam = $this->get_get("parentmodule");
            if($sParam)$arUrl[]="parentmodule=$sParam";
            $sParam = $this->get_get("parentsection");
            if($sParam)$arUrl[]="parentsection=$sParam";
            $sParam = $this->get_get("parentview");
            if($sParam)$arUrl[]="parentview=$sParam";

            $sParam = (int)$this->get_get("close"); 
            $arUrl[]="close=$sParam";        
            return implode("&",$arUrl);            
        }
    }
    
    public function fn_multiassign_window()
    {
        $sParentUrl = $this->get_parent_url();
        $this->set_tmpjs();
        
        if($this->isPermaLink)
        {
            $this->add_tmpjs("if(sUrl) sUrlAction += \"/\"+sUrl;");
            $this->add_tmpjs("if(doClose) sUrlAction += \"/1\";");
            $this->add_tmpjs("else sUrlAction += \"/0\";");
            $this->add_tmpjs("window.open(\"/\"+sUrlAction,\"multiassign\",\"width=\"+iW+\",height=\"+iH,status=0,scrollbars=1,resizable=0,left=0,top=0);");
        }
        else
        {
            $this->add_tmpjs("if(sUrl) sUrlAction += \"&\"+sUrl;");
            $this->add_tmpjs("if(doClose) sUrlAction += \"&close=1\";");
            $this->add_tmpjs("else sUrlAction += \"&close=0\";");
            $this->add_tmpjs("window.open(\"index.php?\"+sUrlAction,\"multiassign\",\"width=\"+iW+\",height=\"+iH,status=0,scrollbars=1,resizable=0,left=0,top=0);");
        }
        
        $sTmpJs = $this->get_tmpjs();        
        
        $sJs = $this->get_opentag();
        $sJs .= "
        //helper_javascript
        //urlget: module,section,view,returnmodule,returnsection,returnview,extra1,extran..
        function multiassign_window(sUrl,doClose,iW,iH)
        {
            var iW = iW||800;
            var iH = iH||600;
            var sUrlAction = \"$sParentUrl\";
            $sTmpJs
        }
        ";
        $sJs .= $this->get_closetag();
        return $sJs;
    }

    public function fn_singleassign_window()
    {
        $sParentUrl = $this->get_parent_url();
        $this->set_tmpjs();
        
        if($this->isPermaLink)
        {
            $this->add_tmpjs("if(sUrl) sUrlAction += \"/\"+sUrl;");
            $this->add_tmpjs("if(doClose) sUrlAction += \"/1\";");
            $this->add_tmpjs("else sUrlAction += \"/0\";");
            $this->add_tmpjs("window.open(\"/\"+sUrlAction,\"multiassign\",\"width=\"+iW+\",height=\"+iH,status=0,scrollbars=1,resizable=0,left=0,top=0);");
        }
        else
        {
            $this->add_tmpjs("if(sUrl) sUrlAction += \"&\"+sUrl;");
            $this->add_tmpjs("if(doClose) sUrlAction += \"&close=1\";");
            $this->add_tmpjs("else sUrlAction += \"&close=0\";");
            $this->add_tmpjs("window.open(\"index.php?\"+sUrlAction,\"multiassign\",\"width=\"+iW+\",height=\"+iH,status=0,scrollbars=1,resizable=0,left=0,top=0);");
        }
        
        $sTmpJs = $this->get_tmpjs();  
        
        $sJs = $this->get_opentag();
        $sJs .= "
        //helper_javascript.fn_singleassign_window
        //urlget: module,section,view,returnmodule,returnsection,returnview,extra1,extran..
        function singleassign_window(sUrl,doClose,iW,iH)
        {
            var iW = iW||800;
            var iH = iH||600;
            var sUrlAction = \"$sParentUrl\";
            $sTmpJs 
        }
        ";
        $sJs .= $this->get_closetag();
        return $sJs;
    }
    
    public function fn_singleadd()
    {
        $sJs = $this->get_opentag();
        $sJs .= "
        //helper javascript
        function singleadd(sIdReturnKey,sIdReturnDesc,sKeyValue,sDescValue)
        {
            var eParentWindow = top.opener;
            //opener: padre //top: popup  //top.opener el padre del popup
            if(eParentWindow)
            {
                var eInput = eParentWindow.document.getElementById(sIdReturnKey);
                if(!eInput) eInput = eParentWindow.document.getElementsByName(sIdReturnKey)[0];
                eInput.value = sKeyValue;
                eInput = eParentWindow.document.getElementById(sIdReturnDesc);
                if(!eInput) eInput = eParentWindow.document.getElementsByName(sIdReturnDesc)[0];
                eInput.value = sDescValue;
                if(doClose) self.close();
            }
        }
        ";
        $sJs .= $this->get_closetag();
        return $sJs;        
    }
    
    protected function build_js_field_ids()
    {
        $arIds = array_values($this->arFilters);
        $sFieldIds = implode("\",\"",$arIds);
        $sFieldIds = "\"$sFieldIds\"";
        return $sFieldIds;
    }    
    
    public function fn_postback()
    {
        $sJs = $this->get_opentag(" helper=\"javascript->fn_postback\"");
        $sJs .= "
        function postback(oControl)
        {
            var sFormId = '$this->sFormId';
            var sUrl = window.location;
            var oForm = document.getElementById(sFormId);
            var oHidAction = document.getElementById('hidAction');
            var oHidPostback = document.getElementById('hidPostback');
            if(oForm) 
            {
                oHidAction.value = 'postback';
                if(oControl && oHidPostback)
                    oHidPostback.value = oControl.id;
                    
                oForm.action = sUrl;
                oForm.submit();
            }
            else
                bug('form id not defined - fn_postback');
        }
        ";
        $sJs .= $this->get_closetag();
        return $sJs;        
    }
    
    public function fn_resetfilters()
    {
        $sFieldIds = $this->build_js_field_ids();
        $sJs = $this->get_opentag(" helper=\"javascript\"");
        $sJs .= "
        function reset_filters()
         {
            var arFields = [$sFieldIds];
            TfwFieldValidate.reset(arFields);
         }
        ";
        $sJs .= $this->get_closetag();
        return $sJs;        
    }
    
    public function fn_multiadd()
    {
        $sParentUrl = $this->get_return_multiurl();
        $this->set_tmpjs();
        
        if($this->isPermaLink)
        {
            $this->add_tmpjs("var sUrlAction = \"/$sParentUrl\";");
            $this->add_tmpjs("if(sUrl) sUrlAction += \"/\"+sUrl;");
        }
        else
        {
            $this->add_tmpjs("var sUrlAction = \"?$sParentUrl\";");
            $this->add_tmpjs("if(sUrl) sUrlAction += \"&\"+sUrl;");
        }
        
        $sTmpJs = $this->get_tmpjs();        
        
        $sJs = $this->get_opentag();
        $sJs .= "
        //helper javascript
        //url: params extras
        function multiadd(sUrl,sIdForm)
        {
            var sIdForm = sIdForm || \"frmList\"; 
            $sTmpJs
                
            var oForm = document.getElementById(sIdForm);
            if(oForm)
            {
                if(is_checked(\"pkeys[]\"))
                {    
                    if(confirm(\"Are you sure to commit this operation?\"))
                    {
                        oForm.action=sUrlAction;
                        oForm.submit();
                    }
                }
                else
                    alert(\"No row selected\");
            }
        }
        ";
        $sJs .= $this->get_closetag();
        return $sJs;        
    }    

    public function fn_onenter_insert()
    {
        $sJs = $this->get_opentag(" helper=\"javascript\"");
        $sJs .= "
        function onenter_insert(oEvent)
        {
            var iKeyPressed = 0;
            //var event = this.event;
            if(window.event) iKeyPressed = window.event.keyCode;
            else if(oEvent) iKeyPressed = oEvent.which;
            else return true;

            if(iKeyPressed == 13)
            {
                insert(); 
                return false;
            }
            else
               return true;
        }
        ";
        $sJs .= $this->get_closetag();
        return $sJs;        
    }

    public function fn_onenter_update()
    {
        $sJs = $this->get_opentag(" helper=\"javascript\"");
        $sJs .= "
        function onenter_update(oEvent)
        {
            var iKeyPressed = 0;
            //var event = this.event;
            if(window.event) iKeyPressed = window.event.keyCode;
            else if(oEvent) iKeyPressed = oEvent.which;
            else return true;

            if(iKeyPressed == 13)
            {
                update(); 
                return false;
            }
            else
               return true;
        }
        ";
        $sJs .= $this->get_closetag();
        return $sJs;        
    }
    
    public function fn_onenter_submit()
    {
        $sJs = $this->get_opentag(" helper=\"javascript\"");
        $sJs .= "
        function onenter_submit(oEvent)
        {
            var sFormId = '$this->sFormId';
            var iKeyPressed = 0;
            //var event = this.event;
            if(window.event) iKeyPressed = window.event.keyCode;
            else if(oEvent) iKeyPressed = oEvent.which;
            else return true;

            if(iKeyPressed == 13)
            {
                var sUrl = window.location;
                var oForm = document.getElementById(sFormId);
                var oHidAction = document.getElementById('hidAction');
                if(oForm) 
                {
                    if(oHidAction) oHidAction.value = 'enter_submit';
                    oForm.action = sUrl;
                    oForm.submit();
                }
                else
                    bug('form id not defined - fn_onenter_submit');
             }
        }
        ";
        $sJs .= $this->get_closetag();
        return $sJs;        
    }
    
    public function fn_closeme()
    {
        $sJs = $this->get_opentag();
        $sJs .= "
        function closeme(){self.close();}
        ";
        $sJs .= $this->get_closetag();
        return $sJs;        
    }    
    
    public function set_focusid($value){$this->sFocusId = $value;}
    public function set_filters($arFields){$this->arFilters=$arFields;}
    public function set_formid($value){$this->sFormId = $value;}
    
    public function process_focus()
    {
        $sJs = $this->get_opentag(" helper=\"javascript\"");
        $sJs .= 
        "
            var oElement = document.getElementById(\"$this->sFocusId\");
            if(!oElement) oElement = document.getElementsByName(\"$this->sFocusId\")[0];
            if(oElement) oElement.focus();
        ";
        $sJs .= $this->get_closetag();
        return $sJs;
    }
    
    protected function set_tmpjs($mxValues=NULL)
    {
        $this->arTmpJs=array();
        if(is_array($mxValues))
            $this->arTmpJs = $mxValues;
        elseif($mxValues)
            $this->arTmpJs[] = $mxValues;
    }
    
    protected function add_tmpjs($sJsString)
    {
        if($sJsString!==NULL)
            $this->arTmpJs[] = $sJsString;
    }    
    
    protected function get_tmpjs($cGlue="\n"){return implode($cGlue,$this->arTmpJs);}
    //**********************************
    //           MAKE PUBLIC
    //**********************************
    public function show_opentag(){echo $this->get_opentag();}
    public function show_closetag(){echo $this->get_closetag();}
    public function show_fn_multiassign(){echo $this->fn_multiassign_window();}
    public function show_fn_singleassign_window(){echo $this->fn_singleassign_window();}
    public function show_fn_multiadd(){echo $this->fn_multiadd();}
    public function show_fn_singleadd(){echo $this->fn_singleadd();}
    public function show_fn_closeme(){echo $this->fn_closeme();}
    public function show_fn_resetfilters(){echo $this->fn_resetfilters();}
    public function show_fn_setfocus(){echo $this->process_focus();}
    public function show_check_before_save($arValidate=array(),$sFormId="frmInsert"){echo $this->check_before_save($arValidate,$sFormId);}
    public function show_fn_postback(){echo $this->fn_postback();}
    public function show_fn_enterinsert(){echo $this->fn_onenter_insert(event);}
    public function show_fn_enterupdate(){echo $this->fn_onenter_update(event);}
    public function show_fn_entersubmit(){echo $this->fn_onenter_submit(event);}
 }