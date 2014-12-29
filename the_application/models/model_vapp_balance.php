
<?php
/**
 * @author Module Builder 1.1.1
 * @link www.eduardoaf.com
 * @version 1.0.0
 * @name ModelVappBalance
 * @file model_vapp_balance.php
 * @date 07-08-2014 16:09 (SPAIN)
 * @observations: 
 * @requires: theapplication_model.php
 */
import_appmain("model");

class ModelVappBalance extends TheApplicationModel
{
    protected $_in_total; //numeric(17)
    protected $_out_total; //numeric(17)
    protected $_total; //numeric(17)
    protected $_in_date; //varchar(8)

    protected $sStartDate;
    protected $sEndDate;    

    public function __construct
    ($in_date=NULL,$in_total=NULL,$out_total=NULL,$total=NULL)
    {
        parent::__construct("vapp_balance");
        $this->set_pk_fieldnames(array("in_date"));
        //if($id!=NULL) $this->_id = $id;
        if($in_total!=NULL) $this->_in_total = $in_total;
        if($out_total!=NULL) $this->_out_total = $out_total;
        if($total!=NULL) $this->_total = $total;
        if($in_date!=NULL) $this->_in_date = $in_date;
        //$this->arDescConfig = array("id","in_date","separator"=>" - ");
    }//__construct()

    public function get_select_all_ids()
    {
        $this->oQuery->set_comment("get_select_all_ids() overriden");
        $this->oQuery->set_fields("$this->_table_name.in_date");
        //EXTRA AND
        $this->oQuery->add_and($this->build_sql_filters());
        if($this->sStartDate)
            $this->oQuery->add_and("$this->_table_name.in_date>='$this->sStartDate'");
        if($this->sEndDate)
            $this->oQuery->add_and("$this->_table_name.in_date<='$this->sEndDate'");        
        //ORDERBY 
        //default orderby
        $this->oQuery->set_orderby("$this->_table_name.in_date DESC");
        $sOrderByAuto = $this->build_sql_orderby();
        if($sOrderByAuto) $this->oQuery->set_orderby($sOrderByAuto);
        $sSQL = $this->oQuery->get_select();
        $this->oQuery->set_fields($this->sSELECTfields);
        return $this->query($sSQL);
    }//get_select_all_ids overriden
                 
    //===================
    //       GETS
    //===================
    public function get_in_total(){return $this->_in_total;}
    public function get_out_total(){return $this->_out_total;}
    public function get_total(){return $this->_total;}
    public function get_in_date(){return $this->_in_date;}
    //===================
    //       SETS
    //===================
    public function set_in_total($value){$this->_in_total = $value;}
    public function set_out_total($value){$this->_out_total = $value;}
    public function set_total($value){$this->_total = $value;}
    public function set_in_date($value){$this->_in_date = $value;}
    
    public function set_date_start($value){$this->sStartDate = bodb_date($value);}
    public function set_date_end($value){$this->sEndDate = bodb_date($value);}        
}
