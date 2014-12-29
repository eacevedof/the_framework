<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.14
 * @name ControllerUsers
 * @fieldname controller_users.php 
 * @date 29-10-2014 08:56 (SPAIN)
 * @observations: Application Controller
 */
import_model("user,lstn_brief");
import_component("page,validate,filter");
import_helper("form,form_fieldset,form_legend,input_text");
import_helper("label,anchor,table,table_typed,input_password,button_basic,raw,div,javascript");
import_appmain("controller,view,behaviour");
import_apphelper("listactionbar,controlgroup,formactions,buttontabs,formhead,alertdiv");
import_appbehaviour("picklist");

class ControllerUsers extends TheApplicationController
{

    public function __construct()
    {
        $this->sModuleName = "users";
        $this->sTrLabelPrefix = "tr_usr_";
        //console,pgpage,clientdevice,sesuser,aftersucces,currop  | log,session,permission,view
        parent::__construct($this->sModuleName);
        $this->load_pagetitle();
    }

//<editor-fold defaultstate="collapsed" desc="LIST">
    protected function build_listoperation_buttons()
    {
        $arButTabs = array();
        $arButTabs["filters"]=array("href"=>"javascript:reset_filters();","icon"=>"awe-magic","innerhtml"=>tr_usr_clear_filters);
        $arButTabs["reload"]=array("href"=>"javascript:TfwControl.form_submit();","icon"=>"awe-refresh","innerhtml"=>tr_usr_refresh);
        $arButTabs["insert"]=array("href"=>$this->build_url($this->sModuleName,NULL,"insert"),"icon"=>"awe-plus","innerhtml"=>tr_usr_entity_new);
        $arButTabs["multidelete"]=array("href"=>"javascript:multi_delete();","icon"=>"awe-remove","innerhtml"=>tr_usr_delete_selection);
        //$arButTabs["multiquarantine"]=array("href"=>"javascript:multi_quarantine();","icon"=>"awe-remove","innerhtml"=>tr_usr_quarantine);
        //crea ventana
        //$arButTabs["multiassign"]=array("href"=>"javascript:multiassign_window('users',null,'multiassign','users','addsellers');","icon"=>"awe-external-link","innerhtml"=>tr_usr_asign_selection);
        //$arButTabs["singleassign"]=array("href"=>"javascript:single_pick('users','singleassign','txtI','txtI');","icon"=>"awe-external-link","innerhtml"=>tr_usr_asign_selection);
        return $arButTabs;
    }//build_listoperation_buttons()
    
