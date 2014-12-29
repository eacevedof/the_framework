<?php
/**
 * @author Module Builder 1.0.2
 * @link www.eduardoaf.com
 * @version 1.0.6
 * @name ControllerOrderLines
 * @file controller_order_lines.php    
 * @date 27-10-2014 09:26 (SPAIN)
 * @observations: 
 */
//TFW
import_component("page,validate,filter");
import_helper("form,form_fieldset,form_legend,input_text,label,anchor,table,table_typed");
import_helper("input_password,button_basic,raw,div,javascript");
//APP
import_model("user,order_line,order_head,order_array,seller,customer,product");
import_appmain("controller,view,behaviour");
import_appbehaviour("picklist");
import_apphelper("listactionbar,controlgroup,formactions,buttontabs,headertabs,breadscrumbs,formhead,alertdiv");
//import_apptranslate("orders");

class PartialOrderLines extends TheApplicationController
{
    private $oOrderHead;
    
    public function __construct()
    {
        $this->sModuleName = "orderlines";
        //console,pgpage,clientdevice,sesuser,aftersucces,currop  | log,session,permission,view
        parent::__construct($this->sModuleName);
        if($this->is_get("id_order_head"))
        {    
            $this->oOrderHead = new ModelOrderHead();
            $this->oOrderHead->set_id($this->get_get("id_order_head"));
            $this->oOrderHead->load_by_id();
        }
    }

    //<editor-fold defaultstate="collapsed" desc="LIST BY ORDER HEAD">
    //list_1
    private function build_list_scrumbs()
    {        
        $sUrlTab = $this->build_url("orders");
        $arTabs["list"]=array("href"=>$sUrlTab,"innerhtml"=>"Orders");
        
        //$this->oOrderHead->set_id($this->get_get("id_order_head"));
        //$this->oOrderHead->load_by_id();
        $sUrlTab = $this->build_url("orders",NULL,"update","id=".$this->get_get("id_order_head"));
        $arTabs["detail"]=array("href"=>$sUrlTab,"innerhtml"=>"Order: ".$this->oOrderHead->get_description());

        $sUrlTab = $this->build_url("orders","orderlines","get_list_by_head","id_order_head=".$this->get_get("id_order_head"));
        $arTabs["lines"]=array("href"=>$sUrlTab,"innerhtml"=>tr_entities);
        
        $oScrumbs = new AppHelperBreadscrumbs($arTabs);        
        return $oScrumbs;
    }//build_list_scrumbs()
    
    private function is_editable_by_head()
    {
        //$this->oOrderHead->set_id($this->get_get("id_order_head"));
        //$this->oOrderHead->load_by_id();
        
        //id_type_validate = 7:visit, 8:delevery 9:canceled
        $arNoEditTypes = array("7","8","9");
        if(in_array($this->oOrderHead->get_id_type_validate(),$arNoEditTypes))
            return false;
        return true;
    }//is_editable_by_head
    
    private function build_listhead_operation_buttons()
    {
        $arButTabs = array();
        //crea ventana
        $sParams = "returnmodule=orders&returnsection=orderlines&returnview=addproducts&id_order_head=".$this->get_get("id_order_head");
        $sAssignUrl = $this->build_url("products",NULL,"multiassign",$sParams);
        if($this->oPermission->is_insert())
            $arButTabs["multiassign"]=array("href"=>"javascript:multiassign_window('$sAssignUrl',1,1000,1000);","icon"=>"awe-external-link","innerhtml"=>"Add Products");
        if($this->oPermission->is_quarantine())
            $arButTabs["multiquarantine"]=array("href"=>"javascript:multi_quarantine();","icon"=>"awe-remove","innerhtml"=>"Remove lines");        
        //$arButTabs["filters"]=array("href"=>"javascript:reset_filters();","icon"=>"awe-magic","innerhtml"=>tr_clear_filters);
        //$arButTabs["reload"]=array("href"=>"javascript:TfwControl.form_submit();","icon"=>"awe-refresh","innerhtml"=>tr_refresh);
        //$arButTabs["insert"]=array("href"=>$this->build_url("orders","orderlines","insert"),"icon"=>"awe-plus","innerhtml"=>tr_new);
        //$arButTabs["multidelete"]=array("href"=>"javascript:multi_delete();","icon"=>"awe-remove","innerhtml"=>tr_delete_selection);
        //$arButTabs["singleassign"]=array("href"=>"javascript:single_pick('order_line','singleassign','txtI','txtI');","icon"=>"awe-external-link","innerhtml"=>tr_asign_selection);
        $oOpButtons = new AppHelperButtontabs(tr_entities);
        $oOpButtons->set_tabs($arButTabs);
        return $oOpButtons;
    }//build_listhead_operation_buttons()

