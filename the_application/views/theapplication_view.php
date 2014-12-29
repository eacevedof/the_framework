<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.5
 * @name TheApplicationView
 * @file theapplication_view.php 
 * @date 09-10-2014 19:36 (SPAIN)
 * @observations: Application Lib
 * @requires: theframewor_view.php
 */
class TheApplicationView extends TheFrameworkView
{
    protected $sUriLoggedHome;
    protected $sUriLogout;
    protected $sUriHome;
    
    public function __construct(
            $sThemefolder="",$viewfolder="",$viewfilename="",$pathview="",$layout="")
    {
        $this->sThemeFolder = $sThemefolder;
        if(!$this->sThemeFolder) $this->sThemeFolder = "huraga";
        if($this->sThemeFolder) $this->sThemeFolderDs = $this->sThemeFolder.TFW_DS;
        //if(!$layout) $this->sLayout = "layout_onecolumn";
        if(!$layout) $this->sLayout = "layout_twocolumn";
        
        $this->sViewFolder = $viewfolder;
        $this->sViewFileName = $viewfilename;
        
        $this->sPathPage404 = $this->get_fixed_syspath("homes/404.php");
        $this->sPathPage401 = $this->get_fixed_syspath("homes/401.php");
        $this->set_path_view($pathview);
       
        //Generarlo al final preferiblemente,define isPermaLink necesario para construir las urls
        parent::__construct();
        //bug($this->isPermaLink,"permalink en appview");
        $this->sUriLoggedHome = $this->build_url(TFW_DEFAULT_CONTROLLER,NULL,TFW_DEFAULT_LOGGED_METHOD);
        $this->sUriLogout = $this->build_url(TFW_DEFAULT_CONTROLLER,NULL,"logout");      
  
    }
  
    public function set_logout_uri($value){$this->sUriLogout=$value;}
    public function set_home_uri($value){$this->sUriHome=$value;}
}
