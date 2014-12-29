<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.1.4
 * @name Elemento de Menu Vertical 
 * @file elem_verticalmenu.php   
 * @date 30-10-2014 11:09 (SPAIN)
 * @observations: 
 * @requires:
 */

//bug($this->isPermaLink,"permalink en vmenu");

$arMenuPermission = $this->oSessionUser->get_module_menu();
//bug($arMenuPermission);
$oModulesNavbar = new ApphelperVerticalmenu();
$oModulesNavbar->set_id("NavigationMenu");
$oModulesNavbar->set_current_id($this->get_current_module());
if(is_array($arMenuPermission))
    $oModulesNavbar->set_user_permissions($arMenuPermission);

$arMenuItems = array();
//dev//dev

//MODULEBUILDER
if($this->oSessionUser->get_id()=="-10")
$arMenuItems["modulebuilder"] = array
(
    "li"=>array("id"=>"modulebuilder")
    ,"a"=>array("text"=>tr_menu_modulebuilder,"icon"=>"awe-group")
    ,"ul"=>array
    (
        "style"=>"display:block;"
        ,"li"=>array
        (

            //accesos directos a secciones
            //array("href"=>$this->build_url("modulebuilder","text"=>tr_menu_mdb_dashboard,"permission"=>"insert")
            //SECCION QUERIES
            //convierte consultas de una sola linea
            array("href"=>$this->build_url("modulebuilder","queries","parsesql"),"text"=>tr_menu_mdb_parsesql,"permission"=>"")
            //"queries&view=parsesql convierte consultas de una sola linea - "queries&view=query FIDDLE CONSULTAS servidor,bd,usuario,pass,puerto
            //QUEDA: "queries&view=query FIDDLE CONSULTAS servidor,bd,usuario,pass,puerto
            //SECCION exports
            ,array("href"=>$this->build_url("modulebuilder","exports","generate"),"text"=>tr_menu_mdb_export,"permission"=>"")
            //GENERADOR DE MVC
            ,array("href"=>$this->build_url("modulebuilder",NULL,"insert"),"text"=>tr_menu_mdb_insert,"permission"=>"")
            //crea código para modificar la generación de modulebuilder
            ,array("href"=>$this->build_url("modulebuilder",NULL,"parsetool"),"text"=>tr_menu_mdb_parsetool,"permission"=>"")
            //SECCION PHP
            //quedan:/php/ FIDLE PHP,/javascript/FIDLE JS,
            //generador de MVC
            //SECCION TEST
            ,array("href"=>$this->build_url("modulebuilder","test"),"text"=>tr_menu_mdb_test,"permission"=>"")
            //SECCION UTILS
            //modulebuilder","utils&view=checkpchost definicion de paquete columna,tipo,longitud
            //module=modulebuilder","utils&view=compressfile comprime
        )
    )
);    

//HOMES - DASHBOEARD
$arMenuItems[] = array
(
    //permission=select: El único item con el que cuenta es directamente un botón al listado
    "li"=>array("id"=>"homes","permission"=>"select")
    ,"a"=>array("href"=>array("href"=>$this->build_url("homes"),"text"=>tr_menu_dashboard,"icon"=>"awe-home"))
);

//CHALAN - BALANCE
$arMenuItems[] = array
(
    "li"=>array("id"=>"balances")
    ,"a"=>array("text"=>tr_menu_balances,"icon"=>"awe-picture")
    ,"ul"=>array
    (
        "style"=>"display:block;"
        ,"li"=>array
        (
                        
            array("href"=>$this->build_url("balances"),"text"=>tr_menu_list_balances,"permission"=>"select")
            ,array("href"=>$this->build_url("balances","incomes"),"text"=>tr_menu_list_bal_incomes,"permission"=>"select")
            ,array("href"=>$this->build_url("balances","incomes","insert"),"text"=>tr_menu_new_bal_income,"permission"=>"insert")
            ,array("href"=>$this->build_url("balances","outcomes"),"text"=>tr_menu_list_bal_outcome,"permission"=>"select")
            ,array("href"=>$this->build_url("balances","outcomes","insert"),"text"=>tr_menu_new_bal_outcome,"permission"=>"insert")            
        )
    )
);


//KUZ - CUSTOMERS
$arMenuItems[] = array
(
    "li"=>array("id"=>"customers")
    ,"a"=>array("text"=>tr_menu_customers,"icon"=>"awe-group")
    ,"ul"=>array
    (
        "style"=>"display:block;"
        ,"li"=>array
        (
            array("href"=>$this->build_url("customers"),"text"=>tr_menu_list,"permission"=>"select")
            ,array("href"=>$this->build_url("customers",NULL,"insert"),"text"=>tr_menu_new,"permission"=>"insert")
            //,array("href"=>$this->build_url("customers","pictures&view=make_folders","text"=>tr_menu_makefolders,"permission"=>"select")
        )
    )
);

//KUZ - CUSTOMERNOTES
$arMenuItems[] = array
(
    "li"=>array("id"=>"customernotes")
    ,"a"=>array("text"=>tr_menu_customernotes,"icon"=>"awe-group")
    ,"ul"=>array
    (
        "style"=>"display:block;"
        ,"li"=>array
        (
            array("href"=>$this->build_url("customernotes"),"text"=>tr_menu_list,"permission"=>"select")
            ,array("href"=>$this->build_url("customernotes",NULL,"insert"),"text"=>tr_menu_new,"permission"=>"insert")
            //,array("href"=>$this->build_url("customers","pictures&view=make_folders","text"=>tr_menu_makefolders,"permission"=>"select")
        )
    )
);

