<?php
/**
 * @author Module Builder 1.0.22
 * @link www.eduardoaf.com
 * @version 1.2.8
 * @name ControllerSuspicions
 * @file controller_suspicions.php   
<<<<<<< Updated upstream
 * @date 28-10-2014 10:24 (SPAIN)
=======
 * @date 25-10-2014 12:59 (SPAIN)
>>>>>>> Stashed changes
 * @observations: 
 *      project: UC - Union Caribe 
 * @requires:
 */
//TFW
import_component("page,validate,filter");
import_helper("form,form_fieldset,form_legend,input_text,label,anchor,table,table_typed,textarea,input_date");
import_helper("button_basic,raw,div,javascript");

//APP
import_apptranslate("suspicionsinvolved");
import_appcomponent("suspicion,involved,suspicionpdf");
//import_model("usernodb");
import_model("user");
import_model("ucclientes,uctabgiros,ucagencias,ucciudades,uctbpaises,uccorrespo,uctbbancos","uc");//OJO usa usernodb
import_model("ucocupacio,ucoperacio,ucopercaja,ucoperdeno,uctbobserv,uccmobscli","uc");
import_model("country,gentilic");
import_model("suspicion_head,suspicion_array,suspicions_details,suspicion_involved,suspicions_involved_details");
import_appmain("controller,view,behaviour");
import_appbehaviour("picklist,suspicion");
import_apphelper("listactionbar,controlgroup,formactions,buttontabs,formhead,alertdiv,breadscrumbs,headertabs");
import_apphelper("tablecheck,tablecheckgroup,anchorupdown");

//bugif();
class ControllerSuspicions extends TheApplicationController
{
    //Modelos
    protected $oSuspicionArray;
    //protected $oSuspicionArrayLang;
    protected $oSuspicionHead;
    protected $oSuspicionsDetails;
    //Behaviours
    //Me ayudará a recuperar los datos de todo un informe en formato array para despues generar el pdf
    //protected $oBehaviourSuspicion;
    
    //Componentes
    protected $oComponentSuspicion;
    protected $oComponentInvolved;
    //protected $oComponentPdf;
    
    public function __construct()
    {
        $this->sModuleName = "suspicions";
        //console,pgpage,clientdevice,sesuser,aftersucces,currop | log,session,permission,view
        parent::__construct($this->sModuleName);
        $this->sTrLabelPrefix = "tr_sss_";
        $this->arAfterSuccessCUD["insert"] = "get_list";
        $this->load_pagetitle();
        $this->oSuspicionArray = new ModelSuspicionArray();
        //$this->oSuspicionArray->set_id_language("3");//Dutch
        $this->oSuspicionArray->is_language();//Todo se intentará traducir
        
        //$this->oSuspicionArrayLang = new ModelSuspicionArrayLang($this->oSessionUser->get_id_language());
        //$this->oSuspicionArray->set_dbobject(self::$oDBTo);
        $this->oSuspicionHead = new ModelSuspicionHead();
        //$this->oSuspicionHead->set_dbobject(self::$oDBTo);
        $this->oSuspicionsDetails = new ModelSuspicionsDetails();
        //$this->oSuspicionsDetails->set_dbobject(self::$oDBTo);
        //$this->oBehaviourSuspicion = new AppBehaviourSuspicion();
        //COMPONENTES
        $this->oComponentSuspicion = new AppComponentSuspicion();
        //$this->oComponentPdf = new AppComponentSuspicionPdf();
        //Ayuda a realizar operaciones crud de las personas involucradas en el giro
        $this->oComponentInvolved = new AppComponentInvolved();
        if($this->is_inpost("txtIdTransfer"))
        {
            $this->oComponentInvolved->set_id_transfer($this->get_post("txtIdTransfer"));
        }
        
        $this->oSuspicionHead->set_platform($this->oSessionUser->get_platform());
        if($this->is_inget("id"))
        {
            $this->oSuspicionHead->set_id($this->get_get("id"));
            $this->oSuspicionHead->load_by_id();
            $this->oSuspicionsDetails->set_id_suspicion($this->get_get("id"));
            $this->oComponentInvolved->set_id_suspicion($this->get_get("id"));
        }
        
        //bug($this->oSessionUser);
        //$this->oSessionUser->set_dataowner_table($this->oSuspicionHead->get_table_name());
        //$this->oSessionUser->set_dataowner_tablefield("id_customer");
        //$this->oSessionUser->set_dataowner_keys(array("id"=>$this->oSuspicionHead->get_id()));
    }
    
//<editor-fold defaultstate="collapsed" desc="LIST">
    //list_1
    protected function build_list_scrumbs()
    {
        $arLinks = array();
        $sUrlLink = $this->build_url();
        $arLinks["list"]=array("href"=>$sUrlLink,"innerhtml"=>tr_sss_entities);
        $oScrumbs = new AppHelperBreadscrumbs($arLinks);
        return $oScrumbs;
    }

    //list_2
    protected function build_list_tabs()
    {
        $arTabs = array();
        //$sUrlTab = $this->build_url($this->sModuleName,NULL,"get_list","id=".$this->get_get("id_parent_foreign"));
        //$arTabs["list"]=array("href"=>$sUrlTab,"innerhtml"=>tr_sss_listtabs_1);
        //$sUrlTab = $this->build_url($this->sModuleName,NULL,"get_list_by_foreign","id_foreign=".$this->get_get("id_parent_foreign"));
        //$arTabs["listbyforeign"]=array("href"=>$sUrlTab,"innerhtml"=>tr_sss_listtabs_2);
        $oTabs = new AppHelperHeadertabs($arTabs,"list");
        return $oTabs;
    }

    //list_3
    protected function build_listoperation_buttons()
    {
        $arOpButtons = array();
        $arOpButtons["filters"]=array("href"=>"javascript:reset_filters();","icon"=>"awe-magic","innerhtml"=>tr_sss_listopbutton_filters);
        $arOpButtons["reload"]=array("href"=>"javascript:TfwControl.form_submit();","icon"=>"awe-refresh","innerhtml"=>tr_sss_listopbutton_reload);
        //$arOpButtons["xls"]=array("href"=>"javascript:TfwControl.form_submit(null,modul);","icon"=>"awe-xls","innerhtml"=>tr_sss_listopbutton_xls);
        
        if($this->oPermission->is_insert())
            $arOpButtons["insert"]=array("href"=>$this->build_url($this->sModuleName,NULL,"insert"),"icon"=>"awe-plus","innerhtml"=>tr_sss_listopbutton_insert);
        if($this->oPermission->is_quarantine())
            $arOpButtons["multiquarantine"]=array("href"=>"javascript:multi_quarantine();","icon"=>"awe-remove","innerhtml"=>tr_sss_listopbutton_multiquarantine);
        //if($this->oPermission->is_delete())
            //$arOpButtons["multidelete"]=array("href"=>"javascript:multi_delete();","icon"=>"awe-remove","innerhtml"=>tr_sss_listopbutton_multidelete);
        //PICK WINDOWS
        //$arOpButtons["multiassign"]=array("href"=>"javascript:multiassign_window('suspicions',null,'multiassign','suspicions','addexternaldata');","icon"=>"awe-external-link","innerhtml"=>tr_sss_listopbutton_multiassign);
        //$arOpButtons["singleassign"]=array("href"=>"javascript:single_pick('suspicions','singleassign','txtI','txtI');","icon"=>"awe-external-link","innerhtml"=>tr_sss_listopbutton_singleassign);
        $oOpButtons = new AppHelperButtontabs(tr_sss_entities);
        $oOpButtons->set_tabs($arOpButtons);
        return $oOpButtons;
    }//build_listoperation_buttons()

    //list_4
    protected function load_config_list_filters()
    {
        //id
        //$this->set_filter("id","txtId",array("operator"=>"like"));
        //code_erp
        //$this->set_filter("code_erp","txtCodeErp",array("operator"=>"like"));
        //date_creation
        $this->set_filter("date_creation","datDateCreation",array("operator"=>">="));        
        //id_transfer = TBOSERV.OBIDGIRO
        $this->set_filter("id_transfer","txtIdTransfer",array("operator"=>"like"));
        //id_isr = TBOSERV.OBIDOPER
        $this->set_filter("id_isr","txtIdIsr",array("operator"=>"like"));
        //description
        $this->set_filter("description","txtDescription",array("operator"=>"like"));        
//        //amount
//        $this->set_filter("amount","txtAmount",array("operator"=>"like"));
//        //amount_cash
//        $this->set_filter("amount_cash","txtAmountCash",array("operator"=>"like"));
//        //filial_name
//        $this->set_filter("filial_name","txtFilialName",array("operator"=>"like"));
//        //notes
//        $this->set_filter("notes","txtNotes",array("operator"=>"like"));
//        //observations
//        $this->set_filter("observations","txtObservations",array("operator"=>"like"));
//        //user_creation
//        $this->set_filter("user_creation","txtUserCreation",array("operator"=>"like"));
//        //hour_creation
//        $this->set_filter("hour_creation","txtHourCreation",array("operator"=>"like"));
//        //status
//        $this->set_filter("status","selStatus",array("operator"=>"like"));
//        //type_char
//        $this->set_filter("type_char","txtTypeChar",array("operator"=>"like"));
//        //description
//        //$this->set_filter("description","txtDescription",array("operator"=>"like"));
//        //path_logo
//        $this->set_filter("path_logo","txtPathLogo",array("operator"=>"like"));
//        //office_name
//        $this->set_filter("office_name","txtOfficeName",array("operator"=>"like"));
//        //number
//        $this->set_filter("number","txtNumber",array("operator"=>"like"));
    }//load_config_list_filters()

    //list_5
    protected function set_listfilters_from_post()
    {
        //id
        //$this->set_filter_value("id",$this->get_post("txtId"));
        //code_erp
        //$this->set_filter_value("code_erp",$this->get_post("txtCodeErp"));
        //date_creation
        $this->set_filter_value("date_creation",$this->get_post("datDateCreation"));        
        //id_transfer = TBOSERV.OBIDGIRO
        $this->set_filter_value("id_transfer",$this->get_post("txtIdTransfer"));
        //id_isr = TBOSERV.OBIDOPER
        $this->set_filter_value("id_isr",$this->get_post("txtIdIsr"));
        //description
        $this->set_filter_value("description",$this->get_post("txtDescription"));        
//        //amount
//        $this->set_filter_value("amount",$this->get_post("txtAmount"));
//        //amount_cash
//        $this->set_filter_value("amount_cash",$this->get_post("txtAmountCash"));
//        //filial_name
//        $this->set_filter_value("filial_name",$this->get_post("txtFilialName"));
//        //notes
//        $this->set_filter_value("notes",$this->get_post("txtNotes"));
//        //observations
//        $this->set_filter_value("observations",$this->get_post("txtObservations"));
//        //user_creation
//        $this->set_filter_value("user_creation",$this->get_post("txtUserCreation"));

//        //hour_creation
//        $this->set_filter_value("hour_creation",$this->get_post("txtHourCreation"));
//        //status
//        $this->set_filter_value("status",$this->get_post("selStatus"));
//        //type_char
//        $this->set_filter_value("type_char",$this->get_post("txtTypeChar"));
//        //description
//        //$this->set_filter_value("description",$this->get_post("txtDescription"));
//        //path_logo
//        $this->set_filter_value("path_logo",$this->get_post("txtPathLogo"));
//        //office_name
//        $this->set_filter_value("office_name",$this->get_post("txtOfficeName"));
//        //number
//        $this->set_filter_value("number",$this->get_post("txtNumber"));
    }//set_listfilters_from_post()

