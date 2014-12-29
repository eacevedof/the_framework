<?php
/**
* @author Module Builder 1.0.2
* @link www.eduardoaf.com
* @version 1.1.0
* @name ControllerProducts
* @file controller_products.php    
* @date 27-10-2013 09:54 (SPAIN)
* @observations: 
* @requires:
*/
//TFW
import_component("page,validate,filter");
import_helper("form,form_fieldset,form_legend,input_text,label,anchor,table,table_typed");
import_helper("input_password,button_basic,raw,div,javascript,image");
//APP
import_model("user,product,product_family,product_array,order_head,picture");
import_appmain("controller,view,behaviour");
import_appbehaviour("picklist");
import_apphelper("listactionbar,controlgroup,formactions,buttontabs,formhead,alertdiv,breadscrumbs,headertabs");

class ControllerProducts extends TheApplicationController
{
    private $oProduct;
    private $oOrderHead;
    
    public function __construct()
    {
        $this->sModuleName = "products";
        //console,pgpage,clientdevice,sesuser,aftersucces,currop  | log,session,permission,view
        parent::__construct($this->sModuleName);
        $this->arAfterSuccessCUD["insert"] = "get_list";
        
        $this->oProduct = new ModelProduct();
        $this->oProduct->set_platform($this->oSessionUser->get_platform());
        if($this->is_inget("id"))
        {
            $this->oProduct->set_id($this->get_get("id"));
            $this->oProduct->load_by_id();
        }        
        
        if($this->is_get("id_order_head"))
        {
            $this->oOrderHead = new ModelOrderHead();
            $this->oOrderHead->set_platform($this->oSessionUser->get_platform());
            $this->oOrderHead->set_id($this->get_get("id_order_head"));
            $this->oOrderHead->load_by_id();
        }
    }

//<editor-fold defaultstate="collapsed" desc="LIST">
    //list_1
    private function build_list_scrumbs()
    {   
        $arLinks = array();
        $sUrlTab = $this->build_url();
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
        //if($this->oPermission->is_delete())
            //$arButTabs["multidelete"]=array("href"=>"javascript:multi_delete();","icon"=>"awe-remove","innerhtml"=>tr_delete_selection);
        //crea ventana
        //$arButTabs["multiassign"]=array("href"=>"javascript:multiassign_window('product',null,'multiassign','product','addexternaldata');","icon"=>"awe-external-link","innerhtml"=>tr_asign_selection);
        //$arButTabs["singleassign"]=array("href"=>"javascript:single_pick('product','singleassign','txtI','txtI');","icon"=>"awe-external-link","innerhtml"=>tr_asign_selection);
        $oOpButtons = new AppHelperButtontabs(tr_entities);
        $oOpButtons->set_tabs($arOpButtons);
        return $oOpButtons;
    }//build_listoperation_buttons()

    //list_4
    private function load_config_list_filters()
    {
        //id
        $this->set_filter("id","txtId",array("operator"=>"like"));
        //code_erp
        $this->set_filter("code_erp","txtCodeErp",array("operator"=>"like"));
        //prar.description AS volume
        $this->set_filter("volume","selVolume",array("operator"=>"like"));
        //description
        $this->set_filter("description","txtDescription",array("operator"=>"like"));
        //name
        $this->set_filter("name","txtName",array("operator"=>"like"));        
        //id_type_size
//        $this->set_filter("id_type_size","selIdTypeSize",array());        
        //id_product_family
//        $this->set_filter("id_product_family","selIdProductFamily",array());
        //id_type_container
//        $this->set_filter("id_type_container","selIdTypeContainer",array());
        //price_cost
//        $this->set_filter("price_cost","txtPriceCost",array("operator"=>"like"));
        //price_regular
//        $this->set_filter("price_regular","txtPriceRegular",array("operator"=>"like"));
        //price_wholesale
//        $this->set_filter("price_wholesale","txtPriceWholesale",array("operator"=>"like"));
        //price_custom
//        $this->set_filter("price_custom","txtPriceCustom",array("operator"=>"like"));
        //observation
//        $this->set_filter("observation","txtObservation",array("operator"=>"like"));
        //web_keywords
//        $this->set_filter("web_keywords","txtWebKeywords",array("operator"=>"like"));
        //lookup_words
//        $this->set_filter("lookup_words","txtLookupWords",array("operator"=>"like"));    
    }//load_config_list_filters    
    