//KUZ - PRODUCTS
$arMenuItems[] = array
(
    "li"=>array("id"=>"products")
    ,"a"=>array("text"=>tr_menu_products,"icon"=>"awe-shopping-cart")
    ,"ul"=>array
    (
        "style"=>"display:block;"
        ,"li"=>array
        (
            array("href"=>$this->build_url("products"),"text"=>tr_menu_list,"permission"=>"select")
            ,array("href"=>$this->build_url("products",NULL,"insert"),"text"=>tr_menu_new,"permission"=>"insert")
        )
    )
);

//KUZ - PICTURES
$arMenuItems[] = array
(
    "li"=>array("id"=>"pictures")
    ,"a"=>array("text"=>tr_menu_pictures,"icon"=>"awe-picture")
    ,"ul"=>array
    (
        "style"=>"display:block;"
        ,"li"=>array
        (
            array("href"=>$this->build_url("pictures"),"text"=>tr_menu_list,"permission"=>"select")
            ,array("href"=>$this->build_url("pictures",NULL,"insert"),"text"=>tr_menu_new,"permission"=>"insert")
        )
    )
);

//KUZ - ORDERS
$arMenuItems[] = array
(
    "li"=>array("id"=>"orders")
    ,"a"=>array("text"=>tr_menu_orders,"icon"=>"awe-barcode")
    ,"ul"=>array
    (
        "style"=>"display:block;"
        ,"li"=>array
        (
            array("href"=>$this->build_url("orders"),"text"=>tr_menu_list,"permission"=>"select")
            ,array("href"=>$this->build_url("orders",NULL,"insert"),"text"=>tr_menu_new,"permission"=>"insert")
        )
    )
);

//KUZ - SELLERS
$arMenuItems[] = array
(
    "li"=>array("id"=>"sellers")
    ,"a"=>array("text"=>tr_menu_sellers,"icon"=>"awe-briefcase")
    ,"ul"=>array
    (
        "style"=>"display:block;"
        ,"li"=>array
        (
            array("href"=>$this->build_url("sellers"),"text"=>tr_menu_list,"permission"=>"select")
            ,array("href"=>$this->build_url("sellers",NULL,"insert"),"text"=>tr_menu_new,"permission"=>"insert")
        )
    )    
);

//bug($arMenuItems,"armenuitems");
//UC - BLACKLISTS
$arMenuItems[] = array
(
    "li"=>array("id"=>"blacklists","permission"=>"select")
    ,"a"=>array("href"=>$this->build_url("blacklists"),"text"=>tr_menu_blacklists,"icon"=>"awe-exclamation-sign")
);

//UC - SUSPICIONS
$arMenuItems[] = array
(
    "li"=>array("id"=>"suspicions")
    ,"a"=>array("text"=>tr_menu_suspicions,"icon"=>"awe-picture")
    ,"ul"=>array
    (
        "style"=>"display:block;"
        ,"li"=>array
        (
            array("href"=>$this->build_url("suspicions"),"text"=>tr_menu_list,"permission"=>"select")
            ,array("href"=>$this->build_url("suspicions",NULL,"insert"),"text"=>tr_menu_new,"permission"=>"insert")
        )
    )
);

//UC - TRANSFERS
$arMenuItems[] = array
(
    "li"=>array("id"=>"transfers")
    ,"a"=>array("text"=>tr_menu_transfers,"icon"=>"awe-picture")
    ,"ul"=>array
    (
        "style"=>"display:block;"
        ,"li"=>array
        (
            array("href"=>$this->build_url("transfers"),"text"=>tr_menu_list,"permission"=>"select")
            ,array("href"=>$this->build_url("transfers",NULL,"insert"),"text"=>tr_menu_new,"permission"=>"insert")
        )
    )
);

//UC - PROJECTS
$arMenuItems[] = array
(
    "li"=>array("id"=>"projects")
    ,"a"=>array("text"=>tr_menu_projects,"icon"=>"awe-group")
    ,"ul"=>array
    (
        "style"=>"display:block;"
        ,"li"=>array
        (
            array("href"=>$this->build_url("projects"),"text"=>tr_menu_list,"permission"=>"select")
            ,array("href"=>$this->build_url("projects",NULL,"insert"),"text"=>tr_menu_new,"permission"=>"insert")
        )
    )
);


//USERS
$arMenuItems[] = array
(
    "li"=>array("id"=>"users")
    ,"a"=>array("text"=>tr_menu_users,"icon"=>"awe-group")
    ,"ul"=>array
    (
        "style"=>"display:block;"
        ,"li"=>array
        (
            array("href"=>$this->build_url("users"),"text"=>tr_menu_list,"permission"=>"select")
            ,array("href"=>$this->build_url("users",NULL,"insert"),"text"=>tr_menu_new,"permission"=>"insert")
        )
    )
);

//AJAX
$arMenuItems[] = array
(
    "li"=>array("id"=>"ajax")
    ,"a"=>array("text"=>tr_menu_ajax,"icon"=>"awe-group")
    ,"ul"=>array
    (
        "style"=>"display:block;"
        ,"li"=>array
        (
            array("href"=>$this->build_url("ajax","products"),"text"=>tr_menu_list,"permission"=>"select")
            ,array("href"=>$this->build_url("ajax","products","insert"),"text"=>tr_menu_new,"permission"=>"insert")
        )
    )
);

$oModulesNavbar->set_menu($arMenuItems);
?>
<!-- Main navigation (elem_verticalmenu) -->
<nav class="main-navigation" role="navigation">
<?php
$oModulesNavbar->show();
?>
</nav>
<!-- /Main navigation (/elem_verticalmenu) -->