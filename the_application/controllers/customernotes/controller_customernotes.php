<?php
/**
 * @author Module Builder 1.0.12
 * @link www.eduardoaf.com
 * @version 1.0.10
 * @name ControllerCustomerNotes
 * @file controller_customernotes.php   
 * @date 31-10-2014 20:20 (SPAIN)
 * @observations: 
 * @requires:
 */
//TFW
import_component("page,validate,filter,mailing");
import_helper("form,form_fieldset,form_legend,input_text,input_date,textarea,label,anchor,table,table_typed");
import_helper("input_password,button_basic,raw,div,javascript");
//APP
import_model("user,seller,customer,customer_note,order_head");
import_appmain("controller,view,behaviour");
import_appbehaviour("picklist");
import_apphelper("listactionbar,controlgroup,formactions,buttontabs,formhead,alertdiv,breadscrumbs,headertabs");

class ControllerCustomerNotes extends TheApplicationController
{
    protected $oCustomerNote;
    protected $oCustomer;
            
    public function __construct()
    {
        $this->sModuleName = "customernotes";
        //console,pgpage,clientdevice,sesuser,aftersucces,currop  | log,session,permission,view
        parent::__construct($this->sModuleName);
        //$this->oPermission->grant_all();
        $this->oCustomerNote = new ModelCustomerNote();
        $this->oCustomerNote->set_platform($this->oSessionUser->get_platform());
        if($this->is_inget("id"))
        {
            $this->oCustomerNote->set_id($this->get_get("id"));
            $this->oCustomerNote->load_by_id();
        }
        if($this->is_get("id_customer"))
        {    
            $this->oCustomer = new ModelCustomer($this->get_get("id_customer"));
            $this->oCustomer->load_by_id();
        }        
        //$this->oSessionUser->set_dataowner_table($this->oCustomerNote->get_table_name());
        //$this->oSessionUser->set_dataowner_tablefield("id_customer");
        //$this->oSessionUser->set_dataowner_keys(array("id"=>$this->oCustomerNote->get_id()));
    }

    protected function email_onsave($id)
    {
        $this->oCustomerNote = new ModelCustomerNote($id);
        $this->oCustomerNote->load_by_id();
        $idCustomer = $this->oCustomerNote->get_id_customer();
        $sCustomer = $this->oCustomerNote->get_customer()->get_description();
        
        $sSubject = "CRM MAILER - Customer Note saved for: ".$sCustomer;

//        $oAnchor = new HelperAnchor();
//        $oAnchor->set_innerhtml($sCustomer." ($idCustomer)");
        $sUrl = "http://".TFW_DOMAIN."/".TFW_FOLDER_PROJECT."/the_public/index.php".$this->build_url("customers",NULL,"update","id=$idCustomer");
//        $oAnchor->set_href($sUrl);
//        $sCustomerLink = $oAnchor->get_html();
        
        $arContent[] = " Customer Note with Code:$id for $sUrl $sCustomer($idCustomer) has been saved by ".$this->oSessionUser->get_description();
        $arContent[] = "";
        //DETAIL
        $sUrl = "http://".TFW_DOMAIN."/".TFW_FOLDER_PROJECT."/the_public/index.php".$this->build_url("customers","notes","update","id_customer=$idCustomer&id=$id");
        $arContent[] = "Check detail: $sUrl";
        
        //DETAIL
        $arContent[] = "";
        $arContent[] = "IN THE OFFICE:";
        $sUrl = "http://".$this->get_server_lanip()."/".TFW_FOLDER_PROJECT."/the_public/index.php".$this->build_url("customers","notes","update","id_customer=$idCustomer&id=$id");
        $arContent[] = "Check detail: $sUrl";
        
        $arContent = implode("\r\n",$arContent);
        $this->log_custom("email: $sSubject\n$arContent");
        
        $oModelSeller = new ModelSeller();
        //$arEmails = $oModelSeller->get_all_emails();
        
        $oComponentMail = new ComponentMailing();
        $oComponentMail->set_title_from("CRM MAILER");
        //$oComponentMail->set_email_from("noreply@kuzeta.com");
        $oComponentMail->set_subject($sSubject);
        $oComponentMail->set_content($arContent);
        
        //$arEmails = array();
        $arEmails[] = "eacevedof@yahoo.es";
        //$arEmails[] = "alvarofarje@me.com";
        $oComponentMail->set_emails_to($arEmails);
        $oComponentMail->send();
        
        if($oComponentMail->is_error())
        {
            $this->log_custom("Sending email error: ".$oComponentMail->get_error_message());
            $this->set_session_message("Error sending email!","w");
        }
    }
//<editor-fold defaultstate="collapsed" desc="LIST">
    //list_1
    protected function build_list_scrumbs()
    {
        $arLinks = array();
        $sUrlTab = $this->build_url();
        $arLinks["list"] = array("href"=>$sUrlTab,"innerhtml"=>tr_crn_entities);
        $oScrumbs = new AppHelperBreadscrumbs($arLinks);
        return $oScrumbs;
    }

    //list_2
    protected function build_list_tabs()
    {
        $arTabs = array();
        //$sUrlTab = $this->build_url($this->sModuleName,NULL,"get_list","id=".$this->get_get("id_parent_foreign"));
        //$arTabs["list"]=array("href"=>$sUrlTab,"innerhtml"=>tr_crn_listtabs_1);
        //$sUrlTab = $this->build_url($this->sModuleName,NULL,"get_list_by_foreign","id_foreign=".$this->get_get("id_parent_foreign"));
        //$arTabs["listbyforeign"]=array("href"=>$sUrlTab,"innerhtml"=>tr_crn_listtabs_2);
        $oTabs = new AppHelperHeadertabs($arTabs,"list");
        return $oTabs;
    }

    //list_3
    protected function build_listoperation_buttons()
    {
        $arOpButtons = array();
        $arOpButtons["filters"]=array("href"=>"javascript:reset_filters();","icon"=>"awe-magic","innerhtml"=>tr_crn_listopbutton_filters);
        $arOpButtons["reload"]=array("href"=>"javascript:TfwControl.form_submit();","icon"=>"awe-refresh","innerhtml"=>tr_crn_listopbutton_reload);
        if($this->oPermission->is_insert())
            $arOpButtons["insert"]=array("href"=>$this->build_url($this->sModuleName,NULL,"insert"),"icon"=>"awe-plus","innerhtml"=>tr_crn_listopbutton_insert);
        if($this->oPermission->is_quarantine())
            $arOpButtons["multiquarantine"]=array("href"=>"javascript:multi_quarantine();","icon"=>"awe-remove","innerhtml"=>tr_crn_listopbutton_multiquarantine);
        //if($this->oPermission->is_delete())
            //$arOpButtons["multidelete"]=array("href"=>"javascript:multi_delete();","icon"=>"awe-remove","innerhtml"=>tr_crn_listopbutton_multidelete);
        //PICK WINDOWS
        //$arOpButtons["multiassign"]=array("href"=>"javascript:multiassign_window('customernotes',null,'multiassign','customernotes','addexternaldata');","icon"=>"awe-external-link","innerhtml"=>tr_crn_listopbutton_multiassign);
        //$arOpButtons["singleassign"]=array("href"=>"javascript:single_pick('customernotes','singleassign','txtI','txtI');","icon"=>"awe-external-link","innerhtml"=>tr_crn_listopbutton_singleassign);
        $oOpButtons = new AppHelperButtontabs(tr_crn_entities);
        $oOpButtons->set_tabs($arOpButtons);
        return $oOpButtons;
    }//build_listoperation_buttons()