    private function get_list_by_head_filters()
    {
        $fTotal = (float)$this->oOrderHead->get_amount_total();
        $fTotal = dbbo_numeric2($fTotal);
        $arFields[]= new AppHelperFormhead("TOTAL: $fTotal");
/*        
        //CAMPOS
        //id
//        $this->set_filter("id","txtId",array("operator"=>"like","value"=>$this->get_post("txtId")));
//        $oAuxField = new HelperInputText("txtId","txtId");
//        $oAuxField->set_value($this->get_post("txtId"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtId",tr_fil_id));
//        $arFields[] = $oAuxWrapper;
        //id_order_head
//        $this->set_filter("id_order_head","selIdOrderHead",array("value"=>$this->get_post("selIdOrderHead")));
//        $arOptions = $this->oOrderHead->get_picklist();
//        $oAuxField = new HelperSelect($arOptions,"selIdOrderHead","selIdOrderHead");
//        $oAuxField->set_value_to_select($this->get_post("selIdOrderHead"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdOrderHead",tr_fil_id_order_head));
//        $arFields[] = $oAuxWrapper;

//        //id_product
//        $this->set_filter("id_product","selIdProduct",array("value"=>$this->get_post("selIdProduct")));
//        $oModelProduct = new ModelProduct();
//        $arOptions = $oModelProduct->get_picklist();
//        $oAuxField = new HelperSelect($arOptions,"selIdProduct","selIdProduct");
//        $oAuxField->set_value_to_select($this->get_post("selIdProduct"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdProduct",tr_fil_id_product));
//        $arFields[] = $oAuxWrapper;
//        //num_items
//        $this->set_filter("num_items","txtNumItems",array("operator"=>"like","value"=>$this->get_post("txtNumItems")));
//        $oAuxField = new HelperInputText("txtNumItems","txtNumItems");
//        $oAuxField->set_value($this->get_post("txtNumItems"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtNumItems",tr_fil_num_items));
//        $arFields[] = $oAuxWrapper;
        //unit_price
//        $this->set_filter("unit_price","txtUnitPrice",array("operator"=>"like","value"=>$this->get_post("txtUnitPrice")));
//        $oAuxField = new HelperInputText("txtUnitPrice","txtUnitPrice");
//        $oAuxField->set_value($this->get_post("txtUnitPrice"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtUnitPrice",tr_fil_unit_price));
//        $arFields[] = $oAuxWrapper;
        //discount
//        $this->set_filter("discount","txtDiscount",array("operator"=>"like","value"=>$this->get_post("txtDiscount")));
//        $oAuxField = new HelperInputText("txtDiscount","txtDiscount");
//        $oAuxField->set_value($this->get_post("txtDiscount"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtDiscount",tr_fil_discount));
//        $arFields[] = $oAuxWrapper;
        //amount
//        $this->set_filter("amount","txtAmount",array("operator"=>"like","value"=>$this->get_post("txtAmount")));
//        $oAuxField = new HelperInputText("txtAmount","txtAmount");
//        $oAuxField->set_value($this->get_post("txtAmount"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtAmount",tr_fil_amount));
//        $arFields[] = $oAuxWrapper;
//        //product
//        $this->set_filter("product","txtProduct",array("operator"=>"like","value"=>$this->get_post("txtProduct")));
//        $oAuxField = new HelperInputText("txtProduct","txtProduct");
//        $oAuxField->set_value($this->get_post("txtProduct"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtProduct",tr_fil_product));
//        $arFields[] = $oAuxWrapper;
*/
        return $arFields;
    }//get_list_by_head_filters()

    private function get_list_by_head_columns()
    {
        $arColumns = array
        (
            //"id"=>tr_col_id
            //,"id_order_head"=>tr_col_id_order_head,"id_product"=>tr_col_id_product
            "line"=>tr_col_line
            ,"product"=>tr_col_product
            ,"unit_price"=>tr_col_unit_price
            ,"num_items"=>tr_col_num_items
            //,"discount"=>tr_col_discount
            ,"amount"=>tr_col_amount
            ,"is_free"=>tr_col_is_free
         );
        return $arColumns;
    }//get_list_by_head_columns()

    private function build_list_tabs()
    {        
        $sUrlTab = $this->build_url("orders",NULL,"update","id=".$this->get_get("id_order_head"));
        $arTabs["detail"]=array("href"=>$sUrlTab,"innerhtml"=>"Detail");
        
        $sUrlTab = $this->build_url("orders","orderlines","get_list_by_head","id_order_head=".$this->get_get("id_order_head"));
        $arTabs["lines"]=array("href"=>$sUrlTab,"innerhtml"=>"Lines");

        $oTabs = new AppHelperHeadertabs($arTabs,"lines");        
        return $oTabs;
    }//build_list_tabs
    
