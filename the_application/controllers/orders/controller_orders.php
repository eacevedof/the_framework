<?php
/**
 * @author Module Builder 1.0.2
 *         Eduardo Acevedo Farje
 * @link www.eduardoaf.com
 * @version 1.1.2
 * @name ControllerOrders
 * @file controller_orders.php
 * @date 31-10-2014 20:20 (SPAIN)
 * @observations:
 * @requires
 */
//TFW
import_component("page,validate,filter,mailing");
import_helper("form,form_fieldset,form_legend,input_text,label,anchor,table,table_typed");
import_helper("input_password,button_basic,raw,div,javascript,input_date,textarea");
//APP
import_model("user,order_head,order_array,seller,customer,order_promotion,order_line");
import_appmain("controller,view,behaviour");
import_appbehaviour("picklist");
import_apphelper("listactionbar,controlgroup,formactions,buttontabs,headertabs,breadscrumbs,formhead,alertdiv");

class ControllerOrders extends TheApplicationController
{
    private $oOrderHead;
    
    public function __construct()
    {
        $this->sModuleName = "orders";
        //console,pgpage,clientdevice,sesuser,aftersucces,currop  | log,session,permission,view
        parent::__construct($this->sModuleName);
        $this->oOrderHead = new ModelOrderHead();
        $this->oOrderHead->set_platform($this->oSessionUser->get_platform());
        $id = $this->get_get("id");
        if(!$id) $id = $this->get_get("id_order_head");

        if($id)
        {
            $this->oOrderHead->set_id($id);
            $this->oOrderHead->load_by_id();
        }
    }
    
    private function mail_onchange_validate($id)
    {
        $this->oOrderHead = new ModelOrderHead($id);
        $this->oOrderHead->load_by_id();
        
        $oOrderArray = new ModelOrderArray();
        $oOrderArray->set_id($this->oOrderHead->get_id_type_validate());
        $oOrderArray->load_by_id();
        
        $sSubject = "CRM MAILER - Order status has been changed";
        
        $arContent[] = " ORDER Code:$id has been changed its status to "
                        .$oOrderArray->get_description()
                        ." by ".$this->oSessionUser->get_description();
        //DETAIL
        $sUrl = "http://".TFW_DOMAIN."/".TFW_FOLDER_PROJECT."/the_public/index.php".$this->build_url($this->sModuleName,NULL,"update","id=$id");
        $arContent[] = "Check detail: $sUrl";
        //LINES
        $sUrl = "http://".TFW_DOMAIN."/".TFW_FOLDER_PROJECT."/the_public/index.php".$this->build_url($this->sModuleName,"orderlines","get_list_by_head","id_order_head=$id");
        $arContent[] = "Check lines: $sUrl";
        
        //DETAIL
        $arContent[] = "";
        $arContent[] = "IN THE OFFICE:";
        $sUrl = "http://".$this->get_server_lanip()."/".TFW_FOLDER_PROJECT."/the_public/index.php".$this->build_url($this->sModuleName,NULL,"update","id=$id");
        $arContent[] = "Check detail: $sUrl";
        //LINES
        $sUrl = "http://".$this->get_server_lanip()."/".TFW_FOLDER_PROJECT."/the_public/index.php".$this->build_url($this->sModuleName,"orderlines","get_list_by_head","id_order_head=$id");
        $arContent[] = "Check lines: $sUrl";
        
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

        $oComponentMail->set_emails_to($arEmails);
        $oComponentMail->send();
        
        if($oComponentMail->is_error());
        {
            $this->log_custom("Sending email error: ".$oComponentMail->get_message());
            $this->set_session_message("Error sending email!","w");
        }
    }
    
    //<editor-fold defaultstate="collapsed" desc="LIST">
    //list_1
    private function build_list_scrumbs()
    {        
        $sUrlTab = $this->build_url();
        $arTabs["list"]=array("href"=>$sUrlTab,"innerhtml"=>"Orders");
        
        $this->oOrderHead->set_id($this->get_get("id_order_head"));
        $this->oOrderHead->load_by_id();
        $sUrlTab = $this->build_url($this->sModuleName,NULL,"update","id=".$this->get_get("id"));
        $arTabs["detail"]=array("href"=>$sUrlTab,"innerhtml"=>"Order: ".$this->oOrderHead->get_description());

        $sUrlTab = $this->build_url($this->sModuleName,"orderlines","get_list_by_head","id_order_head=".$this->get_get("id_order_head"));
        $arTabs["lines"]=array("href"=>$sUrlTab,"innerhtml"=>tr_entities);
        
        $oScrumbs = new AppHelperBreadscrumbs($arTabs);        
        return $oScrumbs;
    }//build_list_scrumbs()
    
    //list_2
    private function build_listoperation_buttons()
    {
        $arButTabs = array();
        $arButTabs["filters"]=array("href"=>"javascript:reset_filters();","icon"=>"awe-magic","innerhtml"=>tr_clear_filters);
        $arButTabs["reload"]=array("href"=>"javascript:TfwControl.form_submit();","icon"=>"awe-refresh","innerhtml"=>tr_refresh);
        if($this->oPermission->is_insert())
            $arButTabs["insert"]=array("href"=>$this->build_url($this->sModuleName,NULL,"insert"),"icon"=>"awe-plus","innerhtml"=>tr_new);
        if($this->oPermission->is_delete())
            $arButTabs["multidelete"]=array("href"=>"javascript:multi_delete();","icon"=>"awe-remove","innerhtml"=>tr_delete_selection);
        //if($this->oPermission->is_quarantine())
        //$arButTabs["multiquarantine"]=array("href"=>"javascript:multi_quarantine();","icon"=>"awe-remove","innerhtml"=>tr_quarantine);
        //crea ventana
        //$arButTabs["multiassign"]=array("href"=>"javascript:multiassign_window('order_head',null,'multiassign','order_head','addexternaldata');","icon"=>"awe-external-link","innerhtml"=>tr_asign_selection);
        //$arButTabs["singleassign"]=array("href"=>"javascript:single_pick('order_head','singleassign','txtI','txtI');","icon"=>"awe-external-link","innerhtml"=>tr_asign_selection);
        $oOpButtons = new AppHelperButtontabs(tr_entities);
        $oOpButtons->set_tabs($arButTabs);        
        return $oOpButtons;
    }//build_listoperation_buttons()

    //list_3
    private function load_config_list_filters()
    {
        //id
        $this->set_filter("id","txtId",array("operator"=>"like"));
        //date
        $this->set_filter("date","datDate",array("operator"=>">="));        
        //code_erp
        //$this->set_filter("code_erp","txtCodeErp",array("operator"=>"like"));
        //id_type_validate
        //$this->set_filter("id_type_validate","selIdTypeValidate"));
        //id_type_payment
        $this->set_filter("id_type_payment",array("operator"=>"="));
        //id_customer
        //$this->set_filter("id_customer","selIdCustomer"));
        //id_seller
        //$this->set_filter("id_seller","selIdSeller"));        
        //id_delivery_user
//        $this->set_filter("id_delivery_user","selIdDeliveryUser"));
        //amount_subtotal
//        $this->set_filter("amount_subtotal","txtAmountSubtotal",array("operator"=>"like"));
        //amount_withtax
//        $this->set_filter("amount_withtax","txtAmountWithtax",array("operator"=>"like"));
        //amount_total
//        $this->set_filter("amount_total","txtAmountTotal",array("operator"=>"like"));
        //description
//        $this->set_filter("description","txtDescription",array("operator"=>"like"));
        //delivery_address
//        $this->set_filter("delivery_address","txtDeliveryAddress",array("operator"=>"like"));
        //delivery_date
//        $this->set_filter("delivery_date","datDeliveryDate",array("operator"=>"like"));
        //delivery_hour
//        $this->set_filter("delivery_hour","txtDseliveryHour",array("operator"=>"like"));
        //is_payed
//        $this->set_filter("is_payed","selIsPayed",array("operator"=>"like"));
        
    }//load_config_list_filters()
    
    //list_4
    private function set_listfilters_from_post()
    {
        //id
        $this->set_filter_value("id",$this->get_post("txtId"));
        //date
        $this->set_filter_value("date",$this->get_post("datDate"));        
        //code_erp
        //$this->set_filter_value("code_erp",$this->get_post("txtCodeErp"));
        //id_type_validate
        //$this->set_filter_value("id_type_validate",$this->get_post("selIdTypeValidate"));
        //id_type_payment
        $this->set_filter_value("id_type_payment",$this->get_post("selIdTypePayment"));
        //id_customer
        //$this->set_filter_value("id_customer",$this->get_post("selIdCustomer"));
        //id_seller
        //$this->set_filter_value("id_seller",$this->get_post("selIdSeller"));        
        //id_delivery_user
//        $this->set_filter_value("id_delivery_user",$this->get_post("selIdDeliveryUser"));
        //amount_subtotal
//        $this->set_filter_value("amount_subtotal",$this->get_post("txtAmountSubtotal"));
        //amount_withtax
//        $this->set_filter_value("amount_withtax",$this->get_post("txtAmountWithtax"));
        //amount_total
//        $this->set_filter_value("amount_total",$this->get_post("txtAmountTotal"));
        //description
//        $this->set_filter_value("description",$this->get_post("txtDescription"));
        //delivery_address
//        $this->set_filter_value("delivery_address",$this->get_post("txtDeliveryAddress"));
        //delivery_date
//        $this->set_filter_value("delivery_date",$this->get_post("datDeliveryDate"));
        //delivery_hour
//        $this->set_filter_value("delivery_hour",$this->get_post("txtDeliveryHour"));
        //is_payed
//        $this->set_filter_value("is_payed",$this->get_post("selIsPayed"));   
    }//set_listfilters_from_post()    
    
