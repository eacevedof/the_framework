<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.1
 * @name App Helper Formhead
 * @date 24-11-2013 15:56 (SPAIN)
 * @file apphelper_formhead.php
 */
class AppHelperFormhead extends TheApplicationHelper
{
    private $_strong_text;
    private $_textmessage;
    private $sDivClass;

    public function __construct($strongtext="",$textmessage="")
    {
        $this->_strong_text = $strongtext;
        $this->_textmessage = $textmessage;
        $this->sDivClass = "alert-inverse";
    }
    
    public function get_html()
    {
        $sHtmlToReturn .= "<div class=\"";
        if($this->iSpan) $sHtmlToReturn .= " span$this->iSpan";
        if($this->sDivClass) $sHtmlToReturn .= " ".$this->sDivClass;
        $sHtmlToReturn .= "\" alert=\"div\"";
        $sHtmlToReturn .= " style=\"background-color:black;color:white;margin:0;padding:0;padding-top:0.5%;\"";
        $sHtmlToReturn .= ">";
        if($this->_strong_text) $sHtmlToReturn .= "<strong alert=\"strong\">$this->_strong_text</strong> ";
        if($this->_textmessage) $sHtmlToReturn .= $this->_textmessage;
        $sHtmlToReturn .= "</div>\n";
        return $sHtmlToReturn;
    }
    
    public function set_h2_icon($sValue){$this->sDivClass=$sValue;}
}
