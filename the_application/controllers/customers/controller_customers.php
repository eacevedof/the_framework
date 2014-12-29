<?php
/**
 * @author Module Builder 1.0.0
 * @link www.eduardoaf.com
 * @version 1.0.6b
 * @name ControllerCustomer
 * @file controller_customer.php    
 * @date 01-11-2014 13:03 (SPAIN)
 * @observations: 
 */
//TFW
import_component("page,validate,filter");
import_helper("form,form_fieldset,form_legend,input_text,label,anchor,table,table_typed");
import_helper("input_password,button_basic,raw,div,javascript");
//APP
import_model("user,seller,customer");
import_appmain("controller,view,behaviour");
import_appbehaviour("picklist");
import_apphelper("listactionbar,controlgroup,formactions,buttontabs,formhead,alertdiv,breadscrumbs,headertabs");

class ControllerCustomers extends TheApplicationController
{
    /**
     * @var ModelCustomer MainControllerObject
     */
    private $oCustomer;
    
    public function __construct()
    {
        $this->sModuleName = "customers";
        //console,pgpage,clientdevice,sesuser,aftersucces,currop  | log,session,permission,view
        parent::__construct($this->sModuleName);
        
        $this->oCustomer = new ModelCustomer();
        $this->oCustomer->set_platform($this->oSessionUser->get_platform());
        if($this->is_inget("id"))
        {
            $this->oCustomer->set_id($this->get_get("id"));
            $this->oCustomer->load_by_id();
        }
        $this->oSessionUser->set_dataowner_table($this->oCustomer->get_table_name());
        $this->oSessionUser->set_dataowner_tablefield("id");
        $this->oSessionUser->set_dataowner_keys(array("id"=>$this->oCustomer->get_id()));
        //pr("constr");
        //bugss("customersget_list");
        //bugp();
    }

//<editor-fold defaultstate="collapsed" desc="LIST">
    //list_1
    private function build_list_scrumbs()
    {   
        $arLinks = array();
        //$sUrlTab = $this->build_url($this->sModuleName);;
        $sUrlTab = $this->build_url($this->sModuleName);
        $arLinks["list"]=array("href"=>$sUrlTab,"innerhtml"=>tr_entities);
        $oScrumbs = new AppHelperBreadscrumbs($arLinks);
        return $oScrumbs;
    }//build_list_scrumbs()
    
    //list_2
    private function build_list_tabs()
    {
        $arTabs = array();
        //$sUrlTab = $this->build_url($this->sModuleName,NULL,"update","id=".$this->get_get("id_parent_foreign"));
        //$arTabs["detail"]=array("href"=>$sUrlTab,"innerhtml"=>"Detail");
        //$sUrlTab = $this->build_url($this->sModuleName,"foreignamodule","get_list_by_foreign","id_foreign=".$this->get_get("id_parent_foreign"));
        //$arTabs["lines"]=array("href"=>$sUrlTab,"innerhtml"=>"foreignmodule");
        $oTabs = new AppHelperHeadertabs($arTabs,"foreign");
        return $oTabs;
    }
    
    //list_3
    private function build_listoperation_buttons()
    {
        $arOpButtons = array();
        $arOpButtons["filters"]=array("href"=>"javascript:reset_filters();","icon"=>"awe-magic","innerhtml"=>tr_clear_filters);
        $arOpButtons["reload"]=array("href"=>"javascript:TfwControl.form_submit();","icon"=>"awe-refresh","innerhtml"=>tr_refresh);
        if($this->oPermission->is_insert())
            $arOpButtons["insert"]=array("href"=>$this->build_url($this->sModuleName,NULL,"insert"),"icon"=>"awe-plus","innerhtml"=>tr_new);
        if($this->oPermission->is_quarantine())
            $arOpButtons["multiquarantine"]=array("href"=>"javascript:multi_quarantine();","icon"=>"awe-remove","innerhtml"=>tr_quarantine);
//        if($this->oPermission->is_delete())
//            $arOpButtons["multidelete"]=array("href"=>"javascript:multi_delete();","icon"=>"awe-remove","innerhtml"=>tr_delete_selection);
        //crea ventana
        //$arOpButtons["multiassign"]=array("href"=>"javascript:multiassign_window('customer',null,'multiassign','customer','addexternaldata');","icon"=>"awe-external-link","innerhtml"=>tr_asign_selection);
        //$arOpButtons["singleassign"]=array("href"=>"javascript:single_pick('customer','singleassign','txtI','txtI');","icon"=>"awe-external-link","innerhtml"=>tr_asign_selection);
        $oOpButtons = new AppHelperButtontabs(tr_entities);
        $oOpButtons->set_tabs($arOpButtons);
        return $oOpButtons;
    }//build_listoperation_buttons()

    //list_4
    private function load_config_list_filters()
    {
	$this->set_filter("id","txtId",array("operator"=>"like"));
	$this->set_filter("code_erp","txtCodeErp",array("operator"=>"like"));
	$this->set_filter("id_seller","selIdSeller");
	$this->set_filter("first_name","txtFirstName",array("operator"=>"like"));
	$this->set_filter("is_validated","selIsValidated");
	$this->set_filter("last_name","txtLastName",array("operator"=>"like"));
	$this->set_filter("company","txtCompany",array("operator"=>"like"));
//        $this->set_filter("id_country","txtIdCountry",array("operator"=>"like"));
//        $this->set_filter("id_type","txtIdType",array("operator"=>"like"));
//        $this->set_filter("last_sale","txtLastSale",array("operator"=>"like"));
//        $this->set_filter("contact","txtContact",array("operator"=>"like"));
//        $this->set_filter("contact_phone","txtContactPhone",array("operator"=>"like"));
//        $this->set_filter("is_robinson_email","selIsRobinsonEmail",array("operator"=>"like"));
//        $this->set_filter("description","txtDescription",array("operator"=>"like"));
//        $this->set_filter("address","txtAddress",array("operator"=>"like"));
//        $this->set_filter("email","txtEmail",array("operator"=>"like"));
//        $this->set_filter("phone_1","txtPhone1",array("operator"=>"like"));
//        $this->set_filter("phone_2","txtPhone2",array("operator"=>"like"));
    }//load_config_list_filters()
    