    public function get_list_by_head()
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
        $arObjFilter = $this->get_list_by_head_filters();
        $arColumns = $this->get_list_by_head_columns(); 

        $oFilter = new ComponentFilter();
        $oFilter->set_fieldnames($this->get_filter_fieldnames());
        //Indica que no se guardara en sesion por nombre de campo sino por nombre de control
        //para esto es necesario respetar el estricto camelcase
        $oFilter->use_field_prefix();
        //Guarda en sesion y post los campos enviados, los de orden y página
        $oFilter->refresh();
        
        $isEditable = $this->is_editable_by_head();
        
        $oModelOrderLine = new ModelOrderLine();
        $oModelOrderLine->set_orderby($this->get_orderby());
        $oModelOrderLine->set_ordertype($this->get_ordertype());
        $oModelOrderLine->set_filters($this->get_filter_searchconfig());
        $arList = $oModelOrderLine->get_select_ids_by_head($this->get_get("id_order_head"));
        //bug($arList,"arListPks");
        $iRequestPage = $this->get_post("selPage");
        $oPage = new ComponentPage($arList,$iRequestPage);
        $arList = $oPage->get_items_to_show();
        $arList = $oModelOrderLine->get_select_all_by_ids($arList);
        //TABLE
        $oTableList = new HelperTableTyped($arList,$arColumns);
        $oTableList->set_fields($arObjFilter);
        //$oTableList->set_module($this->get_current_module());
        $oTableList->add_class("table table-striped table-bordered table-condensed");
        $oTableList->set_keyfields(array("id"));
        //$oTableList->is_ordenable();
        //$oTableList->set_check_onrowclick();
        $oTableList->set_orderby($this->get_orderby());
        $oTableList->set_orderby_type($this->get_ordertype());

        //COLUMNS CONFIGURATION
        $oTableList->set_column_hidden(array("id_product"));
        $oTableList->set_extra_hidden(array("id_order_head"=>$this->get_get("id_order_head")));
        $arColumns = array("num_items"=>"int","discount"=>"numeric2","unit_price"=>"numeric2","amount"=>"numeric2"); 
        $oTableList->set_format_columns($arColumns);        
        //define el contenido de la fila para la columna creada
        if($isEditable)
        {   
            if($this->oPermission->is_quarantine()||$this->oPermission->is_delete())
                $oTableList->set_column_pickmultiple();//checks column
            //$oTableList->set_column_delete();
            if($this->oPermission->is_quarantine())
                $oTableList->set_column_quarantine();
            
            if($this->oPermission->is_insert())
            {
                //$oTableList->set_column_detail();//detail column
                $arExtra[] = array("position"=>7,"label"=>"Save");
                //crea las columnas en la cabecera
                $oTableList->add_extra_colums($arExtra);
                
                $oTableList->set_column_anchor(array("virtual_0"=>array
                    ("href"=>"javascript:row_ajax_save(%cellpos%);","external"=>1
                      ,"innerhtml"=>tr_col_save,"class"=>"btn btn-info","icon"=>"awe-ok-sign")));

                $arColumns = array("num_items"=>array("class"=>"al-right input-mini","onclick"=>"this.select();")
                    ,"amount"=>array("readonly"=>1,"class"=>"al-right input-mini")
                    ,"is_free"=>array("readonly"=>1,"class"=>"al-right input-mini")); 
            
                $oTableList->set_column_text($arColumns);
            }
            //CRUD OPERATIONS BAR
            $oOpButtons = $this->build_listhead_operation_buttons();            
        }//if($isEditable)
        //parametros a pasar al popup
        //$oTableList->set_multiassign(array("keys"=>array("k"=>1,"k2"=>2)));
        $oTableList->set_current_page($oPage->get_current());
        $oTableList->set_next_page($oPage->get_next());
        $oTableList->set_first_page($oPage->get_first());
        $oTableList->set_last_page($oPage->get_last());
        $oTableList->set_total_regs($oPage->get_total_regs());
        $oTableList->set_total_pages($oPage->get_total());
        
        $oJavascript = new HelperJavascript();
        $oJavascript->set_filters($this->get_filter_controls_id());
        //$oJavascript->set_focusid("id_all");
        $oScrumbs = $this->build_list_scrumbs();
        $oTabs = $this->build_list_tabs();
        
        $this->oView->add_var($oJavascript,"oJavascript");
        $this->oView->add_var($oScrumbs,"oScrumbs");
        $this->oView->add_var($oTabs,"oTabs");