    //list_5
    private function get_list_filters()
    {
        //CAMPOS
        //id
        $oAuxField = new HelperInputText("txtId","txtId");
        $oAuxField->set_value($this->get_post("txtId"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtId",tr_fil_id));
        $arFields[] = $oAuxWrapper;
        //date
        $oAuxField = new HelperDate("datDate","datDate");
        $oAuxField->set_is_ipadiphone($this->is_ipad()||$this->is_iphone());
        $oAuxField->set_value($this->get_post("datDate"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("datDate",tr_fil_date));
        $arFields[] = $oAuxWrapper;        
        //id_type_validate
        $oModelOrderArray = new ModelOrderArray(); 
        $arOptions = $oModelOrderArray->get_picklist_by_type("validate");
        $oAuxField = new HelperSelect($arOptions,"selIdTypeValidate","selIdTypeValidate");
        $oAuxField->set_value_to_select($this->get_post("selIdTypeValidate"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdTypeValidate",tr_fil_id_type_validate));
        $arFields[] = $oAuxWrapper;
        //id_type_payment
        $oModelOrderArray = new ModelOrderArray();
        $arOptions = $oModelOrderArray->get_picklist_by_type("payment");
        $oAuxField = new HelperSelect($arOptions,"selIdTypePayment","selIdTypePayment");
        $oAuxField->set_value_to_select($this->get_post("selIdTypePayment"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdTypePayment",tr_fil_id_type_payment));
        $arFields[] = $oAuxWrapper;
/*        
        //code_erp
//        $oAuxField = new HelperInputText("txtCodeErp","txtCodeErp");
//        $oAuxField->set_value($this->get_post("txtCodeErp"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtCodeErp",tr_fil_code_erp));
//        $arFields[] = $oAuxWrapper;        
        //id_seller
//        $oModelSeller = new ModelSeller();
//        $arOptions = $oModelSeller->get_picklist();
//        $oAuxField = new HelperSelect($arOptions,"selIdSeller","selIdSeller");
//        $oAuxField->set_value_to_select($this->get_post("selIdSeller"));
//        $oAuxField->set_postback();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdSeller",tr_fil_id_seller));
//        $arFields[] = $oAuxWrapper;
        //id_customer
//        $oModelCustomer = new ModelCustomer();
//        $arOptions = $oModelCustomer->get_picklist();
//        $oAuxField = new HelperSelect($arOptions,"selIdCustomer","selIdCustomer");
//        $oAuxField->set_value_to_select($this->get_post("selIdCustomer"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdCustomer",tr_fil_id_customer));
//        $arFields[] = $oAuxWrapper;
        
        //id_delivery_user
//        $oModelSeller = new ModelSeller();
//        $arOptions = $oModelSeller->get_picklist();
//        $oAuxField = new HelperSelect($arOptions,"selIdDeliveryUser","selIdDeliveryUser");
//        $oAuxField->set_value_to_select($this->get_post("selIdDeliveryUser"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdDeliveryUser",tr_fil_id_delivery_user));
//        $arFields[] = $oAuxWrapper;
        //amount_subtotal
//        $oAuxField = new HelperInputText("txtAmountSubtotal","txtAmountSubtotal");
//        $oAuxField->set_value($this->get_post("txtAmountSubtotal"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtAmountSubtotal",tr_fil_amount_subtotal));
//        $arFields[] = $oAuxWrapper;
        //amount_withtax
//        $oAuxField = new HelperInputText("txtAmountWithtax","txtAmountWithtax");
//        $oAuxField->set_value($this->get_post("txtAmountWithtax"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtAmountWithtax",tr_fil_amount_withtax));
//        $arFields[] = $oAuxWrapper;
        //amount_total
//        $oAuxField = new HelperInputText("txtAmountTotal","txtAmountTotal");
//        $oAuxField->set_value($this->get_post("txtAmountTotal"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtAmountTotal",tr_fil_amount_total));
//        $arFields[] = $oAuxWrapper;
        //description
//        $oAuxField = new HelperInputText("txtDescription","txtDescription");
//        $oAuxField->set_value($this->get_post("txtDescription"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtDescription",tr_fil_description));
//        $arFields[] = $oAuxWrapper;
        //delivery_address
//        $oAuxField = new HelperInputText("txtDeliveryAddress","txtDeliveryAddress");
//        $oAuxField->set_value($this->get_post("txtDeliveryAddress"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtDeliveryAddress",tr_fil_delivery_address));
//        $arFields[] = $oAuxWrapper;
        //delivery_date
//        $oAuxField = new HelperInputText("datDeliveryDate","datDeliveryDate");
//        $oAuxField->set_value($this->get_post("datDeliveryDate"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("datDeliveryDate",tr_fil_delivery_date));
//        $arFields[] = $oAuxWrapper;
        //delivery_hour
//        $oAuxField = new HelperInputText("txtDeliveryHour","txtDeliveryHour");
//        $oAuxField->set_value($this->get_post("txtDeliveryHour"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtDeliveryHour",tr_fil_delivery_hour));
//        $arFields[] = $oAuxWrapper;
        //is_payed
//        $oAuxField = new HelperInputText("selIsPayed","selIsPayed");
//        $oAuxField->set_value($this->get_post("selIsPayed"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIsPayed",tr_fil_is_payed));
//        $arFields[] = $oAuxWrapper;
 * 
 */
        return $arFields;
    }//get_list_filters()

    //list_6
    private function get_list_columns()
    {
        $arColumns = array();
        $arColumns["id"]=tr_col_id;
        $arColumns["date"]=tr_col_date;
        $arColumns["seller"]=tr_col_id_seller;
        $arColumns["customer"]=tr_col_id_customer;        
        $arColumns["amount_total"]=tr_col_amount_total;
        $arColumns["validate"]=tr_col_id_type_validate;        
        //$arColumns["code_erp"]=tr_col_code_erp;
        //$arColumns["amount_withtax"]=tr_col_amount_withtax;
        //$arColumns["id_type_payment"]=tr_col_id_type_payment;
        //$arColumns["payment"]=tr_col_id_type_payment;
        //$arColumns["id_seller"]=tr_col_id_seller;
        //$arColumns["id_customer"]=tr_col_id_customer;
        //$arColumns["amount_subtotal"]=tr_col_amount_subtotal;
        //$arColumns["delivery_address"]=tr_col_delivery_address;
        //$arColumns["delivery_date"]=tr_col_delivery_date;
        //$arColumns["delivery_hour"]=tr_col_delivery_hour;
        //$arColumns["is_payed"]=tr_col_is_payed;
        //$arColumns["payed"]=tr_col_is_payed;
        //$arColumns["id_type_validate"]=tr_col_id_type_validate;
        //$arColumns["id_delivery_user"]=tr_col_id_delivery_user;
        //$arColumns["delivery_user"]=tr_col_id_delivery_user;
        //$arColumns["url_lines"]=tr_info;
        return $arColumns;
    }//get_list_columns()

    //list_7
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

        $this->oOrderHead->set_orderby($this->get_orderby());
        $this->oOrderHead->set_ordertype($this->get_ordertype());
        $this->oOrderHead->set_filters($this->get_filter_searchconfig(array("date"=>"date")));
        $this->oOrderHead->set_select_user($this->oSessionUser->get_id());
        $arList = $this->oOrderHead->get_select_all_ids();
        $iRequestPage = $this->get_post("selPage");
        $oPage = new ComponentPage($arList,$iRequestPage);
        $arList = $oPage->get_items_to_show();
        $arList = $this->oOrderHead->get_select_all_by_ids($arList);
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
        $arExtra[] = array("position"=>1,"label"=>"Lines");
        $oTableList->add_extra_colums($arExtra);
        $oTableList->set_column_anchor(array("virtual_0"=>array
        ("href"=>"url_lines","innerhtml"=>tr_order_lines,"class"=>"btn btn-info","icon"=>"awe-info-sign")));
        //$oTableList->set_column_delete();
        // ("date","datetime4","datetime6","time4","time6","int","numeric2")
        $arFormat = array("amount_total"=>"numeric2","date"=>"date","delivery_date"=>"date");
        $oTableList->set_format_columns($arFormat);
        if($this->oPermission->is_quarantine())
            $oTableList->set_column_quarantine();
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
        $oOpButtons = $this->build_listoperation_buttons();
        $oJavascript = new HelperJavascript();
        $oJavascript->set_filters($this->get_filter_controls_id());
        $oJavascript->set_focusid("id_all");
        $this->oView->add_var($oJavascript,"oJavascript");
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
        $sUrlTab = $this->build_url();
        $arTabs["list"]=array("href"=>$sUrlTab,"innerhtml"=>tr_entities);
        
        $sUrlTab = $this->build_url($this->sModuleName,NULL,"insert");
        $arTabs["detail"]=array("href"=>$sUrlTab,"innerhtml"=>tr_entity_insert);
        
        $oScrumbs = new AppHelperBreadscrumbs($arTabs);        
        return $oScrumbs;
    }//build_insert_scrumbs()
    
    //insert_2
    private function build_insert_opbuttons()
    {
        $arButTabs = array();
        $arButTabs["list"]=array("href"=>$this->build_url(),"icon"=>"awe-search","innerhtml"=>tr_list);
        
        $oOpButtons = new AppHelperButtontabs(tr_entities);
        $oOpButtons->set_tabs($arButTabs);
        return $oOpButtons;
    }//build_insert_opbuttons()

    //insert_3
    private function get_insert_validate()
    {
        $arFieldsConfig = array();
        //$arFieldsConfig["id"] = array("controlid"=>"txtId","label"=>tr_ins_id,"length"=>9,"type"=>array("numeric","required"));
        $arFieldsConfig["id_seller"] = array("controlid"=>"selIdSeller","label"=>tr_ins_id_seller,"length"=>9,"type"=>array("required"));
        $arFieldsConfig["date"] = array("controlid"=>"datDate","label"=>tr_ins_date,"length"=>10,"type"=>array("required"));
        $arFieldsConfig["code_erp"] = array("controlid"=>"txtCodeErp","label"=>tr_ins_code_erp,"length"=>25,"type"=>array());
        $arFieldsConfig["id_customer"] = array("controlid"=>"selIdCustomer","label"=>tr_ins_id_customer,"length"=>9,"type"=>array("required"));
        $arFieldsConfig["delivery_address"] = array("controlid"=>"txtDeliveryAddress","label"=>tr_ins_delivery_address,"length"=>200,"type"=>array("required"));
        $arFieldsConfig["id_type_validate"] = array("controlid"=>"txtIdTypeValidate","label"=>tr_ins_id_type_validate,"length"=>4,"type"=>array("required"));
        $arFieldsConfig["id_type_payment"] = array("controlid"=>"selIdTypePayment","label"=>tr_ins_id_type_payment,"length"=>4,"type"=>array("required"));
        $arFieldsConfig["amount_total"] = array("controlid"=>"txtAmountTotal","label"=>tr_ins_amount_total,"length"=>9,"type"=>array("numeric"));
//        $arFieldsConfig["description"] = array("controlid"=>"txtDescription","label"=>tr_ins_description,"length"=>200,"type"=>array());
        //$arFieldsConfig["id_delivery_user"] = array("controlid"=>"selIdDeliveryUser","label"=>tr_ins_id_delivery_user,"length"=>9,"type"=>array("numeric"));
        //$arFieldsConfig["amount_subtotal"] = array("controlid"=>"txtAmountSubtotal","label"=>tr_ins_amount_subtotal,"length"=>9,"type"=>array("numeric"));
        //$arFieldsConfig["amount_withtax"] = array("controlid"=>"txtAmountWithtax","label"=>tr_ins_amount_withtax,"length"=>9,"type"=>array("numeric"));
        //$arFieldsConfig["delivery_date"] = array("controlid"=>"datDeliveryDate","label"=>tr_ins_delivery_date,"length"=>8,"type"=>array());
        //$arFieldsConfig["delivery_hour"] = array("controlid"=>"txtDeliveryHour","label"=>tr_ins_delivery_hour,"length"=>4,"type"=>array());
        //$arFieldsConfig["is_payed"] = array("controlid"=>"selIsPayed","label"=>tr_ins_is_payed,"length"=>3,"type"=>array());
        return $arFieldsConfig;
    }//get_insert_validate
    
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
        //$oAuxField->readonly();
        $oAuxField->add_class("readonly");
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdSeller"));
        $oAuxLabel = new HelperLabel("selIdSeller",tr_ins_id_seller,"lblIdSeller");
        $oAuxLabel->add_class("labelreq");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //date
        $oAuxField = new HelperDate("datDate","datDate");
        //$oAuxField->set_is_ipadiphone($this->is_ipad()||$this->is_iphone());
        $oAuxField->set_value(date("d/m/Y"));
        $oAuxField->readonly();
        $oAuxField->add_class("readonly");
        if($usePost) $oAuxField->set_value($this->get_post("datDate"));
        $oAuxLabel = new HelperLabel("datDate",tr_ins_date,"lblDate");
        $oAuxLabel->add_class("control-label");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //id_customer
        $oModelCustomer = new ModelCustomer();
        $oModelCustomer->set_select_user($this->oSessionUser->get_id());
        $oModelCustomer->set_id($this->get_post("selIdCustomer"));
        $oModelCustomer->load_by_id();
        $arOptions = $oModelCustomer->get_picklist_hierarchy("customer","id");
        $oAuxField = new HelperSelect($arOptions,"selIdCustomer","selIdCustomer");
        $oAuxField->set_postback();
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdCustomer"));
        $oAuxLabel = new HelperLabel("selIdCustomer",tr_ins_id_customer,"lblIdCustomer");
        $oAuxLabel->add_class("labelreq");
        $oGroup = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        $oButton = new HelperButtonBasic();
        $oButton->add_class("btn btn-info");
        $oButton->set_innerhtml("Get Orders");
        $oButton->set_js_onclick("get_toplist();");
        $sHtmlButtons = $oButton->get_html();
        $oButton->set_innerhtml("Show Notes");
        $oButton->set_js_onclick("get_history();");
        $sHtmlButtons .= $oButton->get_html();
        $oGroup->set_append_text($sHtmlButtons);
        $arFields[] = $oGroup;
        $oGroup = NULL; $oButton = NULL;

        //delivery_address
        $oAuxField = new HelperInputText("txtDeliveryAddress","txtDeliveryAddress");
        $oAuxField->on_enterinsert();
        if($usePost) $oAuxField->set_value($this->get_post("txtDeliveryAddress"));
        if($this->is_postback("selIdCustomer"))$oAuxField->set_value($oModelCustomer->get_address());
        $oAuxLabel = new HelperLabel("txtDeliveryAddress",tr_ins_delivery_address,"lblDeliveryAddress");
        $oAuxLabel->add_class("labelreq");
        //$oAuxField->readonly();
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //id_type_payment
        $oModelOrderArray = new ModelOrderArray();
        $arOptions = $oModelOrderArray->get_picklist_by_type("payment");
        $oAuxField = new HelperSelect($arOptions,"selIdTypePayment","selIdTypePayment");
        $oAuxField->set_postback();
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdTypePayment"));
        $oAuxLabel = new HelperLabel("selIdTypePayment",tr_ins_id_type_payment,"lblIdTypePayment");
        $oAuxLabel->add_class("labelreq");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
                
        //discount
        $oAuxField = new HelperInputText("txtDiscount","txtDiscount");
        $oAuxField->set_value(dbbo_numeric2($this->oOrderHead->get_discount()));
        $oAuxField->readonly();
        $oAuxField->add_class("readonly");
        $oAuxField->set_value("0.00");
        if($this->is_postback("selIdTypePayment"))
        {
            $oOrderPromotion = new ModelOrderPromotion();
            $oOrderPromotion->set_id_type_payment($this->get_post("selIdTypePayment"));
            $oOrderPromotion->load_by_id_payment();
            $oAuxField->set_value(dbbo_numeric2($oOrderPromotion->get_min_units_discount()));
        }        
        $oAuxLabel = new HelperLabel("txtDiscount",tr_ins_discount,"lblDiscount");
        $oAuxLabel->add_class("control-label");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //amount_total
        $oAuxField = new HelperInputText("txtAmountTotal","txtAmountTotal");
        $oAuxField->set_value("0.00");
        $oAuxField->readonly();
        $oAuxField->add_class("readonly");
        if($usePost) $oAuxField->set_value($this->get_post("txtAmountTotal"));
        $oAuxLabel = new HelperLabel("txtAmountTotal",tr_ins_amount_total,"lblAmountTotal");
        $oAuxLabel->add_class("control-label");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //id_type_validate
        $oModelOrderArray = new ModelOrderArray();
        $arOptions = $oModelOrderArray->get_picklist_by_type("validate");
        $oAuxField = new HelperSelect($arOptions,"selIdTypeValidate","selIdTypeValidate");
        $oAuxField->set_value_to_select(1);
        $oAuxField->readonly();
        $oAuxField->add_class("readonly");
        $oAuxField->set_postback();
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdTypeValidate"));
        $oAuxLabel = new HelperLabel("selIdTypeValidate",tr_ins_id_type_validate,"lblIdTypeValidate");
        $oAuxLabel->add_class("control-label");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        $oAuxField = new HelperInputHidden("hidTimeTakingDate","hidTimeTakingStart");
        if($this->is_post("hidTimeTakingStart"))$oAuxField->set_value($this->get_post("hidTimeTakingStart"));
        else $oAuxField->set_value(date("H:i"));
        $arFields[] = $oAuxField;
        
        $oAuxField = new HelperInputHidden("hidTimeTakingEnd","hidTimeTakingEnd");
        if($this->is_post("hidTimeTakingEnd"))$oAuxField->set_value($this->get_post("hidTimeTakingEnd"));
        //else $oAuxField->set_value(date("H:i"));
        $arFields[] = $oAuxField;
        //id
        //$oAuxField = new HelperInputText("txtId","txtId");
        //
        //$oAuxField->is_primarykey();
        //if($usePost) $oAuxField->set_value($this->get_post("txtId"));
        //$oAuxLabel = new HelperLabel("txtId",tr_ins_id,"lblId");
        //$oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();
        //$arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //code_erp
//        $oAuxField = new HelperInputText("txtCodeErp","txtCodeErp");
//        $oAuxField->set_value("NOT YET");
//        $oAuxField->on_enterinsert();
//        if($usePost) $oAuxField->set_value($this->get_post("txtCodeErp"));
//        $oAuxLabel = new HelperLabel("txtCodeErp",tr_ins_code_erp,"lblCodeErp");
//        $oAuxLabel->add_class("control-label");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //amount_subtotal
//        $oAuxField = new HelperInputText("txtAmountSubtotal","txtAmountSubtotal");
//        $oAuxField->set_value("0.00");
//        $oAuxField->readonly();
//        $oAuxField->add_class("readonly");        
//        if($usePost) $oAuxField->set_value($this->get_post("txtAmountSubtotal"));
//        $oAuxLabel = new HelperLabel("txtAmountSubtotal",tr_ins_amount_subtotal,"lblAmountSubtotal");
//        $oAuxLabel->add_class("control-label");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//                
        //description
//        $oAuxField = new HelperInputText("txtDescription","txtDescription");
//        $oAuxField->on_enterinsert();
//        if($usePost) $oAuxField->set_innerhtml($this->get_post("txtDescription"));
//        $oAuxLabel = new HelperLabel("txtDescription",tr_ins_description,"lblDescription");
//        $oAuxLabel->add_class("control-label");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //amount_withtax
//        $oAuxField = new HelperInputText("txtAmountWithtax","txtAmountWithtax");
//        if($usePost) $oAuxField->set_value($this->get_post("txtAmountWithtax"));
//        $oAuxLabel = new HelperLabel("txtAmountWithtax",tr_ins_amount_withtax,"lblAmountWithtax");
//        $oAuxLabel->add_class("control-label");
//        //$oAuxField->readonly();
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
//        //delivery_date
//        $oAuxField = new HelperInputText("datDeliveryDate","datDeliveryDate");
//        if($usePost) $oAuxField->set_value($this->get_post("datDeliveryDate"));
//        $oAuxLabel = new HelperLabel("datDeliveryDate",tr_ins_delivery_date,"lblDeliveryDate");
//        $oAuxLabel->add_class("labelpk");
//        //$oAuxField->readonly();
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        
//        //delivery_hour
//        $oAuxField = new HelperInputText("txtDeliveryHour","txtDeliveryHour");
//        if($usePost) $oAuxField->set_value($this->get_post("txtDeliveryHour"));
//        $oAuxLabel = new HelperLabel("txtDeliveryHour",tr_ins_delivery_hour,"lblDeliveryHour");
//        $oAuxLabel->add_class("labelpk");
//        //$oAuxField->readonly();
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//
//        //id_delivery_user
//        $oModelSeller = new ModelSeller();
//        $arOptions = $oModelSeller->get_picklist();
//        $oAuxField = new HelperSelect($arOptions,"selIdDeliveryUser","selIdDeliveryUser");
//        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdDeliveryUser"));
//        $oAuxLabel = new HelperLabel("selIdDeliveryUser",tr_ins_id_delivery_user,"lblIdDeliverUser");
//        $oAuxLabel->add_class("labelpk");
//        //$oAuxField->readonly();
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        
//        //is_payed
//        $oAuxField = new HelperSelect($arOptions,"selIsPayed","selIsPayed");
//        $oAuxField->readonly();
//        $oAuxField->add_class("readonly");
//        $oAuxField->set_value_to_select(0);
//        $oAuxField->set_selected_value_as_hidden_on();
//       
//        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIsPayed"));
//        $oAuxLabel = new HelperLabel("selIsPayed",tr_ins_is_payed,"lblIsPayed");
//        $oAuxLabel->add_class("labelpk");
//        //$oAuxField->readonly();
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //button
        $oAuxField = new HelperButtonBasic("butSave",tr_ins_savebutton);
        $oAuxField->add_class("btn btn-primary");
        $oAuxField->set_js_onclick("insert();");
        $arFields[] = new ApphelperFormactions(array($oAuxField));
        
        //Post Action
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
    }//build_insert_form()

    //insert_6
    public function insert()
    {
        $oAlert = new AppHelperAlertdiv();
        $oAlert->use_close_button();        
        
        $arFieldsConfig = $this->get_insert_validate();
        if($this->is_inserting())
        {
            $arFieldsValues = $this->get_fields_from_post();
            //bug($arFieldsValues,"arFieldValues");
            $oValidate = new ComponentValidate($arFieldsConfig,$arFieldsValues);
            $arErrData = $oValidate->get_error_field();
            if($arErrData)
            {
                $oAlert->set_type("e");
                $oAlert->set_title(tr_data_not_saved);
                $oAlert->set_content("Field <b>".$arErrData["label"]."</b> ".$arErrData["message"]);
            }
            //!arErrData
            else
            {
                $arFieldsValues["date"] = bodb_date($arFieldsValues["date"],"/");
                $arFieldsValues["time_taking_start"] = bodb_time4($arFieldsValues["time_taking_start"]);
                //bug($arFieldsValues); die;
                $this->oOrderHead->set_attrib_value($arFieldsValues);
                $this->oOrderHead->set_insert_user($this->oSessionUser->get_id());
                //$this->oOrderHead->set_platform($this->oSessionUser->get_platform());
                $oModelCustomer = new ModelCustomer($this->get_post("selIdCustomer"));
                $oModelCustomer->load_by_id();
                $this->oOrderHead->set_description($oModelCustomer->get_description());
                $this->oOrderHead->autoinsert();
                
                if($this->oOrderHead->is_error())
                {
                    $oAlert->set_type("e");
                    $oAlert->set_title(tr_data_not_saved);
                    $oAlert->set_content(tr_error_trying_to_save);
                }
                else
                {
                    $sUrl = $this->build_url($this->sModuleName,"orderlines","get_list_by_head","id_order_head=".$this->oOrderHead->get_last_insert_id());
                    $oAlert->set_title(tr_data_saved);
                    $this->reset_post();
                    $this->go_to_url($sUrl);
                }
            }//no error
        }//fin if is_inserting (post action=save)
        else
        {
            $sMessage = $this->get_session_message($sMessage);
            if($sMessage)
                $oAlert->set_title($sMessage);
            $sMessage = $this->get_session_message($sMessage,"e");
            if($sMessage)
            {
                $oAlert->set_type();
                $oAlert->set_title($sMessage);
            }            
        }
        
        //Si hay errores se recupera desde post
        if($arErrData || $this->is_postback()) $oForm = $this->build_insert_form(1);
        else $oForm = $this->build_insert_form();

        //SCRUMBS
        $oScrumbs = $this->build_insert_scrumbs();
        //BUTTONS
        $oOpButtons = $this->build_insert_opbuttons();
        
        $oJavascript = new HelperJavascript();
        $oJavascript->set_validate_config($arFieldsConfig);
        $oJavascript->set_formid("frmInsert");
        
        //VIEW SET
        $this->oView->add_js("js_orders");
        $this->oView->add_var($oScrumbs,"oScrumbs");
        $this->oView->add_var($oOpButtons,"oOpButtons");
        $this->oView->add_var($oAlert,"oAlert");
        $this->oView->add_var($oForm,"oForm");
        $this->oView->add_var($oJavascript,"oJavascript");
        $this->oView->set_path_view("orders/view_insert");
        $this->oView->show_page();
    }//insert()
    //</editor-fold>

    //<editor-fold defaultstate="collapsed" desc="UPDATE">
    //update_1
    private function build_update_scrumbs()
    {        
        $sUrlTab = $this->build_url();
        $arTabs["list"]=array("href"=>$sUrlTab,"innerhtml"=>tr_entities);
        $sUrlTab = $this->build_url($this->sModuleName,NULL,"update","id=".$this->get_get("id"));
        $arTabs["detail"]=array("href"=>$sUrlTab,"innerhtml"=>tr_entity.": ".$this->oOrderHead->get_description());

        $oScrumbs = new AppHelperBreadscrumbs($arTabs);        
        return $oScrumbs;
    }//build_update_scrumbs()
    
    //update_2
    private function build_update_tabs()
    {        
        $arTabs = array();
        $sUrlTab = $this->build_url($this->sModuleName,NULL,"update","id=".$this->get_get("id"));
        $arTabs["detail"]=array("href"=>$sUrlTab,"innerhtml"=>"Detail");
        
        $sUrlTab = $this->build_url($this->sModuleName,"orderlines","get_list_by_head","id_order_head=".$this->get_get("id"));
        $arTabs["lines"]=array("href"=>$sUrlTab,"innerhtml"=>"Lines");

        $oTabs = new AppHelperHeadertabs($arTabs,"detail");        
        return $oTabs;
    }//build_update_tabs()

    //update_3
    private function build_update_opbuttons()
    {
        $arOpButtons = array();
        if($this->oPermission->is_select())
            $arOpButtons["list"]=array("href"=>$this->build_url(),"icon"=>"awe-search","innerhtml"=>tr_list);
        //if($this->oPermission->is_insert())
            //$arOpButtons["insert"]=array("href"=>$this->build_url($this->sModuleName,NULL,"insert"),"icon"=>"awe-plus","innerhtml"=>tr_new);
        //if($this->oPermission->is_delete())
            //$arOpButtons["delete"]=array("href"=>$this->build_url($this->sModuleName,NULL,"delete","id=".$this->get_get("id")),"icon"=>"awe-remove","innerhtml"=>tr_delete);
        if($this->oPermission->is_quarantine())
            $arOpButtons["delete"]=array("href"=>$this->build_url($this->sModuleName,NULL,"quarantine","id=".$this->get_get("id")),"icon"=>"awe-remove","innerhtml"=>tr_delete);
        $oOpButtons = new AppHelperButtontabs(tr_entities);
        $oOpButtons->set_tabs($arOpButtons);
        return $oOpButtons;
    }//build_update_opbuttons()

    //update_4
    private function get_update_validate()
    {
        $arFieldsConfig = array();
        //$arFieldsConfig["id"] = array("controlid"=>"txtId","label"=>tr_ins_id,"length"=>9,"type"=>array("numeric","required"));
        $arFieldsConfig["id_seller"] = array("controlid"=>"selIdSeller","label"=>tr_ins_id_seller,"length"=>9,"type"=>array("required"));
        $arFieldsConfig["date"] = array("controlid"=>"datDate","label"=>tr_ins_date,"length"=>10,"type"=>array("required"));
        $arFieldsConfig["code_erp"] = array("controlid"=>"txtCodeErp","label"=>tr_ins_code_erp,"length"=>25,"type"=>array());
        $arFieldsConfig["id_customer"] = array("controlid"=>"selIdCustomer","label"=>tr_ins_id_customer,"length"=>9,"type"=>array("required"));
        $arFieldsConfig["delivery_address"] = array("controlid"=>"txtDeliveryAddress","label"=>tr_ins_delivery_address,"length"=>200,"type"=>array("required"));
        $arFieldsConfig["id_type_validate"] = array("controlid"=>"txtIdTypeValidate","label"=>tr_ins_id_type_validate,"length"=>4,"type"=>array("required"));
        $arFieldsConfig["id_type_payment"] = array("controlid"=>"selIdTypePayment","label"=>tr_ins_id_type_payment,"length"=>4,"type"=>array("required"));
        $arFieldsConfig["amount_total"] = array("controlid"=>"txtAmountTotal","label"=>tr_ins_amount_total,"length"=>9,"type"=>array("numeric"));
//        $arFieldsConfig["description"] = array("controlid"=>"txtDescription","label"=>tr_ins_description,"length"=>200,"type"=>array());
        //$arFieldsConfig["id_delivery_user"] = array("controlid"=>"selIdDeliveryUser","label"=>tr_ins_id_delivery_user,"length"=>9,"type"=>array("numeric"));
        //$arFieldsConfig["amount_subtotal"] = array("controlid"=>"txtAmountSubtotal","label"=>tr_ins_amount_subtotal,"length"=>9,"type"=>array("numeric"));
        //$arFieldsConfig["amount_withtax"] = array("controlid"=>"txtAmountWithtax","label"=>tr_ins_amount_withtax,"length"=>9,"type"=>array("numeric"));
        //$arFieldsConfig["delivery_date"] = array("controlid"=>"datDeliveryDate","label"=>tr_ins_delivery_date,"length"=>8,"type"=>array());
        //$arFieldsConfig["delivery_hour"] = array("controlid"=>"txtDeliveryHour","label"=>tr_ins_delivery_hour,"length"=>4,"type"=>array());
        //$arFieldsConfig["is_payed"] = array("controlid"=>"selIsPayed","label"=>tr_ins_is_payed,"length"=>3,"type"=>array());
        return $arFieldsConfig;
    }//get_update_validate
    
    //update_5
    private function build_update_fields($usePost=0)
    {
        $arFields = array(); $oAuxField = NULL; $oAuxLabel = NULL;
        //id
        $oAuxField = new HelperInputText("txtId","txtId");
        $oAuxField->add_class("readonly");
        $oAuxField->is_primarykey();
        $oAuxField->readonly();
        $oAuxField->set_value($this->oOrderHead->get_id());
        if($usePost) $oAuxField->set_value($this->get_post("txtId"));
        $oAuxLabel = new HelperLabel("txtId",tr_upd_id,"lblId");
        $oAuxLabel->add_class("control-label text-pk color-pk");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //date
        $oAuxField = new HelperDate("datDate","datDate");
        //$oAuxField->set_is_ipadiphone($this->is_ipad()||$this->is_iphone());
        $oAuxField->set_value(dbbo_date($this->oOrderHead->get_date(),"/"));
        $oAuxField->readonly();
        $oAuxField->add_class("readonly");
        //if($usePost) $oAuxField->set_value($this->get_post("datDate"));
        $oAuxLabel = new HelperLabel("datDate",tr_upd_date,"lblDate");
        $oAuxLabel->add_class("labelreq");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);        
        
        //code_erp
//        $oAuxField = new HelperInputText("txtCodeErp","txtCodeErp");
//        $oAuxField->set_value($this->oOrderHead->get_code_erp());
//        $oAuxField->on_enterupdate();
//        if($usePost) $oAuxField->set_value($this->get_post("txtCodeErp"));
//        $oAuxLabel = new HelperLabel("txtCodeErp",tr_upd_code_erp,"lblCodeErp");
//        $oAuxLabel->add_class("control-label");
//        //$oAuxField->readonly();
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        
        //id_seller
        $oModelSeller = new ModelSeller();
        $arOptions = $oModelSeller->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdSeller","selIdSeller");
        $oAuxField->set_value_to_select($this->oOrderHead->get_id_seller());
        $oAuxField->readonly();
        $oAuxField->add_class("readonly");
        //if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdSeller"));
        $oAuxLabel = new HelperLabel("selIdSeller",tr_upd_id_seller,"lblIdSeller");
        $oAuxLabel->add_class("labelreq");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //id_customer
        $oModelCustomer = new ModelCustomer();
        $oModelCustomer->set_id($this->oOrderHead->get_id_customer());
        $oModelCustomer->load_by_id();
        $arOptions = $oModelCustomer->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdCustomer","selIdCustomer");
        $oAuxField->add_class("readonly");
        //if($usePost) 
        $oAuxField->set_value_to_select($this->oOrderHead->get_id_customer());
        $oAuxLabel = new HelperLabel("selIdCustomer",tr_ins_id_customer,"lblIdCustomer");
        $oAuxLabel->add_class("labelreq");
        $oAuxField->readonly();
        $oGroup = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        $oButton = new HelperButtonBasic();
        $oButton->add_class("btn btn-info");
        $oButton->set_innerhtml("Get Orders");
        $oButton->set_js_onclick("get_toplist();");
        $sHtmlButtons = $oButton->get_html();
        $oButton->set_innerhtml("Show Notes");
        $oButton->set_js_onclick("get_history();");
        $sHtmlButtons .= $oButton->get_html();
        $oGroup->set_append_text($sHtmlButtons);
        $arFields[] = $oGroup;
        $oGroup = NULL; $oButton = NULL;

        //delivery_address
        $oAuxField = new HelperInputText("txtDeliveryAddress","txtDeliveryAddress");
        $oAuxField->set_value($this->oOrderHead->get_delivery_address());
        //$oAuxField->readonly();
        if($usePost) $oAuxField->set_value($this->get_post("txtDeliveryAddress"));
        $oAuxLabel = new HelperLabel("txtDeliveryAddress",tr_upd_delivery_address,"lblDeliveryAddress");
        $oAuxLabel->add_class("labelreq");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //id_type_payment
        $oModelOrderArray = new ModelOrderArray();
        $arOptions = $oModelOrderArray->get_picklist_by_type("payment");
        $oAuxField = new HelperSelect($arOptions,"selIdTypePayment","selIdTypePayment");
        $oAuxField->set_value_to_select($this->oOrderHead->get_id_type_payment());
        $oAuxField->readonly();
        $oAuxField->add_class("readonly");
        //if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdTypePayment"));
        $oAuxLabel = new HelperLabel("selIdTypePayment",tr_upd_id_type_payment,"lblIdTypePayment");
        $oAuxLabel->add_class("labelreq");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        /*"1" TAKING
	"2" PROCESS
	"3" CONFIRM
	"8" DELIVERY
	"7" VISIT
	"9" CANCELED*/
        if($this->oOrderHead->get_id_type_validate()>="3")
        {    
            //id_delivery_user
            $oModelSeller = new ModelSeller();
            $arOptions = $oModelSeller->get_picklist();
            $oAuxField = new HelperSelect($arOptions,"selIdDeliveryUser","selIdDeliveryUser");
            $oAuxField->set_value_to_select($this->oOrderHead->get_id_delivery_user());
            $oAuxField->set_postback();
            if($this->oOrderHead->get_id_delivery_user())
            {
                $oAuxField->readonly();
                $oAuxField->add_class("readonly");
            }
            
            if($this->is_postback("selIdDeliveryUser") 
                    && ($this->oOrderHead->get_id_delivery_user()!=$this->get_post("selIdDeliveryUser") 
                    && $this->get_post("selIdDeliveryUser")))
                $isDeliveryUser = TRUE;

            if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdDeliveryUser"));
            $oAuxLabel = new HelperLabel("selIdDeliveryUser",tr_upd_id_delivery_user,"lblIdDeliverUser");
            $oAuxLabel->add_class("control-label");
            $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);            

            if($isDeliveryUser || $this->oOrderHead->get_id_delivery_user())
            {
                //delivery_date
                $oAuxField = new HelperDate("datDeliveryDate","datDeliveryDate");
                //$oAuxField->set_value(dbbo_date($this->oOrderHead->get_delivery_date(),"/"));
                if($isDeliveryUser) $oAuxField->set_value(date("d/m/Y"));
                $oAuxField->readonly();
                $oAuxField->add_class("readonly");
                $oAuxLabel = new HelperLabel("datDeliveryDate",tr_upd_delivery_date,"lblDeliveryDate");
                $oAuxLabel->add_class("control-label");
                $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

                //delivery_hour
                $oAuxField = new HelperInputText("txtDeliveryHour","txtDeliveryHour");
                if($this->oOrderHead->get_delivery_hour())
                    $oAuxField->set_value(dbbo_time6($this->oOrderHead->get_delivery_hour()));
                if($isDeliveryUser) $oAuxField->set_value(date("H:i:s"));
                $oAuxField->readonly();
                $oAuxField->add_class("readonly");
                //if($usePost) $oAuxField->set_value($this->get_post("txtDeliveryHour"));
                $oAuxLabel = new HelperLabel("txtDeliveryHour",tr_upd_delivery_hour,"lblDeliveryHour");
                $oAuxLabel->add_class("control-label");
                $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
            }
        }
        //discount
        $oAuxField = new HelperInputText("txtDiscount","txtDiscount");
        $oAuxField->set_value(dbbo_numeric2($this->oOrderHead->get_discount()));
        if($this->is_postback("selIdTypePayment"))
        {
            $oOrderPromotion = new ModelOrderPromotion();
            $oOrderPromotion->set_id_type_payment($this->get_post("selIdTypePayment"));
            $oOrderPromotion->load_by_id_payment();
            $oAuxField->set_value($oOrderPromotion->get_min_units_discount());
        }
        $oAuxField->readonly();
        $oAuxField->add_class("readonly");
        //if($usePost) $oAuxField->set_value($this->get_post("txtDiscount"));
        $oAuxLabel = new HelperLabel("txtDiscount",tr_upd_discount,"lblDiscount");
        $oAuxLabel->add_class("control-label");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //amount_discounted
        $oAuxField = new HelperInputText("txtAmountDiscounted","txtAmountDiscounted");
        $oAuxField->set_value(dbbo_numeric2($this->oOrderHead->get_amount_discounted()));
        if($usePost) $oAuxField->set_value(dbbo_numeric2($this->get_post("txtAmountDiscounted")));
        $oAuxLabel = new HelperLabel("txtAmountDiscounted",tr_orh_ins_amount_discounted,"lblAmountDiscounted");
        $oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //amount_total
        $oAuxField = new HelperInputText("txtAmountTotal","txtAmountTotal");
        $oAuxField->set_value(dbbo_numeric2($this->oOrderHead->get_amount_total()));
        $oAuxField->readonly();
        $oAuxField->add_class("readonly");
        //if($usePost) $oAuxField->set_value($this->get_post("txtAmountTotal"));
        $oAuxLabel = new HelperLabel("txtAmountTotal",tr_upd_amount_total,"lblAmountTotal");
        $oAuxLabel->add_class("control-label");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //id_type_validate
        $oModelOrderArray = new ModelOrderArray();
        $arOptions = $oModelOrderArray->get_picklist_by_type("validate",1);
        
        $oAuxField = new HelperSelect($arOptions,"selIdTypeValidate","selIdTypeValidate");
        $oAuxField->set_value_to_select($this->oOrderHead->get_id_type_validate());
        $oAuxField->set_postback();
        if($this->oOrderHead->get_id_type_validate()!="8" && $isDeliveryUser)
        {
            $oAuxField->set_value_to_select("8");
            $oAuxField->readonly(); $oAuxField->add_class("readonly");
        }
        elseif($this->oOrderHead->get_id_type_validate()=="8")
        {
            $oAuxField->readonly(); $oAuxField->add_class("readonly");
        }
        
        if($usePost && !$isDeliveryUser) $oAuxField->set_value_to_select($this->get_post("selIdTypeValidate"));
        $oAuxLabel = new HelperLabel("selIdTypeValidate",tr_upd_id_type_validate,"lblIdTypeValidate");
        $oAuxLabel->add_class("labelreq");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //is_payed
//        $oAuxField = new HelperSelect("selIsPayed","selIsPayed");
//        $oAuxField->set_value_to_select($this->oOrderHead->get_is_payed());
//        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIsPayed"));
//        $oAuxLabel = new HelperLabel("selIsPayed",tr_upd_is_payed,"lblIsPayed");
//        $oAuxLabel->add_class("control-label");
//        //$oAuxField->readonly();
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //amount_subtotal
//        $oAuxField = new HelperInputText("txtAmountSubtotal","txtAmountSubtotal");
//        $oAuxField->set_value($this->oOrderHead->get_amount_subtotal());
//        if($usePost) $oAuxField->set_value($this->get_post("txtAmountSubtotal"));
//        $oAuxLabel = new HelperLabel("txtAmountSubtotal",tr_upd_amount_subtotal,"lblAmountSubtotal");
//        $oAuxLabel->add_class("control-label");
//        //$oAuxField->readonly();
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //amount_withtax
//        $oAuxField = new HelperInputText("txtAmountWithtax","txtAmountWithtax");
//        $oAuxField->set_value($this->oOrderHead->get_amount_withtax());
//        if($usePost) $oAuxField->set_value($this->get_post("txtAmountWithtax"));
//        $oAuxLabel = new HelperLabel("txtAmountWithtax",tr_upd_amount_withtax,"lblAmountWithtax");
//        $oAuxLabel->add_class("control-label");
//        //$oAuxField->readonly();
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //description
//        $oAuxField = new HelperInputText("txtDescription","txtDescription");
//        $oAuxField->set_value($this->oOrderHead->get_description());
//        if($usePost) $oAuxField->set_value($this->get_post("txtDescription"));
//        $oAuxLabel = new HelperLabel("txtDescription",tr_upd_description,"lblDescription");
//        $oAuxLabel->add_class("control-label");
//        //$oAuxField->readonly();
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //BUTTON SAVE 
        $oAuxField = new HelperButtonBasic("butSave",tr_upd_savebutton);
        $oAuxField->add_class("btn btn-primary");
        $oAuxField->set_js_onclick("update();");
        if($this->oPermission->is_update())
            $arFields[] = new ApphelperFormactions(array($oAuxField));
        //AUDIT INFO
        $sRegInfo = $this->get_audit_info($this->oOrderHead->get_insert_user(),$this->oOrderHead->get_insert_date()
        ,$this->oOrderHead->get_update_user(),$this->oOrderHead->get_update_date());
        $oAuxField = new AppHelperFormhead(null,$sRegInfo);
        $oAuxField->set_span();
        $arFields[] = $oAuxField;
        //Accion
        $oAuxField = new HelperInputHidden("hidAction","hidAction");
        $arFields[] = $oAuxField;
        $oAuxField = new HelperInputHidden("hidPostback","hidPostback");
        $arFields[] = $oAuxField;        
        return $arFields;
    }//build_update_fields()

    //update_6
    private function build_update_form($usePost=0)
    {
        $id = $this->get_get("id");
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
        return $oForm;
    }//build_update_form()

    //update_7
    public function update()
    {
        $this->go_to_401($this->oPermission->is_not_read() && $this->oPermission->is_not_update());
        
        //Validacion con PHP y JS
        $arFieldsConfig = $this->get_update_validate();
        if($this->is_updating())
        {
            $oAlert = new AppHelperAlertdiv();
            $oAlert->use_close_button();
            $arValidate = array
            ("date"=>"date","hour"=>"time6","delivery_date"=>"date","delivery_hour"=>"time6"
             ,"amount_subtotal"=>"numeric2","discount"=>"numeric2","amount_discounted"=>"numeric2"
             ,"amount_withtax"=>"numeric2","amount_total"=>"numeric2");

            $arFieldsValues = $this->get_fields_from_post($arValidate);
            //bug($arFieldsValues,"die"); die;
            $oValidate = new ComponentValidate($arFieldsConfig,$arFieldsValues);
            $arErrData = $oValidate->get_error_field();
            
            if($arErrData)
            {
                $oAlert->set_type("e");
                $oAlert->set_title(tr_data_not_saved);
                $oAlert->set_content("Field <b>".$arErrData["label"]."</b> ".$arErrData["message"]);
            }
            //No hay errores de datos en el formulario
            else
            {
                $idTypeValidateBd = $this->oOrderHead->get_id_type_validate();
                $idTypeValidateNew = $this->get_post("selIdTypeValidate");
                $isValidateChanged = ($idTypeValidateBd!=$idTypeValidateNew);
                
                //SE PASA A ESTADO PROCESANDO
                if($this->oOrderHead->get_id_type_validate()!="2" && $this->get_post("selIdTypeValidate")=="2")
                {
                    if(!$this->oOrderHead->has_lines())
                    {
                        $oAlert->set_type("e");
                        $oAlert->set_title(tr_data_not_saved);
                        $oAlert->set_content("It has no lines");                    
                    }
                    elseif(!$this->oOrderHead->has_min_saleitems())
                    {
                        $oAlert->set_type("e");
                        $oAlert->set_title(tr_data_not_saved);
                        $oAlert->set_content("It does not reach minimun items");
                    }
                    elseif((int)$this->oOrderHead->get_amount_total()==0) 
                    {
                        $oAlert->set_type("e");
                        $oAlert->set_title(tr_data_not_saved);
                        $oAlert->set_content("Total amount is 0.00");                    
                    }
                    //No hay errores en la gestión del pedido
                    else
                    {
                        $this->oOrderHead->set_attrib_value($arFieldsValues);                
                        $this->oOrderHead->set_update_user($this->oSessionUser->get_id());
                        if(!$this->oOrderHead->get_time_taking_end())
                            $this->oOrderHead->set_time_taking_end(date("Hi"));
                        
                        $this->oOrderHead->autoupdate();
                        
                        if($this->oOrderHead->is_error())
                        {
                            $oAlert->set_type("e");
                            $oAlert->set_title(tr_data_not_saved);
                            $oAlert->set_content(tr_error_trying_to_save);
                        }//no error
                        else
                        {
                            //$oAlert->set_title(tr_data_saved);
                            //$this->reset_post();
                            $this->set_session_message(tr_data_saved." Order in process");
                            //SEND EMAIL
                            if($isValidateChanged)
                                $this->mail_onchange_validate($this->get_get("id"));
                            $this->go_to_insert();
                        }//error save                        
                    }    
                }//fin comprobacion de cambio de estado a PROCESS
                else 
                {
                    $this->oOrderHead->set_attrib_value($arFieldsValues);                
                    $this->oOrderHead->set_update_user($this->oSessionUser->get_id());
                    $oModelCustomer = new ModelCustomer($this->get_post("selIdCustomer"));
                    $oModelCustomer->load_by_id();
                    $this->oOrderHead->set_description($this->oOrderHead->get_id()." - ".$oModelCustomer->get_description());                    
                    $this->oOrderHead->autoupdate();
                    if($this->oOrderHead->is_error())
                    {
                        $oAlert->set_type("e");
                        $oAlert->set_title(tr_data_not_saved);
                        $oAlert->set_content(tr_error_trying_to_save);
                    }//no error
                    else
                    {
                        if($isValidateChanged)
                            $this->mail_onchange_validate($this->get_get("id"));                        
                        $oAlert->set_title(tr_data_saved);
                        $this->reset_post();
                    }//error save                    
                }
            }//error validation
        }//is_updating()
        
        if($arErrData || $this->is_postback() || $this->is_post()) 
        {   
            $oForm = $this->build_update_form(1);
            if($this->is_postback("selIdTypeValidate") && $this->get_post("selIdTypeValidate")=="2")
            {
                $oAlert = new AppHelperAlertdiv();
                $oAlert->use_close_button();
                
                if(!$this->oOrderHead->has_lines())
                {
                    $oAlert->set_type("i");
                    $oAlert->set_title("This order will be not confirmed!");
                    $oAlert->set_content("It has no lines");                    
                }
                elseif(!$this->oOrderHead->has_min_saleitems())
                {
                    $oAlert->set_type("i");
                    $oAlert->set_title("This order will be not confirmed!");
                    $oAlert->set_content("It does not reach minimun items");
                }
                elseif((int)$this->oOrderHead->get_amount_total()==0) 
                {
                    $oAlert->set_type("i");
                    $oAlert->set_title("This order will be not confirmed!");
                    $oAlert->set_content("Total amount is 0.00");                    
                }
                //comprobar que el pedido tenga lineas.
                //que tenga el minimo de unidades segun tipo de pago
                //
            }//tipo de validación = 3
        }//en caso que se haga postback con status
        else 
            $oForm = $this->build_update_form();
        
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

        $this->oView->add_js("js_orders");
        $this->oView->add_var($oScrumbs,"oScrumbs");
        $this->oView->add_var($oTabs,"oTabs");
        $this->oView->add_var($oOpButtons,"oOpButtons");
        $this->oView->add_var($oAlert,"oAlert");
        $this->oView->add_var($oForm,"oForm");
        $this->oView->add_var($oJavascript,"oJavascript");
        //custom_view
        $this->oView->set_path_view("orders/view_update");
        $this->oView->show_page();
    }//update()
    //</editor-fold>

    //<editor-fold defaultstate="collapsed" desc="DELETE">
    //delete_1
    private function single_delete()
    {
        //esto ha creado en el constructor el objeto orderhead
        $id = $this->get_get("id");
        if($id)
        {
            $this->oOrderHead->set_id($id);
            $this->oOrderHead->autodelete();
            if($this->oOrderHead->is_error())
            {
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
        $isError = false;
        //Intenta recuperar pkeys sino pasa a recuperar el id. En ultimo caso lo que se ha pasado por parametro
        $arKeys = $this->get_listkeys();
        $this->oOrderHead = new ModelOrderHead();
        foreach($arKeys as $sKey)
        {
            $id = $sKey;
            $this->oOrderHead->set_id($id);
            $this->oOrderHead->autodelete();
            if($this->oOrderHead->is_error())
            {
                $isError = true;
                $this->set_session_message(tr_error_trying_to_delete,"e");
            }
        }
        if(!$isError)
            $this->set_session_message(tr_data_deleted);
    }//multi_delete()

    //delete_3
    public function delete()
    {
        $this->go_to_401($this->oPermission->is_not_delete());
        if($this->is_multidelete())
            $this->multi_delete();
        else
            $this->single_delete();
        $this->go_to_list();
    }//delete()
    //</editor-fold>

    //<editor-fold defaultstate="collapsed" desc="QUARANTINE">
    private function multi_quarantine()
    {
        $isError = false;
        //Intenta recuperar pkeys sino pasa a id, y en ultimo caso lo que se ha pasado por parametro
        $arKeys = $this->get_listkeys();
        $this->oOrderHead = new ModelOrderHead();
        foreach($arKeys as $sKey)
        {
            $id = $sKey;
            $this->oOrderHead->set_id($id);
            $this->oOrderHead->autoquarantine();
            if($this->oOrderHead->is_error())
            {
                $isError = true;
                $this->set_session_message(tr_error_trying_to_delete,"e");
            }
        }
        if(!$isError)
            $this->set_session_message(tr_data_deleted);
    }//multi_quarantine()

    private function single_quarantine()
    {
        $id = $this->get_get("id");
        if($id)
        {
            $oModelOrderLine = new ModelOrderLine();
            $oModelOrderLine->set_id_order_head($id);
            $oModelOrderLine->quarantine_by_head();
            $this->oOrderHead->autoquarantine();
            
            if($this->oOrderHead->is_error())
                $this->set_session_message(tr_error_trying_to_delete);
            else
                $this->set_session_message(tr_data_deleted);
        }//else no id
        else
        $this->set_session_message(tr_error_key_not_supplied,"e");
    }//single_quarantine()

    public function quarantine()
    {
        $this->go_to_401($this->oPermission->is_not_quarantine());
        if($this->is_multiquarantine())
            $this->multi_quarantine();
        else
            $this->single_quarantine();
        $this->go_to_list();
    }//quarantine()

    //</editor-fold>

    //<editor-fold defaultstate="collapsed" desc="MULTIASSIGN">
    private function build_multiassign_buttons()
    {
        $arButTabs = array();
        $arButTabs["filters"]=array("href"=>"javascript:reset_filters();","icon"=>"awe-magic","innerhtml"=>tr_clear_filters);
        $arButTabs["reload"]=array("href"=>"javascript:TfwControl.form_submit();","icon"=>"awe-refresh","innerhtml"=>tr_refresh);
        $arButTabs["multiadd"]=array("href"=>"javascript:multiadd();","icon"=>"awe-external-link","innerhtml"=>"add values to");
        $arButTabs["closeme"]=array("href"=>"javascript:closeme();","icon"=>"awe-remove-sign","innerhtml"=>tr_closeme);
        return $arButTabs;
    }//build_multiassign_buttons()
    private function get_multiassign_filters()
    {
        //CAMPOS
        //id
        $this->set_filter("id","txtId",array("operator"=>"like","value"=>$this->get_post("txtId")));
        $oAuxField = new HelperInputText("txtId","txtId");
        $oAuxField->set_value($this->get_post("txtId"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtId",tr_fil_id));
        $arFields[] = $oAuxWrapper;
        //code_erp
        $this->set_filter("code_erp","txtCodeErp",array("operator"=>"like","value"=>$this->get_post("txtCodeErp")));
        $oAuxField = new HelperInputText("txtCodeErp","txtCodeErp");
        $oAuxField->set_value($this->get_post("txtCodeErp"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtCodeErp",tr_fil_code_erp));
        $arFields[] = $oAuxWrapper;
        //id_type_validate
        $this->set_filter("id_type_validate","selIdTypeValidate",array("value"=>$this->get_post("selIdTypeValidate")));
        $oModelOrderArray = new ModelOrderArray();
        $arOptions = $oModelOrderArray->get_picklist_by_type("validate");
        $oAuxField = new HelperSelect($arOptions,"selIdTypeValidate","selIdTypeValidate");
        $oAuxField->set_value_to_select($this->get_post("selIdTypeValidate"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdTypeValidate",tr_fil_id_type_validate));
        $arFields[] = $oAuxWrapper;
        //id_type_payment
        $this->set_filter("id_type_payment","selIdTypePayment",array("value"=>$this->get_post("selIdTypePayment")));
        $oModelOrderArray = new ModelOrderArray();
        $arOptions = $oModelOrderArray->get_picklist_by_type("payment");
        $oAuxField = new HelperSelect($arOptions,"selIdTypePayment","selIdTypePayment");
        $oAuxField->set_value_to_select($this->get_post("selIdTypePayment"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdTypePayment",tr_fil_id_type_payment));
        $arFields[] = $oAuxWrapper;
        //id_seller
        $this->set_filter("id_seller","selIdSeller",array("value"=>$this->get_post("selIdSeller")));
        $oModelSeller = new ModelSeller();
        $arOptions = $oModelSeller->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdSeller","selIdSeller");
        $oAuxField->set_value_to_select($this->get_post("selIdSeller"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdSeller",tr_fil_id_seller));
        $arFields[] = $oAuxWrapper;
        //id_customer
        $this->set_filter("id_customer","selIdCustomer",array("value"=>$this->get_post("selIdCustomer")));
        $oModelCustomer = new ModelCustomer();
        $arOptions = $oModelCustomer->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdCustomer","selIdCustomer");
        $oAuxField->set_value_to_select($this->get_post("selIdCustomer"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdCustomer",tr_fil_id_customer));
        $arFields[] = $oAuxWrapper;
        //id_delivery_user
        $this->set_filter("id_delivery_user","selIdDeliveryUser",array("value"=>$this->get_post("selIdDeliveryUser")));
        $oModelSeller = new ModelSeller();
        $arOptions = $oModelSeller->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdDeliveryUser","selIdDeliveryUser");
        $oAuxField->set_value_to_select($this->get_post("selIdDeliveryUser"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdDeliveryUser",tr_fil_id_delivery_user));
        $arFields[] = $oAuxWrapper;
        //amount_subtotal
        $this->set_filter("amount_subtotal","txtAmountSubtotal",array("operator"=>"like","value"=>$this->get_post("txtAmountSubtotal")));
        $oAuxField = new HelperInputText("txtAmountSubtotal","txtAmountSubtotal");
        $oAuxField->set_value($this->get_post("txtAmountSubtotal"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtAmountSubtotal",tr_fil_amount_subtotal));
        $arFields[] = $oAuxWrapper;
        //amount_withtax
        $this->set_filter("amount_withtax","txtAmountWithtax",array("operator"=>"like","value"=>$this->get_post("txtAmountWithtax")));
        $oAuxField = new HelperInputText("txtAmountWithtax","txtAmountWithtax");
        $oAuxField->set_value($this->get_post("txtAmountWithtax"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtAmountWithtax",tr_fil_amount_withtax));
        $arFields[] = $oAuxWrapper;
        //amount_total
        $this->set_filter("amount_total","txtAmountTotal",array("operator"=>"like","value"=>$this->get_post("txtAmountTotal")));
        $oAuxField = new HelperInputText("txtAmountTotal","txtAmountTotal");
        $oAuxField->set_value($this->get_post("txtAmountTotal"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtAmountTotal",tr_fil_amount_total));
        $arFields[] = $oAuxWrapper;
        //description
        $this->set_filter("description","txtDescription",array("operator"=>"like","value"=>$this->get_post("txtDescription")));
        $oAuxField = new HelperInputText("txtDescription","txtDescription");
        $oAuxField->set_value($this->get_post("txtDescription"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtDescription",tr_fil_description));
        $arFields[] = $oAuxWrapper;
        //date
        $this->set_filter("date","datDate",array("operator"=>"like","value"=>$this->get_post("datDate")));
        $oAuxField = new HelperInputText("datDate","datDate");
        $oAuxField->set_value($this->get_post("datDate"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("datDate",tr_fil_date));
        $arFields[] = $oAuxWrapper;
        //delivery_address
        $this->set_filter("delivery_address","txtDeliveryAddress",array("operator"=>"like","value"=>$this->get_post("txtDeliveryAddress")));
        $oAuxField = new HelperInputText("txtDeliveryAddress","txtDeliveryAddress");
        $oAuxField->set_value($this->get_post("txtDeliveryAddress"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtDeliveryAddress",tr_fil_delivery_address));
        $arFields[] = $oAuxWrapper;
        //delivery_date
        $this->set_filter("delivery_date","datDeliveryDate",array("operator"=>"like","value"=>$this->get_post("datDeliveryDate")));
        $oAuxField = new HelperInputText("datDeliveryDate","datDeliveryDate");
        $oAuxField->set_value($this->get_post("datDeliveryDate"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("datDeliveryDate",tr_fil_delivery_date));
        $arFields[] = $oAuxWrapper;
        //delivery_hour
        $this->set_filter("delivery_hour","txtDeliveryHour",array("operator"=>"like","value"=>$this->get_post("txtDeliveryHour")));
        $oAuxField = new HelperInputText("txtDeliveryHour","txtDeliveryHour");
        $oAuxField->set_value($this->get_post("txtDeliveryHour"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtDeliveryHour",tr_fil_delivery_hour));
        $arFields[] = $oAuxWrapper;
        //is_payed
        $this->set_filter("is_payed","selIsPayed",array("operator"=>"like","value"=>$this->get_post("selIsPayed")));
        $oAuxField = new HelperInputText("selIsPayed","selIsPayed");
        $oAuxField->set_value($this->get_post("selIsPayed"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIsPayed",tr_fil_is_payed));
        $arFields[] = $oAuxWrapper;
        return $arFields;
    }//get_multiassign_filters()

    private function get_multiassign_columns()
    {
        $arColumns = array("id"=>tr_col_id,"code_erp"=>tr_col_code_erp,"id_type_validate"=>tr_col_id_type_validate,"id_type_payment"=>tr_col_id_type_payment,"id_seller"=>tr_col_id_seller,"id_customer"=>tr_col_id_customer,"id_delivery_user"=>tr_col_id_delivery_user,"amount_subtotal"=>tr_col_amount_subtotal,"amount_withtax"=>tr_col_amount_withtax,"amount_total"=>tr_col_amount_total,"date"=>tr_col_date,"delivery_address"=>tr_col_delivery_address,"delivery_date"=>tr_col_delivery_date,"delivery_hour"=>tr_col_delivery_hour,"is_payed"=>tr_col_is_payed);
        return $arColumns;
    }//get_multiassign_columns()

    public function multiassign()
    {
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
        $arObjFilter = $this->get_multiassign_filters();
        $arColumns = $this->get_multiassign_columns(); 

        $oFilter = new ComponentFilter();
        $oFilter->set_fieldnames($this->get_filter_fieldnames());
        //Indica que no se guardara en sesion por nombre de campo sino por nombre de control
        //para esto es necesario respetar el estricto camelcase
        $oFilter->use_field_prefix();
        //Guarda en sesion y post los campos enviados, los de orden y página
        $oFilter->refresh();
        
        $this->oOrderHead = new ModelOrderHead();
        $this->oOrderHead->set_orderby($this->get_orderby());
        $this->oOrderHead->set_ordertype($this->get_ordertype());
        $this->oOrderHead->set_filters($this->get_filter_searchconfig());
        $arList = $this->oOrderHead->get_select_all_ids();
        $iRequestPage = $this->get_post("selPage");
        $oPage = new ComponentPage($arList,$iRequestPage);
        $arList = $oPage->get_items_to_show();
        $arList = $this->oOrderHead->get_select_all_by_ids($arList);
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
        $oOpButtons = new AppHelperButtontabs(tr_entities);
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
    private function build_singleassign_buttons()
    {
        $arButTabs = array();
        $arButTabs["filters"]=array("href"=>"javascript:reset_filters();","icon"=>"awe-magic","innerhtml"=>tr_clear_filters);
        $arButTabs["reload"]=array("href"=>"javascript:TfwControl.form_submit();","icon"=>"awe-refresh","innerhtml"=>tr_refresh);
        $arButTabs["closeme"]=array("href"=>"javascript:closeme();","icon"=>"awe-remove-sign","innerhtml"=>tr_closeme);
        return $arButTabs;
    }//build_singleassign_buttons()
    private function get_singleassign_filters()
    {
        //CAMPOS
        //id
        $this->set_filter("id","txtId",array("operator"=>"like","value"=>$this->get_post("txtId")));
        $oAuxField = new HelperInputText("txtId","txtId");
        $oAuxField->set_value($this->get_post("txtId"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtId",tr_fil_id));
        $arFields[] = $oAuxWrapper;
        //code_erp
        $this->set_filter("code_erp","txtCodeErp",array("operator"=>"like","value"=>$this->get_post("txtCodeErp")));
        $oAuxField = new HelperInputText("txtCodeErp","txtCodeErp");
        $oAuxField->set_value($this->get_post("txtCodeErp"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtCodeErp",tr_fil_code_erp));
        $arFields[] = $oAuxWrapper;
        //id_type_validate
        $this->set_filter("id_type_validate","selIdTypeValidate",array("value"=>$this->get_post("selIdTypeValidate")));
        $oModelOrderArray = new ModelOrderArray();
        $arOptions = $oModelOrderArray->get_picklist_by_type("validate");
        $oAuxField = new HelperSelect($arOptions,"selIdTypeValidate","selIdTypeValidate");
        $oAuxField->set_value_to_select($this->get_post("selIdTypeValidate"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdTypeValidate",tr_fil_id_type_validate));
        $arFields[] = $oAuxWrapper;
        //id_type_payment
        $this->set_filter("id_type_payment","selIdTypePayment",array("value"=>$this->get_post("selIdTypePayment")));
        $oModelOrderArray = new ModelOrderArray();
        $arOptions = $oModelOrderArray->get_picklist_by_type("payment");
        $oAuxField = new HelperSelect($arOptions,"selIdTypePayment","selIdTypePayment");
        $oAuxField->set_value_to_select($this->get_post("selIdTypePayment"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdTypePayment",tr_fil_id_type_payment));
        $arFields[] = $oAuxWrapper;
        //id_seller
        $this->set_filter("id_seller","selIdSeller",array("value"=>$this->get_post("selIdSeller")));
        $oModelSeller = new ModelSeller();
        $arOptions = $oModelSeller->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdSeller","selIdSeller");
        $oAuxField->set_value_to_select($this->get_post("selIdSeller"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdSeller",tr_fil_id_seller));
        $arFields[] = $oAuxWrapper;
        //id_customer
        $this->set_filter("id_customer","selIdCustomer",array("value"=>$this->get_post("selIdCustomer")));
        $oModelCustomer = new ModelCustomer();
        $arOptions = $oModelCustomer->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdCustomer","selIdCustomer");
        $oAuxField->set_value_to_select($this->get_post("selIdCustomer"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdCustomer",tr_fil_id_customer));
        $arFields[] = $oAuxWrapper;
        //id_delivery_user
        $this->set_filter("id_delivery_user","selIdDeliveryUser",array("value"=>$this->get_post("selIdDeliveryUser")));
        $oModelSeller = new ModelSeller();
        $arOptions = $oModelSeller->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdDeliveryUser","selIdDeliveryUser");
        $oAuxField->set_value_to_select($this->get_post("selIdDeliveryUser"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdDeliveryUser",tr_fil_id_delivery_user));
        $arFields[] = $oAuxWrapper;
        //amount_subtotal
        $this->set_filter("amount_subtotal","txtAmountSubtotal",array("operator"=>"like","value"=>$this->get_post("txtAmountSubtotal")));
        $oAuxField = new HelperInputText("txtAmountSubtotal","txtAmountSubtotal");
        $oAuxField->set_value($this->get_post("txtAmountSubtotal"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtAmountSubtotal",tr_fil_amount_subtotal));
        $arFields[] = $oAuxWrapper;
        //amount_withtax
        $this->set_filter("amount_withtax","txtAmountWithtax",array("operator"=>"like","value"=>$this->get_post("txtAmountWithtax")));
        $oAuxField = new HelperInputText("txtAmountWithtax","txtAmountWithtax");
        $oAuxField->set_value($this->get_post("txtAmountWithtax"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtAmountWithtax",tr_fil_amount_withtax));
        $arFields[] = $oAuxWrapper;
        //amount_total
        $this->set_filter("amount_total","txtAmountTotal",array("operator"=>"like","value"=>$this->get_post("txtAmountTotal")));
        $oAuxField = new HelperInputText("txtAmountTotal","txtAmountTotal");
        $oAuxField->set_value($this->get_post("txtAmountTotal"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtAmountTotal",tr_fil_amount_total));
        $arFields[] = $oAuxWrapper;
        //description
        $this->set_filter("description","txtDescription",array("operator"=>"like","value"=>$this->get_post("txtDescription")));
        $oAuxField = new HelperInputText("txtDescription","txtDescription");
        $oAuxField->set_value($this->get_post("txtDescription"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtDescription",tr_fil_description));
        $arFields[] = $oAuxWrapper;
        //date
        $this->set_filter("date","datDate",array("operator"=>"like","value"=>$this->get_post("datDate")));
        $oAuxField = new HelperInputText("datDate","datDate");
        $oAuxField->set_value($this->get_post("datDate"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("datDate",tr_fil_date));
        $arFields[] = $oAuxWrapper;
        //delivery_address
        $this->set_filter("delivery_address","txtDeliveryAddress",array("operator"=>"like","value"=>$this->get_post("txtDeliveryAddress")));
        $oAuxField = new HelperInputText("txtDeliveryAddress","txtDeliveryAddress");
        $oAuxField->set_value($this->get_post("txtDeliveryAddress"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtDeliveryAddress",tr_fil_delivery_address));
        $arFields[] = $oAuxWrapper;
        //delivery_date
        $this->set_filter("delivery_date","datDeliveryDate",array("operator"=>"like","value"=>$this->get_post("datDeliveryDate")));
        $oAuxField = new HelperInputText("datDeliveryDate","datDeliveryDate");
        $oAuxField->set_value($this->get_post("datDeliveryDate"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("datDeliveryDate",tr_fil_delivery_date));
        $arFields[] = $oAuxWrapper;
        //delivery_hour
        $this->set_filter("delivery_hour","txtDeliveryHour",array("operator"=>"like","value"=>$this->get_post("txtDeliveryHour")));
        $oAuxField = new HelperInputText("txtDeliveryHour","txtDeliveryHour");
        $oAuxField->set_value($this->get_post("txtDeliveryHour"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtDeliveryHour",tr_fil_delivery_hour));
        $arFields[] = $oAuxWrapper;
        //is_payed
        $this->set_filter("is_payed","selIsPayed",array("operator"=>"like","value"=>$this->get_post("selIsPayed")));
        $oAuxField = new HelperInputText("selIsPayed","selIsPayed");
        $oAuxField->set_value($this->get_post("selIsPayed"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIsPayed",tr_fil_is_payed));
        $arFields[] = $oAuxWrapper;
        return $arFields;
    }//get_singleassign_filters()

    private function get_singleassign_columns()
    {
        $arColumns = array("id"=>tr_col_id,"code_erp"=>tr_col_code_erp,"id_type_validate"=>tr_col_id_type_validate,"id_type_payment"=>tr_col_id_type_payment,"id_seller"=>tr_col_id_seller,"id_customer"=>tr_col_id_customer,"id_delivery_user"=>tr_col_id_delivery_user,"amount_subtotal"=>tr_col_amount_subtotal,"amount_withtax"=>tr_col_amount_withtax,"amount_total"=>tr_col_amount_total,"date"=>tr_col_date,"delivery_address"=>tr_col_delivery_address,"delivery_date"=>tr_col_delivery_date,"delivery_hour"=>tr_col_delivery_hour,"is_payed"=>tr_col_is_payed);
        return $arColumns;
    }//get_singleassign_columns()

    public function singleassign()
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
        $arObjFilter = $this->get_singleassign_filters();
        $arColumns = $this->get_singleassign_columns(); 

        $oFilter = new ComponentFilter();
        $oFilter->set_fieldnames($this->get_filter_fieldnames());
        //Indica que no se guardara en sesion por nombre de campo sino por nombre de control
        //para esto es necesario respetar el estricto camelcase
        $oFilter->use_field_prefix();
        //Guarda en sesion y post los campos enviados, los de orden y página
        $oFilter->refresh();
        $this->oOrderHead = new ModelOrderHead();
        $this->oOrderHead->set_orderby($this->get_orderby());
        $this->oOrderHead->set_ordertype($this->get_ordertype());
        $this->oOrderHead->set_filters($this->get_filter_searchconfig());
        $arList = $this->oOrderHead->get_select_all_ids();
        $iRequestPage = $this->get_post("selPage");
        $oPage = new ComponentPage($arList,$iRequestPage);
        $arList = $oPage->get_items_to_show();
        $arList = $this->oOrderHead->get_select_all_by_ids($arList);
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

    //<editor-fold defaultstate="collapsed" desc="EXTRAS">
    public function addsellers()
    {
        $sUrl = $this->get_assign_backurl(array("k","k2"));
        if($this->get_get("close"))
                $this->js_colseme_and_parent_refresh();
        else
                $this->js_parent_refresh();
        $this->js_go_to($sUrl);
    }
    //</editor-fold>
    
    //<editor-fold defaultstate="collapsed" desc="TOPLIST">
    private function build_toplistoperation_buttons()
    {
        $arButTabs = array();
        //$arButTabs["filters"]=array("href"=>"javascript:reset_filters();","icon"=>"awe-magic","innerhtml"=>tr_clear_filters);
        //$arButTabs["reload"]=array("href"=>"javascript:TfwControl.form_submit();","icon"=>"awe-refresh","innerhtml"=>tr_refresh);
        //$arButTabs["insert"]=array("href"=>$this->build_url($this->sModuleName,NULL,"insert"),"icon"=>"awe-plus","innerhtml"=>tr_new);
        //$arButTabs["multidelete"]=array("href"=>"javascript:multi_delete();","icon"=>"awe-remove","innerhtml"=>tr_delete_selection);
        //$arButTabs["multiquarantine"]=array("href"=>"javascript:multi_quarantine();","icon"=>"awe-remove","innerhtml"=>tr_quarantine);
        //crea ventana
        //$arButTabs["multiassign"]=array("href"=>"javascript:multiassign_window('order_head',null,'multiassign','order_head','addexternaldata');","icon"=>"awe-external-link","innerhtml"=>tr_asign_selection);
        //$arButTabs["singleassign"]=array("href"=>"javascript:single_pick('order_head','singleassign','txtI','txtI');","icon"=>"awe-external-link","innerhtml"=>tr_asign_selection);
        $oModelCustomer = new ModelCustomer();
        $oModelCustomer->set_id($this->get_get("id_customer"));
        $oModelCustomer->load_by_id();
        //bug($oModelCustomer);
        $oOpButtons = new AppHelperButtontabs(tr_entities_of." ".$oModelCustomer->get_description());
        $oOpButtons->set_tabs($arButTabs);        
        return $oOpButtons;
    }//build_toplistoperation_buttons()

    private function get_toplist_columns()
    {
        $arColumns = array
        (
            "id"=>"Cod. Order"
            //,"code_erp"=>tr_col_code_erp
            ,"date"=>tr_col_date
            //,"id_type_payment"=>tr_col_id_type_payment
            //,"payment"=>tr_col_id_type_payment
            //,"id_seller"=>tr_col_id_seller
            //,"id_customer"=>tr_col_id_customer
            //,"customer"=>tr_col_id_customer
            //,"amount_subtotal"=>tr_col_amount_subtotal,"amount_withtax"=>tr_col_amount_withtax
            //,"amount_total"=>tr_col_amount_total
            ,"product"=>tr_col_product
            ,"num_items"=>tr_col_num_items
            //,"delivery_address"=>tr_col_delivery_address
            //,"delivery_date"=>tr_col_delivery_date
            //,"delivery_hour"=>tr_col_delivery_hour
            //,"is_payed"=>tr_col_is_payed
            //,"payed"=>tr_col_is_payed
            //,"id_type_validate"=>tr_col_id_type_validate
            ,"seller"=>tr_col_id_seller            
            ,"validate"=>tr_col_id_type_validate
            //,"id_delivery_user"=>tr_col_id_delivery_user
            //,"delivery_user"=>tr_col_id_delivery_user
            //,"url_lines"=>tr_info
        );
        return $arColumns;
    }//get_toplist_columns()

    public function get_toplist()
    {
        $this->oOrderHead = new ModelOrderHead();
        $this->oOrderHead->set_filters(array("id_customer"=>array("value"=>$this->get_get("id_customer"))));
        $this->oOrderHead->set_top(10);
        $arList = $this->oOrderHead->get_select_top10();
        //TABLE
        //This method adds objects controls to search list form
        $arColumns = $this->get_toplist_columns();
        $oTableList = new HelperTableTyped($arList,$arColumns);
        $oTableList->add_class("table table-striped table-bordered table-condensed");
        $arFormat = array("amount_total"=>"numeric2","date"=>"date","delivery_date"=>"date");
        $oTableList->set_format_columns($arFormat);
        //$oTableList->set_column_quarantine();
        //parametros a pasar al popup
        //$oTableList->set_multiassign(array("keys"=>array("k"=>1,"k2"=>2)));
        $oTableList->set_current_page(1);
//        $oTableList->set_next_page($oPage->get_next());
//        $oTableList->set_first_page($oPage->get_first());
//        $oTableList->set_last_page($oPage->get_last());
        $oTableList->set_total_regs(10);
//        $oTableList->set_total_pages($oPage->get_total());
        
        //CRUD OPERATIONS BAR
        $oOpButtons = $this->build_toplistoperation_buttons();
        $oJavascript = new HelperJavascript();
        $oJavascript->set_filters($this->get_filter_controls_id());
        //$oJavascript->set_focusid("id_all");
        $this->oView->set_layout("onecolumn");
        $this->oView->add_var($oJavascript,"oJavascript");
        $this->oView->add_var($oOpButtons,"oOpButtons");
        //$this->oView->add_var($oAlert,"oAlert");
        $this->oView->add_var($oTableList,"oTableList");
        $this->oView->show_page();
    }//get_toplist()
    //</editor-fold>
    
}//end controller