    //list_5
    private function set_listfilters_from_post()
    {
	$this->set_filter_value("id",$this->get_post("txtId"));
	$this->set_filter_value("code_erp",$this->get_post("txtCodeErp"));
	$this->set_filter_value("id_seller",$this->get_post("selIdSeller"));
	$this->set_filter_value("first_name",$this->get_post("txtFirstName"));
	$this->set_filter_value("is_validated",$this->get_post("selIsValidated"));
	$this->set_filter_value("last_name",$this->get_post("txtLastName"));
	$this->set_filter_value("company",$this->get_post("txtCompany"));
//        $this->set_filter_value("id_country",$this->get_post("txtIdCountry"));
//        $this->set_filter_value("id_type",$this->get_post("txtIdType"));
//        $this->set_filter_value("last_sale",$this->get_post("txtLastSale"));
//        $this->set_filter_value("contact",$this->get_post("txtContact"));
//        $this->set_filter_value("contact_phone",$this->get_post("txtContactPhone"));
//        $this->set_filter_value("is_robinson_email",$this->get_post("selIsRobinsonEmail"));
//        $this->set_filter_value("description",$this->get_post("txtDescription"));
//        $this->set_filter_value("address",$this->get_post("txtAddress"));
//        $this->set_filter_value("email",$this->get_post("txtEmail"));
//        $this->set_filter_value("phone_1",$this->get_post("txtPhone1"));
//        $this->set_filter_value("phone_2",$this->get_post("txtPhone2"));
         
    }//set_listfilters_from_post
        
