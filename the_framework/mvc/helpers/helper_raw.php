<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.0
 * @name HelperRaw
 * @date 11-04-2013 14:38
 * @file helper_raw.php
 * @requires
 */
class HelperRaw extends TheFrameworkHelper
{
    
    public function __construct($sRawHtml="")
    {
        $this->_inner_html = $sRawHtml;
    }
    
    //Raw
    public function get_html()
    {  
        //Agrega a inner_html los valores obtenidos con get_html
        $this->load_inner_objects();
        $sHtmlToReturn .= $this->_inner_html;
        return $sHtmlToReturn;
    }
    
    //Escondo este metodo
    public function set_rawhtml($sRawHtml,$asEntity=0){parent::set_innerhtml($sRawHtml,$asEntity);}
    
    //**********************************
    //             SETS
    //**********************************
    
    //**********************************
    //             GETS
    //**********************************
    
    //**********************************
    //           MAKE PUBLIC
    //**********************************
}