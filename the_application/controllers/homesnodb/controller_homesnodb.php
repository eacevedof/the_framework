<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.8
 * @name ControllerHomesnodb
 * @file controller_homesnodb.php 
 * @date 23-10-2013 09:55 (SPAIN)
 * @observations:
 * @requires  
 */
import_model("usernodb");
import_component("cookie,validate,filter");
//high_chart
import_helper("anchor,form,input_date");
import_helper("form,form_fieldset,form_legend,input_text,label,anchor,table,table_typed");
import_helper("input_password,button_basic,div,javascript,image");

import_appmain("view,controller");
import_apphelper("listactionbar,controlgroup,formactions,buttontabs,formhead,alertdiv,breadscrumbs,headertabs");

class ControllerHomesnodb extends TheApplicationController
{
    //private $oHomesnodb;
    private $sStartModule;
    
    public function __construct()
    {
        $this->sModuleName = "homesnodb";
        $this->sTrLabelPrefix = "tr_hb_";
        //console,pgpage,clientdevice,sesuser,aftersucces,currop  | log,session,permission,view
        parent::__construct($this->sModuleName);
        $this->oView->set_layout("onecolumn");
        $this->load_pagetitle();
        $this->oView->set_page_title(tr_hb_entities." (NDB)");
    }

    /**
     * Utiliza las constantes de aplicación: APP_OFFICE_URI_ARUBA,APP_OFFICE_URI_CURACAO,APP_OFFICE_URI_STMARTEEN
     * @return array $arLinks Listado de botones con accesos directos a oficinas branch
     */
    private function build_links()
    {
        $arUrls["aruba"] = APP_OFFICE_URI_ARUBA;
        $arUrls["curacao"] = APP_OFFICE_URI_CURACAO;
        $arUrls["stmarteen"] = APP_OFFICE_URI_STMARTEEN;
        
        $arLinks = array();
        
        $oAnchorTmp = new HelperAnchor();
        $oAnchorTmp->add_class("btn btn-alt btn-danger");
        $oAnchorTmp->set_innerhtml("Aruba");
        $oAnchorTmp->set_href($arUrls["aruba"]);
        $oAnchorTmp->set_target("blank");
        $arLinks["aruba"] = $oAnchorTmp;
        
        $oAnchorTmp = new HelperAnchor();
        $oAnchorTmp->add_class("btn btn-alt btn-danger");
        $oAnchorTmp->set_innerhtml("St. Marteen");
        $oAnchorTmp->set_href($arUrls["stmarteen"]);
        $oAnchorTmp->set_target("blank");
        $arLinks["stmarteen"] = $oAnchorTmp;
        
        $oAnchorTmp = new HelperAnchor();
        $oAnchorTmp->add_class("btn btn-alt btn-danger");
        $oAnchorTmp->set_innerhtml("Curacao");
        $oAnchorTmp->set_href($arUrls["curacao"]);
        $oAnchorTmp->set_target("blank");
        $arLinks["curacao"] = $oAnchorTmp;
        
        //elimino la oficina en la que estoy
        unset($arLinks[APP_OFFICE_CURRENT]);
        return $arLinks;
    }
    
