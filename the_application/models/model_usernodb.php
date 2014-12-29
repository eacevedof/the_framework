<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.2.0
 * @name ModelUser
 * @file model_usernodb.php 
 * @date 16-06-2014 11:12 (SPAIN)
 * @observations: Application No DB Model 
 * Este modelo se ha creado con el fin de evitar validación contra bd ya que se utilizará para el acceso a multiples
 * bd.
 * Se mejora permitiendo la carga desde archivo xml
 */
include_once("theapplication_model.php");

class ModelUser extends TheApplicationModel
{
    private $_code_type; //int(4)
    private $_bo_login; //varchar(15)
    private $_bo_password; //varchar(15)
    private $_first_name; //varchar(100)
    private $_language; //varchar(50)
    private $_last_name; //varchar(100)
    private $_md_login; //varchar(15)
    private $_md_password; //varchar(15)
    private $_id_start_module; //numeric(18)
    private $_id_seller; //numeric(18)
    private $_path_picture; //varchar(100)
    private $oSeller;
    
    private $arPermissions;
    private $arModuleMenu;
   
    private $sDataownerTable;
    private $sDataownerTablefield;
    private $arDataownerTablekeys;
    private $sDataownerView;
    
    private $arUsers = array();
    private $sPathXml;
    
    public function __construct($id=NULL)
    {
        parent::__construct();
        $this->sPathXml = TFW_PATH_FOL_ROOTDS."the_application/models/uc/model_user.xml";
        //necesita tener predefinido sPathXml para que evite la carga desde array
        $this->load_users();
        $this->arModuleMenu = array();
        $this->arPermissions = array();
        if($id!=NULL) $this->_id = $id;
        
    }
    
    private function load_users()
    {
        //$this->sPathXml=true;
        if($this->sPathXml)
        {
            $this->load_users_xml();
        }
        else
        {
            $this->load_users_array();
        }
    }//load_users    
    
    private function load_users_array()
    {
        $this->arUsers[1000] = array
        (    
            "code_type" => ""
            ,"insert_user" => ""
            ,"delete_user" => ""
            ,"update_user" => ""
            ,"update_date" => ""
            ,"insert_date" => ""
            ,"delete_date" => ""
            ,"is_erpsent" => "0"
            ,"insert_platform" => "7"
            ,"code_erp" => ""
            ,"id_seller" => ""
            ,"is_enabled" => "1"
            
            ,"id" => "1000"
            ,"id_start_module" => "14"
            ,"bo_login" => "1000"
            ,"bo_password" => "1000"
            ,"md_login" => ""
            ,"md_password" => "" 
            
            ,"first_name" => "FIRST"
            ,"last_name" => "READER"
            ,"description" => "FISRT READER (1000)"
            
            ,"language" => "english"

            ,"path_picture" => "/images/pictures/users/user_100/user_1000_0.png"
        );
        
        $this->arUsers[1001] = array
        (    
            "code_type" => ""
            ,"insert_user" => ""
            ,"delete_user" => ""
            ,"update_user" => ""
            ,"update_date" => ""
            ,"insert_date" => ""
            ,"delete_date" => ""
            ,"is_erpsent" => "0"
            ,"insert_platform" => "7"            
            ,"code_erp" => ""
            ,"id_seller" => ""            
            ,"is_enabled" => "1"
            
            ,"id" => "1001"
            ,"id_start_module" => "14"
            ,"bo_login" => "1001"
            ,"bo_password" => "1001"
            ,"md_login" => ""
            ,"md_password" => "" 
            
            ,"first_name" => "SECOND"
            ,"last_name" => "READER"
            ,"description" => "SECOND READER (1001)"
            ,"language" => "english"
            ,"path_picture" => "/images/pictures/users/user_100/user_1000_0.png"
        );
        $this->arUsers[1002] = array
        (    
            "code_type" => ""
            ,"insert_user" => ""
            ,"delete_user" => ""
            ,"update_user" => ""
            ,"update_date" => ""
            ,"insert_date" => ""
            ,"delete_date" => ""
            ,"is_erpsent" => "0"
            ,"insert_platform" => "7"            
            ,"code_erp" => ""
            ,"id_seller" => ""            
            ,"is_enabled" => "1"
            
            ,"id" => "1002"
            ,"id_start_module" => "14"
            ,"bo_login" => "1002"
            ,"bo_password" => "1002"
            ,"md_login" => ""
            ,"md_password" => "" 
            
            ,"first_name" => "nombre del tercero"
            ,"last_name" => "apellido del tercero"
            ,"description" => "tercer  lector (1002)"
            ,"language" => "english"
            ,"path_picture" => "/images/pictures/users/user_100/user_1000_0.png"
        );        
    }//load_users_array
    
