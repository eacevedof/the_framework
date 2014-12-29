<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.1.1
 * @name ControllerHomes
 * @file controller_homes.php 
 * @date 24-10-2014 13:47 (SPAIN)
 * @observations:
 * @requires  
 */
import_model("user");
import_component("cookie,validate,filter");
import_component("baseline,serie","highchart");
//high_chart
import_helper("anchor,form,input_date");
import_helper("form,form_fieldset,form_legend,input_text,label,anchor,table,table_typed");
import_helper("input_password,button_basic,raw,div,javascript,image");

import_appmain("view,controller,behaviour");
import_appbehaviour("report");
import_apphelper("listactionbar,controlgroup,formactions,buttontabs,formhead,alertdiv,breadscrumbs,headertabs");

class ControllerHomes extends TheApplicationController
{
    //private $oHomes;
    private $sStartModule;
    private $oReport;
    private $sDateStart;
    private $sDateEnd;
    
    public function __construct()
    {
        $this->sModuleName = "homes";
        $this->sTrLabelPrefix = "tr_hms_";
        //console,pgpage,clientdevice,sesuser,aftersucces,currop  | log,session,permission,view
        parent::__construct($this->sModuleName);
        $this->oReport = new AppBehaviourReport();
        //$this->oReport->set_debugon();
        $this->oView->set_layout("onecolumn");
        $this->load_pagetitle();
        //$this->oView->set_page_title(tr_enterprise_name." - ".tr_he_entities);
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
        //@TODOTEMPLATE
        $oAnchorTmp->add_class("btn btn-alt btn-danger");
        $oAnchorTmp->set_innerhtml("Aruba");
        $oAnchorTmp->set_href($arUrls["aruba"]);
        $oAnchorTmp->set_target("blank");
        $arLinks["aruba"] = $oAnchorTmp;
    
        $oAnchorTmp = new HelperAnchor();
        //@TODOTEMPLATE
        $oAnchorTmp->add_class("btn btn-alt btn-danger");
        $oAnchorTmp->set_innerhtml("St. Marteen");
        $oAnchorTmp->set_href($arUrls["stmarteen"]);
        $oAnchorTmp->set_target("blank");
        $arLinks["stmarteen"] = $oAnchorTmp;
    
        $oAnchorTmp = new HelperAnchor();
        //@TODOTEMPLATE
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
        $sUrlLink = $this->build_url();
//        $arLinks["list"]=array("href"=>$sUrlLink,"innerhtml"=>tr_entities);
//        $oScrumbs = new AppHelperBreadscrumbs($arLinks);
        return $oScrumbs;
    }//build_login_scrumbs()
    
    //login_2
    protected function build_login_tabs()
    {
        $arTabs = array();
        //$sUrlTab = $this->build_url("homesnodb",NULL,"get_list");
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
        $oAuxLabel = new HelperLabel("txtLogin",tr_he_log_login,"lblLogin");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        $oAuxField = new HelperInputPassword("pasPassword","pasPassword");
        $oAuxField->on_enterinsert();
        $oAuxLabel = new HelperLabel("pasPassword",tr_he_log_password,"lblPassword");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
//        $oAuxField = new HelperCheckbox(array("remember"=>"Remember me"),"chkRemember");
//        $oAuxField->set_values_to_check(array($this->get_post("chkRemember")));
//        $oAuxLabel = new HelperLabel("lblRemember",tr_he_log_remember,"chkRemember");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //SAVE BUTTON
        $oAuxField = new HelperButtonBasic("butSave",tr_he_log_savebutton);
        //@TODOTEMPLATE
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
        $arFieldsConfig["login"] = array("controlid"=>"txtLogin","label"=>tr_he_log_login,"length"=>9,"type"=>array("required"));
        $arFieldsConfig["password"] = array("controlid"=>"pasPassword","label"=>tr_he_log_password,"length"=>25,"type"=>array("required"));
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
            //bugp();
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
                $oAlert->set_title(tr_he_login_warning);
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
                    }
                    else
                    {
                        $oModelUser->set_platform(3);
                    }
                    $oModelUser->set_md_login($this->get_post("txtLogin"));
                    $oModelUser->set_md_password($this->get_post("pasPassword"));
                    $oModelUser->load_by_md_login();                    
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
                    //bug("login ok");
                    if($oCookie->exists("afterloginurl"))
                    {
                        $sUrl = $oCookie->get_value("afterloginurl");
                        $oCookie->kill("afterloginurl");
                        //bug($sUrl,"surl");die;
                        //$this->go_to_url($sUrl);
                       
                    }
                    //header("Location:"."/customers/");die;
                    //header("location: /customers/get_list/");
                    $this->go_to_default_module();
                }
                //login error
                else
                {
                    $this->log_session("login:".$this->get_post("txtLogin")." password:".$this->get_post("pasPassword"));
                    $this->oSession->close();
                    $oAlert->set_type("w");
                    $oAlert->set_title(tr_he_login_warning);
                    $this->oView->set_warning_message(tr_he_login_warning);
                }
            }//no error on form validation
        }
        //!$this->is_inserting();
        else
        {
            //bugp();
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
        //@TODOTEMPLATE
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
        $this->oView->set_path_view("homes/view_login");
        $this->oView->show_page();
    }//login()
    
