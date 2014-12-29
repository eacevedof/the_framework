<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.7
 * @name PartialPictures
 * @file partial_pictures.php 
 * @date 28-10-2014 10:15 (SPAIN)
 * @observations: Gestiona cargas de imágenes
 * @require controller_products.php
 */
import_helper("input_file");
import_apptranslate("products,pictures");
import_appcontroller("pictures");
import_model("product,order_head");
class PartialPictures extends ControllerPictures
{   
    protected $oProduct;
    
    public function __construct()
    {
        //bugp("selItemsPerPage");
        $this->sModuleName = "pictures";
        //console,pgpage,clientdevice,sesuser,aftersucces,currop  | log,session,permission,view
        parent::__construct($this->sModuleName);
        $this->iNumUploads = 3;
        $this->oProduct = new ModelProduct();
        $this->oProduct->set_platform($this->oSessionUser->get_platform());
        $this->sFolderUpload = "products";
        
        if($this->is_inget("id_picture"))
        {
            $this->oPicture->set_id($this->get_get("id_picture"));
            $this->oPicture->load_by_id();
        }
        //para el listado desde producto
        if($this->is_inget("id_product"))
        {
            $this->oProduct->set_id($this->get_get("id_product"));
            $this->oProduct->load_by_id();
        }
        
        $this->sDisplayMode = "grid";
        //bugss("productspicturesget_list");
        if($this->is_inpost("selDisplaymode")) 
            $this->sDisplayMode = $this->get_post("selDisplaymode");
        elseif($this->is_insession_filter("selDisplaymode"))
            $this->sDisplayMode = $this->get_session_filter("selDisplaymode");
         //bug($this->sDisplayMode,"display mode constr");
        
        //$this->oSessionUser->set_dataowner_table($this->oPicture->get_table_name());
        //$this->oSessionUser->set_dataowner_tablefield("id_product");
        //$this->oSessionUser->set_dataowner_keys(array("id"=>$this->oPicture->get_id()));
    }
    
//<editor-fold defaultstate="collapsed" desc="LIST">
    //list_1
    protected function build_list_scrumbs()
    {
        $arLinks = array();
        $sUrlLink = $this->build_url();
        $arLinks["list"]=array("href"=>$sUrlLink,"innerhtml"=>tr_pt_entities);                
        $sUrlLink = $this->build_url("products",NULL,"update","id=".$this->get_get("id_product"));
        $arLinks["detail"]=array("href"=>$sUrlLink,"innerhtml"=>"Product: ".$this->oProduct->get_id()." - ".$this->oProduct->get_description());
        $sUrlLink = $this->build_url("products","pictures","get_list","id_product=".$this->get_get("id_product"));
        $arLinks["pictures"]=array("href"=>$sUrlLink,"innerhtml"=>tr_pe_entities);        
        $oScrumbs = new AppHelperBreadscrumbs($arLinks);
        return $oScrumbs;        
    }

    //list_2
    protected function build_list_tabs()
    {
        $arTabs = array();
        $sUrlTab = $this->build_url("products",NULL,"update","id=".$this->get_get("id_product"));
        $arTabs["detail"]=array("href"=>$sUrlTab,"innerhtml"=>"Detail");
        $sUrlTab = $this->build_url("products","pictures","get_list","id_product=".$this->get_get("id_product"));
        $arTabs["pictures"]=array("href"=>$sUrlTab,"innerhtml"=>tr_pe_entities);
        $oTabs = new AppHelperHeadertabs($arTabs,"pictures");
        return $oTabs;
    }

    //list_3
    protected function build_listoperation_buttons()
    {
        $arOpButtons = array();
        $arOpButtons["filters"]=array("href"=>"javascript:reset_filters();","icon"=>"awe-magic","innerhtml"=>tr_pe_listopbutton_filters);
        $arOpButtons["reload"]=array("href"=>"javascript:TfwControl.form_submit();","icon"=>"awe-refresh","innerhtml"=>tr_pe_listopbutton_reload);
        if($this->oPermission->is_insert())
            $arOpButtons["insert"]=array("href"=>$this->build_url("products","pictures","insert","id_product=".$this->get_get("id_product")),"icon"=>"awe-plus","innerhtml"=>tr_pe_listopbutton_insert);
        //if($this->oPermission->is_quarantine())
            //$arOpButtons["multiquarantine"]=array("href"=>"javascript:multi_quarantine();","icon"=>"awe-remove","innerhtml"=>tr_pe_listopbutton_multiquarantine);
        //if($this->oPermission->is_delete())
            //$arOpButtons["multidelete"]=array("href"=>"javascript:multi_delete();","icon"=>"awe-remove","innerhtml"=>tr_pe_listopbutton_multidelete);
        //PICK WINDOWS
        //$arOpButtons["multiassign"]=array("href"=>"javascript:multiassign_window('pictures',null,'multiassign','pictures','addexternaldata');","icon"=>"awe-external-link","innerhtml"=>tr_pe_listopbutton_multiassign);
        //$arOpButtons["singleassign"]=array("href"=>"javascript:single_pick('pictures','singleassign','txtI','txtI');","icon"=>"awe-external-link","innerhtml"=>tr_pe_listopbutton_singleassign);        
        $oOpButtons = new AppHelperButtontabs(tr_pe_entities_of.$this->oProduct->get_description());
        $oOpButtons->set_tabs($arOpButtons);
        return $oOpButtons;        
    }//build_listoperation_buttons()

    //list_4
    protected function load_config_list_filters()
    {
        //id
        $this->set_filter("id","txtId",array("operator"=>"like"));
        //filename
        $this->set_filter("filename","txtFilename",array("operator"=>"like"));        
        //name
        $this->set_filter("name","txtName",array("operator"=>"like"));
        //shortname
        $this->set_filter("shortname","txtShortname",array("operator"=>"like"));
        //source_filename
        $this->set_filter("source_filename","txtSourceFilename",array("operator"=>"like"));        
        //selDisplaymode (no field) Necesario para que se recupere de sesion
        $this->set_filter("displaymode");
        //selItemsPerPage (no field)
        $this->set_filter("itemsperpage");
//<editor-fold defaultstate="collapsed" desc="NIU">        
        //id_type
        //$this->set_filter("id_type","selIdType");        
        //information
        //$this->set_filter("information","txaInformation",array("operator"=>"like"));
        //information_extra
        //$this->set_filter("information_extra","txaInformationExtra",array("operator"=>"like"));
        //img_title
        //$this->set_filter("img_title","txtImgTitle",array("operator"=>"like"));
        //anchor_text
        //$this->set_filter("anchor_text","txtAnchorText",array("operator"=>"like"));
        //csv_tags
        //$this->set_filter("csv_tags","txtCsvTags",array("operator"=>"like"));
        
        //code_erp
        //$this->set_filter("code_erp","txtCodeErp",array("operator"=>"like"));
        //id_type_entity
//        $this->set_filter("id_type_entity","selIdTypeEntity");
//        //id_owner_entity
//        $this->set_filter("id_owner_entity","selIdOwnerEntity");
//        //is_bydefault
//        $this->set_filter("is_bydefault","selIdBydefault");
//        //id_entity
//        $this->set_filter("id_entity","selIdEntity");
//        //id_owner
//        $this->set_filter("id_owner","selIdOwner");
//        //id_thumb_1
//        $this->set_filter("id_thumb_1","selIdThumb1");
//        //id_thumb_2
//        $this->set_filter("id_thumb_2","selIdThumb2");
//        //id_thumb_3
//        $this->set_filter("id_thumb_3","selIdThumb3");
//        //id_thumb_4
//        $this->set_filter("id_thumb_4","selIdThumb4");
//        //id_thumb_5
//        $this->set_filter("id_thumb_5","selIdThumb5");
//        //width
//        $this->set_filter("width","txtWidth",array("operator"=>"like"));
//        //height
//        $this->set_filter("height","txtHeight",array("operator"=>"like"));
//        //resolution
//        $this->set_filter("resolution","txtResolution",array("operator"=>"like"));
//        //order_by
//        $this->set_filter("order_by","txtOrderBy",array("operator"=>"like"));
//        //rating
//        $this->set_filter("rating","txtRating",array("operator"=>"like"));
//        //show
//        $this->set_filter("show","txtShow",array("operator"=>"like"));
//        //is_public
//        $this->set_filter("is_public","txtIsPublic",array("operator"=>"like"));
//        //is_file
//        $this->set_filter("is_file","txtIsFile",array("operator"=>"like"));
//        //is_error
//        $this->set_filter("is_error","txtIsError",array("operator"=>"like"));
//        //size
//        $this->set_filter("size","txtSize",array("operator"=>"like"));        
//        //extension
//        $this->set_filter("extension","txtExtension",array("operator"=>"like"));
//        //source
//        $this->set_filter("source","txtSource",array("operator"=>"like"));
//        //folder
//        $this->set_filter("folder","txtFolder",array("operator"=>"like"));
//        //parent_path
//        $this->set_filter("parent_path","txtParentPath",array("operator"=>"like"));
//        //description
//        //$this->set_filter("description","txtDescription",array("operator"=>"like"));
//        //uri_local
//        $this->set_filter("uri_local","txtUriLocal",array("operator"=>"like"));
//        //uri_public
//        $this->set_filter("uri_public","txtUriPublic",array("operator"=>"like"));
//        //create_date
//        $this->set_filter("create_date","txtCreateDate",array("operator"=>"like"));
//        //modify_date
//        $this->set_filter("modify_date","txtModifyDate",array("operator"=>"like"));
//</editor-fold>
    }//load_config_list_filters()

