<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.3.1
 * @name ComponentFile
 * @file component_file.php
 * @date 02-10-2014 13:35 (SPAIN)
 * @observations: Para poder escribir en archivos se debe tener permisos de escritura y/o
 * lectura para IUSR_<SERVERNAME>
 * @requires:
 */
class ComponentFile //extends TheFrameworkComponent 
{
    //const NLW = "\r\n"; //windows new line
    //const NLU = "\n";   //unix new line
    //const NLM = "\r";   //ios new line

    //Sistema operativo del servidor
    protected $sServerOS; 
    //Separador de directorios
    protected $sOsDs; 
    //Caracter de nueva linea
    protected $sOsNL;
    //Copias
    //private $_path_target_file;
    //private $_path_source_file;
    
    protected $sPathFolderTarget;
    protected $sPathFolderSource;    
    
    protected $sFilenameSource;
    protected $sFilenameTarget;
    
    //key dentro del array $_FILE
    //nombre del control tipo input file
    protected $sNameInputFile;
    protected $sTargetContent;

    /**
     * @var string tipo: TFW_PATH_FOLDER_LOGDS."custom";
     */
    protected $sPathFolderLog = NULL;
        
    protected $sLogUserOwner;
    //Si se está subiendo un array de archivos, esta propiedad
    //indica con cual de estos se tratará. Usualmente para aplicar un nombre uno a uno
    protected $iUploadIndex;
    
    //Variables de control
    protected $arMessage = array();
    protected $isError = false;

    public function __construct($sServerOS="linux")
    {  
        $this->sServerOS = strtolower($sServerOS);
        $this->load_ds_by_os();
        $this->load_nl_by_os();
        if($_SESSION["tfw_user_identificator"]) $this->sLogUserOwner = $_SESSION["tfw_user_identificator"];
        if(defined("TFW_PATH_FOLDER_LOG")) $this->sPathFolderLog = TFW_PATH_FOLDER_LOGDS."custom";
    }
    
    /**
     * Antes de llamar a este metodo hay que configurar la ruta del directorio de destino donde
     * se desea crear el archivo con set_path_target()
     * Hay que configurar el nombre del archivo con setsFilenameTarget() incluyendo su extensión
     * con is_error() se puede comprobar si ha habido algón problema
     * con get_array_message() se puede identificar el estado de la operación
     * @return boolean 
     */
    public function create()
    {
        $sPathFolder = $this->fix_folderpath($this->sPathFolderTarget);
        if(!is_dir($sPathFolder))
        {
            $this->add_error("$this->sFilenameTarget not created. $sPathFolder is not a valid folder");
            return false;    
        }
        
        $sPathFile = $sPathFolder.$this->sFilenameTarget;
        if(is_file($sPathFile))
        {
            $this->add_error("$this->sFilenameTarget not created. File already exists");
            return false;
        }
        
        //soruce file
        //x: Creación y apertura para sónlo escritura; coloca el puntero al principio del archivo.
        $oCursor = fopen($sPathFile,"x");
        //bug($this->_target_content,"content");
        if($oCursor !== false)
        {
            fwrite($oCursor,""); //Grabo el caracter vacio
            if(!empty($this->sTargetContent)) fwrite($oCursor,$this->sTargetContent);
            fclose($oCursor); //cierro el archivo.
        }
        else
        {
            $this->add_error("$sPathFile not created. fopen() failed. Cursor:".var_export($oCursor,true));
            return false;
        }
        $this->arMessage[] = "File: $sPathFile created";
        return true;
    }
    
    /**
     * Añade un / al final de la ruta de un directorio si hiciera falta
     * @param string $sPathFolderDs
     * @return string Ruta del directorio bien formada con / al final.
     */
    private function fix_folderpath($sPathFolder)
    {
        $sPathFolderDs = $sPathFolder.DIRECTORY_SEPARATOR;
        //windows (cambia los dobles por uno solo)
        $sPathFolderDs = str_replace("\\\\", "\\",$sPathFolderDs);
        //unix 
        $sPathFolderDs = str_replace("//", "/", $sPathFolderDs);
        return $sPathFolderDs;
    }
      
