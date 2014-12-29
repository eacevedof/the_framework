<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.1
 * @name ApphelperActionsbar
 * @file apphelper_actionsbar 
 * @date 20-10-2013 18:08 (SPAIN)
 * @observations: ActionsBar
 */
include_once("helper_ul.php");
include_once("helper_ul_li.php");
include_once("helper_anchor.php");
include_once("helper_button_basic.php");
class ApphelperActionsbar extends TheApplicationHelper
{
    private $_h2;
    private $arButtons;
    private $sFormId;
    private $isDefault;
    //private $isSave;
    
    public function __construct($sFormId="") 
    {
        $this->sFormId = $sFormId;
        $this->iSpan = 12;
        $this->arButtons = array
        (
            "save"=>array("action"=>"submit('$sFormId');"),
            "list"=>array("action"=>"submit('$sFormId');"),
            "reset"=>array("action"=>"submit('$sFormId');")
        );
    }
    
    public function get_html()
    {
        $sHtml = "<div class=\"row\"><article class=\"";
        if($this->iSpan) $sHtml .= "span$this->iSpan ";
        $sHtml .= "data-block\"><div class=\"data-container\"><header>\n";
        $sHtml = "<header>\n";
        if($this->_h2) $sHtml .= "<h2>$this->_h2</h2>\n";
        
        $arObjLi = array();
        $oUl = new HelperUl("Actions");
        $oUl->add_class("data-header-actions");
        
        foreach($this->arButtons as $sType=>$arButton)
        {
            $oAnchor = new HelperAnchor();
            $oAnchor->set_href("javascript:alert('me');");
            $oAnchor->set_innerhtml("search");
            $oAnchor->add_class("btn btn-alt btn-inverse");
            $oLi = new HelperUlLi(null,$oAnchor->get_html());
            $arObjLi[] = $oLi;
        }
        $oUl->set_array_li($arObjLi);
        
        $sHtml .= $oUl->get_html();
        $sHtml .= "</header>\n";
        //$sHtml .= "</header></div></article></div>\n";
        return $sHtml;
    }
    
    public function set_h2($value){$this->_h2=$value;}
}
