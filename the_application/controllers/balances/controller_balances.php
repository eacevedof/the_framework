<?php
/**
 * @author Module Builder 1.1.1
 * @link www.eduardoaf.com
 * @version 1.0.1
 * @name ControllerBalances
 * @file controller_Balances.php   
 * @date 25-08-2014 12:48 (SPAIN)
 * @observations: 
 * @requires:
 */
//TFW
import_component("page,validate,filter,htmlxls");
import_component("baseline,serie","highchart");
import_helper("form,form_fieldset,form_legend,input_text,label,anchor,table,table_typed,textarea");
import_helper("input_date,button_basic,raw,div,javascript");
//APP
import_appcomponent("balances");
import_model("user,vapp_balance");
import_appmain("controller,view,behaviour");
import_appbehaviour("balance");
import_apphelper("listactionbar,controlgroup,formactions,buttontabs,formhead,alertdiv,breadscrumbs,headertabs");

class ControllerBalances extends TheApplicationController
{
    protected $oVappBalance;
    protected $oComponentBalances;
    protected $oBeahviourBalance;
    
    public function __construct()
    {
        $this->sModuleName = "balances";
        $this->sTrLabelPrefix = "tr_mdb_bln_";
        //console,pgpage,clientdevice,sesuser,aftersucces,currop  | log,session,permission,view
        parent::__construct($this->sModuleName);
        $this->load_pagetitle();
        $this->oComponentBalances = new AppComponentBalances();
        $this->oBeahviourBalance = new AppBehaviourBalance();
        $this->oVappBalance = new ModelVappBalance();
        $this->oVappBalance->set_platform($this->oSessionUser->get_platform());

        //$this->oSessionUser->set_dataowner_table($this->oVappBalance->get_table_name());
        //$this->oSessionUser->set_dataowner_tablefield("id_customer");
        //$this->oSessionUser->set_dataowner_keys(array("id"=>$this->oVappBalance->get_id()));
    }

//<editor-fold defaultstate="collapsed" desc="LIST">
    //list_1
    protected function build_list_scrumbs()
    {
        $arLinks = array();
        $sUrlLink = $this->build_url();
        $arLinks["list"]=array("href"=>$sUrlLink,"innerhtml"=>tr_mdb_bln_entities);
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
        $oTabs = new AppHelperHeadertabs($arTabs,"list");
        return $oTabs;
    }

    //list_3
    protected function build_listoperation_buttons()
    {
        $arOpButtons = array();
        $arOpButtons["filters"]=array("href"=>"javascript:reset_filters();","icon"=>"awe-magic","innerhtml"=>tr_mdb_bln_listopbutton_filters);
        $arOpButtons["reload"]=array("href"=>"javascript:TfwControl.form_submit();","icon"=>"awe-refresh","innerhtml"=>tr_mdb_bln_listopbutton_reload);
        $arOpButtons["insert_income"]=array("href"=>$this->build_url($this->sModuleName,"incomes","insert"),"icon"=>"awe-plus","innerhtml"=>tr_mdb_bln_listopbutton_insert_income);
        $arOpButtons["insert_outcome"]=array("href"=>$this->build_url($this->sModuleName,"outcomes","insert"),"icon"=>"awe-plus","innerhtml"=>tr_mdb_bln_listopbutton_insert_outcome);
        $arOpButtons["xls"]=array("href"=>$this->build_url($this->sModuleName,NULL,"get_xls"),"target"=>"blank","icon"=>"awe-plus","innerhtml"=>tr_mdb_bln_listopbutton_xls);
//        if($this->oPermission->is_insert())
//            $arOpButtons["insert"]=array("href"=>$this->build_url($this->sModuleName,NULL,"insert"),"icon"=>"awe-plus","innerhtml"=>tr_mdb_bln_listopbutton_insert);
//        if($this->oPermission->is_quarantine())
//            $arOpButtons["multiquarantine"]=array("href"=>"javascript:multi_quarantine();","icon"=>"awe-remove","innerhtml"=>tr_mdb_bln_listopbutton_multiquarantine);
        //if($this->oPermission->is_delete())
            //$arOpButtons["multidelete"]=array("href"=>"javascript:multi_delete();","icon"=>"awe-remove","innerhtml"=>tr_mdb_bln_listopbutton_multidelete);
        //PICK WINDOWS
        //$arOpButtons["multiassign"]=array("href"=>"javascript:multiassign_window('Balances',null,'multiassign','Balances','addexternaldata');","icon"=>"awe-external-link","innerhtml"=>tr_mdb_bln_listopbutton_multiassign);
        //$arOpButtons["singleassign"]=array("href"=>"javascript:single_pick('Balances','singleassign','txtI','txtI');","icon"=>"awe-external-link","innerhtml"=>tr_mdb_bln_listopbutton_singleassign);
        $oOpButtons = new AppHelperButtontabs(tr_mdb_bln_entities);
        $oOpButtons->set_tabs($arOpButtons);
        return $oOpButtons;
    }//build_listoperation_buttons()