    protected function get_list_filters()
    {
        //Filters use 3 parts, fieldname to asociate with a db field, id control for js and search config to
        //build automatic search query in model
        $this->set_filter("id","txtId",array("operator"=>"likel","value"=>$this->get_post("txtId")));
        $oAuxField = new HelperInputText("txtId","txtId");
        $oAuxField->set_value($this->get_post("txtId"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtId",tr_usr_id));
        $arFields[] = $oAuxWrapper;
        
        $this->set_filter("first_name","txtFirstName",array("operator"=>"likel","value"=>$this->get_post("txtFirstName")));
        $oAuxField = new HelperInputText("txtFirstName","txtFirstName");
        $oAuxField->set_value($this->get_post("txtFirstName"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtFirstName",tr_usr_first_name));
        $arFields[] = $oAuxWrapper;
        
        $this->set_filter("language","selLanguage",array("operator"=>"","value"=>$this->get_post("selLanguage")));
        $oBehaviourPicklist = new AppBehaviourPicklist();
        $oAuxField = new HelperSelect($oBehaviourPicklist->get_languages(),"selLanguage","selLanguage",NULL);
        $oAuxField->set_value_to_select($this->get_post("selLanguage"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField
                ,new HelperLabel("selLanguage",tr_usr_language));
        $arFields[] = $oAuxWrapper;
        
        return $arFields;
    }//get_list_filters()
    
    protected function get_list_columns()
    {
        $arColumns = array("id"=>"Code","code_erp"=>"Cod. QB","description"=>"Desc"
            ,"bo_login"=>"BO Login"); 
        return $arColumns;
    }//get_list_columns()
    
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
        //bugss();
        $oModelUser = new ModelUser();
        $oModelUser->set_orderby($this->get_orderby());
        $oModelUser->set_ordertype($this->get_ordertype());

        //bugss();
        $oModelUser->set_filters($this->get_filter_searchconfig());
        $arList = $oModelUser->get_select_all_ids();
        $iRequestPage = $this->get_post("selPage");
        $oPage = new ComponentPage($arList,$iRequestPage);
        $arList = $oPage->get_items_to_show();
        $arList = $oModelUser->get_select_all_by_ids($arList);
        //TABLE
        $oTableList = new HelperTableTyped($arList,$arColumns);
        //This method adds objects controls to search list form
        $oTableList->set_fields($arObjFilter);
        $oTableList->set_module($this->get_current_module());        
        $oTableList->add_class("table table-striped table-bordered table-condensed");
        $oTableList->set_keyfields(array("id"));
        $oTableList->is_ordenable();
        $oTableList->set_orderby($this->get_orderby());
        $oTableList->set_orderby_type($this->get_ordertype());
        $oTableList->set_column_pickmultiple();//columna checks
        $oTableList->set_column_detail();//detail column
        $oTableList->set_column_delete();
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
        
        //BARRA CRUD
        $oOpButtons = new AppHelperButtontabs(tr_usr_entities);
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
// </editor-fold>

//<editor-fold defaultstate="collapsed" desc="INSERT">
    protected function build_insert_opbuttons()
    {
        $arButTabs = array();
        $arButTabs["list"]=array("href"=>$this->build_url(),"icon"=>"awe-search","innerhtml"=>tr_usr_list);
        return $arButTabs;
    }//build_insert_opbuttons()

    protected function build_insert_fields($usePost=0)
    {   //bugpg();
        //bug($arFields);die;
        $arFields = array(); $oAuxField = null; $oAuxLabel = NULL;
        $oModelUser = new ModelUser();
        $arFields[]= new AppHelperFormhead(tr_usr_entity_new.tr_usr_entity);

        //code_erp
        $oAuxField = new HelperInputText("txtCodeErp","txtCodeErp");
        $oAuxField->add_class("input-medium");
        
        $oAuxField->is_primarykey();
        if($usePost) $oAuxField->set_value($this->get_post("txtCodeErp"));
        //$oAuxField->readonly();
        $oAuxLabel = new HelperLabel("txtCodeErp",tr_usr_code_erp,"lblCodeErp");
        $oAuxLabel->add_class("labelpk");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //First Name
        $oAuxField = new HelperInputText("txtFirstName","txtFirstName");
        $oAuxField->required();
        $oAuxField->add_class("input-large");
        if($usePost) $oAuxField->set_value($this->get_post("txtFirstName"));
        $oAuxLabel = new HelperLabel("txtFirstName",tr_usr_first_name,"lbFirstName");
        $oAuxLabel->add_class("labelreq");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //Last Name
        $oAuxField = new HelperInputText("txtLastName","txtLastName");
        $oAuxField->required();
        $oAuxField->add_class("input-large");
        if($usePost) $oAuxField->set_value($this->get_post("txtLastName"));
        $oAuxLabel = new HelperLabel("txtLastName",tr_usr_last_name,"lbLastName");
        $oAuxLabel->add_class("labelreq");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);  
        
        //Bo Login
        $oAuxField = new HelperInputText("txtBoLogin","txtBoLogin");
        $oAuxField->add_class("input-large");
        if($usePost) $oAuxField->set_value($this->get_post("txtBoLogin"));
        $oAuxLabel = new HelperLabel("txtBoLogin",tr_usr_bo_login,"lblBoLogin");
        $oAuxLabel->add_class("control-label");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);  
        
        //Bo Password
        $oAuxField = new HelperInputPassword("pasBoPassword","pasBoPassword");
        $oAuxField->add_class("input-large");
        $oAuxLabel = new HelperLabel("pasBoPassword",tr_usr_bo_password,"lblBoPassword");
        $oAuxLabel->add_class("control-label");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //Md Login
        $oAuxField = new HelperInputText("txtMdLogin","txtMdLogin");
        $oAuxField->add_class("input-large");
        if($usePost) $oAuxField->set_value($this->get_post("txtMdLogin"));
        $oAuxLabel = new HelperLabel("txtMdLogin",tr_usr_md_login,"lblMdLogin");
        $oAuxLabel->add_class("control-label");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);  
        
        //Md Password
        $oAuxField = new HelperInputPassword("pasMdPassword","pasMdPassword");
        $oAuxField->add_class("input-large");
        $oAuxLabel = new HelperLabel("pasMdPassword",tr_usr_md_password,"lblMdPassword");
        $oAuxLabel->add_class("control-label");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel); 
        
        $oBehaviourPicklist = new AppBehaviourPicklist();
        $arLanguages = $oBehaviourPicklist->get_languages();
        $oAuxField = new HelperSelect($arLanguages,"selLanguage","selLanguage");
        $oAuxField->set_value_to_select($oModelUser->get_language());
        $oAuxField->add_class("input-large");
        $oAuxLabel = new HelperLabel("selLanguage",tr_usr_language,"lblLanguage");
        $oAuxLabel->add_class("control-label");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel); 
        
        $oAuxField = new HelperButtonBasic("butSave",tr_usr_save);
        $oAuxField->add_class("btn btn-primary");
        $oAuxField->set_js_onclick("insert();");
        $arFields[] = new ApphelperFormactions(array($oAuxField));
        //Accion
        $oAuxField = new HelperInputHidden("hidAction","hidAction");
        $arFields[] = $oAuxField;
        $oAuxField = new HelperInputHidden("hidPostback","hidPostback");
        $arFields[] = $oAuxField;        
        return $arFields;
    }//build_insert_fields()

