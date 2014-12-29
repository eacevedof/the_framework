<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.2.5
 * @name ModelUser
 * @file model_user.php 
 * @date 05-10-2014 03:01 (SPAIN)
 * @observations: Application Model 
 */
import_appmain("model");
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
    
    private $_login_hour;//varchar 6
    private $_login_ip;//varchar 15
    private $_login_latitude;//varchar 50
    private $_login_length;//varchar 50
    private $_login_session;//varchar 100
        
    private $arPermissions;
    private $arModuleMenu;
   
    private $sDataownerTable;
    private $sDataownerTablefield;
    private $arDataownerTablekeys;
    private $sDataownerView;
    
    public function __construct($code_type=NULL,$insert_user=NULL,$delete_user=NULL,$id=NULL
            ,$update_user=NULL,$bo_login=NULL,$bo_password=NULL,$code_erp=NULL,$insert_date=NULL
            ,$delete_date=NULL,$description=NULL,$enabled=NULL,$first_name=NULL,$is_erpsent=NULL,$language=NULL
            ,$last_name=NULL,$md_login=NULL,$md_password=NULL,$update_date=NULL,$insert_platform=NULL
            ,$id_start_module=NULL,$id_seller=NULL,$path_picture=NULL)
    {
        parent::__construct("base_user");
        //$this->oDB->set_timecapture();
        
        $this->arModuleMenu = array();
        $this->arPermissions = array();
        
        if($code_type!=NULL) $this->_code_type = $code_type;
        if($insert_user!=NULL) $this->_insert_user = $insert_user;
        if($delete_user!=NULL) $this->_delete_user = $delete_user;
        if($id!=NULL) $this->_id = $id;
        if($update_user!=NULL) $this->_update_user = $update_user;
        if($bo_login!=NULL) $this->_bo_login = $bo_login;
        if($bo_password!=NULL) $this->_bo_password = $bo_password;
        if($code_erp!=NULL) $this->_code_erp = $code_erp;
        if($insert_date!=NULL) $this->_insert_date = $insert_date;
        if($delete_date!=NULL) $this->_delete_date = $delete_date;
        if($description!=NULL) $this->_description = $description;
        if($enabled!=NULL) $this->_is_enabled = $enabled;
        if($first_name!=NULL) $this->_first_name = $first_name;
        if($is_erpsent!=NULL) $this->_is_erpsent = $is_erpsent;
        if($language!=NULL) $this->_language = $language;
        if($last_name!=NULL) $this->_last_name = $last_name;
        if($md_login!=NULL) $this->_md_login = $md_login;
        if($md_password!=NULL) $this->_md_password = $md_password;
        if($update_date!=NULL) $this->_update_date = $update_date;
        if($insert_platform!=NULL) $this->_insert_platform = $insert_platform;
        if($id_start_module!=NULL) $this->_id_start_module = $id_start_module;
        if($id_seller!=NULL) $this->_id_seller = $id_seller;
        if($path_picture!=NULL) $this->_path_picture = $path_picture;
        
        $this->sDataownerTable = "app_customer";
        $this->sDataownerTablefield = "id";
        $this->arDataownerTablekeys = array("id"=>"-1");
        $this->sDataownerView = "customer";        
    }

    public function load_by_id()
    { 
        if($this->_id)
        {    
            $this->oQuery->set_comment("load_by_id()");
            $this->oQuery->set_fields($this->get_all_fields());
            $this->oQuery->set_fromtables($this->_table_name);
            $this->oQuery->set_joins(NULL);
            $this->oQuery->add_where("$this->_table_name.delete_date IS NULL");
            $this->oQuery->add_where("$this->_table_name.is_enabled=1");
            $this->oQuery->set_and("$this->_table_name.id=$this->_id");
            $sSQL = $this->oQuery->get_select();
            //bug($this->oQuery);
            $arRow = $this->query($sSQL,1);
        }
        $this->row_assign($arRow);
    }

    public function load_by_bo_login()
    {
        if($this->_bo_login && $this->_bo_password)
        {
            $sLogin = $this->db_sinitize($this->_bo_login);
            $sPassword = $this->db_sinitize($this->_bo_password);

            $this->oQuery = new ComponentQuery($this->_table_name);
            $this->oQuery->set_comment("load_by_bo_login()");
            $this->oQuery->set_top(2);
            $this->oQuery->set_fields($this->get_all_fields());
            $this->oQuery->add_where("$this->_table_name.delete_date IS NULL");
            $this->oQuery->add_where("$this->_table_name.is_enabled=1");
            $this->oQuery->add_and("$this->_table_name.bo_login='$sLogin'");
            $this->oQuery->add_and("$this->_table_name.bo_password='$sPassword'");
            $sSQL = $this->oQuery->get_select();
        }

        $arRow = $this->query($sSQL);
        if(count($arRow)>1) $arRow = array();
        else $arRow = $arRow[0];
        
        $this->row_assign($arRow);
    }

    public function load_by_md_login()
    {
        if($this->_md_login && $this->_md_password)
        {
            $sLogin = $this->db_sinitize($this->_md_login);
            $sPassword = $this->db_sinitize($this->_md_password);

            $this->oQuery = new ComponentQuery($this->_table_name);
            $this->oQuery->set_comment("load_by_md_login()");
            $this->oQuery->set_top(2);
            $this->oQuery->set_fields($this->get_all_fields());
            $this->oQuery->add_where("$this->_table_name.delete_date IS NULL");
            $this->oQuery->add_where("$this->_table_name.is_enabled=1");
            $this->oQuery->add_and("$this->_table_name.md_login='$sLogin'");
            $this->oQuery->add_and("$this->_table_name.md_password='$sPassword'");
            $sSQL = $this->oQuery->get_select();
        }

        $arRow = $this->query($sSQL);
        if(count($arRow)>1) $arRow = array();
        else $arRow = $arRow[0];
        $this->row_assign($arRow);
    }

    public function load_by_code_erp()
    { 
        $oQuery = new ComponentQuery($this->_table_name);
        $oQuery->set_db_type($this->get_db_type());
        $oQuery->set_top(1);
        $oQuery->set_fields("*");
        $oQuery->set_comment("user.load_by_code_erp");
        $oQuery->add_and("delete_date IS NULL");
        $oQuery->add_and("is_enabled='1'");
        $oQuery->add_and("code_erp=$this->_code_erp");
        $sSQL = $oQuery->get_select();
        $arRow = $this->query($sSQL,1);
        $this->row_assign($arRow);
    }  
    
    public function get_module_menu()
    {
        $sSQL = "/* get_module_menu() */
        SELECT id_module, module 
        ,MIN(is_select_menu) AS is_select
        ,MIN(is_insert_menu) AS is_insert	
        FROM
        (
            SELECT DISTINCT per.id_module, modl.description AS module
            ,per.is_select_menu, per.is_insert_menu, per.is_insert, per.is_select
            ,per.is_update ,per.is_delete, per.is_pick, per.is_quarantine, per.is_read
            FROM
            (
                /*grupos a los que pertenece el usuario aplicada jerarquia vertical. */
                SELECT 
                /*todos los grupos padres*/
                hiergrp.id_group_parent AS id_user_group
                FROM vbase_hiergroup AS hiergrp
                INNER JOIN base_users_groups AS usrgrp
                ON hiergrp.id_group = usrgrp.id_user_group
                WHERE usrgrp.id_user='$this->_id'
                AND id_group_parent IS NOT NULL
            ) AS grps
            INNER JOIN base_modules_permissions AS per
            ON grps.id_user_group = per.id_user_group
            INNER JOIN
            (
                /* modules activos y no borrados */
                SELECT id, description
                FROM base_module
                WHERE 1=1
                AND is_enabled = '1'
                AND delete_date IS NULL
            ) 
            AS modl
            ON modl.id = per.id_module
        ) AS modper
        GROUP BY id_module, module";
        
        if($this->is_db_mysql())
        {
            $sSQLHierGroup = $this->get_vbase_hiergroup($this->_id,NULL,1);
            $sSQL = "/* get_module_menu() */
            SELECT id_module, module 
            ,MIN(is_select_menu) AS is_select
            ,MIN(is_insert_menu) AS is_insert	
            FROM
            (
                SELECT DISTINCT per.id_module, modl.description AS module
                ,per.is_select_menu, per.is_insert_menu, per.is_insert, per.is_select
                ,per.is_update ,per.is_delete, per.is_pick, per.is_quarantine, per.is_read
                FROM
                (
                    $sSQLHierGroup
                ) AS grps
                INNER JOIN base_modules_permissions AS per
                ON grps.id_user_group = per.id_user_group
                INNER JOIN
                (
                    /* modules activos y no borrados */
                    SELECT id, description
                    FROM base_module
                    WHERE 1=1
                    AND is_enabled = '1'
                    AND delete_date IS NULL
                ) 
                AS modl
                ON modl.id = per.id_module
            ) AS modper
            GROUP BY id_module, module";
        }
         //bug($sSQL,"SQL DIE"); DIE;
        $arRow = $this->query($sSQL);
        return $arRow;
    }//get_module_menu
    
    /**
     * Se utiliza en constructor de AppController para obtener el permiso por modulo (el controlador actual)
     * @param string $sModule
     * @return array
     */
    public function get_permissions($sModule)
    {
        $sSQL = " /*get_permissions(module)*/  
        SELECT id_module, module
        ,MIN(is_select_menu) AS is_select_menu
        ,MIN(is_insert_menu) AS is_insert_menu
        ,MIN(is_insert) AS is_insert
        ,MIN(is_select) AS is_select
        ,MIN(is_update) AS is_update
        ,MIN(is_delete) AS is_delete
        ,MIN(is_pick) AS is_pick
        ,MIN(is_quarantine) AS is_quarantine
        ,MIN(is_read) AS is_read	
        FROM
        (
            SELECT DISTINCT per.id_module, modl.description AS module
            ,per.is_select_menu, per.is_insert_menu, per.is_insert, per.is_select
            ,per.is_update ,per.is_delete, per.is_pick, per.is_quarantine, per.is_read
            FROM
            (
                /*grupos a los que pertenece el usuario aplicada jerarquia vertical*/
                SELECT 
                /*todos los grupos padres*/
                hiergrp.id_group_parent AS id_user_group
                FROM vbase_hiergroup AS hiergrp
                INNER JOIN base_users_groups AS usrgrp
                ON hiergrp.id_group = usrgrp.id_user_group
                WHERE usrgrp.id_user='$this->_id'
                AND id_group_parent IS NOT NULL
            ) AS grps
            INNER JOIN base_modules_permissions AS per
            ON grps.id_user_group = per.id_user_group
            INNER JOIN
            (
                /* modules activos y no borrados */
                SELECT id, description
                FROM base_module
                WHERE 1=1
                AND is_enabled = '1'
                AND description = '$sModule'
                AND delete_date IS NULL
            ) 
            AS modl
            ON modl.id = per.id_module
        ) AS modper
        GROUP BY id_module, module
        ";
        
        if($this->is_db_mysql())
        {
            $sSQLHierGroup = $this->get_vbase_hiergroup($this->_id,NULL,1);
            $sSQL = " /*get_permissions(module)*/  
                  SELECT id_module, module
                  ,MIN(is_select_menu) AS is_select_menu
                  ,MIN(is_insert_menu) AS is_insert_menu
                  ,MIN(is_insert) AS is_insert
                  ,MIN(is_select) AS is_select
                  ,MIN(is_update) AS is_update
                  ,MIN(is_delete) AS is_delete
                  ,MIN(is_pick) AS is_pick
                  ,MIN(is_quarantine) AS is_quarantine
                  ,MIN(is_read) AS is_read	
                  FROM
                  (
                      SELECT DISTINCT per.id_module, modl.description AS module
                      ,per.is_select_menu, per.is_insert_menu, per.is_insert, per.is_select
                      ,per.is_update ,per.is_delete, per.is_pick, per.is_quarantine, per.is_read
                      FROM
                      (
                        $sSQLHierGroup
                      ) AS grps
                      INNER JOIN base_modules_permissions AS per
                      ON grps.id_user_group = per.id_user_group
                      INNER JOIN
                      (
                          /* modules activos y no borrados */
                          SELECT id, description
                          FROM base_module
                          WHERE 1=1
                          AND is_enabled = '1'
                          AND description = '$sModule'
                          AND delete_date IS NULL
                      ) 
                      AS modl
                      ON modl.id = per.id_module
                  ) AS modper
                  GROUP BY id_module, module
                  ";
        }
        $arRow = $this->query($sSQL,1);
        return $arRow;
    }//get_permissions    
    
    /**
     * NO SE USA.
     * Se usó para guardar todos los permisos en cache.
     * @return array tipo array(0=>array(id_module=>x,module=>mmmm,is_select_menu=>...is_read=>),1=>..)
     */
    public function get_modules_permissions()
    {
        
        $sSQL = " /* get_modules_permissions() */
        SELECT id_module, module
        ,MIN(is_select_menu) AS is_select_menu
        ,MIN(is_insert_menu) AS is_insert_menu
        ,MIN(is_insert) AS is_insert
        ,MIN(is_select) AS is_select
        ,MIN(is_update) AS is_update
        ,MIN(is_delete) AS is_delete
        ,MIN(is_pick) AS is_pick
        ,MIN(is_quarantine) AS is_quarantine
        ,MIN(is_read) AS is_read	
        FROM
        (
            SELECT DISTINCT per.id_module, modl.description AS module
            ,per.is_select_menu, per.is_insert_menu, per.is_insert, per.is_select
            ,per.is_update ,per.is_delete, per.is_pick, per.is_quarantine, per.is_read
            FROM
            (
                /*grupos a los que pertenece el usuario aplicada jerarquia vertical*/
                SELECT 
                /*todos los grupos padres*/
                hiergrp.id_group_parent AS id_user_group
                FROM vbase_hiergroup AS hiergrp
                INNER JOIN base_users_groups AS usrgrp
                ON hiergrp.id_group = usrgrp.id_user_group
                WHERE usrgrp.id_user='$this->_id'
                AND id_group_parent IS NOT NULL
            ) AS grps
            INNER JOIN base_modules_permissions AS per
            ON grps.id_user_group = per.id_user_group
            INNER JOIN
            (
                /* modules activos y no borrados */
                SELECT id, description
                FROM base_module
                WHERE 1=1
                AND is_enabled = '1'
                AND delete_date IS NULL
            ) 
            AS modl
            ON modl.id = per.id_module
        ) AS modper
        GROUP BY id_module, module";
        
        if($this->is_db_mysql())
        {
            $sSQLHierGroup = $this->get_vbase_hiergroup($this->_id,NULL,1);
            $sSQL = " /* get_modules_permissions() */
            SELECT id_module, module
            ,MIN(is_select_menu) AS is_select_menu
            ,MIN(is_insert_menu) AS is_insert_menu
            ,MIN(is_insert) AS is_insert
            ,MIN(is_select) AS is_select
            ,MIN(is_update) AS is_update
            ,MIN(is_delete) AS is_delete
            ,MIN(is_pick) AS is_pick
            ,MIN(is_quarantine) AS is_quarantine
            ,MIN(is_read) AS is_read	
            FROM
            (
                SELECT DISTINCT per.id_module, modl.description AS module
                ,per.is_select_menu, per.is_insert_menu, per.is_insert, per.is_select
                ,per.is_update ,per.is_delete, per.is_pick, per.is_quarantine, per.is_read
                FROM
                (
                    $sSQLHierGroup
                ) AS grps
                INNER JOIN base_modules_permissions AS per
                ON grps.id_user_group = per.id_user_group
                INNER JOIN
                (
                    /* modules activos y no borrados */
                    SELECT id, description
                    FROM base_module
                    WHERE 1=1
                    AND is_enabled = '1'
                    AND delete_date IS NULL
                ) 
                AS modl
                ON modl.id = per.id_module
            ) AS modper
            GROUP BY id_module, module";            
        }
        $arRow = $this->query($sSQL);
        return $arRow;        
    }//get_modules_permissions
    
    public function load_module_permissions()
    {
        $this->arPermissions = $this->get_modules_permissions();
    }

    public function load_module_menu()
    {
        $this->arModuleMenu = $this->get_module_menu();
    }

    public function get_his_id_seller()
    {
        $oQuery = new ComponentQuery($this->_table_name);
        $oQuery->set_comment("get_his_id_seller()");
        $oQuery->set_db_type($this->get_db_type());
        $oQuery->set_top(1);
        $oQuery->add_fields("app_seller.id");
        $oQuery->add_joins("INNER JOIN app_seller ON app_seller.code_erp=base_user.code_erp");
        $oQuery->add_where("$this->_table_name.delete_date IS NULL");
        $oQuery->add_where("$this->_table_name.is_enabled=1");
        $oQuery->add_where("$this->_table_name.id=$this->_id");
        
        $sSQL = $oQuery->get_select();
        return $this->query($sSQL,1,1);
    }
    
    public function has_hierarchy_sellers()
    {
        $oQuery = new ComponentQuery("vbase_hieruser_seller");
        $oQuery->set_comment("has_hierarchy_sellers()");
        $oQuery->set_fields("COUNT(id_seller) AS numsellers");
        $oQuery->set_where("vbase_hieruser_seller.id_user=$this->_id");
        $sSQL = $oQuery->get_select();
        
        //si estamos usando la bd de mysql debo modificar la consulta
        if($this->is_db_mysql())
        {
            //Recupero el objeto y cambio el campo id AS id_seller por el contador
            $oQuery = $this->get_vbase_hieruser_seller($this->_id,"oquery");
            $oQuery->set_comment("has_hierarchy_sellers()");
            $oQuery->set_fields("COUNT(id) AS numsellers");
            $sSQL = $oQuery->get_select();
        }
        
        $iNumSellers = $this->query($sSQL,1,1);
        if($iNumSellers>1) return TRUE;
        return FALSE;
    }//has_hierarchy_sellers
    
    /**
     * 
     * @param array $this->arDataownerTablekeys array(fieldname=>value,fieldname=>value)
     * @param string $this->sDataownerTable Tabla de donde se extraera el dato a comprobar
     * @param string $this->sDataownerField Nombre del campo foreign en table_name 
     * @param string $this->sDataownerView Nombre de la vista a utilizar. "seller", "customer", "userchild"
     */
    public function is_dataowner()
    {
        if(is_array($this->arDataownerTablekeys))
        {    
            $oQuery = new ComponentQuery($this->sDataownerTable);
            $oQuery->set_fields($this->sDataownerTable.".id");
            $oQuery->set_comment("is_data_owner()");
            /*"INNER JOIN $sHierarchyView 
            ON $sHierarchyView.$sHierarchyField = $sCheckTable.$sTableField
            AND $sHierarchyView.id_user=$this->_id";*/
            $oQuery->add_joins($this->buildcustom_userhierarchy_join($this->_id,$this->sDataownerView,$this->sDataownerTable,$this->sDataownerTablefield));        
            $oQuery->add_where("$this->sDataownerTable.delete_date IS NULL");
            $oQuery->add_where("$this->sDataownerTable.is_enabled=1");

            foreach($this->arDataownerTablekeys as $sFieldName=>$sValue)
                $oQuery->add_and("$this->sDataownerTable.$sFieldName='$sValue'");

            $sSQL = $oQuery->get_select();
            //bug($sSQL); die;
            $arRow = $this->query($sSQL,1,1);
            if($arRow) return TRUE;
        }
        return FALSE;        
    }
    
    public function is_not_dataowner(){return !$this->is_dataowner();}
    
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
    public function get_start_module()
    {
        $sSQL = "SELECT description FROM base_module WHERE id='$this->_id_start_module'";
        $sStartModule = $this->query($sSQL,1,1);
        //$sStartModule = $sStartModule["Description"];
        return $sStartModule;
    }
    
    public function get_cached_module_menu(){return $this->arModuleMenu;}
    public function get_cached_module_permissions(){return $this->arPermissions;}
    public function get_cached_module_permission($sModule)
    {
        foreach($this->arPermissions as $i=>$arPermission)
        {
            if($arPermission["module"]==$sModule)
                return $arPermission;
        }
        return array();
    }
    
    public function get_lowest_group()
    {
        $oQuery = new ComponentQuery("base_user_group AS grp");
        $oQuery->set_db_type($this->get_db_type());
        $oQuery->set_top(1);
        $oQuery->add_fields("description");
        $oQuery->set_comment("get_lowest_group()");
        $oQuery->add_joins("INNER JOIN
                 (
                    SELECT MIN(id_user_group) AS id_group
                    FROM base_users_groups 
                    WHERE id_user=$this->_id
                    AND is_enabled=1
                    AND delete_date IS NULL
                    GROUP BY id_user
                 ) AS min ON grp.id=min.id_group");
        $sSQL = $oQuery->get_select();
        //bug($sSQL); die;
        return $this->query($sSQL,1,1);
    }
    public function get_id_seller(){return $this->_id_seller;}
    public function get_seller()
    {
        $this->oSeller = new ModelSeller($this->_id_seller);
        $this->oSeller->load_by_id();
        return $this->oSeller;
    }
    
    public function get_login_hour(){return $this->_login_hour;}
    public function get_login_ip(){return $this->_login_ip;}
    public function get_login_latitude(){return $this->_login_latitude;}
    public function get_login_length(){return $this->_login_length;}
    public function get_login_session(){return $this->_login_session;}
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
    
    public function set_login_hour($value){ $this->_login_hour = $value;}
    public function set_login_ip($value){ $this->_login_ip = $value;}
    public function set_login_latitude($value){ $this->_login_latitude = $value;}
    public function set_login_length($value){ $this->_login_length = $value;}
    public function set_login_session($value){ $this->_login_session = $value;}    
}