    //list_5
    private function set_listfilters_from_post()
    {
        //id
        $this->set_filter_value("id",$this->get_post("txtId"));
        //code_erp
        $this->set_filter_value("code_erp",$this->get_post("txtCodeErp"));        
        //prar.description AS volume
        $this->set_filter_value("volume",$this->get_post("selVolume"));
        //description
        $this->set_filter_value("description",$this->get_post("txtDescription"));
        //name
        $this->set_filter_value("name",$this->get_post("txtName"));
        //id_type_size
//        $this->set_filter_value("id_type_size",$this->get_post("selIdTypeSize"));        
        //id_product_family
//        $this->set_filter_value("id_product_family",$this->get_post("selIdProductFamily"));
        //id_type_container
//        $this->set_filter_value("id_type_container",$this->get_post("selIdTypeContainer"));
        //price_cost
//        $this->set_filter_value("price_cost",$this->get_post("txtPriceCost"));
        //price_regular
//        $this->set_filter_value("price_regular",$this->get_post("txtPriceRegular"));
        //price_wholesale
//        $this->set_filter_value("price_wholesale",$this->get_post("txtPriceWholesale"));
        //price_custom
//        $this->set_filter_value("price_custom",$this->get_post("txtPriceCustom"));
        //observation
//        $this->set_filter_value("observation",$this->get_post("txtObservation"));
        //web_keywords
//        $this->set_filter_value("web_keywords",$this->get_post("txtWebKeywords"));
        //lookup_words
//        $this->set_filter_value("lookup_words",$this->get_post("txtLookupWords"));        
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
        $oAuxField = new HelperInputText("txtCodeErp","txtCodeErp");
        $oAuxField->set_value($this->get_post("txtCodeErp"));
        $oAuxField->on_entersubmit();        
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtCodeErp",tr_fil_code_erp));
        $arFields[] = $oAuxWrapper;        
        //prar.description as volume
        $oModelProductArray = new ModelProductArray();
        $arOptions = $oModelProductArray->get_picklist_custom("id_tosave","description","type='volume'");
        $oAuxField = new HelperSelect($arOptions,"selVolume","selVolume");
        $oAuxField->set_value_to_select($this->get_post("selVolume"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selVolume",tr_fil_id_type_size));
        $arFields[] = $oAuxWrapper;        
        //name
        $oAuxField = new HelperInputText("txtName","txtName");
        $oAuxField->set_value($this->get_post("txtName"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtName",tr_fil_name));
        $arFields[] = $oAuxWrapper;
        //description
//        $oAuxField = new HelperInputText("txtDescription","txtDescription");
//        $oAuxField->set_value($this->get_post("txtDescription"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtDescription",tr_fil_description));
//        $arFields[] = $oAuxWrapper;
        //observation
//        $oAuxField = new HelperInputText("txtObservation","txtObservation");
//        $oAuxField->set_value($this->get_post("txtObservation"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtObservation",tr_fil_observation));
//        $arFields[] = $oAuxWrapper;
        //web_keywords
//        $oAuxField = new HelperInputText("txtWebKeywords","txtWebKeywords");
//        $oAuxField->set_value($this->get_post("txtWebKeywords"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtWebKeywords",tr_fil_web_keywords));
//        $arFields[] = $oAuxWrapper;
        //lookup_words
//        $oAuxField = new HelperInputText("txtLookupWords","txtLookupWords");
//        $oAuxField->set_value($this->get_post("txtLookupWords"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtLookupWords",tr_fil_lookup_words));
//        $arFields[] = $oAuxWrapper;
        
        //id_type_container
//        $oModelProductArray = new ModelProductArray();
//        $arOptions = $oModelProductArray->get_picklist_by_type("container");
//        $oAuxField = new HelperSelect($arOptions,"selIdTypeContainer","selIdTypeContainer");
//        $oAuxField->set_value_to_select($this->get_post("selIdTypeContainer"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdTypeContainer",tr_fil_id_type_container));
//        $arFields[] = $oAuxWrapper;
        //id_type_size
//        $oModelProductArray = new ModelProductArray();
//        $arOptions = $oModelProductArray->get_picklist_by_type("size");
//        $oAuxField = new HelperSelect($arOptions,"selIdTypeSize","selIdTypeSize");
//        $oAuxField->set_value_to_select($this->get_post("selIdTypeSize"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdTypeSize",tr_fil_id_type_size));
//        $arFields[] = $oAuxWrapper;
        //id_product_family
//        $oModelProductFamily = new ModelProductFamily();
//        $arOptions = $oModelProductFamily->get_picklist();
//        $oAuxField = new HelperSelect($arOptions,"selIdProductFamily","selIdProductFamily");
//        $oAuxField->set_value_to_select($this->get_post("selIdProductFamily"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdProductFamily",tr_fil_id_product_family));
//        $arFields[] = $oAuxWrapper;
        //price_cost
//        $oAuxField = new HelperInputText("txtPriceCost","txtPriceCost");
//        $oAuxField->set_value($this->get_post("txtPriceCost"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtPriceCost",tr_fil_price_cost));
//        $arFields[] = $oAuxWrapper;
        //price_regular
//        $oAuxField = new HelperInputText("txtPriceRegular","txtPriceRegular");
//        $oAuxField->set_value($this->get_post("txtPriceRegular"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtPriceRegular",tr_fil_price_regular));
//        $arFields[] = $oAuxWrapper;
        //price_wholesale
//        $oAuxField = new HelperInputText("txtPriceWholesale","txtPriceWholesale");
//        $oAuxField->set_value($this->get_post("txtPriceWholesale"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtPriceWholesale",tr_fil_price_wholesale));
//        $arFields[] = $oAuxWrapper;
        //price_custom
//        $oAuxField = new HelperInputText("txtPriceCustom","txtPriceCustom");
//        $oAuxField->set_value($this->get_post("txtPriceCustom"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtPriceCustom",tr_fil_price_custom));
//        $arFields[] = $oAuxWrapper;

        return $arFields;
    }//get_list_filters()

    //list_7
    private function get_list_columns()
    {
        $arColumns = array();
        $arColumns["id"] = tr_col_id;
        $arColumns["name"] = tr_col_name;
        $arColumns["volume"] = tr_col_volume;
        $arColumns["price_cost"] = tr_col_price_cost;
        $arColumns["price_wholesale"] = tr_col_price_wholesale;
        $arColumns["price_custom"] = tr_col_price_custom;
        $arColumns["price_regular"] = tr_col_price_regular;        
//        $arColumns["code_erp"] = tr_col_code_erp;
//        $arColumns["id_type_container"] = tr_col_id_type_container;
//        $arColumns["id_type_size"] = tr_col_id_type_size;
//        $arColumns["id_product_family"] = tr_col_id_product_family;
//        $arColumns["lookup_words"] = tr_col_lookup_words;
//        $arColumns["description"] = tr_col_description;
//        $arColumns["observation"] = tr_col_observation;
//        $arColumns["web_keywords"] = tr_col_web_keywords;
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
        $oFilter->set_fieldnames($this->get_filter_fieldnames());
        //Indica que no se guardara en sesion por nombre de campo sino por nombre de control
        //para esto es necesario respetar el estricto camelcase
        $oFilter->use_field_prefix();
        //Guarda en sesion y post los campos enviados, los de orden y página
        $oFilter->refresh();
        $this->set_listfilters_from_post();
        $arObjFilter = $this->get_list_filters();

        //RECOVER DATALIST
        $this->oProduct->set_orderby($this->get_orderby());
        $this->oProduct->set_ordertype($this->get_ordertype());
        //bug($this->get_filter_searchconfig());
        $this->oProduct->set_filters($this->get_filter_searchconfig());
        $arList = $this->oProduct->get_select_all_ids();
        $iRequestPage = $this->get_post("selPage");
        $oPage = new ComponentPage($arList,$iRequestPage,NULL,40);
        $arList = $oPage->get_items_to_show();
        $arList = $this->oProduct->get_select_all_by_ids($arList);
        //bug($arList[0]);die;
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
        //parametros a pasar al popup
        //$oTableList->set_multiassign(array("keys"=>array("k"=>1,"k2"=>2)));
        $arExtra[] = array("position"=>9,"label"=>tr_pt_col_image);
        
        $oImage = new HelperImage();
        $oImage->set_id("img%id%");
        $oImage->set_src("%uri_thumb%");
        //$oImage->set_alt("%img_title%");
        //$oImage->set_title("%source_filename%");
        $oImage->add_style("width:50px");
        $oImage->add_style("width:50px");
        
        $oAnchor = new HelperAnchor();
        $oAnchor->set_href("%uri_href%");
        $oAnchor->set_target("%target%");
        $oAnchor->set_innerhtml($oImage->get_html());
        //bugss();
        $oTableList->add_extra_colums($arExtra);
        $oTableList->set_column_raw(array("virtual_0"=>$oAnchor));
        
        $arFormat = array("price_cost"=>"numeric2","price_wholesale"=>"numeric2","price_regular"=>"numeric2","price_custom"=>"numeric2");
        $oTableList->set_format_columns($arFormat);        
        $oTableList->set_current_page($oPage->get_current());
        $oTableList->set_next_page($oPage->get_next());
        $oTableList->set_first_page($oPage->get_first());
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
        $arLinks = array();
        $sUrlTab = $this->build_url();
        $arLinks["list"]=array("href"=>$sUrlTab,"innerhtml"=>tr_entities);
        $sUrlTab = $this->build_url($this->sModuleName,NULL,"insert");
        $arLinks["insert"]=array("href"=>$sUrlTab,"innerhtml"=>tr_entity_insert);
        $oScrumbs = new AppHelperBreadscrumbs($arLinks);
        return $oScrumbs;
    }//build_insert_scrumbs()
    
    //insert_2
    private function build_insert_opbuttons()
    {
        $arOpButtons = array();
        $arOpButtons["list"]=array("href"=>$this->build_url(),"icon"=>"awe-search","innerhtml"=>tr_list);
        $oOpButtons = new AppHelperButtontabs(tr_entities);
        $oOpButtons->set_tabs($arOpButtons);
        return $oOpButtons;
    }//build_insert_opbuttons()

    //insert_3
    private function get_insert_validate()
    {
        $arFieldsConfig = array();
        //$arFieldsConfig["id"] = array("controlid"=>"txtId","label"=>tr_ins_id,"length"=>9,"type"=>array("numeric","required"));
        $arFieldsConfig["code_erp"] = array("controlid"=>"txtCodeErp","label"=>tr_ins_code_erp,"length"=>25,"type"=>array());
        $arFieldsConfig["id_type_container"] = array("controlid"=>"selIdTypeContainer","label"=>tr_ins_id_type_container,"length"=>4,"type"=>array("required"));
        $arFieldsConfig["id_type_size"] = array("controlid"=>"selIdTypeSize","label"=>tr_ins_id_type_size,"length"=>4,"type"=>array("required"));
        $arFieldsConfig["id_product_family"] = array("controlid"=>"selIdProductFamily","label"=>tr_ins_id_product_family,"length"=>9,"type"=>array("required"));
        $arFieldsConfig["price_cost"] = array("controlid"=>"txtPriceCost","label"=>tr_ins_price_cost,"length"=>9,"type"=>array("numeric"));
        $arFieldsConfig["price_regular"] = array("controlid"=>"txtPriceRegular","label"=>tr_ins_price_regular,"length"=>9,"type"=>array("numeric"));
        $arFieldsConfig["price_wholesale"] = array("controlid"=>"txtPriceWholesale","label"=>tr_ins_price_wholesale,"length"=>9,"type"=>array("numeric"));
        $arFieldsConfig["price_custom"] = array("controlid"=>"txtPriceCustom","label"=>tr_ins_price_custom,"length"=>9,"type"=>array("numeric"));
        //$arFieldsConfig["description"] = array("controlid"=>"txtDescription","label"=>tr_ins_description,"length"=>200,"type"=>array());
        $arFieldsConfig["name"] = array("controlid"=>"txtName","label"=>tr_ins_name,"length"=>100,"type"=>array("required"));
        $arFieldsConfig["observation"] = array("controlid"=>"txtObservation","label"=>tr_ins_observation,"length"=>100,"type"=>array());
        $arFieldsConfig["web_keywords"] = array("controlid"=>"txtWebKeywords","label"=>tr_ins_web_keywords,"length"=>250,"type"=>array());
        $arFieldsConfig["lookup_words"] = array("controlid"=>"txtLookupWords","label"=>tr_ins_lookup_words,"length"=>100,"type"=>array());
        return $arFieldsConfig;
    }//get_insert_validate
    
    //insert_4
    private function build_insert_fields($usePost=0)
    {
        $arFields = array(); $oAuxField = null; $oAuxLabel = NULL;
        $arFields[]= new AppHelperFormhead(tr_new.tr_entity);

        //id_type_container
        $oModelProductArray = new ModelProductArray();
        $arOptions = $oModelProductArray->get_picklist_by_type("container");
        $oAuxField = new HelperSelect($arOptions,"selIdTypeContainer","selIdTypeContainer");
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdTypeContainer"));
        $oAuxLabel = new HelperLabel("selIdTypeContainer",tr_ins_id_type_container,"lblIdTypeContainer");
        //$oAuxField->readonly(); $oAuxField->add_class("readonly");
        $oAuxLabel->add_class("labelreq");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
 
        //id_type_size
        $oModelProductArray = new ModelProductArray();
        $arOptions = $oModelProductArray->get_picklist_by_type("size");
        $oAuxField = new HelperSelect($arOptions,"selIdTypeSize","selIdTypeSize");
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdTypeSize"));
        $oAuxLabel = new HelperLabel("selIdTypeSize",tr_ins_id_type_size,"lblIdTypeSize");
        //$oAuxField->readonly(); $oAuxField->add_class("readonly");
        $oAuxLabel->add_class("labelreq");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //id_product_family
        $oModelProductFamily = new ModelProductFamily();
        $arOptions = $oModelProductFamily->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdProductFamily","selIdProductFamily");
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdProductFamily"));
        $oAuxLabel = new HelperLabel("selIdProductFamily",tr_ins_id_product_family,"lblIdProductFamily");
        //$oAuxField->readonly(); $oAuxField->add_class("readonly");
        $oAuxLabel->add_class("labelreq");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //name
        $oAuxField = new HelperInputText("txtName","txtName");
        if($usePost) $oAuxField->set_value($this->get_post("txtName"));
        $oAuxLabel = new HelperLabel("txtName",tr_ins_name,"lblName");
        //$oAuxField->readonly(); $oAuxField->add_class("readonly");
        $oAuxLabel->add_class("labelreq");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //price_cost
        $oAuxField = new HelperInputText("txtPriceCost","txtPriceCost");
        $oAuxField->set_value("0.00");
        if($usePost) $oAuxField->set_value($this->get_post("txtPriceCost"));
        $oAuxLabel = new HelperLabel("txtPriceCost",tr_ins_price_cost,"lblPriceCost");
        //$oAuxField->readonly(); $oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //price_regular
        $oAuxField = new HelperInputText("txtPriceRegular","txtPriceRegular");
        $oAuxField->set_value("0.00");
        if($usePost) $oAuxField->set_value($this->get_post("txtPriceRegular"));
        $oAuxLabel = new HelperLabel("txtPriceRegular",tr_ins_price_regular,"lblPriceRegular");
        //$oAuxField->readonly(); $oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //price_wholesale
        $oAuxField = new HelperInputText("txtPriceWholesale","txtPriceWholesale");
        $oAuxField->set_value("0.00");
        if($usePost) $oAuxField->set_value($this->get_post("txtPriceWholesale"));
        $oAuxLabel = new HelperLabel("txtPriceWholesale",tr_ins_price_wholesale,"lblPriceWholesale");
        //$oAuxField->readonly(); $oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //price_custom
        $oAuxField = new HelperInputText("txtPriceCustom","txtPriceCustom");
        $oAuxField->set_value("0.00");
        if($usePost) $oAuxField->set_value($this->get_post("txtPriceCustom"));
        $oAuxLabel = new HelperLabel("txtPriceCustom",tr_ins_price_custom,"lblPriceCustom");
        //$oAuxField->readonly(); $oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //observation
        $oAuxField = new HelperInputText("txtObservation","txtObservation");
        if($usePost) $oAuxField->set_value($this->get_post("txtObservation"));
        $oAuxLabel = new HelperLabel("txtObservation",tr_ins_observation,"lblObservation");
        //$oAuxField->readonly(); $oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //id
//        $oAuxField = new HelperInputText("txtId","txtId");
//        $oAuxField->is_primarykey();
//        if($usePost) $oAuxField->set_value($this->get_post("txtId"));
//        $oAuxLabel = new HelperLabel("txtId",tr_ins_id,"lblId");
//        //$oAuxField->readonly(); $oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //description
//        $oAuxField = new HelperInputText("txtDescription","txtDescription");
//        if($usePost) $oAuxField->set_value($this->get_post("txtDescription"));
//        $oAuxLabel = new HelperLabel("txtDescription",tr_ins_description,"lblDescription");
//        //$oAuxField->readonly(); $oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //code_erp
//        $oAuxField = new HelperInputText("txtCodeErp","txtCodeErp");
//        if($usePost) $oAuxField->set_value($this->get_post("txtCodeErp"));
//        $oAuxLabel = new HelperLabel("txtCodeErp",tr_ins_code_erp,"lblCodeErp");        
//        //$oAuxField->readonly(); $oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //web_keywords
//        $oAuxField = new HelperInputText("txtWebKeywords","txtWebKeywords");
//        if($usePost) $oAuxField->set_value($this->get_post("txtWebKeywords"));
//        $oAuxLabel = new HelperLabel("txtWebKeywords",tr_ins_web_keywords,"lblWebKeywords");
//        //$oAuxField->readonly(); $oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //lookup_words
//        $oAuxField = new HelperInputText("txtLookupWords","txtLookupWords");
//        if($usePost) $oAuxField->set_value($this->get_post("txtLookupWords"));
//        $oAuxLabel = new HelperLabel("txtLookupWords",tr_ins_lookup_words,"lblLookupWords");
//        //$oAuxField->readonly(); $oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //SAVE BUTTON
        $oAuxField = new HelperButtonBasic("butSave",tr_ins_savebutton);
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
    }//build_insert_form()

    //insert_6
    public function insert()
    {
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
                //$this->oProduct->log_save_insert();                
                $this->oProduct->set_attrib_value($arFieldsValues);
                $this->oProduct->set_insert_user($this->oSessionUser->get_id());;
                $this->oProduct->autoinsert();
                if($this->oProduct->is_error())
                {
                    $oAlert->set_type("e");
                    $oAlert->set_title(tr_data_not_saved);
                    $oAlert->set_content(tr_error_trying_to_save);
                }
                else //insert ok
                {
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
        $arLinks = array();
        $sUrlTab = $this->build_url();
        $arLinks["list"]=array("href"=>$sUrlTab,"innerhtml"=>tr_entities);
        $sUrlTab = $this->build_url($this->sModuleName,NULL,"update","id=".$this->get_get("id"));
        $arLinks["detail"]=array("href"=>$sUrlTab,"innerhtml"=>tr_entity.": ".$this->oProduct->get_id()." - ".$this->oProduct->get_description());

        $oScrumbs = new AppHelperBreadscrumbs($arLinks);
        return $oScrumbs;
    }//build_update_scrumbs()

    //update_2
    private function build_update_tabs()
    {
        $arTabs = array();
        $sUrlTab = $this->build_url($this->sModuleName,NULL,"update","id=".$this->get_get("id"));
        $arTabs["detail"]=array("href"=>$sUrlTab,"innerhtml"=>tr_detail);
        $sUrlTab = $this->build_url($this->sModuleName,"pictures","get_list","id_product=".$this->get_get("id"));
        $arTabs["pictures"]=array("href"=>$sUrlTab,"innerhtml"=>"Pictures");        
        $oTabs = new AppHelperHeadertabs($arTabs,"detail");
        return $oTabs;
    }//build_update_tabs()    
    
    //update_3
    private function build_update_opbuttons()
    {
        $arOpButtons = array();
        if($this->oPermission->is_select())
            $arOpButtons["list"]=array("href"=>$this->build_url(),"icon"=>"awe-search","innerhtml"=>tr_list);
        if($this->oPermission->is_insert())
            $arOpButtons["insert"]=array("href"=>$this->build_url($this->sModuleName,"pictures","insert","id_product=".$this->oProduct->get_id()),"icon"=>"awe-plus","innerhtml"=>tr_pt_upd_addpicture);
        //if($this->oPermission->is_insert())
            //$arOpButtons["insert"]=array("href"=>$this->build_url($this->sModuleName,NULL,"insert"),"icon"=>"awe-plus","innerhtml"=>tr_new);
        if($this->oPermission->is_quarantine())
            $arOpButtons["delete"]=array("href"=>$this->build_url($this->sModuleName,NULL,"quarantine","id=".$this->get_get("id")),"icon"=>"awe-remove","innerhtml"=>tr_delete);
        //if($this->oPermission->is_delete())
            //$arOpButtons["delete"]=array("href"=>$this->build_url($this->sModuleName,NULL,"delete","id=".$this->get_get("id")),"icon"=>"awe-remove","innerhtml"=>tr_delete);
        $oOpButtons = new AppHelperButtontabs(tr_entities);
        $oOpButtons->set_tabs($arOpButtons);
        return $oOpButtons;
    }//build_update_opbuttons()
    
    //update_4
    private function get_update_validate()
    {
        //Validacion con PHP y JS
        $arFieldsConfig = array();
        $arFieldsConfig["id"] = array("controlid"=>"txtId","label"=>tr_ins_id,"length"=>9,"type"=>array("numeric","required"));
        $arFieldsConfig["code_erp"] = array("controlid"=>"txtCodeErp","label"=>tr_ins_code_erp,"length"=>25,"type"=>array());
        $arFieldsConfig["id_type_container"] = array("controlid"=>"selIdTypeContainer","label"=>tr_ins_id_type_container,"length"=>4,"type"=>array("required"));
        $arFieldsConfig["id_type_size"] = array("controlid"=>"selIdTypeSize","label"=>tr_ins_id_type_size,"length"=>4,"type"=>array("required"));
        $arFieldsConfig["id_product_family"] = array("controlid"=>"selIdProductFamily","label"=>tr_ins_id_product_family,"length"=>9,"type"=>array("required"));
        $arFieldsConfig["price_cost"] = array("controlid"=>"txtPriceCost","label"=>tr_ins_price_cost,"length"=>9,"type"=>array("numeric","required"));
        $arFieldsConfig["price_regular"] = array("controlid"=>"txtPriceRegular","label"=>tr_ins_price_regular,"length"=>9,"type"=>array("numeric","required"));
        $arFieldsConfig["price_wholesale"] = array("controlid"=>"txtPriceWholesale","label"=>tr_ins_price_wholesale,"length"=>9,"type"=>array("numeric","required"));
        $arFieldsConfig["price_custom"] = array("controlid"=>"txtPriceCustom","label"=>tr_ins_price_custom,"length"=>9,"type"=>array("numeric","required"));
        $arFieldsConfig["description"] = array("controlid"=>"txtDescription","label"=>tr_ins_description,"length"=>200,"type"=>array());
        $arFieldsConfig["name"] = array("controlid"=>"txtName","label"=>tr_ins_name,"length"=>100,"type"=>array("required"));
        $arFieldsConfig["observation"] = array("controlid"=>"txtObservation","label"=>tr_ins_observation,"length"=>100,"type"=>array());
        $arFieldsConfig["web_keywords"] = array("controlid"=>"txtWebKeywords","label"=>tr_ins_web_keywords,"length"=>250,"type"=>array());
        $arFieldsConfig["lookup_words"] = array("controlid"=>"txtLookupWords","label"=>tr_ins_lookup_words,"length"=>100,"type"=>array());
        return $arFieldsConfig;
    }//get_update_validate
    
    //update_5
    private function build_update_fields($usePost=0)
    {
        $arFields = array(); $oAuxField = NULL; $oAuxLabel = NULL; $oGroup = NULL;
        //id
        $oAuxField = new HelperInputText("txtId","txtId");
        $oAuxField->is_primarykey();
        $oAuxField->set_value($this->get_post("txtId"));
        $oAuxField->set_value($this->oProduct->get_id());
        if($usePost) $oAuxField->set_value($this->get_post("txtId"));
        $oAuxLabel = new HelperLabel("txtId",tr_upd_id,"lblId");
        $oAuxLabel->add_class("labelpk");
        $oAuxField->readonly(); $oAuxField->add_class("readonly");
        $oGroup = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        $oGroup->set_span(4);
        $arFields[] = $oGroup;
        
        //code_erp
        $oAuxField = new HelperInputText("txtCodeErp","txtCodeErp");
        $oAuxField->set_value($this->oProduct->get_code_erp());
        if($usePost) $oAuxField->set_value($this->get_post("txtCodeErp"));
        $oAuxLabel = new HelperLabel("txtCodeErp",tr_upd_code_erp,"lblCodeErp");
        $oAuxField->readonly(); $oAuxField->add_class("readonly");
        $oGroup = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        $oGroup->set_span(4);
        $arFields[] = $oGroup;
        
        //id_type_container
        $oModelProductArray = new ModelProductArray();
        $arOptions = $oModelProductArray->get_picklist_by_type("container");
        $oAuxField = new HelperSelect($arOptions,"selIdTypeContainer","selIdTypeContainer");
        $oAuxField->set_value_to_select($this->oProduct->get_id_type_container());
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdTypeContainer"));
        $oAuxLabel = new HelperLabel("selIdTypeContainer",tr_upd_id_type_container,"lblIdTypeContainer");
        $oAuxLabel->add_class("labelreq");
        //$oAuxField->readonly(); $oAuxField->add_class("readonly");
        $oGroup = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        $oGroup->set_span(4);
        $arFields[] = $oGroup;
        
        //id_type_size
        $oModelProductArray = new ModelProductArray();
        $arOptions = $oModelProductArray->get_picklist_by_type("size");
        $oAuxField = new HelperSelect($arOptions,"selIdTypeSize","selIdTypeSize");
        $oAuxField->set_value_to_select($this->oProduct->get_id_type_size());
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdTypeSize"));
        $oAuxLabel = new HelperLabel("selIdTypeSize",tr_upd_id_type_size,"lblIdTypeSize");
        $oAuxLabel->add_class("labelreq");
        //$oAuxField->readonly(); $oAuxField->add_class("readonly");
        $oGroup = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        $oGroup->set_span(4);
        $arFields[] = $oGroup;
        
        //id_product_family
        $oModelProductFamily = new ModelProductFamily();
        $arOptions = $oModelProductFamily->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdProductFamily","selIdProductFamily");
        $oAuxField->set_value_to_select($this->oProduct->get_id_product_family());
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdProductFamily"));
        $oAuxLabel = new HelperLabel("selIdProductFamily",tr_upd_id_product_family,"lblIdProductFamily");
        $oAuxLabel->add_class("labelreq");
        //$oAuxField->readonly(); $oAuxField->add_class("readonly");
        $oGroup = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        $oGroup->set_span(4);
        $arFields[] = $oGroup;
        
        //name
        $oAuxField = new HelperInputText("txtName","txtName");
        $oAuxField->set_value($this->oProduct->get_name());
        if($usePost) $oAuxField->set_value($this->get_post("txtName"));
        $oAuxLabel = new HelperLabel("txtName",tr_upd_name,"lblName");
        $oAuxLabel->add_class("labelreq");
        //$oAuxField->readonly(); $oAuxField->add_class("readonly");
        $oGroup = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        $oGroup->set_span(4);
        $arFields[] = $oGroup;
        
        //price_cost
        $oAuxField = new HelperInputText("txtPriceCost","txtPriceCost");
        $oAuxField->set_value(dbbo_numeric2($this->oProduct->get_price_cost()));
        if($usePost) $oAuxField->set_value($this->get_post("txtPriceCost"));
        $oAuxLabel = new HelperLabel("txtPriceCost",tr_upd_price_cost,"lblPriceCost");
        //$oAuxField->readonly(); $oAuxField->add_class("readonly");
        $oGroup = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        $oGroup->set_span(4);
        $arFields[] = $oGroup;
        
        //price_regular
        $oAuxField = new HelperInputText("txtPriceRegular","txtPriceRegular");
        $oAuxField->set_value(dbbo_numeric2($this->oProduct->get_price_regular()));
        if($usePost) $oAuxField->set_value($this->get_post("txtPriceRegular"));
        $oAuxLabel = new HelperLabel("txtPriceRegular",tr_upd_price_regular,"lblPriceRegular");
        //$oAuxField->readonly(); $oAuxField->add_class("readonly");
        $oGroup = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        $oGroup->set_span(4);
        $arFields[] = $oGroup;
        
        //price_wholesale
        $oAuxField = new HelperInputText("txtPriceWholesale","txtPriceWholesale");
        $oAuxField->set_value(dbbo_numeric2($this->oProduct->get_price_wholesale()));
        if($usePost) $oAuxField->set_value($this->get_post("txtPriceWholesale"));
        $oAuxLabel = new HelperLabel("txtPriceWholesale",tr_upd_price_wholesale,"lblPriceWholesale");
        //$oAuxField->readonly(); $oAuxField->add_class("readonly");
        $oGroup = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        $oGroup->set_span(4);
        $arFields[] = $oGroup;
        
        //price_custom
        $oAuxField = new HelperInputText("txtPriceCustom","txtPriceCustom");
        $oAuxField->set_value(dbbo_numeric2($this->oProduct->get_price_custom()));
        if($usePost) $oAuxField->set_value($this->get_post("txtPriceCustom"));
        $oAuxLabel = new HelperLabel("txtPriceCustom",tr_upd_price_custom,"lblPriceCustom");
        //$oAuxField->readonly(); $oAuxField->add_class("readonly");
        $oGroup = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        $oGroup->set_span(4);
        $arFields[] = $oGroup;
        
        //description
        $oAuxField = new HelperInputText("txtDescription","txtDescription");
        $oAuxField->set_value($this->oProduct->get_description());
        if($usePost) $oAuxField->set_value($this->get_post("txtDescription"));
        $oAuxLabel = new HelperLabel("txtDescription",tr_upd_description,"lblDescription");
        $oAuxField->readonly(); $oAuxField->add_class("readonly");
        $oGroup = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        $oGroup->set_span(4);
        $arFields[] = $oGroup;

        //observation
        $oAuxField = new HelperInputText("txtObservation","txtObservation");
        $oAuxField->set_value($this->oProduct->get_observation());
        if($usePost) $oAuxField->set_value($this->get_post("txtObservation"));
        $oAuxLabel = new HelperLabel("txtObservation",tr_upd_observation,"lblObservation");
        //$oAuxField->readonly(); $oAuxField->add_class("readonly");
        $oGroup = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        $oGroup->set_span(4);
        $arFields[] = $oGroup;
        
        //default image
        $oAuxField = new HelperImage();
        $idEntityProduct = 4;
        $oPicture = new ModelPicture();
        $oPicture->set_id_type_entity($idEntityProduct);
        $oPicture->set_id_entity($this->oProduct->get_id());
        $oPicture->load_default_by_entity();
        $sSrc = "/images/custom/no_image_large.png";
        $sTitle = "no image";
        if($oPicture->get_id())
        {    
            $sSrc = "/images/pictures/products/".$oPicture->get_folder()."/".$oPicture->get_filename();
            $sTitle = $oPicture->get_filename();
        }
        $oAuxField->set_src($sSrc);
        $oAuxField->set_title($sTitle);
        
        $oGroup = new ApphelperControlGroup($oAuxField,NULL);
        $oGroup->set_span(4);
        $arFields[] = $oGroup;        
        //web_keywords
//        $oAuxField = new HelperInputText("txtWebKeywords","txtWebKeywords");
//        $oAuxField->set_value($this->oProduct->get_web_keywords());
//        if($usePost) $oAuxField->set_value($this->get_post("txtWebKeywords"));
//        $oAuxLabel = new HelperLabel("txtWebKeywords",tr_upd_web_keywords,"lblWebKeywords");
//        //$oAuxField->readonly(); $oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

        //lookup_words
//        $oAuxField = new HelperInputText("txtLookupWords","txtLookupWords");
//        $oAuxField->set_value($this->oProduct->get_lookup_words());
//        if($usePost) $oAuxField->set_value($this->get_post("txtLookupWords"));
//        $oAuxLabel = new HelperLabel("txtLookupWords",tr_upd_lookup_words,"lblLookupWords");
//        //$oAuxField->readonly(); $oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //BUTTON SAVE
        $oAuxField = new HelperButtonBasic("butSave",tr_upd_savebutton);
        $oAuxField->add_class("btn btn-primary");
        $oAuxField->set_js_onclick("update();");
        if($this->oPermission->is_update())
            $arFields[] = new ApphelperFormactions(array($oAuxField));
        //AUDIT INFO
        $sRegInfo = $this->get_audit_info($this->oProduct->get_insert_user(),$this->oProduct->get_insert_date()
        ,$this->oProduct->get_update_user(),$this->oProduct->get_update_date());
        $arFields[]= new AppHelperFormhead(null,$sRegInfo);
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
	$id = $this->oProduct->get_id();
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
                $this->oProduct->set_attrib_value($arFieldsValues);
                $this->oProduct->set_update_user($this->oSessionUser->get_id());
                $this->oProduct->autoupdate();
                if($this->oProduct->is_error())
                {
                    $oAlert->set_type("e");
                    $oAlert->set_title(tr_data_not_saved);
                    $oAlert->set_content(tr_error_trying_to_save);
                }//no error
                else//update ok
                {
                    //$this->oProduct->load_by_id();
                    $oAlert->set_title(tr_data_saved);
                    $this->reset_post();
                    $this->go_to_after_succes_cud();                    
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
        //$oJavascript->set_focusid("id_all");
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
    private function single_delete()
    {
        $id = $this->get_get("id");
        if($id)
        {
            $this->oProduct->set_id($id);
            $this->oProduct->autodelete();
            if($this->oProduct->is_error())
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

    private function multi_delete()
    {
        //Intenta recuperar pkeys sino pasa a recuperar el id. En ultimo caso lo que se ha pasado por parametro
        $arKeys = $this->get_listkeys();
        foreach($arKeys as $sKey)
        {
            $id = $sKey;
            $this->oProduct->set_id($id);
            $this->oProduct->autodelete();
            if($this->oProduct->is_error())
            {
                $this->isError = TRUE;
                $this->set_session_message(tr_error_trying_to_delete,"e");
            }
        }
        if(!$this->isError)
            $this->set_session_message(tr_data_deleted);
    }//multi_delete()

    public function delete()
    {
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
        else
            $this->go_to_list();
    }//delete()
//</editor-fold>

//<editor-fold defaultstate="collapsed" desc="QUARANTINE">
    private function multi_quarantine()
    {
        $isError = false;
        //Intenta recuperar pkeys sino pasa a id, y en ultimo caso lo que se ha pasado por parametro
        $arKeys = $this->get_listkeys();
        $oModelProduct = new ModelProduct();
        foreach($arKeys as $sKey)
        {
                $id = $sKey;
                $this->oProduct->set_id($id);
                $this->oProduct->autoquarantine();
                if($this->oProduct->is_error())
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
                $oModelProduct = new ModelProduct();
                $this->oProduct->set_id($id);
                $this->oProduct->autoquarantine();
                if($this->oProduct->is_error())
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
    //multiassign_1
    private function build_multiassign_buttons()
    {
        $arOpButtons = array();
        $arOpButtons["filters"]=array("href"=>"javascript:reset_filters();","icon"=>"awe-magic","innerhtml"=>tr_clear_filters);
        $arOpButtons["reload"]=array("href"=>"javascript:TfwControl.form_submit();","icon"=>"awe-refresh","innerhtml"=>tr_refresh);
        $arOpButtons["multiadd"]=array("href"=>"javascript:multiadd();","icon"=>"awe-external-link","innerhtml"=>"add values to");
        $arOpButtons["closeme"]=array("href"=>"javascript:closeme();","icon"=>"awe-remove-sign","innerhtml"=>tr_closeme);
        $oOpButtons = new AppHelperButtontabs(tr_entities);
        $oOpButtons->set_tabs($arOpButtons);
        return $oOpButtons;
    }//build_multiassign_buttons()

    //multiassign_2
    private function load_config_multiassign_filters()
    {
        //id
        $this->set_filter("id","txtId",array("operator"=>"like"));
        //prar.description AS volume
        $this->set_filter("volume","selVolume",array("operator"=>"like"));
        //description
        $this->set_filter("description","txtDescription",array("operator"=>"like"));
        //name
        $this->set_filter("name","txtName",array("operator"=>"like"));        
//        //code_erp
//        $this->set_filter("code_erp","txtCodeErp",array("operator"=>"like"));
        //id_type_size
//        $this->set_filter("id_type_size","selIdTypeSize",array());        
        //id_product_family
//        $this->set_filter("id_product_family","selIdProductFamily",array());
        //id_type_container
//        $this->set_filter("id_type_container","selIdTypeContainer",array());
        //price_cost
//        $this->set_filter("price_cost","txtPriceCost",array("operator"=>"like"));
        //price_regular
//        $this->set_filter("price_regular","txtPriceRegular",array("operator"=>"like"));
        //price_wholesale
//        $this->set_filter("price_wholesale","txtPriceWholesale",array("operator"=>"like"));
        //price_custom
//        $this->set_filter("price_custom","txtPriceCustom",array("operator"=>"like"));
        //observation
//        $this->set_filter("observation","txtObservation",array("operator"=>"like"));
        //web_keywords
//        $this->set_filter("web_keywords","txtWebKeywords",array("operator"=>"like"));
        //lookup_words
//        $this->set_filter("lookup_words","txtLookupWords",array("operator"=>"like"));        
    }
    
    //multiassign_3
    private function set_multiassignfilters_from_post()
    {
        //id
        $this->set_filter_value("id",$this->get_post("txtId"));
        //prar.description AS volume
        $this->set_filter_value("volume",$this->get_post("selVolume"));
        //description
        $this->set_filter_value("description",$this->get_post("txtDescription"));
        //name
        $this->set_filter_value("name",$this->get_post("txtName"));
//        //code_erp
//        $this->set_filter_value("code_erp",$this->get_post("txtCodeErp"));
        //id_type_size
//        $this->set_filter_value("id_type_size",$this->get_post("selIdTypeSize"));        
        //id_product_family
//        $this->set_filter_value("id_product_family",$this->get_post("selIdProductFamily"));
        //id_type_container
//        $this->set_filter_value("id_type_container",$this->get_post("selIdTypeContainer"));
        //price_cost
//        $this->set_filter_value("price_cost",$this->get_post("txtPriceCost"));
        //price_regular
//        $this->set_filter_value("price_regular",$this->get_post("txtPriceRegular"));
        //price_wholesale
//        $this->set_filter_value("price_wholesale",$this->get_post("txtPriceWholesale"));
        //price_custom
//        $this->set_filter_value("price_custom",$this->get_post("txtPriceCustom"));
        //observation
//        $this->set_filter_value("observation",$this->get_post("txtObservation"));
        //web_keywords
//        $this->set_filter_value("web_keywords",$this->get_post("txtWebKeywords"));
        //lookup_words
//        $this->set_filter_value("lookup_words",$this->get_post("txtLookupWords"));        
    }
    
    //multiassign_4
    private function get_multiassign_filters()
    {
        //CAMPOS
        //id
        $oAuxField = new HelperInputText("txtId","txtId");
        $oAuxField->set_value($this->get_post("txtId"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtId",tr_fil_id));
        $arFields[] = $oAuxWrapper;
        //code_erp
        $oAuxField = new HelperInputText("txtCodeErp","txtCodeErp");
        $oAuxField->set_value($this->get_post("txtCodeErp"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtCodeErp",tr_fil_code_erp));
        $arFields[] = $oAuxWrapper;        
        //prar.description as volume
        $oModelProductArray = new ModelProductArray();
        $arOptions = $oModelProductArray->get_picklist_custom("id_tosave","description","type='volume'");
        $oAuxField = new HelperSelect($arOptions,"selVolume","selVolume");
        $oAuxField->set_value_to_select($this->get_post("selVolume"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selVolume",tr_fil_id_type_size));
        $arFields[] = $oAuxWrapper;
        //name
        $oAuxField = new HelperInputText("txtName","txtName");
        $oAuxField->set_value($this->get_post("txtName"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtName",tr_fil_name));
        $arFields[] = $oAuxWrapper;
        //is_free (virtual)
        $arOptions = array(""=>tr_none,"YES"=>"YES");
        $oAuxField = new HelperSelect($arOptions,"selFreeLine","selFreeLine");
        $oAuxField->set_value_to_select($this->get_post("selFreeLine"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selFreeLine",tr_fil_is_free));
        $arFields[] = $oAuxWrapper;        

        //id_type_size
//        $oModelProductArray = new ModelProductArray();
//        $arOptions = $oModelProductArray->get_picklist_by_type("size");
//        $oAuxField = new HelperSelect($arOptions,"selIdTypeSize","selIdTypeSize");
//        $oAuxField->set_value_to_select($this->get_post("selIdTypeSize"));
//        $oAuxField->set_postback();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdTypeSize",tr_fil_id_type_size));
//        $arFields[] = $oAuxWrapper;                
        //id_product_family
//        $oModelProductFamily = new ModelProductFamily();
//        $arOptions = $oModelProductFamily->get_picklist();
//        $oAuxField = new HelperSelect($arOptions,"selIdProductFamily","selIdProductFamily");
//        $oAuxField->set_value_to_select($this->get_post("selIdProductFamily"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdProductFamily",tr_fil_id_product_family));
//        $arFields[] = $oAuxWrapper;
        //id_type_container
//        $oModelProductArray = new ModelProductArray();
//        $arOptions = $oModelProductArray->get_picklist_by_type("container");
//        $oAuxField = new HelperSelect($arOptions,"selIdTypeContainer","selIdTypeContainer");
//        $oAuxField->set_value_to_select($this->get_post("selIdTypeContainer"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdTypeContainer",tr_fil_id_type_container));
//        $arFields[] = $oAuxWrapper;
        //price_cost
//        $oAuxField = new HelperInputText("txtPriceCost","txtPriceCost");
//        $oAuxField->set_value($this->get_post("txtPriceCost"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtPriceCost",tr_fil_price_cost));
//        $arFields[] = $oAuxWrapper;
        //price_regular
//        $oAuxField = new HelperInputText("txtPriceRegular","txtPriceRegular");
//        $oAuxField->set_value($this->get_post("txtPriceRegular"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtPriceRegular",tr_fil_price_regular));
//        $arFields[] = $oAuxWrapper;
        //price_wholesale
//        $oAuxField = new HelperInputText("txtPriceWholesale","txtPriceWholesale");
//        $oAuxField->set_value($this->get_post("txtPriceWholesale"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtPriceWholesale",tr_fil_price_wholesale));
//        $arFields[] = $oAuxWrapper;
        //price_custom
//        $oAuxField = new HelperInputText("txtPriceCustom","txtPriceCustom");
//        $oAuxField->set_value($this->get_post("txtPriceCustom"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtPriceCustom",tr_fil_price_custom));
//        $arFields[] = $oAuxWrapper;
        //description
//        $oAuxField = new HelperInputText("txtDescription","txtDescription");
//        $oAuxField->set_value($this->get_post("txtDescription"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtDescription",tr_fil_description));
//        $arFields[] = $oAuxWrapper;
        //observation
//        $oAuxField = new HelperInputText("txtObservation","txtObservation");
//        $oAuxField->set_value($this->get_post("txtObservation"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtObservation",tr_fil_observation));
//        $arFields[] = $oAuxWrapper;
        //web_keywords
//        $oAuxField = new HelperInputText("txtWebKeywords","txtWebKeywords");
//        $oAuxField->set_value($this->get_post("txtWebKeywords"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtWebKeywords",tr_fil_web_keywords));
//        $arFields[] = $oAuxWrapper;
        //lookup_words
//        $oAuxField = new HelperInputText("txtLookupWords","txtLookupWords");
//        $oAuxField->set_value($this->get_post("txtLookupWords"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtLookupWords",tr_fil_lookup_words));
//        $arFields[] = $oAuxWrapper;
        return $arFields;
    }//get_multiassign_filters()

    //multiassign_5
    private function get_multiassign_columns()
    {
        $arColumns = array
        (
            "id"=>tr_col_id
            ,"code_erp"=>tr_col_code_erp
            ,"name"=>tr_col_name
            //,"price_regular"=>tr_col_price_regular
            ,"price_wholesale"=>tr_col_price_wholesale
            ,"price_custom"=>tr_col_price_custom            
            //,"id_type_container"=>tr_col_id_type_container
            //,"id_type_size"=>tr_col_id_type_size
            //,"id_product_family"=>tr_col_id_product_family
            //,"price_cost"=>tr_col_price_cost
            //,"observation"=>tr_col_observation
            //,"web_keywords"=>tr_col_web_keywords
            //,"lookup_words"=>tr_col_lookup_words
        );
        if($this->oOrderHead->get_id_type_payment()=="4" //COD
                || $this->oOrderHead->get_id_type_payment()=="5" )//ACCOUNT
            //col wholesale
            unset($arColumns["price_custom"]);
        else //CUSTOM PRICE (6)
            unset($arColumns["price_wholesale"]);
            //col custom        
        return $arColumns;
    }//get_multiassign_columns()

    //multiassign_6
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
        $arColumns = $this->get_multiassign_columns(); 
        //Indica los filtros que se recuperarán. Hace un $this->arFilters = arra(fieldname=>value=>..)
        $this->load_config_multiassign_filters();
        //bugss("productsmultiassign");
        $oFilter = new ComponentFilter();
        //bug($this->get_filter_fieldnames(),"get_filter_fields");
        $oFilter->set_fieldnames($this->get_filter_fieldnames());
        //Indica que no se guardara en sesion por nombre de campo sino por nombre de control
        //para esto es necesario respetar el estricto camelcase
        $oFilter->use_field_prefix();
        //Guarda en sesion y post los campos enviados, los de orden y página
        $oFilter->refresh();
        //bugp();
        $this->set_multiassignfilters_from_post();
        
        $arObjFilter = $this->get_multiassign_filters();
        $this->oProduct->set_orderby($this->get_orderby());
        $this->oProduct->set_ordertype($this->get_ordertype());
        //bug($this->get_filter_searchconfig(),"filters");
        $this->oProduct->set_filters($this->get_filter_searchconfig());
        //RECOVER DATALIST
        $arList = $this->oProduct->get_select_all_ids();
        $iRequestPage = $this->get_post("selPage");;
        $oPage = new ComponentPage($arList,$iRequestPage,NULL,30);
        $arList = $oPage->get_items_to_show();
        $arList = $this->oProduct->get_select_all_by_ids($arList);
        //TABLE
        //This method adds objects controls to search list form
        $oTableAssign = new HelperTableTyped($arList,$arColumns);
        $oTableAssign->set_fields($arObjFilter);
        //$oTableAssign->set_module($this->get_current_module());
        $oTableAssign->add_class("table table-striped table-bordered table-condensed");
        $oTableAssign->set_keyfields(array("id"));
        $oTableAssign->set_orderby($this->get_orderby());
        $oTableAssign->set_orderby_type($this->get_ordertype());
        $oTableAssign->set_column_pickmultiple();//columna checks
        $arColumns = array("price_regular"=>"numeric2","price_wholesale"=>"numeric2","price_custom"=>"numeric2"); 
        $oTableAssign->set_format_columns($arColumns);           
        $oTableAssign->merge_pks();//claves separadas por coma
        $oTableAssign->is_ordenable();
        $oTableAssign->set_check_onrowclick();
        
        $arExtra[] = array("position"=>9,"label"=>tr_pt_col_image);
        
        $oImage = new HelperImage();
        $oImage->set_id("img%id%");
        $oImage->set_src("%uri_thumb%");
        //$oImage->set_alt("%img_title%");
        //$oImage->set_title("%source_filename%");
        $oImage->add_style("width:50px");
        $oImage->add_style("width:50px");
        
        $oAnchor = new HelperAnchor();
        $oAnchor->set_href("%uri_href%");
        $oAnchor->set_target("%target%");
        $oAnchor->set_innerhtml($oImage->get_html());
        //bugss();
        $oTableAssign->add_extra_colums($arExtra);
        $oTableAssign->set_column_raw(array("virtual_0"=>$oAnchor));
        
        //$oTableAssign->set_column_picksingle();//crea funcion
        //$oTableAssign->set_column_detail();//detail column
        //esto se define en el padre
        //$oTableAssign->set_multiassign(array("keys"=>array("k"=>1,"k2"=>2)));
        $oTableAssign->set_multiadd(array("keys"=>array("id_order_head"=>$this->get_get("id_order_head"))));
        $oTableAssign->set_current_page($oPage->get_current());
        $oTableAssign->set_next_page($oPage->get_next());
        $oTableAssign->set_first_page($oPage->get_first());
        $oTableAssign->set_last_page($oPage->get_last());
        $oTableAssign->set_total_regs($oPage->get_total_regs());
        $oTableAssign->set_total_pages($oPage->get_total());
        //CRUD BUTTONS BAR
        $oOpButtons = $this->build_multiassign_buttons();
        $oJavascript = new HelperJavascript();
        $oJavascript->set_filters($this->get_filter_controls_id());
        //$oJavascript->set_focusid("id_all");
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
        //id_type_container
        $this->set_filter("id_type_container","selIdTypeContainer",array("value"=>$this->get_post("selIdTypeContainer")));
        $oModelProductArray = new ModelProductArray();
        $arOptions = $oModelProductArray->get_picklist_by_type("container");
        $oAuxField = new HelperSelect($arOptions,"selIdTypeContainer","selIdTypeContainer");
        $oAuxField->set_value_to_select($this->get_post("selIdTypeContainer"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdTypeContainer",tr_fil_id_type_container));
        $arFields[] = $oAuxWrapper;
        //id_type_size
        $this->set_filter("id_type_size","selIdTypeSize",array("value"=>$this->get_post("selIdTypeSize")));
        $oModelProductArray = new ModelProductArray();
        $arOptions = $oModelProductArray->get_picklist_by_type("size");
        $oAuxField = new HelperSelect($arOptions,"selIdTypeSize","selIdTypeSize");
        $oAuxField->set_value_to_select($this->get_post("selIdTypeSize"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdTypeSize",tr_fil_id_type_size));
        $arFields[] = $oAuxWrapper;
        //id_product_family
        $this->set_filter("id_product_family","selIdProductFamily",array("value"=>$this->get_post("selIdProductFamily")));
        $oModelProductFamily = new ModelProductFamily();
        $arOptions = $oModelProductFamily->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdProductFamily","selIdProductFamily");
        $oAuxField->set_value_to_select($this->get_post("selIdProductFamily"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdProductFamily",tr_fil_id_product_family));
        $arFields[] = $oAuxWrapper;
        //price_cost
        $this->set_filter("price_cost","txtPriceCost",array("operator"=>"like","value"=>$this->get_post("txtPriceCost")));
        $oAuxField = new HelperInputText("txtPriceCost","txtPriceCost");
        $oAuxField->set_value($this->get_post("txtPriceCost"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtPriceCost",tr_fil_price_cost));
        $arFields[] = $oAuxWrapper;
        //price_regular
        $this->set_filter("price_regular","txtPriceRegular",array("operator"=>"like","value"=>$this->get_post("txtPriceRegular")));
        $oAuxField = new HelperInputText("txtPriceRegular","txtPriceRegular");
        $oAuxField->set_value($this->get_post("txtPriceRegular"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtPriceRegular",tr_fil_price_regular));
        $arFields[] = $oAuxWrapper;
        //price_wholesale
        $this->set_filter("price_wholesale","txtPriceWholesale",array("operator"=>"like","value"=>$this->get_post("txtPriceWholesale")));
        $oAuxField = new HelperInputText("txtPriceWholesale","txtPriceWholesale");
        $oAuxField->set_value($this->get_post("txtPriceWholesale"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtPriceWholesale",tr_fil_price_wholesale));
        $arFields[] = $oAuxWrapper;
        //price_custom
        $this->set_filter("price_custom","txtPriceCustom",array("operator"=>"like","value"=>$this->get_post("txtPriceCustom")));
        $oAuxField = new HelperInputText("txtPriceCustom","txtPriceCustom");
        $oAuxField->set_value($this->get_post("txtPriceCustom"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtPriceCustom",tr_fil_price_custom));
        $arFields[] = $oAuxWrapper;
        //description
        $this->set_filter("description","txtDescription",array("operator"=>"like","value"=>$this->get_post("txtDescription")));
        $oAuxField = new HelperInputText("txtDescription","txtDescription");
        $oAuxField->set_value($this->get_post("txtDescription"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtDescription",tr_fil_description));
        $arFields[] = $oAuxWrapper;
        //name
        $this->set_filter("name","txtName",array("operator"=>"like","value"=>$this->get_post("txtName")));
        $oAuxField = new HelperInputText("txtName","txtName");
        $oAuxField->set_value($this->get_post("txtName"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtName",tr_fil_name));
        $arFields[] = $oAuxWrapper;
        //observation
        $this->set_filter("observation","txtObservation",array("operator"=>"like","value"=>$this->get_post("txtObservation")));
        $oAuxField = new HelperInputText("txtObservation","txtObservation");
        $oAuxField->set_value($this->get_post("txtObservation"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtObservation",tr_fil_observation));
        $arFields[] = $oAuxWrapper;
        //web_keywords
        $this->set_filter("web_keywords","txtWebKeywords",array("operator"=>"like","value"=>$this->get_post("txtWebKeywords")));
        $oAuxField = new HelperInputText("txtWebKeywords","txtWebKeywords");
        $oAuxField->set_value($this->get_post("txtWebKeywords"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtWebKeywords",tr_fil_web_keywords));
        $arFields[] = $oAuxWrapper;
        //lookup_words
        $this->set_filter("lookup_words","txtLookupWords",array("operator"=>"like","value"=>$this->get_post("txtLookupWords")));
        $oAuxField = new HelperInputText("txtLookupWords","txtLookupWords");
        $oAuxField->set_value($this->get_post("txtLookupWords"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtLookupWords",tr_fil_lookup_words));
        $arFields[] = $oAuxWrapper;
        return $arFields;
    }//get_singleassign_filters()

    private function get_singleassign_columns()
    {
        $arColumns = array("id"=>tr_col_id,"code_erp"=>tr_col_code_erp,"id_type_container"=>tr_col_id_type_container,"id_type_size"=>tr_col_id_type_size,"id_product_family"=>tr_col_id_product_family,"price_cost"=>tr_col_price_cost,"price_regular"=>tr_col_price_regular,"price_wholesale"=>tr_col_price_wholesale,"price_custom"=>tr_col_price_custom,"name"=>tr_col_name,"observation"=>tr_col_observation,"web_keywords"=>tr_col_web_keywords,"lookup_words"=>tr_col_lookup_words);
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
        $this->oProduct->set_orderby($this->get_orderby());
        $this->oProduct->set_ordertype($this->get_ordertype());
        $this->oProduct->set_filters($this->get_filter_searchconfig());
        $arList = $this->oProduct->get_select_all_ids();
        $iRequestPage = $this->get_post("selPage");
        $oPage = new ComponentPage($arList,$iRequestPage,NULL,30);
        $arList = $oPage->get_items_to_show();
        $arList = $this->oProduct->get_select_all_by_ids($arList);
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