<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.2.9
 * @name TheFramework
 * @file theframework.php
 * @date 01-11-2014 21:05 (SPAIN)
 * @observations: core library.
 * @requires:
 */

class TheFramework
{
    protected $sCurrentUrl;    
    /**
     * @var ComponentFile
     */
    protected $oLog;
    /**
     * @var ComponentSession
     */    
    protected $oSession;
    /**
     * @var ModelUser
     */    
    protected $oSessionUser;

    protected $sClientBrowser;
    protected $isMovilDevice;
    protected $isConsoleCalled;

    protected $isAjax;
 
    //ERRORS HANDLERS
    protected $isError = false;
    protected $arErrorMessages = array();
  
    protected $arDebug = array();
    protected $isPermaLink = false;
    
    public function __construct()
    {
        session_start();
        //sets clientbrowser, ismovildevice
        $this->load_client_device();        
        if(defined("STDIN"))$this->isConsoleCalled=true;
        if(defined("TFW_IS_PERMALINK")) $this->isPermaLink = TFW_IS_PERMALINK;
        //bug($this->isPermaLink,"permalink");
    }

    public function js_selfclose($fSeconds=0,$sMessage="")
    {
        $fMiliSeconds = $fSeconds * 1000;
        $sJs = "<script type=\"text/javascript\">\n";
        $sJs .= "function selfclose(){self.close();}\n";
        $sJs .= "window.setTimeout(selfclose,$fMiliSeconds);\n";
        $sJs .= "</script>\n";
        if($sMessage && $fSeconds) $sJs .= "<div style=\"text-align:center;\">$sMessage</div>";
        echo $sJs;        
    }
   
    public function js_parent_refresh()
    {
        $sJs = "<script type=\"text/javascript\">\n";
        $sJs .= "function parent_refresh()
                 {
                    //el padre es el que hizo la llamada directa
                    var eParentWindow = top.opener;
                    eParentWindow.location.replace(eParentWindow.location);
                 }\n";
        $sJs .= "parent_refresh();\n";
        $sJs .= "</script>\n";
        echo $sJs;        
    }    

    public function js_colseme_and_parent_refresh($fSeconds=0,$sMessage="")
    {
        $this->js_parent_refresh();
        $this->js_selfclose($fSeconds, $sMessage);
    }
   
    public function js_go_to($sUrl,$fSeconds=0,$sMessage="")
    {
        //window.setTimeout('runMoreCode()',timeInMilliseconds);
        $fMiliSeconds = $fSeconds * 1000;
        $sJs = "<script type=\"text/javascript\">\n";
        $sJs .= "function go_to(sUrl){window.location=sUrl;}\n";
        $sJs .= "window.setTimeout(go_to,$fMiliSeconds,'$sUrl');\n";
        $sJs .= "</script>\n";
        if($sMessage && $fMiliSeconds) $sJs .= "<div style=\"text-align:center;\">$sMessage</div>";        
        echo $sJs;
    }
   
    protected function go_to_url($sUrl,$isExit=1)
    {
        header("Location:$sUrl");
        if($isExit) exit();
    }
   
    /**
     * Limpia los separadores de directorios al entendido por DIRECTORY_SEPARATOR
     * @param string $sPathSystem Ruta con cualquier tipo de separador de directorios
     * @return string Ruta con separadores de directorios validos
     */
    protected function get_fixed_syspath($sPathSystem="")
    {
        $sPathSystem = trim($sPathSystem);
        //http://websvn.eduardoaf.com/filedetails.php?repname=proy_tasks&path=%2Ftrunk%2Fproy_tasks%2Fthe_framework%2Fmvc%2Fmain%2Ftheframework_view.php&rev=293
        if($sPathSystem)
        {
            //todas las rutas se llevan a un tipo de separador de directorio
            $sPathSystem = str_replace("\\/","/",$sPathSystem);
            $sPathSystem = str_replace("/\\","/",$sPathSystem);
            $sPathSystem = str_replace("\\","/",$sPathSystem);
            $sPathSystem = str_replace("//","/",$sPathSystem);
            $sPathSystem = str_replace("\\\\","/",$sPathSystem);
            //se repmplaza el tipo de separador por el del sistema
            $sPathSystem = str_replace("/",DIRECTORY_SEPARATOR,$sPathSystem);
            $sPathSystem = str_replace("\\",DIRECTORY_SEPARATOR,$sPathSystem);
        }
        return $sPathSystem;
    }  
   