    //list_6
    private function get_list_filters()
    {
        //CAMPOS
        //id
        $oAuxField = new HelperInputText("txtId","txtId");
        $oAuxField->set_value($this->get_post("txtId"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtId",tr_fil_id));
        $arFields[] = $oAuxWrapper;
        //code_erp
//        $oAuxField = new HelperInputText("txtCodeErp","txtCodeErp");
//        $oAuxField->set_value($this->get_post("txtCodeErp"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtCodeErp",tr_fil_code_erp));
//        $arFields[] = $oAuxWrapper; 
        //company
        $oAuxField = new HelperInputText("txtCompany","txtCompany");
        $oAuxField->set_value($this->get_post("txtCompany"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtCompany",tr_fil_company));
        $arFields[] = $oAuxWrapper;
        //first_name
        $oAuxField = new HelperInputText("txtFirstName","txtFirstName");
        $oAuxField->set_value($this->get_post("txtFirstName"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtFirstName",tr_fil_first_name));
        $arFields[] = $oAuxWrapper;
        //last_name
        $oAuxField = new HelperInputText("txtLastName","txtLastName");
        $oAuxField->set_value($this->get_post("txtLastName"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtLastName",tr_fil_last_name));
        $arFields[] = $oAuxWrapper;     
        //is_validated
//        $oBehaviour = new AppBehaviourPicklist();
//        $arOptions = $oBehaviour->get_boolean();
//        $oAuxField = new HelperSelect($arOptions,"selIsValidated","selIsValidated");
//        $oAuxField->set_value_to_select($this->get_post("selIsValidated"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIsValidated",tr_fil_is_validated));
//        $arFields[] = $oAuxWrapper;
        //id_seller
        $oModelSeller = new ModelSeller();
        $oModelSeller->set_select_user($this->oSessionUser->get_id());
        $arOptions = $oModelSeller->get_picklist_hierarchy("seller","id");
        $oAuxField = new HelperSelect($arOptions,"selIdSeller","selIdSeller");
        $oAuxField->set_postback();
        //$idUserSeller = $this->oSessionUser->get_his_id_seller();
        $oAuxField->set_value_to_select($this->get_post("selIdSeller"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdSeller",tr_fil_id_seller));
        $arFields[] = $oAuxWrapper;        
      //id_country
//        $oAuxField = new HelperInputText("txtIdCountry","txtIdCountry");
//        $oAuxField->set_value($this->get_post("txtIdCountry"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtIdCountry",tr_fil_id_country));
//        $arFields[] = $oAuxWrapper;        
//        //id_type
//        $oAuxField = new HelperInputText("txtIdType","txtIdType");
//        $oAuxField->set_value($this->get_post("txtIdType"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtIdType",tr_fil_id_type));
//        $arFields[] = $oAuxWrapper;
//        //last_sale
//        $oAuxField = new HelperInputText("txtLastSale","txtLastSale");
//        $oAuxField->set_value($this->get_post("txtLastSale"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtLastSale",tr_fil_last_sale));
//        $arFields[] = $oAuxWrapper;
//        //contact
//        $oAuxField = new HelperInputText("txtContact","txtContact");
//        $oAuxField->set_value($this->get_post("txtContact"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtContact",tr_fil_contact));
//        $arFields[] = $oAuxWrapper;
//        //contact_phone
//        $oAuxField = new HelperInputText("txtContactPhone","txtContactPhone");
//        $oAuxField->set_value($this->get_post("txtContactPhone"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtContactPhone",tr_fil_contact_phone));
//        $arFields[] = $oAuxWrapper;
//        //is_robinson_email
//        $oAuxField = new HelperInputText("selIsRobinsonEmail","selIsRobinsonEmail");
//        $oAuxField->set_value($this->get_post("selIsRobinsonEmail"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIsRobinsonEmail",tr_fil_is_robinson_email));
//        $arFields[] = $oAuxWrapper;        
//        //description
//        $oAuxField = new HelperInputText("txtDescription","txtDescription");
//        $oAuxField->set_value($this->get_post("txtDescription"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtDescription",tr_fil_description));
//        $arFields[] = $oAuxWrapper;        
//        //address
//        $oAuxField = new HelperInputText("txtAddress","txtAddress");
//        $oAuxField->set_value($this->get_post("txtAddress"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtAddress",tr_fil_address));
//        $arFields[] = $oAuxWrapper;
//        //email
//        $oAuxField = new HelperInputText("txtEmail","txtEmail");
//        $oAuxField->set_value($this->get_post("txtEmail"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtEmail",tr_fil_email));
//        $arFields[] = $oAuxWrapper;
//        //phone_1
//        $oAuxField = new HelperInputText("txtPhone1","txtPhone1");
//        $oAuxField->set_value($this->get_post("txtPhone1"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtPhone1",tr_fil_phone_1));
//        $arFields[] = $oAuxWrapper;
//        //phone_2
//        $oAuxField = new HelperInputText("txtPhone2","txtPhone2");
//        $oAuxField->set_value($this->get_post("txtPhone2"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtPhone2",tr_fil_phone_2));
//        $arFields[] = $oAuxWrapper;
        return $arFields;
    }//get_list_filters()

    //list_7
    private function get_list_columns()
    {
        $arColumns = array();
        $arColumns["id"] = tr_col_id;
        $arColumns["company"] = tr_col_company;
        $arColumns["address"] = tr_col_address;
        $arColumns["email"] = tr_col_email;
        $arColumns["phone_1"] = tr_col_phone_1;
        //$arColumns["id_seller"] = tr_col_id_seller;
        $arColumns["seller"] = tr_col_id_seller;
//        $arColumns["last_name"] = tr_col_last_name;
//        $arColumns["contact"] = tr_col_contact;
//        $arColumns["last_sale"] = tr_col_last_sale;
        //$arColumns["id_country"] = tr_col_id_country;
        //$arColumns["id_type"] = tr_col_id_type;
        //$arColumns["first_name"] = tr_col_first_name;
        //$arColumns["contact_phone"] = tr_col_contact_phone;
        //$arColumns["is_robinson_email"] = tr_col_is_robinson_email;
        //$arColumns["is_validated"] = tr_col_is_validated;        
        //$arColumns["phone_2"] = tr_col_phone_2;
        //$arColumns["code_erp"] = tr_col_code_erp;        
        return $arColumns;
    }//get_list_columns()

    //list_8
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
        $arColumns = $this->get_list_columns(); 
        //FILTERS
        $this->load_config_list_filters();
        $oFilter = new ComponentFilter();
        //pr("set_fieldnames");
        //bugss("customersget_list");
        $oFilter->set_fieldnames($this->get_filter_fieldnames());
        //Indica que no se guardara en sesion por nombre de campo sino por nombre de control
        //para esto es necesario respetar el estricto camelcase
        $oFilter->use_field_prefix();
        //Guarda en sesion y post los campos enviados, los de orden y página
        $oFilter->refresh();
        //pr("despues de refresh");
        //bugss("customersget_list");bugp();
        $this->set_listfilters_from_post();
        $arObjFilter = $this->get_list_filters();
        
        //RECOVER DATALIST
        $this->oCustomer->set_orderby($this->get_orderby());
        $this->oCustomer->set_ordertype($this->get_ordertype());
        //bug($this->get_filter_searchconfig());
        $this->oCustomer->set_filters($this->get_filter_searchconfig());
        //hierarchy user
        $this->oCustomer->set_select_user($this->oSessionUser->get_id());
        $arList = $this->oCustomer->get_select_all_ids();
        $iRequestPage = $this->get_post("selPage");
        $oPage = new ComponentPage($arList,$iRequestPage);
        $arList = $oPage->get_items_to_show();
        //bug($arList);
        $arList = $this->oCustomer->get_select_all_by_ids($arList);
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
        //COLUMNS CONFIG
        if($this->oPermission->is_quarantine()||$this->oPermission->is_delete())
            $oTableList->set_column_pickmultiple();//checks column
        if($this->oPermission->is_read())
            $oTableList->set_column_detail();//detail column
        //$oTableList->set_column_delete();
        if($this->oPermission->is_quarantine())
            $oTableList->set_column_quarantine();
        //if($this->oPermission->is_delete())
            //$oTableList->set_column_delete();
        //parametros a pasar al popup
        //$oTableList->set_multiassign(array("keys"=>array("k"=>1,"k2"=>2)));
        $oTableList->set_current_page($oPage->get_current());
        $oTableList->set_first_page($oPage->get_first());
        $oTableList->set_previous_page($oPage->get_previous());
        $oTableList->set_next_page($oPage->get_next());
        $oTableList->set_last_page($oPage->get_last());
        $oTableList->set_total_regs($oPage->get_total_regs());
        $oTableList->set_total_pages($oPage->get_total());
        //CRUD OPERATIONS BAR
        $oScrumbs = $this->build_list_scrumbs();
        $oTabs = $this->build_list_tabs();
        $oOpButtons = $this->build_listoperation_buttons();
        
        $oJavascript = new HelperJavascript();
        $oJavascript->set_filters($this->get_filter_controls_id());
        $oJavascript->set_focusid("id_all");
        
        $this->oView->add_var($oJavascript,"oJavascript");
        $this->oView->add_var($oScrumbs,"oScrumbs");
        $this->oView->add_var($oTabs,"oTabs");
        $this->oView->add_var($oOpButtons,"oOpButtons");
        $this->oView->add_var($oAlert,"oAlert");
        $this->oView->add_var($oTableList,"oTableList");
        $this->oView->show_page();
    }//get_list()
//</editor-fold>

//<editor-fold defaultstate="collapsed" desc="INSERT">
    //insert_1
    private function build_insert_scrumbs()
    {        
        $sUrlTab = $this->build_url($this->sModuleName);
        $arTabs["list"]=array("href"=>$sUrlTab,"innerhtml"=>tr_entities);
        
        $sUrlTab = $this->build_url($this->sModuleName,NULL,"insert");
        $arTabs["detail"]=array("href"=>$sUrlTab,"innerhtml"=>tr_entity_insert);
        
        $oScrumbs = new AppHelperBreadscrumbs($arTabs);        
        return $oScrumbs;
    }//build_insert_scrumbs()
    
    //insert_2
    private function build_insert_opbuttons()
    {
        $arOpButtons = array();
        $arOpButtons["list"]=array("href"=>$this->build_url($this->sModuleName),"icon"=>"awe-search","innerhtml"=>tr_list);
        $oOpButtons = new AppHelperButtontabs(tr_entities);
        $oOpButtons->set_tabs($arOpButtons);
        return $oOpButtons;
    }//build_insert_opbuttons()

    //insert_3
    private function get_insert_validate()
    {
        $arFieldsConfig = array();
        //$arFieldsConfig["id"] = array("controlid"=>"txtId","label"=>tr_ins_id,"length"=>9,"type"=>array("numeric","required"));
        $arFieldsConfig["id_seller"] = array("controlid"=>"selIdSeller","label"=>tr_ins_id_seller,"length"=>4,"type"=>array("required"));
        $arFieldsConfig["company"] = array("controlid"=>"txtCompany","label"=>tr_ins_company,"length"=>100,"type"=>array("required"));
        $arFieldsConfig["address"] = array("controlid"=>"txtAddress","label"=>tr_ins_address,"length"=>200,"type"=>array("required"));         
        $arFieldsConfig["first_name"] = array("controlid"=>"txtFirstName","label"=>tr_ins_first_name,"length"=>100,"type"=>array("required"));
        $arFieldsConfig["last_name"] = array("controlid"=>"txtLastName","label"=>tr_ins_last_name,"length"=>100,"type"=>array("required"));
        $arFieldsConfig["phone_1"] = array("controlid"=>"txtPhone1","label"=>tr_ins_phone_1,"length"=>15,"type"=>array("required"));
        $arFieldsConfig["contact"] = array("controlid"=>"txtContact","label"=>tr_ins_contact,"length"=>100,"type"=>array());
        $arFieldsConfig["contact_phone"] = array("controlid"=>"txtContactPhone","label"=>tr_ins_contact_phone,"length"=>15,"type"=>array()); 
        $arFieldsConfig["email"] = array("controlid"=>"txtEmail","label"=>tr_ins_email,"length"=>50,"type"=>array());
        $arFieldsConfig["phone_2"] = array("controlid"=>"txtPhone2","label"=>tr_ins_phone_2,"length"=>15,"type"=>array());         

//        $arFieldsConfig["id_country"] = array("controlid"=>"txtIdCountry","label"=>tr_ins_id_country,"length"=>9,"type"=>array("numeric"));
//        $arFieldsConfig["id_type"] = array("controlid"=>"txtIdType","label"=>tr_ins_id_type,"length"=>9,"type"=>array("numeric"));
//        $arFieldsConfig["last_sale"] = array("controlid"=>"txtLastSale","label"=>tr_ins_last_sale,"length"=>9,"type"=>array("numeric"));
//        $arFieldsConfig["insert_date"] = array("controlid"=>"txtCreateDate","label"=>tr_ins_insert_date,"length"=>14,"type"=>array());
//        $arFieldsConfig["update_date"] = array("controlid"=>"txtModifyDate","label"=>tr_ins_update_date,"length"=>14,"type"=>array());
//        $arFieldsConfig["code_erp"] = array("controlid"=>"txtCodeErp","label"=>tr_ins_code_erp,"length"=>25,"type"=>array());
//        $arFieldsConfig["is_robinson_email"] = array("controlid"=>"selIsRobinsonEmail","label"=>tr_ins_is_robinson_email,"length"=>3,"type"=>array());
//        $arFieldsConfig["is_validated"] = array("controlid"=>"selIsValidated","label"=>tr_ins_is_validated,"length"=>3,"type"=>array());
//        $arFieldsConfig["description"] = array("controlid"=>"txtDescription","label"=>tr_ins_description,"length"=>200,"type"=>array());      
        return $arFieldsConfig;

    }//get_insert_validate()
    
    //insert_4
    private function build_insert_fields($usePost=0)
    {
        $arFields = array(); $oAuxField = null; $oAuxLabel = NULL;
        $arFields[]= new AppHelperFormhead(tr_new.tr_entity);
        
        //id_seller
        $oModelSeller = new ModelSeller();
        $oModelSeller->set_select_user($this->oSessionUser->get_id());
        $arOptions = $oModelSeller->get_picklist_hierarchy("seller","id");
        $oAuxField = new HelperSelect($arOptions,"selIdSeller","selIdSeller");
        $idUserSeller = $this->oSessionUser->get_his_id_seller();
        //bug($this->oSessionUser);
        $oAuxField->set_value_to_select($idUserSeller);
        if(!$this->oSessionUser->has_hierarchy_sellers())
        {   
            $oAuxField->readonly();
            $oAuxField->add_class("readonly");
        }
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdSeller"));
        $oAuxLabel = new HelperLabel("selIdSeller",tr_ins_id_seller,"lblIdSeller");
        $oAuxLabel->add_class("labelreq");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //company
        $oAuxField = new HelperInputText("txtCompany","txtCompany");
        if($usePost) $oAuxField->set_value($this->get_post("txtCompany"));
        $oAuxLabel = new HelperLabel("txtCompany",tr_ins_company,"lblCompany");
        $oAuxLabel->add_class("labelreq");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //address
        $oAuxField = new HelperInputText("txtAddress","txtAddress");
        if($usePost) $oAuxField->set_value($this->get_post("txtAddress"));
        $oAuxLabel = new HelperLabel("txtAddress",tr_ins_address,"lblAddress");
        $oAuxLabel->add_class("labelreq");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //first_name
        $oAuxField = new HelperInputText("txtFirstName","txtFirstName");
        if($usePost) $oAuxField->set_value($this->get_post("txtFirstName"));
        $oAuxLabel = new HelperLabel("txtFirstName",tr_ins_first_name,"lblFirstName");
        $oAuxLabel->add_class("labelreq");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //last_name
        $oAuxField = new HelperInputText("txtLastName","txtLastName");
        if($usePost) $oAuxField->set_value($this->get_post("txtLastName"));
        $oAuxLabel = new HelperLabel("txtLastName",tr_ins_last_name,"lblLastName");
        $oAuxLabel->add_class("labelreq");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //phone_1
        $oAuxField = new HelperInputText("txtPhone1","txtPhone1");
        if($usePost) $oAuxField->set_value($this->get_post("txtPhone1"));
        $oAuxLabel = new HelperLabel("txtPhone1",tr_ins_phone_1,"lblPhone1");
        $oAuxLabel->add_class("labelreq");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);              
        //email
        $oAuxField = new HelperInputText("txtEmail","txtEmail");
        if($usePost) $oAuxField->set_value($this->get_post("txtEmail"));
        $oAuxLabel = new HelperLabel("txtEmail",tr_ins_email,"lblEmail");
        
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);     

        //phone_2
        $oAuxField = new HelperInputText("txtPhone2","txtPhone2");
        if($usePost) $oAuxField->set_value($this->get_post("txtPhone2"));
        $oAuxLabel = new HelperLabel("txtPhone2",tr_ins_phone_2,"lblPhone2");
        
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //contact
        $oAuxField = new HelperInputText("txtContact","txtContact");
        if($usePost) $oAuxField->set_value($this->get_post("txtContact"));
        $oAuxLabel = new HelperLabel("txtContact",tr_ins_contact,"lblContact");
        
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);          
        //contact_phone
        $oAuxField = new HelperInputText("txtContactPhone","txtContactPhone");
        if($usePost) $oAuxField->set_value($this->get_post("txtContactPhone"));
        $oAuxLabel = new HelperLabel("txtContactPhone",tr_ins_contact_phone,"lblContactPhone");
        
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //is_robinson_email
        $oBehPicklist = new AppBehaviourPicklist();
        $arOptions = $oBehPicklist->get_boolean();
        $oAuxField = new HelperSelect($arOptions,"selIsRobinsonEmail","selIsRobinsonEmail");
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIsRobinsonEmail"));
        $oAuxLabel = new HelperLabel("selIsRobinsonEmail",tr_ins_is_robinson_email,"lblIsRobinsonEmail");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);        
//        //id_country
//        $oAuxField = new HelperInputText("txtIdCountry","txtIdCountry",$this->oCustomer->get_id_country());
//        if($usePost) $oAuxField->set_value($this->get_post("txtIdCountry"));
//        $oAuxLabel = new HelperLabel("txtIdCountry",tr_ins_id_country,"lblIdCountry");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        
//        //id_type
//        $oAuxField = new HelperInputText("txtIdType","txtIdType",$this->oCustomer->get_id_type());
//        if($usePost) $oAuxField->set_value($this->get_post("txtIdType"));
//        $oAuxLabel = new HelperLabel("txtIdType",tr_ins_id_type,"lblIdType");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        
//        //is_validated        
//        $oAuxField = new HelperSelect($arOptions,"selIsValidated","selIsValidated");
//        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIsValidated"));
//        $oAuxLabel = new HelperLabel("selIsValidated",tr_ins_is_validated,"lblIsValidated");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //description
//        $oAuxField = new HelperInputText("txtDescription","txtDescription");
//        if($usePost) $oAuxField->set_value($this->get_post("txtDescription"));
//        $oAuxField->readonly();
//        $oAuxLabel = new HelperLabel("txtDescription",tr_ins_description,"lblDescription");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //last_sale
//        $oAuxField = new HelperInputText("txtLastSale","txtLastSale");
//        if($usePost) $oAuxField->set_value($this->get_post("txtLastSale"));
//        $oAuxLabel = new HelperLabel("txtLastSale",tr_ins_last_sale,"lblLastSale");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);   
        
        //SAVE BUTTON
        $oAuxField = new HelperButtonBasic("butSave",tr_save);
        $oAuxField->add_class("btn btn-primary");
        $oAuxField->set_js_onclick("insert();");
        $arFields[] = new ApphelperFormactions(array($oAuxField));
        //POST INFO
        $oAuxField = new HelperInputHidden("hidAction","hidAction");
        $arFields[] = $oAuxField;
        $oAuxField = new HelperInputHidden("hidPostback","hidPostback");
        $arFields[] = $oAuxField;        
        return $arFields;
    }//build_insert_fields()
    
    //insert_5
    private function build_insert_form($usePost=0)
    {
        $oForm = new HelperForm("frmInsert");
        $oForm->add_class("form-horizontal");
        $oForm->add_style("margin-bottom:0");
        $arFields = $this->build_insert_fields($usePost);
        $oForm->add_controls($arFields);
        return $oForm;
    }//build_update_form()

    //insert_6
    public function insert()
    {
        //redirige en caso de no tener permiso
        $this->go_to_401($this->oPermission->is_not_insert());        
        //Validacion con PHP y JS
        $arFieldsConfig = $this->get_insert_validate();
        
        if($this->is_inserting())
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
                $this->oCustomer->log_save_insert();
                $this->oCustomer->set_attrib_value($arFieldsValues);
                $this->oCustomer->set_insert_user($this->oSessionUser->get_id());
                //$this->oCustomer->set_platform($this->oSessionUser->get_platform());
                $this->oCustomer->autoinsert();
                if($this->oCustomer->is_error())
                {
                    $oAlert->set_type("e");
                    $oAlert->set_title(tr_data_not_saved);
                    $oAlert->set_content(tr_error_trying_to_save);
                }
                else//insert ok
                {
                    $this->set_get("id",$this->oCustomer->get_last_insert_id());
                    $oAlert->set_title(tr_data_saved);
                    $this->reset_post();
                    $this->go_to_after_succes_cud();
                }
            }//no error
        }//fin if is_inserting (post action=save)
        //Si hay errores se recupera desde post
        if($arErrData) $oForm = $this->build_insert_form(1);
        else $oForm = $this->build_insert_form();
        
        //SCRUMBS
        $oScrumbs = $this->build_insert_scrumbs();
        //BUTTONS
        $oOpButtons = $this->build_insert_opbuttons();
        
        $oJavascript = new HelperJavascript();
        $oJavascript->set_validate_config($arFieldsConfig);
        $oJavascript->set_formid("frmInsert");
        
        //VIEW SET
        $this->oView->add_var($oScrumbs,"oScrumbs");
        $this->oView->add_var($oOpButtons,"oOpButtons");
        $this->oView->add_var($oAlert,"oAlert");
        $this->oView->add_var($oForm,"oForm");
        $this->oView->add_var($oJavascript,"oJavascript");
        $this->oView->show_page();
    }//insert()
//</editor-fold>

//<editor-fold defaultstate="collapsed" desc="UPDATE">
    //update_1
    private function build_update_scrumbs()
    {        
        $sUrlTab = $this->build_url($this->sModuleName);;
        $arTabs["list"]=array("href"=>$sUrlTab,"innerhtml"=>tr_entities);
        $sUrlParam = "id=".$this->get_get("id");
        $sUrlTab = $this->build_url($this->sModuleName,NULL,"update",$sUrlParam);
        $arTabs["detail"]=array("href"=>$sUrlTab,"innerhtml"=>tr_entity.": ".$this->oCustomer->get_id()." - ".$this->oCustomer->get_description());

        $oScrumbs = new AppHelperBreadscrumbs($arTabs);        
        return $oScrumbs;
    }//build_update_scrumbs()
    
    //update_2
    private function build_update_tabs()
    {        
        $arTabs = array();
        $sUrlTab = $this->build_url($this->sModuleName,NULL,"update","id=".$this->get_get("id"));
        $arTabs["detail"]=array("href"=>$sUrlTab,"innerhtml"=>"Detail");
        $sUrlTab = $this->build_url($this->sModuleName,"notes",NULL,"id_customer=".$this->get_get("id"));
        $arTabs["notes"]=array("href"=>$sUrlTab,"innerhtml"=>"Notes");
        $oTabs = new AppHelperHeadertabs($arTabs,"detail");    
        return $oTabs;
    }//build_update_tabs()
    
    //update_3
    private function build_update_opbuttons()
    {
        $arOpButtons = array();
        if($this->oPermission->is_select())
            $arOpButtons["list"]=array("href"=>$this->build_url($this->sModuleName),"icon"=>"awe-search","innerhtml"=>tr_list);
        if($this->oPermission->is_insert())
            $arOpButtons["insert"]=array("href"=>$this->build_url($this->sModuleName,NULL,"insert"),"icon"=>"awe-plus","innerhtml"=>tr_new);
        if($this->oPermission->is_quarantine())
            $arOpButtons["delete"]=array("href"=>$this->build_url($this->sModuleName,NULL,"quarantine","id=".$this->get_get("id")),"icon"=>"awe-remove","innerhtml"=>tr_delete);        
        //if($this->oPermission->is_delete())
            //$arOpButtons["delete"]=array("href"=>"$this->build_url($this->sModuleName,NULL,"delete","id=".$this->get_get("id")),"icon"=>"awe-remove","innerhtml"=>tr_delete);        
        $oOpButtons = new AppHelperButtontabs(tr_entities);
        $oOpButtons->set_tabs($arOpButtons);
        return $oOpButtons;
    }//build_update_opbuttons()

    //update_4
    private function get_update_validate()
    {
        $arFieldsConfig = array();
        $arFieldsConfig["first_name"] = array("controlid"=>"txtFirstName","label"=>tr_upd_first_name,"length"=>100,"type"=>array(""));
        $arFieldsConfig["last_name"] = array("controlid"=>"txtLastName","label"=>tr_upd_last_name,"length"=>100,"type"=>array(""));
        $arFieldsConfig["contact"] = array("controlid"=>"txtContact","label"=>tr_upd_contact,"length"=>100,"type"=>array("required"));
        $arFieldsConfig["contact_phone"] = array("controlid"=>"txtContactPhone","label"=>tr_upd_contact_phone,"length"=>15,"type"=>array()); 
        $arFieldsConfig["company"] = array("controlid"=>"txtCompany","label"=>tr_upd_company,"length"=>100,"type"=>array("required"));
        $arFieldsConfig["address"] = array("controlid"=>"txtAddress","label"=>tr_upd_address,"length"=>200,"type"=>array("required"));
        $arFieldsConfig["email"] = array("controlid"=>"txtEmail","label"=>tr_upd_email,"length"=>50,"type"=>array());
        $arFieldsConfig["phone_1"] = array("controlid"=>"txtPhone1","label"=>tr_upd_phone_1,"length"=>15,"type"=>array("required"));
        $arFieldsConfig["phone_2"] = array("controlid"=>"txtPhone2","label"=>tr_upd_phone_2,"length"=>15,"type"=>array());         
        //$arFieldsConfig["id"] = array("controlid"=>"txtId","label"=>tr_ins_id,"length"=>9,"type"=>array("numeric","required"));
//        $arFieldsConfig["id_seller"] = array("controlid"=>"selIdSeller","label"=>tr_upd_id_seller,"length"=>4,"type"=>array());
//        $arFieldsConfig["id"] = array("controlid"=>"txtId","label"=>tr_upd_id,"length"=>9,"type"=>array("numeric","required"));
//        $arFieldsConfig["id_country"] = array("controlid"=>"txtIdCountry","label"=>tr_upd_id_country,"length"=>9,"type"=>array("numeric"));
//        $arFieldsConfig["id_type"] = array("controlid"=>"txtIdType","label"=>tr_upd_id_type,"length"=>9,"type"=>array("numeric"));
//        $arFieldsConfig["last_sale"] = array("controlid"=>"txtLastSale","label"=>tr_upd_last_sale,"length"=>9,"type"=>array("numeric"));
//        $arFieldsConfig["insert_date"] = array("controlid"=>"txtCreateDate","label"=>tr_upd_insert_date,"length"=>14,"type"=>array());
//        $arFieldsConfig["update_date"] = array("controlid"=>"txtModifyDate","label"=>tr_upd_update_date,"length"=>14,"type"=>array());
//        $arFieldsConfig["code_erp"] = array("controlid"=>"txtCodeErp","label"=>tr_upd_code_erp,"length"=>25,"type"=>array());
//        $arFieldsConfig["is_robinson_email"] = array("controlid"=>"selIsRobinsonEmail","label"=>tr_upd_is_robinson_email,"length"=>3,"type"=>array());
//        $arFieldsConfig["is_validated"] = array("controlid"=>"selIsValidated","label"=>tr_upd_is_validated,"length"=>3,"type"=>array());
//        $arFieldsConfig["description"] = array("controlid"=>"txtDescription","label"=>tr_upd_description,"length"=>200,"type"=>array());         
        return $arFieldsConfig;
    }//get_update_validate
    
    //update_5
    private function build_update_fields($usePost=0)
    {
        $arFields = array(); $oAuxField = NULL; $oAuxLabel = NULL;
        //id
        $oAuxField = new HelperInputText("txtId","txtId",$this->oCustomer->get_id());
        $oAuxField->is_primarykey();
        $oAuxField->readonly(); $oAuxField->add_class("readonly");
        if($usePost) $oAuxField->set_value($this->get_post("txtId"));
        $oAuxLabel = new HelperLabel("txtId",tr_upd_id,"lblId");
        $oAuxLabel->add_class("labelpk");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //id_seller
        $oModelSeller = new ModelSeller();
        $arOptions = $oModelSeller->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdSeller","selIdSeller");
        $oAuxField->set_value_to_select($this->oCustomer->get_id_seller());
        $oAuxField->readonly(); $oAuxField->add_class("readonly");
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdSeller"));
        $oAuxLabel = new HelperLabel("selIdSeller",tr_upd_id_seller,"lblIdSeller");
        $oAuxLabel->add_class("labelreq");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //company
        $oAuxField = new HelperInputText("txtCompany","txtCompany",$this->oCustomer->get_company());
        if($usePost) $oAuxField->set_value($this->get_post("txtCompany"));
        $oAuxLabel = new HelperLabel("txtCompany",tr_upd_company,"lblCompany");
        $oAuxLabel->add_class("labelreq");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //address
        $oAuxField = new HelperInputText("txtAddress","txtAddress",$this->oCustomer->get_address());
        if($usePost) $oAuxField->set_value($this->get_post("txtAddress"));
        $oAuxLabel = new HelperLabel("txtAddress",tr_upd_address,"lblAddress");
        $oAuxLabel->add_class("labelreq");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //contact
        $oAuxField = new HelperInputText("txtContact","txtContact",$this->oCustomer->get_contact());
        if($usePost) $oAuxField->set_value($this->get_post("txtContact"));
        $oAuxLabel = new HelperLabel("txtContact",tr_upd_contact,"lblContact");
        $oAuxLabel->add_class("labelreq");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //phone_1
        $oAuxField = new HelperInputText("txtPhone1","txtPhone1",$this->oCustomer->get_phone_1());
        if($usePost) $oAuxField->set_value($this->get_post("txtPhone1"));
        $oAuxLabel = new HelperLabel("txtPhone1",tr_upd_phone_1,"lblPhone1");
        $oAuxLabel->add_class("labelreq");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //first_name
        $oAuxField = new HelperInputText("txtFirstName","txtFirstName",$this->oCustomer->get_first_name());
        if($usePost) $oAuxField->set_value($this->get_post("txtFirstName"));
        $oAuxLabel = new HelperLabel("txtFirstName",tr_upd_first_name,"lblFirstName");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //last_name
        $oAuxField = new HelperInputText("txtLastName","txtLastName",$this->oCustomer->get_last_name());
        if($usePost) $oAuxField->set_value($this->get_post("txtLastName"));
        $oAuxLabel = new HelperLabel("txtLastName",tr_upd_last_name,"lblLastName");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //contact_phone
        $oAuxField = new HelperInputText("txtContactPhone","txtContactPhone",$this->oCustomer->get_contact_phone());
        if($usePost) $oAuxField->set_value($this->get_post("txtContactPhone"));
        $oAuxLabel = new HelperLabel("txtContactPhone",tr_upd_contact_phone,"lblContactPhone");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //phone_2
        $oAuxField = new HelperInputText("txtPhone2","txtPhone2",$this->oCustomer->get_phone_2());
        if($usePost) $oAuxField->set_value($this->get_post("txtPhone2"));
        $oAuxLabel = new HelperLabel("txtPhone2",tr_upd_phone_2,"lblPhone2");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //email
        $oAuxField = new HelperInputText("txtEmail","txtEmail",$this->oCustomer->get_email());
        if($usePost) $oAuxField->set_value($this->get_post("txtEmail"));
        $oAuxLabel = new HelperLabel("txtEmail",tr_upd_email,"lblEmail");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //is_robinson_email
        $oBehPicklist = new AppBehaviourPicklist();
        $arOptions = $oBehPicklist->get_boolean();
        $oAuxField = new HelperSelect($arOptions,"selIsRobinsonEmail","selIsRobinsonEmail");
        $oAuxField->set_value_to_select($this->oCustomer->get_is_robinson_email());
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIsRobinsonEmail"));
        $oAuxLabel = new HelperLabel("selIsRobinsonEmail",tr_upd_is_robinson_email,"lblIsRobinsonEmail");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //last_sale
        $oAuxField = new HelperInputText("txtLastSale","txtLastSale",dbbo_numeric2($this->oCustomer->get_last_sale()));
        $oAuxField->readonly(); $oAuxField->add_class("readonly");
        if($usePost) $oAuxField->set_value($this->get_post("txtLastSale"));
        $oAuxLabel = new HelperLabel("txtLastSale",tr_upd_last_sale,"lblLastSale");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
//        //description
//        $oAuxField = new HelperInputText("txtDescription","txtDescription",$this->oCustomer->get_description());
//        if($usePost) $oAuxField->set_value($this->get_post("txtDescription"));
//        $oAuxField->readonly(); $oAuxField->add_class("readonly");
//        $oAuxLabel = new HelperLabel("txtDescription",tr_upd_description,"lblDescription");        
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //is_validated        
//        $oAuxField = new HelperSelect($arOptions,"selIsValidated","selIsValidated");
//        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIsValidated"));
//        $oAuxLabel = new HelperLabel("selIsValidated",tr_upd_is_validated,"lblIsValidated");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //code_erp
//        $oAuxField = new HelperInputText("txtCodeErp","txtCodeErp",$this->oCustomer->get_code_erp());
//        $oAuxField->readonly(); $oAuxField->add_class("readonly");
//        if($usePost) $oAuxField->set_value($this->get_post("txtCodeErp"));
//        $oAuxLabel = new HelperLabel("txtCodeErp",tr_upd_code_erp,"lblCodeErp");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);        

//        //id_country
//        $oAuxField = new HelperInputText("txtIdCountry","txtIdCountry",$this->oCustomer->get_id_country());
//        if($usePost) $oAuxField->set_value($this->get_post("txtIdCountry"));
//        $oAuxLabel = new HelperLabel("txtIdCountry",tr_upd_id_country,"lblIdCountry");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        
//        //id_type
//        $oAuxField = new HelperInputText("txtIdType","txtIdType",$this->oCustomer->get_id_type());
//        if($usePost) $oAuxField->set_value($this->get_post("txtIdType"));
//        $oAuxLabel = new HelperLabel("txtIdType",tr_upd_id_type,"lblIdType");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        
        //BUTTON SAVE
        $oAuxField = new HelperButtonBasic("butSave",tr_save);
        $oAuxField->add_class("btn btn-primary");
        $oAuxField->set_js_onclick("update();");
        if($this->oPermission->is_update())
            $arFields[] = new ApphelperFormactions(array($oAuxField));
        //AUDIT INFO
        $sRegInfo = $this->get_audit_info($this->oCustomer->get_insert_user(),$this->oCustomer->get_insert_date()
        ,$this->oCustomer->get_update_user(),$this->oCustomer->get_update_date());
        $oAuxField = new AppHelperFormhead(null,$sRegInfo);
        $oAuxField->set_span();
        $arFields[] = $oAuxField;
        
        //POST INFO
        $oAuxField = new HelperInputHidden("hidAction","hidAction");
        $arFields[] = $oAuxField;
        $oAuxField = new HelperInputHidden("hidPostback","hidPostback");
        $arFields[] = $oAuxField;        
        return $arFields;
    }//build_update_fields()

    //update_6
    private function build_update_form($usePost=0)
    {
        //$id = $this->get_get("id");
        //bug($this->oCustomer); die;
        $id = $this->oCustomer->get_id();
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
        else
            //redirige en caso de no tener permiso
            $this->go_to_404();
        return $oForm;
    }//build_update_form()
    
    //update_7
    public function update()
    {
        //redirige en caso de no tener permiso
        $this->go_to_401(($this->oPermission->is_not_read() && $this->oPermission->is_not_update())||$this->oSessionUser->is_not_dataowner());
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
                $this->oCustomer->set_attrib_value($arFieldsValues);
                $this->oCustomer->set_update_user($this->oSessionUser->get_id());
                //$this->oCustomer->set_platform($this->oSessionUser->get_platform());
                $this->oCustomer->autoupdate();
                if($this->oCustomer->is_error())
                {
                    $oAlert->set_type("e");
                    $oAlert->set_title(tr_data_not_saved);
                    $oAlert->set_content(tr_error_trying_to_save);
                }//no error
                else//update ok
                {
                    //refresco los datos guardados
                    $this->oCustomer->load_by_id();
                    $oAlert->set_title(tr_data_saved);
                    $this->reset_post();
                    //$this->go_to_after_succes_cud();
                }//error save
            }//error validation
        }//is_updating()
        if($arErrData) $oForm = $this->build_update_form(1);
        else $oForm = $this->build_update_form(); 
        
        //TABS
        $oTabs = $this->build_update_tabs();
        //SCRUMBS
        $oScrumbs = $this->build_update_scrumbs();
        //OPER BUTTONS
        $oOpButtons = $this->build_update_opbuttons();
                
        $oJavascript = new HelperJavascript();
        $oJavascript->set_updateaction();
        $oJavascript->set_validate_config($arFieldsConfig);
        $oJavascript->set_formid("frmUpdate");
        //$oJavascript->->set_focusid("id_all");

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
    private function single_delete()
    {
        $id = $this->get_get("id");
        if($id)
        {
            $this->oCustomer->set_id($id);
            $this->oCustomer->autodelete();
            if($this->oCustomer->is_error())
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
    private function multi_delete()
    {
        //Intenta recuperar pkeys sino pasa a recuperar el id. En ultimo caso lo que se ha pasado por parametro
        $arKeys = $this->get_listkeys();
        foreach($arKeys as $sKey)
        {
            $id = $sKey;
            $this->oCustomer->set_id($id);
            $this->oCustomer->autodelete();
            if($this->oCustomer->is_error())
            {
                $this->isError = TRUE;
                $this->set_session_message(tr_error_trying_to_delete,"e");
            }
        }
        if(!$this->isError)
            $this->set_session_message(tr_data_deleted);
    }//multi_delete()

    //delete_3
    public function delete()
    {
        $this->go_to_401($this->oPermission->is_not_delete()||$this->oSessionUser->is_not_dataowner());
        $this->isError = FALSE;
        //Si ocurre un error se guarda en isError
        if($this->is_multidelete())
            $this->multi_delete();
        else
            $this->single_delete();
        //Si no ocurrio errores en el intento de borrado
        if(!$this->isError)
            $this->go_to_after_succes_cud();
        else
            $this->go_to_list();
    }//delete()
//</editor-fold>

//<editor-fold defaultstate="collapsed" desc="QUARANTINE">
    //quarantine_1
    private function multi_quarantine()
    {
        //Intenta recuperar pkeys sino pasa a id, y en ultimo caso lo que se ha pasado por parametro
        $arKeys = $this->get_listkeys();
        //bug($arKeys,"arkeys"); die;
        foreach($arKeys as $sKey)
        {
            $id = $sKey;
            $this->oCustomer->set_id($id);
            $this->oCustomer->autoquarantine();
            if($this->oCustomer->is_error())
            {
                $isError = true;
                $this->set_session_message(tr_error_trying_to_delete,"e");
            }
        }
        if(!$isError)
            $this->set_session_message(tr_data_deleted);
    }//multi_quarantine()

    //quarantine_2
    private function single_quarantine()
    {
        $id = $this->get_get("id");
        if($id)
        {
            $this->oCustomer->set_id($id);
            $this->oCustomer->autoquarantine();
            if($this->oCustomer->is_error())
            {    
                $this->set_session_message(tr_error_trying_to_delete);
            }
            else
                $this->set_session_message(tr_data_deleted);
        }//else no id
        else
            $this->set_session_message(tr_error_key_not_supplied,"e");
    }//single_quarantine()

    //quarantine_3
    public function quarantine()
    {
        $this->go_to_401($this->oPermission->is_not_quarantine()||$this->oSessionUser->is_not_dataowner());
        $this->isError = false;
        if($this->is_multiquarantine())
            $this->multi_quarantine();
        else
            $this->single_quarantine();
        //Si no ocurrio errores en el intento de borrado
        if(!$this->isError)
            $this->go_to_after_succes_cud();
        else
            $this->go_to_list();
    }//quarantine()
//</editor-fold>

//<editor-fold defaultstate="collapsed" desc="REPORTS">
    private function build_report_buttons()
    {
        $arButTabs = array();
        //$arButTabs["filters"]=array("href"=>"javascript:reset_filters();","icon"=>"awe-magic","innerhtml"=>tr_clear_filters);
        //$arButTabs["reload"]=array("href"=>"javascript:TfwControl.form_submit();","icon"=>"awe-refresh","innerhtml"=>tr_refresh);
        $arButTabs["closeme"]=array("href"=>"javascript:closeme();","icon"=>"awe-remove-sign","innerhtml"=>tr_closeme);
        return $arButTabs;
    }//build_report_buttons()
        
    private function get_report_filters()
    {
        $arFields = array();
        return $arFields;
    }//get_report_filters()

    private function get_report_columns()
    {
	$arColumns = array
        (
            "id"=>tr_col_id
            ,"code_erp"=>tr_col_code_erp
            //,"id_country"=>tr_col_id_country
            //,"id_type"=>tr_col_id_type
            ,"first_name"=>tr_col_first_name
            ,"last_name"=>tr_col_last_name
            ,"contact"=>tr_col_contact
            ,"last_sale"=>tr_col_last_sale            
            //,"contact_phone"=>tr_col_contact_phone
            ,"is_robinson_email"=>tr_col_is_robinson_email
            //,"is_validated"=>tr_col_is_validated
            ,"company"=>tr_col_company
            ,"id_seller"=>tr_col_id_seller
            //,"address"=>tr_col_address,"email"=>tr_col_email,"phone_1"=>tr_col_phone_1
            //,"phone_2"=>tr_col_phone_2
        );
        return $arColumns;
    }//get_report_columns()

    public function get_report()
    {
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
        $arObjFilter = $this->get_report_filters();
        $arColumns = $this->get_report_columns(); 

        $oFilter = new ComponentFilter();
        $oFilter->set_fieldnames($this->get_filter_fieldnames());
        //Indica que no se guardara en sesion por nombre de campo sino por nombre de control
        //para esto es necesario respetar el estricto camelcase
        $oFilter->use_field_prefix();
        //Guarda en sesion y post los campos enviados, los de orden y página
        $oFilter->refresh();
        //bugss("customersget_list");
        $this->oCustomer->set_orderby($this->get_orderby());
        $this->oCustomer->set_ordertype($this->get_ordertype());
        $this->oCustomer->set_filters($this->get_filter_searchconfig());
        $arList = $this->oCustomer->get_select_all_ids();
        $iRequestPage = $this->get_post("selPage");
        $oPage = new ComponentPage($arList,$iRequestPage);
        $arList = $oPage->get_items_to_show();
        $arList = $this->oCustomer->get_select_all_by_ids($arList);
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
        $oOpButtons = new AppHelperButtontabs(tr_entities);
        $oOpButtons->set_tabs($this->build_report_buttons());
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
    }//report()
//</editor-fold>
    
}//end controller_customer
