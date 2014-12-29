<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.0
 * @name ListActionsbar 
 * @date 29-03-2013 19:56 (SPAIN)
 * @observations: Operations New Delete Reload
 */
include_once("helper_anchor.php");
class ListActionsbar extends TheFrameworkHelper
{
    private $arButtons;
    private $arButtonsExtra;
    private $isNoRefresh;
    private $isNoInsert;
    private $isNoDelete;
    private $isJustButtons;
    
    public function __construct($arButtons,$arButtonsExtra=array())
    {
        $this->arButtons = $arButtons;
        $this->arButtonsExtra = $arButtonsExtra;
    }
    
    public function get_html()
    {
        $oAnchor = new HelperAnchor();
        $oAnchor->add_class("btn");
        
        $sHtml = "";
        if(!$this->isJustButtons)
            $sHtml .= "<div class=\"btn-group\" helper=\"listactionbar\">\n";

        if(!$this->isNoRefresh)
        {    
            $oAnchor->set_href($this->arButtons["refresh"]["href"]);
            $sInnertext = "<span class=\"awe-refresh\"></span> ".$this->arButtons["refresh"]["innertext"];
            $oAnchor->set_innerhtml($sInnertext);
            
            $sHtml .= $oAnchor->get_html();
        }   
        
        if(!$this->isNoInsert)
        {    
            $oAnchor->set_href($this->arButtons["insert"]["href"]);
            $sInnertext = "<span class=\"awe-plus-sign\"></span> ".$this->arButtons["insert"]["innertext"];
            $oAnchor->set_innerhtml($sInnertext);
            $sHtml .= $oAnchor->get_html();
        }

        if(!$this->isNoDelete)
        {    
            $oAnchor->set_href($this->arButtons["delete"]["href"]);
            $sInnertext = "<span class=\"awe-remove\"></span> ".$this->arButtons["delete"]["innertext"];
            $oAnchor->set_innerhtml($sInnertext);
            $sHtml .= $oAnchor->get_html();
        }        

        //ExtraButtons
        foreach($this->arButtonsExtra as $arButton)
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
    
    public function no_new($isOn=true){$this->isNoInsert=$isOn;}
    public function no_reload($isOn=true){$this->isNoRefresh=$isOn;}
    public function no_delete($isOn=true){$this->isNoDelete=$isOn;}
    public function just_buttons($isOn=true){$this->isJustButtons=$isOn;}
    public function set_extra_buttons($arButtons){$this->arButtonsExtra = $arButtons;}
}
