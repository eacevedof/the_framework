<?php
/**
 * 
 */
class HelperTopForm extends HelperForm
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