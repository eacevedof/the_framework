<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.7
 * @name PartialNotes
 * @file partial_notes.php 
 * @date 31-10-2014 20:14 (SPAIN)
 * @observations: Kuzeta 
 * @require controller_customers.php
 */
import_apptranslate("customernotes");
import_appcontroller("customernotes");
class PartialNotes extends ControllerCustomerNotes
{   

    public function __construct()
    {
        $this->sModuleName = "customernotes";
        //console,pgpage,clientdevice,sesuser,aftersucces,currop  | log,session,permission,view
        parent::__construct($this->sModuleName);
        
        $this->oCustomer = new ModelCustomer();
        $this->oCustomer->set_platform($this->oSessionUser->get_platform());
        
        if($this->is_inget("id_customer_note"))
        {
            $this->oCustomerNote->set_id($this->get_get("id_customer_note"));
            $this->oCustomerNote->load_by_id();
        }
        if($this->is_inget("id_customer"))
        {
            $this->oCustomer->set_id($this->get_get("id_customer"));
            $this->oCustomer->load_by_id();
        }
        //$this->oSessionUser->set_dataowner_table($this->oCustomerNote->get_table_name());
        //$this->oSessionUser->set_dataowner_tablefield("id_customer");
        //$this->oSessionUser->set_dataowner_keys(array("id"=>$this->oCustomerNote->get_id()));
    }
//<editor-fold defaultstate="collapsed" desc="LIST">
    //list_1
    protected function build_list_scrumbs()
    {
        $arLinks = array();
        $sUrlLink = $this->build_url();
        $arLinks["list"]=array("href"=>$sUrlLink,"innerhtml"=>"Customers");                
        $sUrlLink = $this->build_url("customers",NULL,"update","id=".$this->get_get("id_customer"));
        $arLinks["detail"]=array("href"=>$sUrlLink,"innerhtml"=>"Customer: ".$this->oCustomer->get_id()." - ".$this->oCustomer->get_description());
        $sUrlLink = $this->build_url("customers","notes","get_list","id_customer=".$this->get_get("id_customer"));
        $arLinks["notes"]=array("href"=>$sUrlLink,"innerhtml"=>tr_crn_entities);        
        $oScrumbs = new AppHelperBreadscrumbs($arLinks);
        return $oScrumbs;
    }

    //list_2
    protected function build_list_tabs()
    {
        $arTabs = array();
        $sUrlTab = $this->build_url("customers",NULL,"update","id=".$this->get_get("id_customer"));
        $arTabs["detail"]=array("href"=>$sUrlTab,"innerhtml"=>"Detail");
        $sUrlTab = $this->build_url("customers","notes","get_list","id_customer=".$this->get_get("id_customer"));
        $arTabs["notes"]=array("href"=>$sUrlTab,"innerhtml"=>tr_crn_entities);
        $oTabs = new AppHelperHeadertabs($arTabs,"notes");
        return $oTabs;
    }
    
