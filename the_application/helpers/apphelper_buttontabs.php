<?php
/**
 *version: 1.0.1
 */
class AppHelperButtontabs extends TheFrameworkHelper
{
    private $_h2_text;
    private $arTabs;
    private $sActiveTab;
    private $sAnchorClass;
    private $sH2IconClass;
    private $useBorderBottom;

    public function __construct($h2text="",$arTabs=array(),$sActive="")
    {
        $this->_h2_text = $h2text;
        $this->arTabs = $arTabs;
        $this->sActiveTab = $sActive;
        $this->sAnchorClass = "btn";
        //$this->sH2IconClass = "awe-cogs";
    }
    
    public function get_html()
    {
        $sHtmlToReturn = "";
        $sHtmlToReturn .= "<header";
        //;margin-top:12px;margin-bottom:0px
        if($this->useBorderBottom) $sHtmlToReturn .= " style=\"border-bottom:0;\"";
        $sHtmlToReturn .= ">\n";
        $sHtmlToReturn .= "<h2>";
        if($this->_h2_text) $sHtmlToReturn .= "<span class=\"$this->sH2IconClass\"></span> ";
        $sHtmlToReturn .= $this->_h2_text."</h2>";
        $sHtmlToReturn .= $this->build_ul_tabs();
        $sHtmlToReturn .= "</header>\n";
        return $sHtmlToReturn;
    }
    
    protected function build_ul_tabs()
    {
        $sHtmlTabs = "<ul class=\"data-header-actions\">\n";
        foreach($this->arTabs as $sTab=>$arData)
        {
            $sHtmlTabs .= "<li class=\"";
            if($sTab==$this->sActiveTab) $sHtmlTabs.=" active";
            $sHtmlTabs .= "\">";
            $sHref = $arData["href"]; $sIcon = $arData["icon"]; $sTarget = $arData["target"];
            $sInnerHtml = $arData["innerhtml"];
            $sHtmlTabs .= "<a class=\"$this->sAnchorClass\" href=\"$sHref\"";
            if($sTarget) $sHtmlTabs .= " target=\"_$sTarget\"";
            $sHtmlTabs .= ">";
            if($sIcon) $sHtmlTabs .= "<span class=\"$sIcon\"></span> ";
            $sHtmlTabs .= "$sInnerHtml</a>";
            $sHtmlTabs .="</li>\n";
        }
        $sHtmlTabs .= "</ul>\n";
        return $sHtmlTabs;
    }
    
    public function set_anchor_class($sValue){$this->sAnchorClass=$sValue;}
    public function set_active_tab($sValue){$this->sActiveTab=$sValue;}
    public function set_h2_icon($sValue){$this->sH2IconClass=$sValue;}
    public function set_tabs($arTabs){$this->arTabs=$arTabs;}
    public function no_border_bottom($isOn=true){$this->useBorderBottom=$isOn;}
}