    //list_4
    protected function load_config_list_filters()
    {
//        //in_total
//        $this->set_filter("in_total","txtInTotal",array("operator"=>"like"));
//        //out_total
//        $this->set_filter("out_total","txtOutTotal",array("operator"=>"like"));
//        //total
//        $this->set_filter("total","txtTotal",array("operator"=>"like"));
//        //in_date
//        $this->set_filter("in_date","datInDate",array("operator"=>"like"));
        
        //in_date_start
        $this->set_filter("in_date_start","datInDateStart",array("operator"=>">=","mapping"=>"in_date"));
        //in_date_end
        $this->set_filter("in_date_end","datInDateEnd",array("operator"=>"<=","mapping"=>"in_date"));        
    }//load_config_list_filters()

    //list_5
    protected function set_listfilters_from_post()
    {
//        //in_total
//        $this->set_filter_value("in_total",$this->get_post("txtInTotal"));
//        //out_total
//        $this->set_filter_value("out_total",$this->get_post("txtOutTotal"));
//        //total
//        $this->set_filter_value("total",$this->get_post("txtTotal"));
//        //in_date
//        $this->set_filter_value("in_date",$this->get_post("datInDate"));

        //in_date_start
        $this->set_filter_value("in_date_start",$this->get_post("datInDateStart"));
        //in_date_end
        $this->set_filter_value("in_date_end",$this->get_post("datInDateEnd"));
    }//set_listfilters_from_post()