    /**
     * Antes de llamar a este metodo hay que configurar la ruta del directorio de origen y destino donde
     * se desea crear el archivo con set_path_source() y set_path_target()
     * Hay que configurar el nombre del archivo de origen y destino con setsFilenameSource()
     * setsFilenameTarget() incluyendo su extensión
     * con is_error() se puede comprobar si ha habido algón problema
     * con get_array_message() se puede identificar el estado de la operación
     * @return boolean 
     */
    public function copy()
    {
        $sPathFolderSourceDs = $this->fix_folderpath($this->sPathFolderSource);
        $sPathFolderTargetDs = $this->fix_folderpath($this->sPathFolderTarget);
        
        if(!is_dir($sPathFolderSourceDs))
        {
            $this->add_error("File not copied. $sPathFolderSourceDs is not a valid folder");
            return false;
        }
        
        if(!is_dir($sPathFolderTargetDs))
        {
            $this->add_error("File not copied. $sPathFolderTargetDs is not a valid folder");
            return false;
        }
        
        $sPathSourceFile = $sPathFolderSourceDs.$this->sFilenameSource;
        //Al ser una copia de archivo el nombre origen debe ser igual al destino
        $sPathTargetFile = $sPathFolderTargetDs.$this->sFilenameSource;
        
        if(!is_file($sPathSourceFile)) 
        {
            $this->add_error("File not copied. $sPathSourceFile File does not exist");
            return false;
        }
    
        /*
         * Al ser una copia no se comprueba el destino
        if(is_file($sPathTargetFile)) 
        {
            $this->add_error("File not copied. $sPathTargetFile. File already exists");
            return false;
        }*/
        
        $isCopied = copy($sPathSourceFile, $sPathTargetFile);
        if(!$isCopied)
        {
            $this->add_error("$sPathSourceFile not copied. copy() failed.");
            return false;
        }
        $this->arMessage[] = "File: $sPathSourceFile copied";
        return true;        
    }
    
    /**
     * La carpeta de destino se define con setsPathFolderLog 
     * @param string $sTextToWrite ej START.. $sTextToWrite ..END
     * @param string $sFileName ej $sFileName.log
     * @param string $sFileTitle ej Application $sFileTitle on nombremes 2013
     */
    public function writelog($sTextToWrite="",$sFileName="",$sFileTitle="errors") 
    {
        if($this->sPathFolderLog)
        {   
            //Estos auxiliares permiten que configure el objeto cn otros parametros
            //escriba con writelog, esto los machacaria (con path_folder_log y $sFileNameExtra puesto que necesito los atributos para usar
            //add_content.  Finalmente se dejaria como estaba
            $sAuxPathFolder = $this->sPathFolderTarget;
            $sAuxFilename = $this->sFilenameTarget;
            
            //suele ser el directorio logs/custom
            $sPathFolderTarget = $this->sPathFolderLog;
            //fix_folderpath añade el DS que le corresponde
            $sPathLogsDirDs = $this->fix_folderpath($sPathFolderTarget);

            $sDay=date("d");$sMonth=date("m");$sYear=date("Y");$sHour=date("H");
            $sMin=date("i");$sSec=date("s");$sMonthName=date("F");

            if(!$sFileName)
            {
                //si se ha definido un usuario en sesion se añade
                if($this->sLogUserOwner) $sFileName = $this->sLogUserOwner."_";
                $sFileName .= date("Ymd").".log";
            }
            else
                $sFileName .= ".log";
            
            $this->sFilenameTarget = $sFileName;
            $sPathFileToOpen = $sPathLogsDirDs.$this->sFilenameTarget;

            //TITULO DEL CONTENIDO DEL ARCHIVO
            $sLogLineTitle = "======================================$this->sOsNL";
            $sLogLineTitle .= sprintf("Application $sFileTitle on %s %04d$this->sOsNL",$sMonthName,$sYear);
            $sLogLineTitle .= "======================================$this->sOsNL$this->sOsNL";
            //si ya existe no creo titulo de archivo
            if(file_exists($sPathFileToOpen)) $sLogLineTitle="";

            $sContentFirstLineFormat = "[%02d-%02d-%04d %02d:%02d:%02d]";
            $sContentFirstLine = sprintf($sContentFirstLineFormat,$sDay,$sMonth,$sYear,$sHour,$sMin,$sSec);
            $sContentFirstLine .= $this->sOsNL;

            $sContentToWrite = $sLogLineTitle;
            $sContentToWrite .= "START".$sContentFirstLine;
            $sContentToWrite .= $sTextToWrite.$this->sOsNL;
            $sContentToWrite .= "END".$sContentFirstLine;
            //se debe tener configurados estos atributos antes de llamar a add_content
            //sFilenameTarget y path_folder_target. $this->sPathFolderLog
            $this->sPathFolderTarget = $this->sPathFolderLog;
            $this->add_content($sContentToWrite);
            //Despues de escribir restauramos la ruta original de la carpeta y el nombre del archivo.
            $this->sPathFolderTarget = $sAuxPathFolder;
            $this->sFilenameTarget = $sAuxFilename;
        }
        //no existe la ruta de log destino
        else
        {
            $this->add_error("Log not written path_folder_log not supplied");
        }
    }
  
