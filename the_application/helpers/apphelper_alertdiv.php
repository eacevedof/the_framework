<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.1
 * @name ApphelperAlertdiv
 * @file apphelper_alertdiv.php 
 * @date 20-10-2013 18:11 (SPAIN)
 * @observations: ActionsBar
 */
class AppHelperAlertdiv extends TheApplicationHelper
{
    private $sTitle;
    private $sContent;
    private $sDivClass;
    private $sType;//error, info, success, black, white, block
    private $useCloseButton;
    
    public function __construct($sTitle="",$sContent="")
    {
        $this->sTitle = $sTitle;
        $this->sContent = $sContent;
        $this->sType = "success";
    }
    
    public function get_html()
    {
        $sHtmlToReturn = "";
        if($this->sTitle || $this->sContent)
        {            
            $sHtmlToReturn .= "<div class=\"alert";
            if($this->iSpan) $sHtmlToReturn .= " span$this->iSpan";
            $this->load_divclass_by_type();
            if($this->sDivClass) $sHtmlToReturn .= " $this->sDivClass";
            $sHtmlToReturn .= "\">";
            if($this->useCloseButton) $sHtmlToReturn .= $this->build_close_button();
            if(!($this->sType=="b"||$this->sType=="block"))
            {
                if($this->sTitle) $sHtmlToReturn .= "<strong>$this->sTitle</strong>\n";
                if($this->sContent) $sHtmlToReturn .= $this->sContent;
            }
            else
            {
                if($this->sTitle) $sHtmlToReturn .= "<h4 class=\"heading\">$this->sTitle</h4>\n";
                if($this->sContent) $sHtmlToReturn .= "<p>$this->sContent</p>\n";            
            }
            $sHtmlToReturn .= "</div>\n";
        }        
        return $sHtmlToReturn;
    }
    
    protected function build_close_button()
    {
        $sHtmlButton = "<button data-dismiss=\"alert\" class=\"close\">×</button>\n";
        return $sHtmlButton;
    }
    
    protected function load_divclass_by_type()
    {
        switch($this->sType) 
        {
            case "e":
            case "error":
                $this->sDivClass = "alert-error";
            break;
            case "i":
            case "info":
                $this->sDivClass = "alert-info";
            break;
//            case "a":
//            case "alert":
//                $this->sDivClass = "";
//            break;
            case "s":
            case "success":
                $this->sDivClass = "alert-success";
            break;
            case "inv":
            case "inverse":
                $this->sDivClass = "alert-inverse";
            break;
            case "w":
            case "white":
                $this->sDivClass = "alert-white";
            break;        
            case "b":
            case "block":
                $this->sDivClass = "alert-block";
            break;
            default://black
                $this->sDivClass = "";
            break;
        }
    }
    
    public function set_type($value="e"){$this->sType=$value;}
    public function set_title($value){$this->sTitle=$value;}
    public function set_content($value){$this->sContent=$value;}
    public function use_close_button($isOn=true){$this->useCloseButton=$isOn;}
    
}
