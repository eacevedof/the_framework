<?php
/**
* @author Module Builder 1.0.22
* @link www.eduardoaf.com
* @version 1.0.3
* @name ControllerProjects
* @file controller_projects.php   
* @date 30-10-2014 10:43 (SPAIN)
* @observations: 
* @requires:
*/
//TFW
import_component("page,validate,filter");
import_helper("form,form_fieldset,form_legend,input_text,label,anchor,table,table_typed");
import_helper("input_password,button_basic,raw,div,javascript");
//APP
import_model("user,project,project_type,project_array");
import_appmain("controller,view,behaviour");
import_appbehaviour("picklist");
import_apphelper("listactionbar,controlgroup,formactions,buttontabs,formhead,alertdiv,breadscrumbs,headertabs");

class ControllerProjects extends TheApplicationController
{
    protected $oProject;

    public function __construct()
    {
        $this->sModuleName = "projects";
        //console,pgpage,clientdevice,sesuser,aftersucces,currop  | log,session,permission,view
        parent::__construct($this->sModuleName);
        $this->oProject = new ModelProject();
        $this->oProject->set_platform($this->oSessionUser->get_platform());
        if($this->is_inget("id"))
        {
            $this->oProject->set_id($this->get_get("id"));
            $this->oProject->load_by_id();
        }
        //$this->oSessionUser->set_dataowner_table($this->oProject->get_table_name());
        //$this->oSessionUser->set_dataowner_tablefield("id_customer");
        //$this->oSessionUser->set_dataowner_keys(array("id"=>$this->oProject->get_id()));
    }

    //<editor-fold defaultstate="collapsed" desc="LIST">
    //list_1
    protected function build_list_scrumbs()
    {
        $arLinks = array();
        $sUrlLink = $this->build_url();
        $arLinks["list"]=array("href"=>$sUrlLink,"innerhtml"=>tr_pj_entities);
        $oScrumbs = new AppHelperBreadscrumbs($arLinks);
        return $oScrumbs;
    }

    //list_2
    protected function build_list_tabs()
    {
        $arTabs = array();
        //$sUrlTab = $this->build_url($this->sModuleName,NULL,"get_list","id=".$this->get_get("id_parent_foreign"));
        //$arTabs["list"]=array("href"=>$sUrlTab,"innerhtml"=>tr_pj_listtabs_1);
        //$sUrlTab = $this->build_url($this->sModuleName,NULL,"get_list_by_foreign","id_foreign=".$this->get_get("id_parent_foreign"));
        //$arTabs["listbyforeign"]=array("href"=>$sUrlTab,"innerhtml"=>tr_pj_listtabs_2);
        $oTabs = new AppHelperHeadertabs($arTabs,"list");
        return $oTabs;
    }

    //list_3
    protected function build_listoperation_buttons()
    {
        $arOpButtons = array();
        $arOpButtons["filters"]=array("href"=>"javascript:reset_filters();","icon"=>"awe-magic","innerhtml"=>tr_pj_listopbutton_filters);
        $arOpButtons["reload"]=array("href"=>"javascript:TfwControl.form_submit();","icon"=>"awe-refresh","innerhtml"=>tr_pj_listopbutton_reload);
        if($this->oPermission->is_insert())
            $arOpButtons["insert"]=array("href"=>$this->build_url($this->sModuleName,NULL,"insert"),"icon"=>"awe-plus","innerhtml"=>tr_pj_listopbutton_insert);
        if($this->oPermission->is_quarantine())
            $arOpButtons["multiquarantine"]=array("href"=>"javascript:multi_quarantine();","icon"=>"awe-remove","innerhtml"=>tr_pj_listopbutton_multiquarantine);
        //if($this->oPermission->is_delete())
            //$arOpButtons["multidelete"]=array("href"=>"javascript:multi_delete();","icon"=>"awe-remove","innerhtml"=>tr_pj_listopbutton_multidelete);
        //PICK WINDOWS
        //$arOpButtons["multiassign"]=array("href"=>"javascript:multiassign_window('projects',null,'multiassign','projects','addexternaldata');","icon"=>"awe-external-link","innerhtml"=>tr_pj_listopbutton_multiassign);
        //$arOpButtons["singleassign"]=array("href"=>"javascript:single_pick('projects','singleassign','txtI','txtI');","icon"=>"awe-external-link","innerhtml"=>tr_pj_listopbutton_singleassign);
        $oOpButtons = new AppHelperButtontabs(tr_pj_entities);
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
        //id_type
        $this->set_filter("id_type","selIdType");
        //id_type_priority
        $this->set_filter("id_type_priority","selIdTypePriority");
        //id_type_status
        $this->set_filter("id_type_status","selIdTypeStatus");
        //id_user_to
        $this->set_filter("id_user_to","selIdUserTo");
        //id_user_by
        $this->set_filter("id_user_by","selIdUserBy");
        //description
        //$this->set_filter("description","txtDescription",array("operator"=>"like"));
        //date_open
        $this->set_filter("date_open","txtDateOpen",array("operator"=>"like"));
        //date_close
        $this->set_filter("date_close","txtDateClose",array("operator"=>"like"));
        //notes_detail
        $this->set_filter("notes_detail","txtNotesDetail",array("operator"=>"like"));
    }//load_config_list_filters()

