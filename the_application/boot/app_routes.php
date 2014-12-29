<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.5
 * @name arRoutes
 * @file app_routes.php
 * @date 01-11-2014 13:01 (SPAIN)
 * @observations: configuracion de rutas
 * No sirve en IIS
 * @requires: .htaccess,ComponentRouter
 */
//HOME www.tudomino.com
$arRoutes[] = array("request_uri"=>"/","controller"=>"homes","method"=>"login");
$arRoutes[] = array("request_uri"=>"/logout/","controller"=>"homes","method"=>"logout");
$arRoutes[] = array("request_uri"=>"/homes/logout/","method"=>"logout");
$arRoutes[] = array("request_uri"=>"/homes/");

//BLACKLIST
$arRoutes[] = array("request_uri"=>"/blacklists/");
//SUSPICIONS
$arRoutes[] = array("request_uri"=>"/suspicions/");
$arRoutes[] = array("request_uri"=>"/suspicions/insert","method"=>"insert");

//TRANSFERS
$arRoutes[] = array("request_uri"=>"/transfers/");
$arRoutes[] = array("request_uri"=>"/transfers/insert","method"=>"insert");

//PROJECTS
$arRoutes[] = array("request_uri"=>"/projects/");
$arRoutes[] = array("request_uri"=>"/projects/insert","method"=>"insert");

//CUSTOMERS
$arRoutes[] = array("request_uri"=>"/customers/:page:^(\s*|\d+)$:page:");
$arRoutes[] = array("request_uri"=>"/customers/insert","method"=>"insert");
$arRoutes[] = array("request_uri"=>"/customers/update/:id:[0-9]+:id:","method"=>"update");
$arRoutes[] = array("request_uri"=>"/customers/delete","method"=>"delete");
$arRoutes[] = array("request_uri"=>"/customers/quarantine/:id:^(\s*|\d+)$:id:","method"=>"quarantine");
$arRoutes[] = array("request_uri"=>"/customers/notes/insert/:id_customer:[0-9]+:id_customer:","partial"=>"notes","method"=>"insert");
$arRoutes[] = array("request_uri"=>"/customers/notes/:id_customer:[0-9]+:id_customer:/:page:^(\s*|\d+)$:page:","partial"=>"notes");
$arRoutes[] = array("request_uri"=>"/customers/notes/update/:id_customer:[0-9]+:id_customer:/:id:^(\s*|\d+)$:id:","partial"=>"notes","method"=>"update");
$arRoutes[] = array("request_uri"=>"/customers/notes/quarantine/:id_customer:[0-9]+:id_customer:/:id:^(\s*|\d+)$:id:","partial"=>"notes","method"=>"quarantine");
$arRoutes[] = array("request_uri"=>"/customers/notes/:id_customer:[0-9]+:id_customer:/quarantine","partial"=>"notes","method"=>"quarantine");
$arRoutes[] = array("request_uri"=>"/customers/notes/delete/:id_customer:[0-9]+:id_customer:/:id:^(\s*|\d+)$:id:","partial"=>"notes","method"=>"delete");

//NOTES
$arRoutes[] = array("request_uri"=>"/customernotes");
$arRoutes[] = array("request_uri"=>"/customernotes/insert","method"=>"insert");
$arRoutes[] = array("request_uri"=>"/customernotes/update/:id:[0-9]+:id:","method"=>"update");

//PRODUCTS
$arRoutes[] = array("request_uri"=>"/products/:page:^(\s*|\d+)$:page:");
$arRoutes[] = array("request_uri"=>"/products/insert/","method"=>"insert");
$arRoutes[] = array("request_uri"=>"/products/update/:id:[0-9]+:id:","method"=>"update");
$arRoutes[] = array("request_uri"=>"/products/pictures/:id:[0-9]+:id:","partial"=>"pictures");
//PICTURES
$arRoutes[] = array("request_uri"=>"/pictures/:page:^(\s*|\d+)$:page:");
$arRoutes[] = array("request_uri"=>"/pictures/insert/","method"=>"insert"); 

//ORDERS
$arRoutes[] = array("request_uri"=>"/orders/:page:^(\s*|\d+)$:page:");
$arRoutes[] = array("request_uri"=>"/orders/insert/","method"=>"insert");

//SELLERS
$arRoutes[] = array("request_uri"=>"/sellers/:page:^(\s*|\d+)$:page:");
$arRoutes[] = array("request_uri"=>"/sellers/insert/","method"=>"insert");

//USERS
$arRoutes[] = array("request_uri"=>"/users/:page:^(\s*|\d+)$:page:");
$arRoutes[] = array("request_uri"=>"/users/insert/","method"=>"insert");

//AJAX
$arRoutes[] = array("request_uri"=>"/ajax/");
$arRoutes[] = array("request_uri"=>"/ajax/insert/","method"=>"insert");

//BALANCES
$arRoutes[] = array("request_uri"=>"/balances/:page:^(\s*|\d+)$:page:");
$arRoutes[] = array("request_uri"=>"/balances/incomes/:page:^(\s*|\d+)$:page:","partial"=>"incomes");
$arRoutes[] = array("request_uri"=>"/balances/incomes/insert/","partial"=>"incomes","method"=>"insert");
$arRoutes[] = array("request_uri"=>"/balances/incomes/update/:id:[0-9]+:id:","partial"=>"incomes","method"=>"update");
$arRoutes[] = array("request_uri"=>"/balances/incomes/quarantine/:id:^(\s*|\d+)$:id:","partial"=>"incomes","method"=>"quarantine");
$arRoutes[] = array("request_uri"=>"/balances/incomes/delete/:id:[0-9]+:id:","partial"=>"incomes","method"=>"delete");

$arRoutes[] = array("request_uri"=>"/balances/outcomes/:page:^(\s*|\d+)$:page:","partial"=>"outcomes");
$arRoutes[] = array("request_uri"=>"/balances/outcomes/insert/","partial"=>"outcomes","method"=>"insert");
$arRoutes[] = array("request_uri"=>"/balances/outcomes/update/:id:[0-9]+:id:","partial"=>"outcomes","method"=>"update");
$arRoutes[] = array("request_uri"=>"/balances/outcomes/quarantine/:id:^(\s*|\d+)$:id:","partial"=>"outcomes","method"=>"quarantine");
$arRoutes[] = array("request_uri"=>"/balances/outcomes/delete/:id:^(\s*|\d+)$:id:","partial"=>"outcomes","method"=>"delete");

//MODULE BUILDER
$arRoutes[] = array("request_uri"=>"/modulebuilder/");
$arRoutes[] = array("request_uri"=>"/modulebuilder/test","partial"=>"test");
$arRoutes[] = array("request_uri"=>"/modulebuilder/test/match","partial"=>"test","method"=>"test_match");
$arRoutes[] = array("request_uri"=>"/balances/incomes/insert/","partial"=>"incomes","method"=>"insert");

$arRoutes[] = array("request_uri"=>"/balances/outcomes","partial"=>"outcomes");
$arRoutes[] = array("request_uri"=>"/balances/outcomes/insert/","partial"=>"outcomes","method"=>"insert");

