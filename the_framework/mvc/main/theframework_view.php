<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.1.3
 * @name TheFrameworkView
 * @file theframework_view.php 
 * @date 30-10-2014 12:40 (SPAIN)
 * @observations: The Framework Main View
 */
/*Cache-Control	no-store, no-cache, must-revalidate, post-check=0, pre-check=0
Content-Length	18363
Content-Type	text/html
Date	Sat, 27 Apr 2013 11:20:27 GMT
Expires	Thu, 19 Nov 1981 08:52:00 GMT
Pragma	no-cache
Server	Microsoft-IIS/5.1
Set-Cookie	PHPSESSID=p9duf0d3a5otaf65tmhvd3l8n7; path=/
X-Powered-By	ASP.NET, PHP/5.2.17*/
class TheFrameworkView extends TheFramework
{
    protected $sThemeFolder;
    protected $sThemeFolderDs;
    protected $sLayout;
    protected $sViewFolder;
    protected $sViewFileName;
    protected $arVars = array();
    protected $arJs;
    protected $isJsRewrite;
    protected $sPathView = "";
    //protected $sPathLanguage = "english";
    
    protected $showLeftcolumn = true;
    protected $sBodyClass = "";
    protected $isPage404 = false;
    protected $isPage401 = false;
    protected $sPathPage404;
    protected $sPathPage401;
    protected $sPathLayout;
    protected $sPathLayoutDs;
    
    protected $sWarningMessage; //NO SE HAN ENCONTRADO RESULTADOS    
    protected $isUnset = true;

    protected $hideElementTopForm = true;
    
    protected $sController;
    protected $sPartial;
    protected $sMethod;
    
    protected $sUrlReferer;
    protected $sUriModule;
    protected $sUriMvc;
    
    protected $sPageTitle;
    
    public function __construct()
    {
        //inicia sesion, carga dispositivo,si es de consola, si se usa permalink
        parent::__construct();
        $this->arJs[0] = array();//Framework js
        $this->arJs[1] = array();//Application js
        $this->sController = $_GET["controller"]; //module
        $this->sPartial = $_GET["partial"]; //tab
        $this->sMethod = $_GET["method"]; //view
        if($_SERVER["HTTP_REFERER"]) $_SESSION["viewreferer"] = $_SERVER["HTTP_REFERER"];
        //bug($this->get_last_url_referer(),"last url referer");
        //uriModule,uriMvc
        $this->load_uris();       
    }
  
    public function add_var(&$mxVar,$sVarName){$this->arVars[$sVarName]=$mxVar;if($this->isUnset)unset($mxVar);}
    
    public function show_page()
    {     
        //bug($this->is_page401());
        //bug($this->sPathView);
        //$sPathLayoutPage = $this->sThemeFolder.TFW_DS
        //        .$this->sLayout.TFW_DS
        //        ."page.php";
        $sPathLayoutPage = $this->sPathLayoutDs."page.php";
        //cargar variables
        //bug($sPathLayoutPage,"spath_layout_page");//huraga\layout_onecolumn\page.php
        foreach($this->arVars as $mxKey => $valor)
            ${$mxKey}=$valor;
        //bugfileipath($sPathLayoutPage);
        include_once($sPathLayoutPage);
        //bugif();
    }

    public function build_html_table($arTable=array(),$arHeaders=array(),$sTableId="table")
    {
        $sHtmlTable = "";
 
        $sHtmlTable .= "<table id=\"$sTableId\" class=\"table table-striped table-bordered table-condensed\">\n";
        $sHtmlTable .= "<thead>";
        if(!empty($arHeaders))
        {
            $sHtmlTable .= "<tr>\n";
            foreach($arHeaders as $sHeaderName)
                $sHtmlTable .= "<th>$sHeaderName</th>";
            $sHtmlTable .= "</tr>\n";
        }
        $sHtmlTable .= "</thead>";

        $sHtmlTable .= "<tbody>";
        foreach($arTable as $iRow=>$arRow)
        {
            //$sTdStyle = self::get_style_td_background($iRow);
            $sHtmlTable .= "<tr>\n";
            //$sHtmlTable .= "<td class=\"$sTdStyle\">$iRow</td>\n";
            foreach($arRow as $iFieldName => $sFieldValue)
                $sHtmlTable .= "<td>$sFieldValue</td>\n";
            $sHtmlTable .= "</tr>\n";
        }
        $sHtmlTable .= "</tbody>";
        $sHtmlTable .= "</table>\n";
        return $sHtmlTable;
    }