    //list_6
    protected function get_list_filters()
    {
        //CAMPOS
        $arFields = array();
        //id
//        $oAuxField = new HelperInputText("txtId","txtId");
//        $oAuxField->set_value($this->get_post("txtId"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtId",tr_sss_fil_id));
//        $arFields[] = $oAuxWrapper;
        //code_erp
        //$oAuxField = new HelperInputText("txtCodeErp","txtCodeErp");
        //$oAuxField->set_value($this->get_post("txtCodeErp"));
        //$oAuxField->on_entersubmit();
        //$oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtCodeErp",tr_sss_fil_code_erp));
        //$arFields[] = $oAuxWrapper;

        //description
        $oAuxField = new HelperInputText("txtDescription","txtDescription");
        $oAuxField->set_value($this->get_post("txtDescription"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtDescription",tr_sss_fil_description));
        $arFields[] = $oAuxWrapper;        
        
        //date_creation
        $oAuxField = new HelperDate("datDateCreation","datDateCreation");
        $oAuxField->set_value($this->get_post("datDateCreation"));
        //$oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("datDateCreation",tr_sss_fil_date_creation));
        $arFields[] = $oAuxWrapper;  
        
        //id_transfer = TBOSERV.OBIDGIRO
        $oAuxField = new HelperInputText("txtIdTransfer","txtIdTransfer");
        $oAuxField->set_value($this->get_post("txtIdTransfer"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtIdTransfer",tr_sss_fil_id_transfer));
        $arFields[] = $oAuxWrapper;
        
        //id_isr = TBOSERV.OBIDOPER
        $oAuxField = new HelperInputText("txtIdIsr","txtIdIsr");
        $oAuxField->set_value($this->get_post("txtIdIsr"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtIdIsr",tr_sss_fil_id_isr));
        $arFields[] = $oAuxWrapper;
                
        //amount
//        $oAuxField = new HelperInputText("txtAmount","txtAmount");
//        $oAuxField->set_value($this->get_post("txtAmount"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtAmount",tr_sss_fil_amount));
//        $arFields[] = $oAuxWrapper;
        //amount_cash
//        $oAuxField = new HelperInputText("txtAmountCash","txtAmountCash");
//        $oAuxField->set_value($this->get_post("txtAmountCash"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtAmountCash",tr_sss_fil_amount_cash));
//        $arFields[] = $oAuxWrapper;
        //filial_name
//        $oAuxField = new HelperInputText("txtFilialName","txtFilialName");
//        $oAuxField->set_value($this->get_post("txtFilialName"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtFilialName",tr_sss_fil_filial_name));
//        $arFields[] = $oAuxWrapper;
        //notes
//        $oAuxField = new HelperInputText("txtNotes","txtNotes");
//        $oAuxField->set_value($this->get_post("txtNotes"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtNotes",tr_sss_fil_notes));
//        $arFields[] = $oAuxWrapper;
        //observations
//        $oAuxField = new HelperInputText("txtObservations","txtObservations");
//        $oAuxField->set_value($this->get_post("txtObservations"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtObservations",tr_sss_fil_observations));
//        $arFields[] = $oAuxWrapper;
        //user_creation
//        $oAuxField = new HelperInputText("txtUserCreation","txtUserCreation");
//        $oAuxField->set_value($this->get_post("txtUserCreation"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtUserCreation",tr_sss_fil_user_creation));
//        $arFields[] = $oAuxWrapper;

        //hour_creation
//        $oAuxField = new HelperInputText("txtHourCreation","txtHourCreation");
//        $oAuxField->set_value($this->get_post("txtHourCreation"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtHourCreation",tr_sss_fil_hour_creation));
//        $arFields[] = $oAuxWrapper;
        //status
//        $oAuxField = new HelperInputText("selStatus","selStatus");
//        $oAuxField->set_value($this->get_post("selStatus"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selStatus",tr_sss_fil_status));
//        $arFields[] = $oAuxWrapper;
        //type_char
//        $oAuxField = new HelperInputText("txtTypeChar","txtTypeChar");
//        $oAuxField->set_value($this->get_post("txtTypeChar"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtTypeChar",tr_sss_fil_type_char));
//        $arFields[] = $oAuxWrapper;
        //path_logo
//        $oAuxField = new HelperInputText("txtPathLogo","txtPathLogo");
//        $oAuxField->set_value($this->get_post("txtPathLogo"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtPathLogo",tr_sss_fil_path_logo));
//        $arFields[] = $oAuxWrapper;
        //office_name
//        $oAuxField = new HelperInputText("txtOfficeName","txtOfficeName");
//        $oAuxField->set_value($this->get_post("txtOfficeName"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtOfficeName",tr_sss_fil_office_name));
//        $arFields[] = $oAuxWrapper;
        //number
//        $oAuxField = new HelperInputText("txtNumber","txtNumber");
//        $oAuxField->set_value($this->get_post("txtNumber"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtNumber",tr_sss_fil_number));
//        $arFields[] = $oAuxWrapper;
        return $arFields;
    }//get_list_filters()

    //list_7
    protected function get_list_columns()
    {
        $arColumns["id"] = tr_sss_col_id;
        $arColumns["description"] = tr_sss_col_description;
        $arColumns["id_isr"] = tr_sss_col_id_isr;        
        //$arColumns["id_transfer"] = tr_sss_col_id_transfer;
        $arColumns["number"] = tr_sss_col_number;
        $arColumns["user_creation"] = tr_sss_col_user_creation;
        //$arColumns["date_creation"] = tr_sss_col_date_creation;
        
        //$arColumns["filial_name"] = tr_sss_col_filial_name;
        //$arColumns["amount"] = tr_sss_col_amount;
        //$arColumns["amount_cash"] = tr_sss_col_amount_cash;        
        //$arColumns["hour_creation"] = tr_sss_col_hour_creation;
        //$arColumns["code_erp"] = tr_sss_col_code_erp;
        //$arColumns["isr"] = tr_sss_col_id_isr;
        //$arColumns["transfer"] = tr_sss_col_id_transfer;
        //$arColumns["office_name"] = tr_sss_col_office_name;
        //$arColumns["notes"] = tr_sss_col_notes;
        //$arColumns["observations"] = tr_sss_col_observations;
        //$arColumns["status"] = tr_sss_col_status;
        //$arColumns["type_char"] = tr_sss_col_type_char;
        //$arColumns["description"] = tr_sss_col_description;
        //$arColumns["path_logo"] = tr_sss_col_path_logo;
        return $arColumns;
    }//get_list_columns()

    //list_8
    public function get_list()
    {
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
        $arColumns = $this->get_list_columns(); 

        //Carga en la variable global la configuración de los campos que se utilizarán
        //FILTERS
        $this->load_config_list_filters();
        $oFilter = new ComponentFilter();
        $oFilter->set_fieldnames($this->get_filter_fieldnames());
        //Indica que no se guardara en sesion por nombre de campo sino por nombre de control
        //para esto es necesario respetar el estricto camelcase
        $oFilter->use_field_prefix();
        //Guarda en sesion y post los campos enviados, los de orden y página
        $oFilter->refresh();
        $this->set_listfilters_from_post();
        
        $arObjFilter = $this->get_list_filters();
        //RECOVER DATALIST
        $this->oSuspicionHead->set_orderby($this->get_orderby());
        $this->oSuspicionHead->set_ordertype($this->get_ordertype());
        $this->oSuspicionHead->set_filters($this->get_filter_searchconfig(array("date_creation"=>"date")));
        //hierarchy recover
        //$this->oSuspicionHead->set_select_user($this->oSessionUser->get_id());
        $arList = $this->oSuspicionHead->get_select_all_ids();
        $iRequestPage = $this->get_post("selPage");
        $oPage = new ComponentPage($arList,$iRequestPage);
        $arList = $oPage->get_items_to_show();
        $arList = $this->oSuspicionHead->get_select_all_by_ids($arList);
        //TABLE
        //This method adds objects controls to search list form
        $oTableList = new HelperTableTyped($arList,$arColumns);
        $oTableList->set_fields($arObjFilter);
        $oTableList->set_module($this->get_current_module());
        $oTableList->add_class("table table-striped table-bordered table-condensed");
        $oTableList->set_keyfields(array("id"));
        $oTableList->is_ordenable();
        $oTableList->set_orderby($this->get_orderby());
        $oTableList->set_orderby_type($this->get_ordertype());
        
        $arFormat = array("date_creation"=>"date","hour_creation"=>"time4");
        $oTableList->set_format_columns($arFormat);        
        //COLUMNS CONFIGURATION
        if($this->oPermission->is_quarantine()||$this->oPermission->is_delete())
            $oTableList->set_column_pickmultiple();//checks column
        if($this->oPermission->is_read())
            $oTableList->set_column_detail();
        if($this->oPermission->is_quarantine())
            $oTableList->set_column_quarantine();
        //if($this->oPermission->is_delete())
            //$oTableList->set_column_delete();

        $oAnchor = new HelperAnchor();
        $oAnchor->set_href("downloads/suspicions/suspicion_%id%.pdf?".date("YmdHis"));
        $oAnchor->set_target("blank");
        $oAnchor->add_class("btn btn-info");
        $oAnchor->set_innerhtml("download");
        //bugss();
        $arExtra[] = array("position"=>9,"label"=>"PDF");
        $oTableList->add_extra_colums($arExtra);
        $oTableList->set_column_raw(array("virtual_0"=>$oAnchor));

        //$oTableList->set_column_text($arColumns);
        //$arFormat = array("amount_total"=>"numeric2","date"=>"date","delivery_date"=>"date");
        //$oTableList->set_format_columns($arFormat);
        //parametros a pasar al popup
        //$oTableList->set_multiassign(array("keys"=>array("k"=>1,"k2"=>2)));
        $oTableList->set_current_page($oPage->get_current());
        $oTableList->set_first_page($oPage->get_first());
        $oTableList->set_previous_page($oPage->get_previous());
        $oTableList->set_next_page($oPage->get_next());
        $oTableList->set_last_page($oPage->get_last());
        $oTableList->set_total_regs($oPage->get_total_regs());
        $oTableList->set_total_pages($oPage->get_total());
        //SCRUMBS
        $oScrumbs = $this->build_list_scrumbs();
        //TABS
        $oTabs = $this->build_list_tabs();
        //OPER BUTTONS
        $oOpButtons = $this->build_listoperation_buttons();
        //JAVASCRIPT
        $oJavascript = new HelperJavascript();
        $oJavascript->set_filters($this->get_filter_controls_id());
        $oJavascript->set_focusid("id_all");
        //VIEW SET
        $this->oView->add_var($oScrumbs,"oScrumbs");
        $this->oView->add_var($oTabs,"oTabs");
        $this->oView->add_var($oOpButtons,"oOpButtons");
        $this->oView->add_var($oAlert,"oAlert");
        $this->oView->add_var($oJavascript,"oJavascript");
        $this->oView->add_var($oTableList,"oTableList");
        $this->oView->show_page();
    }//get_list()
//</editor-fold>

//<editor-fold defaultstate="collapsed" desc="INSERT">
    //insert_1
    protected function build_insert_scrumbs()
    {
        $arLinks = array();
        $sUrlLink = $this->build_url();
        $arLinks["list"]=array("href"=>$sUrlLink,"innerhtml"=>tr_sss_entities);
        $sUrlLink = $this->build_url($this->sModuleName,NULL,"insert");
        $arLinks["insert"]=array("href"=>$sUrlLink,"innerhtml"=>tr_sss_entity_insert);
        $oScrumbs = new AppHelperBreadscrumbs($arLinks);
        return $oScrumbs;
    }//build_insert_scrumbs()

    //insert_2
    protected function build_insert_tabs()
    {
        $arTabs = array();
        //$sUrlTab = $this->build_url($this->sModuleName,NULL,"insert");
        //$arTabs["insert1"]=array("href"=>$sUrlTab,"innerhtml"=>tr_sss_instabs_1);
        //$sUrlTab = $this->build_url($this->sModuleName,NULL,"insert2");
        //$arTabs["insert2"]=array("href"=>$sUrlTab,"innerhtml"=>tr_sss_instabs_2);
        $oTabs = new AppHelperHeadertabs($arTabs,"insert1");
        return $oTabs;
    }//build_insert_tabs()
  
    //insert_3
    protected function build_insert_opbuttons()
    {
        $arOpButtons = array();
        $arOpButtons["list"] = array("href"=>$this->build_url(),"icon"=>"awe-search","innerhtml"=>tr_sss_insopbutton_list);
        //$arOpButtons["extra"] = array("href"=>$this->build_url(),"icon"=>"awe-xxxx","innerhtml"=>tr_sss_insopbutton_extra1);
        $oOpButtons = new AppHelperButtontabs(tr_sss_entities);
        $oOpButtons->set_tabs($arOpButtons);
        return $oOpButtons;
    }//build_insert_opbuttons()

    //insert_4
    protected function build_insert_fields($usePost=0)
    {
        $arFields = array(); $oAuxField = NULL; $oAuxLabel = NULL;
        $arFields[]= new AppHelperFormhead(tr_sss_entity_new);
        //id
        //$oAuxField = new HelperInputText("txtId","txtId");
        //$oAuxField->is_primarykey();
        //if($usePost) $oAuxField->set_value($this->get_post("txtId"));
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        //$arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //code_erp
        //$oAuxField = new HelperInputText("txtCodeErp","txtCodeErp");
        //if($usePost) $oAuxField->set_value($this->get_post("txtCodeErp"));
        //$oAuxLabel = new HelperLabel("txtCodeErp",tr_sss_ins_code_erp,"lblCodeErp");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        //$arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
	//id_transfer = TBOSERV.OBIDGIRO
        $oAuxField = new HelperInputText("txtIdTransfer","txtIdTransfer");
        $oAuxField->set_value($this->get_post("txtIdTransfer"));
        $oAuxField->on_entersubmit();
        if($usePost) $oAuxField->set_value($this->get_post("txtIdTransfer"));
        $oAuxLabel = new HelperLabel("txtIdTransfer",tr_sss_ins_id_transfer,"lblIdTransfer");
        $oAuxLabel->add_class("labelreq");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        $this->oSuspicionHead->set_id_transfer($this->get_post("txtIdTransfer"));
        $this->oSuspicionHead->load_by_transfer();

        //bugp();
        //Ya exite un reporte
        if($this->oSuspicionHead->get_id())
        {
            $sMessage = "Ya existe un reporte para el giro ".$this->oSuspicionHead->get_id_transfer().". Reporte: ".$this->oSuspicionHead->get_id();
            $this->add_error($sMessage);
            if($this->oSuspicionHead->get_delete_date())
            {
                $sMessage = " Contacte con el administrador de sistemas";
                $this->add_error($sMessage);
            }
        }  
        //No existe un reporte
        else
        {
            //(RSI - Reporte de sospecha interna tabla TBOBSERV)
            //id_isr = TBOSERV.OBIDOPER
            $oTbobserv = new ModelTbobserv();
            $oTbobserv->set_obidgiro($this->get_post("txtIdTransfer"));
            //comprueba si el giro existe en observaciones
            $oTbobserv->load_by_transfer();
            
            //si existe una observacion (RSI)
            if($oTbobserv->get_obidoper())
            {
//                $oTabgiros = new ModelTabgiros();
//                $oTabgiros->set_gridgiro($oTbobserv->get_obidgiro());
//                $oTabgiros->load_by_gridgiro();
                //path_logo
//                $oAuxField = new HelperInputText("txtPathLogo","txtPathLogo");
//                if($usePost) $oAuxField->set_value($this->get_post("txtPathLogo"));
//                $oAuxLabel = new HelperLabel("txtPathLogo",tr_sss_ins_path_logo,"lblPathLogo");
//                $oAuxField->readonly();$oAuxField->add_class("readonly");
//                $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
                
                //office_name : handelsnaam
                $oAuxField = new HelperInputText("txtOfficeName","txtOfficeName");
                $oAuxField->set_value("Union Caribe N.V.");
                //if($usePost) $oAuxField->set_value($this->get_post("txtOfficeName"));
                $oAuxLabel = new HelperLabel("txtOfficeName",tr_sss_ins_office_name,"lblOfficeName");
                $oAuxField->readonly();$oAuxField->add_class("readonly");
                $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
                
                //number: Meldingsnummer
                //TODO: obtener el ultimo id de transferencia
                $this->oSuspicionHead->set_date_creation(date("Ymd"));
                $sCounter = $this->oSuspicionHead->get_counter_number();
                $sTransacNumber = date("mdy").$sCounter;                
                $oAuxField = new HelperInputText("txtNumber","txtNumber");
                $oAuxField->set_value($sTransacNumber);
                if($usePost) $oAuxField->set_value($this->get_post("txtNumber"));
                $oAuxLabel = new HelperLabel("txtNumber",tr_sss_ins_number,"lblNumber");
                $oAuxField->readonly();$oAuxField->add_class("readonly");
                $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
                
                //bug($oTbobserv->get_obidoper(),"obidoper");die;
                //txtIdIsr = TBOSERV.OBDOCUME 
                $oAuxField = new HelperInputText("txtIdIsr","txtIdIsr");
                //$oAuxField->set_value($oTbobserv->get_obidoper());
                //bug($oTbobserv->get_obdocume());
                $oAuxField->set_value($oTbobserv->get_obdocume());
                //if($usePost) $oAuxField->set_value($oTbobserv->get_obidoper());
                $oAuxLabel = new HelperLabel("txtIdIsr",tr_sss_ins_id_isr,"lblIdIsr");
                $oAuxField->readonly();$oAuxField->add_class("readonly");
                $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

                //observations : Situatiebeschrijving
                $oObsClient = new ModelCmobscli();
                $oObsClient->set_occodcli($oTbobserv->get_obcodcli());
                //Esto fuerza un like tipo: ocobserv LIKE '@$this->sIdOperation#%
                //$oObsClient->set_idoperation($oTbobserv->get_obidoper());
                $oObsClient->set_idobdocument($oTbobserv->get_obdocume()); 
                $arObservations = $oObsClient->get_list_by_client();
                //coge las observaciones las concatena y les quita la marca del codigo del rsi
                $sObservations = $this->oComponentSuspicion->get_observations($arObservations,$oTbobserv->get_obdocume());
                
                $oAuxField = new HelperTextarea("txaObservations","txaObservations");
                $oAuxField->set_innerhtml($sObservations);
                //$oAuxField->add_style("width:600px;");
                $oAuxField->add_class("span12");
                if($usePost) $oAuxField->set_innerhtml($this->get_post("txaObservations"));
                $oAuxLabel = new HelperLabel("txaObservations",tr_sss_ins_observations,"lblObservations");
                $oAuxField->readonly();$oAuxField->add_class("readonly");
                $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
                
                //=========================================
                //      detalles de cabecera
                //=========================================
                //SECCION DE MULTISELECCION, head1,head2,head3,head4
                //head1
                $this->oSuspicionArray->use_language();
                $arOptions = $this->oSuspicionArray->get_by_head1();
                //bug($arOptions);die;
                $oAuxField = new ApphelperTableCheck($arOptions,"chkHead1");
                $oAuxField->set_sectionid("Head1");
                $oAuxField->set_title("Kruis de indicator aan die van toepassing is");
                $oAuxField->set_cols_labels(array("","Code","Objective Indicatoren"));
                if($usePost) $oAuxField->set_values_to_check($this->get_post("chkHead1"));
                $arFields[] = $oAuxField;
                
                //head2
                $arOptions = $this->oSuspicionArray->get_by_head2();
                $oAuxField = new ApphelperTableCheck($arOptions,"chkHead2");
                $oAuxField->set_sectionid("Head2");
                $oAuxField->set_cols_labels(array("","Code","Subjective Indicatoren"));
                if($usePost) $oAuxField->set_values_to_check($this->get_post("chkHead2"));
                $arFields[] = $oAuxField;
                
                //head3
                $arOptions = $this->oSuspicionArray->get_by_head3();
                $oAuxField = new ApphelperTableCheckGroup($arOptions,"chkHead3");
                $oAuxField->set_title("Transactiegegevens");
                $oAuxField->set_label("Soort dienst - Financiële dienstverleners");
                $oAuxField->set_sectionid("Head3");
                if($usePost) $oAuxField->set_values_to_check($this->get_post("chkHead3"));
                $arFields[] = $oAuxField;
                
                //head4
                $arOptions = $this->oSuspicionArray->get_by_head4();
                $oAuxField = new ApphelperTableCheckGroup($arOptions,"chkHead4");
                $oAuxField->set_label("Soort dienst - Niet Financiële dienstverleners");
                $oAuxField->set_checks_per_row(2);
                $oAuxField->set_sectionid("Head4");
                if($usePost) $oAuxField->set_values_to_check($this->get_post("chkHead4"));
                $arFields[] = $oAuxField;                
                //=========================================
                //      fin detalles de cabecera
                //=========================================
                $oTabGiros = new ModelTabgiros();
                $oTabGiros->set_gridgiro($oTbobserv->get_obidgiro());
                $oTabGiros->load_by_gridgiro("ControllerSuspicions.build_insert_fields");                
                
                //date_creation = TABGIROS.GRFECHAG - transf. Date
                $oAuxField = new HelperInputText("datDateCreation","datDateCreation");
                //pr($oTabGiros->get_grfechag());//2014-06-20 20:19:38
                $oAuxField->set_value($this->oComponentSuspicion->get_date_converted($oTabGiros->get_grfechag()));
                if($usePost) $oAuxField->set_value($this->get_post("datDateCreation"));
                $oAuxLabel = new HelperLabel("datDateCreation",tr_sss_ins_date_creation,"lblDateCreation");
                $oAuxField->readonly();$oAuxField->add_class("readonly");
                $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
                
                //hour_creation = TABGIROS.GRFECHAG - transf. Hour
                $oAuxField = new HelperInputText("txtHourCreation","txtHourCreation");
                //$oAuxField->set_value(date("h:i A"));
                $oAuxField->set_value($this->oComponentSuspicion->get_hour_converted($oTabGiros->get_grfechag()));
                if($usePost) $oAuxField->set_value($this->get_post("txtHourCreation"));
                $oAuxLabel = new HelperLabel("txtHourCreation",tr_sss_ins_hour_creation,"lblHourCreation");
                $oAuxField->readonly();$oAuxField->add_class("readonly");
                $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
                
                //status : Stadium van transactie
                $arOptions = $this->oSuspicionArray->get_by_head5();
                $oAuxField = new HelperSelect($arOptions,"selStatus","selStatus");
                if($usePost) $oAuxField->set_value_to_select($this->get_post("selStatus"));
                $oAuxLabel = new HelperLabel("selStatus",tr_sss_ins_status,"lblStatus");
                $oAuxLabel->add_class("labelreq");
                //$oAuxField->readonly();$oAuxField->add_class("readonly");
                $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
                
                //type_char : Transactietype
                $oAuxField = new HelperInputText("txtTypeChar","txtTypeChar");
                $oAuxField->set_value("CHAR");//CONSTANTE
                if($usePost) $oAuxField->set_value($this->get_post("txtTypeChar"));
                $oAuxLabel = new HelperLabel("txtTypeChar",tr_sss_ins_type_char,"lblTypeChar");
                $oAuxField->readonly();$oAuxField->add_class("readonly");
                $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

                //amount : Bedrag/Valuta
                $oOperCaja = new ModelOpercaja();
                $oOperCaja->set_cooperac($oTbobserv->get_obidoper());
                $oOperCaja->set_cotipmov("3");//3:recibido dev: devuelto
                //solo carga 3 atributos. Si hay más de un resultado solo carga uno aleatorio
                $arAmount = $oOperCaja->get_amount_data();
                //El cambio va ser siempre o en dolares o en florines nunca mixto
                $sAmount = $this->oComponentSuspicion->get_amount($arAmount);                
                $oAuxField = new HelperInputText("txtAmount","txtAmount");
                $oAuxField->set_value($sAmount);
                if($usePost) $oAuxField->set_value($this->get_post("txtAmount"));
                $oAuxLabel = new HelperLabel("txtAmount",tr_sss_ins_amount,"lblAmount");
                $oAuxField->readonly();$oAuxField->add_class("readonly");
                $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
                
                //amount_cash : Bedrag
                $sAmount = "";
                if($this->oComponentSuspicion->is_dollars($arAmount))
                {
                    $oOperDeno = new ModelOperdeno();
                    $oOperDeno->set_odidoper($oTbobserv->get_obidoper());
                    $arAmount = $oOperDeno->get_bills_breakdown();
                    $sAmount = $this->oComponentSuspicion->get_bills_breakdown($arAmount);
                }
                
                $oAuxField = new HelperTextarea("txaAmountCash","txaAmountCash");
                //$oAuxField->add_style("height:25px;");
                $oAuxField->set_innerhtml($sAmount);
	        $oAuxLabel = new HelperLabel("txaAmountCash",tr_sss_ins_amount_cash,"lblAmountCash");
                $oAuxField->readonly();$oAuxField->add_class("readonly");
                $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

                //filial_name : Naam
                $oAgencia = new ModelAgencias();
                $oAgencia->set_accdagen($oTabGiros->get_grcdagen());
                $oAgencia->load_by_accdagen();
                
                $oAuxField = new HelperInputText("txtFilialName","txtFilialName");
                $oAuxField->set_value("(".$oAgencia->get_accdagen().") ".$oAgencia->get_acnomabr()." - ".$oAgencia->get_acdirecc());
                $oAuxField->add_class("span6");
                if($usePost) $oAuxField->set_value($this->get_post("txtFilialName"));
                $oAuxLabel = new HelperLabel("txtFilialName",tr_sss_ins_filial_name,"lblFilialName");
                $oAuxField->readonly();$oAuxField->add_class("readonly");
                $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);                
                
                //=========================================
                //      INVOLVED_1,INVOLVED_2 (insert)
                //=========================================
                $arFields = array_merge($arFields,$this->oComponentInvolved->get_insert_fields($usePost));
                
                //user_creation
                $oAuxField = new HelperInputText("txtUserCreation","txtUserCreation");
                $oAuxField->set_value($this->oSessionUser->get_description());
                if($usePost) $oAuxField->set_value($this->get_post("txtUserCreation"));
                $oAuxLabel = new HelperLabel("txtUserCreation",tr_sss_ins_user_creation,"lblUserCreation");
                $oAuxField->readonly();$oAuxField->add_class("readonly");
                $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
                
                //notes
                $oAuxField = new HelperTextarea("txaNotes","txaNotes");
                if($usePost) $oAuxField->set_innerhtml($this->get_post("txaNotes"));
                $oAuxLabel = new HelperLabel("txaNotes",tr_sss_ins_notes,"lblNotes");
                //$oAuxField->readonly();$oAuxField->add_class("readonly");
                $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);                
                
                //description
                $oAuxField = new HelperInputHidden("hidDescription","hidDescription");
                $oCliente = $oObsClient->get_cliente();
                $oAuxField->set_value($oCliente->get_cinombre()." (".$oCliente->get_cicodcli().")");
                //$oAuxLabel = new HelperLabel("txtDescription",tr_sss_ins_description,"lblDescription");
                //$oAuxField->readonly();$oAuxField->add_class("readonly");
                //$arFields[] = new ApphelperControlGroup($oAuxField);
                $arFields[] = $oAuxField;
                                                
                //SAVE BUTTON
                $oAuxField = new HelperButtonBasic("butSave",tr_sss_ins_savebutton);
                $oAuxField->add_class("btn btn-primary");
                $oAuxField->set_js_onclick("insert();");
                $arFields[] = new ApphelperFormactions(array($oAuxField));                         
            }
            //No hay un isr para este giro
            else
            {
                $sMessage = "El giro ".$this->get_post("txtIdTransfer")." no tiene un RSI asociado!";
                $this->add_error($sMessage);
            }                
        }//fin else (no suspicion)
        //POST INFO
        $oAuxField = new HelperInputHidden("hidAction","hidAction");
        $arFields[] = $oAuxField;
        $oAuxField = new HelperInputHidden("hidPostback","hidPostback");
        $arFields[] = $oAuxField;          
        //bug($this->arErrorMessages);
        return $arFields;
    }//build_insert_fields()

    //insert_5
    protected function get_insert_validate()
    {
        $arFieldsConfig = array();
        //$arFieldsConfig["id"] = array("controlid"=>"txtId","label"=>tr_sss_ins_id,"length"=>9,"type"=>array("numeric","required"));
        //$arFieldsConfig["code_erp"] = array("controlid"=>"txtCodeErp","label"=>tr_sss_ins_code_erp,"length"=>25,"type"=>array());
        $arFieldsConfig["id_transfer"] = array("controlid"=>"txtIdTransfer","label"=>tr_sss_ins_id_transfer,"length"=>10,"type"=>array("required"));
        //$arFieldsConfig["id_isr"] = array("controlid"=>"txtIdIsr","label"=>tr_sss_ins_id_isr,"length"=>4,"type"=>array());
        $arFieldsConfig["amount"] = array("controlid"=>"txtAmount","label"=>tr_sss_ins_amount,"length"=>200,"type"=>array());
        $arFieldsConfig["amount_cash"] = array("controlid"=>"txtAmountCash","label"=>tr_sss_ins_amount_cash,"length"=>200,"type"=>array());
        $arFieldsConfig["filial_name"] = array("controlid"=>"txtFilialName","label"=>tr_sss_ins_filial_name,"length"=>150,"type"=>array());
        $arFieldsConfig["notes"] = array("controlid"=>"txtNotes","label"=>tr_sss_ins_notes,"length"=>150,"type"=>array());
        $arFieldsConfig["observations"] = array("controlid"=>"txtObservations","label"=>tr_sss_ins_observations,"length"=>1000,"type"=>array());
        $arFieldsConfig["user_creation"] = array("controlid"=>"txtUserCreation","label"=>tr_sss_ins_user_creation,"length"=>100,"type"=>array());
        $arFieldsConfig["date_creation"] = array("controlid"=>"datDateCreation","label"=>tr_sss_ins_date_creation,"length"=>10,"type"=>array());
        $arFieldsConfig["hour_creation"] = array("controlid"=>"txtHourCreation","label"=>tr_sss_ins_hour_creation,"length"=>6,"type"=>array());
        $arFieldsConfig["status"] = array("controlid"=>"selStatus","label"=>tr_sss_ins_status,"length"=>50,"type"=>array("required"));
        $arFieldsConfig["type_char"] = array("controlid"=>"txtTypeChar","label"=>tr_sss_ins_type_char,"length"=>15,"type"=>array());
        //$arFieldsConfig["description"] = array("controlid"=>"txtDescription","label"=>tr_sss_ins_description,"length"=>200,"type"=>array());
        $arFieldsConfig["path_logo"] = array("controlid"=>"txtPathLogo","label"=>tr_sss_ins_path_logo,"length"=>250,"type"=>array());
        $arFieldsConfig["office_name"] = array("controlid"=>"txtOfficeName","label"=>tr_sss_ins_office_name,"length"=>100,"type"=>array());
        $arFieldsConfig["number"] = array("controlid"=>"txtNumber","label"=>tr_sss_ins_number,"length"=>25,"type"=>array());
        return $arFieldsConfig;
    }//get_insert_validate

    //insert_6
    protected function build_insert_form($usePost=0)
    {
        $oForm = new HelperForm("frmInsert");
        $oForm->add_class("form-horizontal");
        $oForm->add_style("margin-bottom:0");
        $arFields = $this->build_insert_fields($usePost);
        $oForm->add_controls($arFields);
        return $oForm;
    }//build_insert_form()

    //insert_7
    public function insert()
    {
        //953841
        //bugp();die;
        $this->go_to_401($this->oPermission->is_not_insert());
        //php and js validation
        $arFieldsConfig = $this->get_insert_validate();
        if($this->is_inserting())
        {
            $oAlert = new AppHelperAlertdiv();
            $oAlert->use_close_button();
            $arFieldsValues = $this->get_fields_from_post();
            //bug($arFieldsValues); die;
            $oValidate = new ComponentValidate($arFieldsConfig,$arFieldsValues);
            $arErrData = $oValidate->get_error_field();
            if($arErrData)
            {
                $oAlert->set_type("e");
                $oAlert->set_title(tr_data_not_saved);
                $oAlert->set_content("Field <b>".$arErrData["label"]."</b> ".$arErrData["message"]);
            }
            else
            {
                $this->oSuspicionHead->set_date_creation(date("Ymd"));
                $sCounter = $this->oSuspicionHead->get_counter_number();
                $sTransacNumber = date("mdy").$sCounter;
                $arFieldsValues["number"] = $sTransacNumber;
                $arFieldsValues["date_creation"] = bodb_date($arFieldsValues["date_creation"]);
                $arFieldsValues["hour_creation"] = bodb_time4($arFieldsValues["hour_creation"]);
                //$this->oSuspicionHead->log_save_insert();
                $this->oSuspicionHead->set_attrib_value($arFieldsValues);
                //bug($this->oSuspicionHead);die;
                $this->oSuspicionHead->set_insert_user($this->oSessionUser->get_id());
                //$this->oSuspicionHead->set_platform($this->oSessionUser->get_platform());
                $this->oSuspicionHead->autoinsert();
                if($this->oSuspicionHead->is_error())
                {
                    $oAlert->set_type("e");
                    $oAlert->set_title(tr_data_not_saved);
                    $oAlert->set_content(tr_error_trying_to_save);
                }
                else//insert ok
                {
                    $idSuspicion = $this->oSuspicionHead->get_last_insert_id();
                    //$this->delete_details($idSuspicion,"head1");
                    $this->insert_details($idSuspicion,"chkHead1","head1");

                    //$this->delete_details($idSuspicion,"head2");
                    $this->insert_details($idSuspicion,"chkHead2","head2");
                    
                    //$this->delete_details($idSuspicion,"head3");
                    $this->insert_details($idSuspicion,"chkHead3","head3");
                    
                    //$this->delete_details($idSuspicion,"head4");
                    $this->insert_details($idSuspicion,"chkHead4","head4");

                    $this->oComponentInvolved->set_id_suspicion($idSuspicion);
                    $this->oComponentInvolved->insert_involved();
                    $this->oComponentInvolved->insert_involved(1);
                    
                    $this->set_get("id",$idSuspicion);
                    $oAlert->set_title(tr_data_saved);
                    $this->reset_post();
                    
                    $this->oComponentSuspicion->set_id_suspicion($idSuspicion);
                    $this->oComponentSuspicion->generate_pdf();
                    $this->go_to_after_succes_cud();
                }
            }//no error
        }//fin if is_inserting (post action=save)
        
        //Si hay errores se recupera desde post
        if($arErrData) $oForm = $this->build_insert_form(1);
        //Aqui entra en la primera carga
        else $oForm = $this->build_insert_form();
        
        if($this->is_action("enter_submit") && $this->is_inpost("txtIdTransfer") && $this->arErrorMessages)
        {
            $oAlert = new AppHelperAlertdiv();
            $oAlert->use_close_button();            
            $oAlert->set_type("e");
            $oAlert->set_title("Advertencia");
            $oAlert->set_content(implode(". ",$this->arErrorMessages));
        }        
        //ANCHOR DOWN
        $oAnchorDown = new AppHelperAnchorUpDown();
        
        //SCRUMBS
        $oScrumbs = $this->build_insert_scrumbs();
        //TABS
        $oTabs = $this->build_insert_tabs();
        //OPER BUTTONS
        $oOpButtons = $this->build_insert_opbuttons();
        //JAVASCRIPT
        $oJavascript = new HelperJavascript();
        $oJavascript->set_validate_config($arFieldsConfig);
        $oJavascript->set_formid("frmInsert");
        //$oJavascript->set_focusid("id_all");
        //VIEW SET
        //$this->oView->set_layout("onecolumn");
        $this->oView->add_var($oAnchorDown,"oAnchorDown");
        $this->oView->add_var($oScrumbs,"oScrumbs");
        $this->oView->add_var($oTabs,"oTabs");
        $this->oView->add_var($oOpButtons,"oOpButtons");
        $this->oView->add_var($oAlert,"oAlert");
        $this->oView->add_var($oForm,"oForm");
        $this->oView->add_var($oJavascript,"oJavascript");
        $this->oView->show_page();
    }//insert()
    
    protected function insert_details($idSuspicion,$sCheckName,$sType)
    {
        //INSERT, UPDATE HEAD DETAILS
        $arCheck = $this->get_post($sCheckName);
        if(is_array($arCheck))
        {
            $oSuspicionsDetails = new ModelSuspicionsDetails();
            //$oSuspicionsDetails->set_dbobject(self::$oDBTo);
            $oSuspicionsDetails->set_id_suspicion($idSuspicion);
            $oSuspicionsDetails->set_type($sType);

            $arExistIdTypes = $oSuspicionsDetails->get_by_suspicion_and_type();
            //Left insert, Right delete. Los que están solo en el check se insertan
            $arInsert = $this->get_array_joins($arCheck,$arExistIdTypes,"leftouter");
            $arInsert = $arInsert["leftouter"];
            
            foreach($arInsert as $idType)
            {
                $oSuspicionsDetails->set_id_type($idType);
                $oSuspicionsDetails->autoinsert();
            }
        }//arCheck is array
    }
    
    protected function delete_details($idSuspicion,$sCheckName,$sType)
    {
        //UPDATE HEAD DETAILS
        $arCheck = $this->get_post($sCheckName);
        if(is_array($arCheck))
        {
            $oSuspicionsDetails = new ModelSuspicionsDetails();
            //$oSuspicionsDetails->set_dbobject(self::$oDBTo);
            $oSuspicionsDetails->set_id_suspicion($idSuspicion);
            $oSuspicionsDetails->set_type($sType);

            $arExistIdTypes = $oSuspicionsDetails->get_by_suspicion_and_type();
            //Left insert, Right delete. Los que están solo en bd se eliminan
            $arDelete = $this->get_array_joins($arCheck,$arExistIdTypes,"rightouter");
            $arDelete = $arDelete["rightouter"];

            foreach($arDelete as $idType)
            {
                $arCondition = array("id_suspicion"=>$idSuspicion,"id_type"=>$idType,"type"=>$sType);
                $oSuspicionsDetails->autodelete($arCondition);
            }
        }
    }
    
//</editor-fold>

//<editor-fold defaultstate="collapsed" desc="UPDATE">
    //update_1
    protected function build_update_scrumbs()
    {
        $arLinks = array();
        $sUrlLink = $this->build_url();
        $arLinks["list"]=array("href"=>$sUrlLink,"innerhtml"=>tr_sss_entities);
        $sUrlLink = $this->build_url($this->sModuleName,NULL,"update","id=".$this->get_get("id"));
        $arLinks["detail"]=array("href"=>$sUrlLink,"innerhtml"=>tr_sss_entity.": ".$this->oSuspicionHead->get_id()." - ".$this->oSuspicionHead->get_description());
        $oScrumbs = new AppHelperBreadscrumbs($arLinks);
        return $oScrumbs;
    }//build_update_scrumbs()

    //update_2
    protected function build_update_tabs()
    {
        $arTabs = array();
        $sUrlTab = $this->build_url($this->sModuleName,NULL,"update","id=".$this->get_get("id"));
        $arTabs["detail"]=array("href"=>$sUrlTab,"innerhtml"=>tr_sss_updtabs_detail);
        //$sUrlTab = $this->build_url($this->sModuleName,"foreignamodule","get_list_by_foreign","id_foreign=".$this->get_get("id_parent_foreign"));
        //$arTabs["foreigndata"]=array("href"=>$sUrlTab,"innerhtml"=>tr_sss_updtabs_foreigndata);
        $oTabs = new AppHelperHeadertabs($arTabs,"detail");
        return $oTabs;
    }//build_update_tabs()

    //update_3
    protected function build_update_opbuttons()
    {
        $arOpButtons = array();
        if($this->oPermission->is_select())
            $arOpButtons["list"]=array("href"=>$this->build_url(),"icon"=>"awe-search","innerhtml"=>tr_sss_updopbutton_list);
        //if($this->oPermission->is_insert())
            //$arOpButtons["insert"]=array("href"=>$this->build_url($this->sModuleName,NULL,"insert"),"icon"=>"awe-plus","innerhtml"=>tr_sss_updopbutton_insert);
        if($this->oPermission->is_quarantine())
            $arOpButtons["delete"]=array("href"=>$this->build_url($this->sModuleName,NULL,"quarantine","id=".$this->get_get("id")),"icon"=>"awe-remove","innerhtml"=>tr_sss_updopbutton_quarantine);
        //if($this->oPermission->is_delete())
            //$arOpButtons["delete"]=array("href"=>$this->build_url($this->sModuleName,NULL,"delete","id=".$this->get_get("id")),"icon"=>"awe-remove","innerhtml"=>tr_sss_updopbutton_delete);
        $arOpButtons["pdf"]=array("href"=>"downloads/suspicions/suspicion_".$this->get_get("id").".pdf?".date("YmdHis"),"icon"=>"awe-pdf","innerhtml"=>tr_sss_updopbutton_pdf,"target"=>"blank");
        $oOpButtons = new AppHelperButtontabs(tr_sss_entities);
        $oOpButtons->set_tabs($arOpButtons);
        return $oOpButtons;
    }//build_update_opbuttons()

    //update_4
    protected function build_update_fields($usePost=0)
    {
        $arFields = array(); $oAuxField = NULL; $oAuxLabel = NULL;
        $arFields[]= new AppHelperFormhead(tr_sss_entity_new);
        
        //code_erp
        //$oAuxField = new HelperInputText("txtCodeErp","txtCodeErp");
        //if($usePost) $oAuxField->set_value($this->get_post("txtCodeErp"));
        //$oAuxLabel = new HelperLabel("txtCodeErp",tr_sss_ins_code_erp,"lblCodeErp");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        //$arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
                
        //id
        $oAuxField = new HelperInputText("txtId","txtId");
        $oAuxField->is_primarykey();
        $oAuxField->set_value($this->oSuspicionHead->get_id());
        $oAuxField->readonly();$oAuxField->add_class("readonly");
        $oAuxLabel = new HelperLabel("txtId",tr_sss_ins_id,"lblId");        
        $oAuxLabel->add_class("labelpk");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
	//id_transfer = TBOSERV.OBIDGIRO
        $oAuxField = new HelperInputText("txtIdTransfer","txtIdTransfer");
        $oAuxField->set_value($this->oSuspicionHead->get_id_transfer());
        $oAuxLabel = new HelperLabel("txtIdTransfer",tr_sss_ins_id_transfer,"lblIdTransfer");
        $oAuxLabel->add_class("labelreq");
        $oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //Ya exite un reporte
        if($this->oSuspicionHead->get_id())
        {                
            //office_name : handelsnaam
            $oAuxField = new HelperInputText("txtOfficeName","txtOfficeName");
            $oAuxField->set_value($this->oSuspicionHead->get_office_name());
            $oAuxLabel = new HelperLabel("txtOfficeName",tr_sss_ins_office_name,"lblOfficeName");
            $oAuxField->readonly();$oAuxField->add_class("readonly");
            $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

            //number: Meldingsnummer
            //TODO: obtener el ultimo id de transferencia
            $oAuxField = new HelperInputText("txtNumber","txtNumber");
            $oAuxField->set_value($this->oSuspicionHead->get_number());
            $oAuxLabel = new HelperLabel("txtNumber",tr_sss_ins_number,"lblNumber");
            $oAuxField->readonly();$oAuxField->add_class("readonly");
            $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

            //bug($oTbobserv->get_obidoper(),"obidoper");die;
            //txtIdIsr = TBOSERV.OBIDOPER
            $oAuxField = new HelperInputText("txtIdIsr","txtIdIsr");
            $oAuxField->set_value($this->oSuspicionHead->get_id_isr());
            $oAuxLabel = new HelperLabel("txtIdIsr",tr_sss_ins_id_isr,"lblIdIsr");
            $oAuxField->readonly();$oAuxField->add_class("readonly");
            $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

            //observations : Situatiebeschrijving
            $oAuxField = new HelperTextarea("txaObservations","txaObservations");
            $oAuxField->set_innerhtml($this->oSuspicionHead->get_observations());
            //$oAuxField->add_style("width:600px;");
            $oAuxField->add_class("span12");
            $oAuxLabel = new HelperLabel("txaObservations",tr_sss_ins_observations,"lblObservations");
            $oAuxField->readonly();$oAuxField->add_class("readonly");
            $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

            //=========================================
            //      detalles de cabecera
            //=========================================
            //SECCION DE MULTISELECCION, head1,head2,head3,head4
            //head1
            $arOptions = $this->oSuspicionArray->get_by_head1();
            $oAuxField = new ApphelperTableCheck($arOptions,"chkHead1");
            $oAuxField->set_sectionid("Head1");
            $oAuxField->set_title("Kruis de indicator aan die van toepassing is");
            $oAuxField->set_cols_labels(array("","Code","Objective Indicatoren"));
            $this->oSuspicionsDetails->set_type("head1");
            $arSelect = $this->oSuspicionsDetails->get_by_suspicion_and_type();
            $oAuxField->set_values_to_check($arSelect);
            if($usePost) $oAuxField->set_values_to_check($this->get_post("chkHead1"));
            $arFields[] = $oAuxField;

            //head2
            $arOptions = $this->oSuspicionArray->get_by_head2();
            $oAuxField = new ApphelperTableCheck($arOptions,"chkHead2");
            $oAuxField->set_sectionid("Head2");
            $oAuxField->set_cols_labels(array("","Code","Subjective Indicatoren"));
            $this->oSuspicionsDetails->set_type("head2");
            $arSelect = $this->oSuspicionsDetails->get_by_suspicion_and_type();
            $oAuxField->set_values_to_check($arSelect);            
            if($usePost) $oAuxField->set_values_to_check($this->get_post("chkHead2"));
            $arFields[] = $oAuxField;

            //head3
            $arOptions = $this->oSuspicionArray->get_by_head3();
            $oAuxField = new ApphelperTableCheckGroup($arOptions,"chkHead3");
            $oAuxField->set_title("Transactiegegevens");
            $oAuxField->set_label("Soort dienst - Financiële dienstverleners");
            $oAuxField->set_sectionid("Head3");
            $this->oSuspicionsDetails->set_type("head3");
            $arSelect = $this->oSuspicionsDetails->get_by_suspicion_and_type();
            $oAuxField->set_values_to_check($arSelect);            
            if($usePost) $oAuxField->set_values_to_check($this->get_post("chkHead3"));
            $arFields[] = $oAuxField;

            //head4
            $arOptions = $this->oSuspicionArray->get_by_head4();
            $oAuxField = new ApphelperTableCheckGroup($arOptions,"chkHead4");
            $oAuxField->set_label("Soort dienst - Niet Financiële dienstverleners");
            $oAuxField->set_checks_per_row(2);
            $oAuxField->set_sectionid("Head4");
            $this->oSuspicionsDetails->set_type("head4");
            $arSelect = $this->oSuspicionsDetails->get_by_suspicion_and_type();
            $oAuxField->set_values_to_check($arSelect);            
            if($usePost) $oAuxField->set_values_to_check($this->get_post("chkHead4"));
            $arFields[] = $oAuxField;                
            //=========================================
            //      fin detalles de cabecera
            //=========================================
           //date_creation
            $oAuxField = new HelperInputText("datDateCreation","datDateCreation");
            $oAuxField->set_value(dbbo_date($this->oSuspicionHead->get_date_creation()));
            $oAuxLabel = new HelperLabel("datDateCreation",tr_sss_ins_date_creation,"lblDateCreation");
            $oAuxField->readonly();$oAuxField->add_class("readonly");
            $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

            //hour_creation
            $oAuxField = new HelperInputText("txtHourCreation","txtHourCreation");
            $oAuxField->set_value(dbbo_time4($this->oSuspicionHead->get_hour_creation()));
            $oAuxLabel = new HelperLabel("txtHourCreation",tr_sss_ins_hour_creation,"lblHourCreation");
            $oAuxField->readonly();$oAuxField->add_class("readonly");
            $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);                                

            //status : Stadium van transactie
            $arOptions = $this->oSuspicionArray->get_by_head5();
            $oAuxField = new HelperSelect($arOptions,"selStatus","selStatus");
            $oAuxField->set_value_to_select($this->oSuspicionHead->get_status());
            if($usePost) $oAuxField->set_value_to_select($this->get_post("selStatus"));
            $oAuxLabel = new HelperLabel("selStatus",tr_sss_ins_status,"lblStatus");
            $oAuxLabel->add_class("labelreq");
            //$oAuxField->readonly();$oAuxField->add_class("readonly");
            $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

            //type_char : Transactietype
            $oAuxField = new HelperInputText("txtTypeChar","txtTypeChar");
            $oAuxField->set_value($this->oSuspicionHead->get_type_char());
            $oAuxLabel = new HelperLabel("txtTypeChar",tr_sss_ins_type_char,"lblTypeChar");
            $oAuxField->readonly();$oAuxField->add_class("readonly");
            $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

            //amount : Bedrag/Valuta
            $oAuxField = new HelperInputText("txtAmount","txtAmount");
            $oAuxField->set_value($this->oSuspicionHead->get_amount());
            $oAuxLabel = new HelperLabel("txtAmount",tr_sss_ins_amount,"lblAmount");
            $oAuxField->readonly();$oAuxField->add_class("readonly");
            $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

            //amount_cash : Bedrag
            $oAuxField = new HelperTextarea("txaAmountCash","txaAmountCash");
            //$oAuxField->add_style("height:25px;");
            $oAuxField->set_innerhtml($this->oSuspicionHead->get_amount_cash());
            $oAuxLabel = new HelperLabel("txaAmountCash",tr_sss_ins_amount_cash,"lblAmountCash");
            $oAuxField->readonly();$oAuxField->add_class("readonly");
            $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

            //filial_name : Naam
            $oAuxField = new HelperInputText("txtFilialName","txtFilialName");
            $oAuxField->set_value($this->oSuspicionHead->get_filial_name());
            $oAuxField->add_class("span6");
            $oAuxLabel = new HelperLabel("txtFilialName",tr_sss_ins_filial_name,"lblFilialName");
            $oAuxField->readonly();$oAuxField->add_class("readonly");
            $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
            //=========================================
            //      INVOLVED_1, INVOLVED_2 (update)
            //=========================================
            $arFields = array_merge($arFields,$this->oComponentInvolved->get_update_fields($usePost));
            
            //user_creation
            $oAuxField = new HelperInputText("txtUserCreation","txtUserCreation");
            $oAuxField->set_value($this->oSessionUser->get_description());
            if($usePost) $oAuxField->set_value($this->get_post("txtUserCreation"));
            $oAuxLabel = new HelperLabel("txtUserCreation",tr_sss_ins_user_creation,"lblUserCreation");
            $oAuxField->readonly();$oAuxField->add_class("readonly");
            $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

            //notes
            $oAuxField = new HelperTextarea("txaNotes","txaNotes");
            $oAuxField->set_innerhtml($this->oSuspicionHead->get_notes());
            if($usePost) $oAuxField->set_innerhtml($this->get_post("txaNotes"));
            $oAuxLabel = new HelperLabel("txaNotes",tr_sss_ins_notes,"lblNotes");
            //$oAuxField->readonly();$oAuxField->add_class("readonly");
            $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);                

            //description
            //$oAuxField = new HelperInputText("txtDescription","txtDescription");
            //if($usePost) $oAuxField->set_value($this->get_post("txtDescription"));
            //$oAuxLabel = new HelperLabel("txtDescription",tr_sss_ins_description,"lblDescription");
            //$oAuxField->readonly();$oAuxField->add_class("readonly");
            //$arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

            //SAVE BUTTON
            $oAuxField = new HelperButtonBasic("butSave",tr_sss_upd_savebutton);
            $oAuxField->add_class("btn btn-primary");
            $oAuxField->set_js_onclick("update();");
            $arFields[] = new ApphelperFormactions(array($oAuxField));      
        }
        //POST INFO
        $oAuxField = new HelperInputHidden("hidAction","hidAction");
        $arFields[] = $oAuxField;
        $oAuxField = new HelperInputHidden("hidPostback","hidPostback");
        $arFields[] = $oAuxField;          
        //bug($this->arErrorMessages);
        return $arFields;
    }//build_update_fields()

    //update_5
    protected function get_update_validate()
    {
        $arFieldsConfig = array();
        $arFieldsConfig["id"] = array("controlid"=>"txtId","label"=>tr_sss_upd_id,"length"=>9,"type"=>array("numeric","required"));
        //$arFieldsConfig["code_erp"] = array("controlid"=>"txtCodeErp","label"=>tr_sss_upd_code_erp,"length"=>25,"type"=>array());
        //$arFieldsConfig["id_transfer"] = array("controlid"=>"txtIdTransfer","label"=>tr_sss_upd_id_transfer,"length"=>4,"type"=>array());
        //$arFieldsConfig["id_isr"] = array("controlid"=>"txtIdIsr","label"=>tr_sss_upd_id_isr,"length"=>4,"type"=>array());
        $arFieldsConfig["amount"] = array("controlid"=>"txtAmount","label"=>tr_sss_upd_amount,"length"=>200,"type"=>array());
        $arFieldsConfig["amount_cash"] = array("controlid"=>"txtAmountCash","label"=>tr_sss_upd_amount_cash,"length"=>200,"type"=>array());
        $arFieldsConfig["filial_name"] = array("controlid"=>"txtFilialName","label"=>tr_sss_upd_filial_name,"length"=>150,"type"=>array());
        $arFieldsConfig["notes"] = array("controlid"=>"txtNotes","label"=>tr_sss_upd_notes,"length"=>150,"type"=>array());
        $arFieldsConfig["observations"] = array("controlid"=>"txtObservations","label"=>tr_sss_upd_observations,"length"=>1000,"type"=>array());
        $arFieldsConfig["user_creation"] = array("controlid"=>"txtUserCreation","label"=>tr_sss_upd_user_creation,"length"=>100,"type"=>array());
        $arFieldsConfig["date_creation"] = array("controlid"=>"datDateCreation","label"=>tr_sss_upd_date_creation,"length"=>10,"type"=>array());
        $arFieldsConfig["hour_creation"] = array("controlid"=>"txtHourCreation","label"=>tr_sss_upd_hour_creation,"length"=>8,"type"=>array());
        $arFieldsConfig["status"] = array("controlid"=>"selStatus","label"=>tr_sss_upd_status,"length"=>50,"type"=>array());
        $arFieldsConfig["type_char"] = array("controlid"=>"txtTypeChar","label"=>tr_sss_upd_type_char,"length"=>15,"type"=>array());
        //$arFieldsConfig["description"] = array("controlid"=>"txtDescription","label"=>tr_sss_upd_description,"length"=>200,"type"=>array());
        //$arFieldsConfig["path_logo"] = array("controlid"=>"txtPathLogo","label"=>tr_sss_upd_path_logo,"length"=>250,"type"=>array());
        $arFieldsConfig["office_name"] = array("controlid"=>"txtOfficeName","label"=>tr_sss_upd_office_name,"length"=>100,"type"=>array());
        $arFieldsConfig["number"] = array("controlid"=>"txtNumber","label"=>tr_sss_upd_number,"length"=>25,"type"=>array());
        return $arFieldsConfig;
    }//get_update_validate

    //update_6
    protected function build_update_form($usePost=0)
    {
        $id = $this->oSuspicionHead->get_id();
        if($id)
        {
            $oForm = new HelperForm("frmUpdate");
            $oForm->add_class("form-horizontal");
            $oForm->add_style("margin-bottom:0");
            if($this->oPermission->is_read()&&$this->oPermission->is_not_update())
                $oForm->readonly();
            $arFields = $this->build_update_fields($usePost);
            $oForm->add_controls($arFields);
        }//if(id)
        else//!id
            $this->go_to_404();
        return $oForm;
    }//build_update_form()

    //update_7
    public function update()
    {
        //$this->go_to_401(($this->oPermission->is_not_read() && $this->oPermission->is_not_update())||$this->oSessionUser->is_not_dataowner());
        $this->go_to_401($this->oPermission->is_not_read() && $this->oPermission->is_not_update());
        $this->go_to_404(!$this->oSuspicionHead->is_in_table());
        //Validacion con PHP y JS
        $arFieldsConfig = $this->get_update_validate();
        if($this->is_updating())
        {
            $oAlert = new AppHelperAlertdiv();
            $oAlert->use_close_button();
            $arFieldsValues = $this->get_fields_from_post();
            $oValidate = new ComponentValidate($arFieldsConfig,$arFieldsValues);
            $arErrData = $oValidate->get_error_field();
            if($arErrData)
            {
                $oAlert->set_type("e");
                $oAlert->set_title(tr_data_not_saved);
                $oAlert->set_content("Field <b>".$arErrData["label"]."</b> ".$arErrData["message"]);
            }
            else
            {
                $arFieldsValues["date_creation"] = bodb_date($arFieldsValues["date_creation"]);
                $arFieldsValues["hour_creation"] = bodb_time4($arFieldsValues["hour_creation"]);                
                
                $this->oSuspicionHead->set_attrib_value($arFieldsValues);
                $this->oSuspicionHead->set_update_user($this->oSessionUser->get_id());
                $this->log_custom($this->get_post());
                $this->log_custom($this->oSuspicionHead);
                $this->oSuspicionHead->autoupdate();

                if($this->oSuspicionHead->is_error())
                {
                    $oAlert->set_type("e");
                    $oAlert->set_title(tr_data_not_saved);
                    $oAlert->set_content(tr_error_trying_to_save);
                }//no error
                else//update ok
                {
                    $idSuspicion = $this->oSuspicionHead->get_id();
                    //UPDATE HEAD DETAILS
                    //elimino los que se han deseleccionado y añado los nuevos
                    $this->delete_details($idSuspicion,"chkHead1","head1");
                    $this->insert_details($idSuspicion,"chkHead1","head1");

                    $this->delete_details($idSuspicion,"chkHead2","head2");
                    $this->insert_details($idSuspicion,"chkHead2","head2");
                    
                    $this->delete_details($idSuspicion,"chkHead3","head3");
                    $this->insert_details($idSuspicion,"chkHead3","head3");
                    
                    $this->delete_details($idSuspicion,"chkHead4","head4");
                    $this->insert_details($idSuspicion,"chkHead4","head4");
                                        
                    $this->oComponentInvolved->set_id_suspicion($idSuspicion);
                    
                    //UPDATE INVOLVED AND INVOLVED DETAILS 
                    $this->oComponentInvolved->update_involved();
                    $this->oComponentInvolved->update_involved(1);
                    
                    $this->oComponentSuspicion->set_id_suspicion($idSuspicion);
                    $this->oComponentSuspicion->generate_pdf();
                    
                    $this->set_get("id",$idSuspicion);
                    $oAlert->set_title(tr_data_saved);
                    $this->reset_post();
                    //$this->go_to_after_succes_cud();
                }//error save
            }//error validation
        }//is_updating()
        if($arErrData) $oForm = $this->build_update_form(1);
        else $oForm = $this->build_update_form(); 
        //ANCHOR DOWN
        $oAnchorDown = new AppHelperAnchorUpDown("Down");
        //SCRUMBS
        $oScrumbs = $this->build_update_scrumbs();
        //TABS
        $oTabs = $this->build_update_tabs();
        //OPER BUTTONS
        $oOpButtons = $this->build_update_opbuttons();
        //JAVASCRIPT
        $oJavascript = new HelperJavascript();
        $oJavascript->set_updateaction();
        $oJavascript->set_validate_config($arFieldsConfig);
        $oJavascript->set_formid("frmUpdate");
        //$oJavascript->set_focusid("id_all");
        //VIEW SET
        $this->oView->add_var($oAnchorDown,"oAnchorDown");
        $this->oView->add_var($oScrumbs,"oScrumbs");
        $this->oView->add_var($oTabs,"oTabs");
        $this->oView->add_var($oOpButtons,"oOpButtons");
        $this->oView->add_var($oAlert,"oAlert");
        $this->oView->add_var($oForm,"oForm");
        $this->oView->add_var($oJavascript,"oJavascript");
        $this->oView->show_page();
    }//update()
//</editor-fold>

//<editor-fold defaultstate="collapsed" desc="DELETE">
    //delete_1
    protected function single_delete()
    {
        $id = $this->get_get("id");
        if($id)
        {
            $this->oSuspicionHead->set_id($id);
            $this->oSuspicionHead->autodelete();
            if($this->oSuspicionHead->is_error())
            {
                    $this->isError = TRUE;
                    $this->set_session_message(tr_error_trying_to_delete);
            }
            else
            {
                    $this->set_session_message(tr_data_deleted);
            }
        }//si existe el id
        else
            $this->set_session_message(tr_error_key_not_supplied,"e");
    }//single_delete()

    //delete_2
    protected function multi_delete()
    {
        //Intenta recuperar pkeys sino pasa a recuperar el id. En ultimo caso lo que se ha pasado por parametro
        $arKeys = $this->get_listkeys();
        foreach($arKeys as $sKey)
        {
            $id = $sKey;
            $this->oSuspicionHead->set_id($id);
            $this->oSuspicionHead->autodelete();
            if($this->oSuspicionHead->is_error())
            {
                    $this->isError = true;
                    $this->set_session_message(tr_error_trying_to_delete,"e");
            }
        }//foreach arkeys
        if(!$this->isError)
            $this->set_session_message(tr_data_deleted);
    }//multi_delete()

    //delete_3
    public function delete()
    {
        //$this->go_to_401($this->oPermission->is_not_delete()||$this->oSessionUser->is_not_dataowner());
        $this->go_to_401($this->oPermission->is_not_delete());
        $this->isError = FALSE;
        //Si ocurre un error se guarda en isError
        if($this->is_multidelete())
            $this->multi_delete();
        else
            $this->single_delete();
        //Si no ocurrio errores en el intento de borrado
        if(!$this->isError)
            $this->go_to_after_succes_cud();
        else//delete ok
            $this->go_to_list();
    }	//delete()
//</editor-fold>

//<editor-fold defaultstate="collapsed" desc="QUARANTINE">
    //quarantine_1
    protected function single_quarantine()
    {
        $id = $this->get_get("id");
        if($id)
        {
            $this->oSuspicionHead->set_id($id);
            $this->oSuspicionHead->autoquarantine();
            if($this->oSuspicionHead->is_error())
                    $this->set_session_message(tr_error_trying_to_delete);
            else
                    $this->set_session_message(tr_data_deleted);
        }//else no id
        else
            $this->set_session_message(tr_error_key_not_supplied,"e");
    }//single_quarantine()

    //quarantine_2
    protected function multi_quarantine()
    {
        $this->isError = FALSE;
        //Intenta recuperar pkeys sino pasa a id, y en ultimo caso lo que se ha pasado por parametro
        $arKeys = $this->get_listkeys();
        foreach($arKeys as $sKey)
        {
            $id = $sKey;
            $this->oSuspicionHead->set_id($id);
            $this->oSuspicionHead->autoquarantine();
            if($this->oSuspicionHead->is_error())
            {
                    $isError = true;
                    $this->set_session_message(tr_error_trying_to_delete,"e");
            }
        }
        if(!$isError)
            $this->set_session_message(tr_data_deleted);
    }//multi_quarantine()

    //quarantine_3
    public function quarantine()
    {
        //$this->go_to_401($this->oPermission->is_not_quarantine()||$this->oSessionUser->is_not_dataowner());
        $this->go_to_401($this->oPermission->is_not_quarantine());
        if($this->is_multiquarantine())
            $this->multi_quarantine();
        else
            $this->single_quarantine();
        $this->go_to_list();
        if(!$this->isError)
            $this-go_to_after_succes_cud();
        else //quarantine ok
            $this->go_to_list();
    }//quarantine()

//</editor-fold>

//<editor-fold defaultstate="collapsed" desc="MULTIASSIGN">
    //multiassign_1
    protected function build_multiassign_buttons()
    {
        $arOpButtons = array();
        $arOpButtons["filters"]=array("href"=>"javascript:reset_filters();","icon"=>"awe-magic","innerhtml"=>tr_sss_clear_filters);
        $arOpButtons["reload"]=array("href"=>"javascript:TfwControl.form_submit();","icon"=>"awe-refresh","innerhtml"=>tr_sss_refresh);
        $arOpButtons["multiadd"]=array("href"=>"javascript:multiadd();","icon"=>"awe-external-link","innerhtml"=>tr_sss_multiadd);
        $arOpButtons["closeme"]=array("href"=>"javascript:closeme();","icon"=>"awe-remove-sign","innerhtml"=>tr_sss_closeme);
        $oOpButtons = new AppHelperButtontabs(tr_sss_entities);
        $oOpButtons->set_tabs($arOpButtons);
        return $oOpButtons;
    }//build_multiassign_buttons()

    //multiassign_2
    protected function load_config_multiassign_filters()
    {
        //id
        $this->set_filter("id","txtId",array("operator"=>"like"));
        //code_erp
        //$this->set_filter("code_erp","txtCodeErp",array("operator"=>"like"));
        //id_transfer = TBOSERV.OBIDGIRO
        $this->set_filter("id_transfer","txtIdTransfer");
        //id_isr = TBOSERV.OBIDOPER
        $this->set_filter("id_isr","txtIdIsr");
        //amount
        $this->set_filter("amount","txtAmount",array("operator"=>"like"));
        //amount_cash
        $this->set_filter("amount_cash","txtAmountCash",array("operator"=>"like"));
        //filial_name
        $this->set_filter("filial_name","txtFilialName",array("operator"=>"like"));
        //notes
        $this->set_filter("notes","txtNotes",array("operator"=>"like"));
        //observations
        $this->set_filter("observations","txtObservations",array("operator"=>"like"));
        //user_creation
        $this->set_filter("user_creation","txtUserCreation",array("operator"=>"like"));
        //date_creation
        $this->set_filter("date_creation","datDateCreation",array("operator"=>"like"));
        //hour_creation
        $this->set_filter("hour_creation","txtHourCreation",array("operator"=>"like"));
        //status
        $this->set_filter("status","selStatus",array("operator"=>"like"));
        //type_char
        $this->set_filter("type_char","txtTypeChar",array("operator"=>"like"));
        //description
        //$this->set_filter("description","txtDescription",array("operator"=>"like"));
        //path_logo
        $this->set_filter("path_logo","txtPathLogo",array("operator"=>"like"));
        //office_name
        $this->set_filter("office_name","txtOfficeName",array("operator"=>"like"));
        //number
        $this->set_filter("number","txtNumber",array("operator"=>"like"));
    }//load_config_multiassign_filters()

    //multiassign_3
    protected function get_multiassign_filters()
    {
        //CAMPOS
        $arFields = array();
        //id
        $oAuxField = new HelperInputText("txtId","txtId");
        $oAuxField->set_value($this->get_post("txtId"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtId",tr_sss_fil_id));
        $arFields[] = $oAuxWrapper;
        //code_erp
        //$oAuxField = new HelperInputText("txtCodeErp","txtCodeErp");
        //$oAuxField->set_value($this->get_post("txtCodeErp"));
        //$oAuxField->on_entersubmit();
        //$oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtCodeErp",tr_sss_fil_code_erp));
        //$arFields[] = $oAuxWrapper;
//        //id_transfer = TBOSERV.OBIDGIRO
//        $oTransfer = new ModelTabgiros();
//        $arOptions = $oTransfer->get_picklist();
//        $oAuxField = new HelperSelect($arOptions,"txtIdTransfer","txtIdTransfer");
//        $oAuxField->set_value_to_select($this->get_post("txtIdTransfer"));
//        $oAuxField->set_postback();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtIdTransfer",tr_sss_fil_id_transfer));
//        $arFields[] = $oAuxWrapper;
        //id_isr = TBOSERV.OBIDOPER
        $oTbobserv = new ModelTbobserv();
        $arOptions = $oTbobserv->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"txtIdIsr","txtIdIsr");
        $oAuxField->set_value_to_select($this->get_post("txtIdIsr"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtIdIsr",tr_sss_fil_id_isr));
        $arFields[] = $oAuxWrapper;
        //amount
        $oAuxField = new HelperInputText("txtAmount","txtAmount");
        $oAuxField->set_value($this->get_post("txtAmount"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtAmount",tr_sss_fil_amount));
        $arFields[] = $oAuxWrapper;
        //amount_cash
        $oAuxField = new HelperInputText("txtAmountCash","txtAmountCash");
        $oAuxField->set_value($this->get_post("txtAmountCash"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtAmountCash",tr_sss_fil_amount_cash));
        $arFields[] = $oAuxWrapper;
        //filial_name
        $oAuxField = new HelperInputText("txtFilialName","txtFilialName");
        $oAuxField->set_value($this->get_post("txtFilialName"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtFilialName",tr_sss_fil_filial_name));
        $arFields[] = $oAuxWrapper;
        //notes
        $oAuxField = new HelperInputText("txtNotes","txtNotes");
        $oAuxField->set_value($this->get_post("txtNotes"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtNotes",tr_sss_fil_notes));
        $arFields[] = $oAuxWrapper;
        //observations
        $oAuxField = new HelperInputText("txtObservations","txtObservations");
        $oAuxField->set_value($this->get_post("txtObservations"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtObservations",tr_sss_fil_observations));
        $arFields[] = $oAuxWrapper;
        //user_creation
        $oAuxField = new HelperInputText("txtUserCreation","txtUserCreation");
        $oAuxField->set_value($this->get_post("txtUserCreation"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtUserCreation",tr_sss_fil_user_creation));
        $arFields[] = $oAuxWrapper;
        //date_creation
        $oAuxField = new HelperInputText("datDateCreation","datDateCreation");
        $oAuxField->set_value($this->get_post("datDateCreation"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("datDateCreation",tr_sss_fil_date_creation));
        $arFields[] = $oAuxWrapper;
        //hour_creation
        $oAuxField = new HelperInputText("txtHourCreation","txtHourCreation");
        $oAuxField->set_value($this->get_post("txtHourCreation"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtHourCreation",tr_sss_fil_hour_creation));
        $arFields[] = $oAuxWrapper;
        //status
        $oAuxField = new HelperInputText("selStatus","selStatus");
        $oAuxField->set_value($this->get_post("selStatus"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selStatus",tr_sss_fil_status));
        $arFields[] = $oAuxWrapper;
        //type_char
        $oAuxField = new HelperInputText("txtTypeChar","txtTypeChar");
        $oAuxField->set_value($this->get_post("txtTypeChar"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtTypeChar",tr_sss_fil_type_char));
        $arFields[] = $oAuxWrapper;
        //description
        //$oAuxField = new HelperInputText("txtDescription","txtDescription");
        //$oAuxField->set_value($this->get_post("txtDescription"));
        //$oAuxField->on_entersubmit();
        //$oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtDescription",tr_sss_fil_description));
        //$arFields[] = $oAuxWrapper;
        //path_logo
        $oAuxField = new HelperInputText("txtPathLogo","txtPathLogo");
        $oAuxField->set_value($this->get_post("txtPathLogo"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtPathLogo",tr_sss_fil_path_logo));
        $arFields[] = $oAuxWrapper;
        //office_name
        $oAuxField = new HelperInputText("txtOfficeName","txtOfficeName");
        $oAuxField->set_value($this->get_post("txtOfficeName"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtOfficeName",tr_sss_fil_office_name));
        $arFields[] = $oAuxWrapper;
        //number
        $oAuxField = new HelperInputText("txtNumber","txtNumber");
        $oAuxField->set_value($this->get_post("txtNumber"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtNumber",tr_sss_fil_number));
        $arFields[] = $oAuxWrapper;
        return $arFields;
    }//get_multiassign_filters()

    //multiassign_4
    protected function set_multiassignfilters_from_post()
    {
        //id
        $this->set_filter_value("id",$this->get_post("txtId"));
        //code_erp
        //$this->set_filter_value("code_erp",$this->get_post("txtCodeErp"));
        //id_transfer = TBOSERV.OBIDGIRO
        $this->set_filter_value("id_transfer",$this->get_post("txtIdTransfer"));
        //id_isr = TBOSERV.OBIDOPER
        $this->set_filter_value("id_isr",$this->get_post("txtIdIsr"));
        //amount
        $this->set_filter_value("amount",$this->get_post("txtAmount"));
        //amount_cash
        $this->set_filter_value("amount_cash",$this->get_post("txtAmountCash"));
        //filial_name
        $this->set_filter_value("filial_name",$this->get_post("txtFilialName"));
        //notes
        $this->set_filter_value("notes",$this->get_post("txtNotes"));
        //observations
        $this->set_filter_value("observations",$this->get_post("txtObservations"));
        //user_creation
        $this->set_filter_value("user_creation",$this->get_post("txtUserCreation"));
        //date_creation
        $this->set_filter_value("date_creation",$this->get_post("datDateCreation"));
        //hour_creation
        $this->set_filter_value("hour_creation",$this->get_post("txtHourCreation"));
        //status
        $this->set_filter_value("status",$this->get_post("selStatus"));
        //type_char
        $this->set_filter_value("type_char",$this->get_post("txtTypeChar"));
        //description
        //$this->set_filter_value("description",$this->get_post("txtDescription"));
        //path_logo
        $this->set_filter_value("path_logo",$this->get_post("txtPathLogo"));
        //office_name
        $this->set_filter_value("office_name",$this->get_post("txtOfficeName"));
        //number
        $this->set_filter_value("number",$this->get_post("txtNumber"));
    }//set_multiassignfilters_from_post()

    //multiassign_5
    protected function get_multiassign_columns()
    {
        $arColumns["id"] = tr_sss_col_id;
        //$arColumns["code_erp"] = tr_sss_col_code_erp;
        //$arColumns["id_transfer"] = tr_sss_col_id_transfer;
        $arColumns["transfer"] = tr_sss_col_id_transfer;
        //$arColumns["id_isr"] = tr_sss_col_id_isr;
        $arColumns["isr"] = tr_sss_col_id_isr;
        $arColumns["amount"] = tr_sss_col_amount;
        $arColumns["amount_cash"] = tr_sss_col_amount_cash;
        $arColumns["filial_name"] = tr_sss_col_filial_name;
        $arColumns["notes"] = tr_sss_col_notes;
        $arColumns["observations"] = tr_sss_col_observations;
        $arColumns["user_creation"] = tr_sss_col_user_creation;
        $arColumns["date_creation"] = tr_sss_col_date_creation;
        $arColumns["hour_creation"] = tr_sss_col_hour_creation;
        $arColumns["status"] = tr_sss_col_status;
        $arColumns["type_char"] = tr_sss_col_type_char;
        //$arColumns["description"] = tr_sss_col_description;
        $arColumns["path_logo"] = tr_sss_col_path_logo;
        $arColumns["office_name"] = tr_sss_col_office_name;
        $arColumns["number"] = tr_sss_col_number;
        return $arColumns;
    }//get_multiassign_columns()

    //multiassign_6
    public function multiassign()
    {
        $this->go_to_401($this->oPermission->is_not_pick());
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
        //build controls and add data to global arFilterControls and arFilterFields
        $arColumns = $this->get_multiassign_columns();
        //FILTERS
        //Indica los filtros que se recuperarán. Hace un $this->arFilters = arra(fieldname=>value=>..)
        $this->load_config_multiassign_filters();
        $oFilter = new ComponentFilter();
        $oFilter->set_fieldnames($this->get_filter_fieldnames());
        //Indica que no se guardara en sesion por nombre de campo sino por nombre de control
        //para esto es necesario respetar el estricto camelcase
        $oFilter->use_field_prefix();
        //Guarda en sesion y post los campos enviados, los de orden y página
        $oFilter->refresh();
        $this->set_multiassignfilters_from_post();
        $arObjFilter = $this->get_multiassign_filters();
        $this->oSuspicionHead->set_orderby($this->get_orderby());
        $this->oSuspicionHead->set_ordertype($this->get_ordertype());
        $this->oSuspicionHead->set_filters($this->get_filter_searchconfig());
        //hierarchy recover
        //$this->oSuspicionHead->set_select_user($this->oSessionUser->get_id());
        //RECOVER DATALIST
        $arList = $this->oSuspicionHead->get_select_all_ids();
        $iRequestPage = $this->get_post("selPage");
        $oPage = new ComponentPage($arList,$iRequestPage);
        $arList = $oPage->get_items_to_show();
        $arList = $this->oSuspicionHead->get_select_all_by_ids($arList);
        //TABLE
        //This method adds objects controls to search list form
        $oTableAssign = new HelperTableTyped($arList,$arColumns);
        $oTableAssign->set_fields($arObjFilter);
        $oTableAssign->set_module($this->get_current_module());
        $oTableAssign->add_class("table table-striped table-bordered table-condensed");
        $oTableAssign->set_keyfields(array("id"));
        $oTableAssign->set_orderby($this->get_orderby());
        $oTableAssign->set_orderby_type($this->get_ordertype());
        $oTableAssign->set_column_pickmultiple();//columna checks
        $oTableAssign->merge_pks();//claves separadas por coma
        $oTableAssign->set_column_picksingle();//crea funcion
        $oTableAssign->set_column_detail();//detail column
        //esto se define en el padre
        //$oTableAssign->set_multiassign(array("keys"=>array("k"=>1,"k2"=>2)));
        $oTableAssign->set_multiadd(array("keys"=>array("k"=>$this->get_get("k"),"k2"=>$this->get_get("k2"))));
        $oTableAssign->set_current_page($oPage->get_current());
        $oTableAssign->set_next_page($oPage->get_next());
        $oTableAssign->set_first_page($oPage->get_first());
        $oTableAssign->set_last_page($oPage->get_last());
        $oTableAssign->set_total_regs($oPage->get_total_regs());
        $oTableAssign->set_total_pages($oPage->get_total());
        //CRUD BUTTONS BAR
        $oOpButtons = new AppHelperButtontabs(tr_sss_entities);
        $oOpButtons->set_tabs($this->build_multiassign_buttons());
        $oJavascript = new HelperJavascript();
        $oJavascript->set_filters($this->get_filter_controls_id());
        $oJavascript->set_focusid("id_all");
        $this->oView->add_var($oJavascript,"oJavascript");
        $this->oView->set_layout("onecolumn");
        $this->oView->add_var($oOpButtons,"oOpButtons");
        $this->oView->add_var($oAlert,"oAlert");
        $this->oView->add_var($oTableAssign,"oTableAssign");
        $this->oView->show_page();
    }//multiassign()
//</editor-fold>

//<editor-fold defaultstate="collapsed" desc="SINGLEASSIGN">
    //singleassign_1
    protected function build_singleassign_buttons()
    {
        $arButTabs = array();
        $arButTabs["filters"]=array("href"=>"javascript:reset_filters();","icon"=>"awe-magic","innerhtml"=>tr_sss_clear_filters);
        $arButTabs["reload"]=array("href"=>"javascript:TfwControl.form_submit();","icon"=>"awe-refresh","innerhtml"=>tr_sss_refresh);
        $arButTabs["closeme"]=array("href"=>"javascript:closeme();","icon"=>"awe-remove-sign","innerhtml"=>tr_sss_closeme);
        return $arButTabs;
    }//build_singleassign_buttons()

    //singleassign_2
    protected function load_config_singleassign_filters()
    {
        //id
        $this->set_filter("id","txtId",array("operator"=>"like"));
        //code_erp
        //$this->set_filter("code_erp","txtCodeErp",array("operator"=>"like"));
        //id_transfer = TBOSERV.OBIDGIRO
        $this->set_filter("id_transfer","txtIdTransfer");
        //id_isr = TBOSERV.OBIDOPER
        $this->set_filter("id_isr","txtIdIsr");
        //amount
        $this->set_filter("amount","txtAmount",array("operator"=>"like"));
        //amount_cash
        $this->set_filter("amount_cash","txtAmountCash",array("operator"=>"like"));
        //filial_name
        $this->set_filter("filial_name","txtFilialName",array("operator"=>"like"));
        //notes
        $this->set_filter("notes","txtNotes",array("operator"=>"like"));
        //observations
        $this->set_filter("observations","txtObservations",array("operator"=>"like"));
        //user_creation
        $this->set_filter("user_creation","txtUserCreation",array("operator"=>"like"));
        //date_creation
        $this->set_filter("date_creation","datDateCreation",array("operator"=>"like"));
        //hour_creation
        $this->set_filter("hour_creation","txtHourCreation",array("operator"=>"like"));
        //status
        $this->set_filter("status","selStatus",array("operator"=>"like"));
        //type_char
        $this->set_filter("type_char","txtTypeChar",array("operator"=>"like"));
        //description
        //$this->set_filter("description","txtDescription",array("operator"=>"like"));
        //path_logo
        $this->set_filter("path_logo","txtPathLogo",array("operator"=>"like"));
        //office_name
        $this->set_filter("office_name","txtOfficeName",array("operator"=>"like"));
        //number
        $this->set_filter("number","txtNumber",array("operator"=>"like"));
    }//load_config_singleassign_filters()

    //singleassign_3
    protected function get_singleassign_filters()
    {
        //CAMPOS
        $arFields = array();
        //id
        $oAuxField = new HelperInputText("txtId","txtId");
        $oAuxField->set_value($this->get_post("txtId"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtId",tr_sss_fil_id));
        $arFields[] = $oAuxWrapper;
        //code_erp
        //$oAuxField = new HelperInputText("txtCodeErp","txtCodeErp");
        //$oAuxField->set_value($this->get_post("txtCodeErp"));
        //$oAuxField->on_entersubmit();
        //$oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtCodeErp",tr_sss_fil_code_erp));
        //$arFields[] = $oAuxWrapper;
//        //id_transfer = TBOSERV.OBIDGIRO
//        $oTransfer = new ModelTabgiros();
//        $arOptions = $oTransfer->get_picklist();
//        $oAuxField = new HelperSelect($arOptions,"txtIdTransfer","txtIdTransfer");
//        $oAuxField->set_value_to_select($this->get_post("txtIdTransfer"));
//        $oAuxField->set_postback();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtIdTransfer",tr_sss_fil_id_transfer));
//        $arFields[] = $oAuxWrapper;
        //id_isr = TBOSERV.OBIDOPER
        $oTbobserv = new ModelTbobserv();
        $arOptions = $oTbobserv->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"txtIdIsr","txtIdIsr");
        $oAuxField->set_value_to_select($this->get_post("txtIdIsr"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtIdIsr",tr_sss_fil_id_isr));
        $arFields[] = $oAuxWrapper;
        //amount
        $oAuxField = new HelperInputText("txtAmount","txtAmount");
        $oAuxField->set_value($this->get_post("txtAmount"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtAmount",tr_sss_fil_amount));
        $arFields[] = $oAuxWrapper;
        //amount_cash
        $oAuxField = new HelperInputText("txtAmountCash","txtAmountCash");
        $oAuxField->set_value($this->get_post("txtAmountCash"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtAmountCash",tr_sss_fil_amount_cash));
        $arFields[] = $oAuxWrapper;
        //filial_name
        $oAuxField = new HelperInputText("txtFilialName","txtFilialName");
        $oAuxField->set_value($this->get_post("txtFilialName"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtFilialName",tr_sss_fil_filial_name));
        $arFields[] = $oAuxWrapper;
        //notes
        $oAuxField = new HelperInputText("txtNotes","txtNotes");
        $oAuxField->set_value($this->get_post("txtNotes"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtNotes",tr_sss_fil_notes));
        $arFields[] = $oAuxWrapper;
        //observations
        $oAuxField = new HelperInputText("txtObservations","txtObservations");
        $oAuxField->set_value($this->get_post("txtObservations"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtObservations",tr_sss_fil_observations));
        $arFields[] = $oAuxWrapper;
        //user_creation
        $oAuxField = new HelperInputText("txtUserCreation","txtUserCreation");
        $oAuxField->set_value($this->get_post("txtUserCreation"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtUserCreation",tr_sss_fil_user_creation));
        $arFields[] = $oAuxWrapper;
        //date_creation
        $oAuxField = new HelperInputText("datDateCreation","datDateCreation");
        $oAuxField->set_value($this->get_post("datDateCreation"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("datDateCreation",tr_sss_fil_date_creation));
        $arFields[] = $oAuxWrapper;
        //hour_creation
        $oAuxField = new HelperInputText("txtHourCreation","txtHourCreation");
        $oAuxField->set_value($this->get_post("txtHourCreation"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtHourCreation",tr_sss_fil_hour_creation));
        $arFields[] = $oAuxWrapper;
        //status
        $oAuxField = new HelperInputText("selStatus","selStatus");
        $oAuxField->set_value($this->get_post("selStatus"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selStatus",tr_sss_fil_status));
        $arFields[] = $oAuxWrapper;
        //type_char
        $oAuxField = new HelperInputText("txtTypeChar","txtTypeChar");
        $oAuxField->set_value($this->get_post("txtTypeChar"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtTypeChar",tr_sss_fil_type_char));
        $arFields[] = $oAuxWrapper;
        //description
        //$oAuxField = new HelperInputText("txtDescription","txtDescription");
        //$oAuxField->set_value($this->get_post("txtDescription"));
        //$oAuxField->on_entersubmit();
        //$oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtDescription",tr_sss_fil_description));
        //$arFields[] = $oAuxWrapper;
        //path_logo
        $oAuxField = new HelperInputText("txtPathLogo","txtPathLogo");
        $oAuxField->set_value($this->get_post("txtPathLogo"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtPathLogo",tr_sss_fil_path_logo));
        $arFields[] = $oAuxWrapper;
        //office_name
        $oAuxField = new HelperInputText("txtOfficeName","txtOfficeName");
        $oAuxField->set_value($this->get_post("txtOfficeName"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtOfficeName",tr_sss_fil_office_name));
        $arFields[] = $oAuxWrapper;
        //number
        $oAuxField = new HelperInputText("txtNumber","txtNumber");
        $oAuxField->set_value($this->get_post("txtNumber"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtNumber",tr_sss_fil_number));
        $arFields[] = $oAuxWrapper;
        return $arFields;
    }//get_singleassign_filters()

    //singleassign_4
    protected function set_singleassignfilters_from_post()
    {
        //id
        $this->set_filter_value("id",$this->get_post("txtId"));
        //code_erp
        //$this->set_filter_value("code_erp",$this->get_post("txtCodeErp"));
        //id_transfer = TBOSERV.OBIDGIRO
        $this->set_filter_value("id_transfer",$this->get_post("txtIdTransfer"));
        //id_isr = TBOSERV.OBIDOPER
        $this->set_filter_value("id_isr",$this->get_post("txtIdIsr"));
        //amount
        $this->set_filter_value("amount",$this->get_post("txtAmount"));
        //amount_cash
        $this->set_filter_value("amount_cash",$this->get_post("txtAmountCash"));
        //filial_name
        $this->set_filter_value("filial_name",$this->get_post("txtFilialName"));
        //notes
        $this->set_filter_value("notes",$this->get_post("txtNotes"));
        //observations
        $this->set_filter_value("observations",$this->get_post("txtObservations"));
        //user_creation
        $this->set_filter_value("user_creation",$this->get_post("txtUserCreation"));
        //date_creation
        $this->set_filter_value("date_creation",$this->get_post("datDateCreation"));
        //hour_creation
        $this->set_filter_value("hour_creation",$this->get_post("txtHourCreation"));
        //status
        $this->set_filter_value("status",$this->get_post("selStatus"));
        //type_char
        $this->set_filter_value("type_char",$this->get_post("txtTypeChar"));
        //description
        //$this->set_filter_value("description",$this->get_post("txtDescription"));
        //path_logo
        $this->set_filter_value("path_logo",$this->get_post("txtPathLogo"));
        //office_name
        $this->set_filter_value("office_name",$this->get_post("txtOfficeName"));
        //number
        $this->set_filter_value("number",$this->get_post("txtNumber"));
    }//set_singleassignfilters_from_post()

    //singleassign_5
    protected function get_singleassign_columns()
    {
        $arColumns["id"] = tr_sss_col_id;
        //$arColumns["code_erp"] = tr_sss_col_code_erp;
        //$arColumns["id_transfer"] = tr_sss_col_id_transfer;
        $arColumns["transfer"] = tr_sss_col_id_transfer;
        //$arColumns["id_isr"] = tr_sss_col_id_isr;
        $arColumns["isr"] = tr_sss_col_id_isr;
        $arColumns["amount"] = tr_sss_col_amount;
        $arColumns["amount_cash"] = tr_sss_col_amount_cash;
        $arColumns["filial_name"] = tr_sss_col_filial_name;
        $arColumns["notes"] = tr_sss_col_notes;
        $arColumns["observations"] = tr_sss_col_observations;
        $arColumns["user_creation"] = tr_sss_col_user_creation;
        $arColumns["date_creation"] = tr_sss_col_date_creation;
        $arColumns["hour_creation"] = tr_sss_col_hour_creation;
        $arColumns["status"] = tr_sss_col_status;
        $arColumns["type_char"] = tr_sss_col_type_char;
        //$arColumns["description"] = tr_sss_col_description;
        $arColumns["path_logo"] = tr_sss_col_path_logo;
        $arColumns["office_name"] = tr_sss_col_office_name;
        $arColumns["number"] = tr_sss_col_number;
        return $arColumns;
    }//get_singleassign_columns()

    //singleassign_6
    public function singleassign()
    {
        $this->go_to_401($this->oPermission->is_not_pick());
        $oAlert = new AppHelperAlertdiv();
        $oAlert->use_close_button();
        $sMessage = $this->get_session_message($sMessage);
        if($sMessage) $oAlert->set_title($sMessage);
        $sMessage = $this->get_session_message($sMessage,"e");
        if($sMessage)
        {
            $oAlert->set_type();
            $oAlert->set_title($sMessage);
        }
        //build controls and add data to global arFilterControls and arFilterFields
        $arColumns = $this->get_singleassign_columns();
        //Indica los filtros que se recuperarán. Hace un $this->arFilters = arra(fieldname=>value=>..)
        $this->load_config_singleassign_filters();
        $oFilter = new ComponentFilter();
        $oFilter->set_fieldnames($this->get_filter_fieldnames());
        //Indica que no se guardara en sesion por nombre de campo sino por nombre de control
        //para esto es necesario respetar el estricto camelcase
        $oFilter->use_field_prefix();
        //Guarda en sesion y post los campos enviados, los de orden y página
        $oFilter->refresh();
        $this->set_singleassignfilters_from_post();
        $arObjFilter = $this->get_singleassign_filters();
        $this->oSuspicionHead->set_orderby($this->get_orderby());
        $this->oSuspicionHead->set_ordertype($this->get_ordertype());
        $this->oSuspicionHead->set_filters($this->get_filter_searchconfig());
        $arList = $this->oSuspicionHead->get_select_all_ids();
        $iRequestPage = $this->get_post("selPage");
        $oPage = new ComponentPage($arList,$iRequestPage);
        $arList = $oPage->get_items_to_show();
        $arList = $this->oSuspicionHead->get_select_all_by_ids($arList);
        //TABLE
        $oTableAssign = new HelperTableTyped($arList,$arColumns);
        $oTableAssign->set_fields($arObjFilter);
        $oTableAssign->set_module($this->get_current_module());
        $oTableAssign->add_class("table table-striped table-bordered table-condensed");
        $oTableAssign->set_keyfields(array("id"));
        $oTableAssign->set_orderby($this->get_orderby());
        $oTableAssign->set_orderby_type($this->get_ordertype());
        $oTableAssign->set_column_picksingle();
        $oTableAssign->set_singleadd(array("destkey"=>"txtCode","destdesc"=>"Desc","keys"=>"id","descs"=>"description,bo_login","close"=>1));
        $oTableAssign->set_current_page($oPage->get_current());
        $oTableAssign->set_next_page($oPage->get_next());
        $oTableAssign->set_first_page($oPage->get_first());
        $oTableAssign->set_last_page($oPage->get_last());
        $oTableAssign->set_total_regs($oPage->get_total_regs());
        $oTableAssign->set_total_pages($oPage->get_total());
        //BARRA CRUD
        $oOpButtons = new AppHelperButtontabs(tr_sss_entities);
        $oOpButtons->set_tabs($this->build_singleassign_buttons());
        //JAVASCRIPT
        $oJavascript = new HelperJavascript();
        $oJavascript->set_filters($this->get_filter_controls_id());
        $oJavascript->set_focusid("id_all");
        //TO VIEW
        $this->oView->add_var($oJavascript,"oJavascript");
        $this->oView->set_layout("onecolumn");
        $this->oView->add_var($oOpButtons,"oOpButtons");
        $this->oView->add_var($oAlert,"oAlert");
        $this->oView->add_var($oTableAssign,"oTableAssign");
        $this->oView->show_page();
    }//singleassign()
//</editor-fold>

}//end controller
