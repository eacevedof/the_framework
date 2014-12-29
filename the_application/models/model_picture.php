<?php
/**
* @author Module Builder 1.0.16
* @link www.eduardoaf.com
* @version 1.0.6
* @name ModelPicture
* @file model_picture.php
* @date 05-10-2014 01:40 (SPAIN)
* @observations: 
* @requires: theapplication_model.php
*/
import_appmain("model");

class ModelPicture extends TheApplicationModel
{
    protected $_id_type; //int(4)
    protected $oType; //Model Object
    protected $_id_type_entity; //int(4)
    protected $oPictureArray; //Model Object
    protected $_id_owner_entity; //int(4)
    protected $oOwnerEntity; //Model Object
    protected $_width; //int(4)
    protected $_height; //int(4)
    protected $_resolution; //int(4)
    protected $_order_by; //int(4)
    protected $_rating; //int(4)
    protected $_show; //int(4)
    protected $_is_bydefault; //int(4)
    protected $_is_public; //int(4)
    protected $_is_file; //int(4)
    protected $_is_error; //int(4)
    protected $_id_entity; //numeric(9)
    protected $oEntity; //Model Object
    protected $_id_owner; //numeric(9)
    protected $oOwner; //Model Object
    protected $_size; //numeric(9)
    protected $_id_thumb_1; //numeric(9)
    protected $oThumb1; //Model Object
    protected $_id_thumb_2; //numeric(9)
    protected $oThumb2; //Model Object
    protected $_id_thumb_3; //numeric(9)
    protected $oThumb3; //Model Object
    protected $_id_thumb_4; //numeric(9)
    protected $oThumb4; //Model Object
    protected $_id_thumb_5; //numeric(9)
    protected $oThumb5; //Model Object
    protected $_uri_local; //varchar(250)
    protected $_uri_public; //varchar(250)
    protected $_filename; //varchar(250)
    protected $_name; //varchar(250)
    protected $_shortname; //varchar(125)
    protected $_shortname_filename; //varchar(250)
    protected $_extension; //varchar(5)
    protected $_anchor_text; //varchar(100)
    protected $_csv_tags; //varchar(200)
    protected $_source; //varchar(250)
    protected $_folder; //varchar(50)
    protected $_parent_path; //varchar(250)
    protected $_information; //varchar(250)
    protected $_information_extra; //varchar(250)
    protected $_img_title; //varchar(100)
    protected $_create_date; //char(14)
    protected $_modify_date; //char(14)
    protected $_is_repeated; //int(4)

    protected $showThumbs;
    