    /**
     * Indica si la url que se está pidiendo es válida para almacenarla en una cookie para despues hacer
     * la redireccion a dicha dirección
     * @return boolean
     */
    private function is_for_cookieuri()
    {
        $sUrl = $this->get_request_uri();
        $sUrl = trim($sUrl);
        $iStart = strpos($sUrl,"?")+1;
        $sUrl = substr($sUrl,$iStart);
        $arUrlParts = explode("&",$sUrl);
        
        $arUrlParams = array();
        foreach($arUrlParts as $sPart)
            if(trim($sPart)!="")
            {    
                $arKeyVal = explode("=",$sPart);
                if(trim($arKeyVal[1])!="")
                    $arUrlParams[$arKeyVal[0]] = $arKeyVal[1];
            }
        
        //bug($arUrlParams);
        if($arUrlParams)
            //if($arUrlParams["module"] && $arUrlParams["module"]!="homes" && $arUrlParams["module"]!="homesnodb")
            if($arUrlParams["module"]!=TFW_DEFAULT_CONTROLLER)    
                return TRUE;
        
        return FALSE;
    }//is_for_cookieuri
    
//<editor-fold defaultstate="collapsed" desc="LOGIN">
    //login_1
    protected function build_login_scrumbs()
    {
        $arLinks = array();
//        $sUrlLink = $this->build_url();
//        $arLinks["list"]=array("href"=>$sUrlLink,"innerhtml"=>tr_entities);
//        $oScrumbs = new AppHelperBreadscrumbs($arLinks);
        return $oScrumbs;
    }//build_login_scrumbs()

    //login_2
    protected function build_login_tabs()
    {
        $arTabs = array();
        //$sUrlTab = $this->build_url("pictures",NULL,"insert");
        //$arTabs["insert1"]=array("href"=>$sUrlTab,"innerhtml"=>tr_pe_instabs_1);
        //$sUrlTab = $this->build_url("pictures",NULL,"insert2");
        //$arTabs["insert2"]=array("href"=>$sUrlTab,"innerhtml"=>tr_pe_instabs_2);
        $oTabs = new AppHelperHeadertabs($arTabs,"insert1");
        return $oTabs;
    }//build_login_tabs()
    
    //login_4
    protected function build_login_fields($usePost=0)
    {
        $arFields = array(); $oAuxField = NULL; $oAuxLabel = NULL;
        $oAuxField = new HelperInputText("txtLogin","txtLogin");
        $oAuxLabel = new HelperLabel("txtLogin",tr_hb_log_login,"lblLogin");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        $oAuxField = new HelperInputPassword("pasPassword","pasPassword");
        $oAuxField->on_enterinsert();
        $oAuxLabel = new HelperLabel("pasPassword",tr_hb_log_password,"lblPassword");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
//        $oAuxField = new HelperCheckbox(array("remember"=>"Remember me"),"chkRemember");
//        $oAuxField->set_values_to_check(array($this->get_post("chkRemember")));
//        $oAuxLabel = new HelperLabel("lblRemember",tr_hb_log_remember,"chkRemember");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //SAVE BUTTON
        $oAuxField = new HelperButtonBasic("butSave",tr_hb_log_savebutton);
        $oAuxField->add_class("btn btn-large btn-inverse btn-alt");
        $oAuxField->set_js_onclick("insert();");
        $arFields[] = new ApphelperFormactions(array($oAuxField));
        //POST INFO
        $oAuxField = new HelperInputHidden("hidAction","hidAction");
        $arFields[] = $oAuxField;
        $oAuxField = new HelperInputHidden("hidPostback","hidPostback");
        $arFields[] = $oAuxField;
        return $arFields;
    }//build_login_fields()

    //login_5
    protected function get_login_validate()
    {
        $arFieldsConfig = array();
        $arFieldsConfig["login"] = array("controlid"=>"txtLogin","label"=>tr_hb_log_login,"length"=>9,"type"=>array("required"));
        $arFieldsConfig["password"] = array("controlid"=>"pasPassword","label"=>tr_hb_log_password,"length"=>25,"type"=>array("required"));
        return $arFieldsConfig;
    }//get_login_validate

    //login_6
    protected function build_login_form($usePost=0)
    {
        $oForm = new HelperForm("frmInsert");
        $oForm->add_class("form-horizontal");
        $oForm->add_style("margin-bottom:0");
        $arFields = $this->build_login_fields($usePost);
        $oForm->add_controls($arFields);
        return $oForm;
    }//build_login_form()

