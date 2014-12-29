<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.3
 * @name TheApplicationHelper
 * @file theapplication_helper.php 
 * @date 20-10-2013 17:55 (SPAIN)
 * @observations: 
 */
//include_once("theframework_helper.php");
class TheApplicationHelper extends TheFrameworkHelper
{
    protected $iSpan;
    
    public function __construct()
    {
        parent::__construct();       
    }
    
    public function set_span($iSpan=12){$this->iSpan = $iSpan;}
    public function get_span(){return $this->iSpan;}
}
