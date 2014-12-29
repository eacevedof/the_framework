<?php
/**
* @author Module Builder 1.0.22
* @link www.eduardoaf.com
* @version 1.0.0
* @name ModelProjectTask
* @file model_project_task.php
* @date 10-12-2013 22:06 (SPAIN)
* @observations: 
* @requires: theapplication_model.php
*/
include_once("theapplication_model.php");

class ModelProjectTask extends TheApplicationModel
{
    protected $_id_type_priority; //int(4)
    protected $oProjectTaskArray; //Model Object
    protected $_id_type_status; //int(4)
    protected $_id_type_percent; //int(4)
    protected $_id_project; //numeric(9)
    protected $oProject; //Model Object
    protected $_id_user_to; //numeric(9)
    protected $oUserTo; //Model Object
    protected $_id_user_by; //numeric(9)
    protected $oUserBy; //Model Object
    protected $_hours; //numeric(5)
    protected $_date_open; //varchar(8)
    protected $_date_close; //varchar(8)
    protected $_notes_detail; //varchar(3000)
    protected $_notes_to; //varchar(250)

    public function __construct
    ($id=NULL,$id_type_priority=NULL,$id_type_status=NULL,$id_type_percent=NULL,$id_project=NULL,$id_user_to=NULL,$id_user_by=NULL,$hours=NULL,$date_open=NULL,$date_close=NULL,$notes_detail=NULL,$notes_to=NULL)
    {
        parent::__construct("app_project_task");
        if($id!=NULL) $this->_id = $id;
        if($id_type_priority!=NULL) $this->_id_type_priority = $id_type_priority;
        if($id_type_status!=NULL) $this->_id_type_status = $id_type_status;
        if($id_type_percent!=NULL) $this->_id_type_percent = $id_type_percent;
        if($id_project!=NULL) $this->_id_project = $id_project;
        if($id_user_to!=NULL) $this->_id_user_to = $id_user_to;
        if($id_user_by!=NULL) $this->_id_user_by = $id_user_by;
        if($hours!=NULL) $this->_hours = $hours;
        if($date_open!=NULL) $this->_date_open = $date_open;
        if($date_close!=NULL) $this->_date_close = $date_close;
        if($notes_detail!=NULL) $this->_notes_detail = $notes_detail;
        if($notes_to!=NULL) $this->_notes_to = $notes_to;
        //$this->arDescConfig = array("id","notes_to","separator"=>" - ");
    }//__construct()

    public function insert()
    {
        $id_type_priority = mssqlclean($this->_id_type_priority,1);
        $id_type_status = mssqlclean($this->_id_type_status,1);
        $id_type_percent = mssqlclean($this->_id_type_percent,1);
        $id_project = mssqlclean($this->_id_project,1);
        $id_user_to = mssqlclean($this->_id_user_to,1);
        $id_user_by = mssqlclean($this->_id_user_by,1);
        $hours = mssqlclean($this->_hours,1);
        $date_open = mssqlclean($this->_date_open);
        $date_close = mssqlclean($this->_date_close);
        $notes_detail = mssqlclean($this->_notes_detail);
        $notes_to = mssqlclean($this->_notes_to);

        $sSQL = "INSERT INTO $this->_table_name
        (id_type_priority,id_type_status,id_type_percent,id_project,id_user_to,id_user_by,hours,date_open,date_close,notes_detail,notes_to)
        VALUES
        ($id_type_priority,$id_type_status,$id_type_percent,$id_project,$id_user_to,$id_user_by,$hours,'$date_open','$date_close','$notes_detail','$notes_to')";
        $this->execute($sSQL);
    }//insert()

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

