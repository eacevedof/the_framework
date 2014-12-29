<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.2
 * @name PartialExports
 * @file partial_exports.php 
 * @date 24-10-2014 13:52 (SPAIN)
 * @observations:
 * @requires: 
 */
import_apphelper("tablecheckgroup");
import_apptranslate("modulebuilder");
import_controller("modulebuilder","modulebuilder");
//bugif();
class PartialExports extends ControllerModuleBuilder
{
    private $sPathProject;
    private $arPaths = array();
    private $sFolderExclude;
    
    public function __construct()
    {
        $this->sModuleName = "modulebuilder";
        //console,pgpage,clientdevice,sesuser,aftersucces,currop  | log,session,permission,view
        parent::__construct($this->sModuleName);
        //$this->sPathProject = TFW_PATH_FOLDER_PROJECT;
        //bug(TFW_PATH_FOL_THEPUBLICDS);
        $this->sPathProject = TFW_PATH_FOLDER_PROJECT;
        $this->load_path_libs();
        //Carpetas dentro de la carpeta raiz del proyecto
        $this->sFolderExclude = "adminhuraga,backupdb,devdocuments,nbproject,sql,.svn";
    }//__construct()

    public function load_path_libs()
    {
        //$sPathRootDS = TFW_PATH_FOLDER_PROJECTDS;
        //Origenes
        $this->arPaths["from"]["project"] = $this->sPathProject;
        $this->arPaths["from"]["the_application"] = "$this->sPathProject/the_application";
        $this->arPaths["from"]["the_framework"] = "$this->sPathProject/the_framework";
        $this->arPaths["from"]["the_public"] = "$this->sPathProject/the_public";
        //$this->arPaths["from"]["backupdb"] = "$this->sPathProject/ruta??";
        //TODO: debo cambiar el procedimiento almacenado de backup para que permita aplicar una ruta de destino
        //$this->arPaths["the_database"] = $sPathRootDS."the_public";
        $this->arPaths["to"]["modulebuilder"] = TFW_PATH_FOL_THEPUBLICDS."downloads/modulebuilder/";
        $this->arPaths["to"]["backupdb"] = TFW_PATH_FOL_THEPUBLICDS."downloads/modulebuilder/backupdb";

    }
    
//<editor-fold defaultstate="collapsed" desc="EXPORT">
    protected function build_export_opbuttons()
    {
        $arButTabs = array();
        $arButTabs["list"]=array("href"=>$this->build_url(),"icon"=>"awe-search","innerhtml"=>tr_mdbexp_dashboard);
        return $arButTabs;
    }//build_export_opbuttons()

    protected function build_export_form($usePost=0)
    {
        $oForm = new HelperForm("frmInsert");
        $oForm->add_class("form-horizontal");
        $oForm->add_style("margin-bottom:0");
        $arFields = $this->build_export_fields($usePost);
        $oForm->add_controls($arFields);
        return $oForm;
    }//build_export_form()
    
    protected function build_export_fields($usePost=0)
    {   //bugpg();
        //bug($arFields);die;
        $arFields = array(); $oAuxField = NULL; //$oAuxLabel = NULL;
        $arFields[]= new AppHelperFormhead("Libs Export");
        
        $oAuxField = new HelperInputText("txtExportFolder","txtExportFolder");
        $oAuxField->on_enterinsert();
        if($usePost) $oAuxField->set_value($this->get_post("txtExportFolder"));
        $oAuxLabel = new HelperLabel("txtExportFolder",tr_mdbexp_export_folder,"lblExportFolder");
        //$oAuxLabel->add_class("labelreq");
        //$oAuxField->readonly();
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        
        //TODO: Debo leer los archivos version_info.txt para añadirlos como información extra
        $arOptions = array
        (
             "the_application"=>$this->get_lib_version("the_application")
             ,"the_public"=>$this->get_lib_version("the_public")
             ,"the_framework"=>$this->get_lib_version("the_framework")
        );
        
        $oAuxField = new ApphelperTableCheckGroup($arOptions,"chkLibs");
        //$oAuxField->set_checks_per_row(1);
        $oAuxField->set_sectionid("Libs");
        $oAuxField->set_values_to_check(array_keys($arOptions));
        if($usePost) $oAuxField->set_values_to_check($this->get_post("chkLibs"));
        $arFields[] = $oAuxField;           
 
        //Boton
        $oAuxField = new HelperButtonBasic("butSave",tr_ins_savebutton);
        $oAuxField->add_class("btn btn-primary");
        $oAuxField->set_js_onclick("insert();");
        $arFields[] = new ApphelperFormactions(array($oAuxField));
               
        //Accion
        //POST INFO
        $oAuxField = new HelperInputHidden("hidAction","hidAction");
        $arFields[] = $oAuxField;
        $oAuxField = new HelperInputHidden("hidPostback","hidPostback");
        $arFields[] = $oAuxField;
       
        return $arFields;
    }//build_export_fields()
    