    public function move()
    {
        $sPathFolderSourceDs = $this->fix_folderpath($this->sPathFolderSource);
        $sPathFolderTargetDs = $this->fix_folderpath($this->sPathFolderTarget);
        
        if(!is_dir($sPathFolderSourceDs))
        {
            $this->add_error("File not moved. $sPathFolderSourceDs is not a valid folder");
            return false;
        }
        
        if(!is_dir($sPathFolderTargetDs))
        {
            $this->add_error("File not moved. $sPathFolderTargetDs is not a valid folder");
            return false;
        }
        
        $sPathSourceFile = $sPathFolderSourceDs.$this->sFilenameSource;
        $sPathTargetFile = $sPathFolderTargetDs.$this->sFilenameTarget;
        
        if(!is_file($sPathSourceFile)) 
        {
            $this->add_error("File not moved. $sPathSourceFile File does not exist");
            return false;
        }
        
        if(is_file($sPathTargetFile)) 
        {
            $this->add_error("File not moved. $sPathTargetFile. File already exists");
            return false;
        }
        
        $isCopied = copy($sPathSourceFile, $sPathTargetFile);
        if(!$isCopied)
        {
            $this->add_error("$sPathSourceFile not moved. copy() failed.");
            return false;
        }
        unlink($sPathSourceFile);
        $this->arMessage[] = "File: $sPathSourceFile moved";
        return true;
    }

    
    public function add_content($sContent)
    {
        if($sContent) $sContent = $sContent.$this->sOsNL;
        $sPathFolderDs = $this->fix_folderpath($this->sPathFolderTarget);
        if(!is_dir($sPathFolderDs))
        {
            $this->add_error("$this->sFilenameTarget not modified. $sPathFolderDs is not a valid folder");
            return false;    
        }
        
        $sPathFile = $sPathFolderDs.$this->sFilenameTarget;
        if(!is_file($sPathFile))
            $this->add_error("$this->sFilenameTarget not modified. File does not exist. Will try to create it");
            //return false;
        
        //TODO y unix?
        //ab: Actualización del archivo al final de la ultima linea. b, solo para
        //WINDOWS 
        $oCursor=fopen($sPathFile,"ab");
        if($oCursor===false)
        {
            $this->add_error("$this->sFilenameTarget not modified. fopen() failed");
            return false;
        }
        //int fwrite ( resource $handle , string $string [, int $length ] )  
        $mxWritten = fwrite($oCursor,$sContent);
        if($mxWritten===false)
        {
            fclose($oCursor);
            $this->add_error("$this->sFilenameTarget not modified. fwrite() failed");
            return false;
        }
        fclose($oCursor);
        $this->arMessage[] = "File: $sPathFile modified. N. Chars: $mxWritten";
        return true;  
    }
    
