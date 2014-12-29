<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.0
 * @name ApphelperFormactions 
 * @date 20-04-2013 18:56 (SPAIN)
 * @observations: Action Wrapper de Uraga theme
 */
class ApphelperFormactions extends TheFrameworkHelper
{
    
    public function __construct($arObjButtons) 
    {
        //bug($arObjButtons);
        $this->arInnerObjects = $arObjButtons;
    }
    
    public function get_html()
    {
        $sHtmlToReturn = "<div class=\"form-actions\" style=\"\">\n";
        $this->load_inner_objects();
        if($this->_inner_html) $sHtmlToReturn .= $this->_inner_html;
        $sHtmlToReturn .= "</div>\n";
        return $sHtmlToReturn;
    }
            
}