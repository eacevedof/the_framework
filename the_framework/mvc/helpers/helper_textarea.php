<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.5
 * @name HelperTextarea
 * @date 04-08-2013 20:40 (SPAIN)
 * @file helper_textarea.php
 * @observations
 */
class HelperTextarea extends TheFrameworkHelper
{
    private $_maxlength;

    private $_cols = 40;
    private $_rows = 8;
    
    public function __construct
    ($id="", $name="", $innerhtml="", $extras="", $maxlength=-1
     , $cols=40, $rows=8, $class="", HelperLabel $oLabel=null)
    {
        $this->_type = "textarea";

        $this->_idprefix = "";
        $this->_id = $id;
        $this->set_innerhtml($innerhtml);
        $this->_name = $name;
        $this->_cols = $cols;
        $this->_rows = $rows;

        if($class) $this->arClasses[] = $class;
        if($style) $this->arStyles[] = $style;
       
        $this->_maxlength = $maxlength;
        $this->arExtras = $extras;
        $this->oLabel = $oLabel;        
    }
    
    public function get_html()
    {  
        $sHtmlOpenTag = "";
        if($this->oLabel) $sHtmlOpenTag .= $this->oLabel->get_html();
        //Una longitud de 0 tiene un comportamiento parecido a un bloqueado
        if($this->_maxlength>-1)
            $this->_js_onkeypress .= " return oMtb.set_max_maxlength(this,$this->_maxlength,event);";
        
        if($this->_comments) $sHtmlOpenTag .= "<!-- $this->_comments -->\n";
        $sHtmlOpenTag .= $this->get_opentag();
        $sHtmlOpenTag .= $this->_inner_html;
        $sHtmlOpenTag .= $this->get_closetag();
        return $sHtmlOpenTag;
    }
 
    public function get_opentag()
    {
        $sHtmlOpenTag = "<$this->_type";
        if($this->_id) $sHtmlOpenTag .= " id=\"$this->_idprefix$this->_id\"";
        if($this->_name) $sHtmlOpenTag .= " name=\"$this->_idprefix$this->_name\"";                
        if($this->_rows) $sHtmlOpenTag .= " rows=\"$this->_rows\"";
        if($this->_cols) $sHtmlOpenTag .= " cols=\"$this->_cols\"";
        //propiedades html5
        if($this->_isDisabled) $sHtmlOpenTag .= " disabled";
        if($this->_isReadOnly) $sHtmlOpenTag .= " readonly"; 
        if($this->_isRequired) $sHtmlOpenTag .= " required"; 
        //eventos
        if($this->_js_onblur) $sHtmlOpenTag .= " onblur=\"$this->_js_onblur\"";
        if($this->_js_onchange) $sHtmlOpenTag .= " onchange=\"$this->_js_onchange\"";
        if($this->_js_onclick) $sHtmlOpenTag .= " onclick=\"$this->_js_onclick\"";
        
        if($this->_js_onkeypress && $this->_isEnterInsert) 
            $sHtmlOpenTag .= " onkeypress=\"$this->_js_onkeypress;onenter_insert(event);\"";
        elseif($this->_js_onkeypress && $this->_isEnterUpdate)
            $sHtmlOpenTag .= " onkeypress=\"$this->_js_onkeypress;onenter_update(event);\"";
        elseif($this->_js_onkeypress && $this->_isEnterSubmit)
            $sHtmlOpenTag .= " onkeypress=\"$this->_js_onkeypress;onenter_submit(event);\"";
        elseif($this->_js_onkeypress) $sHtmlOpenTag .= " onkeypress=\"$this->_js_onkeypress\"";
        //postback(): Funcion definida en HelperJavascript
        elseif($this->_isEnterInsert) $sHtmlOpenTag .= " onkeypress=\"onenter_insert(event);\"";
        elseif($this->_isEnterUpdate) $sHtmlOpenTag .= " onkeypress=\"onenter_update(event);\"";
        elseif($this->_isEnterSubmit) $sHtmlOpenTag .= " onkeypress=\"onenter_submit(event);\"";
        
        if($this->_js_onfocus) $sHtmlOpenTag .= " onfocus=\"$this->_js_onfocus\"";
        if($this->_js_onmouseover) $sHtmlOpenTag .= " onmouseover=\"$this->_js_onmouseover\"";
        if($this->_js_onmouseout) $sHtmlOpenTag .= " onmouseout=\"$this->_js_onmouseout\""; 

        //aspecto
        $this->load_cssclass();
        if($this->_class) $sHtmlOpenTag .= " class=\"$this->_class\"";
        $this->load_style();
        if($this->_style) $sHtmlOpenTag .= " style=\"$this->_style\"";
        //atributos extras
        if($this->_maxlength) $sHtmlOpenTag .= " maxlength=\"$this->_maxlength\"";
        if($this->arExtras) $sHtmlOpenTag .= " ".$this->get_extras();
        if($this->_isPrimaryKey) $sHtmlOpenTag .= " pk=\"pk\"";
        if($this->_attr_dbtype) $sHtmlOpenTag .= " dbtype=\"$this->_attr_dbtype\"";
        
        $sHtmlOpenTag .= ">\n";        
        return $sHtmlOpenTag;
    }    
    
    //**********************************
    //             SETS
    //**********************************
    public function set_maxlength($value){$this->_maxlength = $value;}
    
    //**********************************
    //             GETS
    //**********************************
    public function get_maxlength(){return $this->_maxlength;}
    public function readonly($isReadOnly=true){$this->_isReadOnly = $isReadOnly;}
}
/*
 <label for="detm_Observaciones">Observ.</label>    
 <textarea cols="40" rows="8" name="detm_Observaciones" id="detm_Observaciones"
 *  class="clsNormal"><? echo ComponentText::clean_for_html($oMtbCliente->get_observaciones()) ?></textarea>
 */