<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.0
 * @name AppHelperAnchorUp
 * @file apphelper_alertdiv.php 
 * @date 06-05-2013 18:11 (SPAIN)
 * @observations: 
 *      Crea anclas dentro de la misma página
 */
include_once("helper_anchor.php");
class AppHelperAnchorUpDown extends TheApplicationHelper
{
    private $sType;//topup,todown
    private $sIdTargetElement;
    
    /**
     * @param string $sInnerHtml 
     * @param string $sIdTargetElement
     * @param string $sType
     */
    public function __construct($sInnerHtml="",$sIdTargetElement="butSave",$sType="topup")
    {
        parent::__construct();
        $this->_inner_html = $sInnerHtml;
        $this->sType = $sType;
        $this->sIdTargetElement = $sIdTargetElement;
    }
    
    public function get_html()
    {
        // <a id="acrToBottom" href="#butSave" class="btn btn-primary btn-flat pull-right"> &darr; </a>
        $oAnchor = new HelperAnchor();
        if($this->_id)
            $oAnchor->set_id($this->_id);
        
        $oAnchor->set_href("#".$this->sIdTargetElement);
        $oAnchor->add_class("btn");
        $oAnchor->add_class("btn-primary");
        $oAnchor->add_class("btn-flat");
        $oAnchor->add_class("pull-right");
        
        if($this->iSpan) 
            $oAnchor->add_class("span$this->iSpan");
        
        if($this->sType=="topup") 
            $this->_inner_html.=" &darr; ";
        else 
            $this->_inner_html.=" &uarr; ";

        $oAnchor->set_innerhtml($this->_inner_html);
        
        $sHtmlToReturn = $oAnchor->get_html();
        return $sHtmlToReturn;
    }

    /**
     * @param string $value topup|todown
     */
    public function set_type($value){$this->sType=$value;}
    public function set_target($idElement){$this->sIdTargetElement=$idElement;}
}
