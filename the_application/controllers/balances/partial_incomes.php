<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.2
 * @name PartialIncomes
 * @file partial_incomes.php 
 * @date 25-10-2014 13:01 (SPAIN)
 * @observations: 
 * @require controller_balances.php
 */
import_apptranslate("balances,balanceincomes");
import_appcontroller("balances");
import_model("balance_income,balance_array");

class PartialIncomes extends ControllerBalances
{   
    protected $oBalanceIncome;
    protected $arToday;

    public function __construct()
    {
        $this->arToday["bo"] = date("Ymd");
        $this->arToday["user"] = date("d/m/Y");
        $this->sModuleName = "balances";
        //console,pgpage,clientdevice,sesuser,aftersucces,currop  | log,session,permission,view
        parent::__construct($this->sModuleName);
        
        $this->oBalanceIncome = new ModelBalanceIncome();
        $this->oBalanceIncome->set_platform($this->oSessionUser->get_platform());
        
        if($this->get_get("id"))
        {
            $this->oBalanceIncome->set_id($this->get_get("id"));
            $this->oBalanceIncome->load_by_id();
        }
        
        //$this->oSessionUser->set_dataowner_table($this->oBalanceIncome->get_table_name());
        //$this->oSessionUser->set_dataowner_tablefield("id_customer");
        //$this->oSessionUser->set_dataowner_keys(array("id"=>$this->oBalanceIncome->get_id()));
    }
    
//<editor-fold defaultstate="collapsed" desc="LIST">
    //list_1
    protected function build_list_scrumbs()
    {
        $arLinks = array();
        $sUrlLink = $this->build_url();
        $arLinks["list"]=array("href"=>$sUrlLink,"innerhtml"=>tr_mdb_bln_entities);        
        $sUrlLink = $this->build_url($this->sModuleName,"incomes");
        $arLinks["incomes"]=array("href"=>$sUrlLink,"innerhtml"=>tr_mdb_bei_entities);
        $oScrumbs = new AppHelperBreadscrumbs($arLinks);
        return $oScrumbs;
    }

    //list_2
    protected function build_list_tabs()
    {
        $arTabs = array();
        $sUrlTab = $this->build_url();
        $arTabs["list"]=array("href"=>$sUrlTab,"innerhtml"=>tr_mdb_bln_listtabs_1);        
        $sUrlTab = $this->build_url($this->sModuleName,"incomes");
        $arTabs["incomes"]=array("href"=>$sUrlTab,"innerhtml"=>tr_mdb_bln_listtabs_2);
        $sUrlTab = $this->build_url($this->sModuleName,"outcomes");
        $arTabs["outcomes"]=array("href"=>$sUrlTab,"innerhtml"=>tr_mdb_bln_listtabs_3);
        $oTabs = new AppHelperHeadertabs($arTabs,"incomes");
        return $oTabs;
    }

    //list_3
    protected function build_listoperation_buttons()
    {
        $arOpButtons = array();
        $arOpButtons["filters"]=array("href"=>"javascript:reset_filters();","icon"=>"awe-magic","innerhtml"=>tr_mdb_bei_listopbutton_filters);
        $arOpButtons["reload"]=array("href"=>"javascript:TfwControl.form_submit();","icon"=>"awe-refresh","innerhtml"=>tr_mdb_bei_listopbutton_reload);
        if($this->oPermission->is_insert())
            $arOpButtons["insert"]=array("href"=>$this->build_url($this->sModuleName,"incomes","insert"),"icon"=>"awe-plus","innerhtml"=>tr_mdb_bei_listopbutton_insert);
        if($this->oPermission->is_quarantine())
            $arOpButtons["multiquarantine"]=array("href"=>"javascript:multi_quarantine();","icon"=>"awe-remove","innerhtml"=>tr_mdb_bei_listopbutton_multiquarantine);
        //if($this->oPermission->is_delete())
            //$arOpButtons["multidelete"]=array("href"=>"javascript:multi_delete();","icon"=>"awe-remove","innerhtml"=>tr_mdb_bei_listopbutton_multidelete);
        //PICK WINDOWS
        //$arOpButtons["multiassign"]=array("href"=>"javascript:multiassign_window('balanceincomes',null,'multiassign','balanceincomes','addexternaldata');","icon"=>"awe-external-link","innerhtml"=>tr_mdb_bei_listopbutton_multiassign);
        //$arOpButtons["singleassign"]=array("href"=>"javascript:single_pick('balanceincomes','singleassign','txtI','txtI');","icon"=>"awe-external-link","innerhtml"=>tr_mdb_bei_listopbutton_singleassign);
        $oOpButtons = new AppHelperButtontabs(tr_mdb_bei_entities);
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
        //id_closed_by
        $this->set_filter("id_closed_by","selIdClosedBy");
        //id_type
        $this->set_filter("id_type","selIdType");
        //subtotal_open
        $this->set_filter("subtotal_open","txtSubtotalOpen",array("operator"=>"like"));
        //subtotal_cash
        $this->set_filter("subtotal_cash","txtSubtotalCash",array("operator"=>"like"));
        //subtotal_creditcard
        $this->set_filter("subtotal_creditcard","txtSubtotalCreditcard",array("operator"=>"like"));
        //subtotal_check
        $this->set_filter("subtotal_check","txtSubtotalCheck",array("operator"=>"like"));
        //subtotal
        $this->set_filter("subtotal","txtSubtotal",array("operator"=>"like"));
        //subtotal_exclude
        $this->set_filter("subtotal_exclude","txtSubtotalExclude",array("operator"=>"like"));        
        //total_in
        $this->set_filter("total_in","txtTotalIn",array("operator"=>"like"));
        //notes
        $this->set_filter("notes","txaNotes",array("operator"=>"like"));
        //description
        //$this->set_filter("description","txtDescription",array("operator"=>"like"));
        //receipt_nr
        $this->set_filter("receipt_nr","txtReceiptNr",array("operator"=>"like"));
        //receipt_date
        $this->set_filter("receipt_date","datReceiptDate",array("operator"=>"like"));
        //operation_date
        $this->set_filter("operation_date","datOperationDate",array("operator"=>"like"));
        
        $this->set_filter("operation_date_start","datOperationDateStart",array("operator"=>">=","mapping"=>"operation_date"));
        $this->set_filter("operation_date_end","datOperationDateEnd",array("operator"=>"<=","mapping"=>"operation_date"));
        
    }//load_config_list_filters()

    //list_5
    protected function set_listfilters_from_post()
    {
        //id
        $this->set_filter_value("id",$this->get_post("txtId"));
        //code_erp
        //$this->set_filter_value("code_erp",$this->get_post("txtCodeErp"));
        //id_closed_by
        $this->set_filter_value("id_closed_by",$this->get_post("selIdClosedBy"));
        //id_type
        $this->set_filter_value("id_type",$this->get_post("selIdType"));
        //subtotal_open
        $this->set_filter_value("subtotal_open",$this->get_post("txtSubtotalOpen"));
        //subtotal_cash
        $this->set_filter_value("subtotal_cash",$this->get_post("txtSubtotalCash"));
        //subtotal_creditcard
        $this->set_filter_value("subtotal_creditcard",$this->get_post("txtSubtotalCreditcard"));
        //subtotal_check
        $this->set_filter_value("subtotal_check",$this->get_post("txtSubtotalCheck"));
        //subtotal
        $this->set_filter_value("subtotal",$this->get_post("txtSubtotal"));
        //subtotal_exclude
        $this->set_filter_value("subtotal_exclude",$this->get_post("txtSubtotalExclude"));        
        //total_in
        $this->set_filter_value("total_in",$this->get_post("txtTotalIn"));
        //notes
        $this->set_filter_value("notes",$this->get_post("txaNotes"));
        //description
        //$this->set_filter_value("description",$this->get_post("txtDescription"));
        //receipt_nr
        $this->set_filter_value("receipt_nr",$this->get_post("txtReceiptNr"));
        //receipt_date
        $this->set_filter_value("receipt_date",$this->get_post("datReceiptDate"));
        //operation_date
        $this->set_filter_value("operation_date",$this->get_post("datOperationDate"));
        
        $this->set_filter_value("operation_date_start",$this->get_post("datOperationDateStart"));
        $this->set_filter_value("operation_date_end",$this->get_post("datOperationDateEnd"));
    }//set_listfilters_from_post()