    /**
     * Genera una url segun el tipo. Si es permalink usa / 
     * sino usa &
     * @param string $sController Nombre del controlador del módulo 
     * @param string $sPartial Nombre de clase parcial
     * @param string $sMethod Metodo que dibujará los datos procesados sobre la vista
     * @param string $sExtra 
     * @return string Cadena con la url formada
     */
    protected function build_url($sController=NULL,$sPartial=NULL,$sMethod=NULL,$sExtra=NULL)
    {
        $arUrl = array();
        $sUrl = "";
        
        if(!$sController && $this->sModuleName)
            $sController = $this->sModuleName;
        
        //.htaccess y router
        if($this->isPermaLink)
        {    
            //si se está enrutando si el metodo (o la vista) es "get_list" se marca a nulo
            //para que no exista duplicidad puesto que get_list va intrinsico en /module/            
            if($sMethod=="get_list") $sMethod=NULL;
            $cGlue = "/";//separador web
            if($sController) $arUrl[] = $sController;
            if($sPartial) $arUrl[] = $sPartial;
            if($sMethod) $arUrl[] = $sMethod;            
            $sUrl .= $cGlue.implode($cGlue,$arUrl).$cGlue;
            if($sExtra) 
            {
                $arExtra = array();
                $arParams = explode("&",$sExtra);
                foreach($arParams as $sParam)
                {
                    $arParamVal = explode("=",$sParam);
                    $arExtra[] = $arParamVal[1];
                }
                $sExtra = implode($cGlue,$arExtra).$cGlue;
                $sUrl.=$sExtra;
            }
        }
        //& params url
        else 
        {
            $sUrl .= "index.php?";
            $cGlue = "&";
            if(!$sMethod) $sMethod = "get_list";
            if($sController) $arUrl[] = "module=$sController";
            if($sPartial) $arUrl[] = "section=$sPartial";
            $arUrl[] = "view=$sMethod";
            if($sExtra) $arUrl[] = $sExtra;
            $sUrl .= implode($cGlue,$arUrl);
        }
        //bugcond($sUrl,($sController=="homes"));
        //bug($sUrl,"url in build_url");
        return $sUrl;
    }
   
    protected function go_to_module($sController,$sPartial=NULL,$sMethod=NULL,$sExtra=NULL)
    {
        $sUrl = $this->build_url($sController,$sPartial,$sMethod,$sExtra);
        //bug($sUrl,"url in go_to_module");die;
        $this->go_to_url($sUrl);
    }

    //Para los parciales no van bien pq es necesario pasar el parametro del módulo padre. Por ejemplo las lineas de una 
    //cabecera de pedido. Al borrar una linea si se va al listado hay que pasar el id de la cabecera
    protected function go_to_list()
    {
        $this->go_to_module($_GET["module"],$_GET["partial"],"get_list");
    }
   
    protected function go_to_insert()
    {
        $this->go_to_module($_GET["module"],$_GET["partial"],"insert");
    }
   
    protected function go_to_update($sExtra)
    {
        $this->go_to_module($_GET["module"],$_GET["partial"],"update",$sExtra);
    }    
   
    /**
     * Rescrito en theframework_model añadiendo el nombre de tabla
     * @return string tablename_|idusuario_yyyymmdd.log
     */
    private function get_log_name()
    {
        $sLogName = "controller_";
        if($_SESSION["tfw_user_identificator"]) $sLogName .= $_SESSION["tfw_user_identificator"]."_";
        $sLogName .= date("Ymd").".log";
        return $sLogName;
    }
   
    protected function log_error($sContent)
    {
        $sAuxPathFolder = $this->oLog->get_path_folder_target();
        $sAuxFilename = $this->oLog->get_target_file_name();
        $sPathFolder = TFW_PATH_FOLDER_LOGDS."errors";
       
        $sContent = "[".date("H:i:s")."] $sContent";
        $this->oLog->set_path_folder_target($sPathFolder);
        //nombre tipo: controller_userid_yyyymmdd.log
        $this->oLog->set_filename_target($this->get_log_name());
        $this->oLog->add_content($sContent);
        //Restauro la carpeta y archivo configurado
        $this->oLog->set_path_folder_target($sAuxPathFolder);
        $this->oLog->set_filename_target($sAuxFilename);        
    }
   
    /**
     * Uses var_export $mxContent is not a string
     * @param type $mxContent
     */
    protected function log_custom($mxContent)
    {
        $sLogFilename = $this->get_log_name();
        if(!is_string($mxContent))
            $mxContent = var_export($mxContent,true);
        //guarda en custom
        $sLogFilename = str_replace(".log","",$sLogFilename);
        $this->oLog->writelog($mxContent,$sLogFilename,"trace");
    }
    
    /**
     * Escribe en nombre de archivo: session_user_<user_identificator>|_<attempt>_yyyymmdd.log
     * el contenido [hh:mm:ss] - [ip:xxx.xxx.xxx.xxx] - [goto:urlrequested] $sContent
     * @param string $sContent
     */
    protected function log_session($sContent="")
    {
        $sAuxPathFolder = $this->oLog->get_path_folder_target();
        $sAuxFilename = $this->oLog->get_target_file_name();
        $sPathFolder = TFW_PATH_FOLDER_LOGDS."session";
       
        $sRemoteIp = $this->get_remote_ip();
        $sUrl = $this->get_request_uri();
        
        $sFileName = "session_user";
        if($_SESSION["tfw_user_identificator"])
            $sFileName .= "_".$_SESSION["tfw_user_identificator"];
        else 
            $sFileName .= "_attempt";
        $sFileName.="_".date("Ymd").".log";
        $sContent = "[".date("H:i:s")."] - [ip:$sRemoteIp] - [goto:$sUrl] $sContent";
        $sContent = trim($sContent);
        
        $this->oLog->set_path_folder_target($sPathFolder);
        //nombre tipo: controller_userid_yyyymmdd.log
        $this->oLog->set_filename_target($sFileName);
        $this->oLog->add_content($sContent);
        
        //Restauro la carpeta y archivo configurado
        $this->oLog->set_path_folder_target($sAuxPathFolder);
        $this->oLog->set_filename_target($sAuxFilename);
    }    
   