        $this->oView->add_var($oOpButtons,"oOpButtons");
        $this->oView->add_var($oAlert,"oAlert");
        $this->oView->add_var($oTableList,"oTableList");
        $this->oView->set_path_view("orders/lines/view_index");
        $this->oView->show_page();
    }//get_list_by_head()
    //</editor-fold>

    //<editor-fold defaultstate="collapsed" desc="INSERT">
    private function build_insert_opbuttons()
    {
        $arButTabs = array();
        $arButTabs["list"]=array("href"=>$this->build_url("orders","orderlines"),"icon"=>"awe-search","innerhtml"=>tr_list);
        return $arButTabs;
    }//build_insert_opbuttons()

    private function build_insert_fields($usePost=0)
    {
        $arFields = array(); $oAuxField = null; $oAuxLabel = NULL;
        $oModelOrderLine = new ModelOrderLine();
        $arFields[]= new AppHelperFormhead(tr_new.tr_entity);
        //id
        $oAuxField = new HelperInputText("txtId","txtId");
        
        $oAuxField->is_primarykey();
        if($usePost) $oAuxField->set_value($this->get_post("txtId"));
        $oAuxLabel = new HelperLabel("txtId",tr_ins_id,"lblId");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //id_order_head
        $arOptions = $this->oOrderHead->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdOrderHead","selIdOrderHead");
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdOrderHead"));
        $oAuxLabel = new HelperLabel("selIdOrderHead",tr_ins_id_order_head,"lblIdOrderHead");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //id_product
        $oModelProduct = new ModelProduct();
        $arOptions = $oModelProduct->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdProduct","selIdProduct");
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdProduct"));
        $oAuxLabel = new HelperLabel("selIdProduct",tr_ins_id_product,"lblIdProduct");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //num_items
        $oAuxField = new HelperInputText("txtNumItems","txtNumItems");
        if($usePost) $oAuxField->set_value($this->get_post("txtNumItems"));
        $oAuxLabel = new HelperLabel("txtNumItems",tr_ins_num_items,"lblNumItems");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //unit_price
        $oAuxField = new HelperInputText("txtUnitPrice","txtUnitPrice");
        if($usePost) $oAuxField->set_value($this->get_post("txtUnitPrice"));
        $oAuxLabel = new HelperLabel("txtUnitPrice",tr_ins_unit_price,"lblUnitPrice");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //discount
        $oAuxField = new HelperInputText("txtDiscount","txtDiscount");
        if($usePost) $oAuxField->set_value($this->get_post("txtDiscount"));
        $oAuxLabel = new HelperLabel("txtDiscount",tr_ins_discount,"lblDiscount");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //amount
        $oAuxField = new HelperInputText("txtAmount","txtAmount");
        if($usePost) $oAuxField->set_value($this->get_post("txtAmount"));
        $oAuxLabel = new HelperLabel("txtAmount",tr_ins_amount,"lblAmount");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //product
        $oAuxField = new HelperInputText("txtProduct","txtProduct");
        if($usePost) $oAuxField->set_value($this->get_post("txtProduct"));
        $oAuxLabel = new HelperLabel("txtProduct",tr_ins_product,"lblProduct");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();
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
    }//build_insert_form()

    private function get_insert_validate()
    {
        //Validacion con PHP y JS
        $arFieldsConfig = array();
        $arFieldsConfig["id"] = array("controlid"=>"txtId","label"=>tr_ins_id,"length"=>9,"type"=>array("numeric","required"));
        $arFieldsConfig["id_order_head"] = array("controlid"=>"txtIdOrderHead","label"=>tr_ins_id_order_head,"length"=>9,"type"=>array("numeric"));
        $arFieldsConfig["id_product"] = array("controlid"=>"txtIdProduct","label"=>tr_ins_id_product,"length"=>9,"type"=>array("numeric"));
        $arFieldsConfig["num_items"] = array("controlid"=>"txtNumItems","label"=>tr_ins_num_items,"length"=>5,"type"=>array("numeric"));
        $arFieldsConfig["unit_price"] = array("controlid"=>"txtUnitPrice","label"=>tr_ins_unit_price,"length"=>9,"type"=>array("numeric"));
        $arFieldsConfig["discount"] = array("controlid"=>"txtDiscount","label"=>tr_ins_discount,"length"=>9,"type"=>array("numeric"));
        $arFieldsConfig["amount"] = array("controlid"=>"txtAmount","label"=>tr_ins_amount,"length"=>9,"type"=>array("numeric"));
        $arFieldsConfig["product"] = array("controlid"=>"txtProduct","label"=>tr_ins_product,"length"=>200,"type"=>array());
        return $arFieldsConfig;
    }//get_insert_validate

    public function insert()
    {
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
                $oModelOrderLine = new ModelOrderLine();
                $oModelOrderLine->set_attrib_value($arFieldsValues);
                $oModelOrderLine->set_insert_user($this->oSessionUser->get_id());;
                $oModelOrderLine->autoinsert();
                if($oModelOrderLine->is_error())
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
        $this->oView->set_path_view("orders/lines/view_insert");
        $this->oView->show_page();
    }//insert()
    //</editor-fold>

    //<editor-fold defaultstate="collapsed" desc="UPDATE">
    private function build_update_opbuttons()
    {
        $arButTabs = array();
        $arButTabs["list"]=array("href"=>$this->build_url("orders","orderlines"),"icon"=>"awe-search","innerhtml"=>tr_list);
        $arButTabs["insert"]=array("href"=>$this->build_url("orders","orderlines","insert"),"icon"=>"awe-plus","innerhtml"=>tr_new);
        $arButTabs["delete"]=array("href"=>$this->build_url("orders","orderlines","detele","id=".$this->get_get("id")),"icon"=>"awe-remove","innerhtml"=>tr_delete);
        return $arButTabs;
    }//build_update_opbuttons()

    private function build_update_fields($usePost=0)
    {
        $arFields = array(); $oAuxField = NULL; $oAuxLabel = NULL;
        $oModelOrderLine = new ModelOrderLine();
        $oModelOrderLine->set_id($this->get_get("id"));
        $oModelOrderLine->load_by_id();
        //info
        $sParams = "id=".$oModelOrderLine->get_id()."&code_erp=".$oModelOrderLine->get_code_erp();
        $sUrlDetail = $this->build_url("orders","orderlines","update",$sParams);
        $arButTabs["detail"]=array("href"=>$sUrlDetail,"innerhtml"=>"Detail");
        $oAuxField = new AppHelperButtontabs(tr_detail);
        $oAuxField->no_border_bottom();
        $oAuxField->set_tabs($arButTabs);
        $oAuxField->set_active_tab("detail");
        $arFields[]=$oAuxField;
        $arFields[]= new AppHelperFormhead(tr_entity,$oModelOrderLine->get_id()." - ".$oModelOrderLine->get_description());
        //id
        $oAuxField = new HelperInputText("txtId","txtId");
        
        $oAuxField->is_primarykey();
        $oAuxField->set_value($oModelOrderLine->get_id());
        if($usePost) $oAuxField->set_value($this->get_post("txtId"));
        $oAuxLabel = new HelperLabel("txtId",tr_upd_id,"lblId");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //id_order_head
        $arOptions = $this->oOrderHead->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdOrderHead","selIdOrderHead");
        $oAuxField->set_value_to_select($oModelOrderLine->get_id_order_head());
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdOrderHead"));
        $oAuxLabel = new HelperLabel("selIdOrderHead",tr_upd_id_order_head,"lblIdOrderHead");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //id_product
        $oModelProduct = new ModelProduct();
        $arOptions = $oModelProduct->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdProduct","selIdProduct");
        $oAuxField->set_value_to_select($oModelOrderLine->get_id_product());
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdProduct"));
        $oAuxLabel = new HelperLabel("selIdProduct",tr_upd_id_product,"lblIdProduct");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //num_items
        $oAuxField = new HelperInputText("txtNumItems","txtNumItems");
        $oAuxField->set_value($oModelOrderLine->get_num_items());
        if($usePost) $oAuxField->set_value($this->get_post("txtNumItems"));
        $oAuxLabel = new HelperLabel("txtNumItems",tr_upd_num_items,"lblNumItems");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //unit_price
        $oAuxField = new HelperInputText("txtUnitPrice","txtUnitPrice");
        $oAuxField->set_value($oModelOrderLine->get_unit_price());
        if($usePost) $oAuxField->set_value($this->get_post("txtUnitPrice"));
        $oAuxLabel = new HelperLabel("txtUnitPrice",tr_upd_unit_price,"lblUnitPrice");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //discount
        $oAuxField = new HelperInputText("txtDiscount","txtDiscount");
        $oAuxField->set_value($oModelOrderLine->get_discount());
        if($usePost) $oAuxField->set_value($this->get_post("txtDiscount"));
        $oAuxLabel = new HelperLabel("txtDiscount",tr_upd_discount,"lblDiscount");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //amount
        $oAuxField = new HelperInputText("txtAmount","txtAmount");
        $oAuxField->set_value($oModelOrderLine->get_amount());
        if($usePost) $oAuxField->set_value($this->get_post("txtAmount"));
        $oAuxLabel = new HelperLabel("txtAmount",tr_upd_amount,"lblAmount");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //product
        $oAuxField = new HelperInputText("txtProduct","txtProduct");
        $oAuxField->set_value($oModelOrderLine->get_product());
        if($usePost) $oAuxField->set_value($this->get_post("txtProduct"));
        $oAuxLabel = new HelperLabel("txtProduct",tr_upd_product,"lblProduct");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        $oAuxField = new HelperButtonBasic("butSave",tr_save);
        $oAuxField->add_class("btn btn-primary");
        $oAuxField->set_js_onclick("update();");
        $arFields[] = new ApphelperFormactions(array($oAuxField));
        $sRegInfo = $this->get_audit_info($oModelOrderLine->get_insert_user(),$oModelOrderLine->get_insert_date()
        ,$oModelOrderLine->get_update_user(),$oModelOrderLine->get_update_date());
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

    private function get_update_validate()
    {
        //Validacion con PHP y JS
        $arFieldsConfig = array();
        $arFieldsConfig["id"] = array("controlid"=>"txtId","label"=>tr_ins_id,"length"=>9,"type"=>array("numeric","required"));
        $arFieldsConfig["id_order_head"] = array("controlid"=>"txtIdOrderHead","label"=>tr_ins_id_order_head,"length"=>9,"type"=>array("numeric"));
        $arFieldsConfig["id_product"] = array("controlid"=>"txtIdProduct","label"=>tr_ins_id_product,"length"=>9,"type"=>array("numeric"));
        $arFieldsConfig["num_items"] = array("controlid"=>"txtNumItems","label"=>tr_ins_num_items,"length"=>5,"type"=>array("numeric"));
        $arFieldsConfig["unit_price"] = array("controlid"=>"txtUnitPrice","label"=>tr_ins_unit_price,"length"=>9,"type"=>array("numeric"));
        $arFieldsConfig["discount"] = array("controlid"=>"txtDiscount","label"=>tr_ins_discount,"length"=>9,"type"=>array("numeric"));
        $arFieldsConfig["amount"] = array("controlid"=>"txtAmount","label"=>tr_ins_amount,"length"=>9,"type"=>array("numeric"));
        $arFieldsConfig["product"] = array("controlid"=>"txtProduct","label"=>tr_ins_product,"length"=>200,"type"=>array());
        return $arFieldsConfig;
    }//get_update_validate

    public function update()
    {
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
                $oModelOrderLine = new ModelOrderLine();
                $oModelOrderLine->set_attrib_value($arFieldsValues);
                $oModelOrderLine->set_update_user($this->oSessionUser->get_id());
                $oModelOrderLine->autoupdate();
                if($oModelOrderLine->is_error())
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
        $this->oView->set_path_view("orders/lines/view_update");
        $this->oView->show_page();
    }//update()
    //</editor-fold>

    //<editor-fold defaultstate="collapsed" desc="DELETE">
    private function single_delete()
    {
        $id = $this->get_get("id");
        if($id)
        {
            $oModelOrderLine = new ModelOrderLine();
            $oModelOrderLine->set_id($id);
            $oModelOrderLine->autodelete();
            if($oModelOrderLine->is_error())
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
        $this->go_to_list_by_head();
    }//single_delete()

    private function multi_delete()
    {
        $isError = false;
        //Intenta recuperar pkeys sino pasa a recuperar el id. En ultimo caso lo que se ha pasado por parametro
        $arKeys = $this->get_listkeys();
        //bugpg();die;
        $oModelOrderLine = new ModelOrderLine();
        foreach($arKeys as $sKey)
        {
            $id = $sKey;
            $oModelOrderLine->set_id($id);
            $oModelOrderLine->autodelete();
            if($oModelOrderLine->is_error())
            {
                $isError = true;
                $this->set_session_message(tr_error_trying_to_delete,"e");
            }
        }
        if(!$isError)
            $this->set_session_message(tr_data_deleted);
        $this->go_to_list_by_head();
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
        $oModelOrderLine = new ModelOrderLine();
        foreach($arKeys as $sKey)
        {
            $id = $sKey;
            $oModelOrderLine->set_id($id);
            $oModelOrderLine->autoquarantine();
            if($oModelOrderLine->is_error())
            {
                $isError = true;
                $this->set_session_message(tr_error_trying_to_delete,"e");
            }
        }
        if(!$isError)
        {   
            $this->oOrderHead->load_amounts();
            $this->oOrderHead->autoupdate();
            $this->set_session_message(tr_data_deleted);
        }
    }//multi_quarantine()

    private function single_quarantine()
    {
        $id = $this->get_get("id");
        if($id)
        {
            $oModelOrderLine = new ModelOrderLine();
            $oModelOrderLine->set_id($id);
            $oModelOrderLine->autoquarantine();
            if($oModelOrderLine->is_error())
                $this->set_session_message(tr_error_trying_to_delete);
            else
            {    
                $this->oOrderHead->load_amounts();
                $this->oOrderHead->autoupdate();
                $this->set_session_message(tr_data_deleted);
            }
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
        //$this->go_to_list();
        $this->go_to_list_by_head();
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
        //id_order_head
//        $this->set_filter("id_order_head","selIdOrderHead",array("value"=>$this->get_post("selIdOrderHead")));
//        $arOptions = $this->oOrderHead->get_picklist();
//        $oAuxField = new HelperSelect($arOptions,"selIdOrderHead","selIdOrderHead");
//        $oAuxField->set_value_to_select($this->get_post("selIdOrderHead"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdOrderHead",tr_fil_id_order_head));
//        $arFields[] = $oAuxWrapper;
        //id_product
        $this->set_filter("id_product","selIdProduct",array("value"=>$this->get_post("selIdProduct")));
        $oModelProduct = new ModelProduct();
        $arOptions = $oModelProduct->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdProduct","selIdProduct");
        $oAuxField->set_value_to_select($this->get_post("selIdProduct"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdProduct",tr_fil_id_product));
        $arFields[] = $oAuxWrapper;
        //num_items
//        $this->set_filter("num_items","txtNumItems",array("operator"=>"like","value"=>$this->get_post("txtNumItems")));
//        $oAuxField = new HelperInputText("txtNumItems","txtNumItems");
//        $oAuxField->set_value($this->get_post("txtNumItems"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtNumItems",tr_fil_num_items));
//        $arFields[] = $oAuxWrapper;
        //unit_price
        $this->set_filter("unit_price","txtUnitPrice",array("operator"=>"like","value"=>$this->get_post("txtUnitPrice")));
        $oAuxField = new HelperInputText("txtUnitPrice","txtUnitPrice");
        $oAuxField->set_value($this->get_post("txtUnitPrice"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtUnitPrice",tr_fil_unit_price));
        $arFields[] = $oAuxWrapper;
        //discount
//        $this->set_filter("discount","txtDiscount",array("operator"=>"like","value"=>$this->get_post("txtDiscount")));
//        $oAuxField = new HelperInputText("txtDiscount","txtDiscount");
//        $oAuxField->set_value($this->get_post("txtDiscount"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtDiscount",tr_fil_discount));
//        $arFields[] = $oAuxWrapper;
        //amount
//        $this->set_filter("amount","txtAmount",array("operator"=>"like","value"=>$this->get_post("txtAmount")));
//        $oAuxField = new HelperInputText("txtAmount","txtAmount");
//        $oAuxField->set_value($this->get_post("txtAmount"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtAmount",tr_fil_amount));
//        $arFields[] = $oAuxWrapper;
        //product
        $this->set_filter("product","txtProduct",array("operator"=>"like","value"=>$this->get_post("txtProduct")));
        $oAuxField = new HelperInputText("txtProduct","txtProduct");
        $oAuxField->set_value($this->get_post("txtProduct"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtProduct",tr_fil_product));
        $arFields[] = $oAuxWrapper;
        return $arFields;
    }//get_multiassign_filters()

    private function get_multiassign_columns()
    {
        $arColumns = array
        (
            "id"=>tr_col_id
            ,"id_order_head"=>tr_col_id_order_head
            ,"id_product"=>tr_col_id_product
            ,"num_items"=>tr_col_num_items
            ,"unit_price"=>tr_col_unit_price
            ,"discount"=>tr_col_discount
            ,"amount"=>tr_col_amount
            ,"product"=>tr_col_product
        );
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
        $oModelOrderLine = new ModelOrderLine();
        $oModelOrderLine->set_orderby($this->get_orderby());
        $oModelOrderLine->set_ordertype($this->get_ordertype());
        $oModelOrderLine->set_filters($this->get_filter_searchconfig());
        $arList = $oModelOrderLine->get_select_all_ids();
        $iRequestPage = $this->get_post("selPage");
        $oPage = new ComponentPage($arList,$iRequestPage);
        $arList = $oPage->get_items_to_show();
        $arList = $oModelOrderLine->get_select_all_by_ids($arList);
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
        $this->oView->set_path_view("orders/lines/view_assign");
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
        //id_order_head
        $this->set_filter("id_order_head","selIdOrderHead",array("value"=>$this->get_post("selIdOrderHead")));
        $arOptions = $this->oOrderHead->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdOrderHead","selIdOrderHead");
        $oAuxField->set_value_to_select($this->get_post("selIdOrderHead"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdOrderHead",tr_fil_id_order_head));
        $arFields[] = $oAuxWrapper;
        //id_product
        $this->set_filter("id_product","selIdProduct",array("value"=>$this->get_post("selIdProduct")));
        $oModelProduct = new ModelProduct();
        $arOptions = $oModelProduct->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdProduct","selIdProduct");
        $oAuxField->set_value_to_select($this->get_post("selIdProduct"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdProduct",tr_fil_id_product));
        $arFields[] = $oAuxWrapper;
        //num_items
        $this->set_filter("num_items","txtNumItems",array("operator"=>"like","value"=>$this->get_post("txtNumItems")));
        $oAuxField = new HelperInputText("txtNumItems","txtNumItems");
        $oAuxField->set_value($this->get_post("txtNumItems"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtNumItems",tr_fil_num_items));
        $arFields[] = $oAuxWrapper;
        //unit_price
        $this->set_filter("unit_price","txtUnitPrice",array("operator"=>"like","value"=>$this->get_post("txtUnitPrice")));
        $oAuxField = new HelperInputText("txtUnitPrice","txtUnitPrice");
        $oAuxField->set_value($this->get_post("txtUnitPrice"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtUnitPrice",tr_fil_unit_price));
        $arFields[] = $oAuxWrapper;
        //discount
        $this->set_filter("discount","txtDiscount",array("operator"=>"like","value"=>$this->get_post("txtDiscount")));
        $oAuxField = new HelperInputText("txtDiscount","txtDiscount");
        $oAuxField->set_value($this->get_post("txtDiscount"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtDiscount",tr_fil_discount));
        $arFields[] = $oAuxWrapper;
        //amount
        $this->set_filter("amount","txtAmount",array("operator"=>"like","value"=>$this->get_post("txtAmount")));
        $oAuxField = new HelperInputText("txtAmount","txtAmount");
        $oAuxField->set_value($this->get_post("txtAmount"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtAmount",tr_fil_amount));
        $arFields[] = $oAuxWrapper;
        //product
        $this->set_filter("product","txtProduct",array("operator"=>"like","value"=>$this->get_post("txtProduct")));
        $oAuxField = new HelperInputText("txtProduct","txtProduct");
        $oAuxField->set_value($this->get_post("txtProduct"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtProduct",tr_fil_product));
        $arFields[] = $oAuxWrapper;
        return $arFields;
    }//get_singleassign_filters()

    private function get_singleassign_columns()
    {
        $arColumns = array("id"=>tr_col_id,"id_order_head"=>tr_col_id_order_head,"id_product"=>tr_col_id_product,"num_items"=>tr_col_num_items,"unit_price"=>tr_col_unit_price,"discount"=>tr_col_discount,"amount"=>tr_col_amount,"product"=>tr_col_product);
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
        $oModelOrderLine = new ModelOrderLine();
        $oModelOrderLine->set_orderby($this->get_orderby());
        $oModelOrderLine->set_ordertype($this->get_ordertype());
        $oModelOrderLine->set_filters($this->get_filter_searchconfig());
        $arList = $oModelOrderLine->get_select_all_ids();
        $iRequestPage = $this->get_post("selPage");
        $oPage = new ComponentPage($arList,$iRequestPage);
        $arList = $oPage->get_items_to_show();
        $arList = $oModelOrderLine->get_select_all_by_ids($arList);
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
        $this->oView->set_path_view("orders/lines/view_assign");
        $this->oView->show_page();
    }//singleassign()
    //</editor-fold>

    //<editor-fold defaultstate="collapsed" desc="EXTRAS">
    public function addproducts()
    {
        //bugpg(); die;
        $sFreeLine = $this->get_post("selFreeLine");
        if(!$sFreeLine) $sFreeLine="NO";
        
        $idOrderHead = $this->get_get("id_order_head");
        $arProductIds = $this->get_listkeys();
        $oModelProduct = new ModelProduct();

        foreach($arProductIds as $arId)
        {
            $id = $arId["id"];
            $oModelProduct->set_id($id);
            $oModelProduct->load_by_id();
            //bug($oModelProduct);
            $oModelOrderLine = new ModelOrderLine();
            $oModelOrderLine->set_id_order_head($idOrderHead);
            $oModelOrderLine->load_new_line();
            $oModelOrderLine->set_id_product($oModelProduct->get_id());
            $oModelOrderLine->set_product($oModelProduct->get_description());
            $oModelOrderLine->set_num_items(0);
            //price custom (6) por defecto 
            $oModelOrderLine->set_unit_price($oModelProduct->get_price_custom());
            if($this->oOrderHead->get_id_type_payment()=="4" //COD
                    || $this->oOrderHead->get_id_type_payment()=="5" )//ACCOUNT
            $oModelOrderLine->set_unit_price($oModelProduct->get_price_wholesale());
            
            $oModelOrderLine->set_discount(0);
            $oModelOrderLine->set_is_free($sFreeLine);
            $oModelOrderLine->autoinsert();
        }
        
        $sUrlPopup = $this->get_assign_backurl(array("id_order_head"));
        if($this->get_get("close"))
            //bugpg();
            $this->js_colseme_and_parent_refresh();
        else
            $this->js_parent_refresh();
        $this->js_go_to($sUrlPopup);
    }
    
    private function go_to_list_by_head() 
    {
        $sUrl = $this->build_url("orders","orderlines","get_list_by_head","id_order_head=".$this->get_get("id_order_head"));
        $this->go_to_url($sUrl);
    }
    //</editor-fold>
}//end controller