        $this->_id_type_priority = $arRow["id_type_priority"];
        $this->_id_type_status = $arRow["id_type_status"];
        $this->_id_type_percent = $arRow["id_type_percent"];
        $this->_id_project = $arRow["id_project"];
        $this->_id_user_to = $arRow["id_user_to"];
        $this->_id_user_by = $arRow["id_user_by"];
        $this->_hours = $arRow["hours"];
        $this->_date_open = $arRow["date_open"];
        $this->_date_close = $arRow["date_close"];
        $this->_notes_detail = $arRow["notes_detail"];
        $this->_notes_to = $arRow["notes_to"];
        //BASE FIELDS
        $this->_id = $arRow["id"];
        $this->_insert_platform = $arRow["insert_platform"];
        $this->_insert_user = $arRow["insert_user"];
        $this->_insert_date = $arRow["insert_date"];
        $this->_update_platform = $arRow["update_platform"];
        $this->_update_user = $arRow["update_user"];
        $this->_update_date = $arRow["update_date"];
        $this->_code_erp = $arRow["code_erp"];
        $this->_description = $arRow["description"];
        $this->_delete_platform = $arRow["delete_platform"];
        $this->_delete_date = $arRow["delete_date"];
        $this->_delete_user = $arRow["delete_user"];
        $this->_is_enabled = $arRow["is_enabled"];
        $this->_is_erpsent = $arRow["is_erpsent"];
        $this->_processflag = $arRow["processflag"];
    }//load_by_id()

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
    public function get_id_type_priority(){return $this->_id_type_priority;}
    public function get_type_priority()
    {
        $this->oProjectTaskArray = new Model($this->_id_type_priority);
        $this->oProjectTaskArray->load_by_id();
        return $this->oProjectTaskArray;
    }
    public function get_id_type_status(){return $this->_id_type_status;}
    public function get_type_status()
    {
        $this->oProjectTaskArray = new Model($this->_id_type_status);
        $this->oProjectTaskArray->load_by_id();
        return $this->oProjectTaskArray;
    }
    public function get_id_type_percent(){return $this->_id_type_percent;}
    public function get_type_percent()
    {
        $this->oProjectTaskArray = new Model($this->_id_type_percent);
        $this->oProjectTaskArray->load_by_id();
        return $this->oProjectTaskArray;
    }
    public function get_id_project(){return $this->_id_project;}
    public function get_project()
    {
        $this->oProject = new ModelProject($this->_id_project);
        $this->oProject->load_by_id();
        return $this->oProject;
    }
    public function get_id_user_to(){return $this->_id_user_to;}
    public function get_user_to()
    {
        $this->oUserTo = new ModelUserTo($this->_id_user_to);
        $this->oUserTo->load_by_id();
        return $this->oUserTo;
    }
    public function get_id_user_by(){return $this->_id_user_by;}
    public function get_user_by()
    {
        $this->oUserBy = new ModelUserBy($this->_id_user_by);
        $this->oUserBy->load_by_id();
        return $this->oUserBy;
    }
    public function get_hours(){return $this->_hours;}
    public function get_date_open(){return $this->_date_open;}
    public function get_date_close(){return $this->_date_close;}
    public function get_notes_detail(){return $this->_notes_detail;}
    public function get_notes_to(){return $this->_notes_to;}
    //===================
    //       SETS
    //===================
    public function set_id_type_priority($value){$this->_id_type_priority = $value;}
    public function set_type_priority($oValue){$this->oProjectTaskArray = $oValue;}
    public function set_id_type_status($value){$this->_id_type_status = $value;}
    public function set_type_status($oValue){$this->oProjectTaskArray = $oValue;}
    public function set_id_type_percent($value){$this->_id_type_percent = $value;}
    public function set_type_percent($oValue){$this->oProjectTaskArray = $oValue;}
    public function set_id_project($value){$this->_id_project = $value;}
    public function set_project($oValue){$this->oProject = $oValue;}
    public function set_id_user_to($value){$this->_id_user_to = $value;}
    public function set_user_to($oValue){$this->oUserTo = $oValue;}
    public function set_id_user_by($value){$this->_id_user_by = $value;}
    public function set_user_by($oValue){$this->oUserBy = $oValue;}
    public function set_hours($value){$this->_hours = $value;}
    public function set_date_open($value){$this->_date_open = $value;}
    public function set_date_close($value){$this->_date_close = $value;}
    public function set_notes_detail($value){$this->_notes_detail = $value;}
    public function set_notes_to($value){$this->_notes_to = $value;}
}
