<?php
/**
 * @author Module Builder 1.0.0
 * @link www.eduardoaf.com
 * @version 1.0.7
 * @name ModelProduct
 * @file model_product.php
 * @date 05-10-2014 01:42 (SPAIN)
 * @observations: 
 */
import_appmain("model");

class ModelProduct extends TheApplicationModel
{
    private $_id_type_container; //int(4)
    private $oProductArray; //Model Object
    private $_id_type_size; //int(4)
    private $_price_cost; //numeric(9)
    private $_price_regular; //numeric(9)
    private $_price_wholesale; //numeric(9)
    private $_price_custom; //numeric(9)
    private $_id_product_family; //numeric(9)
    private $oProductFamily; //Model Object    
    private $_name; //varchar(100)
    private $_observation; //varchar(100)
    private $_web_keywords; //varchar(250)
    private $_lookup_words; //varchar(100)

    public function __construct(
$id_type_container=NULL,$id_type_size=NULL,$price_cost=NULL,$price_regular=NULL,$price_wholesale=NULL,$price_custom=NULL,$id_product_family=NULL,$name=NULL,$observation=NULL,$web_keywords=NULL,$lookup_words=NULL)
    {
        parent::__construct("app_product");
        if($id_type_container!=NULL) $this->_id_type_container = $id_type_container;
        if($id_type_size!=NULL) $this->_id_type_size = $id_type_size;
        if($price_cost!=NULL) $this->_price_cost = $price_cost;
        if($price_regular!=NULL) $this->_price_regular = $price_regular;
        if($price_wholesale!=NULL) $this->_price_wholesale = $price_wholesale;
        if($price_custom!=NULL) $this->_price_custom = $price_custom;
        if($id_product_family!=NULL) $this->_id_product_family = $id_product_family;
        if($name!=NULL) $this->_name = $name;
        if($observation!=NULL) $this->_observation = $observation;
        if($web_keywords!=NULL) $this->_web_keywords = $web_keywords;
        if($lookup_words!=NULL) $this->_lookup_words = $lookup_words;
        
        
        $this->sSELECTfields = 
        "
            app_product.processflag ,app_product.insert_platform ,app_product.insert_user ,app_product.insert_date ,app_product.update_platform ,app_product.update_user ,app_product.update_date
            ,app_product.delete_platform ,app_product.delete_user ,app_product.delete_date 
            ,app_product.is_erpsent ,app_product.is_enabled

            ,app_product.id ,app_product.code_erp ,app_product.description ,app_product.name
            ,app_product.observation ,app_product.id_type_container ,app_product.id_type_size ,app_product.price_cost ,app_product.price_regular ,app_product.price_wholesale ,app_product.price_custom
            ,app_product.id_product_family ,app_product.web_keywords ,app_product.lookup_words

            ,prar.description AS volume
            
            ,COALESCE(picture.uri_thumb,'images/custom/no_image_small.png') AS uri_thumb
            ,COALESCE(picture.uri_href,'javascript:;') AS uri_href
            ,COALESCE(picture.target,'self') AS target
        ";
        
        $this->arFieldsMappingExtra["volume"] = "prar.description";
        $this->arDescConfig=array("id","name","separator"=>" - ");
    }