    protected function get_fields_from_post($arFormats=array())
    {
        $arPrefix = "txt,pas,hid,sel,chk,rad,dat,txa";
        $arPrefix = explode(",",$arPrefix);
        $arFieldNames = array();
        foreach($arPrefix as $sPrefix)
        {
            foreach($_POST as $sPostFieldName=>$mxValue)
            {
                $sPostPrefix = $this->extract_prefix($sPostFieldName);
                if($sPrefix == $sPostPrefix)
                {
                    $sFieldName = $this->extract_fieldname($sPostFieldName);
                    $sFieldName = camel_to_sep($sFieldName);
                   
                    $mxValue = $this->format_value($arFormats,$sFieldName,$mxValue);
                    $arFieldNames[$sFieldName] = $mxValue;
                }
//                else
//                    $arFieldNames[$sPostFieldName] = $mxValue;
            }
        }
        return $arFieldNames;
    }
   
    private function extract_prefix($sFieldName){return substr($sFieldName,0,3);}
    private function extract_fieldname($sPostKey){return substr($sPostKey,3);}

    //=======================
    //        SETS
    //=======================
    /**
     * Resetea $arReference y aplica el/los valores pasados en $mxValue
     * Los falsi values se omitirán: NULL,0,"0",FALSE,""
     * @param array $arReference Array a modificar
     * @param string|csvstring|array $mxValue valor o valores a asignar 
     */
    protected function set_array(&$arReference,$mxValue)
    {
        $arReference = array(); 
        if($mxValue!==NULL)
        {
            if(is_array($mxValue)) 
                $arReference = $mxValue;
            elseif(strstr($mxValue,","))
                $arReference = explode(",",$arReference);
            elseif($mxValue)
                $arReference[] = $mxValue; 
        }
    }  
    
    protected function add_error($sMessage)
    {
        $this->isError = TRUE;
        if($sMessage)
            $this->arErrorMessages[] = $sMessage;        
    }
    protected function set_error($sMessage="")
    {
        $this->isError = FALSE;
        $this->set_array($this->arErrorMessages,$sMessage);
//        $this->arErrorMessages = array();
//        if($sMessage)
//        {   
//            $this->isError = TRUE;
//            $this->arErrorMessages[] = $sMessage;
//        }
    }
    
    protected function set_post($sKey,$mxValue){$_POST[$sKey] = $mxValue;}
    protected function set_get($sKey,$mxValue){$_GET[$sKey] = $mxValue;}

    private function load_client_device()
    {
        $this->sClientBrowser = $_SERVER["HTTP_USER_AGENT"];
        //En esta cadena podemos quitar o agregar navegadores de dispositivos
        //moviles,te recomiendo que hagas un
        //echo $_SERVER["HTTP_USER_AGENT"];
        //en otra pagina de prueba y veas la info que arroja para que
        //despues agregues el navegador que quieras detectar        
        $sWebBrowsers = "Android,AvantGo,Blackberry,Blazer,
            Cellphone,Danger,DoCoMo,EPOC,EudoraWeb,Handspring,HTC
            ,Kyocera,LG,MMEF20,MMP,MOT-V,Mot,Motorola,NetFront,Newt
            ,Nokia,Opera Mini,Palm,Palm,PalmOS,PlayStation Portable
            ,ProxiNet,Proxinet,SHARP-TQ-GX10,Samsung,Small
            ,Smartphone,SonyEricsson,SonyEricsson,Symbian
            ,SymbianOS,TS21i-10,UP.Browser,UP.Link,WAP,webOS
            ,Windows CE,hiptop,iPhone,iPod,portalmmm,Elaine/3.0,iPad;";
        $arWebBrowsers = explode(",",$sWebBrowsers);

        foreach($arWebBrowsers AS $sWebBrowser)
            //if(eregi(trim($sWebBrowser),$this->sClientBrowser))
            if(strstr($this->isMovilDevice,$sWebBrowser))
            {    
                $this->isMovilDevice = true;
                return;
            }
    }
   
    /**
     *
     * @param string $sMessage
     * @param string $sType s:success,w:warning,e:error
     */
    protected function set_session_message($sMessage,$sType="s"){$_SESSION["tfw_message"][$sType] = $sMessage;}
   
    /*NIU*/
    protected function post_to_get($sKey){$_GET[$sKey] = $_POST[$sKey];}
    /*NIU*/
    protected function get_to_post($sKey){$_POST[$sKey] = $_GET[$sKey];}  
    /*NIU*/
    protected function even_post_to_get()
    {
        $arKeys = array_keys($_POST);
        foreach($arKeys as $sKey)
            $_GET[$sKey] = $_POST[$sKey];
    }
   
    /*NIU*/
    protected function even_get_to_post()
    {
        $arKeys = array_keys($_GET);
        foreach($arKeys as $sKey)
            $_POST[$sKey] = $_GET[$sKey];
    }
   
    protected function set_post_get_page()
    {
        //bugpg();
        if($_POST["selPage"])$_GET["page"] = $_POST["selPage"];
        if($_GET["page"])$_POST["selPage"] = $_GET["page"];
        //bugpg();die;
        //bugg("page");bugp("selPage");
    }
   