    public function generate()
    {
        $arFieldsConfig = array();
        $arFieldsConfig["selTable"] = array("id"=>"selTable","label"=>tr_table,"length"=>100,"type"=>array("required"));

        if($this->is_inserting())
        {
            //array de configuracion length=>i,type=>array("numeric","required")
            $oAlert = new AppHelperAlertdiv();
            $oAlert->use_close_button();
           
            $arFieldsValues = $this->get_fields_from_post();
            //$oValidate = new ComponentValidate($arFieldsConfig,$arFieldsValues);
            //$arErrData = $oValidate->get_error_field();
            //bug($arErrData); die;
            if($arErrData)
            {
                $oAlert->set_type("e");
                $oAlert->set_title(tr_module_not_built);
                $oAlert->set_content("Field <b>".$arErrData["label"]."</b> ".$arErrData["message"]);
            }
            //no error
            else
            {
                $this->save_repo();
            }//fin else: no error
        }//fin if post action=save

        //Si hay errores se recupera desde post
        if($arErrData) $oForm = $this->build_export_form(1);
        else $oForm = $this->build_export_form();
        //bug($oForm); die;
       
        $oJavascript = new HelperJavascript();
        $oJavascript->set_validate_config($arFieldsConfig);
        $oJavascript->set_focusid("id_all");
       
        $oOpButtons = new AppHelperButtontabs(tr_entities);
        $oOpButtons->set_tabs($this->build_export_opbuttons());

        //bug($oForm); die;
        $this->oView->add_var($oOpButtons,"oOpButtons");
        $this->oView->add_var($oAlert,"oAlert");
        $this->oView->add_var($oForm,"oForm");
        $this->oView->add_var($oJavascript,"oJavascript");
        $this->oView->set_path_view("_base/view_insert");
        $this->oView->show_page();
    }//insert()
    
//</editor-fold>
    
    private function save_repo()
    {
        $oBehavBuilder = new AppBehaviourModuleBuilder();
        $sFolderRepo = "repo_".date("Ymd");
        if($this->get_post("txtExportFolder"))
        {
            $sFolderRepo = $this->get_post("txtExportFolder");
            $sFolderRepo = $this->get_slug_cleaned($sFolderRepo,"_");
        }
        
        $sPathRepo = $this->arPaths["to"]["modulebuilder"].$sFolderRepo;
        $sPathDb = $sPathRepo."/backupdb"; 
        $this->delete_folder($sPathRepo);

        if($this->is_export_all())
        {
            $this->copy_folder($this->arPaths["from"]["project"],$sPathRepo);
            if(TFW_DB_TYPE=="mssql")
            {
                mkdir($sPathDb);
                //bug($sPathDb,"pathdb");
                $oBehavBuilder->db_backup($sPathDb."/");
                $this->zip_folder($sPathDb,"$sPathRepo/backupdb.zip");                
            }
            $this->delete_folder($sPathDb);
        }
        else
        {
            $arPaths = $this->get_paths_selected();
            foreach($arPaths as $sPathLib)
            {
                $this->copy_folder($sPathLib,$sPathRepo);
            }
        }        
    }//save_repo
    