    private function load_users_xml()
    {
        //$sPathXml = TFW_PATH_FOL_ROOTDS."the_application/models/uc/model_user.xml";
        //bug($sPathXml);die;
        if(is_file($this->sPathXml))
        {    
            $oXml = simplexml_load_file($this->sPathXml);
            if($oXml===FALSE)
            {
                $this->log_error("ModelUser->load_users_xml(): Xml $sPathXml mal formado");
            }
            else
            {
                unset($oXml->user[0]);//quito la plantilla
                //Hay al menos un usuario
                if($oXml->user[1])
                {
                    $arSameProps = $this->get_same_attribs($oXml->user[1]);
                    //Recorro todos los usuarios
                    foreach($oXml->user as $oUser)
                    {
                        $arUser = (array)$oUser;
                        //Recorro todas sus propiedades
                        foreach($arSameProps as $sUserProperty)
                        {
                            //TODO: Habría que tratar los textos con valor NULL
                            $this->arUsers[$arUser["id"]][$sUserProperty] = $arUser[$sUserProperty];
                        }//ferach properties
                    }//foreach users
                }
                //No hay usuarios
//                else
//                {
//                    //El array de usuarios se queda vacio
//                }
            }//else $oXml=ok
        }
        //Error ruta erronea
        else
        {
            $sMessage = "ModelUser->load_users_xml(): Error al recuperar $sPathXml comprobar ruta";
            $this->add_error($sMessage);
            $this->log_error($sMessage);
        }
    }//load_users_xml
    
    public function load_by_id()
    { 
        $arRow = $this->arUsers[$this->_id];
        //$this->row_assign($arRow);
        //bug($arRow,"load_by_id $sSQL");
        $this->_code_type = $arRow["code_type"];
        $this->_insert_user = $arRow["insert_user"];
        $this->_delete_user = $arRow["delete_user"];
        $this->_id = $arRow["id"];
        $this->_update_user = $arRow["update_user"];
        $this->_bo_login = $arRow["bo_login"];
        $this->_bo_password = $arRow["bo_password"];
        $this->_code_erp = $arRow["code_erp"];
        $this->_insert_date = $arRow["insert_date"];
        $this->_delete_date = $arRow["delete_date"];
        $this->_description = $arRow["description"];
        $this->_is_enabled = $arRow["is_enabled"];
        $this->_first_name = $arRow["first_name"];
        $this->_is_erpsent = $arRow["is_erpsent"];
        $this->_language = $arRow["language"];
        $this->_last_name = $arRow["last_name"];
        $this->_md_login = $arRow["md_login"];
        $this->_md_password = $arRow["md_password"];
        $this->_update_date = $arRow["update_date"];
        $this->_insert_platform = $arRow["insert_platform"];
        $this->_id_start_module = $arRow["id_start_module"];
        $this->_id_seller = $arRow["id_seller"];
        $this->_path_picture = $arRow["path_picture"];
    }//load_by_id

    public function load_by_bo_login()
    {
        $arRow = array();
        if($this->_bo_login && $this->_bo_password)
        {
            foreach($this->arUsers as $arUser)
                if($arUser["bo_login"]==$this->_bo_login && $arUser["bo_password"]==$this->_bo_password)
                    $arRow = $arUser;
        }
        //bug($arRow);die;
        //0: carga atributos sin bd
        $this->row_assign($arRow,0);
    }//load_by_bo_login