    //list_6
    protected function get_list_filters()
    {
        //To draw graphics. Necessary for get_list()
        $this->oBeahviourBalance->set_date_start($this->get_post("datInDateStart"));
        $this->oBeahviourBalance->set_date_end($this->get_post("datInDateEnd"));
        
        //CAMPOS
        $arFields = array();
        
        $arTotals = $this->oBeahviourBalance->get_totals();
        
        //in_total
//        $oAuxField = new HelperInputText("txtInTotal","txtInTotal");
//        $oAuxField->set_value($this->get_post("txtInTotal"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtInTotal",tr_mdb_bln_fil_in_total));
//        $arFields[] = $oAuxWrapper;
//        //out_total
//        $oAuxField = new HelperInputText("txtOutTotal","txtOutTotal");
//        $oAuxField->set_value($this->get_post("txtOutTotal"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtOutTotal",tr_mdb_bln_fil_out_total));
//        $arFields[] = $oAuxWrapper;
//        //total
//        $oAuxField = new HelperInputText("txtTotal","txtTotal");
//        $oAuxField->set_value($this->get_post("txtTotal"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtTotal",tr_mdb_bln_fil_total));
//        $arFields[] = $oAuxWrapper;
        //in_date
//        $oAuxField = new HelperInputText("datInDate","datInDate");
//        $oAuxField->set_value($this->get_post("datInDate"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("datInDate",tr_mdb_bln_fil_in_date));
//        $arFields[] = $oAuxWrapper;
        
        //in_date_start
        $oAuxField = new HelperDate("datInDateStart","datInDateStart");
        $oAuxField->set_value($this->get_post("datInDateStart"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("datInDateStart",tr_mdb_bln_fil_in_date_start));
        $arFields[] = $oAuxWrapper;
        
        //in_date_end
        $oAuxField = new HelperDate("datInDateEnd","datInDateEnd");
        $oAuxField->set_value($this->get_post("datInDateEnd"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("datInDateEnd",tr_mdb_bln_fil_in_date_end));
        $arFields[] = $oAuxWrapper;        

        $oAuxField = new HelperRaw($this->oComponentBalances->get_resume($arTotals));
        $arFields[] = $oAuxField;
        
        return $arFields;
    }//get_list_filters()

    //list_7
    protected function get_list_columns()
    {
        $arColumns["in_date"] = tr_mdb_bln_col_in_date;
        $arColumns["in_total"] = tr_mdb_bln_col_in_total;
        $arColumns["out_total"] = tr_mdb_bln_col_out_total;
        $arColumns["total"] = tr_mdb_bln_col_total;
        //$arColumns["in_date_start"] = tr_mdb_bln_col_in_date_start;
        //$arColumns["in_date_end"] = tr_mdb_bln_col_in_date_end;
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

        //Carga en la variable global la configuraciÃ³n de los campos que se utilizarÃ¡n
        //FILTERS
        $this->load_config_list_filters();
        $oFilter = new ComponentFilter();
        $oFilter->set_fieldnames($this->get_filter_fieldnames());
        //Indica que no se guardara en sesion por nombre de campo sino por nombre de control
        //para esto es necesario respetar el estricto camelcase
        $oFilter->use_field_prefix();
        //Guarda en sesion y post los campos enviados, los de orden y pÃ¡gina
        $oFilter->refresh();
        $this->set_listfilters_from_post();

        $arObjFilter = $this->get_list_filters();

        //RECOVER DATALIST
        $this->oVappBalance->set_orderby($this->get_orderby());
        $this->oVappBalance->set_ordertype($this->get_ordertype());
        $arFormat = array("in_date_start"=>"date","in_date_end"=>"date");
        $this->oVappBalance->set_filters($this->get_filter_searchconfig($arFormat));
        //$this->oVappBalance->set_date_start($this->get_post("datInDateStart"));
        //$this->oVappBalance->set_date_end($this->get_post("datInDateEnd"));
                
        //hierarchy recover
        //$this->oVappBalance->set_select_user($this->oSessionUser->get_id());
        $arList = $this->oVappBalance->get_select_all_ids();
        $iRequestPage = $this->get_post("selPage");
        $oPage = new ComponentPage($arList,$iRequestPage);
        $arList = $oPage->get_items_to_show();
        $arList = $this->oVappBalance->get_select_all_by_ids($arList);
        //TABLE
        //This method adds objects controls to search list form
        $oTableList = new HelperTableTyped($arList,$arColumns);
        $oTableList->set_fields($arObjFilter);
        $oTableList->add_class("table table-striped table-bordered table-condensed");
        $oTableList->set_keyfields(array("id"));
        $oTableList->is_ordenable();
        $oTableList->set_orderby($this->get_orderby());
        $oTableList->set_orderby_type($this->get_ordertype());
        //COLUMNS CONFIGURATION
        //if($this->oPermission->is_quarantine()||$this->oPermission->is_delete())
            //$oTableList->set_column_pickmultiple();//checks column
        //if($this->oPermission->is_read())
            //$oTableList->set_column_detail();
        //if($this->oPermission->is_quarantine())
            //$oTableList->set_column_quarantine();
        //if($this->oPermission->is_delete())
            //$oTableList->set_column_delete();
        //$arExtra[] = array("position"=>1,"label"=>"Lines");
        //$oTableList->add_extra_colums($arExtra);
        //$oTableList->set_column_anchor(array("virtual_0"=>array
        //("href"=>"url_lines","innerhtml"=>tr_mdb_bln_order_lines,"class"=>"btn btn-info","icon"=>"awe-info-sign")));
        $arFormat = array("in_total"=>"numeric2","out_total"=>"numeric2","total"=>"numeric2","in_date"=>"date");
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
       
        //GRAPHIC
        $arSumPerMonth = $this->oBeahviourBalance->get_sum_per_month();
        //bug($arSumPerMonth);
        $oGraphic = $this->oComponentBalances->build_graphic($arSumPerMonth);
       
        //VIEW SET
        $this->oView->add_var($oGraphic,"oGraphic");
        $this->oView->add_var($oScrumbs,"oScrumbs");
        $this->oView->add_var($oTabs,"oTabs");
        $this->oView->add_var($oOpButtons,"oOpButtons");
        $this->oView->add_var($oAlert,"oAlert");
        $this->oView->add_var($oJavascript,"oJavascript");
        $this->oView->add_var($oTableList,"oTableList");
        $this->oView->set_path_view("balances/view_index");
        $this->oView->show_page();
    }//get_list()
   
    //list_9
    public function get_xls()
    {
        $this->load_config_list_filters();
        $oFilter = new ComponentFilter();
        //Indica que buscara en session los filtros guardades de 
        $oFilter->set_currenturl("balancesget_list");
        $oFilter->set_fieldnames($this->get_filter_fieldnames());
        $oFilter->use_field_prefix();
        //Guarda en sesion o post los campos enviados, los de orden y pÃ¡gina
        $oFilter->refresh();
        //Asigna valores desde post al array filters[value]
        $this->set_listfilters_from_post();
        
        $this->oVappBalance->set_orderby($this->get_orderby());
        $this->oVappBalance->set_ordertype($this->get_ordertype());
        $arFormat = array("in_date_start"=>"date","in_date_end"=>"date");
        $this->oVappBalance->set_filters($this->get_filter_searchconfig($arFormat));
        
        $arHeader = $this->get_list_columns();
        $arList = $this->oVappBalance->get_select_all_by_ids();
        
        //bug($arList);bug($arHeader);die;
        $oHtmlXls = new ComponentHtmlxls($arList);
        $oHtmlXls->set_header($arHeader);
        $oHtmlXls->set_fieldname(tr_mdb_bln_entities);
        $oHtmlXls->download_xls();
    }
//</editor-fold>

//<editor-fold defaultstate="collapsed" desc="INSERT">
    //insert_1
    protected function build_insert_scrumbs()
    {
        $arLinks = array();
        $sUrlLink = $this->build_url();
        $arLinks["list"]=array("href"=>$sUrlLink,"innerhtml"=>tr_mdb_bln_entities);
        $sUrlLink = $this->build_url($this->sModuleName,NULL,"insert");
        $arLinks["insert"]=array("href"=>$sUrlLink,"innerhtml"=>tr_mdb_bln_entity_insert);
        $oScrumbs = new AppHelperBreadscrumbs($arLinks);
        return $oScrumbs;
    }//build_insert_scrumbs()

    //insert_2
    protected function build_insert_tabs()
    {
        $arTabs = array();
        //$sUrlTab = $this->build_url($this->sModuleName,NULL,"insert");
        //$arTabs["insert1"]=array("href"=>$sUrlTab,"innerhtml"=>tr_mdb_bln_instabs_1);
        //$sUrlTab = $this->build_url($this->sModuleName,NULL,"insert2");
        //$arTabs["insert2"]=array("href"=>$sUrlTab,"innerhtml"=>tr_mdb_bln_instabs_2);
        $oTabs = new AppHelperHeadertabs($arTabs,"insert1");
        return $oTabs;
    }//build_insert_tabs()
    //insert_3
    protected function build_insert_opbuttons()
    {
        $arOpButtons = array();
        $arOpButtons["list"] = array("href"=>$this->build_url(),"icon"=>"awe-search","innerhtml"=>tr_mdb_bln_insopbutton_list);
        //$arOpButtons["extra"] = array("href"=>$this->build_url(),"icon"=>"awe-xxxx","innerhtml"=>tr_mdb_bln_insopbutton_extra1);
        $oOpButtons = new AppHelperButtontabs(tr_mdb_bln_entities);
        $oOpButtons->set_tabs($arOpButtons);
        return $oOpButtons;
    }//build_insert_opbuttons()

    //insert_4
    protected function build_insert_fields($usePost=0)
    {
        $arFields = array(); $oAuxField = NULL; $oAuxLabel = NULL;
        $arFields[]= new AppHelperFormhead(tr_mdb_bln_entity_new);
        //in_total
        $oAuxField = new HelperInputText("txtInTotal","txtInTotal");
        $oAuxField->set_value("0.00");
        if($usePost) $oAuxField->set_value(dbbo_numeric2($this->get_post("txtInTotal")));
        $oAuxLabel = new HelperLabel("txtInTotal",tr_mdb_bln_ins_in_total,"lblInTotal");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //out_total
        $oAuxField = new HelperInputText("txtOutTotal","txtOutTotal");
        $oAuxField->set_value("0.00");
        if($usePost) $oAuxField->set_value(dbbo_numeric2($this->get_post("txtOutTotal")));
        $oAuxLabel = new HelperLabel("txtOutTotal",tr_mdb_bln_ins_out_total,"lblOutTotal");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //total
        $oAuxField = new HelperInputText("txtTotal","txtTotal");
        $oAuxField->set_value("0.00");
        if($usePost) $oAuxField->set_value(dbbo_numeric2($this->get_post("txtTotal")));
        $oAuxLabel = new HelperLabel("txtTotal",tr_mdb_bln_ins_total,"lblTotal");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //in_date
        $oAuxField = new HelperInputText("datInDate","datInDate");
        if($usePost) $oAuxField->set_value($this->get_post("datInDate"));
        $oAuxLabel = new HelperLabel("datInDate",tr_mdb_bln_ins_in_date,"lblInDate");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //SAVE BUTTON
        $oAuxField = new HelperButtonBasic("butSave",tr_mdb_bln_ins_savebutton);
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
        $arFieldsConfig["in_total"] = array("controlid"=>"txtInTotal","label"=>tr_mdb_bln_ins_in_total,"length"=>17,"type"=>array("numeric"));
        $arFieldsConfig["out_total"] = array("controlid"=>"txtOutTotal","label"=>tr_mdb_bln_ins_out_total,"length"=>17,"type"=>array("numeric"));
        $arFieldsConfig["total"] = array("controlid"=>"txtTotal","label"=>tr_mdb_bln_ins_total,"length"=>17,"type"=>array("numeric"));
        $arFieldsConfig["in_date"] = array("controlid"=>"datInDate","label"=>tr_mdb_bln_ins_in_date,"length"=>8,"type"=>array());
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
                $oAlert->set_title(tr_mdb_data_not_saved);
                $oAlert->set_content("Field <b>".$arErrData["label"]."</b> ".$arErrData["message"]);
            }
            else
            {
                //$this->oVappBalance->log_save_insert();
                $this->oVappBalance->set_attrib_value($arFieldsValues);
                $this->oVappBalance->set_insert_user($this->oSessionUser->get_id());
                //$this->oVappBalance->set_platform($this->oSessionUser->get_platform());
                $this->oVappBalance->autoinsert();
                if($this->oVappBalance->is_error())
                {
                    $oAlert->set_type("e");
                    $oAlert->set_title(tr_mdb_data_not_saved);
                    $oAlert->set_content(tr_mdb_error_trying_to_save);
                }
                else//insert ok
                {
                    $this->set_get("id",$this->oVappBalance->get_last_insert_id());
                    $oAlert->set_title(tr_mdb_data_saved);
                    $this->reset_post();
                    //$this->go_to_after_succes_cud();
                }
            }//no error
        }//fin if is_inserting (post action=save)
        //Si hay errores se recupera desde post
        if($arErrData) $oForm = $this->build_insert_form(1);
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
        $this->oView->add_var($oAnchorDown,"oAnchorDown");
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
        $sUrlLink = $this->build_url($this->sModuleName,NULL,"update","id=".$this->get_get("id"));
        $arLinks["detail"]=array("href"=>$sUrlLink,"innerhtml"=>tr_mdb_bln_entity.": ".$this->oVappBalance->get_id()." - ".$this->oVappBalance->get_description());
        $oScrumbs = new AppHelperBreadscrumbs($arLinks);
        return $oScrumbs;
    }//build_update_scrumbs()

