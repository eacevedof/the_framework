<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.0
 * @name CustomActionsbar 
 * @date 30-03-2013 10:15 (SPAIN)
 * @observations: Customed operations
 */
include_once("helper_anchor.php");
class CustomActionsbar extends TheFrameworkHelper
{
    private $arButtons;
    private $isJustButtons;
    
    public function __construct($arButtons){$this->arButtons = $arButtons;}
    
    public function get_html()
    {
        $sHtml = "";
        if(!$this->isJustButtons) $sHtml .= "<div class=\"btn-group\" helper=\"customactionsbar\">\n";

        $oAnchor = new HelperAnchor();
        $oAnchor->add_class("btn");
        
        foreach($this->arButtons as $arButton)
        {
            $sInnertext = "";
            if($arButton["icon"]) $sInnertext .= "<span class=\"".$arButton["icon"]."\"></span> ";
            $sInnertext .= $arButton["innertext"];
            $oAnchor->set_innerhtml($sInnertext);   
            $oAnchor->set_href($arButton["href"]);
            $sHtml .= $oAnchor->get_html();
        }
        
        if(!$this->isJustButtons) $sHtml .= "</div>\n";
        return $sHtml;
    }
    
    public function just_buttons($isOn=true){$this->isJustButtons=$isOn;}
}