    protected function build_insert_form($usePost=0)
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
        $this->go_to_401($this->oPermission->is_not_insert());
        //Validacion con PHP y JS
        $arFieldsConfig = array();
        $arFieldsConfig["first_name"] = array("id"=>"txtFirstName","label"=>tr_usr_first_name,"length"=>100,"type"=>array("required"));
        $arFieldsConfig["last_name"] = array("id"=>"txtLastName","label"=>tr_usr_last_name,"length"=>100,"type"=>array("required"));

        //bug("updating");
        if($this->is_inserting())
        {
            //array de configuracion length=>i,type=>array("numeric","required")
            $oAlert = new AppHelperAlertdiv();
            $oAlert->use_close_button();
            
            $arFieldsValues = $this->get_fields_from_post();
            $oValidate = new ComponentValidate($arFieldsConfig,$arFieldsValues);
            $arErrData = $oValidate->get_error_field();
            //bug($arErrData); die;
            if($arErrData)
            {
                $oAlert->set_type("e");
                $oAlert->set_title(tr_usr_data_not_saved);
                $oAlert->set_content("Field <b>".$arErrData["label"]."</b> ".$arErrData["message"]);
            }
            else 
            {
                $oModelUser = new ModelUser();
                //bug($oModelUser->get_fields_definition()); die;
                $oModelUser->set_attrib_value($arFieldsValues);
                $oModelUser->set_description($oModelUser->get_first_name()." ".$oModelUser->get_last_name());
                $oModelUser->set_insert_user($this->oSessionUser->get_id());;
                //bug($oModelUser->get_update_user());
                $oModelUser->autoinsert();
                if($oModelUser->is_error())
                {
                    $oAlert->set_type("e");
                    $oAlert->set_title(tr_usr_data_not_saved);
                    $oAlert->set_content(tr_usr_error_trying_to_save);
                }
                else 
                {
                    $oAlert->set_title(tr_usr_data_saved);
                    $this->reset_post();
                }
            }//no error
        }//fin if post action=save

        //Si hay errores se recupera desde post
        if($arErrData) $oForm = $this->build_insert_form(1);
        else $oForm = $this->build_insert_form(); 
        //bug($oForm); die;
        
        $oJavascript = new HelperJavascript();
        $oJavascript->set_validate_config($arFieldsConfig);
        $oJavascript->set_focusid("id_all");
        
        $oOpButtons = new AppHelperButtontabs(tr_usr_entities);
        $oOpButtons->set_tabs($this->build_insert_opbuttons());

        $this->oView->add_var($oOpButtons,"oOpButtons");
        $this->oView->add_var($oAlert,"oAlert");
        $this->oView->add_var($oForm,"oForm");
        $this->oView->add_var($oJavascript,"oJavascript");
        $this->oView->show_page();
    }//insert()
    
// </editor-fold>
   