    //list_5
    protected function set_listfilters_from_post()
    {
        //id
        $this->set_filter_value("id",$this->get_post("txtId"));
        //filename
        $this->set_filter_value("filename",$this->get_post("txtFilename"));
        //name
        $this->set_filter_value("name",$this->get_post("txtName"));
        //shortname
        $this->set_filter_value("shortname",$this->get_post("txtShortname"));
        //source
        $this->set_filter_value("source_filename",$this->get_post("txtSourceFilename"));        
        //selDisplaymode (no field)
        $this->set_filter_value("displaymode",$this->get_post("selDisplaymode"));        
        //selDisplaymode (no field)
        $this->set_filter_value("itemsperpage",$this->get_post("selItemsPerPage"));
//<editor-fold defaultstate="collapsed" desc="NIU">
        //code_erp
        //$this->set_filter_value("code_erp",$this->get_post("txtCodeErp"));
        //id_type
        //$this->set_filter_value("id_type",$this->get_post("selIdType"));
        //id_type_entity
        //$this->set_filter_value("id_type_entity",$this->get_post("selIdTypeEntity"));
        //id_owner_entity
        //$this->set_filter_value("id_owner_entity",$this->get_post("selIdOwnerEntity"));
        //is_bydefault
        //$this->set_filter_value("is_bydefault",$this->get_post("selIdBydefault"));
        //id_entity
        //$this->set_filter_value("id_entity",$this->get_post("selIdEntity"));
        //id_owner
//        $this->set_filter_value("id_owner",$this->get_post("selIdOwner"));
//        //id_thumb_1
//        $this->set_filter_value("id_thumb_1",$this->get_post("selIdThumb1"));
//        //id_thumb_2
//        $this->set_filter_value("id_thumb_2",$this->get_post("selIdThumb2"));
//        //id_thumb_3
//        $this->set_filter_value("id_thumb_3",$this->get_post("selIdThumb3"));
//        //id_thumb_4
//        $this->set_filter_value("id_thumb_4",$this->get_post("selIdThumb4"));
//        //id_thumb_5
//        $this->set_filter_value("id_thumb_5",$this->get_post("selIdThumb5"));
//        //width
//        $this->set_filter_value("width",$this->get_post("txtWidth"));
//        //height
//        $this->set_filter_value("height",$this->get_post("txtHeight"));
//        //resolution
//        $this->set_filter_value("resolution",$this->get_post("txtResolution"));
//        //order_by
//        $this->set_filter_value("order_by",$this->get_post("txtOrderBy"));
//        //rating
//        $this->set_filter_value("rating",$this->get_post("txtRating"));
//        //show
//        $this->set_filter_value("show",$this->get_post("txtShow"));
//        //is_public
//        $this->set_filter_value("is_public",$this->get_post("txtIsPublic"));
//        //is_file
//        $this->set_filter_value("is_file",$this->get_post("txtIsFile"));
//        //is_error
//        $this->set_filter_value("is_error",$this->get_post("txtIsError"));
//        //size
//        $this->set_filter_value("size",$this->get_post("txtSize"));
//        //img_title
//        $this->set_filter_value("img_title",$this->get_post("txtImgTitle"));
//        //anchor_text
//        $this->set_filter_value("anchor_text",$this->get_post("txtAnchorText"));
//        //csv_tags
//        $this->set_filter_value("csv_tags",$this->get_post("txtCsvTags"));
//        //extension
//        $this->set_filter_value("extension",$this->get_post("txtExtension"));
//        //source
//        $this->set_filter_value("source",$this->get_post("txtSource"));
//        //folder
//        $this->set_filter_value("folder",$this->get_post("txtFolder"));
//        //parent_path
//        $this->set_filter_value("parent_path",$this->get_post("txtParentPath"));
//        //information
//        $this->set_filter_value("information",$this->get_post("txaInformation"));
//        //information_extra
//        $this->set_filter_value("information_extra",$this->get_post("txaInformationExtra"));
//        //description
//        //$this->set_filter_value("description",$this->get_post("txtDescription"));
//        //uri_local
//        $this->set_filter_value("uri_local",$this->get_post("txtUriLocal"));
//        //uri_public
//        $this->set_filter_value("uri_public",$this->get_post("txtUriPublic"));
//        //create_date
//        $this->set_filter_value("create_date",$this->get_post("txtCreateDate"));
//        //modify_date
//        $this->set_filter_value("modify_date",$this->get_post("txtModifyDate"));
//</editor-fold>                
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
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtId",tr_pe_fil_id));
        $arFields[] = $oAuxWrapper;
        //filename
        $oAuxField = new HelperInputText("txtFilename","txtFilename");
        $oAuxField->set_value($this->get_post("txtFilename"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtFilename",tr_pe_fil_filename));
        $arFields[] = $oAuxWrapper;  
        //source_filename
        $oAuxField = new HelperInputText("txtSourceFilename","txtSourceFilename");
        $oAuxField->set_value($this->get_post("txtSourceFilename"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtSourceFilename",tr_pe_fil_source_filename));
        $arFields[] = $oAuxWrapper;        
        //selDisplaymode (no field)
        $oTypeEntity = new ModelPictureArray();
        $arOptions = array("grid"=>"List","catalog"=>"Catalog");
        $oAuxField = new HelperSelect($arOptions,"selDisplaymode","selDisplaymode");
        $oAuxField->set_value_to_select($this->get_post("selDisplaymode"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selDisplaymode",tr_pe_fil_displaymode));
        $arFields[] = $oAuxWrapper;    
//<editor-fold defaultstate="collapsed" desc="NIU">    
//        //name
//        $oAuxField = new HelperInputText("txtName","txtName");
//        $oAuxField->set_value($this->get_post("txtName"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtName",tr_pe_fil_name));
//        $arFields[] = $oAuxWrapper;
//        //shortname
//        $oAuxField = new HelperInputText("txtShortname","txtShortname");
//        $oAuxField->set_value($this->get_post("txtShortname"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtShortname",tr_pe_fil_shortname));
//        $arFields[] = $oAuxWrapper;
        //code_erp
        //$oAuxField = new HelperInputText("txtCodeErp","txtCodeErp");
        //$oAuxField->set_value($this->get_post("txtCodeErp"));
        //$oAuxField->on_entersubmit();
        //$oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtCodeErp",tr_pe_fil_code_erp));
        //$arFields[] = $oAuxWrapper;
        //id_type
//        $oType = new ModelPicture();
//        $arOptions = $oType->get_picklist();
//        $oAuxField = new HelperSelect($arOptions,"selIdType","selIdType");
//        $oAuxField->set_value_to_select($this->get_post("selIdType"));
//        $oAuxField->set_postback();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdType",tr_pe_fil_id_type));
//        $arFields[] = $oAuxWrapper;
        //id_type_entity
//        $oTypeEntity = new ModelPictureArray();
//        $arOptions = $oTypeEntity->get_picklist_by_type("entities");
//        $oAuxField = new HelperSelect($arOptions,"selIdTypeEntity","selIdTypeEntity");
//        $oAuxField->set_value_to_select($this->get_post("selIdTypeEntity"));
//        $oAuxField->set_postback();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdTypeEntity",tr_pe_fil_id_type_entity));
//        $arFields[] = $oAuxWrapper;
//        //id_owner_entity
//        $oOwnerEntity = new ModelPicture();
//        $arOptions = $oOwnerEntity->get_picklist();
//        $oAuxField = new HelperSelect($arOptions,"selIdOwnerEntity","selIdOwnerEntity");
//        $oAuxField->set_value_to_select($this->get_post("selIdOwnerEntity"));
//        $oAuxField->set_postback();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdOwnerEntity",tr_pe_fil_id_owner_entity));
//        $arFields[] = $oAuxWrapper;
//        //is_bydefault
//        $oBydefault = new ModelPicture();
//        $arOptions = $oBydefault->get_picklist();
//        $oAuxField = new HelperSelect($arOptions,"selIdBydefault","selIdBydefault");
//        $oAuxField->set_value_to_select($this->get_post("selIdBydefault"));
//        $oAuxField->set_postback();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdBydefault",tr_pe_fil_is_bydefault));
//        $arFields[] = $oAuxWrapper;
//        //id_entity
//        $oEntity = new ModelPicture();
//        $arOptions = $oEntity->get_picklist();
//        $oAuxField = new HelperSelect($arOptions,"selIdEntity","selIdEntity");
//        $oAuxField->set_value_to_select($this->get_post("selIdEntity"));
//        $oAuxField->set_postback();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdEntity",tr_pe_fil_id_entity));
//        $arFields[] = $oAuxWrapper;
//        //id_owner
//        $oOwner = new ModelPicture();
//        $arOptions = $oOwner->get_picklist();
//        $oAuxField = new HelperSelect($arOptions,"selIdOwner","selIdOwner");
//        $oAuxField->set_value_to_select($this->get_post("selIdOwner"));
//        $oAuxField->set_postback();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdOwner",tr_pe_fil_id_owner));
//        $arFields[] = $oAuxWrapper;
//        //id_thumb_1
//        $oThumb1 = new ModelPicture();
//        $arOptions = $oThumb1->get_picklist();
//        $oAuxField = new HelperSelect($arOptions,"selIdThumb1","selIdThumb1");
//        $oAuxField->set_value_to_select($this->get_post("selIdThumb1"));
//        $oAuxField->set_postback();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdThumb1",tr_pe_fil_id_thumb_1));
//        $arFields[] = $oAuxWrapper;
//        //id_thumb_2
//        $oThumb2 = new ModelPicture();
//        $arOptions = $oThumb2->get_picklist();
//        $oAuxField = new HelperSelect($arOptions,"selIdThumb2","selIdThumb2");
//        $oAuxField->set_value_to_select($this->get_post("selIdThumb2"));
//        $oAuxField->set_postback();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdThumb2",tr_pe_fil_id_thumb_2));
//        $arFields[] = $oAuxWrapper;
//        //id_thumb_3
//        $oThumb3 = new ModelPicture();
//        $arOptions = $oThumb3->get_picklist();
//        $oAuxField = new HelperSelect($arOptions,"selIdThumb3","selIdThumb3");
//        $oAuxField->set_value_to_select($this->get_post("selIdThumb3"));
//        $oAuxField->set_postback();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdThumb3",tr_pe_fil_id_thumb_3));
//        $arFields[] = $oAuxWrapper;
//        //id_thumb_4
//        $oThumb4 = new ModelPicture();
//        $arOptions = $oThumb4->get_picklist();
//        $oAuxField = new HelperSelect($arOptions,"selIdThumb4","selIdThumb4");
//        $oAuxField->set_value_to_select($this->get_post("selIdThumb4"));
//        $oAuxField->set_postback();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdThumb4",tr_pe_fil_id_thumb_4));
//        $arFields[] = $oAuxWrapper;
//        //id_thumb_5
//        $oThumb5 = new ModelPicture();
//        $arOptions = $oThumb5->get_picklist();
//        $oAuxField = new HelperSelect($arOptions,"selIdThumb5","selIdThumb5");
//        $oAuxField->set_value_to_select($this->get_post("selIdThumb5"));
//        $oAuxField->set_postback();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdThumb5",tr_pe_fil_id_thumb_5));
//        $arFields[] = $oAuxWrapper;
//        //width
//        $oAuxField = new HelperInputText("txtWidth","txtWidth");
//        $oAuxField->set_value($this->get_post("txtWidth"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtWidth",tr_pe_fil_width));
//        $arFields[] = $oAuxWrapper;
//        //height
//        $oAuxField = new HelperInputText("txtHeight","txtHeight");
//        $oAuxField->set_value($this->get_post("txtHeight"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtHeight",tr_pe_fil_height));
//        $arFields[] = $oAuxWrapper;
//        //resolution
//        $oAuxField = new HelperInputText("txtResolution","txtResolution");
//        $oAuxField->set_value($this->get_post("txtResolution"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtResolution",tr_pe_fil_resolution));
//        $arFields[] = $oAuxWrapper;
//        //order_by
//        $oAuxField = new HelperInputText("txtOrderBy","txtOrderBy");
//        $oAuxField->set_value($this->get_post("txtOrderBy"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtOrderBy",tr_pe_fil_order_by));
//        $arFields[] = $oAuxWrapper;
//        //rating
//        $oAuxField = new HelperInputText("txtRating","txtRating");
//        $oAuxField->set_value($this->get_post("txtRating"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtRating",tr_pe_fil_rating));
//        $arFields[] = $oAuxWrapper;
        //show
//        $oAuxField = new HelperInputText("txtShow","txtShow");
//        $oAuxField->set_value($this->get_post("txtShow"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtShow",tr_pe_fil_show));
//        $arFields[] = $oAuxWrapper;
//        //is_public
//        $oAuxField = new HelperInputText("txtIsPublic","txtIsPublic");
//        $oAuxField->set_value($this->get_post("txtIsPublic"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtIsPublic",tr_pe_fil_is_public));
//        $arFields[] = $oAuxWrapper;
//        //is_file
//        $oAuxField = new HelperInputText("txtIsFile","txtIsFile");
//        $oAuxField->set_value($this->get_post("txtIsFile"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtIsFile",tr_pe_fil_is_file));
//        $arFields[] = $oAuxWrapper;
//        //is_error
//        $oAuxField = new HelperInputText("txtIsError","txtIsError");
//        $oAuxField->set_value($this->get_post("txtIsError"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtIsError",tr_pe_fil_is_error));
//        $arFields[] = $oAuxWrapper;
//        //size
//        $oAuxField = new HelperInputText("txtSize","txtSize");
//        $oAuxField->set_value($this->get_post("txtSize"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtSize",tr_pe_fil_size));
//        $arFields[] = $oAuxWrapper;
        //img_title
//        $oAuxField = new HelperInputText("txtImgTitle","txtImgTitle");
//        $oAuxField->set_value($this->get_post("txtImgTitle"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtImgTitle",tr_pe_fil_img_title));
//        $arFields[] = $oAuxWrapper;
//        //anchor_text
//        $oAuxField = new HelperInputText("txtAnchorText","txtAnchorText");
//        $oAuxField->set_value($this->get_post("txtAnchorText"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtAnchorText",tr_pe_fil_anchor_text));
//        $arFields[] = $oAuxWrapper;
        //csv_tags
//        $oAuxField = new HelperInputText("txtCsvTags","txtCsvTags");
//        $oAuxField->set_value($this->get_post("txtCsvTags"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtCsvTags",tr_pe_fil_csv_tags));
//        $arFields[] = $oAuxWrapper;
//        //extension
//        $oAuxField = new HelperInputText("txtExtension","txtExtension");
//        $oAuxField->set_value($this->get_post("txtExtension"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtExtension",tr_pe_fil_extension));
//        $arFields[] = $oAuxWrapper;
//        //source
//        $oAuxField = new HelperInputText("txtSource","txtSource");
//        $oAuxField->set_value($this->get_post("txtSource"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtSource",tr_pe_fil_source));
//        $arFields[] = $oAuxWrapper;
//        //folder
//        $oAuxField = new HelperInputText("txtFolder","txtFolder");
//        $oAuxField->set_value($this->get_post("txtFolder"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtFolder",tr_pe_fil_folder));
//        $arFields[] = $oAuxWrapper;
//        //parent_path
//        $oAuxField = new HelperInputText("txtParentPath","txtParentPath");
//        $oAuxField->set_value($this->get_post("txtParentPath"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtParentPath",tr_pe_fil_parent_path));
//        $arFields[] = $oAuxWrapper;
//        //information
//        $oAuxField = new HelperTextarea("txaInformation","txaInformation");
//        $oAuxField->set_innerhtml($this->get_post("txaInformation"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txaInformation",tr_pe_fil_information));
//        $arFields[] = $oAuxWrapper;
//        //information_extra
//        $oAuxField = new HelperTextarea("txaInformationExtra","txaInformationExtra");
//        $oAuxField->set_innerhtml($this->get_post("txaInformationExtra"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txaInformationExtra",tr_pe_fil_information_extra));
//        $arFields[] = $oAuxWrapper;
//        //description
//        //$oAuxField = new HelperInputText("txtDescription","txtDescription");
//        //$oAuxField->set_value($this->get_post("txtDescription"));
//        //$oAuxField->on_entersubmit();
//        //$oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtDescription",tr_pe_fil_description));
//        //$arFields[] = $oAuxWrapper;
//        //uri_local
//        $oAuxField = new HelperInputText("txtUriLocal","txtUriLocal");
//        $oAuxField->set_value($this->get_post("txtUriLocal"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtUriLocal",tr_pe_fil_uri_local));
//        $arFields[] = $oAuxWrapper;
//        //uri_public
//        $oAuxField = new HelperInputText("txtUriPublic","txtUriPublic");
//        $oAuxField->set_value($this->get_post("txtUriPublic"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtUriPublic",tr_pe_fil_uri_public));
//        $arFields[] = $oAuxWrapper;

//        //create_date
//        $oAuxField = new HelperInputText("txtCreateDate","txtCreateDate");
//        $oAuxField->set_value($this->get_post("txtCreateDate"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtCreateDate",tr_pe_fil_create_date));
//        $arFields[] = $oAuxWrapper;
//        //modify_date
//        $oAuxField = new HelperInputText("txtModifyDate","txtModifyDate");
//        $oAuxField->set_value($this->get_post("txtModifyDate"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtModifyDate",tr_pe_fil_modify_date));
//        $arFields[] = $oAuxWrapper;
//</editor-fold>
        return $arFields;
    }//get_list_filters()

    //list_7
    protected function get_list_columns()
    {
        $arColumns["id"] = tr_pe_col_id;
        $arColumns["filename"] = tr_pe_col_filename;
        //$arColumns["img_title"] = tr_pe_col_img_title;        
        //$arColumns["information"] = tr_pe_col_information;
        
//<editor-fold defaultstate="collapsed" desc="NIU">
        //$arColumns["code_erp"] = tr_pe_col_code_erp;
        //$arColumns["id_type"] = tr_pe_col_id_type;
        //$arColumns["name"] = tr_pe_col_name;
        //$arColumns["shortname"] = tr_pe_col_shortname;
        //$arColumns["type"] = tr_pe_col_id_type;
        //$arColumns["id_type_entity"] = tr_pe_col_id_type_entity;
//        $arColumns["entity"] = tr_pe_col_id_type_entity;
        //$arColumns["id_owner_entity"] = tr_pe_col_id_owner_entity;
//        $arColumns["ownerentity"] = tr_pe_col_id_owner_entity;
//        //$arColumns["is_bydefault"] = tr_pe_col_is_bydefault;
//        $arColumns["bydefault"] = tr_pe_col_is_bydefault;
//        //$arColumns["id_entity"] = tr_pe_col_id_entity;
//        $arColumns["entity"] = tr_pe_col_id_entity;
//        //$arColumns["id_owner"] = tr_pe_col_id_owner;
//        $arColumns["owner"] = tr_pe_col_id_owner;
//        //$arColumns["id_thumb_1"] = tr_pe_col_id_thumb_1;
//        $arColumns["thumb1"] = tr_pe_col_id_thumb_1;
//        //$arColumns["id_thumb_2"] = tr_pe_col_id_thumb_2;
//        $arColumns["thumb2"] = tr_pe_col_id_thumb_2;
//        //$arColumns["id_thumb_3"] = tr_pe_col_id_thumb_3;
//        $arColumns["thumb3"] = tr_pe_col_id_thumb_3;
//        //$arColumns["id_thumb_4"] = tr_pe_col_id_thumb_4;
//        $arColumns["thumb4"] = tr_pe_col_id_thumb_4;
//        //$arColumns["id_thumb_5"] = tr_pe_col_id_thumb_5;
//        $arColumns["thumb5"] = tr_pe_col_id_thumb_5;
//        $arColumns["width"] = tr_pe_col_width;
//        $arColumns["height"] = tr_pe_col_height;
//        $arColumns["resolution"] = tr_pe_col_resolution;
//        $arColumns["order_by"] = tr_pe_col_order_by;
//        $arColumns["rating"] = tr_pe_col_rating;
        //$arColumns["show"] = tr_pe_col_show;
//        $arColumns["is_public"] = tr_pe_col_is_public;
//        $arColumns["is_file"] = tr_pe_col_is_file;
//        $arColumns["is_error"] = tr_pe_col_is_error;
//        $arColumns["size"] = tr_pe_col_size;
//        $arColumns["anchor_text"] = tr_pe_col_anchor_text;
//        $arColumns["csv_tags"] = tr_pe_col_csv_tags;
//        $arColumns["extension"] = tr_pe_col_extension;
//        $arColumns["source"] = tr_pe_col_source;
//        $arColumns["folder"] = tr_pe_col_folder;
//        $arColumns["parent_path"] = tr_pe_col_parent_path;
        //$arColumns["information_extra"] = tr_pe_col_information_extra;
        //$arColumns["description"] = tr_pe_col_description;
//        $arColumns["uri_local"] = tr_pe_col_uri_local;
//        $arColumns["uri_public"] = tr_pe_col_uri_public;
//        $arColumns["create_date"] = tr_pe_col_create_date;
//        $arColumns["modify_date"] = tr_pe_col_modify_date;
//</editor-fold>        
        return $arColumns;
    }//get_list_columns()

    //list_8
    public function build_grid($arList,$arColumns,$arObjFilter,ComponentPage $oPage)
    {
       //This method adds objects controls to search list form
        $oTableList = new HelperTableTyped($arList,$arColumns);
        $oTableList->set_fields($arObjFilter);
        $oTableList->add_class("table table-striped table-bordered table-condensed");
        $oTableList->set_keyfields(array("id"));
        $oTableList->is_ordenable();
        $oTableList->set_orderby($this->get_orderby());
        $oTableList->set_orderby_type($this->get_ordertype());
        //$oTableList->set_column_anchor($arColumns);
        //COLUMNS CONFIGURATION
        //if($this->oPermission->is_quarantine()||$this->oPermission->is_delete())
            //$oTableList->set_column_pickmultiple();//checks column
        if($this->oPermission->is_read())
            $oTableList->set_column_detail();
        //if($this->oPermission->is_quarantine())
            //$oTableList->set_column_quarantine();
        //if($this->oPermission->is_delete())
            //$oTableList->set_column_delete();
        $arExtra[] = array("position"=>6,"label"=>"Image");
        
        $oImage = new HelperImage();
        $oImage->set_id("%name%");
        $oImage->set_src("/images/pictures/products/%folder%/%folder%_%id%_th1.%extension%");
        $oImage->set_alt("%img_title%");
        $oImage->set_title("%source_filename%");
        $oImage->add_style("width:50px");
        $oImage->add_style("width:50px");
        
        $oAnchor = new HelperAnchor();
        $oAnchor->set_href("/images/pictures/products/%folder%/%filename%");
        $oAnchor->set_target("blank");
        $oAnchor->set_innerhtml($oImage->get_html());
        //bugss();
        $oTableList->add_extra_colums($arExtra);
        $oTableList->set_column_raw(array("virtual_0"=>$oAnchor));
        //$arFormat = array("amount_total"=>"numeric2","date"=>"date","delivery_date"=>"date");
        //$oTableList->set_format_columns($arFormat);
        //parametros a pasar al popup
        //$oTableList->set_multiassign(array("keys"=>array("k"=>1,"k2"=>2)));
        $oTableList->set_current_page($oPage->get_current());
        $oTableList->set_next_page($oPage->get_next());
        $oTableList->set_first_page($oPage->get_first());
        $oTableList->set_last_page($oPage->get_last());
        $oTableList->set_total_regs($oPage->get_total_regs());
        $oTableList->set_total_pages($oPage->get_total());
        $oTableList->set_items_per_page($oPage->get_items_per_page());
        
        return $oTableList;
    }//build_grid
        
    //list_9
    protected function build_links($arPictures)
    {
        $arLinks = array();
        //bug($arPictures[0]);
        foreach($arPictures as $i=>$arPicture)
        {
            $iPos = $i+1;
            //images/pictures/products/%folder%/%folder%_%id%_th1.%extension%
            $arPathInfo = pathinfo($arPicture["filename"]);
            //bug($arPathInfo);
            $sFileName = $arPathInfo["filename"];
            $sExtension = $arPathInfo["extension"];
            $sSrc = $arPicture["uri_public"]."/".$arPicture["folder"]."/$sFileName"."_th2.$sExtension";
            //bug($sSrc);
            $sHref = $arPicture["uri_public"]."/".$arPicture["folder"]."/".$arPicture["filename"];
            //array("src"=>$sUrlBase."logo_unioncaribe_496_133.jpg","href"=>$this->build_url("modulebuilder"),"innerhtml"=>"3","alt"=>"ok3"
            //,"text"=>array("text","link"));
            $arLinks[] = array
            (
                "src"=>$sSrc
                ,"href"=>$sHref
                ,"target"=>"blank"
                ,"innerhtml"=>"no hace nada"
                ,"alt"=>$arPicture["filename"]
                ,"text"=>"($iPos) ".$arPicture["source_filename"]
            );
        }
        return $arLinks;
    }//build_links
    
    //list_10
    public function build_catalog($arList,$arObjFilter,ComponentPage $oPage)
    {
        //TABLE
        $arList = $this->build_links($arList);
        $oTableList = new HelperImagelist($arList,"ulAlbum");
        $oTableList->set_fields($arObjFilter);
        
        $oTableList->set_orderby($this->get_orderby());
        $oTableList->set_orderby_type($this->get_ordertype());        
        $oTableList->set_current_page($oPage->get_current());
        $oTableList->set_items_per_page($oPage->get_items_per_page());
        $oTableList->set_next_page($oPage->get_next());
        $oTableList->set_first_page($oPage->get_first());
        $oTableList->set_last_page($oPage->get_last());
        $oTableList->set_total_regs($oPage->get_total_regs());
        $oTableList->set_total_pages($oPage->get_total());
        $oTableList->set_li_class("span3");
        
        return $oTableList;
    }//build_catalog
        
    //list_11
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
        //bugp("selItemsPerPage","antes");
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
        $this->oPicture->set_orderby($this->get_orderby());
        $this->oPicture->set_ordertype($this->get_ordertype());
        $this->oPicture->set_filters($this->get_filter_searchconfig());
        $this->oPicture->add_filter("id_entity",array("value"=>$this->oProduct->get_id()));
        $this->oPicture->add_filter("id_type_entity",array("value"=>4));
        //hierarchy recover
        //$this->oPicture->set_select_user($this->oSessionUser->get_id());
        $this->oPicture->set_show_thumbs(0);
        $arList = $this->oPicture->get_select_all_ids();
        $iRequestPage = $this->get_post("selPage");
        $iItemsPerPage = $this->get_post("selItemsPerPage");
        //bugp("selItemsPerPage","despues");
        $oPage = new ComponentPage($arList,$iRequestPage,$iItemsPerPage);
        $arList = $oPage->get_items_to_show();
        $arList = $this->oPicture->get_select_all_by_ids($arList);
        //TABLE
        $oTableList = NULL;
        //grid || catalog
        if($this->is_catalog())
            $oTableList = $this->build_catalog($arList,$arObjFilter,$oPage);
        else
            $oTableList = $this->build_grid($arList,$arColumns,$arObjFilter,$oPage);
        
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
        $arLinks["list"]=array("href"=>$sUrlLink,"innerhtml"=>tr_entities);                
        $sUrlLink = $this->build_url("products",NULL,"update","id=".$this->get_get("id_product"));
        $arLinks["detail"]=array("href"=>$sUrlLink,"innerhtml"=>"Product: ".$this->oProduct->get_id()." - ".$this->oProduct->get_description());
        $sUrlLink = $this->build_url("products","pictures","get_list","id_product=".$this->get_get("id_product"));
        $arLinks["pictures"]=array("href"=>$sUrlLink,"innerhtml"=>tr_pe_entities);
        $sUrlLink = $this->build_url("products","pictures","insert","id_product=".$this->get_get("id_product"));
        $arLinks["newpicture"]=array("href"=>$sUrlLink,"innerhtml"=>tr_pe_entity_new);        
        $oScrumbs = new AppHelperBreadscrumbs($arLinks);
        return $oScrumbs;        
    }//build_insert_scrumbs()

    //insert_2
    protected function build_insert_tabs()
    {
        $arTabs = array();
        //$sUrlTab = $this->build_url($this->sModuleName,NULL,"insert");
        //$arTabs["insert1"]=array("href"=>$sUrlTab,"innerhtml"=>tr_pe_instabs_1);
        //$sUrlTab = $this->build_url($this->sModuleName,NULL,"insert2");
        //$arTabs["insert2"]=array("href"=>$sUrlTab,"innerhtml"=>tr_pe_instabs_2);
        $oTabs = new AppHelperHeadertabs($arTabs,"insert1");
        return $oTabs;
    }//build_insert_tabs()
    
    //insert_3
    protected function build_insert_opbuttons()
    {
        $arOpButtons = array();
        $sUrl = $this->build_url("products","pictures","get_list","id_product=".$this->oProduct->get_id());
        $arOpButtons["list"] = array("href"=>$sUrl,"icon"=>"awe-search","innerhtml"=>tr_pe_insopbutton_list);
        //$arOpButtons["extra"] = array("href"=>$this->build_url("products","pictures"),"icon"=>"awe-xxxx","innerhtml"=>tr_pe_insopbutton_extra1);
        $oOpButtons = new AppHelperButtontabs(tr_pe_entities);
        $oOpButtons->set_tabs($arOpButtons);
        return $oOpButtons;        
    }//build_insert_opbuttons()

    //insert_4
    protected function build_insert_fields($usePost=0)
    {
        $arFields = array(); $oAuxField = NULL; $oAuxLabel = NULL;
        $arFields[]= new AppHelperFormhead(tr_pe_entities_new_of." ".$this->oProduct->get_description());
        //id_type_entity
        $oTypeEntity = new ModelPictureArray();
        $arOptions = $oTypeEntity->get_picklist_by_type("entities");
        $oAuxField = new HelperSelect($arOptions,"selIdTypeEntity","selIdTypeEntity");
        $oAuxField->set_value_to_select("4");
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdTypeEntity",tr_pe_ins_id_type_entity));
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdTypeEntity"));
        $oAuxLabel = new HelperLabel("selIdTypeEntity",tr_pe_ins_id_type_entity,"lblIdTypeEntity");
        //@TODOTEMPLATE
        $oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //MaxSize (Not field)<input type="hidden" name="MAX_FILE_SIZE" value="30000" />
        $oAuxField = new HelperInputHidden("MAX_FILE_SIZE","MAX_FILE_SIZE");
        $oAuxField->set_value($this->get_post_max_size());
        $oAuxField->add_extras("unit","bytes");
        $arFields[] = new ApphelperControlGroup($oAuxField);
        
        for($i=0; $i<$this->iNumUploads; $i++)
        {
            //Picture1 (Not field)
            $oAuxField = new HelperInputFile("filPicture$i","filPicture[]");
            $oAuxLabel = new HelperLabel("filPicture$i",tr_pe_entity." $i","lblPicture$i");
            //$oAuxField->readonly();$oAuxField->add_class("readonly");
            $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel); 
            //Picture1 (Not field)
            $oAuxField = new HelperInputText("txtHttpPicture$i","txtPictureHttp[]");
            $oAuxField->set_maxlength(500);
            $oAuxLabel = new HelperLabel("txtHttpPicture$i",tr_pe_entity." $i http:","lblPictureUri$i");
            //$oAuxField->readonly();$oAuxField->add_class("readonly");
            $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);             
        }

//<editor-fold defaultstate="collapsed" desc="NIU">
        //id
        //$oAuxField = new HelperInputText("txtId","txtId");
        //$oAuxField->is_primarykey();
        //if($usePost) $oAuxField->set_value($this->get_post("txtId"));
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        //$arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //name
//        $oAuxField = new HelperInputText("txtName","txtName");
//        if($usePost) $oAuxField->set_value($this->get_post("txtName"));
//        $oAuxLabel = new HelperLabel("txtName",tr_pe_ins_name,"lblName");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //shortname
//        $oAuxField = new HelperInputText("txtShortname","txtShortname");
//        if($usePost) $oAuxField->set_value($this->get_post("txtShortname"));
//        $oAuxLabel = new HelperLabel("txtShortname",tr_pe_ins_shortname,"lblShortname");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //code_erp
        //$oAuxField = new HelperInputText("txtCodeErp","txtCodeErp");
        //if($usePost) $oAuxField->set_value($this->get_post("txtCodeErp"));
        //$oAuxLabel = new HelperLabel("txtCodeErp",tr_pe_ins_code_erp,"lblCodeErp");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        //$arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //id_type
//        $oType = new ModelPicture();
//        $arOptions = $oType->get_picklist();
//        $oAuxField = new HelperSelect($arOptions,"selIdType","selIdType");
//        $oAuxField->set_value_to_select($this->get_post("selIdType"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdType",tr_pe_ins_id_type));
//        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdType"));
//        $oAuxLabel = new HelperLabel("selIdType",tr_pe_ins_id_type,"lblIdType");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //id_entity
//        $oEntity = new ModelPicture();
//        $arOptions = $oEntity->get_picklist();
//        $oAuxField = new HelperSelect($arOptions,"selIdEntity","selIdEntity");
//        $oAuxField->set_value_to_select($this->get_post("selIdEntity"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdEntity",tr_pe_ins_id_entity));
//        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdEntity"));
//        $oAuxLabel = new HelperLabel("selIdEntity",tr_pe_ins_id_entity,"lblIdEntity");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //filename
//        $oAuxField = new HelperInputText("txtFilename","txtFilename");
//        if($usePost) $oAuxField->set_value($this->get_post("txtFilename"));
//        $oAuxLabel = new HelperLabel("txtFilename",tr_pe_ins_filename,"lblName");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //img_title
//        $oAuxField = new HelperInputText("txtImgTitle","txtImgTitle");
//        if($usePost) $oAuxField->set_value($this->get_post("txtImgTitle"));
//        $oAuxLabel = new HelperLabel("txtImgTitle",tr_pe_ins_img_title,"lblImgTitle");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel); 
//        //information
//        $oAuxField = new HelperTextarea("txaInformation","txaInformation");
//        if($usePost) $oAuxField->set_innerhtml($this->get_post("txaInformation"));
//        $oAuxLabel = new HelperLabel("txaInformation",tr_pe_ins_information,"lblInformation");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //information_extra
//        $oAuxField = new HelperTextarea("txaInformationExtra","txaInformationExtra");
//        if($usePost) $oAuxField->set_innerhtml($this->get_post("txaInformationExtra"));
//        $oAuxLabel = new HelperLabel("txaInformationExtra",tr_pe_ins_information_extra,"lblInformationExtra");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);        
//        //id_owner_entity
//        $oOwnerEntity = new ModelPicture();
//        $arOptions = $oOwnerEntity->get_picklist();
//        $oAuxField = new HelperSelect($arOptions,"selIdOwnerEntity","selIdOwnerEntity");
//        $oAuxField->set_value_to_select($this->get_post("selIdOwnerEntity"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdOwnerEntity",tr_pe_ins_id_owner_entity));
//        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdOwnerEntity"));
//        $oAuxLabel = new HelperLabel("selIdOwnerEntity",tr_pe_ins_id_owner_entity,"lblIdOwnerEntity");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //is_bydefault
//        $oBydefault = new ModelPicture();
//        $arOptions = $oBydefault->get_picklist();
//        $oAuxField = new HelperSelect($arOptions,"selIdBydefault","selIdBydefault");
//        $oAuxField->set_value_to_select($this->get_post("selIdBydefault"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdBydefault",tr_pe_ins_is_bydefault));
//        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdBydefault"));
//        $oAuxLabel = new HelperLabel("selIdBydefault",tr_pe_ins_is_bydefault,"lblIdBydefault");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel); 
//        //id_owner
//        $oOwner = new ModelPicture();
//        $arOptions = $oOwner->get_picklist();
//        $oAuxField = new HelperSelect($arOptions,"selIdOwner","selIdOwner");
//        $oAuxField->set_value_to_select($this->get_post("selIdOwner"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdOwner",tr_pe_ins_id_owner));
//        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdOwner"));
//        $oAuxLabel = new HelperLabel("selIdOwner",tr_pe_ins_id_owner,"lblIdOwner");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //id_thumb_1
//        $oThumb1 = new ModelPicture();
//        $arOptions = $oThumb1->get_picklist();
//        $oAuxField = new HelperSelect($arOptions,"selIdThumb1","selIdThumb1");
//        $oAuxField->set_value_to_select($this->get_post("selIdThumb1"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdThumb1",tr_pe_ins_id_thumb_1));
//        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdThumb1"));
//        $oAuxLabel = new HelperLabel("selIdThumb1",tr_pe_ins_id_thumb_1,"lblIdThumb1");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //id_thumb_2
//        $oThumb2 = new ModelPicture();
//        $arOptions = $oThumb2->get_picklist();
//        $oAuxField = new HelperSelect($arOptions,"selIdThumb2","selIdThumb2");
//        $oAuxField->set_value_to_select($this->get_post("selIdThumb2"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdThumb2",tr_pe_ins_id_thumb_2));
//        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdThumb2"));
//        $oAuxLabel = new HelperLabel("selIdThumb2",tr_pe_ins_id_thumb_2,"lblIdThumb2");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //id_thumb_3
//        $oThumb3 = new ModelPicture();
//        $arOptions = $oThumb3->get_picklist();
//        $oAuxField = new HelperSelect($arOptions,"selIdThumb3","selIdThumb3");
//        $oAuxField->set_value_to_select($this->get_post("selIdThumb3"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdThumb3",tr_pe_ins_id_thumb_3));
//        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdThumb3"));
//        $oAuxLabel = new HelperLabel("selIdThumb3",tr_pe_ins_id_thumb_3,"lblIdThumb3");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //id_thumb_4
//        $oThumb4 = new ModelPicture();
//        $arOptions = $oThumb4->get_picklist();
//        $oAuxField = new HelperSelect($arOptions,"selIdThumb4","selIdThumb4");
//        $oAuxField->set_value_to_select($this->get_post("selIdThumb4"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdThumb4",tr_pe_ins_id_thumb_4));
//        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdThumb4"));
//        $oAuxLabel = new HelperLabel("selIdThumb4",tr_pe_ins_id_thumb_4,"lblIdThumb4");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //id_thumb_5
//        $oThumb5 = new ModelPicture();
//        $arOptions = $oThumb5->get_picklist();
//        $oAuxField = new HelperSelect($arOptions,"selIdThumb5","selIdThumb5");
//        $oAuxField->set_value_to_select($this->get_post("selIdThumb5"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdThumb5",tr_pe_ins_id_thumb_5));
//        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdThumb5"));
//        $oAuxLabel = new HelperLabel("selIdThumb5",tr_pe_ins_id_thumb_5,"lblIdThumb5");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //width
//        $oAuxField = new HelperInputText("txtWidth","txtWidth");
//        $oAuxField->set_value(0);
//        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtWidth")));
//        $oAuxLabel = new HelperLabel("txtWidth",tr_pe_ins_width,"lblWidth");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //height
//        $oAuxField = new HelperInputText("txtHeight","txtHeight");
//        $oAuxField->set_value(0);
//        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtHeight")));
//        $oAuxLabel = new HelperLabel("txtHeight",tr_pe_ins_height,"lblHeight");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //resolution
//        $oAuxField = new HelperInputText("txtResolution","txtResolution");
//        $oAuxField->set_value(0);
//        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtResolution")));
//        $oAuxLabel = new HelperLabel("txtResolution",tr_pe_ins_resolution,"lblResolution");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //order_by
//        $oAuxField = new HelperInputText("txtOrderBy","txtOrderBy");
//        $oAuxField->set_value(0);
//        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtOrderBy")));
//        $oAuxLabel = new HelperLabel("txtOrderBy",tr_pe_ins_order_by,"lblOrderBy");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //rating
//        $oAuxField = new HelperInputText("txtRating","txtRating");
//        $oAuxField->set_value(0);
//        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtRating")));
//        $oAuxLabel = new HelperLabel("txtRating",tr_pe_ins_rating,"lblRating");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //show
//        $oAuxField = new HelperInputText("txtShow","txtShow");
//        $oAuxField->set_value(0);
//        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtShow")));
//        $oAuxLabel = new HelperLabel("txtShow",tr_pe_ins_show,"lblShow");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //is_public
//        $oAuxField = new HelperInputText("txtIsPublic","txtIsPublic");
//        $oAuxField->set_value(0);
//        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtIsPublic")));
//        $oAuxLabel = new HelperLabel("txtIsPublic",tr_pe_ins_is_public,"lblIsPublic");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //is_file
//        $oAuxField = new HelperInputText("txtIsFile","txtIsFile");
//        $oAuxField->set_value(0);
//        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtIsFile")));
//        $oAuxLabel = new HelperLabel("txtIsFile",tr_pe_ins_is_file,"lblIsFile");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //is_error
//        $oAuxField = new HelperInputText("txtIsError","txtIsError");
//        $oAuxField->set_value(0);
//        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtIsError")));
//        $oAuxLabel = new HelperLabel("txtIsError",tr_pe_ins_is_error,"lblIsError");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //size
//        $oAuxField = new HelperInputText("txtSize","txtSize");
//        $oAuxField->set_value("0.00");
//        if($usePost) $oAuxField->set_value(dbbo_numeric2($this->get_post("txtSize")));
//        $oAuxLabel = new HelperLabel("txtSize",tr_pe_ins_size,"lblSize");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //anchor_text
//        $oAuxField = new HelperInputText("txtAnchorText","txtAnchorText");
//        if($usePost) $oAuxField->set_value($this->get_post("txtAnchorText"));
//        $oAuxLabel = new HelperLabel("txtAnchorText",tr_pe_ins_anchor_text,"lblAnchorText");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //csv_tags
//        $oAuxField = new HelperInputText("txtCsvTags","txtCsvTags");
//        if($usePost) $oAuxField->set_value($this->get_post("txtCsvTags"));
//        $oAuxLabel = new HelperLabel("txtCsvTags",tr_pe_ins_csv_tags,"lblCsvTags");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //extension
//        $oAuxField = new HelperInputText("txtExtension","txtExtension");
//        if($usePost) $oAuxField->set_value($this->get_post("txtExtension"));
//        $oAuxLabel = new HelperLabel("txtExtension",tr_pe_ins_extension,"lblExtension");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //source
//        $oAuxField = new HelperInputText("txtSource","txtSource");
//        if($usePost) $oAuxField->set_value($this->get_post("txtSource"));
//        $oAuxLabel = new HelperLabel("txtSource",tr_pe_ins_source,"lblSource");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //folder
//        $oAuxField = new HelperInputText("txtFolder","txtFolder");
//        if($usePost) $oAuxField->set_value($this->get_post("txtFolder"));
//        $oAuxLabel = new HelperLabel("txtFolder",tr_pe_ins_folder,"lblFolder");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //parent_path
//        $oAuxField = new HelperInputText("txtParentPath","txtParentPath");
//        if($usePost) $oAuxField->set_value($this->get_post("txtParentPath"));
//        $oAuxLabel = new HelperLabel("txtParentPath",tr_pe_ins_parent_path,"lblParentPath");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //description
//        //$oAuxField = new HelperInputText("txtDescription","txtDescription");
//        //if($usePost) $oAuxField->set_value($this->get_post("txtDescription"));
//        //$oAuxLabel = new HelperLabel("txtDescription",tr_pe_ins_description,"lblDescription");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        //$arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //uri_local
//        $oAuxField = new HelperInputText("txtUriLocal","txtUriLocal");
//        if($usePost) $oAuxField->set_value($this->get_post("txtUriLocal"));
//        $oAuxLabel = new HelperLabel("txtUriLocal",tr_pe_ins_uri_local,"lblUriLocal");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //uri_public
//        $oAuxField = new HelperInputText("txtUriPublic","txtUriPublic");
//        if($usePost) $oAuxField->set_value($this->get_post("txtUriPublic"));
//        $oAuxLabel = new HelperLabel("txtUriPublic",tr_pe_ins_uri_public,"lblUriPublic");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);

//        //create_date
//        $oAuxField = new HelperInputText("txtCreateDate","txtCreateDate");
//        if($usePost) $oAuxField->set_value($this->get_post("txtCreateDate"));
//        $oAuxLabel = new HelperLabel("txtCreateDate",tr_pe_ins_create_date,"lblCreateDate");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //modify_date
//        $oAuxField = new HelperInputText("txtModifyDate","txtModifyDate");
//        if($usePost) $oAuxField->set_value($this->get_post("txtModifyDate"));
//        $oAuxLabel = new HelperLabel("txtModifyDate",tr_pe_ins_modify_date,"lblModifyDate");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//</editor-fold>        

        //SAVE BUTTON
        $oAuxField = new HelperButtonBasic("butSave",tr_pe_ins_savebutton);
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
//<editor-fold defaultstate="collapsed" desc="NIU">        
        //$arFieldsConfig["id"] = array("controlid"=>"txtId","label"=>tr_pe_ins_id,"length"=>9,"type"=>array("numeric","required"));
        //$arFieldsConfig["code_erp"] = array("controlid"=>"txtCodeErp","label"=>tr_pe_ins_code_erp,"length"=>25,"type"=>array());
        //$arFieldsConfig["id_type"] = array("controlid"=>"selIdType","label"=>tr_pe_ins_id_type,"length"=>4,"type"=>array());
        //$arFieldsConfig["id_type_entity"] = array("controlid"=>"selIdTypeEntity","label"=>tr_pe_ins_id_type_entity,"length"=>4,"type"=>array());
        //$arFieldsConfig["id_owner_entity"] = array("controlid"=>"selIdOwnerEntity","label"=>tr_pe_ins_id_owner_entity,"length"=>4,"type"=>array());
        //$arFieldsConfig["is_bydefault"] = array("controlid"=>"selIdBydefault","label"=>tr_pe_ins_is_bydefault,"length"=>4,"type"=>array());
        //$arFieldsConfig["id_entity"] = array("controlid"=>"selIdEntity","label"=>tr_pe_ins_id_entity,"length"=>9,"type"=>array());
        //$arFieldsConfig["id_owner"] = array("controlid"=>"selIdOwner","label"=>tr_pe_ins_id_owner,"length"=>9,"type"=>array());
        //$arFieldsConfig["id_thumb_1"] = array("controlid"=>"selIdThumb1","label"=>tr_pe_ins_id_thumb_1,"length"=>9,"type"=>array());
        //$arFieldsConfig["id_thumb_2"] = array("controlid"=>"selIdThumb2","label"=>tr_pe_ins_id_thumb_2,"length"=>9,"type"=>array());
        //$arFieldsConfig["id_thumb_3"] = array("controlid"=>"selIdThumb3","label"=>tr_pe_ins_id_thumb_3,"length"=>9,"type"=>array());
        //$arFieldsConfig["id_thumb_4"] = array("controlid"=>"selIdThumb4","label"=>tr_pe_ins_id_thumb_4,"length"=>9,"type"=>array());
        //$arFieldsConfig["id_thumb_5"] = array("controlid"=>"selIdThumb5","label"=>tr_pe_ins_id_thumb_5,"length"=>9,"type"=>array());
//        $arFieldsConfig["name"] = array("controlid"=>"txtName","label"=>tr_pe_ins_name,"length"=>250,"type"=>array());
//        $arFieldsConfig["shortname"] = array("controlid"=>"txtShortname","label"=>tr_pe_ins_shortname,"length"=>100,"type"=>array());        
//        $arFieldsConfig["width"] = array("controlid"=>"txtWidth","label"=>tr_pe_ins_width,"length"=>4,"type"=>array());
//        $arFieldsConfig["height"] = array("controlid"=>"txtHeight","label"=>tr_pe_ins_height,"length"=>4,"type"=>array());
//        $arFieldsConfig["resolution"] = array("controlid"=>"txtResolution","label"=>tr_pe_ins_resolution,"length"=>4,"type"=>array());
//        $arFieldsConfig["order_by"] = array("controlid"=>"txtOrderBy","label"=>tr_pe_ins_order_by,"length"=>4,"type"=>array());
//        $arFieldsConfig["rating"] = array("controlid"=>"txtRating","label"=>tr_pe_ins_rating,"length"=>4,"type"=>array());
//        $arFieldsConfig["show"] = array("controlid"=>"txtShow","label"=>tr_pe_ins_show,"length"=>4,"type"=>array());
//        $arFieldsConfig["is_public"] = array("controlid"=>"txtIsPublic","label"=>tr_pe_ins_is_public,"length"=>4,"type"=>array());
//        $arFieldsConfig["is_file"] = array("controlid"=>"txtIsFile","label"=>tr_pe_ins_is_file,"length"=>4,"type"=>array());
//        $arFieldsConfig["is_error"] = array("controlid"=>"txtIsError","label"=>tr_pe_ins_is_error,"length"=>4,"type"=>array());
//        $arFieldsConfig["size"] = array("controlid"=>"txtSize","label"=>tr_pe_ins_size,"length"=>9,"type"=>array("numeric"));
//        $arFieldsConfig["img_title"] = array("controlid"=>"txtImgTitle","label"=>tr_pe_ins_img_title,"length"=>100,"type"=>array());
//        $arFieldsConfig["anchor_text"] = array("controlid"=>"txtAnchorText","label"=>tr_pe_ins_anchor_text,"length"=>100,"type"=>array());
//        $arFieldsConfig["csv_tags"] = array("controlid"=>"txtCsvTags","label"=>tr_pe_ins_csv_tags,"length"=>200,"type"=>array());
//        $arFieldsConfig["extension"] = array("controlid"=>"txtExtension","label"=>tr_pe_ins_extension,"length"=>5,"type"=>array());
//        $arFieldsConfig["source"] = array("controlid"=>"txtSource","label"=>tr_pe_ins_source,"length"=>250,"type"=>array());
//        $arFieldsConfig["folder"] = array("controlid"=>"txtFolder","label"=>tr_pe_ins_folder,"length"=>50,"type"=>array());
//        $arFieldsConfig["parent_path"] = array("controlid"=>"txtParentPath","label"=>tr_pe_ins_parent_path,"length"=>250,"type"=>array());
//        $arFieldsConfig["information"] = array("controlid"=>"txaInformation","label"=>tr_pe_ins_information,"length"=>250,"type"=>array());
//        $arFieldsConfig["information_extra"] = array("controlid"=>"txaInformationExtra","label"=>tr_pe_ins_information_extra,"length"=>250,"type"=>array());
//        //$arFieldsConfig["description"] = array("controlid"=>"txtDescription","label"=>tr_pe_ins_description,"length"=>200,"type"=>array());
//        $arFieldsConfig["uri_local"] = array("controlid"=>"txtUriLocal","label"=>tr_pe_ins_uri_local,"length"=>250,"type"=>array());
//        $arFieldsConfig["uri_public"] = array("controlid"=>"txtUriPublic","label"=>tr_pe_ins_uri_public,"length"=>250,"type"=>array());
//        $arFieldsConfig["filename"] = array("controlid"=>"txtFilename","label"=>tr_pe_ins_filename,"length"=>250,"type"=>array());
//        $arFieldsConfig["create_date"] = array("controlid"=>"txtCreateDate","label"=>tr_pe_ins_create_date,"length"=>14,"type"=>array());
//        $arFieldsConfig["modify_date"] = array("controlid"=>"txtModifyDate","label"=>tr_pe_ins_modify_date,"length"=>14,"type"=>array());
//</editor-fold>
        return $arFieldsConfig;
    }//get_insert_validate

    //insert_6
    protected function build_insert_form($usePost=0)
    {
        $oForm = new HelperForm("frmInsert");
        $oForm->set_enctype("multipart/form-data");
        $oForm->add_class("form-horizontal");
        $oForm->add_style("margin-bottom:0");
        $arFields = $this->build_insert_fields($usePost);
        $oForm->add_controls($arFields);
        return $oForm;
    }//build_insert_form()

    private function create_thumb($i=1)
    {
        $iTypeEntity = 4; //Products
        $sPrefix = "th$i";
        
        $iWidth = 50; $iHeight = 50;
        if($i==2)
        {
            $iWidth = 100; $iHeight = 100;
        }
        
        $sPathFolderFile = $this->oPicture->get_parent_path();
        $sFolderName = $this->oPicture->get_folder();
        $sPathTargetFolder = $sPathFolderFile;
        $sFileName = $this->oPicture->get_filename();
        $arPathInfo = pathinfo($sFileName);
        //bug($arPathInfo,"pathinfo in create_thumb");
        $sFileName = $arPathInfo["filename"];
        $sExtension = $arPathInfo["extension"];
        $sFileName = $sFileName."_$sPrefix.".$sExtension;
        //bug($sFileName); 
        //var_dump($sPathFolderFile,$sFileName,$sPathTargetFolder,$arPathInfo,$sFileName,$sExtension);die;
        //Inserta imágen thumb en bd
        $oPicture = new ModelPicture();
        $oPicture->set_id_type_entity($iTypeEntity);
        $oPicture->set_platform($this->oSessionUser->get_platform());
        $oPicture->set_source($sPathFolderFile);
        $oPicture->set_source_filename($this->oPicture->get_filename());
        $oPicture->set_folder($sFolderName);
        $oPicture->set_filename($sFileName);
        $oPicture->set_extension($sExtension);
        $oPicture->set_information_extra($this->get_var_export($arPathInfo));
        $oPicture->set_uri_local($sPathTargetFolder);
        $oPicture->set_id_entity($this->oProduct->get_id());
        $oPicture->set_csv_tags("product");
        $oPicture->set_show(1);//public show
        $oPicture->set_order_by(1);
        //images/pictures/products/product_xxx/name
        $oPicture->set_uri_public("/images/pictures/products");                                    
        $oPicture->autoinsert();
        $idInserted = $oPicture->get_last_insert_id();
        
        //Crea imagen en directorio
        $oCompImage = new ComponentImage();
        $oCompImage->set_width($iWidth);
        $oCompImage->set_height($iHeight);
        $oCompImage->set_path_folder_source($sPathTargetFolder);
        //bug($sPathTargetFolder);
        //bug($this->oPicture->get_filename());die;
        $oCompImage->set_filename_source($this->oPicture->get_filename());
        $oCompImage->set_path_folder_target($sPathTargetFolder);
        $oCompImage->set_filename_target($sFileName);
        $oCompImage->make_thumb();
        //bug($oCompImage);die;
        
        if($i==1)
            $this->oPicture->set_id_thumb_1($idInserted);
        else 
            $this->oPicture->set_id_thumb_2($idInserted);
        
        $this->oPicture->autoupdate();
    }//create_thumb
    
    private function upload_file(AppHelperAlertdiv &$oAlert, ComponentFile $oFile,$iIndex,$sFileNamePrefix)
    {
        $arFile = $this->get_upload_data("filPicture",$iIndex);
        $sExtension = pathinfo($arFile["name"]);
        $sExtension = $sExtension["extension"];
        $this->clean_extension($sExtension);

        $idEntityProduct = 4;
        $oPicture = new ModelPicture();
        $oPicture->set_id_type_entity($idEntityProduct);
        //bug($this->oProduct->get_id(),"id product");
        $oPicture->set_id_entity($this->oProduct->get_id());
        $oPicture->load_default_by_entity();        
        //si no hay imagen por defecto se guarda esta con ese valor
        $this->oPicture->set_is_bydefault(0);
        if(!$oPicture->get_id()) 
            $this->oPicture->set_is_bydefault(1);
        
        $this->oPicture->autoinsert();
        
        if($this->oPicture->is_error())
        {
            $oAlert->set_type("e");
            $oAlert->set_title(tr_data_not_saved);
            $oAlert->set_content(tr_error_trying_to_save);
        }
        else//insert ok->se copia archivo
        {
            $idInserted = $this->oPicture->get_last_insert_id();
            $sFileName = $sFileNamePrefix.$idInserted;
            $sFileNameExt = $sFileName;
            if($sExtension) $sFileNameExt .= ".$sExtension";
            $this->oPicture->set_id($idInserted);
            
            $this->oPicture->set_filename($sFileNameExt);
            $this->oPicture->set_extension($sExtension);
            $arPathInfo = pathinfo($arFile["name"]);
            $this->oPicture->set_shortname($arPathInfo["basename"]);
            $this->oPicture->set_source_filename($arPathInfo["basename"]);
            $this->oPicture->set_information_extra($this->get_var_export($arPathInfo));
            $this->oPicture->autoupdate();
            $oFile->set_upload_index($iIndex);
            $oFile->set_filename_target($sFileNameExt);
            $oFile->upload();
            
            $this->create_thumb();
            $this->create_thumb(2);
        }
    }//upload_file
    
    private function upload_content(AppHelperAlertdiv &$oAlert,$iIndex,$sFileNamePrefix)
    {
        $sPictureHttp = $this->get_post("txtPictureHttp",$iIndex);
        //if(!strstr($sPictureHttp,"http")) $sPictureHttp = "http://".$sPictureHttp;   
        $arPathInfo = pathinfo($sPictureHttp);
        //bug($sPictureHttp,"rawurl");bug($arPathInfo,"upload_content arpathinfo");die;
 /*http://127.0.0.1/proy_tasks/the_public/images/custom/no_image_large.png
  * array(4) {
  ["dirname"]=>
  string(74) "http://localhost/proy_tasks/the_public/images/pictures/products/afolder"
  ["basename"]=>
  string(17) "unnombrede_una_imagen.png"
  ["extension"]=>
  string(3) "png"
  ["filename"]=>
  string(13) "unnombrede_una_imagen"
}*/
        $sExtension = $arPathInfo["extension"];
        $this->clean_extension($sExtension);
        $idEntityProduct = 4;
        //creo este objeto para consultar si la entidad actual de producto tiene imágen.
        $oPictureAux = new ModelPicture();
        $oPictureAux->set_id_type_entity($idEntityProduct);
        $oPictureAux->set_id_entity($this->oProduct->get_id());
        $oPictureAux->load_default_by_entity();        
        $this->oPicture->set_is_bydefault(0);
        //si no hay imagen por defecto se guarda esta con ese valor
        if(!$oPictureAux->get_id()) 
            $this->oPicture->set_is_bydefault(1);

        //CREO LA IMÁGEN EN BASE DE DATOS
        $this->oPicture->autoinsert();
        
        $sFileContent = file_get_contents($sPictureHttp);
        //error al recuperar la imágen
        if($sFileContent===FALSE)
            $this->oPicture->set_information_extra("ERROR in file_get_contents"); 
        
        //bug($sFileContent,"filecontent");
        if($this->oPicture->is_error())
        {
            //bug("is error:".$this->oPicture->get_error_message(),"upload_content");
            $oAlert->set_type("e");
            $oAlert->set_title(tr_data_not_saved);
            $oAlert->set_content(tr_error_trying_to_save);
        }
        else//insert ok
        {
            //bug("is ok","upload_content");
            $idInserted = $this->oPicture->get_last_insert_id();
            //$this->log_custom($this->oPicture);
            //bug("img_nr:$iIndex, Last inserted id: $idInserted, id oPicture:".$this->oPicture->get_id());
            //$sFileNamePrefix = "product_".$this->oProduct->get_id()."_";
            $sFileName = $sFileNamePrefix.$idInserted;
            $sFileNameExt = $sFileName;
            if($sExtension) $sFileNameExt .= ".$sExtension";
            $this->oPicture->set_id($idInserted);
            
            $this->oPicture->set_filename($sFileNameExt);
            $this->oPicture->set_extension($sExtension);
            $this->oPicture->set_shortname($sFileName);
            $this->oPicture->set_source_filename($arPathInfo["basename"]);
            $this->oPicture->set_information_extra($this->get_var_export($arPathInfo));
            $this->oPicture->set_source($sPictureHttp);
            $this->oPicture->autoupdate();           
            
            //C:/Inetpub/wwwroot/proy_tasks/the_public/images/pictures\products\product_41
            $sPathTargetFolderDs = $this->oPicture->get_uri_local().DS;
            $sPathFolderFile = $sPathTargetFolderDs.$sFileNameExt;
            //bug($sPathFolderFile);die;
            if($sFileContent)
            {    
                //bug($sFileContent,"antes de volcado");
                //si por algun motivo no se ha podido volcar la imágen en el directorio de destino
                if(!file_put_contents($sPathFolderFile,$sFileContent))
                {   
                    //bug("no created"); die;
                    $this->log_error("upload_content(): file_put_contents error for target file $sPathFolderFile");
                    $this->oPicture->set_is_error(1);
                    $this->oPicture->set_information_extra($this->oPicture->get_information_extra().", ERROR:No written"); 
                }
                //la imágen recuperada se ha guardado correctamente en el directorio. Luego, se crean los thumbs 1 y 2
                else
                {
                    $this->create_thumb();
                    $this->create_thumb(2);
                }
            }
            //No se ha podido recuperar la códificacion del contenido de la imágen
            else
            {    
                $this->log_error("upload_content(): no image content recovered from: $sPictureHttp");
                $this->oPicture->set_is_error(1);
                $this->oPicture->set_information_extra($this->oPicture->get_information_extra().", ERROR:No content");        
            }
            
            $this->oPicture->autoupdate();
        }//else insert ok        
    }//upload_content
        
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
            
            //los indices son el _i de los controles del formulario. Cuando cuentan con información
            //estos indices se llenan Fil=file y http=son cajas que esperan la url de la imágen
            $arIndexesFil = $this->get_upload_indexes("filPicture");
            $iIndexesFil = count($arIndexesFil);
            
            $arIndexesHttp = $this->get_http_indexes("txtPictureHttp");
            //bug($arIndexesFil,"arIndexesFil");bug($arIndexesHttp,"arIndexesHttp");die;
            $iIndexesHttp = count($arIndexesHttp);
            
            if($iIndexesFil>0 || $iIndexesHttp>0)
            {
                //de aqui interesa solo el id_type_entity
                $arFieldsValues = $this->get_fields_from_post();
                //bug($arFieldsValues);die;
                $oValidate = new ComponentValidate($arFieldsConfig,$arFieldsValues);
                $arErrData = $oValidate->get_error_field();
                if($arErrData)
                {
                    $oAlert->set_type("e");
                    $oAlert->set_title(tr_data_not_saved);
                    $oAlert->set_content("Field <b>".$arErrData["label"]."</b> ".$arErrData["message"]);
                }
                //No errors on validatation
                else
                {
                    $oFile = new ComponentFile("windows");
                    //subcarpeta dentro de products: products/product_xxx
                    $sFolderName = "product_".$this->oProduct->get_id();
                    $sPathTargetFolder = $this->sPathUpload.DS.$this->sFolderUpload.DS.$sFolderName;
                    $sFileNamePrefix = "product_".$this->oProduct->get_id()."_";
                    $oFile->set_input_file_name("filPicture[]");
                    
                    $oFile->set_path_folder_target($sPathTargetFolder);
                    if(!$oFile->target_folder_exists()) 
                        $oFile->create_folder();
                    //bug($oFile); die;
                    //Esto al ser un bucle debo resetear el id para que no me lo reinserte
                    //$this->oPicture->set_id();
                    $this->oPicture->set_attrib_value($arFieldsValues);
                    $this->oPicture->set_insert_user($this->oSessionUser->get_id());
                    $this->oPicture->set_folder($sFolderName);
                    $this->oPicture->set_parent_path($sPathTargetFolder);
                    //todo
                    $this->oPicture->set_uri_local($sPathTargetFolder);
                    $this->oPicture->set_id_entity($this->oProduct->get_id());
                    $this->oPicture->set_csv_tags("product");
                    $this->oPicture->set_show(1);//public show
                    $this->oPicture->set_order_by(1);
                    //images/pictures/products/product_xxx/name
                    $this->oPicture->set_uri_public("/images/pictures/products");
                    
                    //Recorro todos archivos enviados, controles txt
                    foreach($arIndexesFil as $iIndex)
                        $this->upload_file($oAlert,$oFile,$iIndex,$sFileNamePrefix,$sPathTargetFolder);
                    //for Upload file
                    
                    //Recorro todas las urls enviadas, controles file
                    foreach($arIndexesHttp as $iIndex)
                        $this->upload_content($oAlert,$iIndex,$sFileNamePrefix,$sPathTargetFolder);
                    //foreach http file
                    
                }//no error
            }
            //No se han seleccionado archivos
            else
            {
                $oAlert->set_type("e");
                $oAlert->set_title(tr_data_not_saved);
                $oAlert->set_content("<b>Pictures</b> No pictures selected");                
            }
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
        $arLinks["list"]=array("href"=>$sUrlLink,"innerhtml"=>tr_pt_entities);                
        $sUrlLink = $this->build_url("products",NULL,"update","id=".$this->get_get("id_product"));
        $arLinks["detail"]=array("href"=>$sUrlLink,"innerhtml"=>tr_pt_entity.": ".$this->oProduct->get_id()." - ".$this->oProduct->get_description());
        $sUrlLink = $this->build_url("products","pictures","get_list","id_product=".$this->get_get("id_product"));
        $arLinks["pictures"]=array("href"=>$sUrlLink,"innerhtml"=>tr_pe_entities);
        $sUrlLink = $this->build_url("products","pictures","update","id=".$this->oPicture->get_id()."&id_product=".$this->get_get("id_product"));
        $arLinks["detailpicture"]=array("href"=>$sUrlLink,"innerhtml"=>tr_pe_entity.": ".$this->oPicture->get_id());        
        $oScrumbs = new AppHelperBreadscrumbs($arLinks);
        return $oScrumbs;
    }//build_update_scrumbs()

    //update_2
    protected function build_update_tabs()
    {
        $arTabs = array();
        //$sUrlTab = $this->build_url("pictures",NULL,"update","id=".$this->get_get("id"));
        //$arTabs["detail"]=array("href"=>$sUrlTab,"innerhtml"=>tr_pe_updtabs_detail);
        //$sUrlTab = $this->build_url("pictures","foreignamodule","get_list_by_foreign","id_foreign=".$this->get_get("id_parent_foreign"));
        //$arTabs["foreigndata"]=array("href"=>$sUrlTab,"innerhtml"=>tr_pe_updtabs_foreigndata);
        $oTabs = new AppHelperHeadertabs($arTabs,"detail");
        return $oTabs;
    }//build_update_tabs()

    //update_3
    protected function build_update_opbuttons()
    {
        $arOpButtons = array();
        if($this->oPermission->is_select())
            $arOpButtons["list"]=array("href"=>$this->build_url("products","pictures","get_list","id_product=".$this->oPicture->get_id_entity()),"icon"=>"awe-search","innerhtml"=>tr_pe_updopbutton_list);
        //if($this->oPermission->is_insert())
            //$arOpButtons["insert"]=array("href"=>$this->build_url("customers","pictures","insert"),"icon"=>"awe-plus","innerhtml"=>tr_pe_updopbutton_insert);
        //if($this->oPermission->is_quarantine())
            //$arOpButtons["delete"]=array("href"=>$this->build_url("products","pictures","quarantine","id=".$this->oPicture->get_id()."&id_product=".$this->oPicture->get_id_entity()),"icon"=>"awe-remove","innerhtml"=>tr_pe_updopbutton_quarantine);
        //if($this->oPermission->is_delete())
            //$arOpButtons["delete"]=array("href"=>$this->build_url("products",NULL,"delete","id=".$this->get_get("id")),"icon"=>"awe-remove","innerhtml"=>tr_pe_updopbutton_delete);
        $oOpButtons = new AppHelperButtontabs("Product picture: ".$this->oPicture->get_filename());
        $oOpButtons->set_tabs($arOpButtons);
        return $oOpButtons;
    }//build_update_opbuttons()

    //update_4
    protected function build_update_fields($usePost=0)
    {
        //images/pictures/products/product_xxx/name
        $arFields = array(); $oAuxField = NULL; $oAuxLabel = NULL; $oGroup = NULL;
        //id
        $oAuxField = new HelperInputText("txtId","txtId");
        $oAuxField->is_primarykey();
        $oAuxField->set_value($this->oPicture->get_id());
        //if($usePost) $oAuxField->set_value(dbbo_numeric2($this->get_post("txtId")));
        $oAuxLabel = new HelperLabel("txtId",tr_pe_upd_id,"lblId");
        $oAuxLabel->add_class("labelpk");
        $oAuxField->readonly();$oAuxField->add_class("readonly");
        $oGroup = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        $arFields[] = $oGroup->set_span(6);
        //
        $oAuxField = new HelperImage();
        $oAuxField->set_id($this->oPicture->get_filename());
        $oAuxField->set_src($this->oPicture->get_uri_public()."/".$this->oPicture->get_folder()."/".$this->oPicture->get_filename());
        $oAuxField->set_alt($this->oPicture->get_img_title());
        $arFields[] = new ApphelperControlGroup($oAuxField);
//<editor-fold defaultstate="collapsed" desc="NIU">        
        //code_erp
        //$oAuxField = new HelperInputText("txtCodeErp","txtCodeErp");
        //$oAuxField->set_value($this->oPicture->get_code_erp());
        //if($usePost) $oAuxField->set_value($this->get_post("txtCodeErp"));
        //$oAuxLabel = new HelperLabel("txtCodeErp",tr_pe_upd_code_erp,"lblCodeErp");
        //$oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        //$arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //id_type
//        $oType = new ModelPicture();
//        $arOptions = $oType->get_picklist();
//        $oAuxField = new HelperSelect($arOptions,"selIdType","selIdType");
//        $oAuxField->set_value_to_select($this->get_post("selIdType"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdType",tr_pe_upd_id_type));
//        $oAuxField->set_value_to_select($this->oPicture->get_id_type());
//        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdType"));
//        $oAuxLabel = new HelperLabel("selIdType",tr_pe_upd_id_type,"lblIdType");
//        $oAuxLabel->add_class("labelpk");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //id_type_entity
//        $oTypeEntity = new ModelPictureArray();
//        $arOptions = $oTypeEntity->get_picklist_by_type("entities");
//        $oAuxField = new HelperSelect($arOptions,"selIdTypeEntity","selIdTypeEntity");
//        $oAuxField->set_value_to_select($this->get_post("selIdTypeEntity"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdTypeEntity",tr_pe_upd_id_type_entity));
//        $oAuxField->set_value_to_select($this->oPicture->get_id_type_entity());
//        //if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdTypeEntity"));
//        $oAuxLabel = new HelperLabel("selIdTypeEntity",tr_pe_upd_id_type_entity,"lblIdTypeEntity");
//        $oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //id_owner_entity
//        $oOwnerEntity = new ModelPicture();
//        $arOptions = $oOwnerEntity->get_picklist();
//        $oAuxField = new HelperSelect($arOptions,"selIdOwnerEntity","selIdOwnerEntity");
//        $oAuxField->set_value_to_select($this->get_post("selIdOwnerEntity"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdOwnerEntity",tr_pe_upd_id_owner_entity));
//        $oAuxField->set_value_to_select($this->oPicture->get_id_owner_entity());
//        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdOwnerEntity"));
//        $oAuxLabel = new HelperLabel("selIdOwnerEntity",tr_pe_upd_id_owner_entity,"lblIdOwnerEntity");
//        $oAuxLabel->add_class("labelpk");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //is_bydefault
//        $oBydefault = new ModelPicture();
//        $arOptions = $oBydefault->get_picklist();
//        $oAuxField = new HelperSelect($arOptions,"selIdBydefault","selIdBydefault");
//        $oAuxField->set_value_to_select($this->get_post("selIdBydefault"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdBydefault",tr_pe_upd_is_bydefault));
//        $oAuxField->set_value_to_select($this->oPicture->get_is_bydefault());
//        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdBydefault"));
//        $oAuxLabel = new HelperLabel("selIdBydefault",tr_pe_upd_is_bydefault,"lblIdBydefault");
//        $oAuxLabel->add_class("labelpk");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //id_entity
//        $oEntity = new ModelPicture();
//        $arOptions = $oEntity->get_picklist();
//        $oAuxField = new HelperSelect($arOptions,"selIdEntity","selIdEntity");
//        $oAuxField->set_value_to_select($this->get_post("selIdEntity"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdEntity",tr_pe_upd_id_entity));
//        $oAuxField->set_value_to_select($this->oPicture->get_id_entity());
//        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdEntity"));
//        $oAuxLabel = new HelperLabel("selIdEntity",tr_pe_upd_id_entity,"lblIdEntity");
//        $oAuxLabel->add_class("labelpk");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //id_owner
//        $oOwner = new ModelPicture();
//        $arOptions = $oOwner->get_picklist();
//        $oAuxField = new HelperSelect($arOptions,"selIdOwner","selIdOwner");
//        $oAuxField->set_value_to_select($this->get_post("selIdOwner"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdOwner",tr_pe_upd_id_owner));
//        $oAuxField->set_value_to_select($this->oPicture->get_id_owner());
//        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdOwner"));
//        $oAuxLabel = new HelperLabel("selIdOwner",tr_pe_upd_id_owner,"lblIdOwner");
//        $oAuxLabel->add_class("labelpk");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //id_thumb_1
//        $oThumb1 = new ModelPicture();
//        $arOptions = $oThumb1->get_picklist();
//        $oAuxField = new HelperSelect($arOptions,"selIdThumb1","selIdThumb1");
//        $oAuxField->set_value_to_select($this->get_post("selIdThumb1"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdThumb1",tr_pe_upd_id_thumb_1));
//        $oAuxField->set_value_to_select($this->oPicture->get_id_thumb_1());
//        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdThumb1"));
//        $oAuxLabel = new HelperLabel("selIdThumb1",tr_pe_upd_id_thumb_1,"lblIdThumb1");
//        $oAuxLabel->add_class("labelpk");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //id_thumb_2
//        $oThumb2 = new ModelPicture();
//        $arOptions = $oThumb2->get_picklist();
//        $oAuxField = new HelperSelect($arOptions,"selIdThumb2","selIdThumb2");
//        $oAuxField->set_value_to_select($this->get_post("selIdThumb2"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdThumb2",tr_pe_upd_id_thumb_2));
//        $oAuxField->set_value_to_select($this->oPicture->get_id_thumb_2());
//        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdThumb2"));
//        $oAuxLabel = new HelperLabel("selIdThumb2",tr_pe_upd_id_thumb_2,"lblIdThumb2");
//        $oAuxLabel->add_class("labelpk");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //id_thumb_3
//        $oThumb3 = new ModelPicture();
//        $arOptions = $oThumb3->get_picklist();
//        $oAuxField = new HelperSelect($arOptions,"selIdThumb3","selIdThumb3");
//        $oAuxField->set_value_to_select($this->get_post("selIdThumb3"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdThumb3",tr_pe_upd_id_thumb_3));
//        $oAuxField->set_value_to_select($this->oPicture->get_id_thumb_3());
//        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdThumb3"));
//        $oAuxLabel = new HelperLabel("selIdThumb3",tr_pe_upd_id_thumb_3,"lblIdThumb3");
//        $oAuxLabel->add_class("labelpk");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //id_thumb_4
//        $oThumb4 = new ModelPicture();
//        $arOptions = $oThumb4->get_picklist();
//        $oAuxField = new HelperSelect($arOptions,"selIdThumb4","selIdThumb4");
//        $oAuxField->set_value_to_select($this->get_post("selIdThumb4"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdThumb4",tr_pe_upd_id_thumb_4));
//        $oAuxField->set_value_to_select($this->oPicture->get_id_thumb_4());
//        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdThumb4"));
//        $oAuxLabel = new HelperLabel("selIdThumb4",tr_pe_upd_id_thumb_4,"lblIdThumb4");
//        $oAuxLabel->add_class("labelpk");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //id_thumb_5
//        $oThumb5 = new ModelPicture();
//        $arOptions = $oThumb5->get_picklist();
//        $oAuxField = new HelperSelect($arOptions,"selIdThumb5","selIdThumb5");
//        $oAuxField->set_value_to_select($this->get_post("selIdThumb5"));
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdThumb5",tr_pe_upd_id_thumb_5));
//        $oAuxField->set_value_to_select($this->oPicture->get_id_thumb_5());
//        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdThumb5"));
//        $oAuxLabel = new HelperLabel("selIdThumb5",tr_pe_upd_id_thumb_5,"lblIdThumb5");
//        $oAuxLabel->add_class("labelpk");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //width
//        $oAuxField = new HelperInputText("txtWidth","txtWidth");
//        $oAuxField->set_value($this->oPicture->get_width());
//        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtWidth")));
//        $oAuxLabel = new HelperLabel("txtWidth",tr_pe_upd_width,"lblWidth");
//        $oAuxLabel->add_class("labelpk");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //height
//        $oAuxField = new HelperInputText("txtHeight","txtHeight");
//        $oAuxField->set_value($this->oPicture->get_height());
//        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtHeight")));
//        $oAuxLabel = new HelperLabel("txtHeight",tr_pe_upd_height,"lblHeight");
//        $oAuxLabel->add_class("labelpk");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //resolution
//        $oAuxField = new HelperInputText("txtResolution","txtResolution");
//        $oAuxField->set_value($this->oPicture->get_resolution());
//        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtResolution")));
//        $oAuxLabel = new HelperLabel("txtResolution",tr_pe_upd_resolution,"lblResolution");
//        $oAuxLabel->add_class("labelpk");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //name
//        $oAuxField = new HelperInputText("txtName","txtName");
//        $oAuxField->set_value($this->oPicture->get_name());
//        if($usePost) $oAuxField->set_value($this->get_post("txtName"));
//        $oAuxLabel = new HelperLabel("txtName",tr_pe_upd_name,"lblName");
//        $oAuxLabel->add_class("labelpk");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //shortname
//        $oAuxField = new HelperInputText("txtShortname","txtShortname");
//        $oAuxField->set_value($this->oPicture->get_shortname());
//        if($usePost) $oAuxField->set_value($this->get_post("txtShortname"));
//        $oAuxLabel = new HelperLabel("txtShortname",tr_pe_upd_shortname,"lblShortname");
//        $oAuxLabel->add_class("labelpk");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //order_by
//        $oAuxField = new HelperInputText("txtOrderBy","txtOrderBy");
//        $oAuxField->set_value($this->oPicture->get_order_by());
//        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtOrderBy")));
//        $oAuxLabel = new HelperLabel("txtOrderBy",tr_pe_upd_order_by,"lblOrderBy");
//        $oAuxLabel->add_class("labelpk");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //rating
//        $oAuxField = new HelperInputText("txtRating","txtRating");
//        $oAuxField->set_value($this->oPicture->get_rating());
//        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtRating")));
//        $oAuxLabel = new HelperLabel("txtRating",tr_pe_upd_rating,"lblRating");
//        $oAuxLabel->add_class("labelpk");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //show
//        $oAuxField = new HelperInputText("txtShow","txtShow");
//        $oAuxField->set_value($this->oPicture->get_show());
//        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtShow")));
//        $oAuxLabel = new HelperLabel("txtShow",tr_pe_upd_show,"lblShow");
//        $oAuxLabel->add_class("labelpk");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //is_public
//        $oAuxField = new HelperInputText("txtIsPublic","txtIsPublic");
//        $oAuxField->set_value($this->oPicture->get_is_public());
//        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtIsPublic")));
//        $oAuxLabel = new HelperLabel("txtIsPublic",tr_pe_upd_is_public,"lblIsPublic");
//        $oAuxLabel->add_class("labelpk");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //is_file
//        $oAuxField = new HelperInputText("txtIsFile","txtIsFile");
//        $oAuxField->set_value($this->oPicture->get_is_file());
//        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtIsFile")));
//        $oAuxLabel = new HelperLabel("txtIsFile",tr_pe_upd_is_file,"lblIsFile");
//        $oAuxLabel->add_class("labelpk");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //is_error
//        $oAuxField = new HelperInputText("txtIsError","txtIsError");
//        $oAuxField->set_value($this->oPicture->get_is_error());
//        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtIsError")));
//        $oAuxLabel = new HelperLabel("txtIsError",tr_pe_upd_is_error,"lblIsError");
//        $oAuxLabel->add_class("labelpk");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //size
//        $oAuxField = new HelperInputText("txtSize","txtSize");
//        $oAuxField->set_value(dbbo_numeric2($this->oPicture->get_size()));
//        if($usePost) $oAuxField->set_value(dbbo_numeric2($this->get_post("txtSize")));
//        $oAuxLabel = new HelperLabel("txtSize",tr_pe_upd_size,"lblSize");
//        $oAuxLabel->add_class("labelpk");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //img_title
//        $oAuxField = new HelperInputText("txtImgTitle","txtImgTitle");
//        $oAuxField->set_value($this->oPicture->get_img_title());
//        if($usePost) $oAuxField->set_value($this->get_post("txtImgTitle"));
//        $oAuxLabel = new HelperLabel("txtImgTitle",tr_pe_upd_img_title,"lblImgTitle");
//        $oAuxLabel->add_class("labelpk");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //anchor_text
//        $oAuxField = new HelperInputText("txtAnchorText","txtAnchorText");
//        $oAuxField->set_value($this->oPicture->get_anchor_text());
//        if($usePost) $oAuxField->set_value($this->get_post("txtAnchorText"));
//        $oAuxLabel = new HelperLabel("txtAnchorText",tr_pe_upd_anchor_text,"lblAnchorText");
//        $oAuxLabel->add_class("labelpk");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //csv_tags
//        $oAuxField = new HelperInputText("txtCsvTags","txtCsvTags");
//        $oAuxField->set_value($this->oPicture->get_csv_tags());
//        if($usePost) $oAuxField->set_value($this->get_post("txtCsvTags"));
//        $oAuxLabel = new HelperLabel("txtCsvTags",tr_pe_upd_csv_tags,"lblCsvTags");
//        $oAuxLabel->add_class("labelpk");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //extension
//        $oAuxField = new HelperInputText("txtExtension","txtExtension");
//        $oAuxField->set_value($this->oPicture->get_extension());
//        if($usePost) $oAuxField->set_value($this->get_post("txtExtension"));
//        $oAuxLabel = new HelperLabel("txtExtension",tr_pe_upd_extension,"lblExtension");
//        $oAuxLabel->add_class("labelpk");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //source
//        $oAuxField = new HelperInputText("txtSource","txtSource");
//        $oAuxField->set_value($this->oPicture->get_source());
//        if($usePost) $oAuxField->set_value($this->get_post("txtSource"));
//        $oAuxLabel = new HelperLabel("txtSource",tr_pe_upd_source,"lblSource");
//        $oAuxLabel->add_class("labelpk");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //folder
//        $oAuxField = new HelperInputText("txtFolder","txtFolder");
//        $oAuxField->set_value($this->oPicture->get_folder());
//        if($usePost) $oAuxField->set_value($this->get_post("txtFolder"));
//        $oAuxLabel = new HelperLabel("txtFolder",tr_pe_upd_folder,"lblFolder");
//        $oAuxLabel->add_class("labelpk");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //parent_path
//        $oAuxField = new HelperInputText("txtParentPath","txtParentPath");
//        $oAuxField->set_value($this->oPicture->get_parent_path());
//        if($usePost) $oAuxField->set_value($this->get_post("txtParentPath"));
//        $oAuxLabel = new HelperLabel("txtParentPath",tr_pe_upd_parent_path,"lblParentPath");
//        $oAuxLabel->add_class("labelpk");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //information
//        $oAuxField = new HelperTextarea("txaInformation","txaInformation");
//        $oAuxField->set_value($this->oPicture->get_information());
//        if($usePost) $oAuxField->set_innerhtml($this->get_post("txaInformation"));
//        $oAuxLabel = new HelperLabel("txaInformation",tr_pe_upd_information,"lblInformation");
//        $oAuxLabel->add_class("labelpk");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //information_extra
//        $oAuxField = new HelperTextarea("txaInformationExtra","txaInformationExtra");
//        $oAuxField->set_value($this->oPicture->get_information_extra());
//        if($usePost) $oAuxField->set_innerhtml($this->get_post("txaInformationExtra"));
//        $oAuxLabel = new HelperLabel("txaInformationExtra",tr_pe_upd_information_extra,"lblInformationExtra");
//        $oAuxLabel->add_class("labelpk");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //description
//        //$oAuxField = new HelperInputText("txtDescription","txtDescription");
//        //$oAuxField->set_value($this->oPicture->get_description());
//        //if($usePost) $oAuxField->set_value($this->get_post("txtDescription"));
//        //$oAuxLabel = new HelperLabel("txtDescription",tr_pe_upd_description,"lblDescription");
//        //$oAuxLabel->add_class("labelpk");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        //$arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //uri_local
//        $oAuxField = new HelperInputText("txtUriLocal","txtUriLocal");
//        $oAuxField->set_value($this->oPicture->get_uri_local());
//        if($usePost) $oAuxField->set_value($this->get_post("txtUriLocal"));
//        $oAuxLabel = new HelperLabel("txtUriLocal",tr_pe_upd_uri_local,"lblUriLocal");
//        $oAuxLabel->add_class("labelpk");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //uri_public
//        $oAuxField = new HelperInputText("txtUriPublic","txtUriPublic");
//        $oAuxField->set_value($this->oPicture->get_uri_public());
//        if($usePost) $oAuxField->set_value($this->get_post("txtUriPublic"));
//        $oAuxLabel = new HelperLabel("txtUriPublic",tr_pe_upd_uri_public,"lblUriPublic");
//        $oAuxLabel->add_class("labelpk");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //filename
//        $oAuxField = new HelperInputText("txtFilename","txtFilename");
//        $oAuxField->set_value($this->oPicture->get_filename());
//        if($usePost) $oAuxField->set_value($this->get_post("txtFilename"));
//        $oAuxLabel = new HelperLabel("txtFilename",tr_pe_upd_filename,"lblName");
//        $oAuxLabel->add_class("labelpk");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //create_date
//        $oAuxField = new HelperInputText("txtCreateDate","txtCreateDate");
//        $oAuxField->set_value($this->oPicture->get_create_date());
//        if($usePost) $oAuxField->set_value($this->get_post("txtCreateDate"));
//        $oAuxLabel = new HelperLabel("txtCreateDate",tr_pe_upd_create_date,"lblCreateDate");
//        $oAuxLabel->add_class("labelpk");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        //modify_date
//        $oAuxField = new HelperInputText("txtModifyDate","txtModifyDate");
//        $oAuxField->set_value($this->oPicture->get_modify_date());
//        if($usePost) $oAuxField->set_value($this->get_post("txtModifyDate"));
//        $oAuxLabel = new HelperLabel("txtModifyDate",tr_pe_upd_modify_date,"lblModifyDate");
//        $oAuxLabel->add_class("labelpk");
//        //$oAuxField->readonly();$oAuxField->add_class("readonly");
//        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//</editor-fold>
        //BUTTON SAVE
//        $oAuxField = new HelperButtonBasic("butSave",tr_pe_upd_savebutton);
//        $oAuxField->add_class("btn btn-primary");
//        $oAuxField->set_js_onclick("update();");
//        if($this->oPermission->is_update())
//            $arFields[] = new ApphelperFormactions(array($oAuxField));
        //AUDIT INFO
        $sRegInfo = $this->get_audit_info($this->oPicture->get_insert_user(),$this->oPicture->get_insert_date()
        ,$this->oPicture->get_update_user(),$this->oPicture->get_update_date());
        $arFields[]= new AppHelperFormhead(null,$sRegInfo);
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
        $arFieldsConfig["id"] = array("controlid"=>"txtId","label"=>tr_pe_upd_id,"length"=>9,"type"=>array("numeric","required"));
//<editor-fold defaultstate="collapsed" desc="NIU">        
        //$arFieldsConfig["code_erp"] = array("controlid"=>"txtCodeErp","label"=>tr_pe_upd_code_erp,"length"=>25,"type"=>array());
        //$arFieldsConfig["id_type"] = array("controlid"=>"selIdType","label"=>tr_pe_upd_id_type,"length"=>4,"type"=>array());
        //$arFieldsConfig["id_type_entity"] = array("controlid"=>"selIdTypeEntity","label"=>tr_pe_upd_id_type_entity,"length"=>4,"type"=>array());
        //$arFieldsConfig["id_owner_entity"] = array("controlid"=>"selIdOwnerEntity","label"=>tr_pe_upd_id_owner_entity,"length"=>4,"type"=>array());
        //$arFieldsConfig["is_bydefault"] = array("controlid"=>"selIdBydefault","label"=>tr_pe_upd_is_bydefault,"length"=>4,"type"=>array());
        //$arFieldsConfig["id_entity"] = array("controlid"=>"selIdEntity","label"=>tr_pe_upd_id_entity,"length"=>9,"type"=>array());
        //$arFieldsConfig["id_owner"] = array("controlid"=>"selIdOwner","label"=>tr_pe_upd_id_owner,"length"=>9,"type"=>array());
        //$arFieldsConfig["id_thumb_1"] = array("controlid"=>"selIdThumb1","label"=>tr_pe_upd_id_thumb_1,"length"=>9,"type"=>array());
        //$arFieldsConfig["id_thumb_2"] = array("controlid"=>"selIdThumb2","label"=>tr_pe_upd_id_thumb_2,"length"=>9,"type"=>array());
        //$arFieldsConfig["id_thumb_3"] = array("controlid"=>"selIdThumb3","label"=>tr_pe_upd_id_thumb_3,"length"=>9,"type"=>array());
        //$arFieldsConfig["id_thumb_4"] = array("controlid"=>"selIdThumb4","label"=>tr_pe_upd_id_thumb_4,"length"=>9,"type"=>array());
        //$arFieldsConfig["id_thumb_5"] = array("controlid"=>"selIdThumb5","label"=>tr_pe_upd_id_thumb_5,"length"=>9,"type"=>array());
//        $arFieldsConfig["width"] = array("controlid"=>"txtWidth","label"=>tr_pe_upd_width,"length"=>4,"type"=>array());
//        $arFieldsConfig["height"] = array("controlid"=>"txtHeight","label"=>tr_pe_upd_height,"length"=>4,"type"=>array());
//        $arFieldsConfig["resolution"] = array("controlid"=>"txtResolution","label"=>tr_pe_upd_resolution,"length"=>4,"type"=>array());
//        $arFieldsConfig["order_by"] = array("controlid"=>"txtOrderBy","label"=>tr_pe_upd_order_by,"length"=>4,"type"=>array());
//        $arFieldsConfig["rating"] = array("controlid"=>"txtRating","label"=>tr_pe_upd_rating,"length"=>4,"type"=>array());
//        $arFieldsConfig["show"] = array("controlid"=>"txtShow","label"=>tr_pe_upd_show,"length"=>4,"type"=>array());
//        $arFieldsConfig["is_public"] = array("controlid"=>"txtIsPublic","label"=>tr_pe_upd_is_public,"length"=>4,"type"=>array());
//        $arFieldsConfig["is_file"] = array("controlid"=>"txtIsFile","label"=>tr_pe_upd_is_file,"length"=>4,"type"=>array());
//        $arFieldsConfig["is_error"] = array("controlid"=>"txtIsError","label"=>tr_pe_upd_is_error,"length"=>4,"type"=>array());
//        $arFieldsConfig["size"] = array("controlid"=>"txtSize","label"=>tr_pe_upd_size,"length"=>9,"type"=>array("numeric"));
//        $arFieldsConfig["img_title"] = array("controlid"=>"txtImgTitle","label"=>tr_pe_upd_img_title,"length"=>100,"type"=>array());
//        $arFieldsConfig["anchor_text"] = array("controlid"=>"txtAnchorText","label"=>tr_pe_upd_anchor_text,"length"=>100,"type"=>array());
//        $arFieldsConfig["csv_tags"] = array("controlid"=>"txtCsvTags","label"=>tr_pe_upd_csv_tags,"length"=>200,"type"=>array());
//        $arFieldsConfig["extension"] = array("controlid"=>"txtExtension","label"=>tr_pe_upd_extension,"length"=>5,"type"=>array());
//        $arFieldsConfig["source"] = array("controlid"=>"txtSource","label"=>tr_pe_upd_source,"length"=>250,"type"=>array());
//        $arFieldsConfig["folder"] = array("controlid"=>"txtFolder","label"=>tr_pe_upd_folder,"length"=>50,"type"=>array());
//        $arFieldsConfig["parent_path"] = array("controlid"=>"txtParentPath","label"=>tr_pe_upd_parent_path,"length"=>250,"type"=>array());
//        $arFieldsConfig["information"] = array("controlid"=>"txaInformation","label"=>tr_pe_upd_information,"length"=>250,"type"=>array());
//        $arFieldsConfig["information_extra"] = array("controlid"=>"txaInformationExtra","label"=>tr_pe_upd_information_extra,"length"=>250,"type"=>array());
//        //$arFieldsConfig["description"] = array("controlid"=>"txtDescription","label"=>tr_pe_upd_description,"length"=>200,"type"=>array());
//        $arFieldsConfig["uri_local"] = array("controlid"=>"txtUriLocal","label"=>tr_pe_upd_uri_local,"length"=>250,"type"=>array());
//        $arFieldsConfig["uri_public"] = array("controlid"=>"txtUriPublic","label"=>tr_pe_upd_uri_public,"length"=>250,"type"=>array());
//        $arFieldsConfig["name"] = array("controlid"=>"txtFilename","label"=>tr_pe_upd_filename,"length"=>250,"type"=>array());
//        $arFieldsConfig["create_date"] = array("controlid"=>"txtCreateDate","label"=>tr_pe_upd_create_date,"length"=>14,"type"=>array());
//        $arFieldsConfig["modify_date"] = array("controlid"=>"txtModifyDate","label"=>tr_pe_upd_modify_date,"length"=>14,"type"=>array());
//</editor-fold>        
        
        return $arFieldsConfig;
    }//get_update_validate

    //update_6
    protected function build_update_form($usePost=0)
    {
        $id = $this->oPicture->get_id();
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
                $this->oPicture->set_attrib_value($arFieldsValues);
                //$this->oPicture->set_description($oPicture->get_field1()." ".$oPicture->get_field2());
                $this->oPicture->set_update_user($this->oSessionUser->get_id());
                $this->oPicture->autoupdate();
                if($this->oPicture->is_error())
                {
                    $oAlert->set_type("e");
                    $oAlert->set_title(tr_data_not_saved);
                    $oAlert->set_content(tr_error_trying_to_save);
                }//no error
                else//update ok
                {
                    //$this->oPicture->load_by_id();
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
   
//<editor-fold defaultstate="collapsed" desc="QUARANTINE">
    //quarantine_1
    protected function single_quarantine()
    {
        $id = $this->get_get("id");
        if($id)
        {
            $this->oPicture->set_id($id);
            $this->oPicture->autoquarantine();
            if($this->oPicture->is_error())
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
            $this->oPicture->set_id($id);
            $this->oPicture->autoquarantine();
            if($this->oPicture->is_error())
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
}//end controller