    //list_4
    protected function load_config_list_filters()
    {
        //id
        $this->set_filter("id","txtId",array("operator"=>"like"));
        //code_erp
        //$this->set_filter("code_erp","txtCodeErp",array("operator"=>"like"));
        //id_user
        $this->set_filter("id_user","selIdUser");
        //id_customer
        $this->set_filter("id_customer","selIdCustomer");
        //id_order
        $this->set_filter("id_order","selIdOrder");
        //description
        //$this->set_filter("description","txtDescription",array("operator"=>"like"));
        //note
        $this->set_filter("note","txaNote",array("operator"=>"like"));
        //date
        $this->set_filter("date","datDate",array("operator"=>"like"));
        //hour
        $this->set_filter("hour","txtHour",array("operator"=>"like"));
    }//load_config_list_filters()

    //list_5
    protected function set_listfilters_from_post()
    {
        //id
        $this->set_filter_value("id",$this->get_post("txtId"));
        //code_erp
        //$this->set_filter_value("code_erp",$this->get_post("txtCodeErp"));
        //id_user
        $this->set_filter_value("id_user",$this->get_post("selIdUser"));
        //id_customer
        $this->set_filter_value("id_customer",$this->get_post("selIdCustomer"));
        //id_order
        $this->set_filter_value("id_order",$this->get_post("selIdOrder"));
        //description
        //$this->set_filter_value("description",$this->get_post("txtDescription"));
        //note
        $this->set_filter_value("note",$this->get_post("txaNote"));
        //date
        $this->set_filter_value("date",$this->get_post("datDate"));
        //hour
        $this->set_filter_value("hour",$this->get_post("txtHour"));
    }//set_listfilters_from_post()

