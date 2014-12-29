<?php
/**
 * @author Module Builder 1.0.1
 * @link www.eduardoaf.com
 * @version 1.0.4
 * @name ControllerSeller
 * @file controller_seller.php    
 * @date 28-10-2014 10:19 (SPAIN)
 * @observations: 
 * @requires
 */
//TFW
import_component("page,validate,filter");
import_helper("form,form_fieldset,form_legend,input_text,label,anchor,table,table_typed");
import_helper("input_password,button_basic,raw,div,javascript");
//APP
import_model("user,seller");
import_appmain("controller,view,behaviour");
import_appbehaviour("picklist");
import_apphelper("listactionbar,controlgroup,formactions,buttontabs,formhead,alertdiv");

class ControllerSellers extends TheApplicationController
{
    public function __construct()
    {
        $this->sModuleName = "sellers";
        //console,pgpage,clientdevice,sesuser,aftersucces,currop  | log,session,permission,view
        parent::__construct($this->sModuleName);
    }

//<editor-fold defaultstate="collapsed" desc="LIST">
    private function build_listoperation_buttons()
    {
        $arButTabs = array();
        $arButTabs["filters"]=array("href"=>"javascript:reset_filters();","icon"=>"awe-magic","innerhtml"=>tr_clear_filters);
        $arButTabs["reload"]=array("href"=>"javascript:TfwControl.form_submit();","icon"=>"awe-refresh","innerhtml"=>tr_refresh);
        $arButTabs["insert"]=array("href"=>$this->build_url($this->sModuleName,NULL,"insert"),"icon"=>"awe-plus","innerhtml"=>tr_new);
        $arButTabs["multidelete"]=array("href"=>"javascript:multi_delete();","icon"=>"awe-remove","innerhtml"=>tr_delete_selection);
        //$arButTabs["multiquarantine"]=array("href"=>"javascript:multi_quarantine();","icon"=>"awe-remove","innerhtml"=>tr_quarantine);
        //crea ventana
        //$arButTabs["multiassign"]=array("href"=>"javascript:multiassign_window('seller',null,'multiassign','seller','addexternaldata');","icon"=>"awe-external-link","innerhtml"=>tr_asign_selection);
        //$arButTabs["singleassign"]=array("href"=>"javascript:single_pick('seller','singleassign','txtI','txtI');","icon"=>"awe-external-link","innerhtml"=>tr_asign_selection);
        return $arButTabs;
    }//build_listoperation_buttons()