//<editor-fold defaultstate="collapsed" desc="UPDATE">
    protected function build_update_opbuttons()
    {
        $arButTabs = array();
        $arButTabs["list"]=array("href"=>$this->build_url(),"icon"=>"awe-search","innerhtml"=>tr_usr_list);
        $arButTabs["insert"]=array("href"=>$this->build_url($this->sModuleName,NULL,"insert"),"icon"=>"awe-plus","innerhtml"=>tr_usr_entity_new);
        $arButTabs["delete"]=array("href"=>$this->build_url(),"icon"=>"awe-remove","innerhtml"=>tr_usr_delete);
        return $arButTabs;        
    }//build_update_opbuttons()
    
    protected function build_update_fields($usePost=0)
    {   //bugpg();
        //bug($arFields);die;
        $arFields = array(); $oAuxField = null; $oAuxLabel = NULL;
        $oModelUser = new ModelUser();
        $oModelUser->set_id($this->get_get("id"));
        $oModelUser->load_by_id();
        
        //info 
        $sParams = "id=".$oModelUser->get_id()."&code_erp=".$oModelUser->get_code_erp();
        $sUrlDetail = $this->build_url($this->sModuleName,NULL,"update",$sParams);
        $arButTabs["detail"]=array("href"=>$sUrlDetail,"innerhtml"=>"Detail");
        $oAuxField = new AppHelperButtontabs(tr_usr_detail);
        $oAuxField->no_border_bottom();
        $oAuxField->set_tabs($arButTabs);
        $oAuxField->set_active_tab("detail");       
        $arFields[]=$oAuxField;
        
        $arFields[]= new AppHelperFormhead("User:",$oModelUser->get_id()." - ".$oModelUser->get_description());
        //id
        $oAuxField = new HelperInputText("txtId","txtId",$oModelUser->get_id());
        $oAuxField->add_class("input-small");
        
        $oAuxField->is_primarykey();
        $oAuxField->readonly();
        $oAuxLabel = new HelperLabel("txtId",tr_usr_id,"lblId");
        $oAuxLabel->add_class("labelpk");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //code_erp
        $oAuxField = new HelperInputText("txtCodeErp","txtCodeErp",$oModelUser->get_code_erp());
        $oAuxField->add_class("input-small");
        
        $oAuxField->is_primarykey();
        if($usePost) $oAuxField->set_value($this->get_post("txtCodeErp"));
        //$oAuxField->readonly();
        $oAuxLabel = new HelperLabel("txtCodeErp",tr_usr_code_erp,"lblCodeErp");
        $oAuxLabel->add_class("labelpk");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //First Name
        $oAuxField = new HelperInputText("txtFirstName","txtFirstName",$oModelUser->get_first_name());
        $oAuxField->required();
        $oAuxField->add_class("input-large");
        if($usePost) $oAuxField->set_value($this->get_post("txtFirstName"));
        $oAuxLabel = new HelperLabel("txtFirstName",tr_usr_first_name,"lbFirstName");
        $oAuxLabel->add_class("labelreq");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //Last Name
        $oAuxField = new HelperInputText("txtLastName","txtLastName",$oModelUser->get_last_name());
        $oAuxField->required();
        $oAuxField->add_class("input-large");
        if($usePost) $oAuxField->set_value($this->get_post("txtLastName"));
        $oAuxLabel = new HelperLabel("txtLastName",tr_usr_last_name,"lbLastName");
        $oAuxLabel->add_class("labelreq");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);  
        
        //Bo Login
        $oAuxField = new HelperInputText("txtBoLogin","txtBoLogin",$oModelUser->get_bo_login());
        $oAuxField->add_class("input-large");
        $oAuxLabel = new HelperLabel("txtBoLogin",tr_usr_bo_login,"lblBoLogin");
        $oAuxLabel->add_class("control-label");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);  
        
        //Bo Password
        $oAuxField = new HelperInputPassword("pasBoPassword","pasBoPassword");
        $oAuxField->add_class("input-large");
        $oAuxLabel = new HelperLabel("pasBoPassword",tr_usr_bo_password,"lblBoPassword");
        $oAuxLabel->add_class("control-label");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //Md Login
        $oAuxField = new HelperInputText("txtMdLogin","txtMdLogin",$oModelUser->get_md_login());
        $oAuxField->add_class("input-large");
        $oAuxLabel = new HelperLabel("txtMdLogin",tr_usr_md_login,"lblMdLogin");
        $oAuxLabel->add_class("control-label");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);  
        
        //Md Password
        $oAuxField = new HelperInputPassword("pasMdPassword","pasMdPassword");
        $oAuxField->add_class("input-large");
        $oAuxLabel = new HelperLabel("pasMdPassword",tr_usr_md_password,"lblMdPassword");
        $oAuxLabel->add_class("control-label");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel); 

        //Test single
        $oAuxField = new HelperInputText("Desc","Desc");
        $oAuxField->add_class("input-large");
        $oAuxField->readonly();
        //module,section,view,returnmodule,returnsection,returnview,extra1,extran..
        //?parentmodule=users&parentview=get_list&k=1&k2=2&module=users&view=multiassign&returnmodule=users&returnview=addsellers
        $sUrl = $this->build_url($this->sModuleName,NULL,"singleassign","k=1&k2");
        $oAuxField->set_js_onclick("singleassign_window('$sUrl',1);");
        $oAuxLabel = new HelperLabel("Desc","Desc","lblDesc");
        $oAuxLabel->add_class("control-label");
        $oExtra = new HelperInputHidden("txtCode","txtCode");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel,$oExtra); 
        
        $oBehaviourPicklist = new AppBehaviourPicklist();
        $arLanguages = $oBehaviourPicklist->get_languages();
        $oAuxField = new HelperSelect($arLanguages,"selLanguage","selLanguage");
        $oAuxField->set_value_to_select($oModelUser->get_language());
        $oAuxField->add_class("input-large");
        $oAuxLabel = new HelperLabel("selLanguage",tr_usr_language,"lblLanguage");
        $oAuxLabel->add_class("control-label");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel); 
        
        $oAuxField = new HelperButtonBasic("butSave",tr_usr_save);
        $oAuxField->add_class("btn btn-primary");
        $oAuxField->set_js_onclick("update();");
        $arFields[] = new ApphelperFormactions(array($oAuxField));

        $sRegInfo = $this->get_audit_info($oModelUser->get_insert_user(),$oModelUser->get_insert_date()
         ,$oModelUser->get_update_user(),$oModelUser->get_update_date());
        $arFields[]= new AppHelperFormhead(null,$sRegInfo);
        //Accion
        $oAuxField = new HelperInputHidden("hidAction","hidAction");
        $arFields[] = $oAuxField;
        $oAuxField = new HelperInputHidden("hidPostback","hidPostback");
        $arFields[] = $oAuxField;        
        return $arFields;
    }//build_update_fields()

    protected function build_update_form($usePost=0)
    {
        $id = $this->get_get("id");
        if($id)
        {
            $oForm = new HelperForm("frmUpdate");
            $oForm->add_class("form-horizontal");
            $oForm->add_style("margin-bottom:0");
            $arFields = $this->build_update_fields($usePost);
            $oForm->add_controls($arFields);
            //$oForm->show();
        }
        return $oForm;
    }//build_update_form()
    
    public function update()
    {
        $this->go_to_401($this->oPermission->is_not_update());
        //Validacion con PHP y JS
        $arFieldsConfig = array();
        $arFieldsConfig["first_name"] = array("id"=>"txtFirstName","label"=>tr_usr_first_name,"length"=>100,"type"=>array("required"));
        $arFieldsConfig["last_name"] = array("id"=>"txtLastName","label"=>tr_usr_last_name,"length"=>100,"type"=>array("required"));

        //bug("updating");
        if($this->is_updating())
        {
            //array de configuracion length=>i,type=>array("numeric","required")
            $oAlert = new AppHelperAlertdiv();
            $oAlert->use_close_button();
            
            $arFieldsValues = $this->get_fields_from_post();
            $oValidate = new ComponentValidate($arFieldsConfig,$arFieldsValues);
            $arErrData = $oValidate->get_error_field();
            //bug($arErrData); die;
            if($arErrData)
            {
                $oAlert->set_type("e");
                $oAlert->set_title(tr_usr_data_not_saved);
                $oAlert->set_content("Field <b>".$arErrData["label"]."</b> ".$arErrData["message"]);
            }
            else 
            {
                $oModelUser = new ModelUser();
                //bug($oModelUser->get_fields_definition()); die;
                $oModelUser->set_attrib_value($arFieldsValues);
                $oModelUser->set_description($oModelUser->get_first_name()." ".$oModelUser->get_last_name());
                $oModelUser->set_update_user($this->oSessionUser->get_id());
                //bug($oModelUser->get_update_user());
                $oModelUser->autoupdate();
                if($oModelUser->is_error())
                {
                    $oAlert->set_type("e");
                    $oAlert->set_title(tr_usr_data_not_saved);
                    $oAlert->set_content(tr_usr_error_trying_to_save);
                }
                else 
                {
                    $oAlert->set_title(tr_usr_data_saved);
                    $this->reset_post();
                }
            }//no error
        }//fin if post action=save

        //Si hay errores se recupera desde post
        if($arErrData) $oForm = $this->build_update_form(1);
        else $oForm = $this->build_update_form(); 

        $oJavascript = new HelperJavascript();
        $oJavascript->set_updateaction();
        $oJavascript->set_validate_config($arFieldsConfig);
        $oJavascript->set_focusid("id_all");
        
        $oOpButtons = new AppHelperButtontabs(tr_usr_entities);
        $oOpButtons->set_tabs($this->build_update_opbuttons());

        $this->oView->add_var($oOpButtons,"oOpButtons");
        $this->oView->add_var($oAlert,"oAlert");
        $this->oView->add_var($oForm,"oForm");
        $this->oView->add_var($oJavascript,"oJavascript");
        $this->oView->show_page();
        
    }//update()