    //list_6
    protected function get_list_filters()
    {
        //CAMPOS
        //id
        $oAuxField = new HelperInputText("txtId","txtId");
        $oAuxField->set_value($this->get_post("txtId"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtId",tr_crn_fil_id));
        $arFields[] = $oAuxWrapper;
        //code_erp
        //$oAuxField = new HelperInputText("txtCodeErp","txtCodeErp");
        //$oAuxField->set_value($this->get_post("txtCodeErp"));
        //$oAuxField->on_entersubmit();
        //$oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtCodeErp",tr_crn_fil_code_erp));
        //$arFields[] = $oAuxWrapper;
        //id_user
        $oUser = new ModelUser();
        $arOptions = $oUser->get_picklist_custom("id","description","code_type='kuzeta'");
        $oAuxField = new HelperSelect($arOptions,"selIdUser","selIdUser");
        $oAuxField->set_value_to_select($this->get_post("selIdUser"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdUser",tr_crn_fil_id_user));
        $arFields[] = $oAuxWrapper;
        //id_customer
        $oCustomer = new ModelCustomer();
        $arOptions = $oCustomer->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdCustomer","selIdCustomer");
        $oAuxField->set_value_to_select($this->get_post("selIdCustomer"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdCustomer",tr_crn_fil_id_customer));
        $arFields[] = $oAuxWrapper;
        //id_order
        $oOrder = new ModelOrderHead();
        $arOptions = $oOrder->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdOrder","selIdOrder");
        $oAuxField->set_value_to_select($this->get_post("selIdOrder"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdOrder",tr_crn_fil_id_order));
        $arFields[] = $oAuxWrapper;
        //description
        //$oAuxField = new HelperInputText("txtDescription","txtDescription");
        //$oAuxField->set_value($this->get_post("txtDescription"));
        //$oAuxField->on_entersubmit();
        //$oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtDescription",tr_crn_fil_description));
        //$arFields[] = $oAuxWrapper;
        //note
//        $oAuxField = new HelperTextarea("txaNote","txaNote");
//        $oAuxField->set_innerhtml($this->get_post("txaNote"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txaNote",tr_crn_fil_note));
//        $arFields[] = $oAuxWrapper;
        //date
        $oAuxField = new HelperDate("datDate","datDate");
        $oAuxField->set_is_ipadiphone($this->is_ipad()||$this->is_iphone());
        $oAuxField->set_value($this->get_post("datDate"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("datDate",tr_crn_fil_date));
        $arFields[] = $oAuxWrapper;
        //hour
        $oAuxField = new HelperInputText("txtHour","txtHour");
        $oAuxField->set_value($this->get_post("txtHour"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtHour",tr_crn_fil_hour));
        $arFields[] = $oAuxWrapper;
        return $arFields;
    }//get_list_filters()

    //list_7
    protected function get_list_columns()
    {
        $arColumns["id"] = tr_crn_col_id;
        //$arColumns["code_erp"] = tr_crn_col_code_erp;
        //$arColumns["id_user"] = tr_crn_col_id_user;
        $arColumns["notifier"] = tr_crn_col_id_user;
        //$arColumns["id_customer"] = tr_crn_col_id_customer;
        $arColumns["customer"] = tr_crn_col_id_customer;
        //$arColumns["id_order"] = tr_crn_col_id_order;
        $arColumns["orderhead"] = tr_crn_col_id_order;
        //$arColumns["description"] = tr_crn_col_description;
        $arColumns["note"] = tr_crn_col_note;
        $arColumns["date"] = tr_crn_col_date;
        $arColumns["hour"] = tr_crn_col_hour;
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
        $this->oCustomerNote->set_orderby($this->get_orderby());
        $this->oCustomerNote->set_ordertype($this->get_ordertype());
        $this->oCustomerNote->set_filters($this->get_filter_searchconfig(array("date"=>"date")));
        //hierarchy recover
        //$this->oCustomerNote->set_select_user($this->oSessionUser->get_id());
        $arList = $this->oCustomerNote->get_select_all_ids();
        $iRequestPage = $this->get_post("selPage");
        $oPage = new ComponentPage($arList,$iRequestPage);
        $arList = $oPage->get_items_to_show();
        $arList = $this->oCustomerNote->get_select_all_by_ids($arList);
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
        //COLUMNS CONFIGURATION
        if($this->oPermission->is_quarantine()||$this->oPermission->is_delete())
            $oTableList->set_column_pickmultiple();//checks column
        if($this->oPermission->is_read())
            $oTableList->set_column_detail();
        if($this->oPermission->is_quarantine())
            $oTableList->set_column_quarantine();
        //if($this->oPermission->is_delete())
            //$oTableList->set_column_delete();
        //$arExtra[] = array("position"=>1,"label"=>"Lines");
        //$oTableList->add_extra_colums($arExtra);
        //$oTableList->set_column_anchor(array("virtual_0"=>array
        //("href"=>"url_lines","innerhtml"=>tr_crn_order_lines,"class"=>"btn btn-info","icon"=>"awe-info-sign")));
        $arFormat = array("date"=>"date","hour"=>"time6");
        $oTableList->set_format_columns($arFormat);
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
        $sUrlTab = $this->build_url();;
        $arLinks["list"]=array("href"=>$sUrlTab,"innerhtml"=>tr_crn_entities);
        $sUrlTab = $this->build_url($this->sModuleName,NULL,"insert");
        $arLinks["insert"]=array("href"=>$sUrlTab,"innerhtml"=>tr_crn_entity_insert);
        $oScrumbs = new AppHelperBreadscrumbs($arLinks);
        return $oScrumbs;
    }//build_insert_scrumbs()

    //insert_2
    protected function build_insert_tabs()
    {
        $arTabs = array();
        //$sUrlTab = $this->build_url($this->sModuleName,NULL,"insert");
        //$arTabs["insert1"]=array("href"=>$sUrlTab,"innerhtml"=>tr_crn_instabs_1);
        //$sUrlTab = $this->build_url($this->sModuleName,NULL,"insert2");2
        //$arTabs["insert2"]=array("href"=>$sUrlTab,"innerhtml"=>tr_crn_instabs_2);
        $oTabs = new AppHelperHeadertabs($arTabs,"insert1");
        return $oTabs;
    }//build_insert_tabs()
    //insert_3
    protected function build_insert_opbuttons()
    {
        $arOpButtons = array();
        $arOpButtons["list"] = array("href"=>$this->build_url(),"icon"=>"awe-search","innerhtml"=>tr_crn_insopbutton_list);
        //$arOpButtons["extra"] = array("href"=>$this->build_url(),"icon"=>"awe-xxxx","innerhtml"=>tr_crn_insopbutton_extra1);
        $oOpButtons = new AppHelperButtontabs(tr_crn_entities);
        $oOpButtons->set_tabs($arOpButtons);
        return $oOpButtons;
    }//build_insert_opbuttons()

    //insert_4
    protected function build_insert_fields($usePost=0)
    {
        $arFields = array(); $oAuxField = NULL; $oAuxLabel = NULL;
        $arFields[]= new AppHelperFormhead(tr_crn_entity_new);
        //id_user
        $oUser = new ModelUser();
        $arOptions = $oUser->get_picklist_custom("id","description","code_type='kuzeta'");
        $oAuxField = new HelperSelect($arOptions,"selIdUser","selIdUser");
        $oAuxField->set_value_to_select($this->oSessionUser->get_id());
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdUser",tr_crn_ins_id_user));
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdUser"));
        $oAuxLabel = new HelperLabel("selIdUser",tr_crn_ins_id_user,"lblIdUser");
        $oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //id_customer
        $oCustomer = new ModelCustomer();
        $arOptions = $oCustomer->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdCustomer","selIdCustomer");
        $oAuxField->set_value_to_select($this->get_post("selIdCustomer"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdCustomer",tr_crn_ins_id_customer));
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdCustomer"));
        $oAuxLabel = new HelperLabel("selIdCustomer",tr_crn_ins_id_customer,"lblIdCustomer");
        $oAuxLabel->add_class("labelreq");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //date
        $oAuxField = new HelperDate("datDate","datDate");
        $oAuxField->set_is_ipadiphone($this->is_ipad()||$this->is_iphone());
        $oAuxField->set_value(date("d/m/Y"));
        if($usePost) $oAuxField->set_value($this->get_post("datDate"));
        $oAuxLabel = new HelperLabel("datDate",tr_crn_ins_date,"lblDate");
        $oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //hour
        $oAuxField = new HelperInputText("txtHour","txtHour");
        $oAuxField->set_value(date("H:i:s"));
        if($usePost) $oAuxField->set_value($this->get_post("txtHour"));
        $oAuxLabel = new HelperLabel("txtHour",tr_crn_ins_hour,"lblHour");
        $oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //id_order
        $oOrder = new ModelOrderHead();
        $arOptions = $oOrder->get_picklist_custom("id","description","id_customer=".$this->get_post("selIdCustomer"));
        $oAuxField = new HelperSelect($arOptions,"selIdOrder","selIdOrder");
        $oAuxField->set_value_to_select($this->get_post("selIdOrder"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdOrder",tr_crn_ins_id_order));
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdOrder"));
        $oAuxLabel = new HelperLabel("selIdOrder",tr_crn_ins_id_order,"lblIdOrder");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //note
        $oAuxField = new HelperTextarea("txaNote","txaNote");
        if($usePost) $oAuxField->set_innerhtml($this->get_post("txaNote"));
        $oAuxLabel = new HelperLabel("txaNote",tr_crn_ins_note,"lblNote");
        $oAuxLabel->add_class("labelreq");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //id
        //$oAuxField = new HelperInputText("txtId","txtId");
        //$oAuxField->is_primarykey();
        //if($usePost) $oAuxField->set_value($this->get_post("txtId"));
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        //$arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //code_erp
        //$oAuxField = new HelperInputText("txtCodeErp","txtCodeErp");
        //if($usePost) $oAuxField->set_value($this->get_post("txtCodeErp"));
        //$oAuxLabel = new HelperLabel("txtCodeErp",tr_crn_ins_code_erp,"lblCodeErp");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        //$arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //description
        //$oAuxField = new HelperInputText("txtDescription","txtDescription");
        //if($usePost) $oAuxField->set_value($this->get_post("txtDescription"));
        //$oAuxLabel = new HelperLabel("txtDescription",tr_crn_ins_description,"lblDescription");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        //$arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
                
        //SAVE BUTTON
        $oAuxField = new HelperButtonBasic("butSave",tr_crn_ins_savebutton);
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
    protected function get_insert_validate()
    {
        $arFieldsConfig = array();
        //$arFieldsConfig["id"] = array("controlid"=>"txtId","label"=>tr_crn_ins_id,"length"=>9,"type"=>array("numeric","required"));
        //$arFieldsConfig["code_erp"] = array("controlid"=>"txtCodeErp","label"=>tr_crn_ins_code_erp,"length"=>25,"type"=>array());
        //$arFieldsConfig["id_user"] = array("controlid"=>"selIdUser","label"=>tr_crn_ins_id_user,"length"=>9,"type"=>array());
        $arFieldsConfig["id_customer"] = array("controlid"=>"selIdCustomer","label"=>tr_crn_ins_id_customer,"length"=>9,"type"=>array("required"));
        //$arFieldsConfig["id_order"] = array("controlid"=>"selIdOrder","label"=>tr_crn_ins_id_order,"length"=>9,"type"=>array());
        //$arFieldsConfig["description"] = array("controlid"=>"txtDescription","label"=>tr_crn_ins_description,"length"=>200,"type"=>array());
        $arFieldsConfig["note"] = array("controlid"=>"txaNote","label"=>tr_crn_ins_note,"length"=>255,"type"=>array("required"));
        $arFieldsConfig["date"] = array("controlid"=>"datDate","label"=>tr_crn_ins_date,"length"=>10,"type"=>array("required"));
        $arFieldsConfig["hour"] = array("controlid"=>"txtHour","label"=>tr_crn_ins_hour,"length"=>9,"type"=>array("required"));
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
        $this->go_to_401($this->oPermission->is_not_insert());
        //php and js validation
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
                //$this->oCustomerNote->log_save_insert();
                $arFieldsValues["date"] = bodb_date($arFieldsValues["date"],"/");
                $arFieldsValues["hour"] = bodb_time6($arFieldsValues["hour"]);
                $this->oCustomerNote->set_attrib_value($arFieldsValues);
                $this->oCustomerNote->set_insert_user($this->oSessionUser->get_id());
                //$this->oCustomerNote->set_platform($this->oSessionUser->get_platform());
                $this->oCustomerNote->autoinsert();
                if($this->oCustomerNote->is_error())
                {
                    $oAlert->set_type("e");
                    $oAlert->set_title(tr_data_not_saved);
                    $oAlert->set_content(tr_error_trying_to_save);
                }
                else//insert ok
                {
                    $this->set_get("id",$this->oCustomerNote->get_last_insert_id());
                    $this->email_onsave($this->get_get("id"));
                    $oAlert->set_title(tr_data_saved);
                    $this->reset_post();
                    //$this->go_to_after_succes_cud();
                }
            }//no error
        }//fin if is_inserting (post action=save)
        //Si hay errores se recupera desde post
        if($arErrData) $oForm = $this->build_insert_form(1);
        else $oForm = $this->build_insert_form();
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
        $this->oView->add_var($oScrumbs,"oScrumbs");
        $this->oView->add_var($oTabs,"oTabs");
        $this->oView->add_var($oOpButtons,"oOpButtons");
        $this->oView->add_var($oAlert,"oAlert");
        $this->oView->add_var($oForm,"oForm");
        $this->oView->add_var($oJavascript,"oJavascript");
        $this->oView->show_page();
    }//insert()
//</editor-fold>

//<editor-fold defaultstate="collapsed" desc="UPDATE">
    //update_1
    protected function build_update_scrumbs()
    {
        $arLinks = array();
        $sUrlTab = $this->build_url();;
        $arLinks["list"]=array("href"=>$sUrlTab,"innerhtml"=>tr_crn_entities);
        $sUrlTab = $this->build_url($this->sModuleName,NULL,"update","id=".$this->get_get("id"));
        $arLinks["detail"]=array("href"=>$sUrlTab,"innerhtml"=>tr_crn_entity.": ".$this->oCustomerNote->get_id()." - ".$this->oCustomerNote->get_description());
        $oScrumbs = new AppHelperBreadscrumbs($arLinks);
        return $oScrumbs;
    }//build_update_scrumbs()

    //update_2
    protected function build_update_tabs()
    {
        $arTabs = array();
        $sUrlTab = $this->build_url($this->sModuleName,NULL,"update","id=".$this->get_get("id"));
        $arTabs["detail"]=array("href"=>$sUrlTab,"innerhtml"=>tr_crn_updtabs_detail);
        //$sUrlTab = $this->build_url($this->sModuleName,"foreignamodule","get_list_by_foreign","id_foreign=".$this->get_get("id_parent_foreign"));
        //$arTabs["foreigndata"]=array("href"=>$sUrlTab,"innerhtml"=>tr_crn_updtabs_foreigndata);
        $oTabs = new AppHelperHeadertabs($arTabs,"detail");
        return $oTabs;
    }//build_update_tabs()

    //update_3
    protected function build_update_opbuttons()
    {
        $arOpButtons = array();
        if($this->oPermission->is_select())
            $arOpButtons["list"]=array("href"=>$this->build_url(),"icon"=>"awe-search","innerhtml"=>tr_crn_updopbutton_list);
        //if($this->oPermission->is_insert())
            //$arOpButtons["insert"]=array("href"=>$this->build_url($this->sModuleName,NULL,"insert"),"icon"=>"awe-plus","innerhtml"=>tr_crn_updopbutton_insert);
        if($this->oPermission->is_quarantine())
            $arOpButtons["delete"]=array("href"=>$this->build_url($this->sModuleName,NULL,"quarantine","id=".$this->get_get("id")),"icon"=>"awe-remove","innerhtml"=>tr_crn_updopbutton_quarantine);
        //if($this->oPermission->is_delete())
            //$arOpButtons["delete"]=array("href"=>$this->build_url($this->sModuleName,NULL,"delete","id=".$this->get_get("id")),"icon"=>"awe-remove","innerhtml"=>tr_crn_updopbutton_delete);
        $oOpButtons = new AppHelperButtontabs(tr_crn_entities);
        $oOpButtons->set_tabs($arOpButtons);
        return $oOpButtons;
    }//build_update_opbuttons()

    //update_4
    protected function build_update_fields($usePost=0)
    {
        $arFields = array(); $oAuxField = NULL; $oAuxLabel = NULL;
        //id
        $oAuxField = new HelperInputText("txtId","txtId");
        $oAuxField->is_primarykey();
        $oAuxField->set_value($this->oCustomerNote->get_id());
        if($usePost) $oAuxField->set_value($this->get_post("txtId"));
        $oAuxLabel = new HelperLabel("txtId",tr_crn_upd_id,"lblId");
        $oAuxLabel->add_class("labelpk");
        $oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //id_user
        $oUser = new ModelUser();
        $arOptions = $oUser->get_picklist_custom("id","description","code_type='kuzeta'");
        $oAuxField = new HelperSelect($arOptions,"selIdUser","selIdUser");
        $oAuxField->set_value_to_select($this->get_post("selIdUser"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdUser",tr_crn_upd_id_user));
        $oAuxField->set_value_to_select($this->oCustomerNote->get_id_user());
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdUser"));
        $oAuxLabel = new HelperLabel("selIdUser",tr_crn_upd_id_user,"lblIdUser");
        $oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //id_customer
        $oCustomer = new ModelCustomer();
        $arOptions = $oCustomer->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdCustomer","selIdCustomer");
        $oAuxField->set_value_to_select($this->get_post("selIdCustomer"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdCustomer",tr_crn_upd_id_customer));
        $oAuxField->set_value_to_select($this->oCustomerNote->get_id_customer());
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdCustomer"));
        $oAuxLabel = new HelperLabel("selIdCustomer",tr_crn_upd_id_customer,"lblIdCustomer");
        $oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
 
        //note
        $oAuxField = new HelperTextarea("txaNote","txaNote");
        $oAuxField->set_innerhtml($this->oCustomerNote->get_note());
        if($usePost) $oAuxField->set_innerhtml($this->get_post("txaNote"));
        $oAuxLabel = new HelperLabel("txaNote",tr_crn_upd_note,"lblNote");
        $oAuxLabel->add_class("labelreq");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //id_order
        $oOrder = new ModelOrderHead();
        $arOptions = $oOrder->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdOrder","selIdOrder");
        $oAuxField->set_value_to_select($this->get_post("selIdOrder"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdOrder",tr_crn_upd_id_order));
        $oAuxField->set_value_to_select($this->oCustomerNote->get_id_order());
        if($this->oCustomerNote->get_id_order())
        {
            $oAuxField->readonly();$oAuxField->add_class("readonly");
        }
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdOrder"));
        $oAuxLabel = new HelperLabel("selIdOrder",tr_crn_upd_id_order,"lblIdOrder");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //date
        $oAuxField = new HelperDate("datDate","datDate");
        $oAuxField->set_is_ipadiphone($this->is_ipad()||$this->is_iphone());
        $oAuxField->set_value(dbbo_date($this->oCustomerNote->get_date(),"/"));
        if($usePost) $oAuxField->set_value($this->get_post("datDate"));
        $oAuxLabel = new HelperLabel("datDate",tr_crn_upd_date,"lblDate");
        $oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //hour
        $oAuxField = new HelperInputText("txtHour","txtHour");
        $oAuxField->set_value(dbbo_time6($this->oCustomerNote->get_hour()));
        if($usePost) $oAuxField->set_value($this->get_post("txtHour"));
        $oAuxLabel = new HelperLabel("txtHour",tr_crn_upd_hour,"lblHour");
        $oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
                
        //BUTTON SAVE
        $oAuxField = new HelperButtonBasic("butSave",tr_crn_upd_savebutton);
        $oAuxField->add_class("btn btn-primary");
        $oAuxField->set_js_onclick("update();");
        if($this->oPermission->is_update())
            $arFields[] = new ApphelperFormactions(array($oAuxField));
        //AUDIT INFO
        $sRegInfo = $this->get_audit_info($this->oCustomerNote->get_insert_user(),$this->oCustomerNote->get_insert_date()
        ,$this->oCustomerNote->get_update_user(),$this->oCustomerNote->get_update_date());
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

    //update_5
    protected function get_update_validate()
    {
        $arFieldsConfig = array();
        $arFieldsConfig["id"] = array("controlid"=>"txtId","label"=>tr_crn_upd_id,"length"=>9,"type"=>array("numeric","required"));
        //$arFieldsConfig["code_erp"] = array("controlid"=>"txtCodeErp","label"=>tr_crn_upd_code_erp,"length"=>25,"type"=>array());
        //$arFieldsConfig["id_user"] = array("controlid"=>"selIdUser","label"=>tr_crn_upd_id_user,"length"=>9,"type"=>array());
        //$arFieldsConfig["id_customer"] = array("controlid"=>"selIdCustomer","label"=>tr_crn_upd_id_customer,"length"=>9,"type"=>array());
        //$arFieldsConfig["id_order"] = array("controlid"=>"selIdOrder","label"=>tr_crn_upd_id_order,"length"=>9,"type"=>array());
        //$arFieldsConfig["description"] = array("controlid"=>"txtDescription","label"=>tr_crn_upd_description,"length"=>200,"type"=>array());
        $arFieldsConfig["note"] = array("controlid"=>"txaNote","label"=>tr_crn_upd_note,"length"=>255,"type"=>array("required"));
        $arFieldsConfig["date"] = array("controlid"=>"datDate","label"=>tr_crn_upd_date,"length"=>10,"type"=>array());
        $arFieldsConfig["hour"] = array("controlid"=>"txtHour","label"=>tr_crn_upd_hour,"length"=>9,"type"=>array());
        return $arFieldsConfig;
    }//get_update_validate

    //update_6
    protected function build_update_form($usePost=0)
    {
        $id = $this->oCustomerNote->get_id();
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
                $arFieldsValues["date"] = bodb_date($arFieldsValues["date"],"/");
                $arFieldsValues["hour"] = bodb_time6($arFieldsValues["hour"]);                
                $this->oCustomerNote->set_attrib_value($arFieldsValues);
                //$this->oCustomerNote->set_description($oCustomerNote->get_field1()." ".$oCustomerNote->get_field2());
                $this->oCustomerNote->set_update_user($this->oSessionUser->get_id());
                $this->oCustomerNote->autoupdate();
                if($this->oCustomerNote->is_error())
                {
                    $oAlert->set_type("e");
                    $oAlert->set_title(tr_data_not_saved);
                    $oAlert->set_content(tr_error_trying_to_save);
                }//no error
                else//update ok
                {
                    //$this->oCustomerNote->load_by_id();
                    $this->email_onsave($this->oCustomerNote->get_id());
                    $oAlert->set_title(tr_data_saved);
                    $this->reset_post();
                    //$this->go_to_after_succes_cud();
                }//error save
            }//error validation
        }//is_updating()
        if($arErrData) $oForm = $this->build_update_form(1);
        else $oForm = $this->build_update_form(); 
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
            $this->oCustomerNote->set_id($id);
            $this->oCustomerNote->autodelete();
            if($this->oCustomerNote->is_error())
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
            $this->oCustomerNote->set_id($id);
            $this->oCustomerNote->autodelete();
            if($this->oCustomerNote->is_error())
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
            $this->oCustomerNote->set_id($id);
            $this->oCustomerNote->autoquarantine();
            if($this->oCustomerNote->is_error())
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
            $this->oCustomerNote->set_id($id);
            $this->oCustomerNote->autoquarantine();
            if($this->oCustomerNote->is_error())
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
        $arOpButtons["filters"]=array("href"=>"javascript:reset_filters();","icon"=>"awe-magic","innerhtml"=>tr_crn_clear_filters);
        $arOpButtons["reload"]=array("href"=>"javascript:TfwControl.form_submit();","icon"=>"awe-refresh","innerhtml"=>tr_crn_refresh);
        $arOpButtons["multiadd"]=array("href"=>"javascript:multiadd();","icon"=>"awe-external-link","innerhtml"=>"tr_multiadd");
        $arOpButtons["closeme"]=array("href"=>"javascript:closeme();","icon"=>"awe-remove-sign","innerhtml"=>tr_crn_closeme);
        $oOpButtons = new AppHelperButtontabs(tr_crn_entities);
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
        //id_user
        $this->set_filter("id_user","selIdUser");
        //id_customer
        $this->set_filter("id_customer","selIdCustomer");
        //id_order
        $this->set_filter("id_order","selIdOrder");
        //description
        //$this->set_filter("description","txtDescription",array("operator"=>"like"));
        //note
        $this->set_filter("note","txaNote",array("operator"=>"like"));
        //date
        $this->set_filter("date","datDate",array("operator"=>"like"));
        //hour
        $this->set_filter("hour","txtHour",array("operator"=>"like"));
    }//load_config_multiassign_filters()

    //multiassign_3
    protected function get_multiassign_filters()
    {
        //CAMPOS
        //id
        $oAuxField = new HelperInputText("txtId","txtId");
        $oAuxField->set_value($this->get_post("txtId"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtId",tr_crn_fil_id));
        $arFields[] = $oAuxWrapper;
        //code_erp
        //$oAuxField = new HelperInputText("txtCodeErp","txtCodeErp");
        //$oAuxField->set_value($this->get_post("txtCodeErp"));
        //$oAuxField->on_entersubmit();
        //$oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtCodeErp",tr_crn_fil_code_erp));
        //$arFields[] = $oAuxWrapper;
        //id_user
        $oUser = new ModelUser();
        $arOptions = $oUser->get_picklist_custom("id","description","code_type='kuzeta'");
        $oAuxField = new HelperSelect($arOptions,"selIdUser","selIdUser");
        $oAuxField->set_value_to_select($this->get_post("selIdUser"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdUser",tr_crn_fil_id_user));
        $arFields[] = $oAuxWrapper;
        //id_customer
        $oCustomer = new ModelCustomer();
        $arOptions = $oCustomer->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdCustomer","selIdCustomer");
        $oAuxField->set_value_to_select($this->get_post("selIdCustomer"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdCustomer",tr_crn_fil_id_customer));
        $arFields[] = $oAuxWrapper;
        //id_order
        $oOrder = new ModelOrderHead();
        $arOptions = $oOrder->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdOrder","selIdOrder");
        $oAuxField->set_value_to_select($this->get_post("selIdOrder"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdOrder",tr_crn_fil_id_order));
        $arFields[] = $oAuxWrapper;
        //description
        //$oAuxField = new HelperInputText("txtDescription","txtDescription");
        //$oAuxField->set_value($this->get_post("txtDescription"));
        //$oAuxField->on_entersubmit();
        //$oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtDescription",tr_crn_fil_description));
        //$arFields[] = $oAuxWrapper;
        //note
        $oAuxField = new HelperTextarea("txaNote","txaNote");
        $oAuxField->set_innerhtml($this->get_post("txaNote"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txaNote",tr_crn_fil_note));
        $arFields[] = $oAuxWrapper;
        //date
        $oAuxField = new HelperDate("datDate","datDate");
        $oAuxField->set_is_ipadiphone($this->is_ipad()||$this->is_iphone());
        $oAuxField->set_value($this->get_post("datDate"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("datDate",tr_crn_fil_date));
        $arFields[] = $oAuxWrapper;
        //hour
        $oAuxField = new HelperInputText("txtHour","txtHour");
        $oAuxField->set_value($this->get_post("txtHour"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtHour",tr_crn_fil_hour));
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
        //id_user
        $this->set_filter_value("id_user",$this->get_post("selIdUser"));
        //id_customer
        $this->set_filter_value("id_customer",$this->get_post("selIdCustomer"));
        //id_order
        $this->set_filter_value("id_order",$this->get_post("selIdOrder"));
        //description
        //$this->set_filter_value("description",$this->get_post("txtDescription"));
        //note
        $this->set_filter_value("note",$this->get_post("txaNote"));
        //date
        $this->set_filter_value("date",$this->get_post("datDate"));
        //hour
        $this->set_filter_value("hour",$this->get_post("txtHour"));
    }//set_multiassignfilters_from_post()

    //multiassign_5
    protected function get_multiassign_columns()
    {
        $arColumns["id"] = tr_crn_col_id;
        //$arColumns["code_erp"] = tr_crn_col_code_erp;
        $arColumns["id_user"] = tr_crn_col_id_user;
        $arColumns["notifier"] = tr_crn_col_id_user;
        $arColumns["id_customer"] = tr_crn_col_id_customer;
        $arColumns["customer"] = tr_crn_col_id_customer;
        $arColumns["id_order"] = tr_crn_col_id_order;
        $arColumns["orderhead"] = tr_crn_col_id_order;
        //$arColumns["description"] = tr_crn_col_description;
        $arColumns["note"] = tr_crn_col_note;
        $arColumns["date"] = tr_crn_col_date;
        $arColumns["hour"] = tr_crn_col_hour;
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
        $this->oCustomerNote->set_orderby($this->get_orderby());
        $this->oCustomerNote->set_ordertype($this->get_ordertype());
        $this->oCustomerNote->set_filters($this->get_filter_searchconfig());
        //hierarchy recover
        //$this->oCustomerNote->set_select_user($this->oSessionUser->get_id());
        //RECOVER DATALIST
        $arList = $this->oCustomerNote->get_select_all_ids();
        $iRequestPage = $this->get_post("selPage");
        $oPage = new ComponentPage($arList,$iRequestPage);
        $arList = $oPage->get_items_to_show();
        $arList = $this->oCustomerNote->get_select_all_by_ids($arList);
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
        $oOpButtons = new AppHelperButtontabs(tr_crn_entities);
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
        $arButTabs["filters"]=array("href"=>"javascript:reset_filters();","icon"=>"awe-magic","innerhtml"=>tr_crn_clear_filters);
        $arButTabs["reload"]=array("href"=>"javascript:TfwControl.form_submit();","icon"=>"awe-refresh","innerhtml"=>tr_crn_refresh);
        $arButTabs["closeme"]=array("href"=>"javascript:closeme();","icon"=>"awe-remove-sign","innerhtml"=>tr_crn_closeme);
        return $arButTabs;
    }//build_singleassign_buttons()

    //singleassign_2
    protected function load_config_singleassign_filters()
    {
        //id
        $this->set_filter("id","txtId",array("operator"=>"like"));
        //code_erp
        //$this->set_filter("code_erp","txtCodeErp",array("operator"=>"like"));
        //id_user
        $this->set_filter("id_user","selIdUser");
        //id_customer
        $this->set_filter("id_customer","selIdCustomer");
        //id_order
        $this->set_filter("id_order","selIdOrder");
        //description
        //$this->set_filter("description","txtDescription",array("operator"=>"like"));
        //note
        $this->set_filter("note","txaNote",array("operator"=>"like"));
        //date
        $this->set_filter("date","datDate",array("operator"=>"like"));
        //hour
        $this->set_filter("hour","txtHour",array("operator"=>"like"));
    }//load_config_singleassign_filters()

    //singleassign_3
    protected function get_singleassign_filters()
    {
        //CAMPOS
        //id
        $oAuxField = new HelperInputText("txtId","txtId");
        $oAuxField->set_value($this->get_post("txtId"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtId",tr_crn_fil_id));
        $arFields[] = $oAuxWrapper;
        //code_erp
        //$oAuxField = new HelperInputText("txtCodeErp","txtCodeErp");
        //$oAuxField->set_value($this->get_post("txtCodeErp"));
        //$oAuxField->on_entersubmit();
        //$oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtCodeErp",tr_crn_fil_code_erp));
        //$arFields[] = $oAuxWrapper;
        //id_user
        $oUser = new ModelUser();
        $arOptions = $oUser->get_picklist_custom("id","description","code_type='kuzeta'");
        $oAuxField = new HelperSelect($arOptions,"selIdUser","selIdUser");
        $oAuxField->set_value_to_select($this->get_post("selIdUser"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdUser",tr_crn_fil_id_user));
        $arFields[] = $oAuxWrapper;
        //id_customer
        $oCustomer = new ModelCustomer();
        $arOptions = $oCustomer->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdCustomer","selIdCustomer");
        $oAuxField->set_value_to_select($this->get_post("selIdCustomer"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdCustomer",tr_crn_fil_id_customer));
        $arFields[] = $oAuxWrapper;
        //id_order
        $oOrder = new ModelOrderHead();
        $arOptions = $oOrder->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdOrder","selIdOrder");
        $oAuxField->set_value_to_select($this->get_post("selIdOrder"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdOrder",tr_crn_fil_id_order));
        $arFields[] = $oAuxWrapper;
        //description
        //$oAuxField = new HelperInputText("txtDescription","txtDescription");
        //$oAuxField->set_value($this->get_post("txtDescription"));
        //$oAuxField->on_entersubmit();
        //$oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtDescription",tr_crn_fil_description));
        //$arFields[] = $oAuxWrapper;
        //note
        $oAuxField = new HelperTextarea("txaNote","txaNote");
        $oAuxField->set_innerhtml($this->get_post("txaNote"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txaNote",tr_crn_fil_note));
        $arFields[] = $oAuxWrapper;
        //date
        $oAuxField = new HelperDate("datDate","datDate");
        $oAuxField->set_is_ipadiphone($this->is_ipad()||$this->is_iphone());
        $oAuxField->set_value($this->get_post("datDate"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("datDate",tr_crn_fil_date));
        $arFields[] = $oAuxWrapper;
        //hour
        $oAuxField = new HelperInputText("txtHour","txtHour");
        $oAuxField->set_value($this->get_post("txtHour"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtHour",tr_crn_fil_hour));
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
        //id_user
        $this->set_filter_value("id_user",$this->get_post("selIdUser"));
        //id_customer
        $this->set_filter_value("id_customer",$this->get_post("selIdCustomer"));
        //id_order
        $this->set_filter_value("id_order",$this->get_post("selIdOrder"));
        //description
        //$this->set_filter_value("description",$this->get_post("txtDescription"));
        //note
        $this->set_filter_value("note",$this->get_post("txaNote"));
        //date
        $this->set_filter_value("date",$this->get_post("datDate"));
        //hour
        $this->set_filter_value("hour",$this->get_post("txtHour"));
    }//set_singleassignfilters_from_post()

    //singleassign_5
    protected function get_singleassign_columns()
    {
        $arColumns["id"] = tr_crn_col_id;
        //$arColumns["code_erp"] = tr_crn_col_code_erp;
        $arColumns["id_user"] = tr_crn_col_id_user;
        $arColumns["notifier"] = tr_crn_col_id_user;
        $arColumns["id_customer"] = tr_crn_col_id_customer;
        $arColumns["customer"] = tr_crn_col_id_customer;
        $arColumns["id_order"] = tr_crn_col_id_order;
        $arColumns["orderhead"] = tr_crn_col_id_order;
        //$arColumns["description"] = tr_crn_col_description;
        $arColumns["note"] = tr_crn_col_note;
        $arColumns["date"] = tr_crn_col_date;
        $arColumns["hour"] = tr_crn_col_hour;
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
        $this->oCustomerNote->set_orderby($this->get_orderby());
        $this->oCustomerNote->set_ordertype($this->get_ordertype());
        $this->oCustomerNote->set_filters($this->get_filter_searchconfig());
        $arList = $this->oCustomerNote->get_select_all_ids();
        $iRequestPage = $this->get_post("selPage");
        $oPage = new ComponentPage($arList,$iRequestPage);
        $arList = $oPage->get_items_to_show();
        $arList = $this->oCustomerNote->get_select_all_by_ids($arList);
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
        $oOpButtons = new AppHelperButtontabs(tr_crn_entities);
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

//<editor-fold defaultstate="collapsed" desc="HISTORY">
    //history_1
    protected function build_history_scrumbs()
    {
        $arLinks = array();
//        $sUrlTab = $this->build_url($this->sModuleName,NULL,"get_history");
//        $arLinks["list"]=array("href"=>$sUrlTab,"innerhtml"=>tr_crn_entities);
        $oScrumbs = new AppHelperBreadscrumbs($arLinks);
        return $oScrumbs;
    }

    //history_2
    protected function build_history_tabs()
    {
        $arTabs = array();
        //$sUrlTab = $this->build_url($this->sModuleName,NULL,"get_history","id=".$this->get_get("id_parent_foreign"));
        //$arTabs["list"]=array("href"=>$sUrlTab,"innerhtml"=>tr_crn_historytabs_1);
        //$sUrlTab = $this->get_url($this->sModuleName,NULL,"get_history_by_foreign","id=".$this->get_get("id_parent_foreign"));
        //$arTabs["listbyforeign"]=array("href"=>$sUrlTab,"innerhtml"=>tr_crn_historytabs_2);
        $oTabs = new AppHelperHeadertabs($arTabs,"list");
        return $oTabs;
    }

    //history_3
    protected function build_historyoperation_buttons()
    {
        $arOpButtons = array();
        $arOpButtons["closeme"]=array("href"=>"javascript:closeme();","icon"=>"awe-remove-sign","innerhtml"=>tr_crn_closeme);
//        $arOpButtons["filters"]=array("href"=>"javascript:reset_filters();","icon"=>"awe-magic","innerhtml"=>tr_crn_historyopbutton_filters);
//        $arOpButtons["reload"]=array("href"=>"javascript:TfwControl.form_submit();","icon"=>"awe-refresh","innerhtml"=>tr_crn_historyopbutton_reload);
//        if($this->oPermission->is_insert())
//            $arOpButtons["insert"]=array("href"=>$this->build_url($this->sModuleName,NULL,"insert"),"icon"=>"awe-plus","innerhtml"=>tr_crn_historyopbutton_insert);
//        if($this->oPermission->is_quarantine())
//            $arOpButtons["multiquarantine"]=array("href"=>"javascript:multi_quarantine();","icon"=>"awe-remove","innerhtml"=>tr_crn_historyopbutton_multiquarantine);
//        //if($this->oPermission->is_delete())
//            //$arOpButtons["multidelete"]=array("href"=>"javascript:multi_delete();","icon"=>"awe-remove","innerhtml"=>tr_crn_historyopbutton_multidelete);
//        //PICK WINDOWS
//        //$arOpButtons["multiassign"]=array("href"=>"javascript:multiassign_window('customernotes',null,'multiassign','customernotes','addexternaldata');","icon"=>"awe-external-link","innerhtml"=>tr_crn_historyopbutton_multiassign);
//        //$arOpButtons["singleassign"]=array("href"=>"javascript:single_pick('customernotes','singleassign','txtI','txtI');","icon"=>"awe-external-link","innerhtml"=>tr_crn_historyopbutton_singleassign);
        $oOpButtons = new AppHelperButtontabs(tr_crn_entities_of.$this->oCustomer->get_description());
        $oOpButtons->set_tabs($arOpButtons);
        
        return $oOpButtons;
    }//build_historyoperation_buttons()

    //history_4
    protected function load_config_history_filters()
    {
//        //id
//        $this->set_filter("id","txtId",array("operator"=>"like"));
//        //code_erp
//        //$this->set_filter("code_erp","txtCodeErp",array("operator"=>"like"));
//        //id_user
//        $this->set_filter("id_user","selIdUser");
//        //id_customer
//        $this->set_filter("id_customer","selIdCustomer");
//        //id_order
//        $this->set_filter("id_order","selIdOrder");
//        //description
//        //$this->set_filter("description","txtDescription",array("operator"=>"like"));
//        //note
//        $this->set_filter("note","txaNote",array("operator"=>"like"));
//        //date
//        $this->set_filter("date","datDate",array("operator"=>"like"));
//        //hour
//        $this->set_filter("hour","txtHour",array("operator"=>"like"));
    }//load_config_history_filters()

    //history_5
    protected function set_historyfilters_from_post()
    {
        //id
//        $this->set_filter_value("id",$this->get_post("txtId"));
//        //code_erp
//        //$this->set_filter_value("code_erp",$this->get_post("txtCodeErp"));
//        //id_user
//        $this->set_filter_value("id_user",$this->get_post("selIdUser"));
//        //id_customer
//        $this->set_filter_value("id_customer",$this->get_post("selIdCustomer"));
//        //id_order
//        $this->set_filter_value("id_order",$this->get_post("selIdOrder"));
//        //description
//        //$this->set_filter_value("description",$this->get_post("txtDescription"));
//        //note
//        $this->set_filter_value("note",$this->get_post("txaNote"));
//        //date
//        $this->set_filter_value("date",$this->get_post("datDate"));
//        //hour
//        $this->set_filter_value("hour",$this->get_post("txtHour"));
    }//set_historyfilters_from_post()

    //history_6
    protected function get_history_filters()
    {
        $arFields = array();
//        //CAMPOS
//        //id
//        $oAuxField = new HelperInputText("txtId","txtId");
//        $oAuxField->set_value($this->get_post("txtId"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtId",tr_crn_fil_id));
//        $arFields[] = $oAuxWrapper;
//        //code_erp
//        //$oAuxField = new HelperInputText("txtCodeErp","txtCodeErp");
//        //$oAuxField->set_value($this->get_post("txtCodeErp"));
//        //$oAuxField->on_entersubmit();
//        //$oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtCodeErp",tr_crn_fil_code_erp));
//        //$arFields[] = $oAuxWrapper;
//        //id_user
//        $oUser = new ModelUser();
//        $arOptions = $oUser->get_picklist_custom("id","description","code_type='kuzeta'");
//        $oAuxField = new HelperSelect($arOptions,"selIdUser","selIdUser");
//        $oAuxField->set_value_to_select($this->get_post("selIdUser"));
//        $oAuxField->set_postback();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdUser",tr_crn_fil_id_user));
//        $arFields[] = $oAuxWrapper;
//        //id_customer
//        $oCustomer = new ModelCustomer();
//        $arOptions = $oCustomer->get_picklist();
//        $oAuxField = new HelperSelect($arOptions,"selIdCustomer","selIdCustomer");
//        $oAuxField->set_value_to_select($this->get_post("selIdCustomer"));
//        $oAuxField->set_postback();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdCustomer",tr_crn_fil_id_customer));
//        $arFields[] = $oAuxWrapper;
//        //id_order
//        $oOrder = new ModelOrderHead();
//        $arOptions = $oOrder->get_picklist();
//        $oAuxField = new HelperSelect($arOptions,"selIdOrder","selIdOrder");
//        $oAuxField->set_value_to_select($this->get_post("selIdOrder"));
//        $oAuxField->set_postback();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdOrder",tr_crn_fil_id_order));
//        $arFields[] = $oAuxWrapper;
//        //description
//        //$oAuxField = new HelperInputText("txtDescription","txtDescription");
//        //$oAuxField->set_value($this->get_post("txtDescription"));
//        //$oAuxField->on_entersubmit();
//        //$oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtDescription",tr_crn_fil_description));
//        //$arFields[] = $oAuxWrapper;
//        //note
////        $oAuxField = new HelperTextarea("txaNote","txaNote");
////        $oAuxField->set_innerhtml($this->get_post("txaNote"));
////        $oAuxField->on_entersubmit();
////        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txaNote",tr_crn_fil_note));
////        $arFields[] = $oAuxWrapper;
//        //date
//        $oAuxField = new HelperDate("datDate","datDate");
//        $oAuxField->set_value($this->get_post("datDate"));
//        $oAuxField->set_postback();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("datDate",tr_crn_fil_date));
//        $arFields[] = $oAuxWrapper;
//        //hour
//        $oAuxField = new HelperInputText("txtHour","txtHour");
//        $oAuxField->set_value($this->get_post("txtHour"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtHour",tr_crn_fil_hour));
//        $arFields[] = $oAuxWrapper;
        return $arFields;
    }//get_history_filters()

    //history_7
    protected function get_history_columns()
    {
        $arColumns["id"] = tr_crn_col_id;
        //$arColumns["code_erp"] = tr_crn_col_code_erp;
        //$arColumns["id_user"] = tr_crn_col_id_user;
        $arColumns["notifier"] = tr_crn_col_id_user;
        //$arColumns["id_customer"] = tr_crn_col_id_customer;
        $arColumns["customer"] = tr_crn_col_id_customer;
        //$arColumns["id_order"] = tr_crn_col_id_order;
        $arColumns["orderhead"] = tr_crn_col_id_order;
        //$arColumns["description"] = tr_crn_col_description;
        $arColumns["note"] = tr_crn_col_note;
        $arColumns["date"] = tr_crn_col_date;
        $arColumns["hour"] = tr_crn_col_hour;
        return $arColumns;
    }//get_history_columns()

    //history_8
    public function get_history()
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
        $arColumns = $this->get_history_columns(); 

        //Carga en la variable global la configuración de los campos que se utilizarán
        //FILTERS
        $this->load_config_history_filters();
        $oFilter = new ComponentFilter();
        $oFilter->set_fieldnames($this->get_filter_fieldnames());
        //Indica que no se guardara en sesion por nombre de campo sino por nombre de control
        //para esto es necesario respetar el estricto camelcase
        $oFilter->use_field_prefix();
        //Guarda en sesion y post los campos enviados, los de orden y página
        $oFilter->refresh();
        $this->set_historyfilters_from_post();

        $arObjFilter = $this->get_history_filters();

        //RECOVER DATALIST
        $this->oCustomerNote->set_orderby($this->get_orderby());
        $this->oCustomerNote->set_ordertype($this->get_ordertype());
        $this->oCustomerNote->add_filter("id_customer",array("value"=>$this->oCustomer->get_id()));
        //hierarchy recover
        $this->oCustomerNote->set_select_user($this->oSessionUser->get_id());
        $arList = $this->oCustomerNote->get_select_all_ids();
        $iRequestPage = $this->get_post("selPage");
        $oPage = new ComponentPage($arList,$iRequestPage);
        $arList = $oPage->get_items_to_show();
        $arList = $this->oCustomerNote->get_select_all_by_ids($arList);
        //TABLE
        //This method adds objects controls to search list form
        $oTableList = new HelperTableTyped($arList,$arColumns);
        $oTableList->set_fields($arObjFilter);
        $oTableList->add_class("table table-striped table-bordered table-condensed");
        //$oTableList->set_keyfields(array("id"));
        $oTableList->is_ordenable();
        $oTableList->set_orderby($this->get_orderby());
        $oTableList->set_orderby_type($this->get_ordertype());
        //COLUMNS CONFIGURATION
        $arFormat = array("date"=>"date","hour"=>"time6");
        $oTableList->set_format_columns($arFormat);
        //parametros a pasar al popup
        //$oTableList->set_multiassign(array("keys"=>array("k"=>1,"k2"=>2)));
        $oTableList->set_current_page($oPage->get_current());
        $oTableList->set_next_page($oPage->get_next());
        $oTableList->set_first_page($oPage->get_first());
        $oTableList->set_last_page($oPage->get_last());
        $oTableList->set_total_regs($oPage->get_total_regs());
        $oTableList->set_total_pages($oPage->get_total());
        //SCRUMBS
        $oScrumbs = $this->build_history_scrumbs();
        //TABS
        $oTabs = $this->build_history_tabs();
        //OPER BUTTONS
        $oOpButtons = $this->build_historyoperation_buttons();
        //JAVASCRIPT
        $oJavascript = new HelperJavascript();
        $oJavascript->set_filters($this->get_filter_controls_id());
        $oJavascript->set_focusid("id_all");
        //VIEW SET
        $this->oView->set_layout("onecolumn");
        $this->oView->add_var($oScrumbs,"oScrumbs");
        $this->oView->add_var($oTabs,"oTabs");
        $this->oView->add_var($oOpButtons,"oOpButtons");
        $this->oView->add_var($oAlert,"oAlert");
        $this->oView->add_var($oJavascript,"oJavascript");
        $this->oView->add_var($oTableList,"oTableList");
        $this->oView->show_page();
    }//get_history()
//</editor-fold>

}//end controller