    private function get_list_filters()
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
        //first_name
        $this->set_filter("first_name","txtFirstName",array("operator"=>"like","value"=>$this->get_post("txtFirstName")));
        $oAuxField = new HelperInputText("txtFirstName","txtFirstName");
        $oAuxField->set_value($this->get_post("txtFirstName"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtFirstName",tr_fil_first_name));
        $arFields[] = $oAuxWrapper;
        //last_name
        $this->set_filter("last_name","txtLastName",array("operator"=>"like","value"=>$this->get_post("txtLastName")));
        $oAuxField = new HelperInputText("txtLastName","txtLastName");
        $oAuxField->set_value($this->get_post("txtLastName"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtLastName",tr_fil_last_name));
        $arFields[] = $oAuxWrapper;
        //description
        $this->set_filter("description","txtDescription",array("operator"=>"like","value"=>$this->get_post("txtDescription")));
        $oAuxField = new HelperInputText("txtDescription","txtDescription");
        $oAuxField->set_value($this->get_post("txtDescription"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtDescription",tr_fil_description));
        $arFields[] = $oAuxWrapper;
        return $arFields;
    }//get_list_filters()

    private function get_list_columns()
    {
        $arColumns = array
        (
            "id"=>tr_col_id,"code_erp"=>tr_col_code_erp,"first_name"=>tr_col_first_name
            ,"last_name"=>tr_col_last_name
        );
        return $arColumns;
    }//get_list_columns()

    public function get_list()
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
        $arObjFilter = $this->get_list_filters();
        $arColumns = $this->get_list_columns(); 

        $oFilter = new ComponentFilter();
        $oFilter->set_fieldnames($this->get_filter_fieldnames());
        //Indica que no se guardara en sesion por nombre de campo sino por nombre de control
        //para esto es necesario respetar el estricto camelcase
        $oFilter->use_field_prefix();
        //Guarda en sesion y post los campos enviados, los de orden y página
        $oFilter->refresh();
        $oModelSeller = new ModelSeller();
        $oModelSeller->set_orderby($this->get_orderby());
        $oModelSeller->set_ordertype($this->get_ordertype());
        $oModelSeller->set_filters($this->get_filter_searchconfig());
        $arList = $oModelSeller->get_select_all_ids();
        $iRequestPage = $this->get_post("selPage");
        $oPage = new ComponentPage($arList,$iRequestPage);
        $arList = $oPage->get_items_to_show();
        $arList = $oModelSeller->get_select_all_by_ids($arList);
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
        $oTableList->set_column_pickmultiple();//checks column
        $oTableList->set_column_detail();//detail column
        //$oTableList->set_column_delete();
        $oTableList->set_column_quarantine();
        //parametros a pasar al popup
        //$oTableList->set_multiassign(array("keys"=>array("k"=>1,"k2"=>2)));
        $oTableList->set_current_page($oPage->get_current());
        $oTableList->set_next_page($oPage->get_next());
        $oTableList->set_first_page($oPage->get_first());
        $oTableList->set_last_page($oPage->get_last());
        $oTableList->set_total_regs($oPage->get_total_regs());
        $oTableList->set_total_pages($oPage->get_total());
        //CRUD OPERATIONS BAR
        $oOpButtons = new AppHelperButtontabs(tr_entities);
        $oOpButtons->set_tabs($this->build_listoperation_buttons());
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
    private function build_insert_opbuttons()
    {
            $arButTabs = array();
            $arButTabs["list"]=array("href"=>$this->build_url(),"icon"=>"awe-search","innerhtml"=>tr_list);
            return $arButTabs;
    }//build_insert_opbuttons()

    private function build_insert_fields($usePost=0)
    {
        $arFields = array(); $oAuxField = null; $oAuxLabel = NULL;
        $oModelSeller = new ModelSeller();
        $arFields[]= new AppHelperFormhead(tr_new.tr_entity);
//        //id
//        $oAuxField = new HelperInputText("txtId","txtId");
//        
//        $oAuxField->is_primarykey();
//        if($usePost) $oAuxField->set_value($this->get_post("txtId"));
//        //$oAuxField->readonly();
//        $oAuxLabel = new HelperLabel("txtId",tr_ins_id,"lblId");
//        $oAuxLabel->add_class("labelpk");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //code_erp
        $oAuxField = new HelperInputText("txtCodeErp","txtCodeErp");
        if($usePost) $oAuxField->set_value($this->get_post("txtCodeErp"));
        //$oAuxField->readonly();
        $oAuxLabel = new HelperLabel("txtCodeErp",tr_ins_code_erp,"lblCodeErp");
        $oAuxLabel->add_class("labelpk");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //first_name
        $oAuxField = new HelperInputText("txtFirstName","txtFirstName");
        if($usePost) $oAuxField->set_value($this->get_post("txtFirstName"));
        //$oAuxField->readonly();
        $oAuxLabel = new HelperLabel("txtFirstName",tr_ins_first_name,"lblFirstName");
        $oAuxLabel->add_class("labelpk");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //last_name
        $oAuxField = new HelperInputText("txtLastName","txtLastName");
        if($usePost) $oAuxField->set_value($this->get_post("txtLastName"));
        //$oAuxField->readonly();
        $oAuxLabel = new HelperLabel("txtLastName",tr_ins_last_name,"lblLastName");
        $oAuxLabel->add_class("labelpk");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //description
        $oAuxField = new HelperInputText("txtDescription","txtDescription");
        if($usePost) $oAuxField->set_value($this->get_post("txtDescription"));
        //$oAuxField->readonly();
        $oAuxLabel = new HelperLabel("txtDescription",tr_ins_description,"lblDescription");
        $oAuxLabel->add_class("labelpk");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        $oAuxField = new HelperButtonBasic("butSave",tr_save);
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

    private function build_insert_form($usePost=0)
    {
            $oForm = new HelperForm("frmInsert");
            $oForm->add_class("form-horizontal");
            $oForm->add_style("margin-bottom:0");
            $arFields = $this->build_insert_fields($usePost);
            $oForm->add_controls($arFields);
            return $oForm;
    }//build_update_form()

    public function insert()
    {
        //Validacion con PHP y JS
        $arFieldsConfig = array();
        //$arFieldsConfig["id"] = array("id"=>"txtId","label"=>tr_ins_id,"length"=>9,"type"=>array("numeric","required"));
        $arFieldsConfig["code_erp"] = array("id"=>"txtCodeErp","label"=>tr_ins_code_erp,"length"=>15,"type"=>array());
        $arFieldsConfig["first_name"] = array("id"=>"txtFirstName","label"=>tr_ins_first_name,"length"=>100,"type"=>array());
        $arFieldsConfig["last_name"] = array("id"=>"txtLastName","label"=>tr_ins_last_name,"length"=>100,"type"=>array());
        $arFieldsConfig["description"] = array("id"=>"txtDescription","label"=>tr_ins_description,"length"=>200,"type"=>array());
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
                $oModelSeller = new ModelSeller();
                $oModelSeller->set_attrib_value($arFieldsValues);
                $oModelSeller->set_insert_user($this->oSessionUser->get_id());;
                $oModelSeller->autoinsert();
                if($oModelSeller->is_error())
                {
                    $oAlert->set_type("e");
                    $oAlert->set_title(tr_data_not_saved);
                    $oAlert->set_content(tr_error_trying_to_save);
                }
                else
                {
                    $oAlert->set_title(tr_data_saved);
                    $this->reset_post();
                }
            }//no error
        }//fin if is_inserting (post action=save)
        //Si hay errores se recupera desde post
        if($arErrData) $oForm = $this->build_insert_form(1);
        else $oForm = $this->build_insert_form();
        $oJavascript = new HelperJavascript();
        $oJavascript->set_validate_config($arFieldsConfig);
        $oJavascript->set_focusid("id_all");
        $oOpButtons = new AppHelperButtontabs(tr_entities);
        $oOpButtons->set_tabs($this->build_insert_opbuttons());
        $this->oView->add_var($oOpButtons,"oOpButtons");
        $this->oView->add_var($oAlert,"oAlert");
        $this->oView->add_var($oForm,"oForm");
        $this->oView->add_var($oJavascript,"oJavascript");
        $this->oView->show_page();
    }//insert()
//</editor-fold>

//<editor-fold defaultstate="collapsed" desc="UPDATE">
    private function build_update_opbuttons()
    {
            $arButTabs = array();
            $arButTabs["list"]=array("href"=>$this->build_url(),"icon"=>"awe-search","innerhtml"=>tr_list);
            $arButTabs["insert"]=array("href"=>$this->build_url($this->sModuleName,NULL,"insert"),"icon"=>"awe-plus","innerhtml"=>tr_new);
            $arButTabs["delete"]=array("href"=>$this->build_url($this->sModuleName,NULL,"delete","id=".$this->get_get("id")),"icon"=>"awe-remove","innerhtml"=>tr_delete);
            return $arButTabs;
    }//build_update_opbuttons()

    private function build_update_fields($usePost=0)
    {
            $arFields = array(); $oAuxField = NULL; $oAuxLabel = NULL;
            $oModelSeller = new ModelSeller();
            $oModelSeller->set_id($this->get_get("id"));
            $oModelSeller->load_by_id();
            //info
            $sParams = "id=".$oModelSeller->get_id()."&code_erp=".$oModelSeller->get_code_erp();
            $sUrlDetail = $this->build_url($this->sModuleName,NULL,"update",$sParams);
            $arButTabs["detail"]=array("href"=>$sUrlDetail,"innerhtml"=>"Detail");
            $oAuxField = new AppHelperButtontabs(tr_detail);
            $oAuxField->no_border_bottom();
            $oAuxField->set_tabs($arButTabs);
            $oAuxField->set_active_tab("detail");
            $arFields[]=$oAuxField;
            $arFields[]= new AppHelperFormhead(tr_entity,$oModelSeller->get_id()." - ".$oModelSeller->get_description());
            //id
            $oAuxField = new HelperInputText("txtId","txtId",$oModelSeller->get_id());
            $oAuxField->add_class("input-small");
            $oAuxField->is_primarykey();
            $oAuxField->readonly();
            if($usePost) $oAuxField->set_value($this->get_post("txtId"));
            //$oAuxField->readonly();
            $oAuxLabel = new HelperLabel("txtId",tr_upd_id,"lblId");
            $oAuxLabel->add_class("labelpk");
            $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
            //code_erp
            $oAuxField = new HelperInputText("txtCodeErp","txtCodeErp",$oModelSeller->get_code_erp());
            if($usePost) $oAuxField->set_value($this->get_post("txtCodeErp"));
            //$oAuxField->readonly();
            $oAuxLabel = new HelperLabel("txtCodeErp",tr_upd_code_erp,"lblCodeErp");
            $oAuxLabel->add_class("labelpk");
            $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
            //first_name
            $oAuxField = new HelperInputText("txtFirstName","txtFirstName",$oModelSeller->get_first_name());
            if($usePost) $oAuxField->set_value($this->get_post("txtFirstName"));
            //$oAuxField->readonly();
            $oAuxLabel = new HelperLabel("txtFirstName",tr_upd_first_name,"lblFirstName");
            $oAuxLabel->add_class("labelpk");
            $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
            //last_name
            $oAuxField = new HelperInputText("txtLastName","txtLastName",$oModelSeller->get_last_name());
            if($usePost) $oAuxField->set_value($this->get_post("txtLastName"));
            //$oAuxField->readonly();
            $oAuxLabel = new HelperLabel("txtLastName",tr_upd_last_name,"lblLastName");
            $oAuxLabel->add_class("labelpk");
            $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
            //description
            $oAuxField = new HelperInputText("txtDescription","txtDescription",$oModelSeller->get_description());
            if($usePost) $oAuxField->set_value($this->get_post("txtDescription"));
            //$oAuxField->readonly();
            $oAuxLabel = new HelperLabel("txtDescription",tr_upd_description,"lblDescription");
            $oAuxLabel->add_class("labelpk");
            $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
            $oAuxField = new HelperButtonBasic("butSave",tr_save);
            $oAuxField->add_class("btn btn-primary");
            $oAuxField->set_js_onclick("update();");
            $arFields[] = new ApphelperFormactions(array($oAuxField));
            $sRegInfo = $this->get_audit_info($oModelSeller->get_insert_user(),$oModelSeller->get_insert_date()
            ,$oModelSeller->get_update_user(),$oModelSeller->get_update_date());
            $arFields[]= new AppHelperFormhead(null,$sRegInfo);
            //Accion
            $oAuxField = new HelperInputHidden("hidAction","hidAction");
            $arFields[] = $oAuxField;
            $oAuxField = new HelperInputHidden("hidPostback","hidPostback");
            $arFields[] = $oAuxField;            
            return $arFields;
    }//build_update_fields()

    private function build_update_form($usePost=0)
    {
            $id = $this->get_get("id");
            if($id)
            {
                    $oForm = new HelperForm("frmUpdate");
                    $oForm->add_class("form-horizontal");
                    $oForm->add_style("margin-bottom:0");
                    $arFields = $this->build_update_fields($usePost);
                    $oForm->add_controls($arFields);
            }//if(id)
            return $oForm;
    }//build_update_form()

    public function update()
    {
            //Validacion con PHP y JS
            $arFieldsConfig = array();
            $arFieldsConfig["id"] = array("id"=>"txtId","label"=>tr_upd_id,"length"=>9,"type"=>array("numeric","required"));
            $arFieldsConfig["code_erp"] = array("id"=>"txtCodeErp","label"=>tr_upd_code_erp,"length"=>15,"type"=>array());
            $arFieldsConfig["first_name"] = array("id"=>"txtFirstName","label"=>tr_upd_first_name,"length"=>100,"type"=>array());
            $arFieldsConfig["last_name"] = array("id"=>"txtLastName","label"=>tr_upd_last_name,"length"=>100,"type"=>array());
            $arFieldsConfig["description"] = array("id"=>"txtDescription","label"=>tr_upd_description,"length"=>200,"type"=>array());
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
                            $oModelSeller = new ModelSeller();
                            $oModelSeller->set_attrib_value($arFieldsValues);
                            $oModelSeller->set_update_user($this->oSessionUser->get_id());
                            $oModelSeller->autoupdate();
                            if($oModelSeller->is_error())
                            {
                                    $oAlert->set_type("e");
                                    $oAlert->set_title(tr_data_not_saved);
                                    $oAlert->set_content(tr_error_trying_to_save);
                            }//no error
                            else
                            {
                                    $oAlert->set_title(tr_data_saved);
                                    $this->reset_post();
                            }//error save
                    }//error validation
            }//is_updating()
            if($arErrData) $oForm = $this->build_update_form(1);
            else $oForm = $this->build_update_form(); 
            $oJavascript = new HelperJavascript();
            $oJavascript->set_updateaction();
            $oJavascript->set_validate_config($arFieldsConfig);
            $oJavascript->set_focusid("id_all");
            $oOpButtons = new AppHelperButtontabs(tr_entities);
            $oOpButtons->set_tabs($this->build_update_opbuttons());
            $this->oView->add_var($oOpButtons,"oOpButtons");
            $this->oView->add_var($oAlert,"oAlert");
            $this->oView->add_var($oForm,"oForm");
            $this->oView->add_var($oJavascript,"oJavascript");
            $this->oView->show_page();
    }//update()
//</editor-fold>

//<editor-fold defaultstate="collapsed" desc="DELETE">
    private function single_delete()
    {
            $id = $this->get_get("id");
            if($id)
            {
                    $oModelSeller = new ModelSeller();
                    $oModelSeller->set_id($id);
                    $oModelSeller->autodelete();
                    if($oModelSeller->is_error())
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

    private function multi_delete()
    {
            $isError = false;
            //Intenta recuperar pkeys sino pasa a recuperar el id. En ultimo caso lo que se ha pasado por parametro
            $arKeys = $this->get_listkeys();
            $oModelSeller = new ModelSeller();
            foreach($arKeys as $sKey)
            {
                    $id = $sKey;
                    $oModelSeller->set_id($id);
                    $oModelSeller->autodelete();
                    if($oModelSeller->is_error())
                    {
                            $isError = true;
                            $this->set_session_message(tr_error_trying_to_delete,"e");
                    }
            }
            if(!$isError)
                    $this->set_session_message(tr_data_deleted);
    }//multi_delete()

    public function delete()
    {
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
        $oModelSeller = new ModelSeller();
        foreach($arKeys as $sKey)
        {
            $id = $sKey;
            $oModelSeller->set_id($id);
            $oModelSeller->autoquarantine();
            if($oModelSeller->is_error())
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
                    $oModelSeller = new ModelSeller();
                    $oModelSeller->set_id($id);
                    $oModelSeller->autoquarantine();
                    if($oModelSeller->is_error())
                            $this->set_session_message(tr_error_trying_to_delete);
                    else
                            $this->set_session_message(tr_data_deleted);
            }//else no id
            else
                    $this->set_session_message(tr_error_key_not_supplied,"e");
    }//single_quarantine()

    public function quarantine()
    {
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
            //first_name
            $this->set_filter("first_name","txtFirstName",array("operator"=>"like","value"=>$this->get_post("txtFirstName")));
            $oAuxField = new HelperInputText("txtFirstName","txtFirstName");
            $oAuxField->set_value($this->get_post("txtFirstName"));
            $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtFirstName",tr_fil_first_name));
            $arFields[] = $oAuxWrapper;
            //last_name
            $this->set_filter("last_name","txtLastName",array("operator"=>"like","value"=>$this->get_post("txtLastName")));
            $oAuxField = new HelperInputText("txtLastName","txtLastName");
            $oAuxField->set_value($this->get_post("txtLastName"));
            $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtLastName",tr_fil_last_name));
            $arFields[] = $oAuxWrapper;
            //description
            $this->set_filter("description","txtDescription",array("operator"=>"like","value"=>$this->get_post("txtDescription")));
            $oAuxField = new HelperInputText("txtDescription","txtDescription");
            $oAuxField->set_value($this->get_post("txtDescription"));
            $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtDescription",tr_fil_description));
            $arFields[] = $oAuxWrapper;
            return $arFields;
    }//get_multiassign_filters()

    private function get_multiassign_columns()
    {
            $arColumns = array("id"=>tr_col_id,"code_erp"=>tr_col_code_erp,"first_name"=>tr_col_first_name,"last_name"=>tr_col_last_name);
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
            $oModelSeller = new ModelSeller();
            $oModelSeller->set_orderby($this->get_orderby());
            $oModelSeller->set_ordertype($this->get_ordertype());
            $oModelSeller->set_filters($this->get_filter_searchconfig());
            $arList = $oModelSeller->get_select_all_ids();
            $iRequestPage = $this->get_post("selPage");
            $oPage = new ComponentPage($arList,$iRequestPage);
            $arList = $oPage->get_items_to_show();
            $arList = $oModelSeller->get_select_all_by_ids($arList);
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
            //first_name
            $this->set_filter("first_name","txtFirstName",array("operator"=>"like","value"=>$this->get_post("txtFirstName")));
            $oAuxField = new HelperInputText("txtFirstName","txtFirstName");
            $oAuxField->set_value($this->get_post("txtFirstName"));
            $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtFirstName",tr_fil_first_name));
            $arFields[] = $oAuxWrapper;
            //last_name
            $this->set_filter("last_name","txtLastName",array("operator"=>"like","value"=>$this->get_post("txtLastName")));
            $oAuxField = new HelperInputText("txtLastName","txtLastName");
            $oAuxField->set_value($this->get_post("txtLastName"));
            $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtLastName",tr_fil_last_name));
            $arFields[] = $oAuxWrapper;
            //description
            $this->set_filter("description","txtDescription",array("operator"=>"like","value"=>$this->get_post("txtDescription")));
            $oAuxField = new HelperInputText("txtDescription","txtDescription");
            $oAuxField->set_value($this->get_post("txtDescription"));
            $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtDescription",tr_fil_description));
            $arFields[] = $oAuxWrapper;
            return $arFields;
    }//get_singleassign_filters()

    private function get_singleassign_columns()
    {
            $arColumns = array("id"=>tr_col_id,"code_erp"=>tr_col_code_erp,"first_name"=>tr_col_first_name,"last_name"=>tr_col_last_name);
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
            $oModelSeller = new ModelSeller();
            $oModelSeller->set_orderby($this->get_orderby());
            $oModelSeller->set_ordertype($this->get_ordertype());
            $oModelSeller->set_filters($this->get_filter_searchconfig());
            $arList = $oModelSeller->get_select_all_ids();
            $iRequestPage = $this->get_post("selPage");
            $oPage = new ComponentPage($arList,$iRequestPage);
            $arList = $oPage->get_items_to_show();
            $arList = $oModelSeller->get_select_all_by_ids($arList);
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
}//end controller