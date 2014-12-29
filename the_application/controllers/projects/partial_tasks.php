<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.2
 * @name PartialTasks
 * @file partial_tasks.php 
 * @date 30-10-2014 10:52 (SPAIN)
 * @observations: UC - Crea carpetas y vuelca imagenes en estas
 * @require controller_projecttasks.php
 */
import_apptranslate("projects");
import_appcontroller("projecttasks");

class PartialTasks extends ControllerProjectTasks
{   
    public function __construct()
    {
        $this->sModuleName = "projects";
        //console,pgpage,clientdevice,sesuser,aftersucces,currop  | log,session,permission,view
        parent::__construct();
        
        $this->oProject = new ModelProject();
        $this->oProject->set_platform($this->oSessionUser->get_platform());
        
        if($this->is_inget("id_project_task"))
        {
            $this->oProjectNote->set_id($this->get_get("id_project_task"));
            $this->oProjectNote->load_by_id();
        }
        if($this->is_inget("id_project"))
        {
            $this->oProject->set_id($this->get_get("id_project"));
            $this->oProject->load_by_id();
        }
        //$this->oSessionUser->set_dataowner_table($this->oProjectNote->get_table_name());
        //$this->oSessionUser->set_dataowner_tablefield("id_project");
        //$this->oSessionUser->set_dataowner_keys(array("id"=>$this->oProjectNote->get_id()));        
    }

//<editor-fold defaultstate="collapsed" desc="LIST">
    //list_1
    protected function build_list_scrumbs()
    {        
        $arLinks = array();
        $sUrlLink = $this->build_url("projecttasks");
        $arLinks["list"]=array("href"=>$sUrlLink,"innerhtml"=>tr_pjt_entities);
        $sUrlLink = $this->build_url($this->sModuleName,NULL,"update","id=".$this->get_get("id_project"));
        $arLinks["detail"]=array("href"=>$sUrlLink,"innerhtml"=>"Project: ".$this->oProject->get_id()." - ".$this->oProject->get_description());
        $sUrlLink = $this->build_url($this->sModuleName,"tasks","get_list","id_project=".$this->get_get("id_project"));
        $arLinks["tasks"]=array("href"=>$sUrlLink,"innerhtml"=>tr_pjt_entities);
        $oScrumbs = new AppHelperBreadscrumbs($arLinks);
        return $oScrumbs;
    }

    //list_2
    protected function build_list_tabs()
    {
        $arTabs = array();
        $sUrlTab = $this->build_url($this->sModuleName,NULL,"update","id=".$this->get_get("id_project"));
        $arTabs["detail"]=array("href"=>$sUrlTab,"innerhtml"=>"Detail");
        $sUrlTab = $this->build_url($this->sModuleName,"tasks","get_list","id_project=".$this->get_get("id_project"));
        $arTabs["tasks"]=array("href"=>$sUrlTab,"innerhtml"=>tr_pjt_entities);
        $oTabs = new AppHelperHeadertabs($arTabs,"tasks");
        return $oTabs;
    }

    //list_3
    protected function build_listoperation_buttons()
    {
        $arOpButtons = array();
        $arOpButtons["filters"]=array("href"=>"javascript:reset_filters();","icon"=>"awe-magic","innerhtml"=>tr_pjt_listopbutton_filters);
        $arOpButtons["reload"]=array("href"=>"javascript:TfwControl.form_submit();","icon"=>"awe-refresh","innerhtml"=>tr_pjt_listopbutton_reload);
        if($this->oPermission->is_insert())
            $arOpButtons["insert"]=array("href"=>$this->build("projecttasks",NULL,"insert"),"icon"=>"awe-plus","innerhtml"=>tr_pjt_listopbutton_insert);
        if($this->oPermission->is_quarantine())
            $arOpButtons["multiquarantine"]=array("href"=>"javascript:multi_quarantine();","icon"=>"awe-remove","innerhtml"=>tr_pjt_listopbutton_multiquarantine);
        //if($this->oPermission->is_delete())
            //$arOpButtons["multidelete"]=array("href"=>"javascript:multi_delete();","icon"=>"awe-remove","innerhtml"=>tr_pjt_listopbutton_multidelete);
        //PICK WINDOWS
        //$arOpButtons["multiassign"]=array("href"=>"javascript:multiassign_window('projecttasks',null,'multiassign','projecttasks','addexternaldata');","icon"=>"awe-external-link","innerhtml"=>tr_pjt_listopbutton_multiassign);
        //$arOpButtons["singleassign"]=array("href"=>"javascript:single_pick('projecttasks','singleassign','txtI','txtI');","icon"=>"awe-external-link","innerhtml"=>tr_pjt_listopbutton_singleassign);
        $oOpButtons = new AppHelperButtontabs(tr_pjt_entities);
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
        //id_type_priority
        $this->set_filter("id_type_priority","selIdTypePriority");
        //id_type_status
        $this->set_filter("id_type_status","selIdTypeStatus");
        //id_type_percent
        $this->set_filter("id_type_percent","selIdTypePercent");
        //id_project
        $this->set_filter("id_project","selIdProject");
        //id_user_to
        $this->set_filter("id_user_to","selIdUserTo");
        //id_user_by
        $this->set_filter("id_user_by","selIdUserBy");
        //hours
        $this->set_filter("hours","txtHours",array("operator"=>"like"));
        //tasks_to
        $this->set_filter("tasks_to","txttasksTo",array("operator"=>"like"));
        //description
        //$this->set_filter("description","txtDescription",array("operator"=>"like"));
        //date_open
        $this->set_filter("date_open","txtDateOpen",array("operator"=>"like"));
        //date_close
        $this->set_filter("date_close","txtDateClose",array("operator"=>"like"));
        //tasks_detail
        $this->set_filter("tasks_detail","txttasksDetail",array("operator"=>"like"));
    }//load_config_list_filters()