    //update_2
    protected function build_update_tabs()
    {
        $arTabs = array();
        $sUrlTab = $this->build_url($this->sModuleName,NULL,"update","id=".$this->get_get("id"));
        $arTabs["detail"]=array("href"=>$sUrlTab,"innerhtml"=>tr_mdb_bln_updtabs_detail);
        //$sUrlTab = $this->build_url($this->sModuleName,"foreignamodule","get_list_by_foreign","id_foreign=".$this->get_get("id_parent_foreign"));
        //$arTabs["foreigndata"]=array("href"=>$sUrlTab,"innerhtml"=>tr_mdb_bln_updtabs_foreigndata);
        $oTabs = new AppHelperHeadertabs($arTabs,"detail");
        return $oTabs;
    }//build_update_tabs()

    //update_3
    protected function build_update_opbuttons()
    {
        $arOpButtons = array();
        if($this->oPermission->is_select())
            $arOpButtons["list"]=array("href"=>$this->build_url(),"icon"=>"awe-search","innerhtml"=>tr_mdb_bln_updopbutton_list);
        //if($this->oPermission->is_insert())
            //$arOpButtons["insert"]=array("href"=>$this->build_url($this->sModuleName,NULL,"insert"),"icon"=>"awe-plus","innerhtml"=>tr_mdb_bln_updopbutton_insert);
        if($this->oPermission->is_quarantine())
            $arOpButtons["delete"]=array("href"=>$this->build_url($this->sModuleName,NULL,"quarantine","id=".$this->get_get("id")),"icon"=>"awe-remove","innerhtml"=>tr_mdb_bln_updopbutton_quarantine);
        //if($this->oPermission->is_delete())
            //$arOpButtons["delete"]=array("href"=>$this->build_url($this->sModuleName,NULL,"delete","id=".$this->get_get("id")),"icon"=>"awe-remove","innerhtml"=>tr_mdb_bln_updopbutton_delete);
        $oOpButtons = new AppHelperButtontabs(tr_mdb_bln_entities);
        $oOpButtons->set_tabs($arOpButtons);
        return $oOpButtons;
    }//build_update_opbuttons()