    //list_3
    protected function build_listoperation_buttons()
    {
        $arOpButtons = array();
        $arOpButtons["filters"]=array("href"=>"javascript:reset_filters();","icon"=>"awe-magic","innerhtml"=>tr_crn_listopbutton_filters);
        $arOpButtons["reload"]=array("href"=>"javascript:TfwControl.form_submit();","icon"=>"awe-refresh","innerhtml"=>tr_crn_listopbutton_reload);
        if($this->oPermission->is_insert())
            $arOpButtons["insert"]=array("href"=>$this->build_url("customers","notes","insert","id_customer=".$this->get_get("id_customer")),"icon"=>"awe-plus","innerhtml"=>tr_crn_listopbutton_insert);
        if($this->oPermission->is_quarantine())
            $arOpButtons["multiquarantine"]=array("href"=>"javascript:multi_quarantine();","icon"=>"awe-remove","innerhtml"=>tr_crn_listopbutton_multiquarantine);
        //if($this->oPermission->is_delete())
            //$arOpButtons["multidelete"]=array("href"=>"javascript:multi_delete();","icon"=>"awe-remove","innerhtml"=>tr_crn_listopbutton_multidelete);
        $oOpButtons = new AppHelperButtontabs(tr_crn_entities_of.$this->oCustomer->get_description());
        $oOpButtons->set_tabs($arOpButtons);
        return $oOpButtons;
    }//build_listoperation_buttons()
    
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
        //id_user
        $oUser = new ModelUser();
        $arOptions = $oUser->get_picklist_custom("id","description","code_type='kuzeta'");
        $oAuxField = new HelperSelect($arOptions,"selIdUser","selIdUser");
        $oAuxField->set_value_to_select($this->get_post("selIdUser"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdUser",tr_crn_fil_id_user));
        $arFields[] = $oAuxWrapper;
        
        //id_order
        $oOrder = new ModelOrderHead();
        $arOptions = $oOrder->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdOrder","selIdOrder");
        $oAuxField->set_value_to_select($this->get_post("selIdOrder"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdOrder",tr_crn_fil_id_order));
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
    }//get_list_filters()

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
        $this->oCustomerNote->add_filter("id_customer",array("value"=>$this->oCustomer->get_id()));
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
        //$oTableList->set_module($this->get_current_module());
        $oTableList->set_url_update($this->build_url("customers","notes","update","id_customer=".$this->oCustomer->get_id()));
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
        $sUrlLink = $this->build_url();
        $arLinks["list"]=array("href"=>$sUrlLink,"innerhtml"=>"Customers");                
        $sUrlLink = $this->build_url("customers",NULL,"update","id=".$this->get_get("id_customer"));
        $arLinks["detail"]=array("href"=>$sUrlLink,"innerhtml"=>"Customer: ".$this->oCustomer->get_id()." - ".$this->oCustomer->get_description());
        $sUrlLink = $this->build_url("customers","notes","get_list","id_customer=".$this->get_get("id_customer"));
        $arLinks["notes"]=array("href"=>$sUrlLink,"innerhtml"=>tr_crn_entities);
        $sUrlLink = $this->build_url("customers","notes","insert","id_customer=".$this->get_get("id_customer"));
        $arLinks["newnote"]=array("href"=>$sUrlLink,"innerhtml"=>tr_crn_entity_new);        
        $oScrumbs = new AppHelperBreadscrumbs($arLinks);
        return $oScrumbs;
    }//build_insert_scrumbs()

    //insert_2
    protected function build_insert_tabs()
    {
        $arTabs = array();
//        $sUrlTab = $this->build_url("customers",NULL,"update","id=".$this->get_get("id_customer"));
//        $arTabs["detail"]=array("href"=>$sUrlTab,"innerhtml"=>tr_crn_updtabs_detail);               
//        $sUrlTab = $this->build_url("customers","notes","insert","id_customer=".$this->get_get("id_customer"));
//        $arTabs["list"]=array("href"=>$sUrlTab,"innerhtml"=>tr_crn_entity_insert);        
//        $sUrlTab = $this->build_url("customers","notes","insert","id_customer=".$this->get_get("id_customer"));
//        $arTabs["list"]=array("href"=>$sUrlTab,"innerhtml"=>tr_crn_entity_insert);
        $oTabs = new AppHelperHeadertabs($arTabs,"insert1");
        return $oTabs;
    }//build_insert_tabs()

    //insert_3
    protected function build_insert_opbuttons()
    {
        $arOpButtons = array();
        $sUrl = $this->build_url("customers","notes","get_list","id_customer=".$this->oCustomer->get_id());
        $arOpButtons["list"] = array("href"=>$sUrl,"icon"=>"awe-search","innerhtml"=>tr_crn_insopbutton_list);
        //$arOpButtons["extra"] = array("href"=>$this->build_url("customers","notes"),"icon"=>"awe-xxxx","innerhtml"=>tr_crn_insopbutton_extra1);
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
        $oAuxField->set_value_to_select($this->oCustomer->get_id());
        $oAuxField->readonly();$oAuxField->add_class("readonly");
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdCustomer",tr_crn_ins_id_customer));
        $oAuxLabel = new HelperLabel("selIdCustomer",tr_crn_ins_id_customer,"lblIdCustomer");
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
        $arOptions = $oOrder->get_picklist_custom("id","description","id_customer=".$this->oCustomer->get_id());
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

    
    //insert_4
    public function insert() 
    {
        parent::insert();
    }

//</editor-fold>

//<editor-fold defaultstate="collapsed" desc="UPDATE">
    //update_1
    protected function build_update_scrumbs()
    {
        $arLinks = array();
        $sUrlLink = $this->build_url();
        $arLinks["list"]=array("href"=>$sUrlLink,"innerhtml"=>"Customers");                
        $sUrlLink = $this->build_url("customers",NULL,"update","id=".$this->get_get("id_customer"));
        $arLinks["detail"]=array("href"=>$sUrlLink,"innerhtml"=>"Customer: ".$this->oCustomer->get_id()." - ".$this->oCustomer->get_description());
        $sUrlLink = $this->build_url("customers","notes","get_list","id_customer=".$this->get_get("id_customer"));
        $arLinks["notes"]=array("href"=>$sUrlLink,"innerhtml"=>tr_crn_entities);
        $sParams = "id_customer=".$this->get_get("id_customer")."&id=".$this->oCustomerNote->get_id();
        $sUrlLink = $this->build_url("customers","notes","update",$sParams);
        $arLinks["detailnote"]=array("href"=>$sUrlLink,"innerhtml"=>tr_crn_entity.": ".$this->oCustomerNote->get_id());        
        $oScrumbs = new AppHelperBreadscrumbs($arLinks);
        return $oScrumbs;
    }//build_update_scrumbs()

    //update_2
    protected function build_update_tabs()
    {
        $arTabs = array();
        //$sUrlTab = $this->build_url("customers",NULL,"update","id=".$this->oCustomerNote->get_id_customer());
        //$arTabs["detail"]=array("href"=>$sUrlTab,"innerhtml"=>tr_crn_updtabs_detail);
        //$sUrlTab = $this->build_url("customers","notes","get_list","id_customer=".$this->oCustomerNote->get_id_customer());
        //$arTabs["notes"]=array("href"=>$sUrlTab,"innerhtml"=>tr_crn_entities);
        //$sUrlTab =  $this->build_url("customers","foreignamodule","get_list_by_foreign","id_foreign=".$this->oCustomerNote->get_id_customer());
        //$arTabs["foreigndata"]=array("href"=>$sUrlTab,"innerhtml"=>tr_crn_updtabs_foreigndata);
        $oTabs = new AppHelperHeadertabs($arTabs,"detail");
        return $oTabs;
    }//build_update_tabs()

    //update_3
    protected function build_update_opbuttons()
    {
        $arOpButtons = array();
        if($this->oPermission->is_select())
            $arOpButtons["list"]=array("href"=>$this->build_url("customers","notes","get_list","id_customer=".$this->oCustomerNote->get_id_customer()),"icon"=>"awe-search","innerhtml"=>tr_crn_updopbutton_list);
        //if($this->oPermission->is_insert())
            //$arOpButtons["insert"]=array("href"=>$this->build_url("customers",NULL,"insert"),"icon"=>"awe-plus","innerhtml"=>tr_crn_updopbutton_insert);
        if($this->oPermission->is_quarantine())
            $arOpButtons["delete"]=array("href"=>$this->build_url("customers","notes","quarantine","id_customer=".$this->oCustomerNote->get_id_customer()."&id=".$this->oCustomerNote->get_id()),"icon"=>"awe-remove","innerhtml"=>tr_crn_updopbutton_quarantine);
        //if($this->oPermission->is_delete())
            //$arOpButtons["delete"]=array("href"=>$this->build_url("customers",NULL,"delete","id=".$this->get_get("id")),"icon"=>"awe-remove","innerhtml"=>tr_crn_updopbutton_delete);
        $oOpButtons = new AppHelperButtontabs(tr_crn_entity);
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
        //bugpg(); die;
        //$this->go_to_401(($this->oPermission->is_not_read() && $this->oPermission->is_not_update())||$this->oSessionUser->is_not_dataowner());
        //$this->go_to_401($this->oPermission->is_not_read() && $this->oPermission->is_not_update());
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
                    $oAlert->set_title(tr_data_saved);
                    $this->reset_post();
                    //$this->go_to_after_succes_cud();
                }//error save
            }//error validation
        }//is_updating()
        //bug($this->oCustomerNote);
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

        if($this->isError)
        {
            
        }
        else 
        {
            $sUrl = $this->build_url("customers","notes","get_list","id_customer=".$this->get_get("id_customer"));
            $this->go_to_url($sUrl);
        }
    }//quarantine()

//</editor-fold>
    
}