    protected function load_uris()
    {
        $arUriModule = array(); $arUriMvc=array();
        if($this->sController) 
        {
            $arUriModule[] = "module=$this->sController";
            $arUriMvc[] = "controller=$this->sController";
        }
        if($this->sPartial)
        { 
            $arUriModule[] = "tab=$this->sPartial";
            $arUriMvc[] = "partial=$this->sPartial";
        }
        
        if($this->sMethod)
        { 
            $arUriModule[] = "view=$this->sMethod";
            $arUriMvc[] = "method=$this->sMethod";
        }
        $this->sUriModule=implode("&",$arUriModule);
        $this->sUriMvc=implode("&",$arUriMvc);
    }
    
    protected function is_firstchar_ds($value){return (is_firstchar($value,"/") || is_firstchar($value,"\\"));}
    
    private function get_path_previous($arFolders,$iCurrent)
    {
        $sPathTemp = "";
        for($i=0;$i<$iCurrent;$i++)
            $sPathTemp .= $arFolders[$i].DS;
        return $sPathTemp;
    }
    
    protected function build_js_folders($sPathFileName,$sPathTarget)
    {
        $sPathFileName = $this->get_fixed_syspath($sPathFileName);
        if(strstr($sPathFileName,DS))
        {
            $arSubFolders = explode(DS,$sPathFileName);
            array_pop($arSubFolders);

            //bug($arSubFolders);
            foreach($arSubFolders as $i=>$sFolder)
            {    
                //recupera las carpetas previas en forma de subruta
                $sPathPrev = $this->get_path_previous($arSubFolders,$i);
                $sPathCheck = $sPathTarget.$sPathPrev.$sFolder; 
                //bug($sPathCheck);
                if(!is_dir($sPathCheck))
                {
                    if(!mkdir($sPathCheck))
                    {
                        $sMessage = "build_js_folder: $sPathCheck no directory created!";
                        $this->add_error($sMessage);
                    }
                }
            }
         }//if (ds in spathfilename)
    }


    public function js_load()
    {
        //theframework paths
        $sPathSoruce = TFW_PATH_FOLDER_PROJECTDS."the_framework/html/js/";
        $sPathTarget = TFW_PATH_FOLDER_PROJECTDS."the_public/js/the_framework/";
        $sPathPublic = "/js/the_framework/";
        
        //bug($this->arJs);
        foreach($this->arJs as $isApp=>$arJs)
        {
            if($isApp)
            {
                $sPathSoruce = TFW_PATH_FOLDER_PROJECTDS."the_application/views/_js/";
                $sPathTarget = TFW_PATH_FOLDER_PROJECTDS."the_public/js/the_application/";
                $sPathPublic = "/js/the_application/";
            }
            //pr($arJs,"arjs");
            //bug($isApp,"isapp");
            foreach($arJs as $sFileName)
            {
                //Si es una libreria del framework se tienen que crear las subcarpetas
                if(!$isApp)
                {    
                    $this->build_js_folders($sFileName,$sPathTarget);
                }
                
                $sFileNameJs = $sFileName.".js";
                //bug(realpath($sFileName),"real");
                $sTmpPathSource = $sPathSoruce.$sFileNameJs;
                $sTmpPathTarget = $sPathTarget.$sFileNameJs;
                //Limpia las barras de directorios
                $sTmpPathSource = $this->get_fixed_syspath($sTmpPathSource);
                $sTmpPathTarget = $this->get_fixed_syspath($sTmpPathTarget);
                //pr($sTmpPathSource,"source"); pr($sTmpPathTarget,"target");
                //pr(is_file($sTmpPathSource,"is file $sPathSoruce"));
                if(is_file($sTmpPathSource))
                {
                    if(!is_file($sTmpPathTarget))
                        copy($sTmpPathSource,$sTmpPathTarget);
                    //si ya existe en destino, se elminina y se crea nuevamente
                    elseif($this->isJsRewrite)
                    {
                        unlink($sTmpPathTarget);
                        copy($sTmpPathSource,$sTmpPathTarget);
                    }
                    
                    //else
                        //$sMessage = "TheFrameworkView.js_load(): file already exists $sTmpPathTarget";
                        //$this->add_error($sMessage);
                    //bug($sPathPublic.$sFileNameJs);
                    echo "<script src=\"$sPathPublic$sFileNameJs\" lib=\"TheFrameworkView.js_load\" type=\"text/javascript\"></script>\n";
                }
                else
                {
                    $sMessage = "TheFrameworkView.js_load(): file not found $sTmpPathSource";
                    $this->add_error($sMessage);
                    //bug($sMessage);die;
                }                
            }//foreach $arJsFiles 
        }//foreach $this->arJs 
    }//js_load()
    