//</editor-fold>

//<editor-fold defaultstate="collapsed" desc="DELETE">
    protected function single_delete()
    {
        $id = $this->get_get("id");
        if($id)
        {
            $oModelUser = new ModelUser();
            $oModelUser->set_id($id);
            $oModelUser->autodelete();
            if($oModelUser->is_error())
            {
                $this->set_session_message(tr_usr_error_trying_to_delete);
            }
            else 
            {
                $this->set_session_message(tr_usr_data_deleted);

            }
        }//si existe id
        else
            $this->set_session_message(tr_usr_error_key_not_supplied,"e");
    }//single_delete()
    
    protected function multi_delete()
    {
        $isError = false;
        //Intenta recuperar pkeys sino pasa a id, y en ultimo caso lo que se ha pasado por parametro
        $arKeys = $this->get_listkeys();
        //bug($arKeys);die;
        $oModelUser = new ModelUser();
        foreach($arKeys as $sKey)
        {
            $id = $sKey;
            $oModelUser->set_id($id);
            $oModelUser->autodelete();
            if($oModelUser->is_error())
            {
                $isError = true;
                $this->set_session_message(tr_usr_error_trying_to_delete,"e");
            }
        }
        if(!$isError)
            $this->set_session_message(tr_usr_data_deleted);
        //bug($arKeys); die;
    }//multi_delete()

    public function delete()
    {
        $this->go_to_401($this->oPermission->is_not_delete());
        if($this->is_multidelete())
            $this->multi_delete();
        else
            $this->single_delete();        
        $this->go_to_module("users");
    }//delete()