    //update_4
    protected function build_update_fields($usePost=0)
    {
        $arFields = array(); $oAuxField = NULL; $oAuxLabel = NULL;
        //in_total
        $oAuxField = new HelperInputText("txtInTotal","txtInTotal");
        $oAuxField->set_value(dbbo_numeric2($this->oVappBalance->get_in_total()));
        if($usePost) $oAuxField->set_value(dbbo_numeric2($this->get_post("txtInTotal")));
        $oAuxLabel = new HelperLabel("txtInTotal",tr_mdb_bln_upd_in_total,"lblInTotal");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //out_total
        $oAuxField = new HelperInputText("txtOutTotal","txtOutTotal");
        $oAuxField->set_value(dbbo_numeric2($this->oVappBalance->get_out_total()));
        if($usePost) $oAuxField->set_value(dbbo_numeric2($this->get_post("txtOutTotal")));
        $oAuxLabel = new HelperLabel("txtOutTotal",tr_mdb_bln_upd_out_total,"lblOutTotal");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //total
        $oAuxField = new HelperInputText("txtTotal","txtTotal");
        $oAuxField->set_value(dbbo_numeric2($this->oVappBalance->get_total()));
        if($usePost) $oAuxField->set_value(dbbo_numeric2($this->get_post("txtTotal")));
        $oAuxLabel = new HelperLabel("txtTotal",tr_mdb_bln_upd_total,"lblTotal");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //in_date
        $oAuxField = new HelperInputText("datInDate","datInDate");
        $oAuxField->set_value($this->oVappBalance->get_in_date());
        if($usePost) $oAuxField->set_value($this->get_post("datInDate"));
        $oAuxLabel = new HelperLabel("datInDate",tr_mdb_bln_upd_in_date,"lblInDate");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //BUTTON SAVE
        $oAuxField = new HelperButtonBasic("butSave",tr_mdb_bln_upd_savebutton);
        $oAuxField->add_class("btn btn-primary");
        $oAuxField->set_js_onclick("update();");
        if($this->oPermission->is_update())
            $arFields[] = new ApphelperFormactions(array($oAuxField));
        //AUDIT INFO
        $sRegInfo = $this->get_audit_info($this->oVappBalance->get_insert_user(),$this->oVappBalance->get_insert_date()
        ,$this->oVappBalance->get_update_user(),$this->oVappBalance->get_update_date());
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
        $arFieldsConfig["in_total"] = array("controlid"=>"txtInTotal","label"=>tr_mdb_bln_upd_in_total,"length"=>17,"type"=>array("numeric"));
        $arFieldsConfig["out_total"] = array("controlid"=>"txtOutTotal","label"=>tr_mdb_bln_upd_out_total,"length"=>17,"type"=>array("numeric"));
        $arFieldsConfig["total"] = array("controlid"=>"txtTotal","label"=>tr_mdb_bln_upd_total,"length"=>17,"type"=>array("numeric"));
        $arFieldsConfig["in_date"] = array("controlid"=>"datInDate","label"=>tr_mdb_bln_upd_in_date,"length"=>8,"type"=>array());
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
        $this->go_to_404(!$this->oVappBalance->is_in_table());
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
                $oAlert->set_title(tr_mdb_data_not_saved);
                $oAlert->set_content("Field <b>".$arErrData["label"]."</b> ".$arErrData["message"]);
            }
            else
            {
                $this->oVappBalance->set_attrib_value($arFieldsValues);
                //$this->oVappBalance->set_description($oVappBalance->get_field1()." ".$oVappBalance->get_field2());
                $this->oVappBalance->set_update_user($this->oSessionUser->get_id());
                $this->oVappBalance->autoupdate();
                if($this->oVappBalance->is_error())
                {
                    $oAlert->set_type("e");
                    $oAlert->set_title(tr_mdb_data_not_saved);
                    $oAlert->set_content(tr_mdb_error_trying_to_save);
                }//no error
                else//update ok
                {
                    //$this->oVappBalance->load_by_id();
                    $oAlert->set_title(tr_mdb_data_saved);
                    $this->reset_post();
                    //$this->go_to_after_succes_cud();
                }//error save
            }//error validation
        }//is_updating()
        if($arErrData) $oForm = $this->build_update_form(1);
        else $oForm = $this->build_update_form(); 
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
            $this->oVappBalance->set_id($id);
            $this->oVappBalance->autodelete();
            if($this->oVappBalance->is_error())
            {
                    $this->isError = TRUE;
                    $this->set_session_message(tr_mdb_error_trying_to_delete);
            }
            else
            {
                    $this->set_session_message(tr_mdb_data_deleted);
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
            $this->oVappBalance->set_id($id);
            $this->oVappBalance->autodelete();
            if($this->oVappBalance->is_error())
            {
                    $this->isError = true;
                    $this->set_session_message(tr_mdb_error_trying_to_delete,"e");
            }
        }//foreach arkeys
        if(!$this->isError)
            $this->set_session_message(tr_mdb_data_deleted);
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
            $this->oVappBalance->set_id($id);
            $this->oVappBalance->autoquarantine();
            if($this->oVappBalance->is_error())
                    $this->set_session_message(tr_mdb_error_trying_to_delete);
            else
                    $this->set_session_message(tr_mdb_data_deleted);
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
            $this->oVappBalance->set_id($id);
            $this->oVappBalance->autoquarantine();
            if($this->oVappBalance->is_error())
            {
                    $isError = true;
                    $this->set_session_message(tr_mdb_error_trying_to_delete,"e");
            }
        }
        if(!$isError)
            $this->set_session_message(tr_mdb_data_deleted);
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
        $arOpButtons["filters"]=array("href"=>"javascript:reset_filters();","icon"=>"awe-magic","innerhtml"=>tr_mdb_bln_clear_filters);
        $arOpButtons["reload"]=array("href"=>"javascript:TfwControl.form_submit();","icon"=>"awe-refresh","innerhtml"=>tr_mdb_bln_refresh);
        $arOpButtons["multiadd"]=array("href"=>"javascript:multiadd();","icon"=>"awe-external-link","innerhtml"=>tr_mdb_bln_multiadd);
        $arOpButtons["closeme"]=array("href"=>"javascript:closeme();","icon"=>"awe-remove-sign","innerhtml"=>tr_mdb_bln_closeme);
        $oOpButtons = new AppHelperButtontabs(tr_mdb_bln_entities);
        $oOpButtons->set_tabs($arOpButtons);
        return $oOpButtons;
    }//build_multiassign_buttons()

    //multiassign_2
    protected function load_config_multiassign_filters()
    {
        //in_total
        $this->set_filter("in_total","txtInTotal",array("operator"=>"like"));
        //out_total
        $this->set_filter("out_total","txtOutTotal",array("operator"=>"like"));
        //total
        $this->set_filter("total","txtTotal",array("operator"=>"like"));
        //in_date
        $this->set_filter("in_date","datInDate",array("operator"=>"like"));
    }//load_config_multiassign_filters()

    //multiassign_3
    protected function get_multiassign_filters()
    {
        //CAMPOS
        $arFields = array();
        //in_total
        $oAuxField = new HelperInputText("txtInTotal","txtInTotal");
        $oAuxField->set_value($this->get_post("txtInTotal"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtInTotal",tr_mdb_bln_fil_in_total));
        $arFields[] = $oAuxWrapper;
        //out_total
        $oAuxField = new HelperInputText("txtOutTotal","txtOutTotal");
        $oAuxField->set_value($this->get_post("txtOutTotal"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtOutTotal",tr_mdb_bln_fil_out_total));
        $arFields[] = $oAuxWrapper;
        //total
        $oAuxField = new HelperInputText("txtTotal","txtTotal");
        $oAuxField->set_value($this->get_post("txtTotal"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtTotal",tr_mdb_bln_fil_total));
        $arFields[] = $oAuxWrapper;
        //in_date
        $oAuxField = new HelperInputText("datInDate","datInDate");
        $oAuxField->set_value($this->get_post("datInDate"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("datInDate",tr_mdb_bln_fil_in_date));
        $arFields[] = $oAuxWrapper;
        return $arFields;
    }//get_multiassign_filters()

    //multiassign_4
    protected function set_multiassignfilters_from_post()
    {
        //in_total
        $this->set_filter_value("in_total",$this->get_post("txtInTotal"));
        //out_total
        $this->set_filter_value("out_total",$this->get_post("txtOutTotal"));
        //total
        $this->set_filter_value("total",$this->get_post("txtTotal"));
        //in_date
        $this->set_filter_value("in_date",$this->get_post("datInDate"));
    }//set_multiassignfilters_from_post()

    //multiassign_5
    protected function get_multiassign_columns()
    {
        $arColumns["in_total"] = tr_mdb_bln_col_in_total;
        $arColumns["out_total"] = tr_mdb_bln_col_out_total;
        $arColumns["total"] = tr_mdb_bln_col_total;
        $arColumns["in_date"] = tr_mdb_bln_col_in_date;
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
        //Indica los filtros que se recuperarÃ¡n. Hace un $this->arFilters = arra(fieldname=>value=>..)
        $this->load_config_multiassign_filters();
        $oFilter = new ComponentFilter();
        $oFilter->set_fieldnames($this->get_filter_fieldnames());
        //Indica que no se guardara en sesion por nombre de campo sino por nombre de control
        //para esto es necesario respetar el estricto camelcase
        $oFilter->use_field_prefix();
        //Guarda en sesion y post los campos enviados, los de orden y pÃ¡gina
        $oFilter->refresh();
        $this->set_multiassignfilters_from_post();
        $arObjFilter = $this->get_multiassign_filters();
        $this->oVappBalance->set_orderby($this->get_orderby());
        $this->oVappBalance->set_ordertype($this->get_ordertype());
        $this->oVappBalance->set_filters($this->get_filter_searchconfig());
        //hierarchy recover
        //$this->oVappBalance->set_select_user($this->oSessionUser->get_id());
        //RECOVER DATALIST
        $arList = $this->oVappBalance->get_select_all_ids();
        $iRequestPage = $this->get_post("selPage");
        $oPage = new ComponentPage($arList,$iRequestPage);
        $arList = $oPage->get_items_to_show();
        $arList = $this->oVappBalance->get_select_all_by_ids($arList);
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
        $oOpButtons = new AppHelperButtontabs(tr_mdb_bln_entities);
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
        $arButTabs["filters"]=array("href"=>"javascript:reset_filters();","icon"=>"awe-magic","innerhtml"=>tr_mdb_bln_clear_filters);
        $arButTabs["reload"]=array("href"=>"javascript:TfwControl.form_submit();","icon"=>"awe-refresh","innerhtml"=>tr_mdb_bln_refresh);
        $arButTabs["closeme"]=array("href"=>"javascript:closeme();","icon"=>"awe-remove-sign","innerhtml"=>tr_mdb_bln_closeme);
        return $arButTabs;
    }//build_singleassign_buttons()

    //singleassign_2
    protected function load_config_singleassign_filters()
    {
        //in_total
        $this->set_filter("in_total","txtInTotal",array("operator"=>"like"));
        //out_total
        $this->set_filter("out_total","txtOutTotal",array("operator"=>"like"));
        //total
        $this->set_filter("total","txtTotal",array("operator"=>"like"));
        //in_date
        $this->set_filter("in_date","datInDate",array("operator"=>"like"));
    }//load_config_singleassign_filters()

    //singleassign_3
    protected function get_singleassign_filters()
    {
        //CAMPOS
        $arFields = array();
        //in_total
        $oAuxField = new HelperInputText("txtInTotal","txtInTotal");
        $oAuxField->set_value($this->get_post("txtInTotal"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtInTotal",tr_mdb_bln_fil_in_total));
        $arFields[] = $oAuxWrapper;
        //out_total
        $oAuxField = new HelperInputText("txtOutTotal","txtOutTotal");
        $oAuxField->set_value($this->get_post("txtOutTotal"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtOutTotal",tr_mdb_bln_fil_out_total));
        $arFields[] = $oAuxWrapper;
        //total
        $oAuxField = new HelperInputText("txtTotal","txtTotal");
        $oAuxField->set_value($this->get_post("txtTotal"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtTotal",tr_mdb_bln_fil_total));
        $arFields[] = $oAuxWrapper;
        //in_date
        $oAuxField = new HelperInputText("datInDate","datInDate");
        $oAuxField->set_value($this->get_post("datInDate"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("datInDate",tr_mdb_bln_fil_in_date));
        $arFields[] = $oAuxWrapper;
        return $arFields;
    }//get_singleassign_filters()

    //singleassign_4
    protected function set_singleassignfilters_from_post()
    {
        //in_total
        $this->set_filter_value("in_total",$this->get_post("txtInTotal"));
        //out_total
        $this->set_filter_value("out_total",$this->get_post("txtOutTotal"));
        //total
        $this->set_filter_value("total",$this->get_post("txtTotal"));
        //in_date
        $this->set_filter_value("in_date",$this->get_post("datInDate"));
    }//set_singleassignfilters_from_post()

    //singleassign_5
    protected function get_singleassign_columns()
    {
        $arColumns["in_total"] = tr_mdb_bln_col_in_total;
        $arColumns["out_total"] = tr_mdb_bln_col_out_total;
        $arColumns["total"] = tr_mdb_bln_col_total;
        $arColumns["in_date"] = tr_mdb_bln_col_in_date;
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
        //Indica los filtros que se recuperarÃ¡n. Hace un $this->arFilters = arra(fieldname=>value=>..)
        $this->load_config_singleassign_filters();
        $oFilter = new ComponentFilter();
        $oFilter->set_fieldnames($this->get_filter_fieldnames());
        //Indica que no se guardara en sesion por nombre de campo sino por nombre de control
        //para esto es necesario respetar el estricto camelcase
        $oFilter->use_field_prefix();
        //Guarda en sesion y post los campos enviados, los de orden y pÃ¡gina
        $oFilter->refresh();
        $this->set_singleassignfilters_from_post();
        $arObjFilter = $this->get_singleassign_filters();
        $this->oVappBalance->set_orderby($this->get_orderby());
        $this->oVappBalance->set_ordertype($this->get_ordertype());
        $this->oVappBalance->set_filters($this->get_filter_searchconfig());
        $arList = $this->oVappBalance->get_select_all_ids();
        $iRequestPage = $this->get_post("selPage");
        $oPage = new ComponentPage($arList,$iRequestPage);
        $arList = $oPage->get_items_to_show();
        $arList = $this->oVappBalance->get_select_all_by_ids($arList);
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
        $oOpButtons = new AppHelperButtontabs(tr_mdb_bln_entities);
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