    //list_5
    protected function set_listfilters_from_post()
    {
        //id
        $this->set_filter_value("id",$this->get_post("txtId"));
        //code_erp
        //$this->set_filter_value("code_erp",$this->get_post("txtCodeErp"));
        //id_type_priority
        $this->set_filter_value("id_type_priority",$this->get_post("selIdTypePriority"));
        //id_type_status
        $this->set_filter_value("id_type_status",$this->get_post("selIdTypeStatus"));
        //id_type_percent
        $this->set_filter_value("id_type_percent",$this->get_post("selIdTypePercent"));
        //id_project
        $this->set_filter_value("id_project",$this->get_post("selIdProject"));
        //id_user_to
        $this->set_filter_value("id_user_to",$this->get_post("selIdUserTo"));
        //id_user_by
        $this->set_filter_value("id_user_by",$this->get_post("selIdUserBy"));
        //hours
        $this->set_filter_value("hours",$this->get_post("txtHours"));
        //tasks_to
        $this->set_filter_value("tasks_to",$this->get_post("txttasksTo"));
        //description
        //$this->set_filter_value("description",$this->get_post("txtDescription"));
        //date_open
        $this->set_filter_value("date_open",$this->get_post("txtDateOpen"));
        //date_close
        $this->set_filter_value("date_close",$this->get_post("txtDateClose"));
        //tasks_detail
        $this->set_filter_value("tasks_detail",$this->get_post("txttasksDetail"));
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
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtId",tr_pjt_fil_id));
        $arFields[] = $oAuxWrapper;
        //code_erp
        //$oAuxField = new HelperInputText("txtCodeErp","txtCodeErp");
        //$oAuxField->set_value($this->get_post("txtCodeErp"));
        //$oAuxField->on_entersubmit();
        //$oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtCodeErp",tr_pjt_fil_code_erp));
        //$arFields[] = $oAuxWrapper;
        //id_type_priority
        $oTypePriority = new ModelProjectArray();
        $arOptions = $oTypePriority->get_picklist_by_type("priority");
        $oAuxField = new HelperSelect($arOptions,"selIdTypePriority","selIdTypePriority");
        $oAuxField->set_value_to_select($this->get_post("selIdTypePriority"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdTypePriority",tr_pjt_fil_id_type_priority));
        $arFields[] = $oAuxWrapper;
        //id_type_status
        $oTypeStatus = new ModelProjectArray();
        $arOptions = $oTypeStatus->get_picklist_by_type("status");
        $oAuxField = new HelperSelect($arOptions,"selIdTypeStatus","selIdTypeStatus");
        $oAuxField->set_value_to_select($this->get_post("selIdTypeStatus"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdTypeStatus",tr_pjt_fil_id_type_status));
        $arFields[] = $oAuxWrapper;
        //id_type_percent
        $oTypePercent = new ModelProjectArray();
        $arOptions = $oTypePercent->get_picklist_by_type("percent");
        $oAuxField = new HelperSelect($arOptions,"selIdTypePercent","selIdTypePercent");
        $oAuxField->set_value_to_select($this->get_post("selIdTypePercent"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdTypePercent",tr_pjt_fil_id_type_percent));
        $arFields[] = $oAuxWrapper;
        //id_project
        $oProject = new ModelProject();
        $arOptions = $oProject->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdProject","selIdProject");
        $oAuxField->set_value_to_select($this->get_post("selIdProject"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdProject",tr_pjt_fil_id_project));
        $arFields[] = $oAuxWrapper;
        //id_user_to
        $oUserTo = new ModelUser();
        $arOptions = $oUserTo->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdUserTo","selIdUserTo");
        $oAuxField->set_value_to_select($this->get_post("selIdUserTo"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdUserTo",tr_pjt_fil_id_user_to));
        $arFields[] = $oAuxWrapper;
        //id_user_by
        $oUserBy = new ModelUser();
        $arOptions = $oUserBy->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdUserBy","selIdUserBy");
        $oAuxField->set_value_to_select($this->get_post("selIdUserBy"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdUserBy",tr_pjt_fil_id_user_by));
        $arFields[] = $oAuxWrapper;
        //hours
        $oAuxField = new HelperInputText("txtHours","txtHours");
        $oAuxField->set_value($this->get_post("txtHours"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtHours",tr_pjt_fil_hours));
        $arFields[] = $oAuxWrapper;
        //tasks_to
        $oAuxField = new HelperInputText("txttasksTo","txttasksTo");
        $oAuxField->set_value($this->get_post("txttasksTo"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txttasksTo",tr_pjt_fil_tasks_to));
        $arFields[] = $oAuxWrapper;
        //description
        //$oAuxField = new HelperInputText("txtDescription","txtDescription");
        //$oAuxField->set_value($this->get_post("txtDescription"));
        //$oAuxField->on_entersubmit();
        //$oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtDescription",tr_pjt_fil_description));
        //$arFields[] = $oAuxWrapper;
        //date_open
        $oAuxField = new HelperInputText("txtDateOpen","txtDateOpen");
        $oAuxField->set_value($this->get_post("txtDateOpen"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtDateOpen",tr_pjt_fil_date_open));
        $arFields[] = $oAuxWrapper;
        //date_close
        $oAuxField = new HelperInputText("txtDateClose","txtDateClose");
        $oAuxField->set_value($this->get_post("txtDateClose"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtDateClose",tr_pjt_fil_date_close));
        $arFields[] = $oAuxWrapper;
        //tasks_detail
        $oAuxField = new HelperInputText("txttasksDetail","txttasksDetail");
        $oAuxField->set_value($this->get_post("txttasksDetail"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txttasksDetail",tr_pjt_fil_tasks_detail));
        $arFields[] = $oAuxWrapper;
        return $arFields;
    }//get_list_filters()

    //list_7
    protected function get_list_columns()
    {
        $arColumns["id"] = tr_pjt_col_id;
        //$arColumns["code_erp"] = tr_pjt_col_code_erp;
        //$arColumns["id_type_priority"] = tr_pjt_col_id_type_priority;
        $arColumns["priority"] = tr_pjt_col_id_type_priority;
        //$arColumns["id_type_status"] = tr_pjt_col_id_type_status;
        $arColumns["status"] = tr_pjt_col_id_type_status;
        //$arColumns["id_type_percent"] = tr_pjt_col_id_type_percent;
        $arColumns["percent"] = tr_pjt_col_id_type_percent;
        //$arColumns["id_project"] = tr_pjt_col_id_project;
        $arColumns["project"] = tr_pjt_col_id_project;
        //$arColumns["id_user_to"] = tr_pjt_col_id_user_to;
        $arColumns["userto"] = tr_pjt_col_id_user_to;
        //$arColumns["id_user_by"] = tr_pjt_col_id_user_by;
        $arColumns["userby"] = tr_pjt_col_id_user_by;
        $arColumns["hours"] = tr_pjt_col_hours;
        $arColumns["tasks_to"] = tr_pjt_col_tasks_to;
        //$arColumns["description"] = tr_pjt_col_description;
        $arColumns["date_open"] = tr_pjt_col_date_open;
        $arColumns["date_close"] = tr_pjt_col_date_close;
        $arColumns["tasks_detail"] = tr_pjt_col_tasks_detail;
        return $arColumns;
    }//get_list_columns()

    //list_8
    public function get_list()
    {
        //$this->go_to_401($this->oPermission->is_not_select());
        
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
        $this->oProjectTask->set_orderby($this->get_orderby());
        $this->oProjectTask->set_ordertype($this->get_ordertype());
        $this->oProjectTask->set_filters($this->get_filter_searchconfig());
        //hierarchy recover
        //$this->oProjectTask->set_select_user($this->oSessionUser->get_id());
        $arList = $this->oProjectTask->get_select_all_ids();
        $iRequestPage = $this->get_post("selPage");
        $oPage = new ComponentPage($arList,$iRequestPage);
        $arList = $oPage->get_items_to_show();
        $arList = $this->oProjectTask->get_select_all_by_ids($arList);
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
        //("href"=>"url_lines","innerhtml"=>tr_pjt_order_lines,"class"=>"btn btn-info","icon"=>"awe-info-sign")));
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
        $sUrlLink = $this->build_url("projecttasks");
        $arLinks["list"]=array("href"=>$sUrlLink,"innerhtml"=>tr_pjt_entities);
        $sUrlLink = $this->build("projecttasks",NULL,"insert");
        $arLinks["insert"]=array("href"=>$sUrlLink,"innerhtml"=>tr_pjt_entity_insert);
        $oScrumbs = new AppHelperBreadscrumbs($arLinks);
        return $oScrumbs;
    }//build_insert_scrumbs()

    //insert_2
    protected function build_insert_tabs()
    {
        $arTabs = array();
        //$sUrlTab = $this->build("projecttasks",NULL,"insert");
        //$arTabs["insert1"]=array("href"=>$sUrlTab,"innerhtml"=>tr_pjt_instabs_1);
        //$sUrlTab = $this->build("projecttasks",NULL,"insert2");
        //$arTabs["insert2"]=array("href"=>$sUrlTab,"innerhtml"=>tr_pjt_instabs_2);
        $oTabs = new AppHelperHeadertabs($arTabs,"insert1");
        return $oTabs;
    }//build_insert_tabs()
    //insert_3
    protected function build_insert_opbuttons()
    {
        $arOpButtons = array();
        $arOpButtons["list"] = array("href"=>$this->build_url("projecttasks"),"icon"=>"awe-search","innerhtml"=>tr_pjt_insopbutton_list);
        //$arOpButtons["extra"] = array("href"=>$this->build_url("projecttasks"),"icon"=>"awe-xxxx","innerhtml"=>tr_pjt_insopbutton_extra1);
        $oOpButtons = new AppHelperButtontabs(tr_pjt_entities);
        $oOpButtons->set_tabs($arOpButtons);
        return $oOpButtons;
    }//build_insert_opbuttons()

    //insert_4
    protected function build_insert_fields($usePost=0)
    {
        $arFields = array(); $oAuxField = NULL; $oAuxLabel = NULL;
        $arFields[]= new AppHelperFormhead(tr_pjt_entity_new);
        //id
        //$oAuxField = new HelperInputText("txtId","txtId");
        //$oAuxField->is_primarykey();
        //if($usePost) $oAuxField->set_value($this->get_post("txtId"));
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        //$arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //code_erp
        //$oAuxField = new HelperInputText("txtCodeErp","txtCodeErp");
        //if($usePost) $oAuxField->set_value($this->get_post("txtCodeErp"));
        //$oAuxLabel = new HelperLabel("txtCodeErp",tr_pjt_ins_code_erp,"lblCodeErp");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        //$arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //id_type_priority
        $oTypePriority = new ModelProjectArray();
        $arOptions = $oTypePriority->get_picklist_by_type("priority");
        $oAuxField = new HelperSelect($arOptions,"selIdTypePriority","selIdTypePriority");
        $oAuxField->set_value_to_select($this->get_post("selIdTypePriority"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdTypePriority",tr_pjt_ins_id_type_priority));
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdTypePriority"));
        $oAuxLabel = new HelperLabel("selIdTypePriority",tr_pjt_ins_id_type_priority,"lblIdTypePriority");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //id_type_status
        $oTypeStatus = new ModelProjectArray();
        $arOptions = $oTypeStatus->get_picklist_by_type("status");
        $oAuxField = new HelperSelect($arOptions,"selIdTypeStatus","selIdTypeStatus");
        $oAuxField->set_value_to_select($this->get_post("selIdTypeStatus"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdTypeStatus",tr_pjt_ins_id_type_status));
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdTypeStatus"));
        $oAuxLabel = new HelperLabel("selIdTypeStatus",tr_pjt_ins_id_type_status,"lblIdTypeStatus");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //id_type_percent
        $oTypePercent = new ModelProjectArray();
        $arOptions = $oTypePercent->get_picklist_by_type("percent");
        $oAuxField = new HelperSelect($arOptions,"selIdTypePercent","selIdTypePercent");
        $oAuxField->set_value_to_select($this->get_post("selIdTypePercent"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdTypePercent",tr_pjt_ins_id_type_percent));
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdTypePercent"));
        $oAuxLabel = new HelperLabel("selIdTypePercent",tr_pjt_ins_id_type_percent,"lblIdTypePercent");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //id_project
        $oProject = new ModelProject();
        $arOptions = $oProject->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdProject","selIdProject");
        $oAuxField->set_value_to_select($this->get_post("selIdProject"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdProject",tr_pjt_ins_id_project));
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdProject"));
        $oAuxLabel = new HelperLabel("selIdProject",tr_pjt_ins_id_project,"lblIdProject");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //id_user_to
        $oUserTo = new ModelUser();
        $arOptions = $oUserTo->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdUserTo","selIdUserTo");
        $oAuxField->set_value_to_select($this->get_post("selIdUserTo"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdUserTo",tr_pjt_ins_id_user_to));
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdUserTo"));
        $oAuxLabel = new HelperLabel("selIdUserTo",tr_pjt_ins_id_user_to,"lblIdUserTo");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //id_user_by
        $oUserBy = new ModelUser();
        $arOptions = $oUserBy->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdUserBy","selIdUserBy");
        $oAuxField->set_value_to_select($this->get_post("selIdUserBy"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdUserBy",tr_pjt_ins_id_user_by));
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdUserBy"));
        $oAuxLabel = new HelperLabel("selIdUserBy",tr_pjt_ins_id_user_by,"lblIdUserBy");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //hours
        $oAuxField = new HelperInputText("txtHours","txtHours");
        $oAuxField->set_value("0.00");
        if($usePost) $oAuxField->set_value(dbbo_numeric2($this->get_post("txtHours")));
        $oAuxLabel = new HelperLabel("txtHours",tr_pjt_ins_hours,"lblHours");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //tasks_to
        $oAuxField = new HelperInputText("txttasksTo","txttasksTo");
        if($usePost) $oAuxField->set_value($this->get_post("txttasksTo"));
        $oAuxLabel = new HelperLabel("txttasksTo",tr_pjt_ins_tasks_to,"lbltasksTo");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //description
        //$oAuxField = new HelperInputText("txtDescription","txtDescription");
        //if($usePost) $oAuxField->set_value($this->get_post("txtDescription"));
        //$oAuxLabel = new HelperLabel("txtDescription",tr_pjt_ins_description,"lblDescription");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        //$arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //date_open
        $oAuxField = new HelperInputText("txtDateOpen","txtDateOpen");
        if($usePost) $oAuxField->set_value($this->get_post("txtDateOpen"));
        $oAuxLabel = new HelperLabel("txtDateOpen",tr_pjt_ins_date_open,"lblDateOpen");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //date_close
        $oAuxField = new HelperInputText("txtDateClose","txtDateClose");
        if($usePost) $oAuxField->set_value($this->get_post("txtDateClose"));
        $oAuxLabel = new HelperLabel("txtDateClose",tr_pjt_ins_date_close,"lblDateClose");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //tasks_detail
        $oAuxField = new HelperInputText("txttasksDetail","txttasksDetail");
        if($usePost) $oAuxField->set_value($this->get_post("txttasksDetail"));
        $oAuxLabel = new HelperLabel("txttasksDetail",tr_pjt_ins_tasks_detail,"lbltasksDetail");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //SAVE BUTTON
        $oAuxField = new HelperButtonBasic("butSave",tr_pjt_ins_savebutton);
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
        //$arFieldsConfig["id"] = array("controlid"=>"txtId","label"=>tr_pjt_ins_id,"length"=>9,"type"=>array("numeric","required"));
        //$arFieldsConfig["code_erp"] = array("controlid"=>"txtCodeErp","label"=>tr_pjt_ins_code_erp,"length"=>25,"type"=>array());
        //$arFieldsConfig["id_type_priority"] = array("controlid"=>"selIdTypePriority","label"=>tr_pjt_ins_id_type_priority,"length"=>4,"type"=>array());
        //$arFieldsConfig["id_type_status"] = array("controlid"=>"selIdTypeStatus","label"=>tr_pjt_ins_id_type_status,"length"=>4,"type"=>array());
        //$arFieldsConfig["id_type_percent"] = array("controlid"=>"selIdTypePercent","label"=>tr_pjt_ins_id_type_percent,"length"=>4,"type"=>array());
        //$arFieldsConfig["id_project"] = array("controlid"=>"selIdProject","label"=>tr_pjt_ins_id_project,"length"=>9,"type"=>array());
        //$arFieldsConfig["id_user_to"] = array("controlid"=>"selIdUserTo","label"=>tr_pjt_ins_id_user_to,"length"=>9,"type"=>array());
        //$arFieldsConfig["id_user_by"] = array("controlid"=>"selIdUserBy","label"=>tr_pjt_ins_id_user_by,"length"=>9,"type"=>array());
        $arFieldsConfig["hours"] = array("controlid"=>"txtHours","label"=>tr_pjt_ins_hours,"length"=>5,"type"=>array("numeric"));
        $arFieldsConfig["tasks_to"] = array("controlid"=>"txttasksTo","label"=>tr_pjt_ins_tasks_to,"length"=>250,"type"=>array());
        //$arFieldsConfig["description"] = array("controlid"=>"txtDescription","label"=>tr_pjt_ins_description,"length"=>200,"type"=>array());
        $arFieldsConfig["date_open"] = array("controlid"=>"txtDateOpen","label"=>tr_pjt_ins_date_open,"length"=>8,"type"=>array());
        $arFieldsConfig["date_close"] = array("controlid"=>"txtDateClose","label"=>tr_pjt_ins_date_close,"length"=>8,"type"=>array());
        $arFieldsConfig["tasks_detail"] = array("controlid"=>"txttasksDetail","label"=>tr_pjt_ins_tasks_detail,"length"=>3000,"type"=>array());
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
                //$this->oProjectTask->log_save_insert();
                $this->oProjectTask->set_attrib_value($arFieldsValues);
                $this->oProjectTask->set_insert_user($this->oSessionUser->get_id());
                //$this->oProjectTask->set_platform($this->oSessionUser->get_platform());
                $this->oProjectTask->autoinsert();
                if($this->oProjectTask->is_error())
                {
                $oAlert->set_type("e");
                $oAlert->set_title(tr_data_not_saved);
                $oAlert->set_content(tr_error_trying_to_save);
                }
                else//insert ok
                {
                $this->set_get("id",$this->oProjectTask->get_last_insert_id());
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
        $sUrlLink = $this->build_url("projecttasks");
        $arLinks["list"]=array("href"=>$sUrlLink,"innerhtml"=>tr_pjt_entities);
        $sUrlLink = $this->build_url("projecttasks",NULL,"update","id=".$this->get_get("id"));
        $arLinks["detail"]=array("href"=>$sUrlLink,"innerhtml"=>tr_pjt_entity.": ".$this->oProjectTask->get_id()." - ".$this->oProjectTask->get_description());
        $oScrumbs = new AppHelperBreadscrumbs($arLinks);
        return $oScrumbs;
    }//build_update_scrumbs()

    //update_2
    protected function build_update_tabs()
    {
        $arTabs = array();
        $sUrlTab = $this->build_url("projecttasks",NULL,"update","id=".$this->get_get("id"));
        $arTabs["detail"]=array("href"=>$sUrlTab,"innerhtml"=>tr_pjt_updtabs_detail);
        //$sUrlTab = $this->build_url("projecttasks","foreignamodule","get_list_by_foreign","id_foreign=".$this->get_get("id_parent_foreign"));
        //$arTabs["foreigndata"]=array("href"=>$sUrlTab,"innerhtml"=>tr_pjt_updtabs_foreigndata);
        $oTabs = new AppHelperHeadertabs($arTabs,"detail");
        return $oTabs;
    }//build_update_tabs()

    //update_3
    protected function build_update_opbuttons()
    {
        $arOpButtons = array();
        if($this->oPermission->is_select())
            $arOpButtons["list"]=array("href"=>$this->build_url("projecttasks"),"icon"=>"awe-search","innerhtml"=>tr_pjt_updopbutton_list);
        //if($this->oPermission->is_insert())
            //$arOpButtons["insert"]=array("href"=>$this->build("projecttasks",NULL,"insert"),"icon"=>"awe-plus","innerhtml"=>tr_pjt_updopbutton_insert);
        if($this->oPermission->is_quarantine())
            $arOpButtons["delete"]=array("href"=>$this->build_url("projecttasks",NULL,"quarantine","id=".$this->get_get("id")),"icon"=>"awe-remove","innerhtml"=>tr_pjt_updopbutton_quarantine);
        //if($this->oPermission->is_delete())
            //$arOpButtons["delete"]=array("href"=>$this->build_url("projecttasks",NULL,"delete","id=".$this->get_get("id")),"icon"=>"awe-remove","innerhtml"=>tr_pjt_updopbutton_delete);
        $oOpButtons = new AppHelperButtontabs(tr_pjt_entities);
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
        $oAuxField->set_value(dbbo_numeric2($this->oProjectTask->get_id()));
        if($usePost) $oAuxField->set_value(dbbo_numeric2($this->get_post("txtId")));
        $oAuxLabel = new HelperLabel("txtId",tr_pjt_upd_id,"lblId");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //code_erp
        //$oAuxField = new HelperInputText("txtCodeErp","txtCodeErp");
        //$oAuxField->set_value($this->oProjectTask->get_code_erp());
        //if($usePost) $oAuxField->set_value($this->get_post("txtCodeErp"));
        //$oAuxLabel = new HelperLabel("txtCodeErp",tr_pjt_upd_code_erp,"lblCodeErp");
        //$oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        //$arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //id_type_priority
        $oTypePriority = new ModelProjectArray();
        $arOptions = $oTypePriority->get_picklist_by_type("priority");
        $oAuxField = new HelperSelect($arOptions,"selIdTypePriority","selIdTypePriority");
        $oAuxField->set_value_to_select($this->get_post("selIdTypePriority"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdTypePriority",tr_pjt_upd_id_type_priority));
        $oAuxField->set_value_to_select($this->oProjectTask->get_id_type_priority());
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdTypePriority"));
        $oAuxLabel = new HelperLabel("selIdTypePriority",tr_pjt_upd_id_type_priority,"lblIdTypePriority");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //id_type_status
        $oTypeStatus = new ModelProjectArray();
        $arOptions = $oTypeStatus->get_picklist_by_type("status");
        $oAuxField = new HelperSelect($arOptions,"selIdTypeStatus","selIdTypeStatus");
        $oAuxField->set_value_to_select($this->get_post("selIdTypeStatus"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdTypeStatus",tr_pjt_upd_id_type_status));
        $oAuxField->set_value_to_select($this->oProjectTask->get_id_type_status());
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdTypeStatus"));
        $oAuxLabel = new HelperLabel("selIdTypeStatus",tr_pjt_upd_id_type_status,"lblIdTypeStatus");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //id_type_percent
        $oTypePercent = new ModelProjectArray();
        $arOptions = $oTypePercent->get_picklist_by_type("percent");
        $oAuxField = new HelperSelect($arOptions,"selIdTypePercent","selIdTypePercent");
        $oAuxField->set_value_to_select($this->get_post("selIdTypePercent"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdTypePercent",tr_pjt_upd_id_type_percent));
        $oAuxField->set_value_to_select($this->oProjectTask->get_id_type_percent());
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdTypePercent"));
        $oAuxLabel = new HelperLabel("selIdTypePercent",tr_pjt_upd_id_type_percent,"lblIdTypePercent");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //id_project
        $oProject = new ModelProject();
        $arOptions = $oProject->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdProject","selIdProject");
        $oAuxField->set_value_to_select($this->get_post("selIdProject"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdProject",tr_pjt_upd_id_project));
        $oAuxField->set_value_to_select($this->oProjectTask->get_id_project());
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdProject"));
        $oAuxLabel = new HelperLabel("selIdProject",tr_pjt_upd_id_project,"lblIdProject");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //id_user_to
        $oUserTo = new ModelUser();
        $arOptions = $oUserTo->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdUserTo","selIdUserTo");
        $oAuxField->set_value_to_select($this->get_post("selIdUserTo"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdUserTo",tr_pjt_upd_id_user_to));
        $oAuxField->set_value_to_select($this->oProjectTask->get_id_user_to());
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdUserTo"));
        $oAuxLabel = new HelperLabel("selIdUserTo",tr_pjt_upd_id_user_to,"lblIdUserTo");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //id_user_by
        $oUserBy = new ModelUser();
        $arOptions = $oUserBy->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdUserBy","selIdUserBy");
        $oAuxField->set_value_to_select($this->get_post("selIdUserBy"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdUserBy",tr_pjt_upd_id_user_by));
        $oAuxField->set_value_to_select($this->oProjectTask->get_id_user_by());
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdUserBy"));
        $oAuxLabel = new HelperLabel("selIdUserBy",tr_pjt_upd_id_user_by,"lblIdUserBy");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //hours
        $oAuxField = new HelperInputText("txtHours","txtHours");
        $oAuxField->set_value(dbbo_numeric2($this->oProjectTask->get_hours()));
        if($usePost) $oAuxField->set_value(dbbo_numeric2($this->get_post("txtHours")));
        $oAuxLabel = new HelperLabel("txtHours",tr_pjt_upd_hours,"lblHours");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //tasks_to
        $oAuxField = new HelperInputText("txttasksTo","txttasksTo");
        $oAuxField->set_value($this->oProjectTask->get_tasks_to());
        if($usePost) $oAuxField->set_value($this->get_post("txttasksTo"));
        $oAuxLabel = new HelperLabel("txttasksTo",tr_pjt_upd_tasks_to,"lbltasksTo");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //description
        //$oAuxField = new HelperInputText("txtDescription","txtDescription");
        //$oAuxField->set_value($this->oProjectTask->get_description());
        //if($usePost) $oAuxField->set_value($this->get_post("txtDescription"));
        //$oAuxLabel = new HelperLabel("txtDescription",tr_pjt_upd_description,"lblDescription");
        //$oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        //$arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //date_open
        $oAuxField = new HelperInputText("txtDateOpen","txtDateOpen");
        $oAuxField->set_value($this->oProjectTask->get_date_open());
        if($usePost) $oAuxField->set_value($this->get_post("txtDateOpen"));
        $oAuxLabel = new HelperLabel("txtDateOpen",tr_pjt_upd_date_open,"lblDateOpen");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //date_close
        $oAuxField = new HelperInputText("txtDateClose","txtDateClose");
        $oAuxField->set_value($this->oProjectTask->get_date_close());
        if($usePost) $oAuxField->set_value($this->get_post("txtDateClose"));
        $oAuxLabel = new HelperLabel("txtDateClose",tr_pjt_upd_date_close,"lblDateClose");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //tasks_detail
        $oAuxField = new HelperInputText("txttasksDetail","txttasksDetail");
        $oAuxField->set_value($this->oProjectTask->get_tasks_detail());
        if($usePost) $oAuxField->set_value($this->get_post("txttasksDetail"));
        $oAuxLabel = new HelperLabel("txttasksDetail",tr_pjt_upd_tasks_detail,"lbltasksDetail");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //BUTTON SAVE
        $oAuxField = new HelperButtonBasic("butSave",tr_pjt_upd_savebutton);
        $oAuxField->add_class("btn btn-primary");
        $oAuxField->set_js_onclick("update();");
        if($this->oPermission->is_update())
            $arFields[] = new ApphelperFormactions(array($oAuxField));
        //AUDIT INFO
        $sRegInfo = $this->get_audit_info($this->oProjectTask->get_insert_user(),$this->oProjectTask->get_insert_date()
        ,$this->oProjectTask->get_update_user(),$this->oProjectTask->get_update_date());
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
        $arFieldsConfig["id"] = array("controlid"=>"txtId","label"=>tr_pjt_upd_id,"length"=>9,"type"=>array("numeric","required"));
        //$arFieldsConfig["code_erp"] = array("controlid"=>"txtCodeErp","label"=>tr_pjt_upd_code_erp,"length"=>25,"type"=>array());
        //$arFieldsConfig["id_type_priority"] = array("controlid"=>"selIdTypePriority","label"=>tr_pjt_upd_id_type_priority,"length"=>4,"type"=>array());
        //$arFieldsConfig["id_type_status"] = array("controlid"=>"selIdTypeStatus","label"=>tr_pjt_upd_id_type_status,"length"=>4,"type"=>array());
        //$arFieldsConfig["id_type_percent"] = array("controlid"=>"selIdTypePercent","label"=>tr_pjt_upd_id_type_percent,"length"=>4,"type"=>array());
        //$arFieldsConfig["id_project"] = array("controlid"=>"selIdProject","label"=>tr_pjt_upd_id_project,"length"=>9,"type"=>array());
        //$arFieldsConfig["id_user_to"] = array("controlid"=>"selIdUserTo","label"=>tr_pjt_upd_id_user_to,"length"=>9,"type"=>array());
        //$arFieldsConfig["id_user_by"] = array("controlid"=>"selIdUserBy","label"=>tr_pjt_upd_id_user_by,"length"=>9,"type"=>array());
        $arFieldsConfig["hours"] = array("controlid"=>"txtHours","label"=>tr_pjt_upd_hours,"length"=>5,"type"=>array("numeric"));
        $arFieldsConfig["tasks_to"] = array("controlid"=>"txttasksTo","label"=>tr_pjt_upd_tasks_to,"length"=>250,"type"=>array());
        //$arFieldsConfig["description"] = array("controlid"=>"txtDescription","label"=>tr_pjt_upd_description,"length"=>200,"type"=>array());
        $arFieldsConfig["date_open"] = array("controlid"=>"txtDateOpen","label"=>tr_pjt_upd_date_open,"length"=>8,"type"=>array());
        $arFieldsConfig["date_close"] = array("controlid"=>"txtDateClose","label"=>tr_pjt_upd_date_close,"length"=>8,"type"=>array());
        $arFieldsConfig["tasks_detail"] = array("controlid"=>"txttasksDetail","label"=>tr_pjt_upd_tasks_detail,"length"=>3000,"type"=>array());
        return $arFieldsConfig;
    }//get_update_validate

    //update_6
    protected function build_update_form($usePost=0)
    {
        $id = $this->oProjectTask->get_id();
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
                $this->oProjectTask->set_attrib_value($arFieldsValues);
                //$this->oProjectTask->set_description($oProjectTask->get_field1()." ".$oProjectTask->get_field2());
                $this->oProjectTask->set_update_user($this->oSessionUser->get_id());
                $this->oProjectTask->autoupdate();
                if($this->oProjectTask->is_error())
                {
                $oAlert->set_type("e");
                $oAlert->set_title(tr_data_not_saved);
                $oAlert->set_content(tr_error_trying_to_save);
                }//no error
                else//update ok
                {
                //$this->oProjectTask->load_by_id();
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
            $this->oProjectTask->set_id($id);
            $this->oProjectTask->autodelete();
            if($this->oProjectTask->is_error())
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
            $this->oProjectTask->set_id($id);
            $this->oProjectTask->autodelete();
            if($this->oProjectTask->is_error())
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
            $this->oProjectTask->set_id($id);
            $this->oProjectTask->autoquarantine();
            if($this->oProjectTask->is_error())
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
            $this->oProjectTask->set_id($id);
            $this->oProjectTask->autoquarantine();
            if($this->oProjectTask->is_error())
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
        $arOpButtons["filters"]=array("href"=>"javascript:reset_filters();","icon"=>"awe-magic","innerhtml"=>tr_pjt_clear_filters);
        $arOpButtons["reload"]=array("href"=>"javascript:TfwControl.form_submit();","icon"=>"awe-refresh","innerhtml"=>tr_pjt_refresh);
        $arOpButtons["multiadd"]=array("href"=>"javascript:multiadd();","icon"=>"awe-external-link","innerhtml"=>tr_pjt_multiadd);
        $arOpButtons["closeme"]=array("href"=>"javascript:closeme();","icon"=>"awe-remove-sign","innerhtml"=>tr_pjt_closeme);
        $oOpButtons = new AppHelperButtontabs(tr_pjt_entities);
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
        //id_type_priority
        $this->set_filter("id_type_priority","selIdTypePriority");
        //id_type_status
        $this->set_filter("id_type_status","selIdTypeStatus");
        //id_type_percent
        $this->set_filter("id_type_percent","selIdTypePercent");
        //id_project
        $this->set_filter("id_project","selIdProject");
        //id_user_to
        $this->set_filter("id_user_to","selIdUserTo");
        //id_user_by
        $this->set_filter("id_user_by","selIdUserBy");
        //hours
        $this->set_filter("hours","txtHours",array("operator"=>"like"));
        //tasks_to
        $this->set_filter("tasks_to","txttasksTo",array("operator"=>"like"));
        //description
        //$this->set_filter("description","txtDescription",array("operator"=>"like"));
        //date_open
        $this->set_filter("date_open","txtDateOpen",array("operator"=>"like"));
        //date_close
        $this->set_filter("date_close","txtDateClose",array("operator"=>"like"));
        //tasks_detail
        $this->set_filter("tasks_detail","txttasksDetail",array("operator"=>"like"));
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
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtId",tr_pjt_fil_id));
        $arFields[] = $oAuxWrapper;
        //code_erp
        //$oAuxField = new HelperInputText("txtCodeErp","txtCodeErp");
        //$oAuxField->set_value($this->get_post("txtCodeErp"));
        //$oAuxField->on_entersubmit();
        //$oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtCodeErp",tr_pjt_fil_code_erp));
        //$arFields[] = $oAuxWrapper;
        //id_type_priority
        $oTypePriority = new ModelProjectArray();
        $arOptions = $oTypePriority->get_picklist_by_type("priority");
        $oAuxField = new HelperSelect($arOptions,"selIdTypePriority","selIdTypePriority");
        $oAuxField->set_value_to_select($this->get_post("selIdTypePriority"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdTypePriority",tr_pjt_fil_id_type_priority));
        $arFields[] = $oAuxWrapper;
        //id_type_status
        $oTypeStatus = new ModelProjectArray();
        $arOptions = $oTypeStatus->get_picklist_by_type("status");
        $oAuxField = new HelperSelect($arOptions,"selIdTypeStatus","selIdTypeStatus");
        $oAuxField->set_value_to_select($this->get_post("selIdTypeStatus"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdTypeStatus",tr_pjt_fil_id_type_status));
        $arFields[] = $oAuxWrapper;
        //id_type_percent
        $oTypePercent = new ModelProjectArray();
        $arOptions = $oTypePercent->get_picklist_by_type("percent");
        $oAuxField = new HelperSelect($arOptions,"selIdTypePercent","selIdTypePercent");
        $oAuxField->set_value_to_select($this->get_post("selIdTypePercent"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdTypePercent",tr_pjt_fil_id_type_percent));
        $arFields[] = $oAuxWrapper;
        //id_project
        $oProject = new ModelProject();
        $arOptions = $oProject->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdProject","selIdProject");
        $oAuxField->set_value_to_select($this->get_post("selIdProject"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdProject",tr_pjt_fil_id_project));
        $arFields[] = $oAuxWrapper;
        //id_user_to
        $oUserTo = new ModelUser();
        $arOptions = $oUserTo->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdUserTo","selIdUserTo");
        $oAuxField->set_value_to_select($this->get_post("selIdUserTo"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdUserTo",tr_pjt_fil_id_user_to));
        $arFields[] = $oAuxWrapper;
        //id_user_by
        $oUserBy = new ModelUser();
        $arOptions = $oUserBy->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdUserBy","selIdUserBy");
        $oAuxField->set_value_to_select($this->get_post("selIdUserBy"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdUserBy",tr_pjt_fil_id_user_by));
        $arFields[] = $oAuxWrapper;
        //hours
        $oAuxField = new HelperInputText("txtHours","txtHours");
        $oAuxField->set_value($this->get_post("txtHours"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtHours",tr_pjt_fil_hours));
        $arFields[] = $oAuxWrapper;
        //tasks_to
        $oAuxField = new HelperInputText("txttasksTo","txttasksTo");
        $oAuxField->set_value($this->get_post("txttasksTo"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txttasksTo",tr_pjt_fil_tasks_to));
        $arFields[] = $oAuxWrapper;
        //description
        //$oAuxField = new HelperInputText("txtDescription","txtDescription");
        //$oAuxField->set_value($this->get_post("txtDescription"));
        //$oAuxField->on_entersubmit();
        //$oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtDescription",tr_pjt_fil_description));
        //$arFields[] = $oAuxWrapper;
        //date_open
        $oAuxField = new HelperInputText("txtDateOpen","txtDateOpen");
        $oAuxField->set_value($this->get_post("txtDateOpen"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtDateOpen",tr_pjt_fil_date_open));
        $arFields[] = $oAuxWrapper;
        //date_close
        $oAuxField = new HelperInputText("txtDateClose","txtDateClose");
        $oAuxField->set_value($this->get_post("txtDateClose"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtDateClose",tr_pjt_fil_date_close));
        $arFields[] = $oAuxWrapper;
        //tasks_detail
        $oAuxField = new HelperInputText("txttasksDetail","txttasksDetail");
        $oAuxField->set_value($this->get_post("txttasksDetail"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txttasksDetail",tr_pjt_fil_tasks_detail));
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
        //id_type_priority
        $this->set_filter_value("id_type_priority",$this->get_post("selIdTypePriority"));
        //id_type_status
        $this->set_filter_value("id_type_status",$this->get_post("selIdTypeStatus"));
        //id_type_percent
        $this->set_filter_value("id_type_percent",$this->get_post("selIdTypePercent"));
        //id_project
        $this->set_filter_value("id_project",$this->get_post("selIdProject"));
        //id_user_to
        $this->set_filter_value("id_user_to",$this->get_post("selIdUserTo"));
        //id_user_by
        $this->set_filter_value("id_user_by",$this->get_post("selIdUserBy"));
        //hours
        $this->set_filter_value("hours",$this->get_post("txtHours"));
        //tasks_to
        $this->set_filter_value("tasks_to",$this->get_post("txttasksTo"));
        //description
        //$this->set_filter_value("description",$this->get_post("txtDescription"));
        //date_open
        $this->set_filter_value("date_open",$this->get_post("txtDateOpen"));
        //date_close
        $this->set_filter_value("date_close",$this->get_post("txtDateClose"));
        //tasks_detail
        $this->set_filter_value("tasks_detail",$this->get_post("txttasksDetail"));
    }//set_multiassignfilters_from_post()

    //multiassign_5
    protected function get_multiassign_columns()
    {
        $arColumns["id"] = tr_pjt_col_id;
        //$arColumns["code_erp"] = tr_pjt_col_code_erp;
        //$arColumns["id_type_priority"] = tr_pjt_col_id_type_priority;
        $arColumns["priority"] = tr_pjt_col_id_type_priority;
        //$arColumns["id_type_status"] = tr_pjt_col_id_type_status;
        $arColumns["status"] = tr_pjt_col_id_type_status;
        //$arColumns["id_type_percent"] = tr_pjt_col_id_type_percent;
        $arColumns["percent"] = tr_pjt_col_id_type_percent;
        //$arColumns["id_project"] = tr_pjt_col_id_project;
        $arColumns["project"] = tr_pjt_col_id_project;
        //$arColumns["id_user_to"] = tr_pjt_col_id_user_to;
        $arColumns["userto"] = tr_pjt_col_id_user_to;
        //$arColumns["id_user_by"] = tr_pjt_col_id_user_by;
        $arColumns["userby"] = tr_pjt_col_id_user_by;
        $arColumns["hours"] = tr_pjt_col_hours;
        $arColumns["tasks_to"] = tr_pjt_col_tasks_to;
        //$arColumns["description"] = tr_pjt_col_description;
        $arColumns["date_open"] = tr_pjt_col_date_open;
        $arColumns["date_close"] = tr_pjt_col_date_close;
        $arColumns["tasks_detail"] = tr_pjt_col_tasks_detail;
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
        $this->oProjectTask->set_orderby($this->get_orderby());
        $this->oProjectTask->set_ordertype($this->get_ordertype());
        $this->oProjectTask->set_filters($this->get_filter_searchconfig());
        //hierarchy recover
        //$this->oProjectTask->set_select_user($this->oSessionUser->get_id());
        //RECOVER DATALIST
        $arList = $this->oProjectTask->get_select_all_ids();
        $iRequestPage = $this->get_post("selPage");
        $oPage = new ComponentPage($arList,$iRequestPage);
        $arList = $oPage->get_items_to_show();
        $arList = $this->oProjectTask->get_select_all_by_ids($arList);
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
        $oOpButtons = new AppHelperButtontabs(tr_pjt_entities);
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
        $arButTabs["filters"]=array("href"=>"javascript:reset_filters();","icon"=>"awe-magic","innerhtml"=>tr_pjt_clear_filters);
        $arButTabs["reload"]=array("href"=>"javascript:TfwControl.form_submit();","icon"=>"awe-refresh","innerhtml"=>tr_pjt_refresh);
        $arButTabs["closeme"]=array("href"=>"javascript:closeme();","icon"=>"awe-remove-sign","innerhtml"=>tr_pjt_closeme);
        return $arButTabs;
    }//build_singleassign_buttons()

    //singleassign_2
    protected function load_config_singleassign_filters()
    {
        //id
        $this->set_filter("id","txtId",array("operator"=>"like"));
        //code_erp
        //$this->set_filter("code_erp","txtCodeErp",array("operator"=>"like"));
        //id_type_priority
        $this->set_filter("id_type_priority","selIdTypePriority");
        //id_type_status
        $this->set_filter("id_type_status","selIdTypeStatus");
        //id_type_percent
        $this->set_filter("id_type_percent","selIdTypePercent");
        //id_project
        $this->set_filter("id_project","selIdProject");
        //id_user_to
        $this->set_filter("id_user_to","selIdUserTo");
        //id_user_by
        $this->set_filter("id_user_by","selIdUserBy");
        //hours
        $this->set_filter("hours","txtHours",array("operator"=>"like"));
        //tasks_to
        $this->set_filter("tasks_to","txttasksTo",array("operator"=>"like"));
        //description
        //$this->set_filter("description","txtDescription",array("operator"=>"like"));
        //date_open
        $this->set_filter("date_open","txtDateOpen",array("operator"=>"like"));
        //date_close
        $this->set_filter("date_close","txtDateClose",array("operator"=>"like"));
        //tasks_detail
        $this->set_filter("tasks_detail","txttasksDetail",array("operator"=>"like"));
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
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtId",tr_pjt_fil_id));
        $arFields[] = $oAuxWrapper;
        //code_erp
        //$oAuxField = new HelperInputText("txtCodeErp","txtCodeErp");
        //$oAuxField->set_value($this->get_post("txtCodeErp"));
        //$oAuxField->on_entersubmit();
        //$oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtCodeErp",tr_pjt_fil_code_erp));
        //$arFields[] = $oAuxWrapper;
        //id_type_priority
        $oTypePriority = new ModelProjectArray();
        $arOptions = $oTypePriority->get_picklist_by_type("priority");
        $oAuxField = new HelperSelect($arOptions,"selIdTypePriority","selIdTypePriority");
        $oAuxField->set_value_to_select($this->get_post("selIdTypePriority"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdTypePriority",tr_pjt_fil_id_type_priority));
        $arFields[] = $oAuxWrapper;
        //id_type_status
        $oTypeStatus = new ModelProjectArray();
        $arOptions = $oTypeStatus->get_picklist_by_type("status");
        $oAuxField = new HelperSelect($arOptions,"selIdTypeStatus","selIdTypeStatus");
        $oAuxField->set_value_to_select($this->get_post("selIdTypeStatus"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdTypeStatus",tr_pjt_fil_id_type_status));
        $arFields[] = $oAuxWrapper;
        //id_type_percent
        $oTypePercent = new ModelProjectArray();
        $arOptions = $oTypePercent->get_picklist_by_type("percent");
        $oAuxField = new HelperSelect($arOptions,"selIdTypePercent","selIdTypePercent");
        $oAuxField->set_value_to_select($this->get_post("selIdTypePercent"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdTypePercent",tr_pjt_fil_id_type_percent));
        $arFields[] = $oAuxWrapper;
        //id_project
        $oProject = new ModelProject();
        $arOptions = $oProject->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdProject","selIdProject");
        $oAuxField->set_value_to_select($this->get_post("selIdProject"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdProject",tr_pjt_fil_id_project));
        $arFields[] = $oAuxWrapper;
        //id_user_to
        $oUserTo = new ModelUser();
        $arOptions = $oUserTo->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdUserTo","selIdUserTo");
        $oAuxField->set_value_to_select($this->get_post("selIdUserTo"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdUserTo",tr_pjt_fil_id_user_to));
        $arFields[] = $oAuxWrapper;
        //id_user_by
        $oUserBy = new ModelUser();
        $arOptions = $oUserBy->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdUserBy","selIdUserBy");
        $oAuxField->set_value_to_select($this->get_post("selIdUserBy"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdUserBy",tr_pjt_fil_id_user_by));
        $arFields[] = $oAuxWrapper;
        //hours
        $oAuxField = new HelperInputText("txtHours","txtHours");
        $oAuxField->set_value($this->get_post("txtHours"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtHours",tr_pjt_fil_hours));
        $arFields[] = $oAuxWrapper;
        //tasks_to
        $oAuxField = new HelperInputText("txttasksTo","txttasksTo");
        $oAuxField->set_value($this->get_post("txttasksTo"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txttasksTo",tr_pjt_fil_tasks_to));
        $arFields[] = $oAuxWrapper;
        //description
        //$oAuxField = new HelperInputText("txtDescription","txtDescription");
        //$oAuxField->set_value($this->get_post("txtDescription"));
        //$oAuxField->on_entersubmit();
        //$oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtDescription",tr_pjt_fil_description));
        //$arFields[] = $oAuxWrapper;
        //date_open
        $oAuxField = new HelperInputText("txtDateOpen","txtDateOpen");
        $oAuxField->set_value($this->get_post("txtDateOpen"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtDateOpen",tr_pjt_fil_date_open));
        $arFields[] = $oAuxWrapper;
        //date_close
        $oAuxField = new HelperInputText("txtDateClose","txtDateClose");
        $oAuxField->set_value($this->get_post("txtDateClose"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtDateClose",tr_pjt_fil_date_close));
        $arFields[] = $oAuxWrapper;
        //tasks_detail
        $oAuxField = new HelperInputText("txttasksDetail","txttasksDetail");
        $oAuxField->set_value($this->get_post("txttasksDetail"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txttasksDetail",tr_pjt_fil_tasks_detail));
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
        //id_type_priority
        $this->set_filter_value("id_type_priority",$this->get_post("selIdTypePriority"));
        //id_type_status
        $this->set_filter_value("id_type_status",$this->get_post("selIdTypeStatus"));
        //id_type_percent
        $this->set_filter_value("id_type_percent",$this->get_post("selIdTypePercent"));
        //id_project
        $this->set_filter_value("id_project",$this->get_post("selIdProject"));
        //id_user_to
        $this->set_filter_value("id_user_to",$this->get_post("selIdUserTo"));
        //id_user_by
        $this->set_filter_value("id_user_by",$this->get_post("selIdUserBy"));
        //hours
        $this->set_filter_value("hours",$this->get_post("txtHours"));
        //tasks_to
        $this->set_filter_value("tasks_to",$this->get_post("txttasksTo"));
        //description
        //$this->set_filter_value("description",$this->get_post("txtDescription"));
        //date_open
        $this->set_filter_value("date_open",$this->get_post("txtDateOpen"));
        //date_close
        $this->set_filter_value("date_close",$this->get_post("txtDateClose"));
        //tasks_detail
        $this->set_filter_value("tasks_detail",$this->get_post("txttasksDetail"));
    }//set_singleassignfilters_from_post()

    //singleassign_5
    protected function get_singleassign_columns()
    {
        $arColumns["id"] = tr_pjt_col_id;
        //$arColumns["code_erp"] = tr_pjt_col_code_erp;
        //$arColumns["id_type_priority"] = tr_pjt_col_id_type_priority;
        $arColumns["priority"] = tr_pjt_col_id_type_priority;
        //$arColumns["id_type_status"] = tr_pjt_col_id_type_status;
        $arColumns["status"] = tr_pjt_col_id_type_status;
        //$arColumns["id_type_percent"] = tr_pjt_col_id_type_percent;
        $arColumns["percent"] = tr_pjt_col_id_type_percent;
        //$arColumns["id_project"] = tr_pjt_col_id_project;
        $arColumns["project"] = tr_pjt_col_id_project;
        //$arColumns["id_user_to"] = tr_pjt_col_id_user_to;
        $arColumns["userto"] = tr_pjt_col_id_user_to;
        //$arColumns["id_user_by"] = tr_pjt_col_id_user_by;
        $arColumns["userby"] = tr_pjt_col_id_user_by;
        $arColumns["hours"] = tr_pjt_col_hours;
        $arColumns["tasks_to"] = tr_pjt_col_tasks_to;
        //$arColumns["description"] = tr_pjt_col_description;
        $arColumns["date_open"] = tr_pjt_col_date_open;
        $arColumns["date_close"] = tr_pjt_col_date_close;
        $arColumns["tasks_detail"] = tr_pjt_col_tasks_detail;
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
        $this->oProjectTask->set_orderby($this->get_orderby());
        $this->oProjectTask->set_ordertype($this->get_ordertype());
        $this->oProjectTask->set_filters($this->get_filter_searchconfig());
        $arList = $this->oProjectTask->get_select_all_ids();
        $iRequestPage = $this->get_post("selPage");
        $oPage = new ComponentPage($arList,$iRequestPage);
        $arList = $oPage->get_items_to_show();
        $arList = $this->oProjectTask->get_select_all_by_ids($arList);
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
        $oOpButtons = new AppHelperButtontabs(tr_pjt_entities);
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