    public function replace()
    {
        $sPathFolderSource = $this->fix_folderpath($this->sPathFolderSource);
        $sPathFolderTarget = $this->fix_folderpath($this->sPathFolderTarget);
        
        if(!is_dir($sPathFolderSource))
        {
            $this->add_error("File not replaced. $sPathFolderSource is not a valid folder");
            return false;
        }
        
        if(!is_dir($sPathFolderTarget))
        {
            $this->add_error("File not replaced. $sPathFolderTarget is not a valid folder");
            return false;
        }
        
        $sPathSourceFile = $sPathFolderSource.$this->sFilenameSource;
        $sPathTargetFile = $sPathFolderTarget.$this->sFilenameTarget;
        
        if(!is_file($sPathSourceFile)) 
        {
            $this->add_error("File not replaced. $sPathSourceFile File does not exist");
            return false;
        }
        
        if(is_file($sPathTargetFile)) unlink($sPathTargetFile);
                
        $isCopied = copy($sPathSourceFile, $sPathTargetFile);
        if(!$isCopied)
        {
            $this->add_error("$sPathSourceFile not replaced. copy() failed.");
            return false;
        }
        $this->arMessage[] = "File: $sPathSourceFile moved";
        return true;        
    }
    
    public function source_remove()
    {
        $sPathFolder = $this->fix_folderpath($this->sPathFolderSource);
        if(!is_dir($sPathFolder))
        {
            $this->add_error("$this->sFilenameSource not created. $sPathFolder is not a valid folder");
            return false;    
        }
        
        $sPathFile = $sPathFolder.$this->sFilenameSource;
        if(!is_file($sPathFile))
        {
            $this->add_error("$this->sFilenameSource not removed. File does not exist");
            return false;
        }
        
        //soruce file
        //x: Creación y apertura para sónlo escritura; coloca el puntero al principio del archivo.
        $isRemoved = unlink($sPathFile); 
        if(!$isRemoved)
        {
            $this->add_error("$sPathFile not created. unlink() failed.");
            return false;
        }
        $this->arMessage[] = "File: $sPathFile removed";
        return true;        
    }
    
    public function target_remove()
    {
        $sPathFolder = $this->fix_folderpath($this->sPathFolderTarget);
        if(!is_dir($sPathFolder))
        {
            $this->add_error("$this->sFilenameTarget not created. $sPathFolder is not a valid folder");
            return false;    
        }
        
        $sPathFile = $sPathFolder.$this->sFilenameTarget;
        if(!is_file($sPathFile))
        {
            $this->add_error("$this->sFilenameTarget not removed. File does not exist");
            return false;
        }
        
        //soruce file
        //x: Creación y apertura para sónlo escritura; coloca el puntero al principio del archivo.
        $isRemoved = unlink($sPathFile); 
        if(!$isRemoved)
        {
            $this->add_error("$sPathFile not created. unlink() failed.");
            return false;
        }
        $this->arMessage[] = "File: $sPathFile removed";
        return true;    
    }
    
    public function rename()
    {
        $sPathFolderSource = $this->fix_folderpath($this->sPathFolderSource);
        
        if(!is_dir($sPathFolderSource))
        {
            $this->add_error("File not renamed. $sPathFolderSource is not a valid folder");
            return false;
        }
        
        $sPathSourceFile = $sPathFolderSource.$this->sFilenameSource;
        $sPathTargetFile = $sPathFolderSource.$this->sFilenameTarget;
        
        if(!is_file($sPathSourceFile)) 
        {
            $this->add_error("File not renamed. $sPathSourceFile File does not exist");
            return false;
        }
        
        if(is_file($sPathTargetFile)) unlink($sPathTargetFile);
                
        $isRenamed = rename($sPathSourceFile, $sPathTargetFile);
        if(!$isRenamed)
        {
            $this->add_error("$sPathSourceFile not renamed. rename() failed.");
            return false;
        }
        $this->arMessage[] = "File: $sPathSourceFile renamed to $this->sFilenameTarget";
        return true;
        //rename("/tmp/archivo_tmp.txt", "/home/user/login/docs/mi_archivo.txt");
    }
    
    protected function get_upload_count($sInputFileName)
    {
        $iCount = 0;
        foreach($_FILES[$sInputFileName]["name"] as $sName)
            if($sName!=="") $iCount++;
        return $iCount;
    }    
    /**
     * 
     * @param string $iFile
     */
    protected function get_upload_data($sInputFileName,$iFile)
    {
        $arFile = array();
        $arFile["name"] = $_FILES[$sInputFileName]["name"][$iFile];
        $arFile["type"] = $_FILES[$sInputFileName]["type"][$iFile];
        $arFile["tmp_name"] = $_FILES[$sInputFileName]["tmp_name"][$iFile];
        $arFile["error"] = $_FILES[$sInputFileName]["error"][$iFile];
        $arFile["size"] = $_FILES[$sInputFileName]["size"][$iFile];
        return $arFile;
    }
    