    public function __construct
    ($id=NULL,$id_type=NULL,$id_type_entity=NULL,$id_owner_entity=NULL,$width=NULL,$height=NULL
     ,$resolution=NULL,$order_by=NULL,$rating=NULL,$show=NULL,$is_bydefault=NULL,$is_public=NULL
     ,$is_file=NULL,$is_error=NULL,$id_entity=NULL,$id_owner=NULL,$size=NULL,$id_thumb_1=NULL
    ,$id_thumb_2=NULL,$id_thumb_3=NULL,$id_thumb_4=NULL,$id_thumb_5=NULL,$uri_local=NULL,$uri_public=NULL
    ,$filename=NULL,$name=NULL,$shortname=NULL,$extension=NULL,$anchor_text=NULL,$csv_tags=NULL
    ,$source_filename=NULL,$source=NULL,$folder=NULL,$parent_path=NULL,$information=NULL,$information_extra=NULL
    ,$img_title=NULL,$create_date=NULL,$modify_date=NULL)
    {
        parent::__construct("app_picture");
        if($id!=NULL) $this->_id = $id;
        if($id_type!=NULL) $this->_id_type = $id_type;
        if($id_type_entity!=NULL) $this->_id_type_entity = $id_type_entity;
        if($id_owner_entity!=NULL) $this->_id_owner_entity = $id_owner_entity;
        if($width!=NULL) $this->_width = $width;
        if($height!=NULL) $this->_height = $height;
        if($resolution!=NULL) $this->_resolution = $resolution;
        if($order_by!=NULL) $this->_order_by = $order_by;
        if($rating!=NULL) $this->_rating = $rating;
        if($show!=NULL) $this->_show = $show;
        if($is_bydefault!=NULL) $this->_is_bydefault = $is_bydefault;
        if($is_public!=NULL) $this->_is_public = $is_public;
        if($is_file!=NULL) $this->_is_file = $is_file;
        if($is_error!=NULL) $this->_is_error = $is_error;
        if($id_entity!=NULL) $this->_id_entity = $id_entity;
        if($id_owner!=NULL) $this->_id_owner = $id_owner;
        if($size!=NULL) $this->_size = $size;
        if($id_thumb_1!=NULL) $this->_id_thumb_1 = $id_thumb_1;
        if($id_thumb_2!=NULL) $this->_id_thumb_2 = $id_thumb_2;
        if($id_thumb_3!=NULL) $this->_id_thumb_3 = $id_thumb_3;
        if($id_thumb_4!=NULL) $this->_id_thumb_4 = $id_thumb_4;
        if($id_thumb_5!=NULL) $this->_id_thumb_5 = $id_thumb_5;
        if($uri_local!=NULL) $this->_uri_local = $uri_local;
        if($uri_public!=NULL) $this->_uri_public = $uri_public;
        if($filename!=NULL) $this->_filename = $filename;
        if($name!=NULL) $this->_name = $name;
        if($shortname!=NULL) $this->_shortname = $shortname;
        if($extension!=NULL) $this->_extension = $extension;
        if($anchor_text!=NULL) $this->_anchor_text = $anchor_text;
        if($csv_tags!=NULL) $this->_csv_tags = $csv_tags;
        if($source!=NULL) $this->_source = $source;
        if($source_filename!=NULL) $this->_source_filename = $source_filename;
        if($folder!=NULL) $this->_folder = $folder;
        if($parent_path!=NULL) $this->_parent_path = $parent_path;
        if($information!=NULL) $this->_information = $information;
        if($information_extra!=NULL) $this->_information_extra = $information_extra;
        if($img_title!=NULL) $this->_img_title = $img_title;
        if($create_date!=NULL) $this->_create_date = $create_date;
        if($modify_date!=NULL) $this->_modify_date = $modify_date;
        //$this->arDescConfig = array("id","modify_date","separator"=>" - ");
    }//__construct()

    public function insert()
    {
        $id_type = mssqlclean($this->_id_type,1);
        $id_type_entity = mssqlclean($this->_id_type_entity,1);
        $id_owner_entity = mssqlclean($this->_id_owner_entity,1);
        $width = mssqlclean($this->_width,1);
        $height = mssqlclean($this->_height,1);
        $resolution = mssqlclean($this->_resolution,1);
        $order_by = mssqlclean($this->_order_by,1);
        $rating = mssqlclean($this->_rating,1);
        $show = mssqlclean($this->_show,1);
        $is_bydefault = mssqlclean($this->_is_bydefault,1);
        $is_public = mssqlclean($this->_is_public,1);
        $is_file = mssqlclean($this->_is_file,1);
        $is_error = mssqlclean($this->_is_error,1);
        $id_entity = mssqlclean($this->_id_entity,1);
        $id_owner = mssqlclean($this->_id_owner,1);
        $size = mssqlclean($this->_size,1);
        $id_thumb_1 = mssqlclean($this->_id_thumb_1,1);
        $id_thumb_2 = mssqlclean($this->_id_thumb_2,1);
        $id_thumb_3 = mssqlclean($this->_id_thumb_3,1);
        $id_thumb_4 = mssqlclean($this->_id_thumb_4,1);
        $id_thumb_5 = mssqlclean($this->_id_thumb_5,1);
        $uri_local = mssqlclean($this->_uri_local);
        $uri_public = mssqlclean($this->_uri_public);
        $filename = mssqlclean($this->_filename);
        $name = mssqlclean($this->_name);
        $shortname = mssqlclean($this->_shortname);
        $extension = mssqlclean($this->_extension);
        $anchor_text = mssqlclean($this->_anchor_text);
        $csv_tags = mssqlclean($this->_csv_tags);
        $source = mssqlclean($this->_source);
        $source_filename = mssqlclean($this->_source_filename);
        $folder = mssqlclean($this->_folder);
        $parent_path = mssqlclean($this->_parent_path);
        $information = mssqlclean($this->_information);
        $information_extra = mssqlclean($this->_information_extra);
        $img_title = mssqlclean($this->_img_title);
        $create_date = mssqlclean($this->_create_date);
        $modify_date = mssqlclean($this->_modify_date);

        $sSQL = "INSERT INTO $this->_table_name
        (id_type,id_type_entity,id_owner_entity,width,height,resolution,order_by,rating,show,is_bydefault,is_public,is_file,is_error,id_entity,id_owner,size,id_thumb_1,id_thumb_2,id_thumb_3,id_thumb_4,id_thumb_5,uri_local,uri_public,filename,name,shortname,extension,anchor_text,csv_tags,source,folder,parent_path,information,information_extra,img_title,create_date,modify_date)
        VALUES
        ($id_type,$id_type_entity,$id_owner_entity,$width,$height,$resolution,$order_by,$rating,$show,$is_bydefault,$is_public,$is_file,$is_error,$id_entity,$id_owner,$size,$id_thumb_1,$id_thumb_2,$id_thumb_3,$id_thumb_4,$id_thumb_5,'$uri_local','$uri_public','$filename','$name','$shortname','$extension','$anchor_text','$csv_tags','$source','$folder','$parent_path','$information','$information_extra','$img_title','$create_date','$modify_date')";
        $this->execute($sSQL);
    }//insert()