    //list_5
    protected function set_listfilters_from_post()
    {
        //id
        $this->set_filter_value("id",$this->get_post("txtId"));
        //code_erp
        //$this->set_filter_value("code_erp",$this->get_post("txtCodeErp"));
        //id_type
        $this->set_filter_value("id_type",$this->get_post("selIdType"));
        //id_type_priority
        $this->set_filter_value("id_type_priority",$this->get_post("selIdTypePriority"));
        //id_type_status
        $this->set_filter_value("id_type_status",$this->get_post("selIdTypeStatus"));
        //id_user_to
        $this->set_filter_value("id_user_to",$this->get_post("selIdUserTo"));
        //id_user_by
        $this->set_filter_value("id_user_by",$this->get_post("selIdUserBy"));
        //description
        //$this->set_filter_value("description",$this->get_post("txtDescription"));
        //date_open
        $this->set_filter_value("date_open",$this->get_post("txtDateOpen"));
        //date_close
        $this->set_filter_value("date_close",$this->get_post("txtDateClose"));
        //notes_detail
        $this->set_filter_value("notes_detail",$this->get_post("txtNotesDetail"));
    }//set_listfilters_from_post()

    //list_6
    protected function get_list_filters()
    {
        //CAMPOS
        $arFields = array();
        //id
        $oAuxField = new HelperInputText("txtId","txtId");
        $oAuxField->set_value($this->get_post("txtId"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtId",tr_pj_fil_id));
        $arFields[] = $oAuxWrapper;
        //code_erp
        //$oAuxField = new HelperInputText("txtCodeErp","txtCodeErp");
        //$oAuxField->set_value($this->get_post("txtCodeErp"));
        //$oAuxField->on_entersubmit();
        //$oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtCodeErp",tr_pj_fil_code_erp));
        //$arFields[] = $oAuxWrapper;
        //id_type
        $oType = new ModelProjectType();
        $arOptions = $oType->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdType","selIdType");
        $oAuxField->set_value_to_select($this->get_post("selIdType"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdType",tr_pj_fil_id_type));
        $arFields[] = $oAuxWrapper;
        //id_type_priority
        $oTypePriority = new ModelProjectArray();
        $arOptions = $oTypePriority->get_picklist_by_type("priority");
        $oAuxField = new HelperSelect($arOptions,"selIdTypePriority","selIdTypePriority");
        $oAuxField->set_value_to_select($this->get_post("selIdTypePriority"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdTypePriority",tr_pj_fil_id_type_priority));
        $arFields[] = $oAuxWrapper;
        //id_type_status
        $oTypeStatus = new ModelProjectArray();
        $arOptions = $oTypeStatus->get_picklist_by_type("status");
        $oAuxField = new HelperSelect($arOptions,"selIdTypeStatus","selIdTypeStatus");
        $oAuxField->set_value_to_select($this->get_post("selIdTypeStatus"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdTypeStatus",tr_pj_fil_id_type_status));
        $arFields[] = $oAuxWrapper;
        //id_user_to
        $oUserTo = new ModelUser();
        $arOptions = $oUserTo->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdUserTo","selIdUserTo");
        $oAuxField->set_value_to_select($this->get_post("selIdUserTo"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdUserTo",tr_pj_fil_id_user_to));
        $arFields[] = $oAuxWrapper;
        //id_user_by
        $oUserBy = new ModelUser();
        $arOptions = $oUserBy->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdUserBy","selIdUserBy");
        $oAuxField->set_value_to_select($this->get_post("selIdUserBy"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdUserBy",tr_pj_fil_id_user_by));
        $arFields[] = $oAuxWrapper;
        //description
        //$oAuxField = new HelperInputText("txtDescription","txtDescription");
        //$oAuxField->set_value($this->get_post("txtDescription"));
        //$oAuxField->on_entersubmit();
        //$oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtDescription",tr_pj_fil_description));
        //$arFields[] = $oAuxWrapper;
        //date_open
        $oAuxField = new HelperInputText("txtDateOpen","txtDateOpen");
        $oAuxField->set_value($this->get_post("txtDateOpen"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtDateOpen",tr_pj_fil_date_open));
        $arFields[] = $oAuxWrapper;
        //date_close
        $oAuxField = new HelperInputText("txtDateClose","txtDateClose");
        $oAuxField->set_value($this->get_post("txtDateClose"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtDateClose",tr_pj_fil_date_close));
        $arFields[] = $oAuxWrapper;
        //notes_detail
        $oAuxField = new HelperInputText("txtNotesDetail","txtNotesDetail");
        $oAuxField->set_value($this->get_post("txtNotesDetail"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtNotesDetail",tr_pj_fil_notes_detail));
        $arFields[] = $oAuxWrapper;
        return $arFields;
    }//get_list_filters()

    //list_7
    protected function get_list_columns()
    {
        $arColumns["id"] = tr_pj_col_id;
        //$arColumns["code_erp"] = tr_pj_col_code_erp;
        //$arColumns["id_type"] = tr_pj_col_id_type;
        $arColumns["subtype"] = tr_pj_col_id_type;
        //$arColumns["id_type_priority"] = tr_pj_col_id_type_priority;
        $arColumns["priority"] = tr_pj_col_id_type_priority;
        //$arColumns["id_type_status"] = tr_pj_col_id_type_status;
        $arColumns["status"] = tr_pj_col_id_type_status;
        //$arColumns["id_user_to"] = tr_pj_col_id_user_to;
        $arColumns["userto"] = tr_pj_col_id_user_to;
        //$arColumns["id_user_by"] = tr_pj_col_id_user_by;
        $arColumns["userby"] = tr_pj_col_id_user_by;
        //$arColumns["description"] = tr_pj_col_description;
        $arColumns["date_open"] = tr_pj_col_date_open;
        $arColumns["date_close"] = tr_pj_col_date_close;
        $arColumns["notes_detail"] = tr_pj_col_notes_detail;
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
        $this->oProject->set_orderby($this->get_orderby());
        $this->oProject->set_ordertype($this->get_ordertype());
        $this->oProject->set_filters($this->get_filter_searchconfig());
        //hierarchy recover
        //$this->oProject->set_select_user($this->oSessionUser->get_id());
        $arList = $this->oProject->get_select_all_ids();
        $iRequestPage = $this->get_post("selPage");
        $oPage = new ComponentPage($arList,$iRequestPage);
        $arList = $oPage->get_items_to_show();
        $arList = $this->oProject->get_select_all_by_ids($arList);
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
        //("href"=>"url_lines","innerhtml"=>tr_pj_order_lines,"class"=>"btn btn-info","icon"=>"awe-info-sign")));
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
        $arLinks["list"]=array("href"=>$sUrlLink,"innerhtml"=>tr_pj_entities);
        $sUrlLink = $this->build_url($this->sModuleName,NULL,"insert");
        $arLinks["insert"]=array("href"=>$sUrlLink,"innerhtml"=>tr_pj_entity_insert);
        $oScrumbs = new AppHelperBreadscrumbs($arLinks);
        return $oScrumbs;
    }//build_insert_scrumbs()

    //insert_2
    protected function build_insert_tabs()
    {
        $arTabs = array();
        //$sUrlTab = $this->build_url($this->sModuleName,NULL,"insert");
        //$arTabs["insert1"]=array("href"=>$sUrlTab,"innerhtml"=>tr_pj_instabs_1);
        //$sUrlTab = $this->build_url($this->sModuleName,NULL,"insert");
        //$arTabs["insert2"]=array("href"=>$sUrlTab,"innerhtml"=>tr_pj_instabs_2);
        $oTabs = new AppHelperHeadertabs($arTabs,"insert1");
        return $oTabs;
    }//build_insert_tabs()
    //insert_3
    protected function build_insert_opbuttons()
    {
        $arOpButtons = array();
        $arOpButtons["list"] = array("href"=>$this->build_url(),"icon"=>"awe-search","innerhtml"=>tr_pj_insopbutton_list);
        //$arOpButtons["extra"] = array("href"=>$this->build_url(),"icon"=>"awe-xxxx","innerhtml"=>tr_pj_insopbutton_extra1);
        $oOpButtons = new AppHelperButtontabs(tr_pj_entities);
        $oOpButtons->set_tabs($arOpButtons);
        return $oOpButtons;
    }//build_insert_opbuttons()

    //insert_4
    protected function build_insert_fields($usePost=0)
    {
        $arFields = array(); $oAuxField = NULL; $oAuxLabel = NULL;
        $arFields[]= new AppHelperFormhead(tr_pj_entity_new);
        //id
        //$oAuxField = new HelperInputText("txtId","txtId");
        //$oAuxField->is_primarykey();
        //if($usePost) $oAuxField->set_value($this->get_post("txtId"));
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        //$arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //code_erp
        //$oAuxField = new HelperInputText("txtCodeErp","txtCodeErp");
        //if($usePost) $oAuxField->set_value($this->get_post("txtCodeErp"));
        //$oAuxLabel = new HelperLabel("txtCodeErp",tr_pj_ins_code_erp,"lblCodeErp");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        //$arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //id_type
        $oType = new ModelProjectType();
        $arOptions = $oType->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdType","selIdType");
        $oAuxField->set_value_to_select($this->get_post("selIdType"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdType",tr_pj_ins_id_type));
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdType"));
        $oAuxLabel = new HelperLabel("selIdType",tr_pj_ins_id_type,"lblIdType");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //id_type_priority
        $oTypePriority = new ModelProjectArray();
        $arOptions = $oTypePriority->get_picklist_by_type("priority");
        $oAuxField = new HelperSelect($arOptions,"selIdTypePriority","selIdTypePriority");
        $oAuxField->set_value_to_select($this->get_post("selIdTypePriority"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdTypePriority",tr_pj_ins_id_type_priority));
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdTypePriority"));
        $oAuxLabel = new HelperLabel("selIdTypePriority",tr_pj_ins_id_type_priority,"lblIdTypePriority");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //id_type_status
        $oTypeStatus = new ModelProjectArray();
        $arOptions = $oTypeStatus->get_picklist_by_type("status");
        $oAuxField = new HelperSelect($arOptions,"selIdTypeStatus","selIdTypeStatus");
        $oAuxField->set_value_to_select($this->get_post("selIdTypeStatus"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdTypeStatus",tr_pj_ins_id_type_status));
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdTypeStatus"));
        $oAuxLabel = new HelperLabel("selIdTypeStatus",tr_pj_ins_id_type_status,"lblIdTypeStatus");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //id_user_to
        $oUserTo = new ModelUser();
        $arOptions = $oUserTo->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdUserTo","selIdUserTo");
        $oAuxField->set_value_to_select($this->get_post("selIdUserTo"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdUserTo",tr_pj_ins_id_user_to));
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdUserTo"));
        $oAuxLabel = new HelperLabel("selIdUserTo",tr_pj_ins_id_user_to,"lblIdUserTo");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //id_user_by
        $oUserBy = new ModelUser();
        $arOptions = $oUserBy->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdUserBy","selIdUserBy");
        $oAuxField->set_value_to_select($this->get_post("selIdUserBy"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdUserBy",tr_pj_ins_id_user_by));
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdUserBy"));
        $oAuxLabel = new HelperLabel("selIdUserBy",tr_pj_ins_id_user_by,"lblIdUserBy");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //description
        //$oAuxField = new HelperInputText("txtDescription","txtDescription");
        //if($usePost) $oAuxField->set_value($this->get_post("txtDescription"));
        //$oAuxLabel = new HelperLabel("txtDescription",tr_pj_ins_description,"lblDescription");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        //$arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //date_open
        $oAuxField = new HelperInputText("txtDateOpen","txtDateOpen");
        if($usePost) $oAuxField->set_value($this->get_post("txtDateOpen"));
        $oAuxLabel = new HelperLabel("txtDateOpen",tr_pj_ins_date_open,"lblDateOpen");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //date_close
        $oAuxField = new HelperInputText("txtDateClose","txtDateClose");
        if($usePost) $oAuxField->set_value($this->get_post("txtDateClose"));
        $oAuxLabel = new HelperLabel("txtDateClose",tr_pj_ins_date_close,"lblDateClose");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //notes_detail
        $oAuxField = new HelperInputText("txtNotesDetail","txtNotesDetail");
        if($usePost) $oAuxField->set_value($this->get_post("txtNotesDetail"));
        $oAuxLabel = new HelperLabel("txtNotesDetail",tr_pj_ins_notes_detail,"lblNotesDetail");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //SAVE BUTTON
        $oAuxField = new HelperButtonBasic("butSave",tr_pj_ins_savebutton);
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
        //$arFieldsConfig["id"] = array("controlid"=>"txtId","label"=>tr_pj_ins_id,"length"=>9,"type"=>array("numeric","required"));
        //$arFieldsConfig["code_erp"] = array("controlid"=>"txtCodeErp","label"=>tr_pj_ins_code_erp,"length"=>25,"type"=>array());
        //$arFieldsConfig["id_type"] = array("controlid"=>"selIdType","label"=>tr_pj_ins_id_type,"length"=>4,"type"=>array());
        //$arFieldsConfig["id_type_priority"] = array("controlid"=>"selIdTypePriority","label"=>tr_pj_ins_id_type_priority,"length"=>4,"type"=>array());
        //$arFieldsConfig["id_type_status"] = array("controlid"=>"selIdTypeStatus","label"=>tr_pj_ins_id_type_status,"length"=>4,"type"=>array());
        //$arFieldsConfig["id_user_to"] = array("controlid"=>"selIdUserTo","label"=>tr_pj_ins_id_user_to,"length"=>9,"type"=>array());
        //$arFieldsConfig["id_user_by"] = array("controlid"=>"selIdUserBy","label"=>tr_pj_ins_id_user_by,"length"=>9,"type"=>array());
        //$arFieldsConfig["description"] = array("controlid"=>"txtDescription","label"=>tr_pj_ins_description,"length"=>200,"type"=>array());
        $arFieldsConfig["date_open"] = array("controlid"=>"txtDateOpen","label"=>tr_pj_ins_date_open,"length"=>8,"type"=>array());
        $arFieldsConfig["date_close"] = array("controlid"=>"txtDateClose","label"=>tr_pj_ins_date_close,"length"=>8,"type"=>array());
        $arFieldsConfig["notes_detail"] = array("controlid"=>"txtNotesDetail","label"=>tr_pj_ins_notes_detail,"length"=>250,"type"=>array());
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
                    //$this->oProject->log_save_insert();
                    $this->oProject->set_attrib_value($arFieldsValues);
                    $this->oProject->set_insert_user($this->oSessionUser->get_id());
                    //$this->oProject->set_platform($this->oSessionUser->get_platform());
                    $this->oProject->autoinsert();
                    if($this->oProject->is_error())
                    {
                        $oAlert->set_type("e");
                        $oAlert->set_title(tr_data_not_saved);
                        $oAlert->set_content(tr_error_trying_to_save);
                    }
                    else//insert ok
                    {
                        $this->set_get("id",$this->oProject->get_last_insert_id());
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
        $sUrlLink = $this->build_url();
        $arLinks["list"]=array("href"=>$sUrlLink,"innerhtml"=>tr_pj_entities);
        $sUrlLink = $this->build_url($this->sModuleName,NULL,"update","id=".$this->get_get("id"));
        $arLinks["detail"]=array("href"=>$sUrlLink,"innerhtml"=>tr_pj_entity.": ".$this->oProject->get_id()." - ".$this->oProject->get_description());
        $oScrumbs = new AppHelperBreadscrumbs($arLinks);
        return $oScrumbs;
    }//build_update_scrumbs()

    //update_2
    protected function build_update_tabs()
    {
        $arTabs = array();
        $sUrlTab = $this->build_url($this->sModuleName,NULL,"update","id=".$this->get_get("id"));
        $arTabs["detail"]=array("href"=>$sUrlTab,"innerhtml"=>tr_pj_updtabs_detail);
        $sUrlTab = $this->build_url($this->sModuleName,"tasks","get_list","id_project=".$this->get_get("id"));
        $arTabs["tasks"]=array("href"=>$sUrlTab,"innerhtml"=>tr_pj_updtabs_foreigndata);
        $oTabs = new AppHelperHeadertabs($arTabs,"detail");
        return $oTabs;
    }//build_update_tabs()

    //update_3
    protected function build_update_opbuttons()
    {
        $arOpButtons = array();
        if($this->oPermission->is_select())
            $arOpButtons["list"]=array("href"=>$this->build_url(),"icon"=>"awe-search","innerhtml"=>tr_pj_updopbutton_list);
        //if($this->oPermission->is_insert())
            //$arOpButtons["insert"]=array("href"=>$this->build_url($this->sModuleName,NULL,"insert"),"icon"=>"awe-plus","innerhtml"=>tr_pj_updopbutton_insert);
        if($this->oPermission->is_quarantine())
            $arOpButtons["delete"]=array("href"=>$this->build_url($this->sModuleName,NULL,"quarantine","id=".$this->get_get("id")),"icon"=>"awe-remove","innerhtml"=>tr_pj_updopbutton_quarantine);
        //if($this->oPermission->is_delete())
            //$arOpButtons["delete"]=array("href"=>$this->build_url($this->sModuleName,NULL,"delete","id=".$this->get_get("id")),"icon"=>"awe-remove","innerhtml"=>tr_pj_updopbutton_delete);
        $oOpButtons = new AppHelperButtontabs(tr_pj_entities);
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
        $oAuxField->set_value(dbbo_numeric2($this->oProject->get_id()));
        if($usePost) $oAuxField->set_value(dbbo_numeric2($this->get_post("txtId")));
        $oAuxLabel = new HelperLabel("txtId",tr_pj_upd_id,"lblId");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //code_erp
        //$oAuxField = new HelperInputText("txtCodeErp","txtCodeErp");
        //$oAuxField->set_value($this->oProject->get_code_erp());
        //if($usePost) $oAuxField->set_value($this->get_post("txtCodeErp"));
        //$oAuxLabel = new HelperLabel("txtCodeErp",tr_pj_upd_code_erp,"lblCodeErp");
        //$oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        //$arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //id_type
        $oType = new ModelProjectType();
        $arOptions = $oType->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdType","selIdType");
        $oAuxField->set_value_to_select($this->get_post("selIdType"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdType",tr_pj_upd_id_type));
        $oAuxField->set_value_to_select($this->oProject->get_id_type());
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdType"));
        $oAuxLabel = new HelperLabel("selIdType",tr_pj_upd_id_type,"lblIdType");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //id_type_priority
        $oTypePriority = new ModelProjectArray();
        $arOptions = $oTypePriority->get_picklist_by_type("priority");
        $oAuxField = new HelperSelect($arOptions,"selIdTypePriority","selIdTypePriority");
        $oAuxField->set_value_to_select($this->get_post("selIdTypePriority"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdTypePriority",tr_pj_upd_id_type_priority));
        $oAuxField->set_value_to_select($this->oProject->get_id_type_priority());
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdTypePriority"));
        $oAuxLabel = new HelperLabel("selIdTypePriority",tr_pj_upd_id_type_priority,"lblIdTypePriority");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //id_type_status
        $oTypeStatus = new ModelProjectArray();
        $arOptions = $oTypeStatus->get_picklist_by_type("status");
        $oAuxField = new HelperSelect($arOptions,"selIdTypeStatus","selIdTypeStatus");
        $oAuxField->set_value_to_select($this->get_post("selIdTypeStatus"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdTypeStatus",tr_pj_upd_id_type_status));
        $oAuxField->set_value_to_select($this->oProject->get_id_type_status());
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdTypeStatus"));
        $oAuxLabel = new HelperLabel("selIdTypeStatus",tr_pj_upd_id_type_status,"lblIdTypeStatus");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //id_user_to
        $oUserTo = new ModelUser();
        $arOptions = $oUserTo->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdUserTo","selIdUserTo");
        $oAuxField->set_value_to_select($this->get_post("selIdUserTo"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdUserTo",tr_pj_upd_id_user_to));
        $oAuxField->set_value_to_select($this->oProject->get_id_user_to());
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdUserTo"));
        $oAuxLabel = new HelperLabel("selIdUserTo",tr_pj_upd_id_user_to,"lblIdUserTo");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //id_user_by
        $oUserBy = new ModelUser();
        $arOptions = $oUserBy->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdUserBy","selIdUserBy");
        $oAuxField->set_value_to_select($this->get_post("selIdUserBy"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdUserBy",tr_pj_upd_id_user_by));
        $oAuxField->set_value_to_select($this->oProject->get_id_user_by());
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdUserBy"));
        $oAuxLabel = new HelperLabel("selIdUserBy",tr_pj_upd_id_user_by,"lblIdUserBy");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //description
        //$oAuxField = new HelperInputText("txtDescription","txtDescription");
        //$oAuxField->set_value($this->oProject->get_description());
        //if($usePost) $oAuxField->set_value($this->get_post("txtDescription"));
        //$oAuxLabel = new HelperLabel("txtDescription",tr_pj_upd_description,"lblDescription");
        //$oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        //$arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //date_open
        $oAuxField = new HelperInputText("txtDateOpen","txtDateOpen");
        $oAuxField->set_value($this->oProject->get_date_open());
        if($usePost) $oAuxField->set_value($this->get_post("txtDateOpen"));
        $oAuxLabel = new HelperLabel("txtDateOpen",tr_pj_upd_date_open,"lblDateOpen");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //date_close
        $oAuxField = new HelperInputText("txtDateClose","txtDateClose");
        $oAuxField->set_value($this->oProject->get_date_close());
        if($usePost) $oAuxField->set_value($this->get_post("txtDateClose"));
        $oAuxLabel = new HelperLabel("txtDateClose",tr_pj_upd_date_close,"lblDateClose");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //notes_detail
        $oAuxField = new HelperInputText("txtNotesDetail","txtNotesDetail");
        $oAuxField->set_value($this->oProject->get_notes_detail());
        if($usePost) $oAuxField->set_value($this->get_post("txtNotesDetail"));
        $oAuxLabel = new HelperLabel("txtNotesDetail",tr_pj_upd_notes_detail,"lblNotesDetail");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //BUTTON SAVE
        $oAuxField = new HelperButtonBasic("butSave",tr_pj_upd_savebutton);
        $oAuxField->add_class("btn btn-primary");
        $oAuxField->set_js_onclick("update();");
        if($this->oPermission->is_update())
            $arFields[] = new ApphelperFormactions(array($oAuxField));
        //AUDIT INFO
        $sRegInfo = $this->get_audit_info($this->oProject->get_insert_user(),$this->oProject->get_insert_date()
        ,$this->oProject->get_update_user(),$this->oProject->get_update_date());
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
        $arFieldsConfig["id"] = array("controlid"=>"txtId","label"=>tr_pj_upd_id,"length"=>9,"type"=>array("numeric","required"));
        //$arFieldsConfig["code_erp"] = array("controlid"=>"txtCodeErp","label"=>tr_pj_upd_code_erp,"length"=>25,"type"=>array());
        //$arFieldsConfig["id_type"] = array("controlid"=>"selIdType","label"=>tr_pj_upd_id_type,"length"=>4,"type"=>array());
        //$arFieldsConfig["id_type_priority"] = array("controlid"=>"selIdTypePriority","label"=>tr_pj_upd_id_type_priority,"length"=>4,"type"=>array());
        //$arFieldsConfig["id_type_status"] = array("controlid"=>"selIdTypeStatus","label"=>tr_pj_upd_id_type_status,"length"=>4,"type"=>array());
        //$arFieldsConfig["id_user_to"] = array("controlid"=>"selIdUserTo","label"=>tr_pj_upd_id_user_to,"length"=>9,"type"=>array());
        //$arFieldsConfig["id_user_by"] = array("controlid"=>"selIdUserBy","label"=>tr_pj_upd_id_user_by,"length"=>9,"type"=>array());
        //$arFieldsConfig["description"] = array("controlid"=>"txtDescription","label"=>tr_pj_upd_description,"length"=>200,"type"=>array());
        $arFieldsConfig["date_open"] = array("controlid"=>"txtDateOpen","label"=>tr_pj_upd_date_open,"length"=>8,"type"=>array());
        $arFieldsConfig["date_close"] = array("controlid"=>"txtDateClose","label"=>tr_pj_upd_date_close,"length"=>8,"type"=>array());
        $arFieldsConfig["notes_detail"] = array("controlid"=>"txtNotesDetail","label"=>tr_pj_upd_notes_detail,"length"=>250,"type"=>array());
        return $arFieldsConfig;
    }//get_update_validate

    //update_6
    protected function build_update_form($usePost=0)
    {
        $id = $this->oProject->get_id();
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
                    $this->oProject->set_attrib_value($arFieldsValues);
                    //$this->oProject->set_description($oProject->get_field1()." ".$oProject->get_field2());
                    $this->oProject->set_update_user($this->oSessionUser->get_id());
                    $this->oProject->autoupdate();
                    if($this->oProject->is_error())
                    {
                        $oAlert->set_type("e");
                        $oAlert->set_title(tr_data_not_saved);
                        $oAlert->set_content(tr_error_trying_to_save);
                    }//no error
                    else//update ok
                    {
                        //$this->oProject->load_by_id();
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
            $this->oProject->set_id($id);
            $this->oProject->autodelete();
            if($this->oProject->is_error())
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
            $this->oProject->set_id($id);
            $this->oProject->autodelete();
            if($this->oProject->is_error())
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
            $this->oProject->set_id($id);
            $this->oProject->autoquarantine();
            if($this->oProject->is_error())
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
            $this->oProject->set_id($id);
            $this->oProject->autoquarantine();
            if($this->oProject->is_error())
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
        $arOpButtons["filters"]=array("href"=>"javascript:reset_filters();","icon"=>"awe-magic","innerhtml"=>tr_pj_clear_filters);
        $arOpButtons["reload"]=array("href"=>"javascript:TfwControl.form_submit();","icon"=>"awe-refresh","innerhtml"=>tr_pj_refresh);
        $arOpButtons["multiadd"]=array("href"=>"javascript:multiadd();","icon"=>"awe-external-link","innerhtml"=>tr_pj_multiadd);
        $arOpButtons["closeme"]=array("href"=>"javascript:closeme();","icon"=>"awe-remove-sign","innerhtml"=>tr_pj_closeme);
        $oOpButtons = new AppHelperButtontabs(tr_pj_entities);
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
        //id_type
        $this->set_filter("id_type","selIdType");
        //id_type_priority
        $this->set_filter("id_type_priority","selIdTypePriority");
        //id_type_status
        $this->set_filter("id_type_status","selIdTypeStatus");
        //id_user_to
        $this->set_filter("id_user_to","selIdUserTo");
        //id_user_by
        $this->set_filter("id_user_by","selIdUserBy");
        //description
        //$this->set_filter("description","txtDescription",array("operator"=>"like"));
        //date_open
        $this->set_filter("date_open","txtDateOpen",array("operator"=>"like"));
        //date_close
        $this->set_filter("date_close","txtDateClose",array("operator"=>"like"));
        //notes_detail
        $this->set_filter("notes_detail","txtNotesDetail",array("operator"=>"like"));
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
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtId",tr_pj_fil_id));
        $arFields[] = $oAuxWrapper;
        //code_erp
        //$oAuxField = new HelperInputText("txtCodeErp","txtCodeErp");
        //$oAuxField->set_value($this->get_post("txtCodeErp"));
        //$oAuxField->on_entersubmit();
        //$oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtCodeErp",tr_pj_fil_code_erp));
        //$arFields[] = $oAuxWrapper;
        //id_type
        $oType = new ModelProjectType();
        $arOptions = $oType->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdType","selIdType");
        $oAuxField->set_value_to_select($this->get_post("selIdType"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdType",tr_pj_fil_id_type));
        $arFields[] = $oAuxWrapper;
        //id_type_priority
        $oTypePriority = new ModelProjectArray();
        $arOptions = $oTypePriority->get_picklist_by_type("priority");
        $oAuxField = new HelperSelect($arOptions,"selIdTypePriority","selIdTypePriority");
        $oAuxField->set_value_to_select($this->get_post("selIdTypePriority"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdTypePriority",tr_pj_fil_id_type_priority));
        $arFields[] = $oAuxWrapper;
        //id_type_status
        $oTypeStatus = new ModelProjectArray();
        $arOptions = $oTypeStatus->get_picklist_by_type("status");
        $oAuxField = new HelperSelect($arOptions,"selIdTypeStatus","selIdTypeStatus");
        $oAuxField->set_value_to_select($this->get_post("selIdTypeStatus"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdTypeStatus",tr_pj_fil_id_type_status));
        $arFields[] = $oAuxWrapper;
        //id_user_to
        $oUserTo = new ModelUser();
        $arOptions = $oUserTo->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdUserTo","selIdUserTo");
        $oAuxField->set_value_to_select($this->get_post("selIdUserTo"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdUserTo",tr_pj_fil_id_user_to));
        $arFields[] = $oAuxWrapper;
        //id_user_by
        $oUserBy = new ModelUser();
        $arOptions = $oUserBy->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdUserBy","selIdUserBy");
        $oAuxField->set_value_to_select($this->get_post("selIdUserBy"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdUserBy",tr_pj_fil_id_user_by));
        $arFields[] = $oAuxWrapper;
        //description
        //$oAuxField = new HelperInputText("txtDescription","txtDescription");
        //$oAuxField->set_value($this->get_post("txtDescription"));
        //$oAuxField->on_entersubmit();
        //$oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtDescription",tr_pj_fil_description));
        //$arFields[] = $oAuxWrapper;
        //date_open
        $oAuxField = new HelperInputText("txtDateOpen","txtDateOpen");
        $oAuxField->set_value($this->get_post("txtDateOpen"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtDateOpen",tr_pj_fil_date_open));
        $arFields[] = $oAuxWrapper;
        //date_close
        $oAuxField = new HelperInputText("txtDateClose","txtDateClose");
        $oAuxField->set_value($this->get_post("txtDateClose"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtDateClose",tr_pj_fil_date_close));
        $arFields[] = $oAuxWrapper;
        //notes_detail
        $oAuxField = new HelperInputText("txtNotesDetail","txtNotesDetail");
        $oAuxField->set_value($this->get_post("txtNotesDetail"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtNotesDetail",tr_pj_fil_notes_detail));
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
        //id_type
        $this->set_filter_value("id_type",$this->get_post("selIdType"));
        //id_type_priority
        $this->set_filter_value("id_type_priority",$this->get_post("selIdTypePriority"));
        //id_type_status
        $this->set_filter_value("id_type_status",$this->get_post("selIdTypeStatus"));
        //id_user_to
        $this->set_filter_value("id_user_to",$this->get_post("selIdUserTo"));
        //id_user_by
        $this->set_filter_value("id_user_by",$this->get_post("selIdUserBy"));
        //description
        //$this->set_filter_value("description",$this->get_post("txtDescription"));
        //date_open
        $this->set_filter_value("date_open",$this->get_post("txtDateOpen"));
        //date_close
        $this->set_filter_value("date_close",$this->get_post("txtDateClose"));
        //notes_detail
        $this->set_filter_value("notes_detail",$this->get_post("txtNotesDetail"));
    }//set_multiassignfilters_from_post()

    //multiassign_5
    protected function get_multiassign_columns()
    {
        $arColumns["id"] = tr_pj_col_id;
        //$arColumns["code_erp"] = tr_pj_col_code_erp;
        //$arColumns["id_type"] = tr_pj_col_id_type;
        $arColumns["subtype"] = tr_pj_col_id_type;
        //$arColumns["id_type_priority"] = tr_pj_col_id_type_priority;
        $arColumns["priority"] = tr_pj_col_id_type_priority;
        //$arColumns["id_type_status"] = tr_pj_col_id_type_status;
        $arColumns["status"] = tr_pj_col_id_type_status;
        //$arColumns["id_user_to"] = tr_pj_col_id_user_to;
        $arColumns["userto"] = tr_pj_col_id_user_to;
        //$arColumns["id_user_by"] = tr_pj_col_id_user_by;
        $arColumns["userby"] = tr_pj_col_id_user_by;
        //$arColumns["description"] = tr_pj_col_description;
        $arColumns["date_open"] = tr_pj_col_date_open;
        $arColumns["date_close"] = tr_pj_col_date_close;
        $arColumns["notes_detail"] = tr_pj_col_notes_detail;
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
        $this->oProject->set_orderby($this->get_orderby());
        $this->oProject->set_ordertype($this->get_ordertype());
        $this->oProject->set_filters($this->get_filter_searchconfig());
        //hierarchy recover
        //$this->oProject->set_select_user($this->oSessionUser->get_id());
        //RECOVER DATALIST
        $arList = $this->oProject->get_select_all_ids();
        $iRequestPage = $this->get_post("selPage");
        $oPage = new ComponentPage($arList,$iRequestPage);
        $arList = $oPage->get_items_to_show();
        $arList = $this->oProject->get_select_all_by_ids($arList);
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
        $oOpButtons = new AppHelperButtontabs(tr_pj_entities);
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
        $arButTabs["filters"]=array("href"=>"javascript:reset_filters();","icon"=>"awe-magic","innerhtml"=>tr_pj_clear_filters);
        $arButTabs["reload"]=array("href"=>"javascript:TfwControl.form_submit();","icon"=>"awe-refresh","innerhtml"=>tr_pj_refresh);
        $arButTabs["closeme"]=array("href"=>"javascript:closeme();","icon"=>"awe-remove-sign","innerhtml"=>tr_pj_closeme);
        return $arButTabs;
    }//build_singleassign_buttons()

    //singleassign_2
    protected function load_config_singleassign_filters()
    {
        //id
        $this->set_filter("id","txtId",array("operator"=>"like"));
        //code_erp
        //$this->set_filter("code_erp","txtCodeErp",array("operator"=>"like"));
        //id_type
        $this->set_filter("id_type","selIdType");
        //id_type_priority
        $this->set_filter("id_type_priority","selIdTypePriority");
        //id_type_status
        $this->set_filter("id_type_status","selIdTypeStatus");
        //id_user_to
        $this->set_filter("id_user_to","selIdUserTo");
        //id_user_by
        $this->set_filter("id_user_by","selIdUserBy");
        //description
        //$this->set_filter("description","txtDescription",array("operator"=>"like"));
        //date_open
        $this->set_filter("date_open","txtDateOpen",array("operator"=>"like"));
        //date_close
        $this->set_filter("date_close","txtDateClose",array("operator"=>"like"));
        //notes_detail
        $this->set_filter("notes_detail","txtNotesDetail",array("operator"=>"like"));
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
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtId",tr_pj_fil_id));
        $arFields[] = $oAuxWrapper;
        //code_erp
        //$oAuxField = new HelperInputText("txtCodeErp","txtCodeErp");
        //$oAuxField->set_value($this->get_post("txtCodeErp"));
        //$oAuxField->on_entersubmit();
        //$oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtCodeErp",tr_pj_fil_code_erp));
        //$arFields[] = $oAuxWrapper;
        //id_type
        $oType = new ModelProjectType();
        $arOptions = $oType->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdType","selIdType");
        $oAuxField->set_value_to_select($this->get_post("selIdType"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdType",tr_pj_fil_id_type));
        $arFields[] = $oAuxWrapper;
        //id_type_priority
        $oTypePriority = new ModelProjectArray();
        $arOptions = $oTypePriority->get_picklist_by_type("priority");
        $oAuxField = new HelperSelect($arOptions,"selIdTypePriority","selIdTypePriority");
        $oAuxField->set_value_to_select($this->get_post("selIdTypePriority"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdTypePriority",tr_pj_fil_id_type_priority));
        $arFields[] = $oAuxWrapper;
        //id_type_status
        $oTypeStatus = new ModelProjectArray();
        $arOptions = $oTypeStatus->get_picklist_by_type("status");
        $oAuxField = new HelperSelect($arOptions,"selIdTypeStatus","selIdTypeStatus");
        $oAuxField->set_value_to_select($this->get_post("selIdTypeStatus"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdTypeStatus",tr_pj_fil_id_type_status));
        $arFields[] = $oAuxWrapper;
        //id_user_to
        $oUserTo = new ModelUser();
        $arOptions = $oUserTo->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdUserTo","selIdUserTo");
        $oAuxField->set_value_to_select($this->get_post("selIdUserTo"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdUserTo",tr_pj_fil_id_user_to));
        $arFields[] = $oAuxWrapper;
        //id_user_by
        $oUserBy = new ModelUser();
        $arOptions = $oUserBy->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdUserBy","selIdUserBy");
        $oAuxField->set_value_to_select($this->get_post("selIdUserBy"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdUserBy",tr_pj_fil_id_user_by));
        $arFields[] = $oAuxWrapper;
        //description
        //$oAuxField = new HelperInputText("txtDescription","txtDescription");
        //$oAuxField->set_value($this->get_post("txtDescription"));
        //$oAuxField->on_entersubmit();
        //$oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtDescription",tr_pj_fil_description));
        //$arFields[] = $oAuxWrapper;
        //date_open
        $oAuxField = new HelperInputText("txtDateOpen","txtDateOpen");
        $oAuxField->set_value($this->get_post("txtDateOpen"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtDateOpen",tr_pj_fil_date_open));
        $arFields[] = $oAuxWrapper;
        //date_close
        $oAuxField = new HelperInputText("txtDateClose","txtDateClose");
        $oAuxField->set_value($this->get_post("txtDateClose"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtDateClose",tr_pj_fil_date_close));
        $arFields[] = $oAuxWrapper;
        //notes_detail
        $oAuxField = new HelperInputText("txtNotesDetail","txtNotesDetail");
        $oAuxField->set_value($this->get_post("txtNotesDetail"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtNotesDetail",tr_pj_fil_notes_detail));
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
        //id_type
        $this->set_filter_value("id_type",$this->get_post("selIdType"));
        //id_type_priority
        $this->set_filter_value("id_type_priority",$this->get_post("selIdTypePriority"));
        //id_type_status
        $this->set_filter_value("id_type_status",$this->get_post("selIdTypeStatus"));
        //id_user_to
        $this->set_filter_value("id_user_to",$this->get_post("selIdUserTo"));
        //id_user_by
        $this->set_filter_value("id_user_by",$this->get_post("selIdUserBy"));
        //description
        //$this->set_filter_value("description",$this->get_post("txtDescription"));
        //date_open
        $this->set_filter_value("date_open",$this->get_post("txtDateOpen"));
        //date_close
        $this->set_filter_value("date_close",$this->get_post("txtDateClose"));
        //notes_detail
        $this->set_filter_value("notes_detail",$this->get_post("txtNotesDetail"));
    }//set_singleassignfilters_from_post()

    //singleassign_5
    protected function get_singleassign_columns()
    {
        $arColumns["id"] = tr_pj_col_id;
        //$arColumns["code_erp"] = tr_pj_col_code_erp;
        //$arColumns["id_type"] = tr_pj_col_id_type;
        $arColumns["subtype"] = tr_pj_col_id_type;
        //$arColumns["id_type_priority"] = tr_pj_col_id_type_priority;
        $arColumns["priority"] = tr_pj_col_id_type_priority;
        //$arColumns["id_type_status"] = tr_pj_col_id_type_status;
        $arColumns["status"] = tr_pj_col_id_type_status;
        //$arColumns["id_user_to"] = tr_pj_col_id_user_to;
        $arColumns["userto"] = tr_pj_col_id_user_to;
        //$arColumns["id_user_by"] = tr_pj_col_id_user_by;
        $arColumns["userby"] = tr_pj_col_id_user_by;
        //$arColumns["description"] = tr_pj_col_description;
        $arColumns["date_open"] = tr_pj_col_date_open;
        $arColumns["date_close"] = tr_pj_col_date_close;
        $arColumns["notes_detail"] = tr_pj_col_notes_detail;
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
        $this->oProject->set_orderby($this->get_orderby());
        $this->oProject->set_ordertype($this->get_ordertype());
        $this->oProject->set_filters($this->get_filter_searchconfig());
        $arList = $this->oProject->get_select_all_ids();
        $iRequestPage = $this->get_post("selPage");
        $oPage = new ComponentPage($arList,$iRequestPage);
        $arList = $oPage->get_items_to_show();
        $arList = $this->oProject->get_select_all_by_ids($arList);
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
        $oOpButtons = new AppHelperButtontabs(tr_pj_entities);
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