    protected function get_upload_indexes($sInputFileName)
    {
        $arIndex = array();
        foreach($_FILES[$sInputFileName]["name"] as $i=>$sName)
            if($sName!=="") $arIndex[] = $i;
        return $arIndex;
    }  
    
    //TODO para Optimizar para iUploadIndex NULL y !==NULL codigo similar
    protected function upload_by_index($iIndex)
    {
        
    }
    
    /**
     * Antes de llamar a este metodo hay que configurar la ruta del directorio de destino donde
     * se desea subir el archivo con set_path_target().
     * Se debe asignar el nombre del archivo de destino con set_filename_target().
     * Hay que configurar el nombre del control (element html) input file con set_input_file_name();
     * con is_error() se puede comprobar si ha habido algún problema
     * con get_array_message() se puede identificar el estado de la operación
     * @param mixed $sFileName array|string Si se utiliza este paránmetro se omite el valor asignado desde set_filename_target()
     * @return boolean Estado de la operación
     */
    public function upload($sFileName=NULL)
    {
        //array de archivos
        if(strstr($this->sNameInputFile,"[]"))
        {
            $sInputFileName = str_replace("[]","",$this->sNameInputFile);
            $iNumFiles = $this->get_upload_count($sInputFileName);
            
            if($iNumFiles>0)
            {
                $arIndexes = $this->get_upload_indexes($sInputFileName);
                //TODO Optimizar para NULL y !==NULL codigo similar
                if($this->iUploadIndex!==NULL)
                {
                    if(in_array($this->iUploadIndex,$arIndexes))
                    {        
                        $arFile = $this->get_upload_data($sInputFileName,$this->iUploadIndex);
                        $sOriginalName = $arFile["name"];
                        if($arFile["error"] == UPLOAD_ERR_OK)
                        {
                            $sPathFileTemp = $arFile["tmp_name"];
                            $sPathFolderTarget = $this->fix_folderpath($this->sPathFolderTarget);

                            if(!is_dir($sPathFolderTarget))
                            {
                                $this->add_error("File $sOriginalName($this->iUploadIndex) not uploaded. $sPathFolderTarget is not a valid folder");
                                return false;
                            }

                            if(!$sFileName) $sFileName = $this->sFilenameTarget;

                            $sPathFileTarget = $sPathFolderTarget.$sFileName;
                            if(file_exists($sPathFileTarget))
                                unlink($sPathFileTarget);

                            $isUploadMoved = move_uploaded_file($sPathFileTemp, $sPathFileTarget);
                            //bug($sPathFileTemp,"temp"); bug($sPathFileTarget,"target"); bug($isUploadMoved,"ismovido");
                            if(!$isUploadMoved)
                            {
                                $this->add_error("File $sOriginalName($this->iUploadIndex) not uploaded. It was not able to move file from temp folder: $sPathFileTemp to $sPathFolderTarget");
                                return false;
                            }
                            $this->arMessage[] = "File $sOriginalName($this->iUploadIndex) successful uploaded";
                            return true;
                        }
                        elseif($arFile["error"] == UPLOAD_ERR_INI_SIZE)
                        {
                            $this->add_error("File $sOriginalName($this->iUploadIndex) not uploaded. File size is larger than which is defined in php.ini");
                            return false;
                        }
                        else
                        {
                            $this->add_error("File $sOriginalName($this->iUploadIndex) not uploaded. Error: ".$arFile["error"]);
                            return false;
                        }                       
                    }//si $_files[indice] tiene datos
                    else
                    {
                        $this->add_error("File $sOriginalName($this->iUploadIndex) not uploaded. Error: no data to upload");
                        return false;                        
                    }    
                }
                else 
                {
                    for($i=0; $i<$iNumFiles; $i++)
                    {
                        //Puede que del listado se seleccionen controles aleatorios. Ejemplo si tengo 5 cajas
                        //el usuario puede haber seleccionado la segunda y cuarta,así pues en arIndexes solo se guardaria el indice
                        //1 y 3. Si el indice no tiene datos se pasa al siguiente
                        if(!in_array($i,$arIndexes)) 
                            continue;
                        $arFile = $this->get_upload_data($sInputFileName,$i);
                        $sOriginalName = $arFile["name"];
                        if($arFile["error"] == UPLOAD_ERR_OK)
                        {
                            $sPathFileTemp = $arFile["tmp_name"];
                            $sPathFolderTarget = $this->fix_folderpath($this->sPathFolderTarget);

                            if(!is_dir($sPathFolderTarget))
                            {
                                $this->add_error("File $sOriginalName($i) not uploaded. $sPathFolderTarget is not a valid folder");
                                return false;
                            }

                            if(!$sFileName) $sFileName = $this->sFilenameTarget."_$i";

                            $sPathFileTarget = $sPathFolderTarget.$sFileName;
                            if(file_exists($sPathFileTarget))unlink($sPathFileTarget);

                            $isUploadMoved = move_uploaded_file($sPathFileTemp, $sPathFileTarget);
                            //bug($sPathFileTemp,"temp"); bug($sPathFileTarget,"target"); bug($isUploadMoved,"ismovido");
                            if(!$isUploadMoved)
                            {
                                $this->add_error("File $sOriginalName($i) not uploaded. It was not able to move file from temp folder: $sPathFileTemp to $sPathFolderTarget");
                                return false;
                            }
                            $this->arMessage[] = "File $sOriginalName($i) successful uploaded";
                            return true;
                        }
                        elseif($arFile["error"] == UPLOAD_ERR_INI_SIZE)
                        {
                            $this->add_error("File $sOriginalName($i) not uploaded. File size is larger than which is defined in php.ini");
                            return false;
                        }
                        else
                        {
                            $this->add_error("File $sOriginalName($i) not uploaded. Error: ".$arFile["error"]);
                            return false;
                        }                    
                    }//fin for i in files 
                }//si no se ha definido un indice de carga
            }
            else 
            {
                $this->add_error("Files not uploaded. Error: No files received!");
                return false;                
            }
        } 
        //no es un array de archivos
        else
        {    //filSubir es el input type=file con name=filSubir
            if($_FILES[$this->sNameInputFile]["error"] == UPLOAD_ERR_OK)
            {
                $sPathFileTemp = $_FILES[$this->sNameInputFile]["tmp_name"];
                $sPathFolderTarget = $this->fix_folderpath($this->sPathFolderTarget);

                if(!is_dir($sPathFolderTarget))
                {
                    $this->add_error("File not uploaded. $sPathFolderTarget is not a valid folder");
                    return false;
                }

                if(empty($sFileName)) $sFileName = $this->sFilenameTarget;

                $sPathFileTarget = $sPathFolderTarget . $sFileName;
                if(file_exists($sPathFileTarget))unlink($sPathFileTarget);

                $isUploadMoved = move_uploaded_file($sPathFileTemp, $sPathFileTarget);
                //bug($sPathFileTemp,"temp"); bug($sPathFileTarget,"target"); bug($isUploadMoved,"ismovido");
                if(!$isUploadMoved)
                {
                    $this->add_error("File not uploaded. It was not able to move file from temp folder: $sPathFileTemp to $sPathFolderTarget");
                    return false;
                }
                $this->arMessage[] = "File successful uploaded";
                return true;
            }
            elseif($_FILES[$this->sNameInputFile]["error"] == UPLOAD_ERR_INI_SIZE)
            {
                $this->add_error("File not uploaded. File size is larger than which is defined in php.ini");
                return false;
            }
            else
            {
                $this->add_error("File not uploaded. Error: ".$_FILES[$this->sNameInputFile]["error"]);
                return false;
            }
        }
    }
    