    protected function clear_post(){$_POST = NULL;}
    protected function clear_get(){$_GET = NULL;}    
    protected function reset_post(){$_POST = NULL;}
    protected function reset_get(){$_GET = NULL;}
    protected function reset_session(){$_SESSION = NULL;}

    public function set_ajax($isOn=TRUE){$this->isAjax=$isOn;}
   
    protected function go_to_401($isForbidden=FALSE){if($isForbidden) $this->go_to_module("homes",NULL,"error_401");}
    protected function go_to_404($isNotExist=TRUE){if($isNotExist) $this->go_to_module("homes",NULL,"error_404");}
    protected function build_session_filterkey()
    {
        $sSessionKey = "";
        $arKeys = array("module","partial","view");
        foreach($arKeys as $sKey)
            if($this->is_inget($sKey))
                $sSessionKey .= $this->get_get($sKey);
        return $sSessionKey;
    }

    protected function set_debug($mxVar){$this->arDebug=array(); $this->arDebug[] = $mxVar;}
    protected function add_debug($mxVar){$this->arDebug[] = $mxVar;}
    protected function add_alert($sMessage){$_SESSION["tfw_message"]["a"][]=$sMessage;}
    
    //=======================
    //         GETS
    //=======================
    protected function is_error(){return $this->isError;}
   
    protected function get_error_message($isHtmlNl=0)
    {
        $sMessage = implode("\n",$this->arErrorMessages);
        if($isHtmlNl)
            $sMessage = implode("<br/>",$this->arErrorMessages);
        return $sMessage;
    }
    
    /**
     * @return boolean Indica si la accion en post es de actualizacion o insercion
     */
    protected function is_updating(){return (boolean)($_POST["hidAction"]=="update");}
    protected function is_inserting(){return (boolean)($_POST["hidAction"]=="insert");}
    protected function is_postback($sFieldName="")
    {
        if($_POST["hidAction"]=="postback")
        {
            if($sFieldName)
            {
                if($_POST["hidPostback"]==$sFieldName)
                    return TRUE;
                return FALSE;
            }
            else
                return TRUE;
        }
        return FALSE;
    }
    protected function is_action($sAction)
    {
        if($sAction)
            return (boolean)($_POST["hidAction"]==$sAction);
        else //si no se indica una accion se comprueba lo que trae hidAction
            return (boolean)$_POST["hidAction"];
    }
    protected function is_multidelete(){return (boolean)($_POST["hidAction"]=="multidelete");}
    protected function is_multiselect(){return (boolean)($_POST["hidAction"]=="multiselect");}
    protected function is_multiquarantine(){return (boolean)($_POST["hidAction"]=="multiquarantine");}
    /**
     * @param string $sKey El indice en el array $_POST
     * @return mixed el valor que se guarde en $_POST
     */    
    //protected function get_post($sKey="",$iIndex=NULL){return (($sKey=="")? $_POST : ($iIndex!==NULL)?$_POST[$sKey][$iIndex]:$_POST[$sKey]);}
    protected function get_post($sKey=NULL,$iIndex=NULL)
    {
        if($sKey===NULL) 
            return $_POST;
        elseif($iIndex!==NULL)
            return $_POST[$sKey][$iIndex];
        else
            return $_POST[$sKey];
    }
   
    /**
     * @param string $sKey El indice en el array $_GET
     * @return mixed el valor que se guarde en $_GET
     */
    protected function get_get($sKey=""){return ($sKey =="" ? $_GET : $_GET[$sKey]);}
    
    protected function is_post($sKey="")
    {
        if($sKey)
            return in_array($sKey,array_keys($_POST));
        else
            return (boolean)count($_POST);
    }
    protected function is_get($sKey="")
    {
        if($sKey)
            return in_array($sKey,array_keys($_GET));
        else
            return (boolean)count($_GET);
    }
    protected function is_inpost($mxKey){return in_array($mxKey,array_keys($_POST));}
    protected function is_inget($mxKey){return in_array($mxKey,array_keys($_GET));}
    protected function is_insession($mxKey){return in_array($mxKey,array_keys($_SESSION));}
    protected function is_insession_filter($sFilterName)
    {
        $sSessionKey = $this->build_session_filterkey();
        $arSessFilter = array_keys($_SESSION[$sSessionKey]["filters"]);
        return in_array($sFilterName,$arSessFilter);
    }
    protected function get_current_url()
    { 
        $sUrl="";
        $sPiece = $this->get_get("module");
        if($sPiece)$sUrl .= $sPiece;
        $sPiece = $this->get_get("section");
        if($sPiece)$sUrl .= $sPiece;
        $sPiece = $this->get_get("view");
        if($sPiece)$sUrl .= $sPiece;
        //bug($sUrl);die;
        return $sUrl;
    }    

    protected function get_current_section(){return ($_GET["section"])?$_GET["section"]:$_GET["partial"];}
    protected function get_current_view(){return ($_GET["view"])?$_GET["view"]:NULL;}
    protected function get_current_module(){return($_GET["controller"]?$_GET["controller"]:$_GET["module"]);}
    protected function get_var_export($mxVar){return var_export($mxVar,true);}