    public function load_by_id()
    {
        if($this->_id)
        {
            $this->oQuery->set_comment("load_by_id()");
            $this->oQuery->set_fields($this->get_all_fields());
            $this->oQuery->set_fromtables($this->_table_name);
            $this->oQuery->set_joins();
            $this->oQuery->add_where("$this->_table_name.delete_date IS NULL");
            $this->oQuery->add_where("$this->_table_name.is_enabled=1");
            $this->oQuery->set_and("$this->_table_name.id=$this->_id");
            $sSQL = $this->oQuery->get_select();
            //bug($this->oQuery);
            $arRow = $this->query($sSQL,1);
        }
        $this->row_assign($arRow);
    }//load_by_id()

    public function get_select_all_ids()
    {
        $this->oQuery->set_comment("get_select_all_ids() overriden");
        $this->oQuery->set_fields("$this->_table_name.id");
        //si está definido $this->_select_user
        $this->oQuery->add_joins($this->build_userhierarchy_join($this->_select_user,"customer","id_customer"));
        if(!$this->showThumbs)
        {    
            $this->oQuery->add_joins
            ("
                LEFT OUTER JOIN 
                (
                    SELECT id_thumb_1 AS id FROM app_picture WHERE id_thumb_1 IS NOT NULL AND delete_date IS NULL
                    UNION
                    SELECT id_thumb_2 FROM app_picture WHERE id_thumb_2 IS NOT NULL AND delete_date IS NULL
                    UNION
                    SELECT id_thumb_3 FROM app_picture WHERE id_thumb_3 IS NOT NULL AND delete_date IS NULL                
                ) AS thumbs
                ON $this->_table_name.id = thumbs.id
             "
            );
            $this->oQuery->add_where("thumbs.id IS NULL");
        }
        $this->oQuery->add_where("$this->_table_name.delete_date IS NULL");
        $this->oQuery->add_where("$this->_table_name.is_enabled=1");
        //EXTRA AND
        $this->oQuery->add_and($this->build_sql_filters());
        //ORDERBY 
        //default orderby
        $this->oQuery->set_orderby("$this->_table_name.id DESC");
        $sOrderByAuto = $this->build_sql_orderby();
        if($sOrderByAuto) $this->oQuery->set_orderby($sOrderByAuto);
        $sSQL = $this->oQuery->get_select();
        $this->oQuery->set_fields($this->sSELECTfields);
        //bug($sSQL);
        return $this->query($sSQL);
    }//get_select_all_ids overriden

       
    //===================
    //       GETS
    //===================
    public function get_id_type(){return $this->_id_type;}
    public function get_type()
    {
        $this->oType = new ModelType($this->_id_type);
        $this->oType->load_by_id();
        return $this->oType;
    }
    public function get_id_type_entity(){return $this->_id_type_entity;}
    public function get_type_entity()
    {
        $this->oPictureArray = new ModelType($this->_id_type_entity);
        $this->oPictureArray->load_by_id();
        return $this->oPictureArray;
    }
    public function get_id_owner_entity(){return $this->_id_owner_entity;}
    public function get_owner_entity()
    {
        $this->oOwnerEntity = new ModelOwnerEntity($this->_id_owner_entity);
        $this->oOwnerEntity->load_by_id();
        return $this->oOwnerEntity;
    }
    public function get_width(){return $this->_width;}
    public function get_height(){return $this->_height;}
    public function get_resolution(){return $this->_resolution;}
    public function get_order_by(){return $this->_order_by;}
    public function get_rating(){return $this->_rating;}
    public function get_show(){return $this->_show;}
    public function get_is_bydefault(){return $this->_is_bydefault;}
    public function get_is_public(){return $this->_is_public;}
    public function get_is_file(){return $this->_is_file;}
    public function get_is_error(){return $this->_is_error;}
    public function get_id_entity(){return $this->_id_entity;}
    public function get_entity()
    {
        $this->oEntity = new ModelEntity($this->_id_entity);
        $this->oEntity->load_by_id();
        return $this->oEntity;
    }
    public function get_id_owner(){return $this->_id_owner;}
    public function get_owner()
    {
        $this->oOwner = new ModelOwner($this->_id_owner);
        $this->oOwner->load_by_id();
        return $this->oOwner;
    }
    public function get_size(){return $this->_size;}
    public function get_id_thumb_1(){return $this->_id_thumb_1;}
    public function get_thumb_1()
    {
        $this->oThumb1 = new ModelPicture($this->_id_thumb_1);
        $this->oThumb1->load_by_id();
        return $this->oThumb1;
    }
    public function get_id_thumb_2(){return $this->_id_thumb_2;}
    public function get_thumb_2()
    {
        $this->oThumb2 = new ModelPicture($this->_id_thumb_2);
        $this->oThumb2->load_by_id();
        return $this->oThumb2;
    }
    public function get_id_thumb_3(){return $this->_id_thumb_3;}
    public function get_thumb_3()
    {
        $this->oThumb3 = new ModelPicture($this->_id_thumb_3);
        $this->oThumb3->load_by_id();
        return $this->oThumb3;
    }
    public function get_id_thumb_4(){return $this->_id_thumb_4;}
    public function get_thumb_4()
    {
        $this->oThumb4 = new ModelPicture($this->_id_thumb_4);
        $this->oThumb4->load_by_id();
        return $this->oThumb4;
    }
    public function get_id_thumb_5(){return $this->_id_thumb_5;}
    public function get_thumb_5()
    {
        $this->oThumb5 = new ModelPicture($this->_id_thumb_5);
        $this->oThumb5->load_by_id();
        return $this->oThumb5;
    }
    public function get_uri_local(){return $this->_uri_local;}
    public function get_uri_public(){return $this->_uri_public;}
    public function get_filename(){return $this->_filename;}
    public function get_name(){return $this->_name;}
    public function get_shortname(){return $this->_shortname;}
    public function get_extension(){return $this->_extension;}
    public function get_anchor_text(){return $this->_anchor_text;}
    public function get_csv_tags(){return $this->_csv_tags;}
    public function get_source(){return $this->_source;}
    public function get_folder(){return $this->_folder;}
    public function get_parent_path(){return $this->_parent_path;}
    public function get_information(){return $this->_information;}
    public function get_information_extra(){return $this->_information_extra;}
    public function get_img_title(){return $this->_img_title;}
    public function get_create_date(){return $this->_create_date;}
    public function get_modify_date(){return $this->_modify_date;}
    public function get_source_filename(){return $this->_source_filename;}
    public function get_is_repeated(){return $this->_is_repeated;}
    public function load_default_by_entity()
    {
        $oQuery = new ComponentQuery($this->_table_name);
        $oQuery->set_db_type($this->get_db_type());
        $oQuery->set_comment("load_by_entity");
        $oQuery->set_top(1);
        $oQuery->set_fields("*");
        $oQuery->add_and("id_type_entity = $this->_id_type_entity");
        $oQuery->add_and("id_entity = $this->_id_entity");
        $oQuery->add_and("is_bydefault = 1");
        $sSQL = $oQuery->get_select();
        $arRow = $this->query($sSQL,1);
        //bug($arRow,$sSQL);
        $this->row_assign($arRow);
    }
    