//</editor-fold>
    
//<editor-fold defaultstate="collapsed" desc="QUARANTINE">
    protected function multi_quarantine()
    {        
        $isError = false;
        //Intenta recuperar pkeys sino pasa a id, y en ultimo caso lo que se ha pasado por parametro
        $arKeys = $this->get_listkeys();
        //bug($arKeys);die;
        $oModelUser = new ModelUser();
        foreach($arKeys as $sKey)
        {
            $id = $sKey;
            $oModelUser->set_id($id);
            $oModelUser->autoquarantine();
            if($oModelUser->is_error())
            {
                $isError = true;
                $this->set_session_message(tr_usr_error_trying_to_delete,"e");
            }
        }
        if(!$isError)
            $this->set_session_message(tr_usr_data_deleted);        
    }//multi_quarantine()
    
    protected function single_quarantine()
    {
        $id = $this->get_get("id");
        if($id)
        {
            $oModelUser = new ModelUser();
            $oModelUser->set_id($id);
            $oModelUser->autoquarantine();
            if($oModelUser->is_error())
                $this->set_session_message(tr_usr_error_trying_to_delete);
            else 
                $this->set_session_message(tr_usr_data_deleted);
        }//si existe id
        else
            $this->set_session_message(tr_usr_error_key_not_supplied,"e");
    }//single_quarantine()
    
    public function quarantine()
    {
        $this->go_to_401($this->oPermission->is_not_quarantine());
        if($this->is_multiquarantine())
            $this->multi_quarantine();
        else
            $this->single_quarantine();
        $this->go_to_module("users");
    }//quarantine()
//</editor-fold>    
    