    protected function get_session_message($sMessage,$sType="s",$clear=1)
    {
        $sMessage = $_SESSION["tfw_message"][$sType];
        if($clear && $sMessage) unset($_SESSION["tfw_message"][$sType]);
        return $sMessage;
    }
 
    protected function get_session_filter($sFilterName)
    {
        $sSessionKey = $this->build_session_filterkey();
        return $_SESSION[$sSessionKey]["filters"][$sFilterName];
    }
   
    // ["hidOrderBy"]=> ["hidOrderType"]=>
    protected function get_orderby($sDelimiter=","){return ($_POST["hidOrderBy"]) ? explode($sDelimiter,$_POST["hidOrderBy"]) : NULL;}//hidOrderType
    protected function get_ordertype($sDelimiter=","){return ($_POST["hidOrderType"]) ? explode($sDelimiter,$_POST["hidOrderType"]) : NULL;}
    protected function get_audit_info($sInsertUser="",$sInsertDate=""
            ,$sUpdateUser="",$sUpdateDate=""
            ,$sDeleteUser="",$sDeleteDate="")
    {
        $sAuditInfo = NULL;
        
        if(class_exists("ModelUser"))
        {    
            $oModelUser = new ModelUser();
            if($sInsertUser)
            {
                $oModelUser->set_id($sInsertUser);
                $oModelUser->load_by_id();
                $sAuditInfo = tr_insert_user.$oModelUser->get_description();
                $sAuditInfo .= " - ";
            }
            //if($sCreateDate) $sAuditInfo .= tr_insert_date.crmdate_to_userdate($sCreateDate);
            if($sInsertDate) $sAuditInfo .= " ".crmdate_to_userdate($sInsertDate,12);
            //MODIFY
            if($sInsertUser || $sInsertDate) $sAuditInfo .= "&nbsp;&nbsp;<b>|</b>&nbsp;&nbsp;";
            if($sUpdateUser)
            {      
                $oModelUser->set_id($sUpdateUser);
                $oModelUser->load_by_id();
                $sAuditInfo .= tr_update_user.$oModelUser->get_description();
                $sAuditInfo .= " - ";
            }
            //if($sModifyDate) $sAuditInfo .= tr_update_date.crmdate_to_userdate($sModifyDate);
            if($sUpdateDate) $sAuditInfo .= " ".crmdate_to_userdate($sUpdateDate,12);
            //DELETE
            if($sDeleteUser || $sDeleteDate) $sAuditInfo .= "&nbsp;&nbsp;<b>|</b>&nbsp;&nbsp;";

            if($sDeleteDate)
            {  
                $oModelUser->set_id($sDeleteUser);
                $oModelUser->load_by_id();
                $sAuditInfo .= tr_delete_user.$oModelUser->get_description();
                $sAuditInfo .= " - ";
            }        
            //if($sDeleteDate) $sAuditInfo .= tr_delete_date.crmdate_to_userdate($sDeleteDate);
            if($sDeleteDate) $sAuditInfo .= " ".crmdate_to_userdate($sDeleteDate,12);
        }
        else
        {
            $sMessage = "get_audit_info(): Class ModelUser not found!";
            $this->add_error($sMessage);
        }
        return $sAuditInfo;
    }
   
    protected function get_assign_backurl($arKeys=array())
    {
        $arUrl = array();
        if($this->isPermaLink)
        {
            $sParam = $this->get_post("hidDataModule");
            if($sParam)$arUrl["mod"]=$sParam;
            $sParam = $this->get_post("hidDataSection");
            if($sParam) $arUrl["sec"]=$sParam;
            $sParam = $this->get_post("hidDataView");
            if($sParam)$arUrl["view"]=$sParam;

            $sParam = $this->get_get("parentmodule");
            if($sParam) $arUrl["pmod"]=$sParam;
            $sParam = $this->get_get("parentsection");
            if($sParam)$arUrl["psec"]=$sParam;
            $sParam = $this->get_get("parentview");
            if($sParam)$arUrl["pview"]=$sParam;

            $sParam = $this->get_get("module");
            if($sParam)$arUrl["rmod"]=$sParam;
            $sParam = $this->get_get("section");
            if($sParam)$arUrl["rsec"]=$sParam;
            $sParam = $this->get_get("view");
            if($sParam)$arUrl["rview"]=$sParam;
            $sParam = (int)$this->get_get("close");
            $arUrl["close"]=$sParam;
            
            foreach($arKeys as $sKey)
            {
                $sParam = $this->get_get($sKey);
                if($sParam) $arUrl[$sKey] = $sParam;
            }
             //if($sParam)$arUrl["keys"]="k=";this->get_get("k")."&k2=";this->get_get("k2");
            return "/".implode("/",$arUrl)."/";
        }
        else
        {
            $sParam = $this->get_post("hidDataModule");
            if($sParam)$arUrl["mod"]="module=$sParam";
            $sParam = $this->get_post("hidDataSection");
            if($sParam) $arUrl["sec"]="section=$sParam";
            $sParam = $this->get_post("hidDataView");
            if($sParam)$arUrl["view"]="view=$sParam";

            $sParam = $this->get_get("parentmodule");
            if($sParam) $arUrl["pmod"]="parentmodule=$sParam";
            $sParam = $this->get_get("parentsection");
            if($sParam)$arUrl["psec"]="parentsection=$sParam";
            $sParam = $this->get_get("parentview");
            if($sParam)$arUrl["pview"]="parentview=$sParam";

            $sParam = $this->get_get("module");
            if($sParam)$arUrl["rmod"]="returnmodule=$sParam";
            $sParam = $this->get_get("section");
            if($sParam)$arUrl["rsec"]="returnsection=$sParam";
            $sParam = $this->get_get("view");
            if($sParam)$arUrl["rview"]="returnview=$sParam";
            $sParam = (int)$this->get_get("close");
            $arUrl["close"]="close=$sParam";            
                
            foreach($arKeys as $sKey)
            {
                $sParam = $this->get_get($sKey);
                if($sParam) $arUrl[$sKey] = "$sKey=$sParam";
            }
             //if($sParam)$arUrl["keys"]="k=";this->get_get("k")."&k2=";this->get_get("k2");
            return "?".implode("&",$arUrl);
        }
    }
   