    //===================
    //       SETS
    //===================
    public function set_id_type($value){$this->_id_type = $value;}
    public function set_type($oValue){$this->oType = $oValue;}
    public function set_id_type_entity($value){$this->_id_type_entity = $value;}
    public function set_type_entity($oValue){$this->oPictureArray = $oValue;}
    public function set_id_owner_entity($value){$this->_id_owner_entity = $value;}
    public function set_owner_entity($oValue){$this->oOwnerEntity = $oValue;}
    public function set_width($value){$this->_width = $value;}
    public function set_height($value){$this->_height = $value;}
    public function set_resolution($value){$this->_resolution = $value;}
    public function set_order_by($value){$this->_order_by = $value;}
    public function set_rating($value){$this->_rating = $value;}
    public function set_show($value){$this->_show = $value;}
    public function set_is_bydefault($value){$this->_is_bydefault = $value;}
    public function set_is_public($value){$this->_is_public = $value;}
    public function set_is_file($value){$this->_is_file = $value;}
    public function set_is_error($value){$this->_is_error = $value;}
    public function set_id_entity($value){$this->_id_entity = $value;}
    public function set_entity($oValue){$this->oEntity = $oValue;}
    public function set_id_owner($value){$this->_id_owner = $value;}
    public function set_owner($oValue){$this->oOwner = $oValue;}
    public function set_size($value){$this->_size = $value;}
    public function set_id_thumb_1($value){$this->_id_thumb_1 = $value;}
    public function set_thumb_1($oValue){$this->oThumb1 = $oValue;}
    public function set_id_thumb_2($value){$this->_id_thumb_2 = $value;}
    public function set_thumb_2($oValue){$this->oThumb2 = $oValue;}
    public function set_id_thumb_3($value){$this->_id_thumb_3 = $value;}
    public function set_thumb_3($oValue){$this->oThumb3 = $oValue;}
    public function set_id_thumb_4($value){$this->_id_thumb_4 = $value;}
    public function set_thumb_4($oValue){$this->oThumb4 = $oValue;}
    public function set_id_thumb_5($value){$this->_id_thumb_5 = $value;}
    public function set_thumb_5($oValue){$this->oThumb5 = $oValue;}
    public function set_uri_local($value){$this->_uri_local = $value;}
    public function set_uri_public($value){$this->_uri_public = $value;}
    public function set_filename($value){$this->_filename = $value;}
    public function set_name($value){$this->_name = $value;}
    public function set_shortname($value){$this->_shortname = $value;}
    public function set_extension($value){$this->_extension = $value;}
    public function set_anchor_text($value){$this->_anchor_text = $value;}
    public function set_csv_tags($value){$this->_csv_tags = $value;}
    public function set_source($value){$this->_source = $value;}
    public function set_folder($value){$this->_folder = $value;}
    public function set_parent_path($value){$this->_parent_path = $value;}
    public function set_information($value){$this->_information = $value;}
    public function set_information_extra($value){$this->_information_extra = $value;}
    public function set_img_title($value){$this->_img_title = $value;}
    public function set_create_date($value){$this->_create_date = $value;}
    public function set_modify_date($value){$this->_modify_date = $value;}
    public function set_source_filename($value){$this->_source_filename = $value;}
    public function set_is_repeated($value){$this->_is_repeated = $value;}
    public function set_show_thumbs($isOn=TRUE){$this->showThumbs = $isOn;}
}