    private function load_ds_by_os()
    {
        $this->sOsDs = DIRECTORY_SEPARATOR;
        //bug("directory_separator: $this->sOsDs ");die;
//        switch($this->sServerOS) 
//        {
//            case "windows":
//            case "w":
//                $this->sOsDs = "\\";
//            break;
//            case "linux":
//            case "l":
//            case "unix":
//            case "u":
//            case "mac":
//            case "m":
//            case "ios":
//                $this->sOsDs = "/";
//            break;
//            default:
//                $this->arMessage[] = "server os not provided";
//                $this->sOsDs = "/";
//            break;
//        }
    }
    
    private function load_nl_by_os()
    {
        switch($this->sServerOS) 
        {
            case "windows":
            case "w":
                $this->sOsNL = "\r\n";
            break;            
            case "linux":
            case "l":
            case "unix":
            case "u":
                $this->sOsNL = "\n";
            break;
            case "mac":
            case "m":
            case "ios":
                $this->sOsNL = "\r";
            break;

            default:
                $this->arMessage[] = "server os not provided";
                $this->sOsNL = "\n";
            break;
        }
    }
    
    public function target_exists()
    {
        $sPathFolderSource = $this->fix_folderpath($this->sPathFolderTarget);
        $sPathTargetFile = $sPathFolderSource.$this->sFilenameTarget;
        if(is_file($sPathTargetFile)) return true;
        return false;
    }
    