    //list_6
    protected function get_list_filters()
    {
        //CAMPOS
        $arFields = array();
        //id_type
        $oType = new ModelBalanceArray();
        $oType->use_language();
        $oType->set_type("income");
        $arOptions = $oType->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdType","selIdType");
        $oAuxField->set_value_to_select($this->get_post("selIdType"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdType",tr_mdb_bei_fil_id_type));
        $arFields[] = $oAuxWrapper;        

        //receipt_nr
        $oAuxField = new HelperInputText("txtReceiptNr","txtReceiptNr");
        $oAuxField->set_value($this->get_post("txtReceiptNr"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtReceiptNr",tr_mdb_bei_fil_receipt_nr));
        $arFields[] = $oAuxWrapper;
        
        //receipt_date
        $oAuxField = new HelperDate("datReceiptDate","datReceiptDate");
        $oAuxField->set_value($this->get_post("datReceiptDate"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("datReceiptDate",tr_mdb_bei_fil_receipt_date));
        $arFields[] = $oAuxWrapper;

        //operation_date
        // start - end
        $oAuxField = new HelperDate("datOperationDateStart","datOperationDateStart");
        $oAuxField->set_value($this->get_post("datOperationDateStart"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("datOperationDateStart",tr_mdb_bei_fil_operation_date_start));
        $arFields[] = $oAuxWrapper;

        $oAuxField = new HelperDate("datOperationDateEnd","datOperationDateEnd");
        $oAuxField->set_value($this->get_post("datOperationDateEnd"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("datOperationDateEnd",tr_mdb_bei_fil_operation_date_end));
        $arFields[] = $oAuxWrapper;        
        
        //notes
        $oAuxField = new HelperInputText("txaNotes","txaNotes");
        $oAuxField->set_value($this->get_post("txaNotes"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txaNotes",tr_mdb_bei_fil_notes));
        $arFields[] = $oAuxWrapper;
        
//        //id
//        $oAuxField = new HelperInputText("txtId","txtId");
//        $oAuxField->set_value($this->get_post("txtId"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtId",tr_mdb_bei_fil_id));
//        $arFields[] = $oAuxWrapper;
//        //code_erp
//        //$oAuxField = new HelperInputText("txtCodeErp","txtCodeErp");
//        //$oAuxField->set_value($this->get_post("txtCodeErp"));
//        //$oAuxField->on_entersubmit();
//        //$oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtCodeErp",tr_mdb_bei_fil_code_erp));
//        //$arFields[] = $oAuxWrapper;
//        //id_closed_by
//        $oClosedBy = new ModelUser();
//        $arOptions = $oClosedBy->get_picklist();
//        $oAuxField = new HelperSelect($arOptions,"selIdClosedBy","selIdClosedBy");
//        $oAuxField->set_value_to_select($this->get_post("selIdClosedBy"));
//        $oAuxField->set_postback();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdClosedBy",tr_mdb_bei_fil_id_closed_by));
//        $arFields[] = $oAuxWrapper;
//
//        //subtotal_open
//        $oAuxField = new HelperInputText("txtSubtotalOpen","txtSubtotalOpen");
//        $oAuxField->set_value($this->get_post("txtSubtotalOpen"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtSubtotalOpen",tr_mdb_bei_fil_subtotal_open));
//        $arFields[] = $oAuxWrapper;
//        //subtotal_cash
//        $oAuxField = new HelperInputText("txtSubtotalCash","txtSubtotalCash");
//        $oAuxField->set_value($this->get_post("txtSubtotalCash"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtSubtotalCash",tr_mdb_bei_fil_subtotal_cash));
//        $arFields[] = $oAuxWrapper;
//        //subtotal_creditcard
//        $oAuxField = new HelperInputText("txtSubtotalCreditcard","txtSubtotalCreditcard");
//        $oAuxField->set_value($this->get_post("txtSubtotalCreditcard"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtSubtotalCreditcard",tr_mdb_bei_fil_subtotal_creditcard));
//        $arFields[] = $oAuxWrapper;
//        //subtotal_check
//        $oAuxField = new HelperInputText("txtSubtotalCheck","txtSubtotalCheck");
//        $oAuxField->set_value($this->get_post("txtSubtotalCheck"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtSubtotalCheck",tr_mdb_bei_fil_subtotal_check));
//        $arFields[] = $oAuxWrapper;
//        //subtotal
//        $oAuxField = new HelperInputText("txtSubtotal","txtSubtotal");
//        $oAuxField->set_value($this->get_post("txtSubtotal"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtSubtotal",tr_mdb_bei_fil_subtotal));
//        $arFields[] = $oAuxWrapper;
//        //subtotal_exclude
//        $oAuxField = new HelperInputText("txtSubtotalExclude","txtSubtotalExclude");
//        $oAuxField->set_value($this->get_post("txtSubtotalExclude"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtSubtotalExclude",tr_mdb_bei_fil_subtotal_exclude));
//        $arFields[] = $oAuxWrapper;
//        //total_in
//        $oAuxField = new HelperInputText("txtTotalIn","txtTotalIn");
//        $oAuxField->set_value($this->get_post("txtTotalIn"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtTotalIn",tr_mdb_bei_fil_total));
//        $arFields[] = $oAuxWrapper;

        //description
        //$oAuxField = new HelperInputText("txtDescription","txtDescription");
        //$oAuxField->set_value($this->get_post("txtDescription"));
        //$oAuxField->on_entersubmit();
        //$oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtDescription",tr_mdb_bei_fil_description));
        //$arFields[] = $oAuxWrapper;
        return $arFields;
    }//get_list_filters()

    //list_7
    protected function get_list_columns()
    {
        $arColumns["id"] = tr_mdb_bei_col_id;
        //$arColumns["code_erp"] = tr_mdb_bei_col_code_erp;
        //$arColumns["id_closed_by"] = tr_mdb_bei_col_id_closed_by;
        $arColumns["operation_date"] = tr_mdb_bei_col_operation_date;
        //$arColumns["id_type"] = tr_mdb_bei_col_id_type;
        $arColumns["type"] = tr_mdb_bei_col_id_type;
        $arColumns["subtotal_open"] = tr_mdb_bei_col_subtotal_open;
        $arColumns["subtotal_cash"] = tr_mdb_bei_col_subtotal_cash;
        $arColumns["subtotal_creditcard"] = tr_mdb_bei_col_subtotal_creditcard;
        //$arColumns["subtotal_check"] = tr_mdb_bei_col_subtotal_check;
        //$arColumns["subtotal"] = tr_mdb_bei_col_subtotal;
        //$arColumns["subtotal_exclude"] = tr_mdb_bei_col_subtotal_exclude;
        $arColumns["total_in"] = tr_mdb_bei_col_total_in;
        //$arColumns["notes"] = tr_mdb_bei_col_notes;
        //$arColumns["description"] = tr_mdb_bei_col_description;
        //$arColumns["receipt_nr"] = tr_mdb_bei_col_receipt_nr;
        //$arColumns["receipt_date"] = tr_mdb_bei_col_receipt_date;
        
        //$arColumns["closedby"] = tr_mdb_bei_col_id_closed_by;
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
        $this->oBalanceIncome->set_orderby($this->get_orderby());
        $this->oBalanceIncome->set_ordertype($this->get_ordertype());
        $arFormats = array("operation_date_start"=>"date","operation_date_end"=>"date","operation_date"=>"date","receipt_date"=>"date");
        $this->oBalanceIncome->set_filters($this->get_filter_searchconfig($arFormats));
        //hierarchy recover
        //$this->oBalanceIncome->set_select_user($this->oSessionUser->get_id());
        $arList = $this->oBalanceIncome->get_select_all_ids();
        $iRequestPage = $this->get_post("selPage");
        $oPage = new ComponentPage($arList,$iRequestPage);
        $arList = $oPage->get_items_to_show();
        $arList = $this->oBalanceIncome->get_select_all_by_ids($arList);
        //TABLE
        //This method adds objects controls to search list form
        $oTableList = new HelperTableTyped($arList,$arColumns);
        $oTableList->set_fields($arObjFilter);
        //$oTableList->set_module($this->get_current_module());
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
        //("href"=>"url_lines","innerhtml"=>tr_mdb_bei_order_lines,"class"=>"btn btn-info","icon"=>"awe-info-sign")));
        $arFormat = array("subtotal_open"=>"numeric2","subtotal_cash"=>"numeric2","subtotal_creditcard"=>"numeric2","subtotal_check"=>"numeric2"
            ,"subtotal"=>"numeric2","subtotal_exclude"=>"numeric2","total_in"=>"numeric2","receipt_date"=>"date","operation_date"=>"date");
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
        $arLinks["list"]=array("href"=>$sUrlLink,"innerhtml"=>tr_mdb_bln_entities);        
        $sUrlLink = $this->build_url($this->sModuleName,"incomes");
        $arLinks["incomes"]=array("href"=>$sUrlLink,"innerhtml"=>tr_mdb_bei_entities);
        $sUrlLink = $this->build_url($this->sModuleName,"incomes","insert");
        $arLinks["insert"]=array("href"=>$sUrlLink,"innerhtml"=>tr_mdb_bei_entity_insert);
        $oScrumbs = new AppHelperBreadscrumbs($arLinks);
        return $oScrumbs;
    }//build_insert_scrumbs()

    //insert_2
    protected function build_insert_tabs()
    {
        $arTabs = array();
        //$sUrlTab = $this->build_url($this->sModuleName,"incomes","insert")
        //$arTabs["insert1"]=array("href"=>$sUrlTab,"innerhtml"=>tr_mdb_bei_instabs_1);
        //$sUrlTab = $this->build_url($this->sModuleName,"incomes","insert2")
        //$arTabs["insert2"]=array("href"=>$sUrlTab,"innerhtml"=>tr_mdb_bei_instabs_2);
        $oTabs = new AppHelperHeadertabs($arTabs,"insert1");
        return $oTabs;
    }//build_insert_tabs()
    //insert_3
    protected function build_insert_opbuttons()
    {
        $arOpButtons = array();
        $arOpButtons["list"] = array("href"=>$this->build_url($this->sModuleName,"incomes"),"icon"=>"awe-search","innerhtml"=>tr_mdb_bei_insopbutton_list);
        //$arOpButtons["extra"] = array("href"=>$this->build_url("balanceincomes"),"icon"=>"awe-xxxx","innerhtml"=>tr_mdb_bei_insopbutton_extra1);
        $oOpButtons = new AppHelperButtontabs(tr_mdb_bei_entities);
        $oOpButtons->set_tabs($arOpButtons);
        return $oOpButtons;
    }//build_insert_opbuttons()

    //insert_4
    protected function build_insert_fields($usePost=0)
    {
        $arFields = array(); $oAuxField = NULL; $oAuxLabel = NULL;
        $arFields[]= new AppHelperFormhead(tr_mdb_bei_entity_new);
        //id_closed_by
        $oClosedBy = new ModelUser();
        $arOptions = $oClosedBy->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdClosedBy","selIdClosedBy");
        $oAuxField->set_value_to_select($this->get_post("selIdClosedBy"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdClosedBy",tr_mdb_bei_ins_id_closed_by));
        $oAuxField->set_value_to_select($this->oSessionUser->get_id());
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdClosedBy"));
        $oAuxLabel = new HelperLabel("selIdClosedBy",tr_mdb_bei_ins_id_closed_by,"lblIdClosedBy");
        $oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //id_type
        $oType = new ModelBalanceArray();
        $oType->use_language();
        $oType->set_type("income");
        $arOptions = $oType->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdType","selIdType");
        $oAuxField->set_value_to_select("1");
        $oAuxField->set_postback();
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdType"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdType",tr_mdb_bei_ins_id_type));        
        $oAuxLabel = new HelperLabel("selIdType",tr_mdb_bei_ins_id_type,"lblIdType");
        $oAuxLabel->add_class("labelreq");        
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //operation_date
        $oAuxField = new HelperDate("datOperationDate","datOperationDate");
        $oAuxField->set_value($this->arToday["user"]);
        if($usePost) $oAuxField->set_value($this->get_post("datOperationDate"));
        $oAuxLabel = new HelperLabel("datOperationDate",tr_mdb_bei_ins_operation_date,"lblOperationDate");
        $oAuxLabel->add_class("labelreq");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //pending payment o in debt o other. Si se paga una deuda pendiente o se deja a deber
        if($this->get_post("selIdType")!="1" && $this->is_post())
        {    
            //receipt_nr
            $oAuxField = new HelperInputText("txtReceiptNr","txtReceiptNr");
            if($usePost) $oAuxField->set_value($this->get_post("txtReceiptNr"));
            $oAuxLabel = new HelperLabel("txtReceiptNr",tr_mdb_bei_ins_receipt_nr,"lblReceiptNr");
            //other
            if($this->get_post("selIdType")!="4")
                $oAuxLabel->add_class("labelreq");
            //$oAuxField->readonly();$oAuxField->add_class("readonly");
            $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

            //receipt_date
            $oAuxField = new HelperDate("datReceiptDate","datReceiptDate");
            if($usePost) $oAuxField->set_value($this->get_post("datReceiptDate"));
            $oAuxLabel = new HelperLabel("datReceiptDate",tr_mdb_bei_ins_receipt_date,"lblReceiptDate");
            //other
            if($this->get_post("selIdType")!="4")
                $oAuxLabel->add_class("labelreq");
            //$oAuxField->readonly();$oAuxField->add_class("readonly");
            $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        }
        
        if($this->get_post("selIdType")=="1" || !$this->is_post())
        {
            //subtotal_open
            $oAuxField = new HelperInputText("txtSubtotalOpen","txtSubtotalOpen");
            $oAuxField->set_value("0.00");
            if($usePost) $oAuxField->set_value(dbbo_numeric2($this->get_post("txtSubtotalOpen")));
            $oAuxLabel = new HelperLabel("txtSubtotalOpen",tr_mdb_bei_ins_subtotal_open,"lblSubtotalOpen");
            //other
            if($this->get_post("selIdType")!="4")
                $oAuxLabel->add_class("labelreq");
            //$oAuxField->readonly();$oAuxField->add_class("readonly");
            $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        }
        
        //subtotal_cash
        $oAuxField = new HelperInputText("txtSubtotalCash","txtSubtotalCash");
        $oAuxField->add_extras("onclick","this.select();");
        $oAuxField->add_extras("onkeyup","update_subtotal();");
        $oAuxField->set_value("0.00");
        if($usePost) $oAuxField->set_value(dbbo_numeric2($this->get_post("txtSubtotalCash")));
        $oAuxLabel = new HelperLabel("txtSubtotalCash",tr_mdb_bei_ins_subtotal_cash,"lblSubtotalCash");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //subtotal_creditcard
        $oAuxField = new HelperInputText("txtSubtotalCreditcard","txtSubtotalCreditcard");
        $oAuxField->add_extras("onclick","this.select();");
        $oAuxField->add_extras("onkeyup","update_subtotal();");
        $oAuxField->set_value("0.00");
        if($usePost) $oAuxField->set_value(dbbo_numeric2($this->get_post("txtSubtotalCreditcard")));
        $oAuxLabel = new HelperLabel("txtSubtotalCreditcard",tr_mdb_bei_ins_subtotal_creditcard,"lblSubtotalCreditcard");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //subtotal_check
        $oAuxField = new HelperInputText("txtSubtotalCheck","txtSubtotalCheck");
        $oAuxField->add_extras("onclick","this.select();");
        $oAuxField->add_extras("onkeyup","update_subtotal();");
        $oAuxField->set_value("0.00");
        if($usePost) $oAuxField->set_value(dbbo_numeric2($this->get_post("txtSubtotalCheck")));
        $oAuxLabel = new HelperLabel("txtSubtotalCheck",tr_mdb_bei_ins_subtotal_check,"lblSubtotalCheck");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
//        if($this->get_post("selIdType")!="3" && $this->get_post("selIdType")!="2")
//        {        
//            //subtotal
//            $oAuxField = new HelperInputText("txtSubtotal","txtSubtotal");
//            $oAuxField->set_value("0.00");
//            if($usePost) $oAuxField->set_value(dbbo_numeric2($this->get_post("txtSubtotal")));
//            $oAuxLabel = new HelperLabel("txtSubtotal",tr_mdb_bei_ins_subtotal,"lblSubtotal");
//            $oAuxField->readonly();$oAuxField->add_class("readonly");
//            $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        } 

        //subtotal
        $oAuxField = new HelperInputText("txtSubtotal","txtSubtotal");
        $oAuxField->set_value("0.00");
        if($usePost) $oAuxField->set_value(dbbo_numeric2($this->get_post("txtSubtotal")));
        $oAuxLabel = new HelperLabel("txtSubtotal",tr_mdb_bei_ins_subtotal,"lblSubtotal");
        $oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);        
        
        //subtotal_exclude
        $oAuxField = new HelperInputText("txtSubtotalExclude","txtSubtotalExclude");
        $oAuxField->set_value("0.00");
        if($usePost) $oAuxField->set_value(dbbo_numeric2($this->get_post("txtSubtotalExclude")));
        $oAuxLabel = new HelperLabel("txtSubtotalExclude",tr_mdb_bei_ins_subtotal_exclude,"lblSubtotalExclude");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//                
        //total_in
//        $oAuxField = new HelperInputText("txtTotalIn","txtTotalIn");
//        $oAuxField->set_value("0.00");
//        if($usePost) $oAuxField->set_value(dbbo_numeric2($this->get_post("txtTotalIn")));
//        $oAuxLabel = new HelperLabel("txtTotalIn",tr_mdb_bei_ins_total_in,"lblTotal");
//        $oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //notes
        $oAuxField = new HelperTextarea("txaNotes","txaNotes");
        if($usePost) $oAuxField->set_innerhtml($this->get_post("txaNotes"));
        $oAuxLabel = new HelperLabel("txaNotes",tr_mdb_bei_ins_notes,"lblNotes");
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
        //$oAuxLabel = new HelperLabel("txtCodeErp",tr_mdb_bei_ins_code_erp,"lblCodeErp");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        //$arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
                
        //description
        //$oAuxField = new HelperInputText("txtDescription","txtDescription");
        //if($usePost) $oAuxField->set_value($this->get_post("txtDescription"));
        //$oAuxLabel = new HelperLabel("txtDescription",tr_mdb_bei_ins_description,"lblDescription");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        //$arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //SAVE BUTTON
        $oAuxField = new HelperButtonBasic("butSave",tr_mdb_bei_ins_savebutton);
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
        //$arFieldsConfig["id"] = array("controlid"=>"txtId","label"=>tr_mdb_bei_ins_id,"length"=>9,"type"=>array("numeric","required"));
        //$arFieldsConfig["code_erp"] = array("controlid"=>"txtCodeErp","label"=>tr_mdb_bei_ins_code_erp,"length"=>25,"type"=>array());
        //$arFieldsConfig["id_closed_by"] = array("controlid"=>"selIdClosedBy","label"=>tr_mdb_bei_ins_id_closed_by,"length"=>4,"type"=>array());
        //$arFieldsConfig["id_type"] = array("controlid"=>"selIdType","label"=>tr_mdb_bei_ins_id_type,"length"=>4,"type"=>array());
        $arFieldsConfig["subtotal_open"] = array("controlid"=>"txtSubtotalOpen","label"=>tr_mdb_bei_ins_subtotal_open,"length"=>9,"type"=>array("required","numeric"));
        $arFieldsConfig["subtotal_cash"] = array("controlid"=>"txtSubtotalCash","label"=>tr_mdb_bei_ins_subtotal_cash,"length"=>9,"type"=>array("numeric"));
        $arFieldsConfig["subtotal_creditcard"] = array("controlid"=>"txtSubtotalCreditcard","label"=>tr_mdb_bei_ins_subtotal_creditcard,"length"=>9,"type"=>array("numeric"));
        $arFieldsConfig["subtotal_check"] = array("controlid"=>"txtSubtotalCheck","label"=>tr_mdb_bei_ins_subtotal_check,"length"=>9,"type"=>array("numeric"));
        $arFieldsConfig["subtotal"] = array("controlid"=>"txtSubtotal","label"=>tr_mdb_bei_ins_subtotal,"length"=>9,"type"=>array("numeric"));
        $arFieldsConfig["subtotal_exclude"] = array("controlid"=>"txtSubtotalExclude","label"=>tr_mdb_bei_ins_subtotal_exclude,"length"=>9,"type"=>array("numeric"));
        $arFieldsConfig["total_in"] = array("controlid"=>"txtTotalIn","label"=>tr_mdb_bei_ins_total_in,"length"=>9,"type"=>array("numeric"));
        $arFieldsConfig["notes"] = array("controlid"=>"txaNotes","label"=>tr_mdb_bei_ins_notes,"length"=>500,"type"=>array());
        //$arFieldsConfig["description"] = array("controlid"=>"txtDescription","label"=>tr_mdb_bei_ins_description,"length"=>200,"type"=>array());
        $arFieldsConfig["receipt_nr"] = array("controlid"=>"txtReceiptNr","label"=>tr_mdb_bei_ins_receipt_nr,"length"=>25,"type"=>array("required"));
        $arFieldsConfig["receipt_date"] = array("controlid"=>"datReceiptDate","label"=>tr_mdb_bei_ins_receipt_date,"length"=>10,"type"=>array("required"));
        
        if($this->get_post("selIdType")=="4")//other
        {
            $arFieldsConfig["subtotal_open"]["type"]=array();
            $arFieldsConfig["receipt_nr"]["type"]=array();
            $arFieldsConfig["receipt_date"]["type"]=array();
        }
        $arFieldsConfig["operation_date"] = array("controlid"=>"datOperationDate","label"=>tr_mdb_bei_ins_operation_date,"length"=>10,"type"=>array("required"));
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
            $arFieldsValues = $this->get_fields_from_post(array("receipt_date"=>"date","operation_date"=>"date"));
            //bug($arFieldsValues,"arFieldsValues");die;
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
                //$this->oBalanceIncome->log_save_insert();
                $arFieldsValues = array_merge($arFieldsValues,$this->oComponentBalances->get_total());
                //bug($arFieldsValues);die;
                $this->oBalanceIncome->set_attrib_value($arFieldsValues);
                $this->oBalanceIncome->set_insert_user($this->oSessionUser->get_id());

                //$this->oBalanceIncome->set_platform($this->oSessionUser->get_platform());
                $this->oBalanceIncome->autoinsert();
                if($this->oBalanceIncome->is_error())
                {
                    $oAlert->set_type("e");
                    $oAlert->set_title(tr_data_not_saved);
                    $oAlert->set_content(tr_mdb_error_trying_to_save);
                }
                else//insert ok
                {
                    $this->set_get("id",$this->oBalanceIncome->get_last_insert_id());
                    $oAlert->set_title(tr_data_saved);
                    $this->reset_post();
                    //$this->go_to_after_succes_cud();
                }
            }//no error
            
        }//fin if is_inserting (post action=save)
        
        //Si hay errores se recupera desde post
        if($arErrData || $this->is_postback()) $oForm = $this->build_insert_form(1);
        else $oForm = $this->build_insert_form();
        
        //ANCHOR DOWN
        //$oAnchorDown = new HelperAnchor();
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
        //$this->oView->add_var($oAnchorDown,"oAnchorDown");
        $this->oView->add_js("js_balances");
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
        $sUrlLink = $this->build_url();
        $arLinks["list"]=array("href"=>$sUrlLink,"innerhtml"=>tr_mdb_bln_entities);        
        $sUrlLink = $this->build_url($this->sModuleName,"incomes");
        $arLinks["incomes"]=array("href"=>$sUrlLink,"innerhtml"=>tr_mdb_bei_entities);
        $sUrlLink = $this->build_url($this->sModuleName,"incomes","update","id=".$this->get_get("id"));
        $arLinks["detail"]=array("href"=>$sUrlLink,"innerhtml"=>tr_mdb_bei_entity.": ".$this->oBalanceIncome->get_id()." - ".$this->oBalanceIncome->get_description());
        $oScrumbs = new AppHelperBreadscrumbs($arLinks);
        return $oScrumbs;
    }//build_update_scrumbs()

    //update_2
    protected function build_update_tabs()
    {
        $arTabs = array();
        $sUrlTab = $this->build_url($this->sModuleName,"incomes","update","id=".$this->get_get("id"));
        $arTabs["detail"]=array("href"=>$sUrlTab,"innerhtml"=>tr_mdb_bei_updtabs_detail);
        //$sUrlTab = $this->build_url($this->sModuleName,"incomes","foreignmodule","get_list_by_foreign","id_foreign=".$this->get_get("id_parent_foreign"));
        //$arTabs["foreigndata"]=array("href"=>$sUrlTab,"innerhtml"=>tr_mdb_bei_updtabs_foreigndata);
        $oTabs = new AppHelperHeadertabs($arTabs,"detail");
        return $oTabs;
    }//build_update_tabs()

    //update_3
    protected function build_update_opbuttons()
    {
        $arOpButtons = array();
        if($this->oPermission->is_select())
            $arOpButtons["list"]=array("href"=>$this->build_url($this->sModuleName,"incomes"),"icon"=>"awe-search","innerhtml"=>tr_mdb_bei_updopbutton_list);
        //if($this->oPermission->is_insert())
            //$arOpButtons["insert"]=array("href"=>$this->build_url($this->sModuleName,"incomes","insert"),"icon"=>"awe-plus","innerhtml"=>tr_mdb_bei_updopbutton_insert);
        if($this->oPermission->is_quarantine())
            $arOpButtons["delete"]=array("href"=>$this->build_url($this->sModuleName,"incomes","quarantine","id=".$this->get_get("id")),"icon"=>"awe-remove","innerhtml"=>tr_mdb_bei_updopbutton_quarantine);
        //if($this->oPermission->is_delete())
            //$arOpButtons["delete"]=array("href"=>$this->build_url($this->sModuleName,"incomes","delete","id=".$this->get_get("id")),"icon"=>"awe-remove","innerhtml"=>tr_mdb_bei_updopbutton_delete);
        $oOpButtons = new AppHelperButtontabs(tr_mdb_bei_entities);
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
        $oAuxField->set_value($this->oBalanceIncome->get_id());
        $oAuxLabel = new HelperLabel("txtId",tr_mdb_bei_upd_id,"lblId");
        $oAuxLabel->add_class("labelpk");
        $oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //id_closed_by
        $oClosedBy = new ModelUser();
        $arOptions = $oClosedBy->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdClosedBy","selIdClosedBy");
        $oAuxField->set_value_to_select($this->oBalanceIncome->get_id_closed_by());
        //if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdClosedBy"));
        $oAuxLabel = new HelperLabel("selIdClosedBy",tr_mdb_bei_upd_id_closed_by,"lblIdClosedBy");
        //$oAuxLabel->add_class("labelreq");
        $oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //id_type
        $oType = new ModelBalanceArray();
        $oType->use_language();
        $oType->set_type("income");
        $arOptions = $oType->get_picklist();
        if($this->oBalanceIncome->get_id_type()=="2")
        {
            unset($arOptions["4"]);unset($arOptions["1"]);
        }
        $oAuxField = new HelperSelect($arOptions,"selIdType","selIdType");
        $oAuxField->set_postback();
        $oAuxField->set_value_to_select($this->oBalanceIncome->get_id_type());
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdType"));
        //si no es in debt (a deber)
        if($this->oBalanceIncome->get_id_type()!="2")
        {
            $oAuxField->readonly();$oAuxField->add_class("readonly");
        }        
        $oAuxLabel = new HelperLabel("selIdType",tr_mdb_bei_upd_id_type,"lblIdType");
        $oAuxLabel->add_class("labelreq");        
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        $arFields[] = $oAuxWrapper;

        //operation_date
        $oAuxField = new HelperDate("datOperationDate","datOperationDate");
        $oAuxField->set_value(dbbo_date($this->oBalanceIncome->get_operation_date(),"/"));
        if($usePost) $oAuxField->set_value($this->get_post("datOperationDate"));
        $oAuxLabel = new HelperLabel("datOperationDate",tr_mdb_bei_upd_operation_date,"lblOperationDate");
        $oAuxLabel->add_class("labelreq");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //pending payment o in debt o other. Si se paga una deuda pendiente o se deja a deber
        if($this->oBalanceIncome->get_id_type()!="1")
        {
            //receipt_nr
            $oAuxField = new HelperInputText("txtReceiptNr","txtReceiptNr");
            $oAuxField->set_value($this->oBalanceIncome->get_receipt_nr());
            if($usePost) $oAuxField->set_value($this->get_post("txtReceiptNr"));
            $oAuxLabel = new HelperLabel("txtReceiptNr",tr_mdb_bei_upd_receipt_nr,"lblReceiptNr");
            //$oAuxLabel->add_class("labelreq");            
            //$oAuxField->readonly();$oAuxField->add_class("readonly");
            $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

            //receipt_date
            $oAuxField = new HelperDate("datReceiptDate","datReceiptDate");
            $oAuxField->set_value(dbbo_date($this->oBalanceIncome->get_receipt_date(),"/"));
            if($usePost) $oAuxField->set_value($this->get_post("datReceiptDate"));
            $oAuxLabel = new HelperLabel("datReceiptDate",tr_mdb_bei_upd_receipt_date,"lblReceiptDate");
            //$oAuxLabel->add_class("labelreq");
            //$oAuxField->readonly();$oAuxField->add_class("readonly");
            $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        }
        
        //TPV
        if($this->oBalanceIncome->get_id_type()=="1")
        {
            //subtotal_open
            $oAuxField = new HelperInputText("txtSubtotalOpen","txtSubtotalOpen");
            $oAuxField->set_value(dbbo_numeric2($this->oBalanceIncome->get_subtotal_open()));
            $oAuxField->add_extras("onclick","this.select();");
            if($usePost) $oAuxField->set_value(dbbo_numeric2($this->get_post("txtSubtotalOpen")));
            $oAuxLabel = new HelperLabel("txtSubtotalOpen",tr_mdb_bei_upd_subtotal_open,"lblSubtotalOpen");
            $oAuxLabel->add_class("labelreq");
            //$oAuxField->readonly();$oAuxField->add_class("readonly");
            $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        }
        
        //subtotal_cash
        $oAuxField = new HelperInputText("txtSubtotalCash","txtSubtotalCash");
        $oAuxField->set_value(dbbo_numeric2($this->oBalanceIncome->get_subtotal_cash()));
        $oAuxField->add_extras("onclick","this.select();");
        if($usePost) $oAuxField->set_value(dbbo_numeric2($this->get_post("txtSubtotalCash")));
        $oAuxLabel = new HelperLabel("txtSubtotalCash",tr_mdb_bei_upd_subtotal_cash,"lblSubtotalCash");
        //$oAuxLabel->add_class("labelreq");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //subtotal_creditcard
        $oAuxField = new HelperInputText("txtSubtotalCreditcard","txtSubtotalCreditcard");
        $oAuxField->set_value(dbbo_numeric2($this->oBalanceIncome->get_subtotal_creditcard()));
        $oAuxField->add_extras("onclick","this.select();");
        if($usePost) $oAuxField->set_value(dbbo_numeric2($this->get_post("txtSubtotalCreditcard")));
        $oAuxLabel = new HelperLabel("txtSubtotalCreditcard",tr_mdb_bei_upd_subtotal_creditcard,"lblSubtotalCreditcard");
        //$oAuxLabel->add_class("labelreq");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //subtotal_check
        $oAuxField = new HelperInputText("txtSubtotalCheck","txtSubtotalCheck");
        $oAuxField->set_value(dbbo_numeric2($this->oBalanceIncome->get_subtotal_check()));
        $oAuxField->add_extras("onclick","this.select();");
        if($usePost) $oAuxField->set_value(dbbo_numeric2($this->get_post("txtSubtotalCheck")));
        $oAuxLabel = new HelperLabel("txtSubtotalCheck",tr_mdb_bei_upd_subtotal_check,"lblSubtotalCheck");
        //$oAuxLabel->add_class("labelreq");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //subtotal
        $oAuxField = new HelperInputText("txtSubtotal","txtSubtotal");
        $oAuxField->set_value(dbbo_numeric2($this->oBalanceIncome->get_subtotal()));
        if($usePost) $oAuxField->set_value(dbbo_numeric2($this->get_post("txtSubtotal")));
        $oAuxLabel = new HelperLabel("txtSubtotal",tr_mdb_bei_upd_subtotal,"lblSubtotal");
        //$oAuxLabel->add_class("labelreq");
        $oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        $oAuxField = new HelperInputText("txtSubtotalExclude","txtSubtotalExclude");
        $oAuxField->set_value(dbbo_numeric2($this->oBalanceIncome->get_subtotal_exclude()));
        if($usePost) $oAuxField->set_value(dbbo_numeric2($this->get_post("txtSubtotalExclude")));
        $oAuxLabel = new HelperLabel("txtSubtotalExclude",tr_mdb_bei_upd_subtotal_exclude,"lblSubtotalExclude");
        //$oAuxLabel->add_class("labelreq");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //total_in
        $oAuxField = new HelperInputText("txtTotalIn","txtTotalIn");
        $oAuxField->set_value(dbbo_numeric2($this->oBalanceIncome->get_total_in()));
        //$oAuxField->add_extras("onclick","this.select();");
        if($usePost) $oAuxField->set_value(dbbo_numeric2($this->get_post("txtTotalIn")));
        $oAuxLabel = new HelperLabel("txtTotalIn",tr_mdb_bei_upd_total_in,"lblTotal");
        //$oAuxLabel->add_class("labelreq");
        $oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //notes
        $oAuxField = new HelperTextarea("txaNotes","txaNotes");
        $oAuxField->set_innerhtml($this->oBalanceIncome->get_notes());
        if($usePost) $oAuxField->set_innerhtml($this->get_post("txaNotes"));
        $oAuxLabel = new HelperLabel("txaNotes",tr_mdb_bei_upd_notes,"lblNotes");
        //$oAuxLabel->add_class("labelreq");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);


        //description
        //$oAuxField = new HelperInputText("txtDescription","txtDescription");
        //$oAuxField->set_value($this->oBalanceIncome->get_description());
        //if($usePost) $oAuxField->set_value($this->get_post("txtDescription"));
        //$oAuxLabel = new HelperLabel("txtDescription",tr_mdb_bei_upd_description,"lblDescription");
        //$oAuxLabel->add_class("labelreq");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        //$arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
                
        //code_erp
        //$oAuxField = new HelperInputText("txtCodeErp","txtCodeErp");
        //$oAuxField->set_value($this->oBalanceIncome->get_code_erp());
        //if($usePost) $oAuxField->set_value($this->get_post("txtCodeErp"));
        //$oAuxLabel = new HelperLabel("txtCodeErp",tr_mdb_bei_upd_code_erp,"lblCodeErp");
        //$oAuxLabel->add_class("labelreq");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        //$arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
                
        //BUTTON SAVE
        $oAuxField = new HelperButtonBasic("butSave",tr_mdb_bei_upd_savebutton);
        $oAuxField->add_class("btn btn-primary");
        $oAuxField->set_js_onclick("update();");
        if($this->oPermission->is_update())
            $arFields[] = new ApphelperFormactions(array($oAuxField));
        //AUDIT INFO
        $sRegInfo = $this->get_audit_info($this->oBalanceIncome->get_insert_user(),$this->oBalanceIncome->get_insert_date()
        ,$this->oBalanceIncome->get_update_user(),$this->oBalanceIncome->get_update_date());
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
        $arFieldsConfig["id"] = array("controlid"=>"txtId","label"=>tr_mdb_bei_upd_id,"length"=>9,"type"=>array("numeric","required"));
        //$arFieldsConfig["code_erp"] = array("controlid"=>"txtCodeErp","label"=>tr_mdb_bei_upd_code_erp,"length"=>25,"type"=>array());
        //$arFieldsConfig["id_closed_by"] = array("controlid"=>"selIdClosedBy","label"=>tr_mdb_bei_upd_id_closed_by,"length"=>4,"type"=>array());
        //$arFieldsConfig["id_type"] = array("controlid"=>"selIdType","label"=>tr_mdb_bei_upd_id_type,"length"=>4,"type"=>array());
        $arFieldsConfig["subtotal_open"] = array("controlid"=>"txtSubtotalOpen","label"=>tr_mdb_bei_upd_subtotal_open,"length"=>9,"type"=>array("numeric"));
        $arFieldsConfig["subtotal_cash"] = array("controlid"=>"txtSubtotalCash","label"=>tr_mdb_bei_upd_subtotal_cash,"length"=>9,"type"=>array("numeric"));
        $arFieldsConfig["subtotal_creditcard"] = array("controlid"=>"txtSubtotalCreditcard","label"=>tr_mdb_bei_upd_subtotal_creditcard,"length"=>9,"type"=>array("numeric"));
        $arFieldsConfig["subtotal_check"] = array("controlid"=>"txtSubtotalCheck","label"=>tr_mdb_bei_upd_subtotal_check,"length"=>9,"type"=>array("numeric"));
        $arFieldsConfig["subtotal"] = array("controlid"=>"txtSubtotal","label"=>tr_mdb_bei_upd_subtotal,"length"=>9,"type"=>array("numeric"));
        $arFieldsConfig["subtotal_exclude"] = array("controlid"=>"txtSubtotalExclude","label"=>tr_mdb_bei_upd_subtotal_exclude,"length"=>9,"type"=>array("numeric"));        
        $arFieldsConfig["total_in"] = array("controlid"=>"txtTotalIn","label"=>tr_mdb_bei_upd_total_in,"length"=>9,"type"=>array("numeric"));
        $arFieldsConfig["notes"] = array("controlid"=>"txaNotes","label"=>tr_mdb_bei_upd_notes,"length"=>500,"type"=>array());
        //$arFieldsConfig["description"] = array("controlid"=>"txtDescription","label"=>tr_mdb_bei_upd_description,"length"=>200,"type"=>array());
        $arFieldsConfig["receipt_nr"] = array("controlid"=>"txtReceiptNr","label"=>tr_mdb_bei_upd_receipt_nr,"length"=>25,"type"=>array());
        $arFieldsConfig["receipt_date"] = array("controlid"=>"datReceiptDate","label"=>tr_mdb_bei_upd_receipt_date,"length"=>10,"type"=>array());
        $arFieldsConfig["operation_date"] = array("controlid"=>"datOperationDate","label"=>tr_mdb_bei_upd_operation_date,"length"=>10,"type"=>array());
        return $arFieldsConfig;
    }//get_update_validate

    //update_6
    protected function build_update_form($usePost=0)
    {
        $oForm = new HelperForm("frmUpdate");
        $oForm->add_class("form-horizontal");
        $oForm->add_style("margin-bottom:0");
        if($this->oPermission->is_read()&&$this->oPermission->is_not_update())
            $oForm->readonly();
        $arFields = $this->build_update_fields($usePost);
        $oForm->add_controls($arFields);
        return $oForm;
    }//build_update_form()

    //update_7
    public function update()
    {
        //$this->go_to_401(($this->oPermission->is_not_read() && $this->oPermission->is_not_update())||$this->oSessionUser->is_not_dataowner());
        $this->go_to_401($this->oPermission->is_not_read() && $this->oPermission->is_not_update());
        $this->go_to_404(!$this->oBalanceIncome->is_in_table());
        //Validacion con PHP y JS
        $arFieldsConfig = $this->get_update_validate();
        if($this->is_updating())
        {
            $oAlert = new AppHelperAlertdiv();
            $oAlert->use_close_button();
            $arFieldsValues = $this->get_fields_from_post(array("receipt_date"=>"date","operation_date"=>"date"));
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
                $arFieldsValues = array_merge($arFieldsValues,$this->oComponentBalances->get_total());
                //bug($arFieldsValues);die;                
                $this->oBalanceIncome->set_attrib_value($arFieldsValues);
                //$this->oBalanceIncome->set_description($oBalanceIncome->get_field1()." ".$oBalanceIncome->get_field2());
                
                $this->oBalanceIncome->set_update_user($this->oSessionUser->get_id());
                $this->oBalanceIncome->autoupdate();
                if($this->oBalanceIncome->is_error())
                {
                    $oAlert->set_type("e");
                    $oAlert->set_title(tr_data_not_saved);
                    $oAlert->set_content(tr_mdb_error_trying_to_save);
                }//no error
                else//update ok
                {
                    //$this->oBalanceIncome->load_by_id();
                    $oAlert->set_title(tr_data_saved);
                    $this->reset_post();
                    //$this->go_to_after_succes_cud();
                }//error save
            }//error validation
        }//is_updating()
        if($arErrData || $this->is_postback()) 
            $oForm = $this->build_update_form(1);
        else 
            $oForm = $this->build_update_form(); 
        //ANCHOR DOWN
        //$oAnchorDown = new HelperAnchor();
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
            $this->oBalanceIncome->set_id($id);
            $this->oBalanceIncome->autodelete();
                if($this->oBalanceIncome->is_error())
            {
                    $this->isError = TRUE;
                    $this->set_session_message(tr_mdb_error_trying_to_delete);
            }
                else
            {
                    $this->set_session_message(tr_data_deleted);
            }
        }//si existe el id
        else
            $this->set_session_message(tr_mdb_error_key_not_supplied,"e");
    }//single_delete()

    //delete_2
    protected function multi_delete()
    {
        //Intenta recuperar pkeys sino pasa a recuperar el id. En ultimo caso lo que se ha pasado por parametro
        $arKeys = $this->get_listkeys();
        foreach($arKeys as $sKey)
        {
            $id = $sKey;
            $this->oBalanceIncome->set_id($id);
            $this->oBalanceIncome->autodelete();
                if($this->oBalanceIncome->is_error())
            {
                    $this->isError = true;
                    $this->set_session_message(tr_mdb_error_trying_to_delete,"e");
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
            $this->oBalanceIncome->set_id($id);
            $this->oBalanceIncome->autoquarantine();
                if($this->oBalanceIncome->is_error())
                    $this->set_session_message(tr_mdb_error_trying_to_delete);
                else
                    $this->set_session_message(tr_data_deleted);
        }//else no id
        else
            $this->set_session_message(tr_mdb_error_key_not_supplied,"e");
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
            $this->oBalanceIncome->set_id($id);
            $this->oBalanceIncome->autoquarantine();
                if($this->oBalanceIncome->is_error())
            {
                    $isError = true;
                    $this->set_session_message(tr_mdb_error_trying_to_delete,"e");
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
        $arOpButtons["filters"]=array("href"=>"javascript:reset_filters();","icon"=>"awe-magic","innerhtml"=>tr_mdb_bei_clear_filters);
        $arOpButtons["reload"]=array("href"=>"javascript:TfwControl.form_submit();","icon"=>"awe-refresh","innerhtml"=>tr_mdb_bei_refresh);
        $arOpButtons["multiadd"]=array("href"=>"javascript:multiadd();","icon"=>"awe-external-link","innerhtml"=>tr_mdb_bei_multiadd);
        $arOpButtons["closeme"]=array("href"=>"javascript:closeme();","icon"=>"awe-remove-sign","innerhtml"=>tr_mdb_bei_closeme);
        $oOpButtons = new AppHelperButtontabs(tr_mdb_bei_entities);
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
        //id_closed_by
        $this->set_filter("id_closed_by","selIdClosedBy");
        //id_type
        $this->set_filter("id_type","selIdType");
        //subtotal_open
        $this->set_filter("subtotal_open","txtSubtotalOpen",array("operator"=>"like"));
        //subtotal_cash
        $this->set_filter("subtotal_cash","txtSubtotalCash",array("operator"=>"like"));
        //subtotal_creditcard
        $this->set_filter("subtotal_creditcard","txtSubtotalCreditcard",array("operator"=>"like"));
        //subtotal_check
        $this->set_filter("subtotal_check","txtSubtotalCheck",array("operator"=>"like"));
        //subtotal
        $this->set_filter("subtotal","txtSubtotal",array("operator"=>"like"));
        //total_in
        $this->set_filter("total_in","txtTotalIn",array("operator"=>"like"));
        //notes
        $this->set_filter("notes","txaNotes",array("operator"=>"like"));
        //description
        //$this->set_filter("description","txtDescription",array("operator"=>"like"));
        //receipt_nr
        $this->set_filter("receipt_nr","txtReceiptNr",array("operator"=>"like"));
        //receipt_date
        $this->set_filter("receipt_date","datReceiptDate",array("operator"=>"like"));
        //operation_date
        $this->set_filter("operation_date","datOperationDate",array("operator"=>"like"));
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
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtId",tr_mdb_bei_fil_id));
        $arFields[] = $oAuxWrapper;
        //code_erp
        //$oAuxField = new HelperInputText("txtCodeErp","txtCodeErp");
        //$oAuxField->set_value($this->get_post("txtCodeErp"));
        //$oAuxField->on_entersubmit();
        //$oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtCodeErp",tr_mdb_bei_fil_code_erp));
        //$arFields[] = $oAuxWrapper;
        //id_closed_by
        $oClosedBy = new ModelUser();
        $arOptions = $oClosedBy->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdClosedBy","selIdClosedBy");
        $oAuxField->set_value_to_select($this->get_post("selIdClosedBy"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdClosedBy",tr_mdb_bei_fil_id_closed_by));
        $arFields[] = $oAuxWrapper;
        //id_type
        $oType = new ModelBalanceArray();
        $oType->use_language();
        $oType->set_type("income");
        $arOptions = $oType->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdType","selIdType");
        $oAuxField->set_value_to_select($this->get_post("selIdType"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdType",tr_mdb_bei_fil_id_type));
        $arFields[] = $oAuxWrapper;
        //subtotal_open
        $oAuxField = new HelperInputText("txtSubtotalOpen","txtSubtotalOpen");
        $oAuxField->set_value($this->get_post("txtSubtotalOpen"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtSubtotalOpen",tr_mdb_bei_fil_subtotal_open));
        $arFields[] = $oAuxWrapper;
        //subtotal_cash
        $oAuxField = new HelperInputText("txtSubtotalCash","txtSubtotalCash");
        $oAuxField->set_value($this->get_post("txtSubtotalCash"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtSubtotalCash",tr_mdb_bei_fil_subtotal_cash));
        $arFields[] = $oAuxWrapper;
        //subtotal_creditcard
        $oAuxField = new HelperInputText("txtSubtotalCreditcard","txtSubtotalCreditcard");
        $oAuxField->set_value($this->get_post("txtSubtotalCreditcard"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtSubtotalCreditcard",tr_mdb_bei_fil_subtotal_creditcard));
        $arFields[] = $oAuxWrapper;
        //subtotal_check
        $oAuxField = new HelperInputText("txtSubtotalCheck","txtSubtotalCheck");
        $oAuxField->set_value($this->get_post("txtSubtotalCheck"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtSubtotalCheck",tr_mdb_bei_fil_subtotal_check));
        $arFields[] = $oAuxWrapper;
        //subtotal
        $oAuxField = new HelperInputText("txtSubtotal","txtSubtotal");
        $oAuxField->set_value($this->get_post("txtSubtotal"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtSubtotal",tr_mdb_bei_fil_subtotal));
        $arFields[] = $oAuxWrapper;
        //total_in
        $oAuxField = new HelperInputText("txtTotalIn","txtTotalIn");
        $oAuxField->set_value($this->get_post("txtTotalIn"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtTotalIn",tr_mdb_bei_fil_total));
        $arFields[] = $oAuxWrapper;
        //notes
        $oAuxField = new HelperTextarea("txaNotes","txaNotes");
        $oAuxField->set_innerhtml($this->get_post("txaNotes"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txaNotes",tr_mdb_bei_fil_notes));
        $arFields[] = $oAuxWrapper;
        //description
        //$oAuxField = new HelperInputText("txtDescription","txtDescription");
        //$oAuxField->set_value($this->get_post("txtDescription"));
        //$oAuxField->on_entersubmit();
        //$oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtDescription",tr_mdb_bei_fil_description));
        //$arFields[] = $oAuxWrapper;
        //receipt_nr
        $oAuxField = new HelperInputText("txtReceiptNr","txtReceiptNr");
        $oAuxField->set_value($this->get_post("txtReceiptNr"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtReceiptNr",tr_mdb_bei_fil_receipt_nr));
        $arFields[] = $oAuxWrapper;
        //receipt_date
        $oAuxField = new HelperDate("datReceiptDate","datReceiptDate");
        $oAuxField->set_value($this->get_post("datReceiptDate"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("datReceiptDate",tr_mdb_bei_fil_receipt_date));
        $arFields[] = $oAuxWrapper;
        //operation_date
        $oAuxField = new HelperInputText("datOperationDate","datOperationDate");
        $oAuxField->set_value($this->get_post("datOperationDate"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("datOperationDate",tr_mdb_bei_fil_operation_date));
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
        //id_closed_by
        $this->set_filter_value("id_closed_by",$this->get_post("selIdClosedBy"));
        //id_type
        $this->set_filter_value("id_type",$this->get_post("selIdType"));
        //subtotal_open
        $this->set_filter_value("subtotal_open",$this->get_post("txtSubtotalOpen"));
        //subtotal_cash
        $this->set_filter_value("subtotal_cash",$this->get_post("txtSubtotalCash"));
        //subtotal_creditcard
        $this->set_filter_value("subtotal_creditcard",$this->get_post("txtSubtotalCreditcard"));
        //subtotal_check
        $this->set_filter_value("subtotal_check",$this->get_post("txtSubtotalCheck"));
        //subtotal
        $this->set_filter_value("subtotal",$this->get_post("txtSubtotal"));
        //total_in
        $this->set_filter_value("total_in",$this->get_post("txtTotalIn"));
        //notes
        $this->set_filter_value("notes",$this->get_post("txaNotes"));
        //description
        //$this->set_filter_value("description",$this->get_post("txtDescription"));
        //receipt_nr
        $this->set_filter_value("receipt_nr",$this->get_post("txtReceiptNr"));
        //receipt_date
        $this->set_filter_value("receipt_date",$this->get_post("datReceiptDate"));
        //operation_date
        $this->set_filter_value("operation_date",$this->get_post("datOperationDate"));
    }//set_multiassignfilters_from_post()

    //multiassign_5
    protected function get_multiassign_columns()
    {
        $arColumns["id"] = tr_mdb_bei_col_id;
        //$arColumns["code_erp"] = tr_mdb_bei_col_code_erp;
        //$arColumns["id_closed_by"] = tr_mdb_bei_col_id_closed_by;
        $arColumns["closedby"] = tr_mdb_bei_col_id_closed_by;
        //$arColumns["id_type"] = tr_mdb_bei_col_id_type;
        $arColumns["type"] = tr_mdb_bei_col_id_type;
        $arColumns["subtotal_open"] = tr_mdb_bei_col_subtotal_open;
        $arColumns["subtotal_cash"] = tr_mdb_bei_col_subtotal_cash;
        $arColumns["subtotal_creditcard"] = tr_mdb_bei_col_subtotal_creditcard;
        $arColumns["subtotal_check"] = tr_mdb_bei_col_subtotal_check;
        $arColumns["subtotal"] = tr_mdb_bei_col_subtotal;
        $arColumns["total_in"] = tr_mdb_bei_col_total_in;
        $arColumns["notes"] = tr_mdb_bei_col_notes;
        //$arColumns["description"] = tr_mdb_bei_col_description;
        $arColumns["receipt_nr"] = tr_mdb_bei_col_receipt_nr;
        $arColumns["receipt_date"] = tr_mdb_bei_col_receipt_date;
        $arColumns["operation_date"] = tr_mdb_bei_col_operation_date;
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
        $this->oBalanceIncome->set_orderby($this->get_orderby());
        $this->oBalanceIncome->set_ordertype($this->get_ordertype());
        $this->oBalanceIncome->set_filters($this->get_filter_searchconfig());
        //hierarchy recover
        //$this->oBalanceIncome->set_select_user($this->oSessionUser->get_id());
        //RECOVER DATALIST
        $arList = $this->oBalanceIncome->get_select_all_ids();
        $iRequestPage = $this->get_post("selPage");
        $oPage = new ComponentPage($arList,$iRequestPage);
        $arList = $oPage->get_items_to_show();
        $arList = $this->oBalanceIncome->get_select_all_by_ids($arList);
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
        $oOpButtons = new AppHelperButtontabs(tr_mdb_bei_entities);
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
        $arButTabs["filters"]=array("href"=>"javascript:reset_filters();","icon"=>"awe-magic","innerhtml"=>tr_mdb_bei_clear_filters);
        $arButTabs["reload"]=array("href"=>"javascript:TfwControl.form_submit();","icon"=>"awe-refresh","innerhtml"=>tr_mdb_bei_refresh);
        $arButTabs["closeme"]=array("href"=>"javascript:closeme();","icon"=>"awe-remove-sign","innerhtml"=>tr_mdb_bei_closeme);
        return $arButTabs;
    }//build_singleassign_buttons()

    //singleassign_2
    protected function load_config_singleassign_filters()
    {
        //id
        $this->set_filter("id","txtId",array("operator"=>"like"));
        //code_erp
        //$this->set_filter("code_erp","txtCodeErp",array("operator"=>"like"));
        //id_closed_by
        $this->set_filter("id_closed_by","selIdClosedBy");
        //id_type
        $this->set_filter("id_type","selIdType");
        //subtotal_open
        $this->set_filter("subtotal_open","txtSubtotalOpen",array("operator"=>"like"));
        //subtotal_cash
        $this->set_filter("subtotal_cash","txtSubtotalCash",array("operator"=>"like"));
        //subtotal_creditcard
        $this->set_filter("subtotal_creditcard","txtSubtotalCreditcard",array("operator"=>"like"));
        //subtotal_check
        $this->set_filter("subtotal_check","txtSubtotalCheck",array("operator"=>"like"));
        //subtotal
        $this->set_filter("subtotal","txtSubtotal",array("operator"=>"like"));
        //total_in
        $this->set_filter("total_in","txtTotalIn",array("operator"=>"like"));
        //notes
        $this->set_filter("notes","txaNotes",array("operator"=>"like"));
        //description
        //$this->set_filter("description","txtDescription",array("operator"=>"like"));
        //receipt_nr
        $this->set_filter("receipt_nr","txtReceiptNr",array("operator"=>"like"));
        //receipt_date
        $this->set_filter("receipt_date","datReceiptDate",array("operator"=>"like"));
        //operation_date
        $this->set_filter("operation_date","datOperationDate",array("operator"=>"like"));
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
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtId",tr_mdb_bei_fil_id));
        $arFields[] = $oAuxWrapper;
        //code_erp
        //$oAuxField = new HelperInputText("txtCodeErp","txtCodeErp");
        //$oAuxField->set_value($this->get_post("txtCodeErp"));
        //$oAuxField->on_entersubmit();
        //$oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtCodeErp",tr_mdb_bei_fil_code_erp));
        //$arFields[] = $oAuxWrapper;
        //id_closed_by
        $oClosedBy = new ModelUser();
        $arOptions = $oClosedBy->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdClosedBy","selIdClosedBy");
        $oAuxField->set_value_to_select($this->get_post("selIdClosedBy"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdClosedBy",tr_mdb_bei_fil_id_closed_by));
        $arFields[] = $oAuxWrapper;
        //id_type
        $oType = new ModelBalanceArray();
        $oType->use_language();
        $oType->set_type("income");
        $arOptions = $oType->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdType","selIdType");
        $oAuxField->set_value_to_select($this->get_post("selIdType"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdType",tr_mdb_bei_fil_id_type));
        $arFields[] = $oAuxWrapper;
        //subtotal_open
        $oAuxField = new HelperInputText("txtSubtotalOpen","txtSubtotalOpen");
        $oAuxField->set_value($this->get_post("txtSubtotalOpen"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtSubtotalOpen",tr_mdb_bei_fil_subtotal_open));
        $arFields[] = $oAuxWrapper;
        //subtotal_cash
        $oAuxField = new HelperInputText("txtSubtotalCash","txtSubtotalCash");
        $oAuxField->set_value($this->get_post("txtSubtotalCash"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtSubtotalCash",tr_mdb_bei_fil_subtotal_cash));
        $arFields[] = $oAuxWrapper;
        //subtotal_creditcard
        $oAuxField = new HelperInputText("txtSubtotalCreditcard","txtSubtotalCreditcard");
        $oAuxField->set_value($this->get_post("txtSubtotalCreditcard"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtSubtotalCreditcard",tr_mdb_bei_fil_subtotal_creditcard));
        $arFields[] = $oAuxWrapper;
        //subtotal_check
        $oAuxField = new HelperInputText("txtSubtotalCheck","txtSubtotalCheck");
        $oAuxField->set_value($this->get_post("txtSubtotalCheck"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtSubtotalCheck",tr_mdb_bei_fil_subtotal_check));
        $arFields[] = $oAuxWrapper;
        //subtotal
        $oAuxField = new HelperInputText("txtSubtotal","txtSubtotal");
        $oAuxField->set_value($this->get_post("txtSubtotal"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtSubtotal",tr_mdb_bei_fil_subtotal));
        $arFields[] = $oAuxWrapper;
        //total_in
        $oAuxField = new HelperInputText("txtTotalIn","txtTotalIn");
        $oAuxField->set_value($this->get_post("txtTotalIn"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtTotalIn",tr_mdb_bei_fil_total));
        $arFields[] = $oAuxWrapper;
        //notes
        $oAuxField = new HelperTextarea("txaNotes","txaNotes");
        $oAuxField->set_innerhtml($this->get_post("txaNotes"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txaNotes",tr_mdb_bei_fil_notes));
        $arFields[] = $oAuxWrapper;
        //description
        //$oAuxField = new HelperInputText("txtDescription","txtDescription");
        //$oAuxField->set_value($this->get_post("txtDescription"));
        //$oAuxField->on_entersubmit();
        //$oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtDescription",tr_mdb_bei_fil_description));
        //$arFields[] = $oAuxWrapper;
        //receipt_nr
        $oAuxField = new HelperInputText("txtReceiptNr","txtReceiptNr");
        $oAuxField->set_value($this->get_post("txtReceiptNr"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtReceiptNr",tr_mdb_bei_fil_receipt_nr));
        $arFields[] = $oAuxWrapper;
        //receipt_date
        $oAuxField = new HelperDate("datReceiptDate","datReceiptDate");
        $oAuxField->set_value($this->get_post("datReceiptDate"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("datReceiptDate",tr_mdb_bei_fil_receipt_date));
        $arFields[] = $oAuxWrapper;
        //operation_date
        $oAuxField = new HelperInputText("datOperationDate","datOperationDate");
        $oAuxField->set_value($this->get_post("datOperationDate"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("datOperationDate",tr_mdb_bei_fil_operation_date));
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
        //id_closed_by
        $this->set_filter_value("id_closed_by",$this->get_post("selIdClosedBy"));
        //id_type
        $this->set_filter_value("id_type",$this->get_post("selIdType"));
        //subtotal_open
        $this->set_filter_value("subtotal_open",$this->get_post("txtSubtotalOpen"));
        //subtotal_cash
        $this->set_filter_value("subtotal_cash",$this->get_post("txtSubtotalCash"));
        //subtotal_creditcard
        $this->set_filter_value("subtotal_creditcard",$this->get_post("txtSubtotalCreditcard"));
        //subtotal_check
        $this->set_filter_value("subtotal_check",$this->get_post("txtSubtotalCheck"));
        //subtotal
        $this->set_filter_value("subtotal",$this->get_post("txtSubtotal"));
        //subtotal_exclude
        $this->set_filter_value("subtotal_exclude",$this->get_post("txtSubtotalExclude"));        
        //total_in
        $this->set_filter_value("total_in",$this->get_post("txtTotalIn"));
        //notes
        $this->set_filter_value("notes",$this->get_post("txaNotes"));
        //description
        //$this->set_filter_value("description",$this->get_post("txtDescription"));
        //receipt_nr
        $this->set_filter_value("receipt_nr",$this->get_post("txtReceiptNr"));
        //receipt_date
        $this->set_filter_value("receipt_date",$this->get_post("datReceiptDate"));
        //operation_date
        $this->set_filter_value("operation_date",$this->get_post("datOperationDate"));
    }//set_singleassignfilters_from_post()

    //singleassign_5
    protected function get_singleassign_columns()
    {
        $arColumns["id"] = tr_mdb_bei_col_id;
        //$arColumns["code_erp"] = tr_mdb_bei_col_code_erp;
        //$arColumns["id_closed_by"] = tr_mdb_bei_col_id_closed_by;
        $arColumns["closedby"] = tr_mdb_bei_col_id_closed_by;
        //$arColumns["id_type"] = tr_mdb_bei_col_id_type;
        $arColumns["type"] = tr_mdb_bei_col_id_type;
        $arColumns["subtotal_open"] = tr_mdb_bei_col_subtotal_open;
        $arColumns["subtotal_cash"] = tr_mdb_bei_col_subtotal_cash;
        $arColumns["subtotal_creditcard"] = tr_mdb_bei_col_subtotal_creditcard;
        $arColumns["subtotal_check"] = tr_mdb_bei_col_subtotal_check;
        $arColumns["subtotal"] = tr_mdb_bei_col_subtotal;
        $arColumns["subtotal_exclude"] = tr_mdb_bei_col_subtotal_exclude;
        $arColumns["total_in"] = tr_mdb_bei_col_total_in;
        $arColumns["notes"] = tr_mdb_bei_col_notes;
        //$arColumns["description"] = tr_mdb_bei_col_description;
        $arColumns["receipt_nr"] = tr_mdb_bei_col_receipt_nr;
        $arColumns["receipt_date"] = tr_mdb_bei_col_receipt_date;
        $arColumns["operation_date"] = tr_mdb_bei_col_operation_date;
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
        $this->oBalanceIncome->set_orderby($this->get_orderby());
        $this->oBalanceIncome->set_ordertype($this->get_ordertype());
        $this->oBalanceIncome->set_filters($this->get_filter_searchconfig());
        $arList = $this->oBalanceIncome->get_select_all_ids();
        $iRequestPage = $this->get_post("selPage");
        $oPage = new ComponentPage($arList,$iRequestPage);
        $arList = $oPage->get_items_to_show();
        $arList = $this->oBalanceIncome->get_select_all_by_ids($arList);
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
        $oOpButtons = new AppHelperButtontabs(tr_mdb_bei_entities);
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
    
}