//<editor-fold defaultstate="collapsed" desc="MULTIASSIGN">
    protected function build_multiassign_buttons()
    {
        $arButTabs = array();
        $arButTabs["filters"]=array("href"=>"javascript:reset_filters();","icon"=>"awe-magic","innerhtml"=>tr_usr_clear_filters);
        $arButTabs["reload"]=array("href"=>"javascript:TfwControl.form_submit();","icon"=>"awe-refresh","innerhtml"=>tr_usr_refresh);
        $arButTabs["multiadd"]=array("href"=>"javascript:multiadd();","icon"=>"awe-external-link","innerhtml"=>"add values to");
        $arButTabs["closeme"]=array("href"=>"javascript:closeme();","icon"=>"awe-remove-sign","innerhtml"=>tr_usr_closeme);
        return $arButTabs;
    }//build_multiassign_buttons()
       
    protected function get_multiassign_filters()
    {
        //Filters use 3 parts, fieldname to asociate with a db field, id control for js and search config to        
        //build automatic search query in model        
        $this->set_filter("id","txtId",array("operator"=>"likel","value"=>$this->get_post("txtId")));
        $oAuxField = new HelperInputText("txtId","txtId");
        $oAuxField->set_value($this->get_post("txtId"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtId",tr_usr_id));
        $arFields[] = $oAuxWrapper;

        $this->set_filter("first_name","txtFirstName",array("operator"=>"likel","value"=>$this->get_post("txtFirstName")));        
        $oAuxField = new HelperInputText("txtFirstName","txtFirstName");
        $oAuxField->set_value($this->get_post("txtFirstName"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtFirstName",tr_usr_first_name));
        $arFields[] = $oAuxWrapper;
        
        $this->set_filter("language","selLanguage",array("operator"=>"","value"=>$this->get_post("selLanguage")));        
        $oBehaviourPicklist = new AppBehaviourPicklist();
        $oAuxField = new HelperSelect($oBehaviourPicklist->get_languages(),"selLanguage","selLanguage",NULL);
        $oAuxField->set_value_to_select($this->get_post("selLanguage"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField
                ,new HelperLabel("selLanguage",tr_usr_language));
        $arFields[] = $oAuxWrapper;
        
        return $arFields;        
    }//get_multiassign_filters()

    protected function get_multiassign_columns()
    {
        $arColumns = array("id"=>"Code","code_erp"=>"Cod. QB","description"=>"Desc"
            ,"bo_login"=>"BO Login");  
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
        //build filters and add data to global arFilterFields y arFilterControls
        $arObjFilter = $this->get_multiassign_filters();
        $arColumns = $this->get_multiassign_columns();
        $oFilter = new ComponentFilter();
        $oFilter->set_fieldnames($this->get_filter_fieldnames());
        //Indica que no se guardara en sesion por nombre de campo sino por nombre de control
        //para esto es necesario respetar el estricto camelcase
        $oFilter->use_field_prefix();
        //Guarda en sesion y post los campos enviados, los de orden y página
        $oFilter->refresh();
        //bugss();
        $oModelUser = new ModelUser();
        $oModelUser->set_orderby($this->get_orderby());
        $oModelUser->set_ordertype($this->get_ordertype());
        
        $oModelUser->set_filters($this->get_filter_searchconfig());
        $arList = $oModelUser->get_select_all_ids();
        $iRequestPage = $this->get_post("selPage");
        $oPage = new ComponentPage($arList,$iRequestPage);
        $arList = $oPage->get_items_to_show();
        $arList = $oModelUser->get_select_all_by_ids($arList);
        
        $oTableAssign = new HelperTableTyped($arList,$arColumns);
        //Controls objects to show as list form
        $oTableAssign->set_fields($arObjFilter);
        $oTableAssign->set_module($this->get_current_module());
        $oTableAssign->add_class("table table-striped table-bordered table-condensed");
        $oTableAssign->set_keyfields(array("id","code_erp"));
        $oTableAssign->set_orderby(array("id","description"));
        $oTableAssign->set_orderby_type(array("asc","desc"));
        $oTableAssign->set_column_pickmultiple();//columna checks
        $oTableAssign->merge_pks();//claves separadas por coma
        $oTableAssign->set_column_picksingle();//crea funcion
        $oTableAssign->set_column_detail();//detail column
        //esto se define en el padre
        //$oTableAssign->set_multiassign(array("keys"=>array("k"=>1,"k2"=>2)));
        $oTableAssign->set_multiadd(array("keys"=>array("k"=>$this->get_get("k"),"k2"=>$this->get_get("k2"))));
        //$oTableAssign->set_column_delete();
        $oTableAssign->set_current_page($oPage->get_current());
        $oTableAssign->set_next_page($oPage->get_next());
        $oTableAssign->set_first_page($oPage->get_first());
        $oTableAssign->set_last_page($oPage->get_last());
        $oTableAssign->set_total_regs($oPage->get_total_regs());
        $oTableAssign->set_total_pages($oPage->get_total());
        
        //BARRA CRUD
        $oOpButtons = new AppHelperButtontabs(tr_usr_entities);
        $oOpButtons->set_tabs($this->build_multiassign_buttons());
        
        $oJavascript = new HelperJavascript();
        $this->get_filter_controls_id();
        $oJavascript->set_focusid("id_all");
        
        $this->oView->add_var($oJavascript,"oJavascript");        
        $this->oView->set_layout("onecolumn");
        $this->oView->add_var($oOpButtons,"oOpButtons");
        $this->oView->add_var($oAlert,"oAlert");
        $this->oView->add_var($oTableAssign,"oTableAssign");;
        $this->oView->show_page();
    }//get_multiassign()
//</editor-fold>
    
//<editor-fold defaultstate="collapsed" desc="SINGLEASSIGN">
    protected function build_singleassign_buttons()
    {
        $arButTabs = array();
        $arButTabs["filters"]=array("href"=>"javascript:reset_filters();","icon"=>"awe-magic","innerhtml"=>tr_usr_clear_filters);
        $arButTabs["reload"]=array("href"=>"javascript:TfwControl.form_submit();","icon"=>"awe-refresh","innerhtml"=>tr_usr_refresh);
        $arButTabs["closeme"]=array("href"=>"javascript:closeme();","icon"=>"awe-remove-sign","innerhtml"=>tr_usr_closeme);
        return $arButTabs;
    }//build_singleassign_buttons()

    protected function get_singleassign_filters()
    {
        //Filters use 3 parts, fieldname to asociate with a db field, id control for js and search config to        
        //build automatic search query in model        
        $this->set_filter("id","txtId",array("operator"=>"likel","value"=>$this->get_post("txtId")));
        $oAuxField = new HelperInputText("txtId","txtId");
        $oAuxField->set_value($this->get_post("txtId"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtId",tr_usr_id));
        $arFields[] = $oAuxWrapper;

        $this->set_filter("first_name","txtFirstName",array("operator"=>"likel","value"=>$this->get_post("txtFirstName")));        
        $oAuxField = new HelperInputText("txtFirstName","txtFirstName");
        $oAuxField->set_value($this->get_post("txtFirstName"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtFirstName",tr_usr_first_name));
        $arFields[] = $oAuxWrapper;
        
        $this->set_filter("language","selLanguage",array("operator"=>"","value"=>$this->get_post("selLanguage")));        
        $oBehaviourPicklist = new AppBehaviourPicklist();
        $oAuxField = new HelperSelect($oBehaviourPicklist->get_languages(),"selLanguage","selLanguage",NULL);
        $oAuxField->set_value_to_select($this->get_post("selLanguage"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField
                ,new HelperLabel("selLanguage",tr_usr_language));
        $arFields[] = $oAuxWrapper;
        
        return $arFields;        
    }//get_multiassign_filters()
    
    protected function get_singleassign_columns()
    {
        $arColumns = array("id"=>"Code","code_erp"=>"Cod. QB","description"=>"Desc"
            ,"bo_login"=>"BO Login");  
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
        
        $arObjFilter = $this->get_multiassign_filters();
        $arColumns = $this->get_singleassign_columns();
        $oFilter = new ComponentFilter();
        $oFilter->set_fieldnames($this->get_filter_fieldnames()); 
        //Indica que no se guardara en sesion por nombre de campo sino por nombre de control
        //para esto es necesario respetar el estricto camelcase
        $oFilter->use_field_prefix();
        //Guarda en sesion y post los campos enviados, los de orden y página
        $oFilter->refresh();
        //bugss();
        $oModelUser = new ModelUser();
        $oModelUser->set_orderby($this->get_orderby());
        $oModelUser->set_ordertype($this->get_ordertype());
        $oModelUser->set_filters($arObjFilter);
        $arList = $oModelUser->get_select_all_ids();
        $iRequestPage = $this->get_post("selPage");
        $oPage = new ComponentPage($arList,$iRequestPage);
        $arList = $oPage->get_items_to_show();
        $arList = $oModelUser->get_select_all_by_ids($arList);

        //TABLA
        $oTableAssign = new HelperTableTyped($arList,$arColumns);
        //Search form objects
        $oTableAssign->set_fields($arObjFilter);
        $oTableAssign->set_module($this->get_current_module());
        $oTableAssign->add_class("table table-striped table-bordered table-condensed");
        $oTableAssign->set_keyfields(array("id","code_erp"));
        $oTableAssign->set_orderby(array("id","description"));
        $oTableAssign->set_orderby_type(array("asc","desc"));
        $oTableAssign->set_column_picksingle();
        $oTableAssign->set_singleadd(array("destkey"=>"txtCode","destdesc"=>"Desc","keys"=>"id","descs"=>"description,bo_login","close"=>1));
        $oTableAssign->set_current_page($oPage->get_current());
        $oTableAssign->set_next_page($oPage->get_next());
        $oTableAssign->set_first_page($oPage->get_first());
        $oTableAssign->set_last_page($oPage->get_last());
        $oTableAssign->set_total_regs($oPage->get_total_regs());
        $oTableAssign->set_total_pages($oPage->get_total());

        //BARRA CRUD
        $oOpButtons = new AppHelperButtontabs(tr_usr_entities);
        $oOpButtons->set_tabs($this->build_singleassign_buttons());
        
        //JAVASCRIPT
        $oJavascript = new HelperJavascript();
        $this->get_filter_controls_id();
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
    
    public function addsellers()
    {
        $sUrl = $this->get_assign_backurl(array("k","k2"));
        if($this->get_get("close"))
            $this->js_colseme_and_parent_refresh();
        else
            $this->js_parent_refresh();
        $this->js_go_to($sUrl);
    }
}//end controller
