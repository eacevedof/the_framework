<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.5
 * @name ApphelperControlGroup
 * @file apphelper_controlgroup.php 
 * @date 20-10-2013 17:54 (SPAIN)
 * @observations: Field Wrapper de Uraga theme
 */
include_once("theapplication_helper.php");
include_once("helper_label.php");
class ApphelperControlGroup extends TheApplicationHelper //Aplica estidos del tema
{
    private $oField;
    //private $oLabel;
    private $oExtra;
   
    private $sPrependText;
    private $sAppendText;
    private $sNoteInline;
    private $sHelpBlock;
    
    public function __construct($oField=NULL, HelperLabel $oLabel=NULL, $oExtra=NULL) 
    {
        $this->oField = $oField;
        $this->oLabel = $oLabel;
        $this->oExtra = $oExtra;
    }
    
    public function get_html()
    {
        $sHtmlToReturn = "<div class=\"";
        if($this->iSpan) $sHtmlToReturn .= "span$this->iSpan ";
        $sHtmlToReturn .= "control-group\">\n";
        if($this->oLabel) $sHtmlToReturn .= $this->oLabel->get_html()."\n";
        $sHtmlToReturn .= "<div class=\"form-controls\">\n";
        //imprime input prpend y append
        $sHtmlToReturn .= $this->get_open_tag_divinfo();
        if($this->sPrependText) $sHtmlToReturn .= "<span class=\"add-on\">$this->sPrependText</span>\n";
        if($this->_isReadOnly)
            if(method_exists($this->oField,"readonly"))
            {   
                $this->oField->readonly();
                $this->oField->add_class("readonly");
            }
        $sHtmlToReturn .= $this->oField->get_html();
        if($this->sAppendText) $sHtmlToReturn .= "<span class=\"add-on\">$this->sAppendText</span>\n";
        $sHtmlToReturn .= "</div>\n";
        if($this->sNoteInline) $sHtmlToReturn .= "<span class=\"note-inline\">$this->sNoteInline</span>\n";
        if($this->sHelpBlock) $sHtmlToReturn .= "<p class=\"help-block\">$this->sHelpBlock</p>\n";
        if($this->oExtra) $sHtmlToReturn .= $this->oExtra->get_html();
        //controlgroup, append, form-controls
        $sHtmlToReturn .= "</div>\n</div>";
        //<span class=\"note-inline\">This is inline input note</span>
        return $sHtmlToReturn;
    }
    
    protected function get_open_tag_divinfo()
    {
        $sHtmlDiv = "<div";
        if($this->sPrependText)
            $sHtmlToReturn .= " class=\"input-prepend\"";
        elseif($this->sAppendText)
            $sHtmlToReturn .= " class=\"input-append\"";
        elseif($this->sAppendText && $this->sPrependText)
            $sHtmlToReturn .= " class=\"input-prepend input-append\"";
        
        $sHtmlDiv .= ">";
        return $sHtmlDiv;
    }

    public function set_note_inline($value){$this->sNoteInline=$value;}
    public function set_help_block($value){$this->sHelpBlock=$value;}    
    public function set_append_text($value){$this->sAppendText=$value;}
    public function set_prpend_text($value){$this->sPrependText=$value;}
    public function set_obj_field($oField){$this->oField=$oField;}
    public function set_obj_label(HelperLabel $oLabel){$this->oLabel=$$oLabel;}
    public function readonly($isReadOnly = true){$this->_isReadOnly = $isReadOnly;}
    
}