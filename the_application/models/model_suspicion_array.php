<?php
/**
 * @author Module Builder 1.0.22
 * @link www.eduardoaf.com
 * @version 1.0.3
 * @name ModelSuspicionArray
 * @file model_suspicion_array.php
 * @date 27-06-2014 19:59 (SPAIN)
 * @observations: 
 * @requires: theapplication_model.php
 */
include_once("theapplication_model.php");

class ModelSuspicionArray extends TheApplicationModel
{
    protected $_order_by; //int(4)
    protected $_type; //varchar(15)
    protected $_id_tosave; //varchar(25)
    protected $oTosave; //Model Object

    protected $oLanguage;//

    public function __construct
    ($id=NULL,$order_by=NULL,$type=NULL,$id_tosave=NULL,$description=NULL)
    {
        parent::__construct("app_suspicion_array");
        if($id!=NULL) $this->_id = $id;
        if($order_by!=NULL) $this->_order_by = $order_by;
        if($type!=NULL) $this->_type = $type;
        if($id_tosave!=NULL) $this->_id_tosave = $id_tosave;
        if($description!=NULL) $this->_description = $description;
        //$this->arDescConfig = array("id","description","separator"=>" - ");
    }//__construct()

    public function load_by_id()
    {
        if($this->_id)
        {
            $this->oQuery->set_comment("load_by_id()");
            $this->oQuery->set_fields($this->get_all_fields());
            $this->oQuery->set_fromtables($this->_table_name);
            $this->oQuery->set_joins(NULL);
            $this->oQuery->add_where("$this->_table_name.delete_date IS NULL");
            $this->oQuery->add_where("$this->_table_name.is_enabled=1");
            $this->oQuery->set_and("$this->_table_name.id=$this->_id");
            $sSQL = $this->oQuery->get_select();
            //bug($this->oQuery);
            $arRow = $this->query($sSQL,1);
        }

        $this->row_assign($arRow);
    }//load_by_id()

    public function load_by_erptype()
    {
        if($this->_code_erp && $this->_type)
        {
            $this->oQuery = new ComponentQuery();
            $this->oQuery->set_comment("load_by_erptype()");
            $this->oQuery->set_fields($this->get_all_fields());
            $this->oQuery->set_fromtables($this->_table_name);
            $this->oQuery->set_joins(NULL);
            $this->oQuery->add_where("$this->_table_name.delete_date IS NULL");
            $this->oQuery->add_where("$this->_table_name.is_enabled=1");
            $this->oQuery->add_and("$this->_table_name.code_erp='$this->_code_erp'");
            $this->oQuery->add_and("$this->_table_name.type='$this->_type'");
            $sSQL = $this->oQuery->get_select();
            //bug($this->oQuery);
            $arRow = $this->query($sSQL,1);
        }

        $this->row_assign($arRow);
    }//load_by_id()        