//</editor-fold>
   
    //@TODO: Hace un redirect aqui la lista es el dashboard
    //public function get_list(){$this->login();}
    
    public function logout()
    {
        //$sRemoteIp = $this->get_remote_ip();
        //$sSessionLog = "user:".$this->oSessionUser->get_description()." (".$this->oSessionUser->get_id()."), CLOSED its session from: $sRemoteIp";
        //$this->log_custom($sSessionLog);
        $sSessionLog = "[END ".$this->oSessionUser->get_description()."]";
        $this->log_session($sSessionLog);
        $this->oSession->close();
        $this->go_to_module("homes",null,"login");
    }//logout    
    
    private function go_to_default_module()
    {
        $sStartModule = TFW_DEFAULT_LOGGED_CONTROLLER;
        $sStartView = TFW_DEFAULT_LOGGED_METHOD;
        
        //Esto ya lo controla build_uri
        //if($this->isPermaLink && $sStartView=="get_list") $sStartView = NULL; 
        if($this->sStartModule) $sStartModule = $this->sStartModule;
        
        //bug($sStartModule,"startmodule");die;
        $this->go_to_module($sStartModule,NULL,$sStartView);
    }    

    public function page_notfound()
    {
        $this->oView->use_page404();
        $this->oView->show_page();
    }    
    
    //dash_1
    private function load_config_dash_filters()
    {
        //date_start
        $this->set_filter("date_start","datDateStart",array("operator"=>"like"));
        //date_end
        $this->set_filter("date_end","datDateEnd",array("operator"=>"like"));        
  
    }//load_config_dash_filters    
    
    //dash_2
    private function set_dashfilters_from_post()
    {
        $this->set_filter_value("date_start",$this->get_post("datDateStart"));
        //date_end
        $this->set_filter_value("date_end",$this->get_post("datDateEnd"));
    }//set_dashfilters_from_post
    
    //dash_3
    private function get_dash_filters()
    {
        if(!$this->is_inpost("datDateStart")) $this->set_post("datDateStart",date("01/m/Y"));
        if(!$this->is_inpost("datDateEnd")) $this->set_post("datDateEnd",$this->get_todaybo(4));
        //CAMPOS DE FILTROS
        //date_start
        $oAuxField = new HelperDate("datDateStart","datDateStart");
        $oAuxField->set_is_ipadiphone($this->is_ipad()||$this->is_iphone());
        $oAuxField->set_value($this->get_post("datDateStart"));
        $oAuxField->add_extras("placeholder","Start Date");
        //@TODOTEMPLATE
        $oAuxField->add_class("input-small");
        $oAuxField->add_style("margin-right:4px;");
        $oAuxField->on_entersubmit();
        $arFields[] = $oAuxField;
        
        //date_end
        $oAuxField = new HelperDate("datDateEnd","datDateEnd");
        $oAuxField->set_is_ipadiphone($this->is_ipad()||$this->is_iphone());
        $oAuxField->set_value($this->get_post("datDateEnd"));
        $oAuxField->add_extras("placeholder","End Date");
        //@TODOTEMPLATE
        $oAuxField->add_class("input-small");
        $oAuxField->add_style("margin-right:4px;");
        $oAuxField->on_entersubmit();        
        $arFields[] = $oAuxField;

        //Submit
        $oAuxField = new HelperButtonBasic("butRefresh",tr_he_refresh);
        //@TODOTEMPLATE
        $oAuxField->add_class("btn");
        $oAuxField->set_icon("awe-refresh");
        $oAuxField->add_style("margin-right:2px;");
        $oAuxField->set_js_onclick("TfwControl.form_submit('frmSearch');");
        $arFields[] = $oAuxField;
        
        //Reset
        $oAuxField = new HelperButtonBasic("butClear",tr_he_reset);
        //@TODOTEMPLATE
        $oAuxField->add_class("btn");
        $oAuxField->set_js_onclick("reset_filters();");
        $oAuxField->set_icon("awe-magic");
        $arFields[] = $oAuxField;
        
        //Post Action
        $oAuxField = new HelperInputHidden("hidAction","hidAction");
        $arFields[] = $oAuxField;
        $oAuxField = new HelperInputHidden("hidPostback","hidPostback");
        $arFields[] = $oAuxField;
        
        return $arFields;
    }//get_dash_filters()

    //dash_4
    private function get_dash_columns($sReportType="customers")
    {
        $arColumns = array();
        if($sReportType=="customers")
        {      
            //$arColumns["id"] = tr_he_col_id;
//            $arColumns["first_name"] = tr_he_col_first_name;
//            $arColumns["last_name"] = tr_he_col_last_name;
            $arColumns["company"] = tr_he_col_company;
            $arColumns["num_ordersh"] = tr_he_col_num_ordersh;
            $arColumns["totalsale"] = tr_he_col_totalsale;
        }
        //Products
        else
        {
            //$arColumns["id"] = tr_he_col_id;
            $arColumns["description"] = tr_he_col_description;
            $arColumns["sumsale"] = tr_he_col_sumsale;
        }   
        return $arColumns;   
    }//get_dash_columns()
    
    //insert_5
    private function build_search_form()
    {
        $oForm = new HelperForm("frmSearch");
        $oForm->add_class("header-search");
        $oForm->add_style("margin-bottom:0");
        $arFields = $this->get_dash_filters();
        $oForm->add_controls($arFields);
        return $oForm;
    }//build_search_form()
    //
    //dash_6
    private function build_table($arList,$arColumns,$isCustomer=1)
    {
        $oTableList = new HelperTableTyped($arList,$arColumns);
        $oTableList->add_class("table table-stripped table-hover");
        $oTableList->set_keyfields(array("id"));
        $oTableList->set_url_update($this->build_url("customers",NULL,"update"));
        $oTableList->set_no_paginatebar();
        //COLUMNS CONFIGURATION
        if($this->oPermission->is_read())
            $oTableList->set_column_detail();
 
        if(!$isCustomer)//products
        {            
            $oTableList->set_module("products");
            $oTableList->set_url_update("products",NULL,"update");
            $arExtra[] = array("position"=>9,"label"=>tr_he_col_image);

            $oImage = new HelperImage();
            $oImage->set_id("img%id%");
            $oImage->set_src("%uri_thumb%");
            //$oImage->set_alt("%img_title%");
            //$oImage->set_title("%source_filedate_end%");
            $oImage->add_style("width:50px");
            $oImage->add_style("width:50px");

            $oAnchor = new HelperAnchor();
            $oAnchor->set_href("%uri_href%");
            $oAnchor->set_target("%target%");
            $oAnchor->set_innerhtml($oImage->get_html());
            //bugss();
            $oTableList->add_extra_colums($arExtra);
            $oTableList->set_column_raw(array("virtual_0"=>$oAnchor));
        }
        $arFormat = array("totalsale"=>"numeric2");
        $oTableList->set_format_columns($arFormat);       
        return $oTableList;
    }
    
    public function build_graphic($arData)
    {        
        $arLabelsX = array();
        $arFigures = array();
        $arObjSeries = array(); 
        
        foreach($arData as $i=>$arRow)
            $arLabelsX[] = $arRow["period"];
        
        foreach($arData as $i=>$arRow)
            $arFigures[] = $arRow["rounded_total"];
        
        $arObjSeries[] = new ComponentSerie("Sales per Month",$arFigures); 
        $oGraphic = new ComponentBaseline($arLabelsX, $arObjSeries);
        $oGraphic->set_titulo("Sales per Month");
        $oGraphic->set_subtitulo("Confirmed Orders: ".$this->get_post("datDateStart")." - ".$this->get_post("datDateEnd"));
        $oGraphic->set_titulo_eje_y("Afl.");
        $oGraphic->set_unit(" Afl.");
                
        return $oGraphic;
    }
    
    //dash_6 (dashboard)
    public function get_list()
    {
        //redirige en caso de no tener permiso
        $this->go_to_401($this->oPermission->is_not_select());
        
        $oAlert = new AppHelperAlertdiv();
        $oAlert->use_close_button();
        $sMessage = $this->get_session_message($sMessage);
        if($sMessage)
            $oAlert->set_title($sMessage);
        
        $sMessage = $this->get_session_message($sMessage,"e");
        if($sMessage)
        {
            $oAlert->set_type();
            $oAlert->set_title($sMessage);
        }
        
        //FILTERS
        $this->load_config_dash_filters();
        $oFilter = new ComponentFilter();
        $oFilter->set_fieldnames($this->get_filter_fieldnames());
        //Indica que no se guardara en sesion por nombre de campo sino por nombre de control
        //para esto es necesario respetar el estricto camelcase
        $oFilter->use_field_prefix();
        //Guarda en sesion y post los campos enviados, los de orden y página.
        //siempre devuelve algo en post
        $oFilter->refresh();
        $this->set_dashfilters_from_post();
        //Hay q crear aqui el formulario ya que así se recupera las variables de sesion
        $oForm = $this->build_search_form();
        //RECOVER DATALIST
        //bugp();
        $this->sDateStart = bodb_date($this->get_post("datDateStart"));
        $this->sDateEnd = bodb_date($this->get_post("datDateEnd"));
        //bug($this->sDateEnd,  $this->sDateStart);
        $this->oReport->set_date_start($this->sDateStart);
        $this->oReport->set_date_end($this->sDateEnd);
        $fTotalSold = $this->oReport->get_total_sold();
        $fTotalSold = number_format($fTotalSold,2);
        $fTotalSold .= " Afl.";
        $arTop10Customers = $this->oReport->get_top_10_customers();
        $arTop10Products = $this->oReport->get_top_10_products();
        $arSalePerMonth = $this->oReport->get_sale_per_month();
        //bug($arSalePerMonth);
        //bug($arTop10Customers); bug($arTop10Products);
        //bug($arList[0]);die;
        //TABLE
        $arColumns = $this->get_dash_columns();
        $oTableCustomers = $this->build_table($arTop10Customers,$arColumns);
        $arColumns = $this->get_dash_columns("products");
        $oTableProducts = $this->build_table($arTop10Products,$arColumns,0);
        
        $oGraphic = $this->build_graphic($arSalePerMonth);
        
        
        $oJavascript = new HelperJavascript();
        $oJavascript->set_formid("frmSearch");
        $oJavascript->set_filters($this->get_filter_controls_id());
        
        $this->oView->add_var($oGraphic,"oGraphic");
        $this->oView->add_var($oForm,"oForm");
        $this->oView->add_var($fTotalSold,"fTotalSold");
        $this->oView->add_var($oTableCustomers,"oTableCustomers");
        $this->oView->add_var($oTableProducts,"oTableProducts");
        
        $this->oView->add_var($oJavascript,"oJavascript");
        $this->oView->add_var($oAlert,"oAlert");
        $this->oView->set_layout("twocolumn");
        $this->oView->set_path_view("homes/view_dashboard");
        
        unset($arTop10Customers);unset($arTop10Products);
        unset($oTableCustomers);unset($oTableProducts);
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
    public function error_404(){ header("HTTP/1.0 404 Not Found");$this->oView->show_page();}
    /**
    * server error
    */
    public function error_500(){ $this->oView->show_page();}     
    /**
    * service unavailible
    */
    public function error_503(){ $this->oView->show_page();}    
}