    public function load_by_md_login()
    {
        $arRow = array();
        if($this->_md_login && $this->_md_password)
        {
           foreach($this->arUsers as $arUser)
                if($arUser["md_login"]==$this->_md_login && $arUser["md_password"]==$this->_md_password)
                    $arRow = $arUser;
        }
        //0: carga atributos sin bd
        $this->row_assign($arRow,0);
    }//load_by_md_login

    /**
     * Compara el objeto el modelo actual con un objeto XML equivalente y obtiene todas las propiedades asignables
     * @param Object $oSourceObject Xml node object similar to a table row
     * @return array Properties are fieldnames
     */
    protected function get_same_attribs($oSourceObject)
    {
        $arThisProperties = get_object_vars($this);
        $arThisProperties = array_keys($arThisProperties);
        
        $arSourceProperties = get_object_vars($oSourceObject);
        $arSourceProperties = array_keys($arSourceProperties);
        
        $arSameProps = array();
        foreach($arThisProperties as $sThisProperty)
            //Si tiene _ entonces es un campo en tabla
            if(is_firstchar($sThisProperty,"_"))
            {   
                $sSourcePropName = $sThisProperty;
                remove_firstchar($sSourcePropName);
                
                if(in_array($sSourcePropName,$arSourceProperties))
                    $arSameProps[$sThisProperty] = $sSourcePropName;
            }
        return $arSameProps;
    }//get_same_attribs
        
    //Configuración del menu
    public function get_module_menu()
    {
        $arRow = array();
        /*   SELECT id_module
         * , module 
        ,MIN(is_select_menu) AS is_select
        ,MIN(is_insert_menu) AS is_insert*/
        //No es developer
        if($this->_id!="-10")
        {    
            //Tiene que ser un listado sino el menú no lo entiende
            $arRow[] = array
            (   
                "id_module"=>"14"
                ,"module" => "transfers"
                ,"is_select" => "1"
                ,"is_insert" => "0"
            );
            $arRow[] = array
            (   
                "id_module"=>"16"
                ,"module" => "suspicions"
                ,"is_select" => "1"
                ,"is_insert" => "1"
            );
        }
        else //developer
            $arRow[] = array
            (   
                "id_module"=>"11"
                ,"module" => "modulebuilder"
                ,"is_select" => "1"
                ,"is_insert" => "1"
            );            
        return $arRow;
    }//get_module_menu
        
    //Escritura sobre módulos (se valida en controlador)
    /**
     * Se utiliza en constructor de TAppController para obtener el permiso por modulo (el controlador actual)
     * @param string $sModule
     * @return array
     */    
    public function get_permissions($sModule)
    {
        $arRow = array();
        $arRow["transfers"] = array
        (   
            "id_module"=>"14"
            ,"module" => "transfers"
            ,"is_select_menu" => "1"
            ,"is_insert_menu" => "0"
            ,"is_insert" => "0"
            ,"is_select" => "1"
            ,"is_update" => "0"
            ,"is_delete" => "0"
            ,"is_pick" => "0"
            ,"is_quarantine" => "0"
            ,"is_read" => "0"
            ,"is_excelexport" => "0"
            ,"is_print" => "0"
        );
        
        $arRow["suspicions"] = array
        (   
            "id_module"=>"16"
            ,"module" => "suspicions"
            ,"is_select_menu" => "1"
            ,"is_insert_menu" => "1"
            ,"is_insert" => "1"
            ,"is_select" => "1"
            ,"is_update" => "1"
            ,"is_delete" => "1"
            ,"is_pick" => "1"
            ,"is_quarantine" => "1"
            ,"is_read" => "1"
            ,"is_excelexport" => "1"
            ,"is_print" => "1"
        );
        return $arRow[$sModule];
    }//get_permissions
        