    //login_7
    public function login()
    {
        //bugp();die;
        $oCookie = new ComponentCookie();
        //$arLinks = $this->build_links();
        
        $arFieldsConfig = $this->get_login_validate();
        if($this->is_inserting())
        {
            $oAlert = new AppHelperAlertdiv();
            $oAlert->use_close_button();    

            if(!$this->is_session("oSessionUser"))
                if($this->is_for_cookieuri())
                    $oCookie->write("afterloginurl",$this->get_request_uri());
            
            $arFieldsValues = $this->get_fields_from_post();
            $oValidate = new ComponentValidate($arFieldsConfig,$arFieldsValues);
            $arErrData = $oValidate->get_error_field();
            if($arErrData)
            {
                $oAlert->set_type("e");
                $oAlert->set_title(tr_hb_login_warning);
                $oAlert->set_content("Field <b>".$arErrData["label"]."</b> ".$arErrData["message"]);
            }
            //No errors on validatation
            else
            {
                $oModelUser = new ModelUser();
                //bug("end","end",1);
                if($this->get_post("txtLogin")==TFW_DEVELOPER_USER 
                        && $this->get_post("pasPassword")==TFW_DEVELOPER_PASSWORD)
                {
                    //Para usuario developer
                    if($this->isMovilDevice)
                        $oModelUser->set_platform(4);//md
                    else
                        $oModelUser->set_platform(3);//pc

                    $oModelUser->set_id(-10);
                    $oModelUser->set_language("english");
                    $oModelUser->set_id_language("1");
                    $oModelUser->set_id_start_module("11");
                    $oModelUser->set_first_name("Module");
                    $oModelUser->set_last_name("Developer");
                    $oModelUser->set_description("Module Developer");
                    $oModelUser->set_path_picture("/images/pictures/users/user_developer.png");
                    $oCookie->kill("afterloginurl");
                    $this->sStartModule = "modulebuilder";
                    $this->oSession->set_user_id($oModelUser->get_id());
                    $this->oSession->set_user_language($oModelUser->get_language());
                    $this->oSession->set_user_language_id($oModelUser->get_id_language());
                    $this->oSession->set("oSessionUser",$oModelUser);
                    $this->log_session("[START: ".$oModelUser->get_description()."]");
             
                    $this->go_to_default_module();
                }
                //No es developer
                else
                {    
                    //bugp();die;
                    if(!$this->is_inpost("chkRemember"))
                    {
                        //elimino la cookie
                        //bug("killing cookie");
                        //$oCookie->kill("remember");
                        $oCookie->kill("remember[login]");
                        $oCookie->kill("remember[password]");
                    }
                    //Se ha marcado la casilla remember
                    else
                    {
                        $oCookie->write("remember[login]",$this->get_post("txtLogin"));
                        $oCookie->write("remember[password]",$this->get_post("pasPassword"));
                    }

                    if($this->isMovilDevice)
                    {
                        $oModelUser->set_platform(4);
                        $oModelUser->set_md_login($this->get_post("txtLogin"));
                        $oModelUser->set_md_password($this->get_post("pasPassword"));
                        $oModelUser->load_by_md_login();
                    }
                    else
                    {
                        $oModelUser->set_platform(3);
                        $oModelUser->set_bo_login($this->get_post("txtLogin"));
                        $oModelUser->set_bo_password($this->get_post("pasPassword"));
                        $oModelUser->load_by_bo_login();
                    }
                }//fin else no developer

                //login ok
                if($oModelUser->get_id())
                //if(1==1)                
                {
                    $this->oSession->set_user_id($oModelUser->get_id());
                    $this->oSession->set_user_language($oModelUser->get_language());
                    $this->oSession->set_user_language_id($oModelUser->get_id_language());
                    $this->oSession->set("oSessionUser",$oModelUser);
                    $this->sStartModule = $oModelUser->get_start_module();
                    //$sRemoteIp = $this->get_remote_ip();
                    //$sSessionLog = "user:".$oModelUser->get_description()." (".$oModelUser->get_id()."), OPENED its session from: $sRemoteIp";
                    //$this->log_custom($sSessionLog);
                    $this->log_session("[START: ".$oModelUser->get_description()."]");
                    if($oCookie->exists("afterloginurl"))
                    {
                        $sUrl = $oCookie->get_value("afterloginurl");
                        $oCookie->kill("afterloginurl");
                        $this->go_to_url($sUrl);
                    }
                    $this->go_to_default_module();
                }
                //login error
                else
                {
                    $this->log_session("login:".$this->get_post("txtLogin")." password:".$this->get_post("pasPassword"));
                    $this->oSession->close();
                    $oAlert->set_type("w");
                    $oAlert->set_title(tr_hb_login_warning);
                    $this->oView->set_warning_message(tr_hb_login_warning);
                }
            }//no error on form validation
        }
        //!$this->is_inserting();
        else 
        {
            //comprobar si se ha guardado en la cookie la opcion "remember"
            $arRemember = $oCookie->get_array("remember");
            if(!empty($arRemember))
            {
                $this->set_post("txtLogin",$arRemember["login"]);
                $this->set_post("pasPassword",$arRemember["pasPassword"]);
                $this->set_post("chkRemember","remember");
            }            
        }
        
        //Si hay errores se recupera desde post
        if($arErrData) $oForm = $this->build_login_form(1);
        else $oForm = $this->build_login_form();
        
        $oAnchor = new HelperAnchor();
        $oAnchor->set_href("#");
        $oAnchor->add_class("brand");
        $oAnchor->set_innerhtml(tr_enterprise_name);
        //SCRUMBS
        $oScrumbs = $this->build_login_scrumbs();
        //TABS
        $oTabs = $this->build_login_tabs();
        //JAVASCRIPT
        $oJavascript = new HelperJavascript();
        $oJavascript->set_validate_config($arFieldsConfig);
        $oJavascript->set_formid("frmInsert");
        $oJavascript->set_focusid("txtLogin");
        //VIEW SET
        $this->oView->add_var($oAnchor,"oAnchor");
        $this->oView->add_var($oScrumbs,"oScrumbs");
        $this->oView->add_var($oTabs,"oTabs");
        $this->oView->add_var($oAlert,"oAlert");
        $this->oView->add_var($oForm,"oForm");
        //$this->oView->add_var($arLinks,"arLinks");
        $this->oView->add_var($oJavascript,"oJavascript");
        $this->oView->set_path_view("homesnodb/view_login");
        $this->oView->show_page();
    }//login()
    
//</editor-fold>
    