    protected function get_listkeys($sKey="")
    {
        /*        $arKeys = $this->get_post("pkeys");
        if(!$arKeys) $arKeys = $this->get_post("id");
        $arKeyFields = $this->get_post("hidKeyFields");
        $arKeyFields = explode(",",$arKeyFields);
*/
        //si son multiples
        $arKeyValue = array();
        if($_POST["pkeys"])
        {
            //$arKeysNames = explode(",",$_POST["hidKeyFields"]);//id,Code_Erp
            $arKeyFields = $_POST["pkeys"];//id=24,Code_Erp=
            foreach($arKeyFields as $i=>$sKeysValues)
            {    
                $arKeysValues = explode(",",$sKeysValues);
                foreach($arKeysValues as $sKeyValue)
                {    
                    $arKV = explode("=",$sKeyValue);
                    $arKeyValue[$i][$arKV[0]] = $arKV[1];
                }
            }
        }
        elseif($_POST["id"])
            $arKeyValue = $_POST["id"];
        elseif($_POST[$sKey])
            $arKeyValue = $_POST[$sKey];
        return $arKeyValue;
    }
   
    /**
     * Convierte valores de interface a base de datos. Lo opuesto que se hace en table_basic
     * Se rescribe en the table_basic. 
     * @param array $arFormats array(fieldname1=>format1,fieldname2=>format2...)
     * @param string $sFieldName 
     * @param string $sFieldValue
     * @return mixed 
     */
    protected function format_value($arFormats,$sFieldName,$sFieldValue)
    {
        $sValue = "";
        $sFormat = $arFormats[$sFieldName];
        
        switch($sFormat) 
        {
            case "date":
                $sValue = bodb_date($sFieldValue);
            break;
        
            case "datetime4":
                $sValue = bodb_datetime4($sFieldValue);
            break;

            case "datetime6":
                $sValue = bodb_datetime6($sFieldValue);
            break;

            case "time4":
                $sValue = bodb_time4($sFieldValue);
            break;

            case "time6":
                $sValue = bodb_time6($sFieldValue);
            break;

            case "int":
                $sValue = bodb_int($sFieldValue);
            break;
        
            case "numeric2":
                $sValue = bodb_numeric2($sFieldValue);
            break;
        
            default:
                $sValue = $sFieldValue;
            break;
        }
        //bug($sValue,$sFieldName);
        return $sValue;
    }

    protected function get_todaydb($iSize=14)
    {
        switch ($iSize)
        {
            case 14:
                return date("YdmHis");
            break;
            case 12:
                return date("YdmHi");
            break;
            default:
                return date("Ydm");
            break;
        }
    }
   
    //d-m-a h:m:s  d-m-a h:m
    protected function get_todaybo($iSize=14)
    {
        switch ($iSize)
        {
            case 14:
                return date("d/m/Y H:i:s");
            break;
            case 12:
                return date("d/m/Y H:i");
            break;
            default:
                return date("d/m/Y");
            break;
        }
    }
   
    public function is_ajax(){return $this->isAjax;}
   
    protected function get_post_numrow()
    {
        foreach($_POST as $sKey=>$mxValue)
            if(strstr($sKey,"hidRow_"))
                return $mxValue;
        return NULL;
    }
   
    protected function get_session($sKey=NULL){ return ($sKey)? $_SESSION[$sKey]:$_SESSION;}
    protected function is_session($sKey=""){return ($sKey)?in_array($sKey,array_keys($_SESSION)):(boolean)count($_SESSION);}
   
    protected function get_post_max_size()
    {
        //ini_get: obtiene el valor de un parametro de configuracion de apache
        return size_inbytes(ini_get("post_max_size"));      
//echo 'display_errors = ' . ini_get('display_errors') . "\n";
//echo 'register_globals = ' . ini_get('register_globals') . "\n";
//echo 'post_max_size = ' . ini_get('post_max_size') . "\n";
//echo 'post_max_size+1 = ' . (ini_get('post_max_size')+1) . "\n";
//echo 'post_max_size in bytes = ' . size_inbytes(ini_get("post_max_size"));        
    }
    