    public function insert()
    {
        $id_type_container = mssqlclean($this->_id_type_container,1);
        $id_type_size = mssqlclean($this->_id_type_size,1);
        $price_cost = mssqlclean($this->_price_cost,1);
        $price_regular = mssqlclean($this->_price_regular,1);
        $price_wholesale = mssqlclean($this->_price_wholesale,1);
        $price_custom = mssqlclean($this->_price_custom,1);
        $id_product_family = mssqlclean($this->_id_product_family,1);
        $name = mssqlclean($this->_name);
        $observation = mssqlclean($this->_observation);
        $web_keywords = mssqlclean($this->_web_keywords);
        $lookup_words = mssqlclean($this->_lookup_words);

        $sSQL = "INSERT INTO $this->_table_name
        (id_type_container,id_type_size,price_cost,price_regular,price_wholesale,price_custom
        ,id_product_family,name,observation,web_keywords,lookup_words)
        VALUES
        ($id_type_container,$id_type_size,$price_cost,$price_regular,$price_wholesale
         ,$price_custom,$id_product_family,'$name','$observation','$web_keywords','$lookup_words')";
        $this->execute($sSQL);
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

        $this->_id_type_container = $arRow["id_type_container"];
        $this->_id_type_size = $arRow["id_type_size"];
        $this->_price_cost = $arRow["price_cost"];
        $this->_price_regular = $arRow["price_regular"];
        $this->_price_wholesale = $arRow["price_wholesale"];
        $this->_price_custom = $arRow["price_custom"];
        $this->_id_product_family = $arRow["id_product_family"];
        $this->_name = $arRow["name"];
        $this->_observation = $arRow["observation"];
        $this->_web_keywords = $arRow["web_keywords"];
        $this->_lookup_words = $arRow["lookup_words"];
        //BASE FIELDS
        $this->_id = $arRow["id"];
        $this->_insert_platform = $arRow["insert_platform"];
        $this->_insert_user = $arRow["insert_user"];
        $this->_insert_date = $arRow["insert_date"];
        $this->_update_platform = $arRow["update_platform"];
        $this->_update_user = $arRow["update_user"];
        $this->_update_date = $arRow["update_date"];
        $this->_code_erp = $arRow["code_erp"];
        $this->_description = $arRow["description"];
        $this->_delete_platform = $arRow["delete_platform"];
        $this->_delete_date = $arRow["delete_date"];
        $this->_delete_user = $arRow["delete_user"];
        $this->_is_enabled = $arRow["is_enabled"];
        $this->_is_erpsent = $arRow["is_erpsent"];
        $this->_processflag = $arRow["processflag"];
    }

    public function get_select_all_ids()
    {
        $this->oQuery->set_comment("get_select_all_ids() overridden");
        $this->oQuery->set_top($this->_top);
        $this->oQuery->set_fields("$this->_table_name.id");
        $this->oQuery->set_fromtables($this->_table_name);
        $this->oQuery->add_joins("INNER JOIN app_product_array prar ON app_product.id_type_size = prar.id");
        $this->oQuery->add_joins("AND prar.type='size' AND (prar.delete_date IS NULL)");
        $this->oQuery->add_joins("AND (prar.description LIKE '%Q%' OR prar.description LIKE '%G%' OR prar.description LIKE '%L%')");
        $this->oQuery->add_where("$this->_table_name.delete_date IS NULL");
        $this->oQuery->add_where("$this->_table_name.is_enabled=1");
        
        //EXTRA AND
        $this->oQuery->add_and($this->build_sql_filters());
        //ORDERBY 
        //default orderby
        $this->oQuery->set_orderby("$this->_table_name.id DESC");
        $sOrderByAuto = $this->build_sql_orderby();
        if($sOrderByAuto) $this->oQuery->set_orderby($sOrderByAuto);
        $sSQL = $this->oQuery->get_select();
        //bug($sSQL);
        return $this->query($sSQL);
    }//get_select_all_ids overriden   