    //@TODO: Hace un redirect 
    public function get_list(){$this->login();}
    
    public function logout()
    {
        //$sRemoteIp = $this->get_remote_ip();
        //$sSessionLog = "user:".$this->oSessionUser->get_description()." (".$this->oSessionUser->get_id()."), CLOSED its session from: $sRemoteIp";
        //$this->log_custom($sSessionLog);
        $sSessionLog = "[END ".$this->oSessionUser->get_description()."]";
        $this->log_session($sSessionLog);
        $this->oSession->close();
        $this->go_to_module("homesnodb",null,"login");
    }//logout    
            
    private function go_to_default_module()
    {
        $sStartModule = TFW_DEFAULT_LOGGED_CONTROLLER;
        $sStartView = TFW_DEFAULT_LOGGED_METHOD;
        if($this->sStartModule)
            $sStartModule = $this->sStartModule;
        $this->go_to_module($sStartModule,null,$sStartView);
    }
    
    public function page_notfound()
    {
        $this->oView->use_page404();
        $this->oView->show_page();
    }    
    
    /**
     * acces denied
     */
    public function error_401(){ $this->oView->show_page();} 
    
    /**
     * forbidden
     */
    public function error_403(){ $this->oView->show_page();} 
    /**
    * not found
    */
    public function error_404(){ $this->oView->show_page();} 
    /**
    * server error
    */
    public function error_500(){ $this->oView->show_page();}     
    /**
    * service unavailible
    */
    public function error_503(){ $this->oView->show_page();}    
}