    private function copy_folder($sPathSourceDS,$sPathTarget)
    {
        if(is_dir($sPathSourceDS))
        {
            @mkdir($sPathTarget);
            $oDir = dir($sPathSourceDS);
            $arExclude = $this->get_folders_excluded();
            
            while(FALSE!==($sDirElement=$oDir->read()))
            {
                //Si el nombre son las marcas del sistema de directorios o es una carpeta .svn
                //salta al siguiente elemento
                if($sDirElement=="."||$sDirElement==".." || in_array($sDirElement,$arExclude))
                    continue;

                $sPathElement = $sPathSourceDS."/".$sDirElement;

                //Si el elemento interno es un directorio
                if(is_dir($sPathElement))
                {
                    //Se llama recursivamente, se copia el directorio y salta al siguiente
                    $this->copy_folder($sPathElement,$sPathTarget."/".$sDirElement);
                    continue;
                }
                //El elemento a evaluar no es un directorio, Es un Archivo. Se copia
                copy($sPathElement,$sPathTarget."/".$sDirElement);
            }
            $oDir->close();
        }
        //No es directorio. Es un archivo
        else
        {
            //Copia el archivo en el destino
            copy($sPathSourceDS,$sPathTarget);
        }
    }//dir_copy
    
    private function zip_folder($sPathSource,$sPathTargetZip)
    {
        // instantate object
        $oZipArchive = new ZipArchive();
        // create and open the archive 
        if($oZipArchive->open("$sPathTargetZip",ZipArchive::CREATE) !== TRUE) 
        {
            $sMessage = "Could not open archive";
            $this->add_error($sMessage);
            return;
        }

        $arDirContent = scandir($sPathSource);
        //C:\inetpub\wwwroot\proy_tasks\the_public\downloads/modulebuilder/repo_20140606/backupdb.zip
        //bug($sPathTargetZip);
        //bug($arDirContent,"arDirContent");die;
        foreach($arDirContent as $sElement)
        {
            if($sElement!= "." && $sElement!="..") 
            {
                $sPathFileAdd = $sPathSource.DS.$sElement;
                //bugfile($sPathFileAdd);
                if(is_file($sPathFileAdd))
                {
                    //pr($iFileSize);
                    $sContent = $this->get_file_content($sPathFileAdd);
                    $oZipArchive->addFromString($sElement,$sContent);
                }//si es un archivo
            }//si es un elemento válido en la carpeta
        }//foreach $arDirContent
        reset($arDirContent);
        // close the archive
        $oZipArchive->close();
    }//zip_folder
    
    private function is_export_all()
    {
        $arSelected = $this->get_post("chkLibs");
        $arAll = array_keys($this->arPaths["from"]);
        unset($arAll[0]);//quito el item project
        if(count($arSelected)==count($arAll))
            return TRUE;
        return FALSE;
    }

    private function delete_folder($sPathDir) 
    {
        if(is_dir($sPathDir)) 
        {
            $arDirContent = scandir($sPathDir);
            foreach($arDirContent as $sElement)
            {
                if($sElement!= "." && $sElement!="..") 
                {
                    if(filetype($sPathDir.DS.$sElement)=="dir") 
                        $this->delete_folder($sPathDir.DS.$sElement);
                    else 
                        unlink($sPathDir.DS.$sElement);
                }
            }//foreach $arDirContent
            reset($arDirContent);
            rmdir($sPathDir);
        }//fin if(is_dir(..))
    }
    
    private function get_paths_selected()
    {
        $arPaths = array();
        $arSelected = $this->get_post("chkLibs");
        
        foreach($arSelected as $sLibName)
            $arPaths[] = $this->arPaths["from"][$sLibName];

        return $arPaths;
    }

    private function get_lib_version($sLibName)
    {
        $sPathFolderLib = $this->arPaths["from"][$sLibName]."/version_info.txt";
        $sContent = $this->get_file_content($sPathFolderLib);
        $sContent = str_replace("\n","</br>",$sContent);
        return $sContent;
    }

    private function get_folders_excluded(){return explode(",",$this->sFolderExclude);}
}
