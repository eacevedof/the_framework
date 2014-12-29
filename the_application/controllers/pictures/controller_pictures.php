<?php
/**
* @author Module Builder 1.0.16
* @link www.eduardoaf.com
* @version 1.0.4
* @name ControllerPictures
* @file controller_pictures.php   
* @date 27-10-2013 09:40 (SPAIN)
* @observations: 
* @requires:
*/
//TFW
import_component("page,validate,filter,image");
import_helper("form,form_fieldset,form_legend,input_text,label,anchor,table,table_typed,input_date,input_hidden");
import_helper("input_password,button_basic,raw,div,javascript,textarea,image,imagelist");
//APP
import_model("user,picture,picture_array");
import_appmain("controller,view,behaviour");
import_appbehaviour("picklist");
import_apphelper("listactionbar,controlgroup,formactions,buttontabs,formhead,alertdiv,breadscrumbs,headertabs");

class ControllerPictures extends TheApplicationController
{
    /**
     * @var ModelPicture 
     */
    protected $oPicture;
    protected $sPathUpload;
    protected $sFolderUpload;
    protected $sDisplayMode;
    protected $iNumUploads;

    public function __construct()
    {
        $this->sModuleName = "pictures";
        //console,pgpage,clientdevice,sesuser,aftersucces,currop  | log,session,permission,view
        parent::__construct($this->sModuleName);
        $this->oPicture = new ModelPicture();
        $this->oPicture->set_platform($this->oSessionUser->get_platform());
        $this->sPathUpload = TFW_PATH_FOLDER_PICTURES;
        $this->iNumUploads = 10;
        if($this->is_inget("id"))
        {
            $this->oPicture->set_id($this->get_get("id"));
            $this->oPicture->load_by_id();
        }
        //$this->oSessionUser->set_dataowner_table($this->oPicture->get_table_name());
        //$this->oSessionUser->set_dataowner_tablefield("id_customer");
        //$this->oSessionUser->set_dataowner_keys(array("id"=>$this->oPicture->get_id()));
    }

//<editor-fold defaultstate="collapsed" desc="LIST">
    //list_1
    protected function build_list_scrumbs()
    {
        $arLinks = array();
        $sUrlLink = $this->build_url();
        $arLinks["list"]=array("href"=>$sUrlLink,"innerhtml"=>tr_pe_entities);
        $oScrumbs = new AppHelperBreadscrumbs($arLinks);
        return $oScrumbs;
    }

    //list_2
    protected function build_list_tabs()
    {
        $arTabs = array();
        //$sUrlTab = $this->build_url($this->sModuleName,NULL,"get_list","id=".$this->get_get("id_parent_foreign"));
        //$arTabs["list"]=array("href"=>$sUrlTab,"innerhtml"=>tr_pe_listtabs_1);
        //$sUrlTab = $this->build_url($this->sModuleName,NULL,"get_list_by_foreign","id=".$this->get_get("id_parent_foreign"));
        //$arTabs["listbyforeign"]=array("href"=>$sUrlTab,"innerhtml"=>tr_pe_listtabs_2);
        $oTabs = new AppHelperHeadertabs($arTabs,"list");
        return $oTabs;
    }

    //list_3
    protected function build_listoperation_buttons()
    {
        $arOpButtons = array();
        $arOpButtons["filters"]=array("href"=>"javascript:reset_filters();","icon"=>"awe-magic","innerhtml"=>tr_pe_listopbutton_filters);
        $arOpButtons["reload"]=array("href"=>"javascript:TfwControl.form_submit();","icon"=>"awe-refresh","innerhtml"=>tr_pe_listopbutton_reload);
        if($this->oPermission->is_insert())
            $arOpButtons["insert"]=array("href"=>$this->build_url($this->sModuleName,NULL,"insert"),"icon"=>"awe-plus","innerhtml"=>tr_pe_listopbutton_insert);
        if($this->oPermission->is_quarantine())
            $arOpButtons["multiquarantine"]=array("href"=>"javascript:multi_quarantine();","icon"=>"awe-remove","innerhtml"=>tr_pe_listopbutton_multiquarantine);
        //if($this->oPermission->is_delete())
            //$arOpButtons["multidelete"]=array("href"=>"javascript:multi_delete();","icon"=>"awe-remove","innerhtml"=>tr_pe_listopbutton_multidelete);
        //PICK WINDOWS
        //$arOpButtons["multiassign"]=array("href"=>"javascript:multiassign_window('pictures',null,'multiassign','pictures','addexternaldata');","icon"=>"awe-external-link","innerhtml"=>tr_pe_listopbutton_multiassign);
        //$arOpButtons["singleassign"]=array("href"=>"javascript:single_pick('pictures','singleassign','txtI','txtI');","icon"=>"awe-external-link","innerhtml"=>tr_pe_listopbutton_singleassign);
        $oOpButtons = new AppHelperButtontabs(tr_pe_entities);
        $oOpButtons->set_tabs($arOpButtons);
        return $oOpButtons;
    }//build_listoperation_buttons()