    //**********************************
    //             SETS
    //**********************************    
    public function set_session_user($oUser){$this->oSessionUser=$oUser;}
    /**
     * Creates sLayout, sPathLayout y sPathLayoutDs
     * @param string $value Suffix layout folder name
     */
    public function set_layout($value)
    {
        $this->sLayout = "layout_$value";
        $this->sPathLayout = $this->sThemeFolderDs.$this->sLayout;
        $this->sPathLayoutDs = $this->sPathLayout.TFW_DS;
    }
    
    public function set_bodyclass($value){$this->sBodyClass = $value;}
    
    private function has_twoparts($value)
    {
        $arParts = explode("/",$value);
        if(!is_array($arParts))
            $arParts = explode("\\",$value);
        if(count($arParts)==2 && $arParts[0]=="")
            return TRUE;
        return FALSE;
    }
    
    private function build_default_view()
    {
        $arFixedViews = array
        (
            "insert"=>"view_insert"
            ,"update"=>"view_update"
            ,"delete"=>"view_delete"
            ,"quarantine"=>"view_quarantine"
            ,"singleassign"=>"view_assign"
            ,"multiassign"=>"view_assign"
            ,"get_list"=>"view_index"
            ,"pictures"=>"view_pictures"
            ,"error_401"=>"401"
            ,"error_403"=>"403"
            ,"error_404"=>"404"
            ,"error_500"=>"500"
            ,"error_503"=>"503"
        );
        
        $arKeys = array_keys($arFixedViews);
        
        $sView = $_GET["view"];
        if(!$sView) 
            $sView = $_GET["function"];
        
        if(in_array($sView,$arKeys))
            return $arFixedViews[$sView];
        else
            return "view_index";
    }
    
    public function set_path_view($value)
    {
        if(!$value)
        {
            $value = $this->build_default_view();
            $value = "_base".DS.$value;
        }   
        $this->sPathView = $this->get_fixed_syspath("$value.php");
        //bug($this->sPathView);
        //if(is_file($this->sPathView)) echo "existe";
    }
    
    public function hide_leftcolumn($isOn=false){$this->showLeftcolumn =$isOn;}
    public function use_page404($isOn=true){$this->isPage404 = $isOn;}
    public function use_page401($isOn=true){$this->isPage401 = $isOn;}
    public function set_theme_folder($value=""){$this->sThemeFolder=$value; $this->sThemeFolderDs=$this->sThemeFolder.TFW_DS;}
    public function set_warning_message($sMessage){$this->sWarningMessage = $sMessage;}
    public function hide_element_topform($isOn=true){$this->hideElementTopForm=$isOn;}
    public function set_page_title($sValue){$this->sPageTitle=$sValue;}
    public function disable_unset($isOn=false){$this->isUnset=$isOn;}
    
    /**
     * Add js files to be loaded
     * @param string|csvstring $mxFileName
     * @param boolean $isApp 0:Theframework 1:Theapplication
     */
    public function add_js($mxFileName,$isApp=1)
    {
        if(strstr($mxFileName,","))
        {
            $mxFileName = explode(",",$mxFileName);
            foreach($mxFileName as $sFileName)
                $this->arJs[$isApp][] = $sFileName;
        }        
        elseif($mxFileName)
            $this->arJs[$isApp][] = $mxFileName;
    }
 
    /**
     * Resetea $this->arJs[$isApp]
     * Asigna $mxValue si procede
     * @param string|csvstring|array $mxValue
     * @param boolean $isApp 1|0
     */
    public function set_js($mxValue,$isApp=1)
    {
        $this->arJs[$isApp] = array();
        if(is_array($mxValue))
            $this->arJs[$isApp] = $mxValue;
        elseif(strstr($mxValue,",")) 
            $this->arJs[$isApp] = explode(",",$mxValue);
        elseif($mxValue)
            $this->arJs[$isApp][] = $mxValue;
    }
    
    public function set_js_rewrite($isOn=TRUE){$this->isJsRewrite=$isOn;}
    
    //**********************************
    //             GETS
    //**********************************    
    public function get_session_user(){return $this->oSessionUser;}
    public function get_bodyclass(){return $this->sBodyClass;}
    public function is_leftcolumn(){return $this->showLeftcolumn;}    
    public function get_path_view(){return $this->sPathView;}
    public function is_page404(){return $this->isPage404;}
    public function is_page401(){return $this->isPage401;}
    public function get_theme_folder(){return $this->sThemeFolder;}
    public function get_layout(){return $this->sLayout;}

    protected function show_url_referer(){echo $_SERVER["HTTP_REFERER"];}
    public function get_page_title(){return $this->sPageTitle;}
    public function show_page_title(){echo $this->get_page_title();}
    public function get_last_url_referer(){return $_SESSION["viewreferer"];}
    public function show_last_url_referer(){echo $this->get_last_url_referer();}
}