    public function target_folder_exists()
    {
        $sPathFolderDs = $this->fix_folderpath($this->sPathFolderTarget);
        if(is_lastchar($sPathFolderDs,$this->sOsDs))
            remove_lastchar($sPathFolderDs);
        
        return is_dir($sPathFolderDs);
    }    
    
    public function create_folder()
    {
        $sPathFolderDs = $this->fix_folderpath($this->sPathFolderTarget);
        if(is_lastchar($sPathFolderDs,$this->sOsDs))
            remove_lastchar($sPathFolderDs);

        if(is_dir($sPathFolderDs))
        {
            $this->add_error("Folder $this->sFilenameTarget not created. It already exists");
            return false;
        }
  
        //TODO mkdir acepta más parametros ajusta las clase a estos.
        $oStatusCreation = mkdir($sPathFolderDs,0755);
        if($oStatusCreation == false)
        {
            $this->add_error("$sPathFolderDs not created. mkdir() failed. Cursor:".var_export($oStatusCreation,true));
            return false;
        }
        $this->arMessage[] = "Folder: $sPathFolderDs created";
        return true;        
    }
    
    //=======================
    //         SETS
    //=======================
//    private function set_error($sMessage)
//    {
//        $this->isError = FALSE;
//        $this->arMessage = array();
//        if($sMessage)
//        {   
//            $this->isError = TRUE;
//            $this->arMessage[] = $sMessage;
//        }
//    } 
    
    private function add_error($sMessage)
    {
        $this->isError = TRUE;
        if($sMessage)
            $this->arMessage[] = $sMessage;
    }
    
    public function set_path_target($sPathFile){$this->_path_target_file = $sPathFile;}
    public function set_path_source($sPathFile){$this->_path_source_file = $sPathFile;}
    public function set_filename_source($sFileName){$this->sFilenameSource = $sFileName;}
    public function set_filename_target($sFileName){$this->sFilenameTarget = $sFileName;}
    public function set_path_folder_source($sPathFolder){$this->sPathFolderSource = $sPathFolder;}
    public function set_path_folder_target($sPathFolder){$this->sPathFolderTarget = $sPathFolder;}
    public function set_target_content($sContent){$this->sTargetContent = $sContent;}
    /**
     * En caso de querer subir un archivo
     * @param string $sInputFileName Nombre del control tipo "file" necesario para
     * cargar el archivo que se enviarón al servidor
     */
    public function set_input_file_name($sInputFileName){$this->sNameInputFile = $sInputFileName;}
    public function set_path_folder_log($sPathFolder){$this->sPathFolderLog = $sPathFolder;}
    public function set_log_user_owner($sUserSession){$this->sLogUserOwner=$sUserSession;}
    public function set_upload_index($iUpload){$this->iUploadIndex=$iUpload;}
    //=======================
    //        GETS
    //=======================
    public function is_error(){return $this->isError;}
    public function get_array_message(){return $this->arMessage;}
    public function get_path_folder_source(){return $this->sPathFolderSource;}
    public function get_path_folder_target(){return $this->sPathFolderTarget;}
    public function get_target_file_name(){return $this->sFilenameTarget;}
    public function get_upload_index(){return $this->iUploadIndex;}
}