    /**
     *
     * @param string $sInputFileName
     * @param int $iFile
     * @return array
     */
    protected function get_upload_data($sInputFileName,$iFile=NULL)
    {
        $arFile = array();
        if(is_array($_FILES[$sInputFileName]["name"]))
        {    
            if($iFile!==NULL)
            {    
                $arFile["name"] = $_FILES[$sInputFileName]["name"][$iFile];
                $arFile["type"] = $_FILES[$sInputFileName]["type"][$iFile];
                $arFile["tmp_name"] = $_FILES[$sInputFileName]["tmp_name"][$iFile];
                $arFile["error"] = $_FILES[$sInputFileName]["error"][$iFile];
                $arFile["size"] = $_FILES[$sInputFileName]["size"][$iFile];
            }
            else
            {
                $arFile = $_FILES[$sInputFileName];
            }
        }
        else
        {            
            $arFile["name"] = $_FILES[$sInputFileName]["name"];
            $arFile["type"] = $_FILES[$sInputFileName]["type"];
            $arFile["tmp_name"] = $_FILES[$sInputFileName]["tmp_name"];
            $arFile["error"] = $_FILES[$sInputFileName]["error"];
            $arFile["size"] = $_FILES[$sInputFileName]["size"];                
        }  
        return $arFile;
    }
       
    protected function get_upload_count($sInputFileName)
    {
        $iCount = 0;
        foreach($_FILES[$sInputFileName]["name"] as $sName)
            if($sName!=="") $iCount++;
        return $iCount;
    }
   
    protected function get_upload_indexes($sInputFileName)
    {
        $arIndex = array();
        foreach($_FILES[$sInputFileName]["name"] as $i=>$sName)
            if($sName!=="") $arIndex[] = $i;
        return $arIndex;
    }
   
    //=======================
    // OVERRIDE TO PUBLIC IF NECESSARY
    //=======================
    protected function set_session($sKey,$sValue){$_SESSION[$sKey]=$sValue;}
    protected function set_insession($sKey,$mxValue){$_SESSION[$sKey]=$mxValue;}
    
    protected function get_server_lanip()
    {
        if($_SERVER["SERVER_ADDR"])
            return $_SERVER["SERVER_ADDR"];
        else
            return $_SERVER["LOCAL_ADDR"];
    }
   
    protected function get_url_referer(){return $_SERVER["HTTP_REFERER"];}
   
    protected function get_request_uri(){return $_SERVER["REQUEST_URI"];}
   
    protected function is_ipad(){return strstr($_SERVER["HTTP_USER_AGENT"],"iPad");}
    protected function is_iphone(){return strstr($_SERVER["HTTP_USER_AGENT"],"iPhone");}
    protected function get_remote_ip(){return $_SERVER["REMOTE_ADDR"];}
    
  
    /**
     * Emula las tres operaciones básicas tipo join existentes en SQL
     * @param array $arLeft 
     * @param array $arRight
     * @param string $sType all|leftouter|inner|rightouter
     * @return array tipo array("leftouter"=>array(),"inner"=>array(),"rightouter"=>array())
     */
    protected function get_array_joins($arLeft,$arRight,$sType="all")
    {
        $arTmpLeft = array();
        $arTmpRight = array();
        $arTmpInner = array();
        
        $arAll = array();
        if($sType=="leftouter" || $sType=="all")
        {
            foreach($arLeft as $mxValue)
                if(!in_array($mxValue,$arRight))
                    $arTmpLeft[] = $mxValue;
            $arAll["leftouter"] = $arTmpLeft;
        }
        
        if($sType=="inner" || $sType=="all")
        {
            foreach($arLeft as $mxValue)
                if(in_array($mxValue,$arRight))
                    $arTmpInner[] = $mxValue;
            $arAll["inner"] = $arTmpInner;
        }
        
        if($sType=="rightouter" || $sType=="all")
        {
            foreach($arRight as $mxValue)
                if(!in_array($mxValue,$arLeft))
                    $arTmpRight[] = $mxValue;
            $arAll["rightouter"] = $arTmpRight;
        }
        return $arAll;
    }
    
    protected function get_slug_cleaned($sString,$sSpChar="-")
    {
        $sCleaned = trim($sString);
        $arBadChars = array
        (
            //caracter valido => caracteres invalidos
            "" => array
                (
                    "\'", "\"", "'", "|", "@", "#", "·", "$", "%", "&", "¬", "(", ")", "=", "?", "¿", 
                    "[", "]", "*", "+",  "\\", "/", "ª", "º", "{", "}", "<", ">", ";", ",", ":",
                    "¡", "!", "^","¨"
                ),
            "a" => array("á", "Á", "ä", "Ä", "â", "Â" ),
            "e" => array("é", "É", "ë", "Ë", "ê", "Ê" ),
            "i" => array("í", "Í", "ï", "Ï", "î", "Î" ),
            "o" => array("ó", "Ó", "ö", "Ö", "ô", "Ô" ),
            "u" => array("ú", "Ú", "ü", "Ü", "û", "Ü" ),
            "n" => array("ñ", "Ñ")
        );
        
        //Lo pasamos todo a minusculas
        $sCleaned = strtolower($sCleaned);
        
        //Relizamos la sustitucion de los caracteres extraños
        foreach($arBadChars as $cGood => $arWrongs)
        {
            foreach($arWrongs as $cWrong)
            { 
                $sCleaned = str_replace($cWrong,$cGood,$sCleaned);
            }
        } 
        //Los espacios se cambian por el char en el argumento
        $sCleaned = str_replace(" ",$sSpChar,$sCleaned);
        return $sCleaned;
    }
    