    /**
     * NO SE USA.
     * Se usó para guardar todos los permisos en cache.
     * @return array tipo array(0=>array(id_module=>x,module=>mmmm,is_select_menu=>...is_read=>),1=>..)
     */    
    public function get_modules_permissions()
    {        
        $arRow = array();
        $arRow[] = array
        (   
            "id_module"=>"14"
            ,"module" => "transfers"
            ,"is_select_menu" => "1"
            ,"is_insert_menu" => "1"
            ,"is_insert" => "1"
            ,"is_select" => "1"
            ,"is_update" => "1"
            ,"is_delete" => "0"
            ,"is_pick" => "1"
            ,"is_quarantine" => "1"
            ,"is_read" => "0"
            ,"is_excelexport" => "0"
            ,"is_print" => "0"            
        );        
        $arRow[] = array
        (   
            "id_module"=>"16"
            ,"module" => "suspicions"
            ,"is_select_menu" => "1"
            ,"is_insert_menu" => "1"
            ,"is_insert" => "1"
            ,"is_select" => "1"
            ,"is_update" => "1"
            ,"is_delete" => "0"
            ,"is_pick" => "1"
            ,"is_quarantine" => "1"
            ,"is_read" => "0"
            ,"is_excelexport" => "0"
            ,"is_print" => "0"            
        );
        return $arRow;      
    }//get_module_permissions
    
    public function load_module_permissions(){$this->arPermissions = $this->get_modules_permissions();}//load_module_permissions
    public function load_module_menu(){$this->arModuleMenu = $this->get_module_menu();}//load_module_menu
    
    public function is_dataowner(){return TRUE;}//is_dataowner
    
    public function is_not_dataowner(){return FALSE;}//is_not_dataowner
    
    //=======================
    //         GETS
    //=======================
    public function get_code_type(){ return $this->_code_type; }
    public function get_bo_login(){ return $this->_bo_login; }
    public function get_bo_password(){ return $this->_bo_password; }
    public function get_first_name(){ return $this->_first_name; }
    public function get_language(){ return $this->_language; }
    public function get_last_name(){ return $this->_last_name; }
    public function get_md_login(){ return $this->_md_login; }
    public function get_md_password(){ return $this->_md_password; }
    public function get_id_profile(){ return $this->_id_profile; }
    public function get_path_picture(){ return $this->_path_picture; }
    public function get_id_start_module(){ return $this->_id_start_module;}
    public function get_start_module(){return TFW_DEFAULT_LOGGED_CONTROLLER;}
    
    public function get_cached_module_menu(){return $this->arModuleMenu;}
    public function get_cached_module_permissions(){return $this->arPermissions;}
    public function get_cached_module_permission($sModule)
    {
        foreach($this->arPermissions as $arPermission)
        {
            if($arPermission["module"]==$sModule)
                return $arPermission;
        }
        return array();
    }
    
    public function get_lowest_group(){return "TRANSFERREAD";}
    
    public function get_id_seller(){return $this->_id_seller;}
    public function get_seller(){return NULL;}
    
    //=======================
    //         SETS
    //=======================    
    public function set_code_type($value){ $this->_code_type = $value; }
    public function set_bo_login($value){ $this->_bo_login = $value; }
    public function set_bo_password($value){ $this->_bo_password = $value; }
    public function set_first_name($value){ $this->_first_name = $value; }
    public function set_language($value){ $this->_language = $value; }
    public function set_last_name($value){ $this->_last_name = $value; }
    public function set_md_login($value){ $this->_md_login = $value; }
    public function set_md_password($value){ $this->_md_password = $value; }
    public function set_id_start_module($value){ $this->_id_start_module = $value; }
    public function set_path_picture($value){$this->_path_picture = $value;}
    
    public function set_dataowner_table($value){ $this->sDataownerTable = $value; }
    public function set_dataowner_tablefield($value){ $this->sDataownerTablefield = $value; }
    public function set_dataowner_keys($array){ $this->arDataownerTablekeys = $array; }
    public function set_dataowner_view($value){ $this->sDataownerView = $value; }
    public function set_id_seller($value){ $this->_id_seller = $value; }
    public function set_seller($oValue){ $this->oSeller = $oValue; }
    public function set_pathxml($value){ $this->sPathXml = $value; }
}