    public function get_by_head1()
    {
        //bug($this->oDB,"byhead1");
        $this->oQuery = new ComponentQuery();
        $this->oQuery->set_comment("get_by_head1()");
        $this->oQuery->add_fields("$this->_table_name.id AS id");
        $this->oQuery->add_fields("$this->_table_name.id_tosave AS code");
        $this->oQuery->add_fields("CONVERT(TEXT,$this->_table_name.description) AS description");        
        $this->oQuery->set_fromtables($this->_table_name);
        $this->oQuery->add_where("$this->_table_name.delete_date IS NULL");
        $this->oQuery->add_where("$this->_table_name.is_enabled=1");
        $this->oQuery->add_and("$this->_table_name.type='head1'");
        $this->oQuery->set_orderby("$this->_table_name.order_by ASC");
        $this->oQuery->add_orderby("$this->_table_name.description ASC");
        
        if($this->isLanguage && $this->_id_language)
        {
            $this->oQuery->set_fields("$this->_table_name.id AS id");
            $this->oQuery->add_fields("$this->_table_name.id_tosave AS code");            
            $this->oQuery->add_fields("CONVERT(TEXT,lang.description) AS description");
            $this->oQuery->add_joins("LEFT JOIN $this->_table_name_lang AS lang 
                                ON $this->_table_name.id = lang.id_source 
                                AND lang.id_language=$this->_id_language");
            $this->oQuery->set_orderby("$this->_table_name.order_by ASC");
            $this->oQuery->add_orderby("lang.description ASC");            
        }        
        $sSQL = $this->oQuery->get_select();
        //bug($this->oQuery);
        $arRows = $this->query($sSQL);
        return $arRows;
    }//get_by_head1

    public function get_by_head2()
    {
        $this->oQuery = new ComponentQuery();
        $this->oQuery->set_comment("get_by_head2()");
        $this->oQuery->add_fields("$this->_table_name.id AS id");
        $this->oQuery->add_fields("$this->_table_name.id_tosave AS code");
        $this->oQuery->add_fields("CONVERT(TEXT,$this->_table_name.description) AS description");
        $this->oQuery->set_fromtables($this->_table_name);
        $this->oQuery->add_where("$this->_table_name.delete_date IS NULL");
        $this->oQuery->add_where("$this->_table_name.is_enabled=1");
        $this->oQuery->add_and("$this->_table_name.type='head2'");
        $this->oQuery->add_orderby("$this->_table_name.order_by ASC");
        $this->oQuery->add_orderby("$this->_table_name.description ASC");
        if($this->isLanguage && $this->_id_language)
        {
            $this->oQuery->set_fields("$this->_table_name.id AS id");
            $this->oQuery->add_fields("$this->_table_name.id_tosave AS code");            
            $this->oQuery->add_fields("CONVERT(TEXT,lang.description) AS description");
            $this->oQuery->add_joins("LEFT JOIN $this->_table_name_lang AS lang 
                                ON $this->_table_name.id = lang.id_source 
                                AND lang.id_language=$this->_id_language");
            $this->oQuery->set_orderby("$this->_table_name.order_by ASC");
            $this->oQuery->add_orderby("lang.description ASC");            
        }            
        $sSQL = $this->oQuery->get_select();
        //bug($this->oQuery);
        $arRows = $this->query($sSQL);
        return $arRows;
    }//get_by_head2

    public function get_by_head3()
    {
        $this->oQuery = new ComponentQuery();
        $this->oQuery->set_comment("get_by_head3()");
        //$this->oQuery->set_distinct();
        $this->oQuery->add_fields("$this->_table_name.id AS id");
        //$this->oQuery->add_fields("$this->_table_name.id_tosave AS code");
        $this->oQuery->add_fields("CONVERT(TEXT,$this->_table_name.description) AS description");
        $this->oQuery->set_fromtables($this->_table_name);
        $this->oQuery->add_where("$this->_table_name.delete_date IS NULL");
        $this->oQuery->add_where("$this->_table_name.is_enabled=1");
        $this->oQuery->add_and("$this->_table_name.type='head3'");
        $this->oQuery->add_orderby("$this->_table_name.order_by ASC");
        $this->oQuery->add_orderby("$this->_table_name.description ASC");
        //No se traduce son ordinales 1º,2º...etc
//        if($this->isLanguage && $this->_id_language)
//        {
//            $this->oQuery->set_fields("$this->_table_name.id AS id");
//            //$this->oQuery->add_fields("$this->_table_name.id_tosave AS code");        
//            $this->oQuery->add_fields("CONVERT(TEXT,lang.description) AS description");
//            $this->oQuery->add_joins("LEFT JOIN $this->_table_name_lang AS lang 
//                                ON $this->_table_name.id = lang.id_source 
//                                AND lang.id_language=$this->_id_language");
//            $this->oQuery->set_orderby("$this->_table_name.order_by ASC");
//            $this->oQuery->add_orderby("lang.description ASC");            
//        }           
        $sSQL = $this->oQuery->get_select();
        //bug($this->oQuery);
        $arRows = $this->query($sSQL);

        foreach($arRows as $arRow)
            $arPicklist[$arRow["id"]] = $arRow["description"];

        return $arPicklist;
    }//get_by_head3

    public function get_by_head4()
    {        
        $this->oQuery = new ComponentQuery();
        $this->oQuery->set_comment("get_by_head4()");
        //$this->oQuery->set_distinct();
        $this->oQuery->add_fields("$this->_table_name.id AS id");
        //$this->oQuery->add_fields("$this->_table_name.id_tosave AS code");
        $this->oQuery->add_fields("CONVERT(TEXT,$this->_table_name.description) AS description");
        $this->oQuery->set_fromtables($this->_table_name);
        $this->oQuery->add_where("$this->_table_name.delete_date IS NULL");
        $this->oQuery->add_where("$this->_table_name.is_enabled=1");
        $this->oQuery->add_and("$this->_table_name.type='head4'");
        $this->oQuery->add_orderby("$this->_table_name.order_by ASC");
        $this->oQuery->add_orderby("$this->_table_name.description ASC");
        //No se traduce son ordinales 1º,2º...etc        
//        if($this->isLanguage && $this->_id_language)
//        {
//            $this->oQuery->set_fields("$this->_table_name.id AS id");
//            //$this->oQuery->add_fields("$this->_table_name.id_tosave AS code");       
//            $this->oQuery->add_fields("CONVERT(TEXT,lang.description) AS description");
//            $this->oQuery->add_joins("LEFT JOIN $this->_table_name_lang AS lang 
//                                ON $this->_table_name.id = lang.id_source 
//                                AND lang.id_language=$this->_id_language");
//            $this->oQuery->set_orderby("$this->_table_name.order_by ASC");
//            $this->oQuery->add_orderby("lang.description ASC");            
//        }           
        $sSQL = $this->oQuery->get_select();
        //bug($this->oQuery);
        $arRows = $this->query($sSQL);

        foreach($arRows as $arRow)
            $arPicklist[$arRow["id"]] = $arRow["description"];

        return $arPicklist;
    }//get_by_head4 

    public function get_by_head5()
    {        
        $this->oQuery = new ComponentQuery();
        $this->oQuery->set_comment("get_by_head5()");
        //$this->oQuery->set_distinct();
        $this->oQuery->add_fields("$this->_table_name.id AS id");
        $this->oQuery->add_fields("CONVERT(TEXT,$this->_table_name.description) AS description");
        $this->oQuery->set_fromtables($this->_table_name);
        $this->oQuery->add_where("$this->_table_name.delete_date IS NULL");
        $this->oQuery->add_where("$this->_table_name.is_enabled=1");
        $this->oQuery->add_and("$this->_table_name.type='head5'");
        $this->oQuery->add_orderby("$this->_table_name.order_by ASC");
        $this->oQuery->add_orderby("$this->_table_name.description ASC");
        //No se traduce. Se muestra en holandes
//        if($this->isLanguage && $this->_id_language)
//        {
//            $this->oQuery->set_fields("$this->_table_name.id AS id");           
//            $this->oQuery->add_fields("CONVERT(TEXT,lang.description) AS description");
//            $this->oQuery->add_joins("LEFT JOIN $this->_table_name_lang AS lang 
//                                ON $this->_table_name.id = lang.id_source 
//                                AND lang.id_language=$this->_id_language");
//            $this->oQuery->set_orderby("$this->_table_name.order_by ASC");
//            $this->oQuery->add_orderby("lang.description ASC");            
//        }           
        $sSQL = $this->oQuery->get_select();
        //bug($this->oQuery);
        $arRows = $this->query($sSQL);

        foreach($arRows as $arRow)
            $arPicklist[$arRow["id"]] = $arRow["description"];

        return $arPicklist;
    }//get_by_head5

    public function get_by_involved1()
    {          
        $this->oQuery = new ComponentQuery();
        $this->oQuery->set_comment("get_by_involved1()");
        //$this->oQuery->set_distinct();
        $this->oQuery->add_fields("$this->_table_name.id AS id");
        //$this->oQuery->add_fields("$this->_table_name.id_tosave AS code");
        $this->oQuery->add_fields("CONVERT(TEXT,$this->_table_name.description) AS description");
        $this->oQuery->set_fromtables($this->_table_name);
        $this->oQuery->add_where("$this->_table_name.delete_date IS NULL");
        $this->oQuery->add_where("$this->_table_name.is_enabled=1");
        $this->oQuery->add_and("$this->_table_name.type='involved1'");
        $this->oQuery->add_orderby("$this->_table_name.order_by ASC");
        $this->oQuery->add_orderby("$this->_table_name.description ASC");
        if($this->isLanguage && $this->_id_language)
        {
            $this->oQuery->set_fields("$this->_table_name.id AS id");
            //$this->oQuery->add_fields("$this->_table_name.id_tosave AS code");            
            $this->oQuery->add_fields("CONVERT(TEXT,lang.description) AS description");
            $this->oQuery->add_joins("LEFT JOIN $this->_table_name_lang AS lang 
                                ON $this->_table_name.id = lang.id_source 
                                AND lang.id_language=$this->_id_language");
            $this->oQuery->set_orderby("$this->_table_name.order_by ASC");
            $this->oQuery->add_orderby("lang.description ASC");            
        }           
        $sSQL = $this->oQuery->get_select();
        //bug($this->oQuery);
        $arRows = $this->query($sSQL);

        foreach($arRows as $arRow)
            $arPicklist[$arRow["id"]] = $arRow["description"];

        return $arPicklist;
    }//get_by_involved1

    public function get_by_doctype()
    {
        $this->oQuery = new ComponentQuery();
        $this->oQuery->set_comment("get_by_doctype()");
        //$this->oQuery->set_distinct();
        //$this->oQuery->add_fields("$this->_table_name.id AS id");
        $this->oQuery->add_fields("$this->_table_name.id AS id");
        $this->oQuery->add_fields("CONVERT(TEXT,$this->_table_name.description) AS description");
        $this->oQuery->set_fromtables($this->_table_name);
        $this->oQuery->add_where("$this->_table_name.delete_date IS NULL");
        $this->oQuery->add_where("$this->_table_name.is_enabled=1");
        $this->oQuery->add_and("$this->_table_name.type='iddocument'");
        $this->oQuery->add_orderby("$this->_table_name.order_by ASC");
        $this->oQuery->add_orderby("$this->_table_name.description ASC");
        if($this->isLanguage && $this->_id_language)
        {
            $this->oQuery->set_fields("$this->_table_name.id AS id");         
            $this->oQuery->add_fields("CONVERT(TEXT,lang.description) AS description");
            $this->oQuery->add_joins("LEFT JOIN $this->_table_name_lang AS lang 
                                ON $this->_table_name.id = lang.id_source 
                                AND lang.id_language=$this->_id_language");
            $this->oQuery->set_orderby("$this->_table_name.order_by ASC");
            $this->oQuery->add_orderby("lang.description ASC");            
        }           
        $sSQL = $this->oQuery->get_select();
        //bug($this->oQuery);
        $arRows = $this->query($sSQL);

        $arPicklist = array(""=>tr_none);
        foreach($arRows as $arRow)
            $arPicklist[$arRow["id"]] = $arRow["description"];

        return $arPicklist;
    }//get_by_doctype

    public function get_by_sex()
    {
        $this->oQuery = new ComponentQuery();
        $this->oQuery->set_comment("get_by_sex()");
        //$this->oQuery->set_distinct();
        //$this->oQuery->add_fields("$this->_table_name.id AS id");
        $this->oQuery->add_fields("$this->_table_name.id AS id");
        $this->oQuery->add_fields("CONVERT(TEXT,$this->_table_name.description) AS description");
        $this->oQuery->set_fromtables($this->_table_name);
        $this->oQuery->add_where("$this->_table_name.delete_date IS NULL");
        $this->oQuery->add_where("$this->_table_name.is_enabled=1");
        $this->oQuery->add_and("$this->_table_name.type='sex'");
        $this->oQuery->add_orderby("$this->_table_name.order_by ASC");
        $this->oQuery->add_orderby("$this->_table_name.description ASC");
        if($this->isLanguage && $this->_id_language)
        {
            $this->oQuery->set_fields("$this->_table_name.id AS id");         
            $this->oQuery->add_fields("CONVERT(TEXT,lang.description) AS description");
            $this->oQuery->add_joins("LEFT JOIN $this->_table_name_lang AS lang 
                                ON $this->_table_name.id = lang.id_source 
                                AND lang.id_language=$this->_id_language");
            $this->oQuery->set_orderby("$this->_table_name.order_by ASC");
            $this->oQuery->add_orderby("lang.description ASC");            
        }           
        $sSQL = $this->oQuery->get_select();
        //bug($this->oQuery);
        $arRows = $this->query($sSQL);

        $arPicklist = array(""=>tr_none);
        foreach($arRows as $arRow)
            $arPicklist[$arRow["id"]] = $arRow["description"];

        return $arPicklist;            
    }//get_by_sex()

    public function get_by_profession()
    {
        $this->oQuery = new ComponentQuery();
        $this->oQuery->set_comment("get_by_profession()");
        //$this->oQuery->set_distinct();
        //$this->oQuery->add_fields("$this->_table_name.id AS id");
        $this->oQuery->add_fields("$this->_table_name.id AS id");
        $this->oQuery->add_fields("CONVERT(TEXT,$this->_table_name.description) AS description");
        $this->oQuery->set_fromtables($this->_table_name);
        $this->oQuery->add_where("$this->_table_name.delete_date IS NULL");
        $this->oQuery->add_where("$this->_table_name.is_enabled=1");
        $this->oQuery->add_and("$this->_table_name.type='profession'");
        $this->oQuery->add_orderby("$this->_table_name.order_by ASC");
        $this->oQuery->add_orderby("$this->_table_name.description ASC");
        if($this->isLanguage && $this->_id_language)
        {
            $this->oQuery->set_fields("$this->_table_name.id AS id");           
            $this->oQuery->add_fields("CONVERT(TEXT,lang.description) AS description");
            $this->oQuery->add_joins("LEFT JOIN $this->_table_name_lang AS lang 
                                ON $this->_table_name.id = lang.id_source 
                                AND lang.id_language=$this->_id_language");
            $this->oQuery->set_orderby("$this->_table_name.order_by ASC");
            $this->oQuery->add_orderby("lang.description ASC");            
        }        
        $sSQL = $this->oQuery->get_select();
        //bug($this->oQuery);
        $arRows = $this->query($sSQL);

        $arPicklist = array(""=>tr_none);
        foreach($arRows as $arRow)
            $arPicklist[$arRow["id"]] = $arRow["description"];

        return $arPicklist;            
    }//get_by_profession()

    public function get_by_phone()
    {
        $this->oQuery = new ComponentQuery();
        $this->oQuery->set_comment("get_by_phone()");
        //$this->oQuery->set_distinct();
        //$this->oQuery->add_fields("$this->_table_name.id AS id");
        $this->oQuery->add_fields("$this->_table_name.id AS id");
        $this->oQuery->add_fields("CONVERT(TEXT,$this->_table_name.description) AS description");
        $this->oQuery->set_fromtables($this->_table_name);
        $this->oQuery->add_where("$this->_table_name.delete_date IS NULL");
        $this->oQuery->add_where("$this->_table_name.is_enabled=1");
        $this->oQuery->add_and("$this->_table_name.type='phone'");
        $this->oQuery->add_orderby("$this->_table_name.order_by ASC");
        $this->oQuery->add_orderby("$this->_table_name.description ASC");
        if($this->isLanguage && $this->_id_language)
        {
            $this->oQuery->set_fields("$this->_table_name.id AS id");        
            $this->oQuery->add_fields("CONVERT(TEXT,lang.description) AS description");
            $this->oQuery->add_joins("LEFT JOIN $this->_table_name_lang AS lang 
                                ON $this->_table_name.id = lang.id_source 
                                AND lang.id_language=$this->_id_language");
            $this->oQuery->set_orderby("$this->_table_name.order_by ASC");
            $this->oQuery->add_orderby("lang.description ASC");            
        }          
        $sSQL = $this->oQuery->get_select();
        //bug($this->oQuery);
        $arRows = $this->query($sSQL);

        $arPicklist = array(""=>tr_none);
        foreach($arRows as $arRow)
            $arPicklist[$arRow["id"]] = $arRow["description"];

        return $arPicklist;            
    }//get_by_phone()

    //public function get_select_all_ids()
    //{
        //$this->oQuery->set_comment("get_select_all_ids() overriden");
        //$this->oQuery->set_fields("$this->_table_name.id");
        ////si está definido $this->_select_user
        //$this->oQuery->add_joins($this->build_userhierarchy_join($this->_select_user,"customer","id_customer"));
        //$this->oQuery->add_where("$this->_table_name.delete_date IS NULL");
        //$this->oQuery->add_where("$this->_table_name.is_enabled=1");
        ////EXTRA AND
        //$this->oQuery->add_and($this->build_sql_filters());
        ////ORDERBY 
        ////default orderby
        //$this->oQuery->set_orderby("$this->_table_name.id DESC");
        //$sOrderByAuto = $this->build_sql_orderby();
        //if($sOrderByAuto) $this->oQuery->set_orderby($sOrderByAuto);
        //$sSQL = $this->oQuery->get_select();
        //$this->oQuery->set_fields($this->sSELECTfields);
        ////bug($sSQL);
        //return $this->query($sSQL);
    //}//get_select_all_ids overriden

    //===================
    //       GETS
    //===================
    public function get_order_by(){return $this->_order_by;}
    public function get_type(){return $this->_type;}
    public function get_id_tosave(){return $this->_id_tosave;}
    public function get_tosave()
    {
        //$this->oTosave = new ModelTosave($this->_id_tosave);
        //$this->oTosave->load_by_id();
        return $this->oTosave;
    }
    public function get_translated_by_id()
    {
        if($this->oLanguage)
        {
            $this->oLanguage->set_id_source($this->_id);
            $this->oLanguage->load_by_src_and_lang();
        }
        if(class_exists("ModelSuspicionArrayLang"))
        {
            $this->oLanguage = new ModelSuspicionArrayLang($this->oSession->get_user_id_language());
            $this->oLanguage->set_id_source($this->_id);
            $this->oLanguage->load_by_src_and_lang();
        }
        //No hay clase
        else 
        {
            $sMessage = "ModelSuspicionArray.get_translated():Class ModelSuspiconArrayLang does not exist!";
            $this->add_error($sMessage);
        }
        return $this->oLanguage;
    }//get_translated_by_id

    //===================
    //       SETS
    //===================
    public function set_order_by($value){$this->_order_by = $value;}
    public function set_type($value){$this->_type = $value;}
    public function set_id_tosave($value){$this->_id_tosave = $value;}
    public function set_tosave($oValue){$this->oTosave = $oValue;}
    public function set_obj_language(ModelSuspicionArrayLang $oLanguage){$this->oLanguage = $oLanguage;}
}