    protected function to_utf8($sValue)
    {
        $sCoding = mb_detect_encoding($sValue);
        if($sCoding!="UTF-8")
            return utf8_encode($sValue); //devuelve UTF8
            //return utf8_decode($sValue); //devuelve ISO
        return $sValue;
    }
   
    protected function has_childs($arParentChild,$idTest)
    {
        foreach($arParentChild as $arNode)
            if($idTest==$arNode["id_parent"] && $arNode["id"]!=NULL)
                return TRUE;
        return FALSE;
    }

    protected function has_parents($arParentChild,$idTest)
    {
         foreach($arParentChild as $arNode)
            if($idTest==$arNode["id"] && $arNode["id_parent"]!=NULL)
                return TRUE;
        return FALSE;
    }
    
    protected function get_childs($arParentChild,$idTest)
    {
        $arChilds = NULL;
        if($arParentChild) 
        {    
            $arChilds = array();
            foreach($arParentChild as $arNode)
                if($idTest==$arNode["id_parent"])
                    $arChilds[] = $arNode["id"];
        }
        return $arChilds;
    }
    
    protected function get_parents($arParentChild,$idTest)
    {
        $arParents = NULL;
        if($arParentChild) 
        {    
            $arParents = array();
            foreach($arParentChild as $arNode)
                if($idTest==$arNode["id"] && $arNode["id_parent"]!=NULL)
                    $arParents[] = $arNode["id_parent"];
        }
        return $arParents;        
    }
    
    /**
     * Recursive function
     * Before calling this, ensure $arParentChild has a NULL parent for the highest hierarchy id to avoid infinite recursion
     * @param string $idTest
     * @param array $arParentChild Full hierarchy  
     *  $arData[] = array("id"=>"1","id_parent"=>NULL);
     *  $arData[] = array("id"=>"2","id_parent"=>"1");
     *  $arData[] = array("id"=>"3","id_parent"=>"1");
     * @param int $toUp 0:gets childs hierarchy 1:gets parents hierarchy
     * @param array $arHierarchy All parents|childs of $idTest
     * @return 
     */
    protected function get_vhierarchy($idTest,$arParentChild,&$arHierarchy,$toUp=0)
    {
        //si el id a comprobar no es nulo y no existe en el array
        //el id a comprobar siempre debe estar incluido
        if($idTest!=NULL && !in_array($idTest,$arHierarchy))
            $arHierarchy[] = $idTest;
        
        if($toUp==0)
        {
             //si el id a comprobar es de un nivel superior
            if($this->has_childs($arParentChild,$idTest))
            {
                //obtenemos todos sus hijos 
                $arTmpChilds = $this->get_childs($arParentChild,$idTest);
                //con cada hijo lo guardamos en el array de hijos y al mismo tiempo
                //comprobamos si tienen hijos
                foreach($arTmpChilds as $idChild)
                {
                    if(!in_array($idChild, $arHierarchy) && $idChild!=NULL)
                        $arHierarchy[]=$idChild;
                    $this->get_vhierarchy($idChild,$arParentChild,$arHierarchy,0);
                }
            }
            //No es un superior
            else 
            {
                return;
            }
        }
        //$toUp=1 Jerarquia hacia arriba
        else
        {
             //si el id a comprobar es de un nivel inferior
            if($this->has_parents($arParentChild,$idTest))
            {
                //obtenemos todos sus padres 
                $arTmpParents = $this->get_parents($arParentChild,$idTest);
                //a cada padre lo guardamos en el array al mismo tiempo
                //comprobamos si tienen padres
                foreach($arTmpParents as $idParent)
                {
                    if(!in_array($idParent, $arHierarchy) && $idParent!=NULL)
                        $arHierarchy[]=$idParent;
                    $this->get_vhierarchy($idParent,$arParentChild,$arHierarchy,1);
                }
            }
            //No es un inferior, tiene id_parent NULL
            else 
            {
                return;
            }
            
        }
    }//get_vhierarchy
    
    protected function mixed_to_array($mxVariable,$sEplode=",")
    {
        //Casi siempre vendra a null
        if($mxVariable===NULL)
            return array();
        elseif(is_string($mxVariable))
            return explode($sEplode,$mxVariable);
        elseif(is_array($mxVariable))
            return $mxVariable;
        else
            return array();
    }

    /**
    * Elimina del $string el primer caracter si es igual a $c
    * @param string $sString La cadena sobre la que se operara
    * @param char $cLastChar El caracter que se desea eliminar
    * @return string String sin el primer caracter
    */
    public function remove_first_char(&$sString,$cLastChar="/")
    {
        $cFirstChar = $sString{0};
        if($cFirstChar == $cLastChar)
            $sString = substr($sString,1);
    }
    
    public function remove_last_char(&$sString,$cLastChar="/")
    {
        $iStrLen = strlen($sString);
        $cLast = $sString{$iStrLen-1};
        
        if($cLast == $cLastChar)
            $sString = substr($sString,0,$iStrLen-1);
    }
    
    public function is_lastchar_slash($sURL)
    {
        $iLen = strlen($sURL);
        if($iLen>0)
            $cLastChar = $sURL{$iLen-1};
        return ($cLastChar == "/");
    }
            
    
}//end theframework