    //list_4
    protected function load_config_list_filters()
    {
        //id
        $this->set_filter("id","txtId",array("operator"=>"like"));
        //id_type
        $this->set_filter("id_type","selIdType");        
        //information
        $this->set_filter("information","txtInformation",array("operator"=>"like"));
        //information_extra
        $this->set_filter("information_extra","txtInformationExtra",array("operator"=>"like"));
        //img_title
        $this->set_filter("img_title","txtImgTitle",array("operator"=>"like"));
        //anchor_text
        $this->set_filter("anchor_text","txtAnchorText",array("operator"=>"like"));
        //csv_tags
        $this->set_filter("csv_tags","txtCsvTags",array("operator"=>"like"));
        //name
        $this->set_filter("name","txtFilename",array("operator"=>"like"));
        
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
        //id_type_entity
        $this->set_filter_value("id_type_entity",$this->get_post("selIdTypeEntity"));
        //id_owner_entity
        $this->set_filter_value("id_owner_entity",$this->get_post("selIdOwnerEntity"));
        //is_bydefault
        $this->set_filter_value("is_bydefault",$this->get_post("selIdBydefault"));
        //id_entity
        $this->set_filter_value("id_entity",$this->get_post("selIdEntity"));
        //id_owner
        $this->set_filter_value("id_owner",$this->get_post("selIdOwner"));
        //id_thumb_1
        $this->set_filter_value("id_thumb_1",$this->get_post("selIdThumb1"));
        //id_thumb_2
        $this->set_filter_value("id_thumb_2",$this->get_post("selIdThumb2"));
        //id_thumb_3
        $this->set_filter_value("id_thumb_3",$this->get_post("selIdThumb3"));
        //id_thumb_4
        $this->set_filter_value("id_thumb_4",$this->get_post("selIdThumb4"));
        //id_thumb_5
        $this->set_filter_value("id_thumb_5",$this->get_post("selIdThumb5"));
        //width
        $this->set_filter_value("width",$this->get_post("txtWidth"));
        //height
        $this->set_filter_value("height",$this->get_post("txtHeight"));
        //resolution
        $this->set_filter_value("resolution",$this->get_post("txtResolution"));
        //order_by
        $this->set_filter_value("order_by",$this->get_post("txtOrderBy"));
        //rating
        $this->set_filter_value("rating",$this->get_post("txtRating"));
        //show
        $this->set_filter_value("show",$this->get_post("txtShow"));
        //is_public
        $this->set_filter_value("is_public",$this->get_post("txtIsPublic"));
        //is_file
        $this->set_filter_value("is_file",$this->get_post("txtIsFile"));
        //is_error
        $this->set_filter_value("is_error",$this->get_post("txtIsError"));
        //size
        $this->set_filter_value("size",$this->get_post("txtSize"));
        //img_title
        $this->set_filter_value("img_title",$this->get_post("txtImgTitle"));
        //anchor_text
        $this->set_filter_value("anchor_text",$this->get_post("txtAnchorText"));
        //csv_tags
        $this->set_filter_value("csv_tags",$this->get_post("txtCsvTags"));
        //extension
        $this->set_filter_value("extension",$this->get_post("txtExtension"));
        //source
        $this->set_filter_value("source",$this->get_post("txtSource"));
        //folder
        $this->set_filter_value("folder",$this->get_post("txtFolder"));
        //parent_path
        $this->set_filter_value("parent_path",$this->get_post("txtParentPath"));
        //information
        $this->set_filter_value("information",$this->get_post("txtInformation"));
        //information_extra
        $this->set_filter_value("information_extra",$this->get_post("txtInformationExtra"));
        //description
        //$this->set_filter_value("description",$this->get_post("txtDescription"));
        //uri_local
        $this->set_filter_value("uri_local",$this->get_post("txtUriLocal"));
        //uri_public
        $this->set_filter_value("uri_public",$this->get_post("txtUriPublic"));
        //name
        $this->set_filter_value("name",$this->get_post("txtFilename"));
        //create_date
        $this->set_filter_value("create_date",$this->get_post("txtCreateDate"));
        //modify_date
        $this->set_filter_value("modify_date",$this->get_post("txtModifyDate"));
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
        //code_erp
        //$oAuxField = new HelperInputText("txtCodeErp","txtCodeErp");
        //$oAuxField->set_value($this->get_post("txtCodeErp"));
        //$oAuxField->on_entersubmit();
        //$oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtCodeErp",tr_pe_fil_code_erp));
        //$arFields[] = $oAuxWrapper;
        //id_type
        $oType = new ModelPicture();
        $arOptions = $oType->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdType","selIdType");
        $oAuxField->set_value_to_select($this->get_post("selIdType"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdType",tr_pe_fil_id_type));
        $arFields[] = $oAuxWrapper;
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
        $oAuxField = new HelperInputText("txtShow","txtShow");
        $oAuxField->set_value($this->get_post("txtShow"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtShow",tr_pe_fil_show));
        $arFields[] = $oAuxWrapper;
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
        $oAuxField = new HelperInputText("txtImgTitle","txtImgTitle");
        $oAuxField->set_value($this->get_post("txtImgTitle"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtImgTitle",tr_pe_fil_img_title));
        $arFields[] = $oAuxWrapper;
//        //anchor_text
//        $oAuxField = new HelperInputText("txtAnchorText","txtAnchorText");
//        $oAuxField->set_value($this->get_post("txtAnchorText"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtAnchorText",tr_pe_fil_anchor_text));
//        $arFields[] = $oAuxWrapper;
        //csv_tags
        $oAuxField = new HelperInputText("txtCsvTags","txtCsvTags");
        $oAuxField->set_value($this->get_post("txtCsvTags"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtCsvTags",tr_pe_fil_csv_tags));
        $arFields[] = $oAuxWrapper;
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
//        $oAuxField = new HelperInputText("txtInformation","txtInformation");
//        $oAuxField->set_value($this->get_post("txtInformation"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtInformation",tr_pe_fil_information));
//        $arFields[] = $oAuxWrapper;
//        //information_extra
//        $oAuxField = new HelperInputText("txtInformationExtra","txtInformationExtra");
//        $oAuxField->set_value($this->get_post("txtInformationExtra"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtInformationExtra",tr_pe_fil_information_extra));
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
        //name
        $oAuxField = new HelperInputText("txtFilename","txtFilename");
        $oAuxField->set_value($this->get_post("txtFilename"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtFilename",tr_pe_fil_filename));
        $arFields[] = $oAuxWrapper;
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
        return $arFields;
    }//get_list_filters()

    //list_7
    protected function get_list_columns()
    {
        $arColumns["id"] = tr_pe_col_id;
        //$arColumns["code_erp"] = tr_pe_col_code_erp;
        //$arColumns["id_type"] = tr_pe_col_id_type;
//        $arColumns["type"] = tr_pe_col_id_type;
        //$arColumns["id_type_entity"] = tr_pe_col_id_type_entity;
//        $arColumns["entity"] = tr_pe_col_id_type_entity;
        //$arColumns["id_owner_entity"] = tr_pe_col_id_owner_entity;
//        $arColumns["ownerentity"] = tr_pe_col_id_owner_entity;
//        //$arColumns["is_bydefault"] = tr_pe_col_is_bydefault;
//        $arColumns["bydefault"] = tr_pe_col_is_bydefault;
        $arColumns["id_entity"] = tr_pe_col_id_entity;
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
//        $arColumns["show"] = tr_pe_col_show;
//        $arColumns["is_public"] = tr_pe_col_is_public;
//        $arColumns["is_file"] = tr_pe_col_is_file;
//        $arColumns["is_error"] = tr_pe_col_is_error;
//        $arColumns["size"] = tr_pe_col_size;
//        $arColumns["img_title"] = tr_pe_col_img_title;
//        $arColumns["anchor_text"] = tr_pe_col_anchor_text;
//        $arColumns["csv_tags"] = tr_pe_col_csv_tags;
//        $arColumns["extension"] = tr_pe_col_extension;
//        $arColumns["source"] = tr_pe_col_source;
//        $arColumns["folder"] = tr_pe_col_folder;
//        $arColumns["parent_path"] = tr_pe_col_parent_path;
//        $arColumns["information"] = tr_pe_col_information;
        $arColumns["information_extra"] = tr_pe_col_information_extra;
        //$arColumns["description"] = tr_pe_col_description;
//        $arColumns["uri_local"] = tr_pe_col_uri_local;
//        $arColumns["uri_public"] = tr_pe_col_uri_public;
        $arColumns["filename"] = tr_pe_col_filename;
//        $arColumns["create_date"] = tr_pe_col_create_date;
//        $arColumns["modify_date"] = tr_pe_col_modify_date;
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
        $this->oPicture->set_orderby($this->get_orderby());
        $this->oPicture->set_ordertype($this->get_ordertype());
        $this->oPicture->set_filters($this->get_filter_searchconfig());
        //hierarchy recover
        //$this->oPicture->set_select_user($this->oSessionUser->get_id());
        $arList = $this->oPicture->get_select_all_ids();
        $iRequestPage = $this->get_post("selPage");
        $oPage = new ComponentPage($arList,$iRequestPage);
        $arList = $oPage->get_items_to_show();
        $arList = $this->oPicture->get_select_all_by_ids($arList);
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
        $arExtra[] = array("position"=>6,"label"=>"img");
        $oTableList->add_extra_colums($arExtra);
        $oTableList->set_column_raw(array("virtual_0"=>$oAnchor));
        
        //$oTableList->set_column_anchor(array("virtual_0"=>array
        //("href"=>"url_lines","innerhtml"=>tr_pe_order_lines,"class"=>"btn btn-info","icon"=>"awe-info-sign")));
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
        $arLinks["list"]=array("href"=>$sUrlLink,"innerhtml"=>tr_pe_entities);
        $sUrlLink = $this->build_url($this->sModuleName,NULL,"insert");
        $arLinks["insert"]=array("href"=>$sUrlLink,"innerhtml"=>tr_pe_entity_insert);
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
        $arOpButtons["list"] = array("href"=>$this->build_url(),"icon"=>"awe-search","innerhtml"=>tr_pe_insopbutton_list);
        //$arOpButtons["extra"] = array("href"=>$this->build_url(),"icon"=>"awe-xxxx","innerhtml"=>tr_pe_insopbutton_extra1);
        $oOpButtons = new AppHelperButtontabs(tr_pe_entities);
        $oOpButtons->set_tabs($arOpButtons);
        return $oOpButtons;
    }//build_insert_opbuttons()

    //insert_4
    protected function build_insert_fields($usePost=0)
    {
        $arFields = array(); $oAuxField = NULL; $oAuxLabel = NULL;
        $arFields[]= new AppHelperFormhead(tr_pe_entity_new);
        //id
        //$oAuxField = new HelperInputText("txtId","txtId");
        //$oAuxField->is_primarykey();
        //if($usePost) $oAuxField->set_value($this->get_post("txtId"));
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        //$arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //code_erp
        //$oAuxField = new HelperInputText("txtCodeErp","txtCodeErp");
        //if($usePost) $oAuxField->set_value($this->get_post("txtCodeErp"));
        //$oAuxLabel = new HelperLabel("txtCodeErp",tr_pe_ins_code_erp,"lblCodeErp");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        //$arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //id_type
        $oType = new ModelPicture();
        $arOptions = $oType->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdType","selIdType");
        $oAuxField->set_value_to_select($this->get_post("selIdType"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdType",tr_pe_ins_id_type));
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdType"));
        $oAuxLabel = new HelperLabel("selIdType",tr_pe_ins_id_type,"lblIdType");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //id_type_entity
        $oTypeEntity = new ModelPictureArray();
        $arOptions = $oTypeEntity->get_picklist_by_type("entities");
        $oAuxField = new HelperSelect($arOptions,"selIdTypeEntity","selIdTypeEntity");
        $oAuxField->set_value_to_select($this->get_post("selIdTypeEntity"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdTypeEntity",tr_pe_ins_id_type_entity));
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdTypeEntity"));
        $oAuxLabel = new HelperLabel("selIdTypeEntity",tr_pe_ins_id_type_entity,"lblIdTypeEntity");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //id_owner_entity
        $oOwnerEntity = new ModelPicture();
        $arOptions = $oOwnerEntity->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdOwnerEntity","selIdOwnerEntity");
        $oAuxField->set_value_to_select($this->get_post("selIdOwnerEntity"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdOwnerEntity",tr_pe_ins_id_owner_entity));
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdOwnerEntity"));
        $oAuxLabel = new HelperLabel("selIdOwnerEntity",tr_pe_ins_id_owner_entity,"lblIdOwnerEntity");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //is_bydefault
        $oBydefault = new ModelPicture();
        $arOptions = $oBydefault->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdBydefault","selIdBydefault");
        $oAuxField->set_value_to_select($this->get_post("selIdBydefault"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdBydefault",tr_pe_ins_is_bydefault));
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdBydefault"));
        $oAuxLabel = new HelperLabel("selIdBydefault",tr_pe_ins_is_bydefault,"lblIdBydefault");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //id_entity
        $oEntity = new ModelPicture();
        $arOptions = $oEntity->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdEntity","selIdEntity");
        $oAuxField->set_value_to_select($this->get_post("selIdEntity"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdEntity",tr_pe_ins_id_entity));
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdEntity"));
        $oAuxLabel = new HelperLabel("selIdEntity",tr_pe_ins_id_entity,"lblIdEntity");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //id_owner
        $oOwner = new ModelPicture();
        $arOptions = $oOwner->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdOwner","selIdOwner");
        $oAuxField->set_value_to_select($this->get_post("selIdOwner"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdOwner",tr_pe_ins_id_owner));
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdOwner"));
        $oAuxLabel = new HelperLabel("selIdOwner",tr_pe_ins_id_owner,"lblIdOwner");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //id_thumb_1
        $oThumb1 = new ModelPicture();
        $arOptions = $oThumb1->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdThumb1","selIdThumb1");
        $oAuxField->set_value_to_select($this->get_post("selIdThumb1"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdThumb1",tr_pe_ins_id_thumb_1));
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdThumb1"));
        $oAuxLabel = new HelperLabel("selIdThumb1",tr_pe_ins_id_thumb_1,"lblIdThumb1");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //id_thumb_2
        $oThumb2 = new ModelPicture();
        $arOptions = $oThumb2->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdThumb2","selIdThumb2");
        $oAuxField->set_value_to_select($this->get_post("selIdThumb2"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdThumb2",tr_pe_ins_id_thumb_2));
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdThumb2"));
        $oAuxLabel = new HelperLabel("selIdThumb2",tr_pe_ins_id_thumb_2,"lblIdThumb2");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //id_thumb_3
        $oThumb3 = new ModelPicture();
        $arOptions = $oThumb3->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdThumb3","selIdThumb3");
        $oAuxField->set_value_to_select($this->get_post("selIdThumb3"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdThumb3",tr_pe_ins_id_thumb_3));
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdThumb3"));
        $oAuxLabel = new HelperLabel("selIdThumb3",tr_pe_ins_id_thumb_3,"lblIdThumb3");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //id_thumb_4
        $oThumb4 = new ModelPicture();
        $arOptions = $oThumb4->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdThumb4","selIdThumb4");
        $oAuxField->set_value_to_select($this->get_post("selIdThumb4"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdThumb4",tr_pe_ins_id_thumb_4));
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdThumb4"));
        $oAuxLabel = new HelperLabel("selIdThumb4",tr_pe_ins_id_thumb_4,"lblIdThumb4");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //id_thumb_5
        $oThumb5 = new ModelPicture();
        $arOptions = $oThumb5->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdThumb5","selIdThumb5");
        $oAuxField->set_value_to_select($this->get_post("selIdThumb5"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdThumb5",tr_pe_ins_id_thumb_5));
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdThumb5"));
        $oAuxLabel = new HelperLabel("selIdThumb5",tr_pe_ins_id_thumb_5,"lblIdThumb5");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //width
        $oAuxField = new HelperInputText("txtWidth","txtWidth");
        $oAuxField->set_value(0);
        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtWidth")));
        $oAuxLabel = new HelperLabel("txtWidth",tr_pe_ins_width,"lblWidth");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //height
        $oAuxField = new HelperInputText("txtHeight","txtHeight");
        $oAuxField->set_value(0);
        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtHeight")));
        $oAuxLabel = new HelperLabel("txtHeight",tr_pe_ins_height,"lblHeight");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //resolution
        $oAuxField = new HelperInputText("txtResolution","txtResolution");
        $oAuxField->set_value(0);
        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtResolution")));
        $oAuxLabel = new HelperLabel("txtResolution",tr_pe_ins_resolution,"lblResolution");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //order_by
        $oAuxField = new HelperInputText("txtOrderBy","txtOrderBy");
        $oAuxField->set_value(0);
        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtOrderBy")));
        $oAuxLabel = new HelperLabel("txtOrderBy",tr_pe_ins_order_by,"lblOrderBy");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //rating
        $oAuxField = new HelperInputText("txtRating","txtRating");
        $oAuxField->set_value(0);
        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtRating")));
        $oAuxLabel = new HelperLabel("txtRating",tr_pe_ins_rating,"lblRating");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //show
        $oAuxField = new HelperInputText("txtShow","txtShow");
        $oAuxField->set_value(0);
        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtShow")));
        $oAuxLabel = new HelperLabel("txtShow",tr_pe_ins_show,"lblShow");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //is_public
        $oAuxField = new HelperInputText("txtIsPublic","txtIsPublic");
        $oAuxField->set_value(0);
        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtIsPublic")));
        $oAuxLabel = new HelperLabel("txtIsPublic",tr_pe_ins_is_public,"lblIsPublic");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //is_file
        $oAuxField = new HelperInputText("txtIsFile","txtIsFile");
        $oAuxField->set_value(0);
        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtIsFile")));
        $oAuxLabel = new HelperLabel("txtIsFile",tr_pe_ins_is_file,"lblIsFile");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //is_error
        $oAuxField = new HelperInputText("txtIsError","txtIsError");
        $oAuxField->set_value(0);
        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtIsError")));
        $oAuxLabel = new HelperLabel("txtIsError",tr_pe_ins_is_error,"lblIsError");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //size
        $oAuxField = new HelperInputText("txtSize","txtSize");
        $oAuxField->set_value("0.00");
        if($usePost) $oAuxField->set_value(dbbo_numeric2($this->get_post("txtSize")));
        $oAuxLabel = new HelperLabel("txtSize",tr_pe_ins_size,"lblSize");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //img_title
        $oAuxField = new HelperInputText("txtImgTitle","txtImgTitle");
        if($usePost) $oAuxField->set_value($this->get_post("txtImgTitle"));
        $oAuxLabel = new HelperLabel("txtImgTitle",tr_pe_ins_img_title,"lblImgTitle");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //anchor_text
        $oAuxField = new HelperInputText("txtAnchorText","txtAnchorText");
        if($usePost) $oAuxField->set_value($this->get_post("txtAnchorText"));
        $oAuxLabel = new HelperLabel("txtAnchorText",tr_pe_ins_anchor_text,"lblAnchorText");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //csv_tags
        $oAuxField = new HelperInputText("txtCsvTags","txtCsvTags");
        if($usePost) $oAuxField->set_value($this->get_post("txtCsvTags"));
        $oAuxLabel = new HelperLabel("txtCsvTags",tr_pe_ins_csv_tags,"lblCsvTags");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //extension
        $oAuxField = new HelperInputText("txtExtension","txtExtension");
        if($usePost) $oAuxField->set_value($this->get_post("txtExtension"));
        $oAuxLabel = new HelperLabel("txtExtension",tr_pe_ins_extension,"lblExtension");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //source
        $oAuxField = new HelperInputText("txtSource","txtSource");
        if($usePost) $oAuxField->set_value($this->get_post("txtSource"));
        $oAuxLabel = new HelperLabel("txtSource",tr_pe_ins_source,"lblSource");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //folder
        $oAuxField = new HelperInputText("txtFolder","txtFolder");
        if($usePost) $oAuxField->set_value($this->get_post("txtFolder"));
        $oAuxLabel = new HelperLabel("txtFolder",tr_pe_ins_folder,"lblFolder");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //parent_path
        $oAuxField = new HelperInputText("txtParentPath","txtParentPath");
        if($usePost) $oAuxField->set_value($this->get_post("txtParentPath"));
        $oAuxLabel = new HelperLabel("txtParentPath",tr_pe_ins_parent_path,"lblParentPath");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //information
        $oAuxField = new HelperInputText("txtInformation","txtInformation");
        if($usePost) $oAuxField->set_value($this->get_post("txtInformation"));
        $oAuxLabel = new HelperLabel("txtInformation",tr_pe_ins_information,"lblInformation");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //information_extra
        $oAuxField = new HelperInputText("txtInformationExtra","txtInformationExtra");
        if($usePost) $oAuxField->set_value($this->get_post("txtInformationExtra"));
        $oAuxLabel = new HelperLabel("txtInformationExtra",tr_pe_ins_information_extra,"lblInformationExtra");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //description
        //$oAuxField = new HelperInputText("txtDescription","txtDescription");
        //if($usePost) $oAuxField->set_value($this->get_post("txtDescription"));
        //$oAuxLabel = new HelperLabel("txtDescription",tr_pe_ins_description,"lblDescription");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        //$arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //uri_local
        $oAuxField = new HelperInputText("txtUriLocal","txtUriLocal");
        if($usePost) $oAuxField->set_value($this->get_post("txtUriLocal"));
        $oAuxLabel = new HelperLabel("txtUriLocal",tr_pe_ins_uri_local,"lblUriLocal");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //uri_public
        $oAuxField = new HelperInputText("txtUriPublic","txtUriPublic");
        if($usePost) $oAuxField->set_value($this->get_post("txtUriPublic"));
        $oAuxLabel = new HelperLabel("txtUriPublic",tr_pe_ins_uri_public,"lblUriPublic");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //name
        $oAuxField = new HelperInputText("txtFilename","txtFilename");
        if($usePost) $oAuxField->set_value($this->get_post("txtFilename"));
        $oAuxLabel = new HelperLabel("txtFilename",tr_pe_ins_filename,"lblName");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //create_date
        $oAuxField = new HelperInputText("txtCreateDate","txtCreateDate");
        if($usePost) $oAuxField->set_value($this->get_post("txtCreateDate"));
        $oAuxLabel = new HelperLabel("txtCreateDate",tr_pe_ins_create_date,"lblCreateDate");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //modify_date
        $oAuxField = new HelperInputText("txtModifyDate","txtModifyDate");
        if($usePost) $oAuxField->set_value($this->get_post("txtModifyDate"));
        $oAuxLabel = new HelperLabel("txtModifyDate",tr_pe_ins_modify_date,"lblModifyDate");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
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
        $arFieldsConfig["width"] = array("controlid"=>"txtWidth","label"=>tr_pe_ins_width,"length"=>4,"type"=>array());
        $arFieldsConfig["height"] = array("controlid"=>"txtHeight","label"=>tr_pe_ins_height,"length"=>4,"type"=>array());
        $arFieldsConfig["resolution"] = array("controlid"=>"txtResolution","label"=>tr_pe_ins_resolution,"length"=>4,"type"=>array());
        $arFieldsConfig["order_by"] = array("controlid"=>"txtOrderBy","label"=>tr_pe_ins_order_by,"length"=>4,"type"=>array());
        $arFieldsConfig["rating"] = array("controlid"=>"txtRating","label"=>tr_pe_ins_rating,"length"=>4,"type"=>array());
        $arFieldsConfig["show"] = array("controlid"=>"txtShow","label"=>tr_pe_ins_show,"length"=>4,"type"=>array());
        $arFieldsConfig["is_public"] = array("controlid"=>"txtIsPublic","label"=>tr_pe_ins_is_public,"length"=>4,"type"=>array());
        $arFieldsConfig["is_file"] = array("controlid"=>"txtIsFile","label"=>tr_pe_ins_is_file,"length"=>4,"type"=>array());
        $arFieldsConfig["is_error"] = array("controlid"=>"txtIsError","label"=>tr_pe_ins_is_error,"length"=>4,"type"=>array());
        $arFieldsConfig["size"] = array("controlid"=>"txtSize","label"=>tr_pe_ins_size,"length"=>9,"type"=>array("numeric"));
        $arFieldsConfig["img_title"] = array("controlid"=>"txtImgTitle","label"=>tr_pe_ins_img_title,"length"=>100,"type"=>array());
        $arFieldsConfig["anchor_text"] = array("controlid"=>"txtAnchorText","label"=>tr_pe_ins_anchor_text,"length"=>100,"type"=>array());
        $arFieldsConfig["csv_tags"] = array("controlid"=>"txtCsvTags","label"=>tr_pe_ins_csv_tags,"length"=>200,"type"=>array());
        $arFieldsConfig["extension"] = array("controlid"=>"txtExtension","label"=>tr_pe_ins_extension,"length"=>5,"type"=>array());
        $arFieldsConfig["source"] = array("controlid"=>"txtSource","label"=>tr_pe_ins_source,"length"=>250,"type"=>array());
        $arFieldsConfig["folder"] = array("controlid"=>"txtFolder","label"=>tr_pe_ins_folder,"length"=>50,"type"=>array());
        $arFieldsConfig["parent_path"] = array("controlid"=>"txtParentPath","label"=>tr_pe_ins_parent_path,"length"=>250,"type"=>array());
        $arFieldsConfig["information"] = array("controlid"=>"txtInformation","label"=>tr_pe_ins_information,"length"=>250,"type"=>array());
        $arFieldsConfig["information_extra"] = array("controlid"=>"txtInformationExtra","label"=>tr_pe_ins_information_extra,"length"=>250,"type"=>array());
        //$arFieldsConfig["description"] = array("controlid"=>"txtDescription","label"=>tr_pe_ins_description,"length"=>200,"type"=>array());
        $arFieldsConfig["uri_local"] = array("controlid"=>"txtUriLocal","label"=>tr_pe_ins_uri_local,"length"=>250,"type"=>array());
        $arFieldsConfig["uri_public"] = array("controlid"=>"txtUriPublic","label"=>tr_pe_ins_uri_public,"length"=>250,"type"=>array());
        $arFieldsConfig["name"] = array("controlid"=>"txtFilename","label"=>tr_pe_ins_filename,"length"=>250,"type"=>array());
        $arFieldsConfig["create_date"] = array("controlid"=>"txtCreateDate","label"=>tr_pe_ins_create_date,"length"=>14,"type"=>array());
        $arFieldsConfig["modify_date"] = array("controlid"=>"txtModifyDate","label"=>tr_pe_ins_modify_date,"length"=>14,"type"=>array());
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
                //$this->oPicture->log_save_insert();
                $this->oPicture->set_attrib_value($arFieldsValues);
                $this->oPicture->set_insert_user($this->oSessionUser->get_id());
                //$this->oPicture->set_platform($this->oSessionUser->get_platform());
                $this->oPicture->autoinsert();
                if($this->oPicture->is_error())
                {
                    $oAlert->set_type("e");
                    $oAlert->set_title(tr_data_not_saved);
                    $oAlert->set_content(tr_error_trying_to_save);
                }
                else//insert ok
                {
                    $this->set_get("id",$this->oPicture->get_last_insert_id());
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
        $arLinks["list"]=array("href"=>$sUrlLink,"innerhtml"=>tr_pe_entities);
        $sUrlLink = $this->build_url($this->sModuleName,NULL,"update","id=".$this->get_get("id"));
        $arLinks["detail"]=array("href"=>$sUrlLink,"innerhtml"=>tr_pe_entity.": ".$this->oPicture->get_id()." - ".$this->oPicture->get_description());
        $oScrumbs = new AppHelperBreadscrumbs($arLinks);
        return $oScrumbs;
    }//build_update_scrumbs()

    //update_2
    protected function build_update_tabs()
    {
        $arTabs = array();
        $sUrlTab = $this->build_url($this->sModuleName,NULL,"update","id=".$this->get_get("id"));
        $arTabs["detail"]=array("href"=>$sUrlTab,"innerhtml"=>tr_pe_updtabs_detail);
        //$sUrlTab = $this->build_url($this->sModuleName,"foreignamodule","get_list_by_foreign","id_foreign=".$this->get_get("id_parent_foreign"));
        //$arTabs["foreigndata"]=array("href"=>$sUrlTab,"innerhtml"=>tr_pe_updtabs_foreigndata);
        $oTabs = new AppHelperHeadertabs($arTabs,"detail");
        return $oTabs;
    }//build_update_tabs()

    //update_3
    protected function build_update_opbuttons()
    {
        $arOpButtons = array();
        if($this->oPermission->is_select())
            $arOpButtons["list"]=array("href"=>$this->build_url(),"icon"=>"awe-search","innerhtml"=>tr_pe_updopbutton_list);
        //if($this->oPermission->is_insert())
            //$arOpButtons["insert"]=array("href"=>$this->build_url($this->sModuleName,NULL,"insert"),"icon"=>"awe-plus","innerhtml"=>tr_pe_updopbutton_insert);
        if($this->oPermission->is_quarantine())
            $arOpButtons["delete"]=array("href"=>$this->build_url($this->sModuleName,NULL,"quarantine","id=".$this->get_get("id")),"icon"=>"awe-remove","innerhtml"=>tr_pe_updopbutton_quarantine);
        //if($this->oPermission->is_delete())
            //$arOpButtons["delete"]=array("href"=>$this->build_url($this->sModuleName,NULL,"delete","id=".$this->get_get("id")),"icon"=>"awe-remove","innerhtml"=>tr_pe_updopbutton_delete);
        $oOpButtons = new AppHelperButtontabs(tr_pe_entities);
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
        $oAuxField->set_value(dbbo_numeric2($this->oPicture->get_id()));
        if($usePost) $oAuxField->set_value(dbbo_numeric2($this->get_post("txtId")));
        $oAuxLabel = new HelperLabel("txtId",tr_pe_upd_id,"lblId");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //code_erp
        //$oAuxField = new HelperInputText("txtCodeErp","txtCodeErp");
        //$oAuxField->set_value($this->oPicture->get_code_erp());
        //if($usePost) $oAuxField->set_value($this->get_post("txtCodeErp"));
        //$oAuxLabel = new HelperLabel("txtCodeErp",tr_pe_upd_code_erp,"lblCodeErp");
        //$oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        //$arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //id_type
        $oType = new ModelPicture();
        $arOptions = $oType->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdType","selIdType");
        $oAuxField->set_value_to_select($this->get_post("selIdType"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdType",tr_pe_upd_id_type));
        $oAuxField->set_value_to_select($this->oPicture->get_id_type());
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdType"));
        $oAuxLabel = new HelperLabel("selIdType",tr_pe_upd_id_type,"lblIdType");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //id_type_entity
        $oTypeEntity = new ModelPictureArray();
        $arOptions = $oTypeEntity->get_picklist_by_type("entities");
        $oAuxField = new HelperSelect($arOptions,"selIdTypeEntity","selIdTypeEntity");
        $oAuxField->set_value_to_select($this->get_post("selIdTypeEntity"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdTypeEntity",tr_pe_upd_id_type_entity));
        $oAuxField->set_value_to_select($this->oPicture->get_id_type_entity());
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdTypeEntity"));
        $oAuxLabel = new HelperLabel("selIdTypeEntity",tr_pe_upd_id_type_entity,"lblIdTypeEntity");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //id_owner_entity
        $oOwnerEntity = new ModelPicture();
        $arOptions = $oOwnerEntity->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdOwnerEntity","selIdOwnerEntity");
        $oAuxField->set_value_to_select($this->get_post("selIdOwnerEntity"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdOwnerEntity",tr_pe_upd_id_owner_entity));
        $oAuxField->set_value_to_select($this->oPicture->get_id_owner_entity());
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdOwnerEntity"));
        $oAuxLabel = new HelperLabel("selIdOwnerEntity",tr_pe_upd_id_owner_entity,"lblIdOwnerEntity");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //is_bydefault
        $oBydefault = new ModelPicture();
        $arOptions = $oBydefault->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdBydefault","selIdBydefault");
        $oAuxField->set_value_to_select($this->get_post("selIdBydefault"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdBydefault",tr_pe_upd_is_bydefault));
        $oAuxField->set_value_to_select($this->oPicture->get_is_bydefault());
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdBydefault"));
        $oAuxLabel = new HelperLabel("selIdBydefault",tr_pe_upd_is_bydefault,"lblIdBydefault");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //id_entity
        $oEntity = new ModelPicture();
        $arOptions = $oEntity->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdEntity","selIdEntity");
        $oAuxField->set_value_to_select($this->get_post("selIdEntity"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdEntity",tr_pe_upd_id_entity));
        $oAuxField->set_value_to_select($this->oPicture->get_id_entity());
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdEntity"));
        $oAuxLabel = new HelperLabel("selIdEntity",tr_pe_upd_id_entity,"lblIdEntity");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //id_owner
        $oOwner = new ModelPicture();
        $arOptions = $oOwner->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdOwner","selIdOwner");
        $oAuxField->set_value_to_select($this->get_post("selIdOwner"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdOwner",tr_pe_upd_id_owner));
        $oAuxField->set_value_to_select($this->oPicture->get_id_owner());
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdOwner"));
        $oAuxLabel = new HelperLabel("selIdOwner",tr_pe_upd_id_owner,"lblIdOwner");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //id_thumb_1
        $oThumb1 = new ModelPicture();
        $arOptions = $oThumb1->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdThumb1","selIdThumb1");
        $oAuxField->set_value_to_select($this->get_post("selIdThumb1"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdThumb1",tr_pe_upd_id_thumb_1));
        $oAuxField->set_value_to_select($this->oPicture->get_id_thumb_1());
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdThumb1"));
        $oAuxLabel = new HelperLabel("selIdThumb1",tr_pe_upd_id_thumb_1,"lblIdThumb1");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //id_thumb_2
        $oThumb2 = new ModelPicture();
        $arOptions = $oThumb2->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdThumb2","selIdThumb2");
        $oAuxField->set_value_to_select($this->get_post("selIdThumb2"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdThumb2",tr_pe_upd_id_thumb_2));
        $oAuxField->set_value_to_select($this->oPicture->get_id_thumb_2());
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdThumb2"));
        $oAuxLabel = new HelperLabel("selIdThumb2",tr_pe_upd_id_thumb_2,"lblIdThumb2");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //id_thumb_3
        $oThumb3 = new ModelPicture();
        $arOptions = $oThumb3->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdThumb3","selIdThumb3");
        $oAuxField->set_value_to_select($this->get_post("selIdThumb3"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdThumb3",tr_pe_upd_id_thumb_3));
        $oAuxField->set_value_to_select($this->oPicture->get_id_thumb_3());
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdThumb3"));
        $oAuxLabel = new HelperLabel("selIdThumb3",tr_pe_upd_id_thumb_3,"lblIdThumb3");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //id_thumb_4
        $oThumb4 = new ModelPicture();
        $arOptions = $oThumb4->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdThumb4","selIdThumb4");
        $oAuxField->set_value_to_select($this->get_post("selIdThumb4"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdThumb4",tr_pe_upd_id_thumb_4));
        $oAuxField->set_value_to_select($this->oPicture->get_id_thumb_4());
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdThumb4"));
        $oAuxLabel = new HelperLabel("selIdThumb4",tr_pe_upd_id_thumb_4,"lblIdThumb4");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //id_thumb_5
        $oThumb5 = new ModelPicture();
        $arOptions = $oThumb5->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdThumb5","selIdThumb5");
        $oAuxField->set_value_to_select($this->get_post("selIdThumb5"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdThumb5",tr_pe_upd_id_thumb_5));
        $oAuxField->set_value_to_select($this->oPicture->get_id_thumb_5());
        if($usePost) $oAuxField->set_value_to_select($this->get_post("selIdThumb5"));
        $oAuxLabel = new HelperLabel("selIdThumb5",tr_pe_upd_id_thumb_5,"lblIdThumb5");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //width
        $oAuxField = new HelperInputText("txtWidth","txtWidth");
        $oAuxField->set_value($this->oPicture->get_width());
        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtWidth")));
        $oAuxLabel = new HelperLabel("txtWidth",tr_pe_upd_width,"lblWidth");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //height
        $oAuxField = new HelperInputText("txtHeight","txtHeight");
        $oAuxField->set_value($this->oPicture->get_height());
        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtHeight")));
        $oAuxLabel = new HelperLabel("txtHeight",tr_pe_upd_height,"lblHeight");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //resolution
        $oAuxField = new HelperInputText("txtResolution","txtResolution");
        $oAuxField->set_value($this->oPicture->get_resolution());
        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtResolution")));
        $oAuxLabel = new HelperLabel("txtResolution",tr_pe_upd_resolution,"lblResolution");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //order_by
        $oAuxField = new HelperInputText("txtOrderBy","txtOrderBy");
        $oAuxField->set_value($this->oPicture->get_order_by());
        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtOrderBy")));
        $oAuxLabel = new HelperLabel("txtOrderBy",tr_pe_upd_order_by,"lblOrderBy");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //rating
        $oAuxField = new HelperInputText("txtRating","txtRating");
        $oAuxField->set_value($this->oPicture->get_rating());
        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtRating")));
        $oAuxLabel = new HelperLabel("txtRating",tr_pe_upd_rating,"lblRating");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //show
        $oAuxField = new HelperInputText("txtShow","txtShow");
        $oAuxField->set_value($this->oPicture->get_show());
        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtShow")));
        $oAuxLabel = new HelperLabel("txtShow",tr_pe_upd_show,"lblShow");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //is_public
        $oAuxField = new HelperInputText("txtIsPublic","txtIsPublic");
        $oAuxField->set_value($this->oPicture->get_is_public());
        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtIsPublic")));
        $oAuxLabel = new HelperLabel("txtIsPublic",tr_pe_upd_is_public,"lblIsPublic");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //is_file
        $oAuxField = new HelperInputText("txtIsFile","txtIsFile");
        $oAuxField->set_value($this->oPicture->get_is_file());
        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtIsFile")));
        $oAuxLabel = new HelperLabel("txtIsFile",tr_pe_upd_is_file,"lblIsFile");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //is_error
        $oAuxField = new HelperInputText("txtIsError","txtIsError");
        $oAuxField->set_value($this->oPicture->get_is_error());
        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtIsError")));
        $oAuxLabel = new HelperLabel("txtIsError",tr_pe_upd_is_error,"lblIsError");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //size
        $oAuxField = new HelperInputText("txtSize","txtSize");
        $oAuxField->set_value(dbbo_numeric2($this->oPicture->get_size()));
        if($usePost) $oAuxField->set_value(dbbo_numeric2($this->get_post("txtSize")));
        $oAuxLabel = new HelperLabel("txtSize",tr_pe_upd_size,"lblSize");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //img_title
        $oAuxField = new HelperInputText("txtImgTitle","txtImgTitle");
        $oAuxField->set_value($this->oPicture->get_img_title());
        if($usePost) $oAuxField->set_value($this->get_post("txtImgTitle"));
        $oAuxLabel = new HelperLabel("txtImgTitle",tr_pe_upd_img_title,"lblImgTitle");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //anchor_text
        $oAuxField = new HelperInputText("txtAnchorText","txtAnchorText");
        $oAuxField->set_value($this->oPicture->get_anchor_text());
        if($usePost) $oAuxField->set_value($this->get_post("txtAnchorText"));
        $oAuxLabel = new HelperLabel("txtAnchorText",tr_pe_upd_anchor_text,"lblAnchorText");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //csv_tags
        $oAuxField = new HelperInputText("txtCsvTags","txtCsvTags");
        $oAuxField->set_value($this->oPicture->get_csv_tags());
        if($usePost) $oAuxField->set_value($this->get_post("txtCsvTags"));
        $oAuxLabel = new HelperLabel("txtCsvTags",tr_pe_upd_csv_tags,"lblCsvTags");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //extension
        $oAuxField = new HelperInputText("txtExtension","txtExtension");
        $oAuxField->set_value($this->oPicture->get_extension());
        if($usePost) $oAuxField->set_value($this->get_post("txtExtension"));
        $oAuxLabel = new HelperLabel("txtExtension",tr_pe_upd_extension,"lblExtension");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //source
        $oAuxField = new HelperInputText("txtSource","txtSource");
        $oAuxField->set_value($this->oPicture->get_source());
        if($usePost) $oAuxField->set_value($this->get_post("txtSource"));
        $oAuxLabel = new HelperLabel("txtSource",tr_pe_upd_source,"lblSource");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //folder
        $oAuxField = new HelperInputText("txtFolder","txtFolder");
        $oAuxField->set_value($this->oPicture->get_folder());
        if($usePost) $oAuxField->set_value($this->get_post("txtFolder"));
        $oAuxLabel = new HelperLabel("txtFolder",tr_pe_upd_folder,"lblFolder");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //parent_path
        $oAuxField = new HelperInputText("txtParentPath","txtParentPath");
        $oAuxField->set_value($this->oPicture->get_parent_path());
        if($usePost) $oAuxField->set_value($this->get_post("txtParentPath"));
        $oAuxLabel = new HelperLabel("txtParentPath",tr_pe_upd_parent_path,"lblParentPath");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //information
        $oAuxField = new HelperInputText("txtInformation","txtInformation");
        $oAuxField->set_value($this->oPicture->get_information());
        if($usePost) $oAuxField->set_value($this->get_post("txtInformation"));
        $oAuxLabel = new HelperLabel("txtInformation",tr_pe_upd_information,"lblInformation");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //information_extra
        $oAuxField = new HelperInputText("txtInformationExtra","txtInformationExtra");
        $oAuxField->set_value($this->oPicture->get_information_extra());
        if($usePost) $oAuxField->set_value($this->get_post("txtInformationExtra"));
        $oAuxLabel = new HelperLabel("txtInformationExtra",tr_pe_upd_information_extra,"lblInformationExtra");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //description
        //$oAuxField = new HelperInputText("txtDescription","txtDescription");
        //$oAuxField->set_value($this->oPicture->get_description());
        //if($usePost) $oAuxField->set_value($this->get_post("txtDescription"));
        //$oAuxLabel = new HelperLabel("txtDescription",tr_pe_upd_description,"lblDescription");
        //$oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        //$arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //uri_local
        $oAuxField = new HelperInputText("txtUriLocal","txtUriLocal");
        $oAuxField->set_value($this->oPicture->get_uri_local());
        if($usePost) $oAuxField->set_value($this->get_post("txtUriLocal"));
        $oAuxLabel = new HelperLabel("txtUriLocal",tr_pe_upd_uri_local,"lblUriLocal");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //uri_public
        $oAuxField = new HelperInputText("txtUriPublic","txtUriPublic");
        $oAuxField->set_value($this->oPicture->get_uri_public());
        if($usePost) $oAuxField->set_value($this->get_post("txtUriPublic"));
        $oAuxLabel = new HelperLabel("txtUriPublic",tr_pe_upd_uri_public,"lblUriPublic");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //name
        $oAuxField = new HelperInputText("txtFilename","txtFilename");
        $oAuxField->set_value($this->oPicture->get_filename());
        if($usePost) $oAuxField->set_value($this->get_post("txtFilename"));
        $oAuxLabel = new HelperLabel("txtFilename",tr_pe_upd_filename,"lblName");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //create_date
        $oAuxField = new HelperInputText("txtCreateDate","txtCreateDate");
        $oAuxField->set_value($this->oPicture->get_create_date());
        if($usePost) $oAuxField->set_value($this->get_post("txtCreateDate"));
        $oAuxLabel = new HelperLabel("txtCreateDate",tr_pe_upd_create_date,"lblCreateDate");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //modify_date
        $oAuxField = new HelperInputText("txtModifyDate","txtModifyDate");
        $oAuxField->set_value($this->oPicture->get_modify_date());
        if($usePost) $oAuxField->set_value($this->get_post("txtModifyDate"));
        $oAuxLabel = new HelperLabel("txtModifyDate",tr_pe_upd_modify_date,"lblModifyDate");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //BUTTON SAVE
        $oAuxField = new HelperButtonBasic("butSave",tr_pe_upd_savebutton);
        $oAuxField->add_class("btn btn-primary");
        $oAuxField->set_js_onclick("update();");
        if($this->oPermission->is_update())
            $arFields[] = new ApphelperFormactions(array($oAuxField));
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
        $arFieldsConfig["width"] = array("controlid"=>"txtWidth","label"=>tr_pe_upd_width,"length"=>4,"type"=>array());
        $arFieldsConfig["height"] = array("controlid"=>"txtHeight","label"=>tr_pe_upd_height,"length"=>4,"type"=>array());
        $arFieldsConfig["resolution"] = array("controlid"=>"txtResolution","label"=>tr_pe_upd_resolution,"length"=>4,"type"=>array());
        $arFieldsConfig["order_by"] = array("controlid"=>"txtOrderBy","label"=>tr_pe_upd_order_by,"length"=>4,"type"=>array());
        $arFieldsConfig["rating"] = array("controlid"=>"txtRating","label"=>tr_pe_upd_rating,"length"=>4,"type"=>array());
        $arFieldsConfig["show"] = array("controlid"=>"txtShow","label"=>tr_pe_upd_show,"length"=>4,"type"=>array());
        $arFieldsConfig["is_public"] = array("controlid"=>"txtIsPublic","label"=>tr_pe_upd_is_public,"length"=>4,"type"=>array());
        $arFieldsConfig["is_file"] = array("controlid"=>"txtIsFile","label"=>tr_pe_upd_is_file,"length"=>4,"type"=>array());
        $arFieldsConfig["is_error"] = array("controlid"=>"txtIsError","label"=>tr_pe_upd_is_error,"length"=>4,"type"=>array());
        $arFieldsConfig["size"] = array("controlid"=>"txtSize","label"=>tr_pe_upd_size,"length"=>9,"type"=>array("numeric"));
        $arFieldsConfig["img_title"] = array("controlid"=>"txtImgTitle","label"=>tr_pe_upd_img_title,"length"=>100,"type"=>array());
        $arFieldsConfig["anchor_text"] = array("controlid"=>"txtAnchorText","label"=>tr_pe_upd_anchor_text,"length"=>100,"type"=>array());
        $arFieldsConfig["csv_tags"] = array("controlid"=>"txtCsvTags","label"=>tr_pe_upd_csv_tags,"length"=>200,"type"=>array());
        $arFieldsConfig["extension"] = array("controlid"=>"txtExtension","label"=>tr_pe_upd_extension,"length"=>5,"type"=>array());
        $arFieldsConfig["source"] = array("controlid"=>"txtSource","label"=>tr_pe_upd_source,"length"=>250,"type"=>array());
        $arFieldsConfig["folder"] = array("controlid"=>"txtFolder","label"=>tr_pe_upd_folder,"length"=>50,"type"=>array());
        $arFieldsConfig["parent_path"] = array("controlid"=>"txtParentPath","label"=>tr_pe_upd_parent_path,"length"=>250,"type"=>array());
        $arFieldsConfig["information"] = array("controlid"=>"txtInformation","label"=>tr_pe_upd_information,"length"=>250,"type"=>array());
        $arFieldsConfig["information_extra"] = array("controlid"=>"txtInformationExtra","label"=>tr_pe_upd_information_extra,"length"=>250,"type"=>array());
        //$arFieldsConfig["description"] = array("controlid"=>"txtDescription","label"=>tr_pe_upd_description,"length"=>200,"type"=>array());
        $arFieldsConfig["uri_local"] = array("controlid"=>"txtUriLocal","label"=>tr_pe_upd_uri_local,"length"=>250,"type"=>array());
        $arFieldsConfig["uri_public"] = array("controlid"=>"txtUriPublic","label"=>tr_pe_upd_uri_public,"length"=>250,"type"=>array());
        $arFieldsConfig["name"] = array("controlid"=>"txtFilename","label"=>tr_pe_upd_filename,"length"=>250,"type"=>array());
        $arFieldsConfig["create_date"] = array("controlid"=>"txtCreateDate","label"=>tr_pe_upd_create_date,"length"=>14,"type"=>array());
        $arFieldsConfig["modify_date"] = array("controlid"=>"txtModifyDate","label"=>tr_pe_upd_modify_date,"length"=>14,"type"=>array());
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

//<editor-fold defaultstate="collapsed" desc="DELETE">
    //delete_1
    protected function single_delete()
    {
        $id = $this->get_get("id");
        if($id)
        {
            $this->oPicture->set_id($id);
            $this->oPicture->autodelete();
            if($this->oPicture->is_error())
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
            $this->oPicture->set_id($id);
            $this->oPicture->autodelete();
            if($this->oPicture->is_error())
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

//<editor-fold defaultstate="collapsed" desc="MULTIASSIGN">
    //multiassign_1
    protected function build_multiassign_buttons()
    {
        $arOpButtons = array();
        $arOpButtons["filters"]=array("href"=>"javascript:reset_filters();","icon"=>"awe-magic","innerhtml"=>tr_pe_clear_filters);
        $arOpButtons["reload"]=array("href"=>"javascript:TfwControl.form_submit();","icon"=>"awe-refresh","innerhtml"=>tr_pe_refresh);
        $arOpButtons["multiadd"]=array("href"=>"javascript:multiadd();","icon"=>"awe-external-link","innerhtml"=>tr_pe_multiadd);
        $arOpButtons["closeme"]=array("href"=>"javascript:closeme();","icon"=>"awe-remove-sign","innerhtml"=>tr_pe_closeme);
        $oOpButtons = new AppHelperButtontabs(tr_pe_entities);
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
        //id_type_entity
        $this->set_filter("id_type_entity","selIdTypeEntity");
        //id_owner_entity
        $this->set_filter("id_owner_entity","selIdOwnerEntity");
        //is_bydefault
        $this->set_filter("is_bydefault","selIdBydefault");
        //id_entity
        $this->set_filter("id_entity","selIdEntity");
        //id_owner
        $this->set_filter("id_owner","selIdOwner");
        //id_thumb_1
        $this->set_filter("id_thumb_1","selIdThumb1");
        //id_thumb_2
        $this->set_filter("id_thumb_2","selIdThumb2");
        //id_thumb_3
        $this->set_filter("id_thumb_3","selIdThumb3");
        //id_thumb_4
        $this->set_filter("id_thumb_4","selIdThumb4");
        //id_thumb_5
        $this->set_filter("id_thumb_5","selIdThumb5");
        //width
        $this->set_filter("width","txtWidth",array("operator"=>"like"));
        //height
        $this->set_filter("height","txtHeight",array("operator"=>"like"));
        //resolution
        $this->set_filter("resolution","txtResolution",array("operator"=>"like"));
        //order_by
        $this->set_filter("order_by","txtOrderBy",array("operator"=>"like"));
        //rating
        $this->set_filter("rating","txtRating",array("operator"=>"like"));
        //show
        $this->set_filter("show","txtShow",array("operator"=>"like"));
        //is_public
        $this->set_filter("is_public","txtIsPublic",array("operator"=>"like"));
        //is_file
        $this->set_filter("is_file","txtIsFile",array("operator"=>"like"));
        //is_error
        $this->set_filter("is_error","txtIsError",array("operator"=>"like"));
        //size
        $this->set_filter("size","txtSize",array("operator"=>"like"));
        //img_title
        $this->set_filter("img_title","txtImgTitle",array("operator"=>"like"));
        //anchor_text
        $this->set_filter("anchor_text","txtAnchorText",array("operator"=>"like"));
        //csv_tags
        $this->set_filter("csv_tags","txtCsvTags",array("operator"=>"like"));
        //extension
        $this->set_filter("extension","txtExtension",array("operator"=>"like"));
        //source
        $this->set_filter("source","txtSource",array("operator"=>"like"));
        //folder
        $this->set_filter("folder","txtFolder",array("operator"=>"like"));
        //parent_path
        $this->set_filter("parent_path","txtParentPath",array("operator"=>"like"));
        //information
        $this->set_filter("information","txtInformation",array("operator"=>"like"));
        //information_extra
        $this->set_filter("information_extra","txtInformationExtra",array("operator"=>"like"));
        //description
        //$this->set_filter("description","txtDescription",array("operator"=>"like"));
        //uri_local
        $this->set_filter("uri_local","txtUriLocal",array("operator"=>"like"));
        //uri_public
        $this->set_filter("uri_public","txtUriPublic",array("operator"=>"like"));
        //name
        $this->set_filter("name","txtFilename",array("operator"=>"like"));
        //create_date
        $this->set_filter("create_date","txtCreateDate",array("operator"=>"like"));
        //modify_date
        $this->set_filter("modify_date","txtModifyDate",array("operator"=>"like"));
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
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtId",tr_pe_fil_id));
        $arFields[] = $oAuxWrapper;
        //code_erp
        //$oAuxField = new HelperInputText("txtCodeErp","txtCodeErp");
        //$oAuxField->set_value($this->get_post("txtCodeErp"));
        //$oAuxField->on_entersubmit();
        //$oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtCodeErp",tr_pe_fil_code_erp));
        //$arFields[] = $oAuxWrapper;
        //id_type
        $oType = new ModelPicture();
        $arOptions = $oType->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdType","selIdType");
        $oAuxField->set_value_to_select($this->get_post("selIdType"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdType",tr_pe_fil_id_type));
        $arFields[] = $oAuxWrapper;
        //id_type_entity
        $oTypeEntity = new ModelPictureArray();
        $arOptions = $oTypeEntity->get_picklist_by_type("entities");
        $oAuxField = new HelperSelect($arOptions,"selIdTypeEntity","selIdTypeEntity");
        $oAuxField->set_value_to_select($this->get_post("selIdTypeEntity"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdTypeEntity",tr_pe_fil_id_type_entity));
        $arFields[] = $oAuxWrapper;
        //id_owner_entity
        $oOwnerEntity = new ModelPicture();
        $arOptions = $oOwnerEntity->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdOwnerEntity","selIdOwnerEntity");
        $oAuxField->set_value_to_select($this->get_post("selIdOwnerEntity"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdOwnerEntity",tr_pe_fil_id_owner_entity));
        $arFields[] = $oAuxWrapper;
        //is_bydefault
        $oBydefault = new ModelPicture();
        $arOptions = $oBydefault->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdBydefault","selIdBydefault");
        $oAuxField->set_value_to_select($this->get_post("selIdBydefault"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdBydefault",tr_pe_fil_is_bydefault));
        $arFields[] = $oAuxWrapper;
        //id_entity
        $oEntity = new ModelPicture();
        $arOptions = $oEntity->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdEntity","selIdEntity");
        $oAuxField->set_value_to_select($this->get_post("selIdEntity"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdEntity",tr_pe_fil_id_entity));
        $arFields[] = $oAuxWrapper;
        //id_owner
        $oOwner = new ModelPicture();
        $arOptions = $oOwner->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdOwner","selIdOwner");
        $oAuxField->set_value_to_select($this->get_post("selIdOwner"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdOwner",tr_pe_fil_id_owner));
        $arFields[] = $oAuxWrapper;
        //id_thumb_1
        $oThumb1 = new ModelPicture();
        $arOptions = $oThumb1->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdThumb1","selIdThumb1");
        $oAuxField->set_value_to_select($this->get_post("selIdThumb1"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdThumb1",tr_pe_fil_id_thumb_1));
        $arFields[] = $oAuxWrapper;
        //id_thumb_2
        $oThumb2 = new ModelPicture();
        $arOptions = $oThumb2->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdThumb2","selIdThumb2");
        $oAuxField->set_value_to_select($this->get_post("selIdThumb2"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdThumb2",tr_pe_fil_id_thumb_2));
        $arFields[] = $oAuxWrapper;
        //id_thumb_3
        $oThumb3 = new ModelPicture();
        $arOptions = $oThumb3->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdThumb3","selIdThumb3");
        $oAuxField->set_value_to_select($this->get_post("selIdThumb3"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdThumb3",tr_pe_fil_id_thumb_3));
        $arFields[] = $oAuxWrapper;
        //id_thumb_4
        $oThumb4 = new ModelPicture();
        $arOptions = $oThumb4->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdThumb4","selIdThumb4");
        $oAuxField->set_value_to_select($this->get_post("selIdThumb4"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdThumb4",tr_pe_fil_id_thumb_4));
        $arFields[] = $oAuxWrapper;
        //id_thumb_5
        $oThumb5 = new ModelPicture();
        $arOptions = $oThumb5->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdThumb5","selIdThumb5");
        $oAuxField->set_value_to_select($this->get_post("selIdThumb5"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdThumb5",tr_pe_fil_id_thumb_5));
        $arFields[] = $oAuxWrapper;
        //width
        $oAuxField = new HelperInputText("txtWidth","txtWidth");
        $oAuxField->set_value($this->get_post("txtWidth"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtWidth",tr_pe_fil_width));
        $arFields[] = $oAuxWrapper;
        //height
        $oAuxField = new HelperInputText("txtHeight","txtHeight");
        $oAuxField->set_value($this->get_post("txtHeight"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtHeight",tr_pe_fil_height));
        $arFields[] = $oAuxWrapper;
        //resolution
        $oAuxField = new HelperInputText("txtResolution","txtResolution");
        $oAuxField->set_value($this->get_post("txtResolution"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtResolution",tr_pe_fil_resolution));
        $arFields[] = $oAuxWrapper;
        //order_by
        $oAuxField = new HelperInputText("txtOrderBy","txtOrderBy");
        $oAuxField->set_value($this->get_post("txtOrderBy"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtOrderBy",tr_pe_fil_order_by));
        $arFields[] = $oAuxWrapper;
        //rating
        $oAuxField = new HelperInputText("txtRating","txtRating");
        $oAuxField->set_value($this->get_post("txtRating"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtRating",tr_pe_fil_rating));
        $arFields[] = $oAuxWrapper;
        //show
        $oAuxField = new HelperInputText("txtShow","txtShow");
        $oAuxField->set_value($this->get_post("txtShow"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtShow",tr_pe_fil_show));
        $arFields[] = $oAuxWrapper;
        //is_public
        $oAuxField = new HelperInputText("txtIsPublic","txtIsPublic");
        $oAuxField->set_value($this->get_post("txtIsPublic"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtIsPublic",tr_pe_fil_is_public));
        $arFields[] = $oAuxWrapper;
        //is_file
        $oAuxField = new HelperInputText("txtIsFile","txtIsFile");
        $oAuxField->set_value($this->get_post("txtIsFile"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtIsFile",tr_pe_fil_is_file));
        $arFields[] = $oAuxWrapper;
        //is_error
        $oAuxField = new HelperInputText("txtIsError","txtIsError");
        $oAuxField->set_value($this->get_post("txtIsError"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtIsError",tr_pe_fil_is_error));
        $arFields[] = $oAuxWrapper;
        //size
        $oAuxField = new HelperInputText("txtSize","txtSize");
        $oAuxField->set_value($this->get_post("txtSize"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtSize",tr_pe_fil_size));
        $arFields[] = $oAuxWrapper;
        //img_title
        $oAuxField = new HelperInputText("txtImgTitle","txtImgTitle");
        $oAuxField->set_value($this->get_post("txtImgTitle"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtImgTitle",tr_pe_fil_img_title));
        $arFields[] = $oAuxWrapper;
        //anchor_text
        $oAuxField = new HelperInputText("txtAnchorText","txtAnchorText");
        $oAuxField->set_value($this->get_post("txtAnchorText"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtAnchorText",tr_pe_fil_anchor_text));
        $arFields[] = $oAuxWrapper;
        //csv_tags
        $oAuxField = new HelperInputText("txtCsvTags","txtCsvTags");
        $oAuxField->set_value($this->get_post("txtCsvTags"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtCsvTags",tr_pe_fil_csv_tags));
        $arFields[] = $oAuxWrapper;
        //extension
        $oAuxField = new HelperInputText("txtExtension","txtExtension");
        $oAuxField->set_value($this->get_post("txtExtension"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtExtension",tr_pe_fil_extension));
        $arFields[] = $oAuxWrapper;
        //source
        $oAuxField = new HelperInputText("txtSource","txtSource");
        $oAuxField->set_value($this->get_post("txtSource"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtSource",tr_pe_fil_source));
        $arFields[] = $oAuxWrapper;
        //folder
        $oAuxField = new HelperInputText("txtFolder","txtFolder");
        $oAuxField->set_value($this->get_post("txtFolder"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtFolder",tr_pe_fil_folder));
        $arFields[] = $oAuxWrapper;
        //parent_path
        $oAuxField = new HelperInputText("txtParentPath","txtParentPath");
        $oAuxField->set_value($this->get_post("txtParentPath"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtParentPath",tr_pe_fil_parent_path));
        $arFields[] = $oAuxWrapper;
        //information
        $oAuxField = new HelperInputText("txtInformation","txtInformation");
        $oAuxField->set_value($this->get_post("txtInformation"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtInformation",tr_pe_fil_information));
        $arFields[] = $oAuxWrapper;
        //information_extra
        $oAuxField = new HelperInputText("txtInformationExtra","txtInformationExtra");
        $oAuxField->set_value($this->get_post("txtInformationExtra"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtInformationExtra",tr_pe_fil_information_extra));
        $arFields[] = $oAuxWrapper;
        //description
        //$oAuxField = new HelperInputText("txtDescription","txtDescription");
        //$oAuxField->set_value($this->get_post("txtDescription"));
        //$oAuxField->on_entersubmit();
        //$oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtDescription",tr_pe_fil_description));
        //$arFields[] = $oAuxWrapper;
        //uri_local
        $oAuxField = new HelperInputText("txtUriLocal","txtUriLocal");
        $oAuxField->set_value($this->get_post("txtUriLocal"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtUriLocal",tr_pe_fil_uri_local));
        $arFields[] = $oAuxWrapper;
        //uri_public
        $oAuxField = new HelperInputText("txtUriPublic","txtUriPublic");
        $oAuxField->set_value($this->get_post("txtUriPublic"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtUriPublic",tr_pe_fil_uri_public));
        $arFields[] = $oAuxWrapper;
        //name
        $oAuxField = new HelperInputText("txtFilename","txtFilename");
        $oAuxField->set_value($this->get_post("txtFilename"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtFilename",tr_pe_fil_filename));
        $arFields[] = $oAuxWrapper;
        //create_date
        $oAuxField = new HelperInputText("txtCreateDate","txtCreateDate");
        $oAuxField->set_value($this->get_post("txtCreateDate"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtCreateDate",tr_pe_fil_create_date));
        $arFields[] = $oAuxWrapper;
        //modify_date
        $oAuxField = new HelperInputText("txtModifyDate","txtModifyDate");
        $oAuxField->set_value($this->get_post("txtModifyDate"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtModifyDate",tr_pe_fil_modify_date));
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
        //id_type_entity
        $this->set_filter_value("id_type_entity",$this->get_post("selIdTypeEntity"));
        //id_owner_entity
        $this->set_filter_value("id_owner_entity",$this->get_post("selIdOwnerEntity"));
        //is_bydefault
        $this->set_filter_value("is_bydefault",$this->get_post("selIdBydefault"));
        //id_entity
        $this->set_filter_value("id_entity",$this->get_post("selIdEntity"));
        //id_owner
        $this->set_filter_value("id_owner",$this->get_post("selIdOwner"));
        //id_thumb_1
        $this->set_filter_value("id_thumb_1",$this->get_post("selIdThumb1"));
        //id_thumb_2
        $this->set_filter_value("id_thumb_2",$this->get_post("selIdThumb2"));
        //id_thumb_3
        $this->set_filter_value("id_thumb_3",$this->get_post("selIdThumb3"));
        //id_thumb_4
        $this->set_filter_value("id_thumb_4",$this->get_post("selIdThumb4"));
        //id_thumb_5
        $this->set_filter_value("id_thumb_5",$this->get_post("selIdThumb5"));
        //width
        $this->set_filter_value("width",$this->get_post("txtWidth"));
        //height
        $this->set_filter_value("height",$this->get_post("txtHeight"));
        //resolution
        $this->set_filter_value("resolution",$this->get_post("txtResolution"));
        //order_by
        $this->set_filter_value("order_by",$this->get_post("txtOrderBy"));
        //rating
        $this->set_filter_value("rating",$this->get_post("txtRating"));
        //show
        $this->set_filter_value("show",$this->get_post("txtShow"));
        //is_public
        $this->set_filter_value("is_public",$this->get_post("txtIsPublic"));
        //is_file
        $this->set_filter_value("is_file",$this->get_post("txtIsFile"));
        //is_error
        $this->set_filter_value("is_error",$this->get_post("txtIsError"));
        //size
        $this->set_filter_value("size",$this->get_post("txtSize"));
        //img_title
        $this->set_filter_value("img_title",$this->get_post("txtImgTitle"));
        //anchor_text
        $this->set_filter_value("anchor_text",$this->get_post("txtAnchorText"));
        //csv_tags
        $this->set_filter_value("csv_tags",$this->get_post("txtCsvTags"));
        //extension
        $this->set_filter_value("extension",$this->get_post("txtExtension"));
        //source
        $this->set_filter_value("source",$this->get_post("txtSource"));
        //folder
        $this->set_filter_value("folder",$this->get_post("txtFolder"));
        //parent_path
        $this->set_filter_value("parent_path",$this->get_post("txtParentPath"));
        //information
        $this->set_filter_value("information",$this->get_post("txtInformation"));
        //information_extra
        $this->set_filter_value("information_extra",$this->get_post("txtInformationExtra"));
        //description
        //$this->set_filter_value("description",$this->get_post("txtDescription"));
        //uri_local
        $this->set_filter_value("uri_local",$this->get_post("txtUriLocal"));
        //uri_public
        $this->set_filter_value("uri_public",$this->get_post("txtUriPublic"));
        //name
        $this->set_filter_value("name",$this->get_post("txtFilename"));
        //create_date
        $this->set_filter_value("create_date",$this->get_post("txtCreateDate"));
        //modify_date
        $this->set_filter_value("modify_date",$this->get_post("txtModifyDate"));
    }//set_multiassignfilters_from_post()

    //multiassign_5
    protected function get_multiassign_columns()
    {
        $arColumns["id"] = tr_pe_col_id;
        //$arColumns["code_erp"] = tr_pe_col_code_erp;
        //$arColumns["id_type"] = tr_pe_col_id_type;
        $arColumns["type"] = tr_pe_col_id_type;
        //$arColumns["id_type_entity"] = tr_pe_col_id_type_entity;
        $arColumns["entity"] = tr_pe_col_id_type_entity;
        //$arColumns["id_owner_entity"] = tr_pe_col_id_owner_entity;
        $arColumns["ownerentity"] = tr_pe_col_id_owner_entity;
        //$arColumns["is_bydefault"] = tr_pe_col_is_bydefault;
        $arColumns["bydefault"] = tr_pe_col_is_bydefault;
        //$arColumns["id_entity"] = tr_pe_col_id_entity;
        $arColumns["entity"] = tr_pe_col_id_entity;
        //$arColumns["id_owner"] = tr_pe_col_id_owner;
        $arColumns["owner"] = tr_pe_col_id_owner;
        //$arColumns["id_thumb_1"] = tr_pe_col_id_thumb_1;
        $arColumns["thumb1"] = tr_pe_col_id_thumb_1;
        //$arColumns["id_thumb_2"] = tr_pe_col_id_thumb_2;
        $arColumns["thumb2"] = tr_pe_col_id_thumb_2;
        //$arColumns["id_thumb_3"] = tr_pe_col_id_thumb_3;
        $arColumns["thumb3"] = tr_pe_col_id_thumb_3;
        //$arColumns["id_thumb_4"] = tr_pe_col_id_thumb_4;
        $arColumns["thumb4"] = tr_pe_col_id_thumb_4;
        //$arColumns["id_thumb_5"] = tr_pe_col_id_thumb_5;
        $arColumns["thumb5"] = tr_pe_col_id_thumb_5;
        $arColumns["width"] = tr_pe_col_width;
        $arColumns["height"] = tr_pe_col_height;
        $arColumns["resolution"] = tr_pe_col_resolution;
        $arColumns["order_by"] = tr_pe_col_order_by;
        $arColumns["rating"] = tr_pe_col_rating;
        $arColumns["show"] = tr_pe_col_show;
        $arColumns["is_public"] = tr_pe_col_is_public;
        $arColumns["is_file"] = tr_pe_col_is_file;
        $arColumns["is_error"] = tr_pe_col_is_error;
        $arColumns["size"] = tr_pe_col_size;
        $arColumns["img_title"] = tr_pe_col_img_title;
        $arColumns["anchor_text"] = tr_pe_col_anchor_text;
        $arColumns["csv_tags"] = tr_pe_col_csv_tags;
        $arColumns["extension"] = tr_pe_col_extension;
        $arColumns["source"] = tr_pe_col_source;
        $arColumns["folder"] = tr_pe_col_folder;
        $arColumns["parent_path"] = tr_pe_col_parent_path;
        $arColumns["information"] = tr_pe_col_information;
        $arColumns["information_extra"] = tr_pe_col_information_extra;
        //$arColumns["description"] = tr_pe_col_description;
        $arColumns["uri_local"] = tr_pe_col_uri_local;
        $arColumns["uri_public"] = tr_pe_col_uri_public;
        $arColumns["name"] = tr_pe_col_filename;
        $arColumns["create_date"] = tr_pe_col_create_date;
        $arColumns["modify_date"] = tr_pe_col_modify_date;
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
        $this->oPicture->set_orderby($this->get_orderby());
        $this->oPicture->set_ordertype($this->get_ordertype());
        $this->oPicture->set_filters($this->get_filter_searchconfig());
        //hierarchy recover
        //$this->oPicture->set_select_user($this->oSessionUser->get_id());
        //RECOVER DATALIST
        $arList = $this->oPicture->get_select_all_ids();
        $iRequestPage = $this->get_post("selPage");
        $oPage = new ComponentPage($arList,$iRequestPage);
        $arList = $oPage->get_items_to_show();
        $arList = $this->oPicture->get_select_all_by_ids($arList);
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
        $oOpButtons = new AppHelperButtontabs(tr_pe_entities);
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
        $arButTabs["filters"]=array("href"=>"javascript:reset_filters();","icon"=>"awe-magic","innerhtml"=>tr_pe_clear_filters);
        $arButTabs["reload"]=array("href"=>"javascript:TfwControl.form_submit();","icon"=>"awe-refresh","innerhtml"=>tr_pe_refresh);
        $arButTabs["closeme"]=array("href"=>"javascript:closeme();","icon"=>"awe-remove-sign","innerhtml"=>tr_pe_closeme);
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
        //id_type_entity
        $this->set_filter("id_type_entity","selIdTypeEntity");
        //id_owner_entity
        $this->set_filter("id_owner_entity","selIdOwnerEntity");
        //is_bydefault
        $this->set_filter("is_bydefault","selIdBydefault");
        //id_entity
        $this->set_filter("id_entity","selIdEntity");
        //id_owner
        $this->set_filter("id_owner","selIdOwner");
        //id_thumb_1
        $this->set_filter("id_thumb_1","selIdThumb1");
        //id_thumb_2
        $this->set_filter("id_thumb_2","selIdThumb2");
        //id_thumb_3
        $this->set_filter("id_thumb_3","selIdThumb3");
        //id_thumb_4
        $this->set_filter("id_thumb_4","selIdThumb4");
        //id_thumb_5
        $this->set_filter("id_thumb_5","selIdThumb5");
        //width
        $this->set_filter("width","txtWidth",array("operator"=>"like"));
        //height
        $this->set_filter("height","txtHeight",array("operator"=>"like"));
        //resolution
        $this->set_filter("resolution","txtResolution",array("operator"=>"like"));
        //order_by
        $this->set_filter("order_by","txtOrderBy",array("operator"=>"like"));
        //rating
        $this->set_filter("rating","txtRating",array("operator"=>"like"));
        //show
        $this->set_filter("show","txtShow",array("operator"=>"like"));
        //is_public
        $this->set_filter("is_public","txtIsPublic",array("operator"=>"like"));
        //is_file
        $this->set_filter("is_file","txtIsFile",array("operator"=>"like"));
        //is_error
        $this->set_filter("is_error","txtIsError",array("operator"=>"like"));
        //size
        $this->set_filter("size","txtSize",array("operator"=>"like"));
        //img_title
        $this->set_filter("img_title","txtImgTitle",array("operator"=>"like"));
        //anchor_text
        $this->set_filter("anchor_text","txtAnchorText",array("operator"=>"like"));
        //csv_tags
        $this->set_filter("csv_tags","txtCsvTags",array("operator"=>"like"));
        //extension
        $this->set_filter("extension","txtExtension",array("operator"=>"like"));
        //source
        $this->set_filter("source","txtSource",array("operator"=>"like"));
        //folder
        $this->set_filter("folder","txtFolder",array("operator"=>"like"));
        //parent_path
        $this->set_filter("parent_path","txtParentPath",array("operator"=>"like"));
        //information
        $this->set_filter("information","txtInformation",array("operator"=>"like"));
        //information_extra
        $this->set_filter("information_extra","txtInformationExtra",array("operator"=>"like"));
        //description
        //$this->set_filter("description","txtDescription",array("operator"=>"like"));
        //uri_local
        $this->set_filter("uri_local","txtUriLocal",array("operator"=>"like"));
        //uri_public
        $this->set_filter("uri_public","txtUriPublic",array("operator"=>"like"));
        //name
        $this->set_filter("name","txtFilename",array("operator"=>"like"));
        //create_date
        $this->set_filter("create_date","txtCreateDate",array("operator"=>"like"));
        //modify_date
        $this->set_filter("modify_date","txtModifyDate",array("operator"=>"like"));
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
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtId",tr_pe_fil_id));
        $arFields[] = $oAuxWrapper;
        //code_erp
        //$oAuxField = new HelperInputText("txtCodeErp","txtCodeErp");
        //$oAuxField->set_value($this->get_post("txtCodeErp"));
        //$oAuxField->on_entersubmit();
        //$oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtCodeErp",tr_pe_fil_code_erp));
        //$arFields[] = $oAuxWrapper;
        //id_type
        $oType = new ModelPicture();
        $arOptions = $oType->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdType","selIdType");
        $oAuxField->set_value_to_select($this->get_post("selIdType"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdType",tr_pe_fil_id_type));
        $arFields[] = $oAuxWrapper;
        //id_type_entity
        $oTypeEntity = new ModelPictureArray();
        $arOptions = $oTypeEntity->get_picklist_by_type("entities");
        $oAuxField = new HelperSelect($arOptions,"selIdTypeEntity","selIdTypeEntity");
        $oAuxField->set_value_to_select($this->get_post("selIdTypeEntity"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdTypeEntity",tr_pe_fil_id_type_entity));
        $arFields[] = $oAuxWrapper;
        //id_owner_entity
        $oOwnerEntity = new ModelPicture();
        $arOptions = $oOwnerEntity->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdOwnerEntity","selIdOwnerEntity");
        $oAuxField->set_value_to_select($this->get_post("selIdOwnerEntity"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdOwnerEntity",tr_pe_fil_id_owner_entity));
        $arFields[] = $oAuxWrapper;
        //is_bydefault
        $oBydefault = new ModelPicture();
        $arOptions = $oBydefault->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdBydefault","selIdBydefault");
        $oAuxField->set_value_to_select($this->get_post("selIdBydefault"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdBydefault",tr_pe_fil_is_bydefault));
        $arFields[] = $oAuxWrapper;
        //id_entity
        $oEntity = new ModelPicture();
        $arOptions = $oEntity->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdEntity","selIdEntity");
        $oAuxField->set_value_to_select($this->get_post("selIdEntity"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdEntity",tr_pe_fil_id_entity));
        $arFields[] = $oAuxWrapper;
        //id_owner
        $oOwner = new ModelPicture();
        $arOptions = $oOwner->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdOwner","selIdOwner");
        $oAuxField->set_value_to_select($this->get_post("selIdOwner"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdOwner",tr_pe_fil_id_owner));
        $arFields[] = $oAuxWrapper;
        //id_thumb_1
        $oThumb1 = new ModelPicture();
        $arOptions = $oThumb1->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdThumb1","selIdThumb1");
        $oAuxField->set_value_to_select($this->get_post("selIdThumb1"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdThumb1",tr_pe_fil_id_thumb_1));
        $arFields[] = $oAuxWrapper;
        //id_thumb_2
        $oThumb2 = new ModelPicture();
        $arOptions = $oThumb2->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdThumb2","selIdThumb2");
        $oAuxField->set_value_to_select($this->get_post("selIdThumb2"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdThumb2",tr_pe_fil_id_thumb_2));
        $arFields[] = $oAuxWrapper;
        //id_thumb_3
        $oThumb3 = new ModelPicture();
        $arOptions = $oThumb3->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdThumb3","selIdThumb3");
        $oAuxField->set_value_to_select($this->get_post("selIdThumb3"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdThumb3",tr_pe_fil_id_thumb_3));
        $arFields[] = $oAuxWrapper;
        //id_thumb_4
        $oThumb4 = new ModelPicture();
        $arOptions = $oThumb4->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdThumb4","selIdThumb4");
        $oAuxField->set_value_to_select($this->get_post("selIdThumb4"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdThumb4",tr_pe_fil_id_thumb_4));
        $arFields[] = $oAuxWrapper;
        //id_thumb_5
        $oThumb5 = new ModelPicture();
        $arOptions = $oThumb5->get_picklist();
        $oAuxField = new HelperSelect($arOptions,"selIdThumb5","selIdThumb5");
        $oAuxField->set_value_to_select($this->get_post("selIdThumb5"));
        $oAuxField->set_postback();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("selIdThumb5",tr_pe_fil_id_thumb_5));
        $arFields[] = $oAuxWrapper;
        //width
        $oAuxField = new HelperInputText("txtWidth","txtWidth");
        $oAuxField->set_value($this->get_post("txtWidth"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtWidth",tr_pe_fil_width));
        $arFields[] = $oAuxWrapper;
        //height
        $oAuxField = new HelperInputText("txtHeight","txtHeight");
        $oAuxField->set_value($this->get_post("txtHeight"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtHeight",tr_pe_fil_height));
        $arFields[] = $oAuxWrapper;
        //resolution
        $oAuxField = new HelperInputText("txtResolution","txtResolution");
        $oAuxField->set_value($this->get_post("txtResolution"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtResolution",tr_pe_fil_resolution));
        $arFields[] = $oAuxWrapper;
        //order_by
        $oAuxField = new HelperInputText("txtOrderBy","txtOrderBy");
        $oAuxField->set_value($this->get_post("txtOrderBy"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtOrderBy",tr_pe_fil_order_by));
        $arFields[] = $oAuxWrapper;
        //rating
        $oAuxField = new HelperInputText("txtRating","txtRating");
        $oAuxField->set_value($this->get_post("txtRating"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtRating",tr_pe_fil_rating));
        $arFields[] = $oAuxWrapper;
        //show
        $oAuxField = new HelperInputText("txtShow","txtShow");
        $oAuxField->set_value($this->get_post("txtShow"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtShow",tr_pe_fil_show));
        $arFields[] = $oAuxWrapper;
        //is_public
        $oAuxField = new HelperInputText("txtIsPublic","txtIsPublic");
        $oAuxField->set_value($this->get_post("txtIsPublic"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtIsPublic",tr_pe_fil_is_public));
        $arFields[] = $oAuxWrapper;
        //is_file
        $oAuxField = new HelperInputText("txtIsFile","txtIsFile");
        $oAuxField->set_value($this->get_post("txtIsFile"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtIsFile",tr_pe_fil_is_file));
        $arFields[] = $oAuxWrapper;
        //is_error
        $oAuxField = new HelperInputText("txtIsError","txtIsError");
        $oAuxField->set_value($this->get_post("txtIsError"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtIsError",tr_pe_fil_is_error));
        $arFields[] = $oAuxWrapper;
        //size
        $oAuxField = new HelperInputText("txtSize","txtSize");
        $oAuxField->set_value($this->get_post("txtSize"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtSize",tr_pe_fil_size));
        $arFields[] = $oAuxWrapper;
        //img_title
        $oAuxField = new HelperInputText("txtImgTitle","txtImgTitle");
        $oAuxField->set_value($this->get_post("txtImgTitle"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtImgTitle",tr_pe_fil_img_title));
        $arFields[] = $oAuxWrapper;
        //anchor_text
        $oAuxField = new HelperInputText("txtAnchorText","txtAnchorText");
        $oAuxField->set_value($this->get_post("txtAnchorText"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtAnchorText",tr_pe_fil_anchor_text));
        $arFields[] = $oAuxWrapper;
        //csv_tags
        $oAuxField = new HelperInputText("txtCsvTags","txtCsvTags");
        $oAuxField->set_value($this->get_post("txtCsvTags"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtCsvTags",tr_pe_fil_csv_tags));
        $arFields[] = $oAuxWrapper;
        //extension
        $oAuxField = new HelperInputText("txtExtension","txtExtension");
        $oAuxField->set_value($this->get_post("txtExtension"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtExtension",tr_pe_fil_extension));
        $arFields[] = $oAuxWrapper;
        //source
        $oAuxField = new HelperInputText("txtSource","txtSource");
        $oAuxField->set_value($this->get_post("txtSource"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtSource",tr_pe_fil_source));
        $arFields[] = $oAuxWrapper;
        //folder
        $oAuxField = new HelperInputText("txtFolder","txtFolder");
        $oAuxField->set_value($this->get_post("txtFolder"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtFolder",tr_pe_fil_folder));
        $arFields[] = $oAuxWrapper;
        //parent_path
        $oAuxField = new HelperInputText("txtParentPath","txtParentPath");
        $oAuxField->set_value($this->get_post("txtParentPath"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtParentPath",tr_pe_fil_parent_path));
        $arFields[] = $oAuxWrapper;
        //information
        $oAuxField = new HelperInputText("txtInformation","txtInformation");
        $oAuxField->set_value($this->get_post("txtInformation"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtInformation",tr_pe_fil_information));
        $arFields[] = $oAuxWrapper;
        //information_extra
        $oAuxField = new HelperInputText("txtInformationExtra","txtInformationExtra");
        $oAuxField->set_value($this->get_post("txtInformationExtra"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtInformationExtra",tr_pe_fil_information_extra));
        $arFields[] = $oAuxWrapper;
        //description
        //$oAuxField = new HelperInputText("txtDescription","txtDescription");
        //$oAuxField->set_value($this->get_post("txtDescription"));
        //$oAuxField->on_entersubmit();
        //$oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtDescription",tr_pe_fil_description));
        //$arFields[] = $oAuxWrapper;
        //uri_local
        $oAuxField = new HelperInputText("txtUriLocal","txtUriLocal");
        $oAuxField->set_value($this->get_post("txtUriLocal"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtUriLocal",tr_pe_fil_uri_local));
        $arFields[] = $oAuxWrapper;
        //uri_public
        $oAuxField = new HelperInputText("txtUriPublic","txtUriPublic");
        $oAuxField->set_value($this->get_post("txtUriPublic"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtUriPublic",tr_pe_fil_uri_public));
        $arFields[] = $oAuxWrapper;
        //name
        $oAuxField = new HelperInputText("txtFilename","txtFilename");
        $oAuxField->set_value($this->get_post("txtFilename"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtFilename",tr_pe_fil_filename));
        $arFields[] = $oAuxWrapper;
        //create_date
        $oAuxField = new HelperInputText("txtCreateDate","txtCreateDate");
        $oAuxField->set_value($this->get_post("txtCreateDate"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtCreateDate",tr_pe_fil_create_date));
        $arFields[] = $oAuxWrapper;
        //modify_date
        $oAuxField = new HelperInputText("txtModifyDate","txtModifyDate");
        $oAuxField->set_value($this->get_post("txtModifyDate"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtModifyDate",tr_pe_fil_modify_date));
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
        //id_type_entity
        $this->set_filter_value("id_type_entity",$this->get_post("selIdTypeEntity"));
        //id_owner_entity
        $this->set_filter_value("id_owner_entity",$this->get_post("selIdOwnerEntity"));
        //is_bydefault
        $this->set_filter_value("is_bydefault",$this->get_post("selIdBydefault"));
        //id_entity
        $this->set_filter_value("id_entity",$this->get_post("selIdEntity"));
        //id_owner
        $this->set_filter_value("id_owner",$this->get_post("selIdOwner"));
        //id_thumb_1
        $this->set_filter_value("id_thumb_1",$this->get_post("selIdThumb1"));
        //id_thumb_2
        $this->set_filter_value("id_thumb_2",$this->get_post("selIdThumb2"));
        //id_thumb_3
        $this->set_filter_value("id_thumb_3",$this->get_post("selIdThumb3"));
        //id_thumb_4
        $this->set_filter_value("id_thumb_4",$this->get_post("selIdThumb4"));
        //id_thumb_5
        $this->set_filter_value("id_thumb_5",$this->get_post("selIdThumb5"));
        //width
        $this->set_filter_value("width",$this->get_post("txtWidth"));
        //height
        $this->set_filter_value("height",$this->get_post("txtHeight"));
        //resolution
        $this->set_filter_value("resolution",$this->get_post("txtResolution"));
        //order_by
        $this->set_filter_value("order_by",$this->get_post("txtOrderBy"));
        //rating
        $this->set_filter_value("rating",$this->get_post("txtRating"));
        //show
        $this->set_filter_value("show",$this->get_post("txtShow"));
        //is_public
        $this->set_filter_value("is_public",$this->get_post("txtIsPublic"));
        //is_file
        $this->set_filter_value("is_file",$this->get_post("txtIsFile"));
        //is_error
        $this->set_filter_value("is_error",$this->get_post("txtIsError"));
        //size
        $this->set_filter_value("size",$this->get_post("txtSize"));
        //img_title
        $this->set_filter_value("img_title",$this->get_post("txtImgTitle"));
        //anchor_text
        $this->set_filter_value("anchor_text",$this->get_post("txtAnchorText"));
        //csv_tags
        $this->set_filter_value("csv_tags",$this->get_post("txtCsvTags"));
        //extension
        $this->set_filter_value("extension",$this->get_post("txtExtension"));
        //source
        $this->set_filter_value("source",$this->get_post("txtSource"));
        //folder
        $this->set_filter_value("folder",$this->get_post("txtFolder"));
        //parent_path
        $this->set_filter_value("parent_path",$this->get_post("txtParentPath"));
        //information
        $this->set_filter_value("information",$this->get_post("txtInformation"));
        //information_extra
        $this->set_filter_value("information_extra",$this->get_post("txtInformationExtra"));
        //description
        //$this->set_filter_value("description",$this->get_post("txtDescription"));
        //uri_local
        $this->set_filter_value("uri_local",$this->get_post("txtUriLocal"));
        //uri_public
        $this->set_filter_value("uri_public",$this->get_post("txtUriPublic"));
        //name
        $this->set_filter_value("name",$this->get_post("txtFilename"));
        //create_date
        $this->set_filter_value("create_date",$this->get_post("txtCreateDate"));
        //modify_date
        $this->set_filter_value("modify_date",$this->get_post("txtModifyDate"));
    }//set_singleassignfilters_from_post()

    //singleassign_5
    protected function get_singleassign_columns()
    {
        $arColumns["id"] = tr_pe_col_id;
        //$arColumns["code_erp"] = tr_pe_col_code_erp;
        //$arColumns["id_type"] = tr_pe_col_id_type;
        $arColumns["type"] = tr_pe_col_id_type;
        //$arColumns["id_type_entity"] = tr_pe_col_id_type_entity;
        $arColumns["entity"] = tr_pe_col_id_type_entity;
        //$arColumns["id_owner_entity"] = tr_pe_col_id_owner_entity;
        $arColumns["ownerentity"] = tr_pe_col_id_owner_entity;
        //$arColumns["is_bydefault"] = tr_pe_col_is_bydefault;
        $arColumns["bydefault"] = tr_pe_col_is_bydefault;
        //$arColumns["id_entity"] = tr_pe_col_id_entity;
        $arColumns["entity"] = tr_pe_col_id_entity;
        //$arColumns["id_owner"] = tr_pe_col_id_owner;
        $arColumns["owner"] = tr_pe_col_id_owner;
        //$arColumns["id_thumb_1"] = tr_pe_col_id_thumb_1;
        $arColumns["thumb1"] = tr_pe_col_id_thumb_1;
        //$arColumns["id_thumb_2"] = tr_pe_col_id_thumb_2;
        $arColumns["thumb2"] = tr_pe_col_id_thumb_2;
        //$arColumns["id_thumb_3"] = tr_pe_col_id_thumb_3;
        $arColumns["thumb3"] = tr_pe_col_id_thumb_3;
        //$arColumns["id_thumb_4"] = tr_pe_col_id_thumb_4;
        $arColumns["thumb4"] = tr_pe_col_id_thumb_4;
        //$arColumns["id_thumb_5"] = tr_pe_col_id_thumb_5;
        $arColumns["thumb5"] = tr_pe_col_id_thumb_5;
        $arColumns["width"] = tr_pe_col_width;
        $arColumns["height"] = tr_pe_col_height;
        $arColumns["resolution"] = tr_pe_col_resolution;
        $arColumns["order_by"] = tr_pe_col_order_by;
        $arColumns["rating"] = tr_pe_col_rating;
        $arColumns["show"] = tr_pe_col_show;
        $arColumns["is_public"] = tr_pe_col_is_public;
        $arColumns["is_file"] = tr_pe_col_is_file;
        $arColumns["is_error"] = tr_pe_col_is_error;
        $arColumns["size"] = tr_pe_col_size;
        $arColumns["img_title"] = tr_pe_col_img_title;
        $arColumns["anchor_text"] = tr_pe_col_anchor_text;
        $arColumns["csv_tags"] = tr_pe_col_csv_tags;
        $arColumns["extension"] = tr_pe_col_extension;
        $arColumns["source"] = tr_pe_col_source;
        $arColumns["folder"] = tr_pe_col_folder;
        $arColumns["parent_path"] = tr_pe_col_parent_path;
        $arColumns["information"] = tr_pe_col_information;
        $arColumns["information_extra"] = tr_pe_col_information_extra;
        //$arColumns["description"] = tr_pe_col_description;
        $arColumns["uri_local"] = tr_pe_col_uri_local;
        $arColumns["uri_public"] = tr_pe_col_uri_public;
        $arColumns["name"] = tr_pe_col_filename;
        $arColumns["create_date"] = tr_pe_col_create_date;
        $arColumns["modify_date"] = tr_pe_col_modify_date;
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
        $this->oPicture->set_orderby($this->get_orderby());
        $this->oPicture->set_ordertype($this->get_ordertype());
        $this->oPicture->set_filters($this->get_filter_searchconfig());
        $arList = $this->oPicture->get_select_all_ids();
        $iRequestPage = $this->get_post("selPage");
        $oPage = new ComponentPage($arList,$iRequestPage);
        $arList = $oPage->get_items_to_show();
        $arList = $this->oPicture->get_select_all_by_ids($arList);
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
        $oOpButtons = new AppHelperButtontabs(tr_pe_entities);
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

    protected function is_grid(){ return ($this->sDisplayMode=="grid");}
    protected function is_notgrid(){return ($this->sDisplayMode!="grid");}    
    protected function is_catalog(){ return ($this->sDisplayMode=="catalog");}
    protected function is_notcatalog(){return ($this->sDisplayMode!="catalog");}
    protected function get_http_indexes($sInputFileName)
    {
        $arIndex = array();
        foreach($_POST[$sInputFileName] as $i=>$sUrl)
            if(trim($sUrl)!="") $arIndex[] = $i;
        return $arIndex;        
    }
    
    protected function clean_extension(&$sExtension)
    {
        $sExtension = strtolower($sExtension);
        $arExtensions = array("jpg","png","jpeg","gif","tiff","tif","bmp");
        foreach($arExtensions as $sExt)
            if(strstr($sExtension,$sExt))
            {
                $sExtension = $sExt;
                return;
            }
        $sExtension = "txt";
    }
}//end controller