    public function get_select_all_by_ids($arIds)
    {
        $this->oQuery->set_comment("get_select_all_by_ids()");
        $this->oQuery->set_top($this->_top);
        $this->oQuery->set_distinct();
        //Si no hay campos definidos en el objeto se usa *
        if(!$this->sSELECTfields)
            $this->oQuery->set_fields($this->get_all_fields());
        else
            $this->oQuery->set_fields($this->sSELECTfields);
        
        if(!$this->oQuery->get_fromtables()) 
            $this->oQuery->set_fromtables($this->_table_name);
        
        //hago esta subconsulta con otro objeto porque la concatenacion depende el tipo de bd
        $oQuery = new ComponentQuery("app_picture");
        $oQuery->add_fields("id_entity");
        if($this->is_db_mssql())
        {
            $oQuery->add_fields("uri_public+'/'+folder+'/'+folder+'_'+CONVERT(VARCHAR,id)+'_th1.'+extension AS uri_thumb");
            $oQuery->add_fields("uri_public+'/'+folder+'/'+folder+'_'+CONVERT(VARCHAR,id)+'.'+extension AS uri_href");
        }
        else
        {
            $oQuery->add_fields("CONCAT(uri_public,'/',folder,'/',folder,'_',CAST(id AS CHAR),'_th1.',extension) AS uri_thumb");
            $oQuery->add_fields("CONCAT(uri_public,'/',folder,'/',folder,'_',CAST(id AS CHAR),'.',extension) AS uri_href");            
        }
        $oQuery->add_fields("'blank' AS target");
        $oQuery->add_and("is_bydefault=1");
        $oQuery->add_and("id_type_entity=4");
        $oQuery->add_and("is_enabled=1");
        $oQuery->add_and("delete_date IS NULL");
        
        $sSQL = $oQuery->get_select();
        
        /*SELECT id_entity
            ,uri_public+'/'+folder+'/'+folder+'_'+CONVERT(VARCHAR,id)+'_th1.'+extension AS uri_thumb
            ,uri_public+'/'+folder+'/'+folder+'_'+CONVERT(VARCHAR,id)+'.'+extension AS uri_href
            ,'blank' AS target
            FROM app_picture 
            WHERE is_bydefault=1 
            AND id_type_entity=4 
            AND is_enabled=1
            AND delete_date IS NULL*/
        $this->oQuery->add_joins("LEFT JOIN ( $sSQL) AS picture 
          ON picture.id_entity=$this->_table_name.id");
        $this->oQuery->set_and();
        //ANDS
        if($arIds) 
        {
            $arIds = $this->get_column_values($arIds);
            $this->oQuery->add_and("$this->_table_name.id IN ".$this->build_sqlin($arIds,1));
        }
        //si la query anterior no devolvio resultados el array de ids viene vacio
        else 
            $this->oQuery->add_and("$this->_table_name.id IS NULL"); 
        
        $sSQL = $this->oQuery->get_select();
        //bug($sSQL); die;
        return $this->query($sSQL);
    }//get_select_all_by_ids

    
    //Para usar con llamadas ajax
    public function get_suggested($sSearch)
    {
        $oQuery = new ComponentQuery($this->_table_name,"mssql");
        $oQuery->set_comment("get_suggested()");
        $oQuery->set_top(10);
        $oQuery->set_fields("id, description=description+' ('+CONVERT(VARCHAR,id)+')' ");
        $oQuery->set_and("description LIKE '%$sSearch%'");
        
        if(is_numeric($sSearch)) 
            $oQuery->set_or("id LIKE '%$sSearch%'");    
        $oQuery->set_orderby("2 ASC");
        $sSQL = $oQuery->get_select();
        $arRows = $this->query($sSQL);
        return $arRows;
    }

    //===================
    //       GETS
    //===================
    public function get_id_type_container(){return $this->_id_type_container;}
    public function get_type_container()
    {
        $this->oProductArray = new ModelProductArray($this->_id_type_container);
        $this->oProductArray->load_by_id();
        return $this->oProductArray;
    }

    public function get_id_type_size(){return $this->_id_type_size;}
    public function get_type_size()
    {
        $this->oProductArray = new ModelProductArray($this->_id_type_size);
        $this->oProductArray->load_by_id();
        return $this->oProductArray;
    }
    
    public function get_price_cost(){return $this->_price_cost;}
    public function get_price_regular(){return $this->_price_regular;}
    public function get_price_wholesale(){return $this->_price_wholesale;}
    public function get_price_custom(){return $this->_price_custom;}
    public function get_id_product_family(){return $this->_id_product_family;}
    public function get_product_family()
    {
        $this->oProductFamily = new ModelProductFamily($this->_id_product_family);
        $this->oProductFamily->load_by_id();
        return $this->oProductFamily;
    }    
    public function get_name(){return $this->_name;}
    public function get_observation(){return $this->_observation;}
    public function get_web_keywords(){return $this->_web_keywords;}
    public function get_lookup_words(){return $this->_lookup_words;}
    //===================
    //       SETS
    //===================
    public function set_id_type_container($value){$this->_id_type_container = $value;}
    public function set_type_container($oValue){$this->oProductArray = $oValue;}
    public function set_id_type_size($value){$this->_id_type_size = $value;}
    public function set_type_size($oValue){$this->oProductArray = $oValue;}
    public function set_price_cost($value){$this->_price_cost = $value;}
    public function set_price_regular($value){$this->_price_regular = $value;}
    public function set_price_wholesale($value){$this->_price_wholesale = $value;}
    public function set_price_custom($value){$this->_price_custom = $value;}
    public function set_id_product_family($value){$this->_id_product_family = $value;}
    public function set_product_family($oValue){$this->oProductFamily = $oValue;}
    public function set_name($value){$this->_name = $value;}
    public function set_observation($value){$this->_observation = $value;}
    public function set_web_keywords($value){$this->_web_keywords = $value;}
    public function set_lookup_words($value){$this->_lookup_words = $value;}
}
