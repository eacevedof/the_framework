<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.0
 * @name ApphelperTableBasic 
 * @date 17-03-2013 12:12 (SPAIN)
 * @observations: Tabla básica
 */
include_once("helper_table.php");
include_once("helper_table_td.php");
include_once("helper_table_tr.php");
include_once("helper_table_tr.php");
class ApphelperTableBasic extends HelperTable
{
    private $oInputText;
    public function __construct() 
    {
        $this->_id = "TopSearch";
        $this->_idprefix = "frm";
        $this->_inner_html = "";
        $this->oInputText = new HelperInputText("txt$this->_id","txt$this->_id");
        //$this->
        //parent::__construct($id,$name,$method,$innertext,$action,$class,$style,$extras,$enctype,$onsubmit);
    }
}