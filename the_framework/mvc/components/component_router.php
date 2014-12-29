<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.7
 * @name ComponentRouter
 * @file component_router.php
 * @date 01-11-2014 18:47 (SPAIN)
 * @observations: Trata las rutas enviadas por htaccess
 * No sirve en IIS
 * @requires: .htaccess
 */
class ComponentRouter 
{
    private $sRequestedURI;
    private $arRequestedURI;
    private $sRequestedLastPiece;
    private $iRequestedPieces;
    
    private $arConfigRoutes;
    private $arConfigRouteFound;
    //si se encuentra una ruta se guarda la url troceada de configuración
    private $arConfigURIFound;
    
    private $isSlashAdded;
    private $isPage;
    private $arConfigSame;
    private $arConfigSame1;
    
    public function __construct($arConfigRoutes)
    {
        define("TFW_WS","/");//web separator
        $this->load_requested_uri();        
        $this->load_configured_routes($arConfigRoutes);
    }
    
    /**
    * Trocea la URI pedida ($this->_the_uri) tomando como marca TFW_WS
    * @return array  Array con los trozos de la url 
    */    
    private function load_requested_uri()
    {
        $this->sRequestedURI = $_SERVER["REQUEST_URI"];
        
        //Htaccess ya le mete el slash al final siempre
        if(!$this->is_lastchar_slash($this->sRequestedURI))
            $this->isSlashAdded = TRUE;
        
        //bug($this->isSlashAdded,"slashadded");die;
        $sTheUri = $this->remove_first_char($this->sRequestedURI);
        $sTheUri = $this->remove_last_char($sTheUri);
        $this->arRequestedURI = explode(TFW_WS,$sTheUri);
        $this->iRequestedPieces = count($this->arRequestedURI);
        //Guardo la ultima parte para comprobar si lo que me llega es 1 para hacer la redireccion en caso da pagina
        $this->sRequestedLastPiece = $this->arRequestedURI[$this->iRequestedPieces-1];
        //bug($this->sRequestedLastPiece);die;
    }
    
    /**
     * Necesita que previamente este cargado iRequestedPieces
     * @param type $arConfigRoutes
     */
    private function load_configured_routes($arConfigRoutes)
    {
        $this->arConfigRoutes = $arConfigRoutes;
        
        foreach($this->arConfigRoutes as $arConfigRoute)
        {
            $sConfUri = $arConfigRoute["request_uri"];
            $sConfUri = $this->remove_first_char($sConfUri);
            $sConfUri = $this->remove_last_char($sConfUri);
            $arConfPieces = explode(TFW_WS,$sConfUri);
            $iConfPieces = count($arConfPieces);
            //bug($iConfPieces,$this->iRequestedPieces);
            if($iConfPieces==($this->iRequestedPieces+1))
            {    
                $arConfigRoute["request_uri"] = $sConfUri;
                $this->arConfigSame1[] = $arConfigRoute;
            }
            elseif($iConfPieces==$this->iRequestedPieces)
            {   
                $arConfigRoute["request_uri"] = $sConfUri;
                $this->arConfigSame[] = $arConfigRoute;
            }
        }//fin foreach
    }//load_confiugured_routes

   private function has_regexp($string)
    {
        $arRegExpChar = array("[","]","+","*","?","(",")","^","{","}","$");
        $iLongitud = strlen($string);
        for($i=0; $i<$iLongitud; $i++)
        {
            $char = $string{$i};
            if(in_array($char,$arRegExpChar))
                return true;
        }
        return false;
    }
    
    private function match_all_pieces($arConfigURI,$arRequestedURI)
    {
        //no puedo cambiar $arRequestedURI por $this->arReq.. ya que para las rutas+1
        //tengo que añadir un item vacio al final
        $iConfigPieces = count($arConfigURI);
        $iRequestedPieces = count($arRequestedURI);
        
        if($iConfigPieces==$iRequestedPieces)
        {
            //bug($arConfigURI,"arrutauri");
            //bug($arRequestedURI,"arrequri");
            foreach($arConfigURI as $iPiece=>$sConfigPiece)
            {
                $sRequestedPiece = $arRequestedURI[$iPiece];
                
                if($this->has_regexp($sConfigPiece))
                {
                    //Si no es expregular no puedo usar pregmatch pq sino el patron // casa con todo
                    $sPatronRouter = TFW_WS.$sConfigPiece.TFW_WS;
                    $isEqual = preg_match($sPatronRouter,$sRequestedPiece);
                    //bug("in preg_match: patron:$sPatronRouter, stringtest:$sRequestedPiece, result:$isEqual");
                }
                else
                {
                    $isEqual = ($sConfigPiece==$sRequestedPiece);
                }    
                if(!$isEqual) 
                    return FALSE;                
            }//Fin foreach            
            return TRUE;
        }//Fin igual trozos
        return FALSE;
    }//match_all_pieces
    
    /**
    * Lee la uri en el navegador y la coteja con cada ruta definida en 
    * el archivo router.php.  Si encuentra la correspondiente devuelve 
    * un array con el controlador y la funcion a ejecutar
    * 
    * @param string $sRequestedURI
    * @param array $arR
    * @return array $arControlMetodo El controlador y la funcion a ejecutar. nombre_controlador, nombre_funcion
    */
    private function uri_to_controller()
    {
        $arControlMethod404 = array("controller"=>"homes","method"=>"error_404");
        
        $arRequestedURI = $this->arRequestedURI;
        //bug($this->arConfigSame,"same"); bug($this->arConfigSame1,"same+1");
        //como se están comprobando rtuas+1 se añade el item vacio
        $arRequestedURI[]="";
        foreach($this->arConfigSame1 as $arConfigRoute)
        {
            $sConfigURI = $arConfigRoute["request_uri"];
            $sConfigURIClean = $sConfigURI;
            
            $this->isPage=FALSE;
            if($this->has_page_pattern($sConfigURI))
                $this->isPage=TRUE;
            //quito las marcas de paso de parametros a GET
            $this->remove_params($sConfigURIClean);
            $arConfigClean = explode(TFW_WS,$sConfigURIClean);
            
            //bug($arConfigClean,$arRequestedURI);
            $isMatchAll = $this->match_all_pieces($arConfigClean,$arRequestedURI);
            if($isMatchAll) 
            {
                if(!$arConfigRoute["controller"]) $arConfigRoute["controller"] = $this->arRequestedURI[0];
                if(!$arConfigRoute["method"]) $arConfigRoute["method"] = "get_list";
                $this->arConfigRouteFound = $arConfigRoute;
                $this->arConfigURIFound = explode(TFW_WS,$sConfigURI);
                //bug($this->arRequestedURI,"arRequestURI");bug($this->arRouteFound,"routefound");die;
                return $this->arConfigRouteFound;
            }
        }//fin foreach arConfigSame1
        
        
        foreach($this->arConfigSame as $arConfigRoute)
        {
            $sConfigURI = $arConfigRoute["request_uri"];
            $sConfigURIClean = $sConfigURI;
            
            $this->isPage=FALSE;
            if($this->has_page_pattern($sConfigURI))
                $this->isPage=TRUE;
            
            //quito todas las marcas tipo :id:<subcadena o patron>:id:
            $this->remove_params($sConfigURIClean);
            //Comprueba la URI de la ruta_i con la URI pedida en trozos
            $arConfigClean = explode(TFW_WS,$sConfigURIClean);
            //bug($arConfigClean,"same: ConfigUri");bug($this->arRequestedURI,"same: requested URI");
            $isMatchAll = $this->match_all_pieces($arConfigClean,$this->arRequestedURI);
            if($isMatchAll) 
            {
                if(!$arConfigRoute["controller"]) $arConfigRoute["controller"] = $this->arRequestedURI[0];
                if(!$arConfigRoute["method"]) $arConfigRoute["method"] = "get_list";
                $this->arConfigRouteFound = $arConfigRoute;
                $this->arConfigURIFound = explode(TFW_WS,$sConfigURI);
                //bug($this->arRequestedURI,"arRequestURI");bug($this->arRouteFound,"routefound");die;
                return $this->arConfigRouteFound;
            }
        }//fin foreach $arConfigSame
        //die("end_uri_to_controller");
        
        return $arControlMethod404;
    }//uri_to_controller
    
    /**
    * Asigna el nombre del controlador a ser ejecutado a $this->_the_controller_path;
    * Asigna el nombre de la funcion a ser ejecutad a $this->_the_function
    */
    public function get_controller()
    {
        $arControlMethod = $this->uri_to_controller();
        //bug($this->isPage,"this.isPage");bug($this->arConfigRouteFound,"this.arConfigRouteFound");die;
        if($this->arConfigRouteFound)
        {
            //$sUrl = "";
            if($this->isSlashAdded)
            {
                //bug("isslache");
                if($this->isPage && $this->sRequestedLastPiece=="1")
                    $sUrl = $this->remove_last_char($this->sRequestedURI,"1");
                else
                    $sUrl = $this->sRequestedURI.TFW_WS;
                
                //bug($this->isPage,"this.isPage");bug($sUrl,"isSlashed");die;
                //bug($sUrl,"no isSlash");bugss("tfw_redirect301");die;
                $this->save_post($sUrl);
                $this->redirect($sUrl,301);
            }
            elseif($this->isPage && $this->sRequestedLastPiece=="1")
            {   
                //la url estaba bien formada y es de paginacion pero termina en 1/ por lo tanto la envio a la raiz
                $sUrl = $this->remove_last_char($this->sRequestedURI);
                $sUrl = $this->remove_last_char($sUrl,"1");
                
                $this->save_post($sUrl);
                //bug($sUrl,"page1 redirect");bugss("tfw_redirect301");bugp();die;
                $this->redirect($sUrl,301);
            }
            else
            {
                //bug($this->sRequestedURI,"content");bugss("tfw_redirect301");bugp();die;
                //si hubiera algo enviado previament por post en una redirección se recuperaría
                $this->load_post($this->sRequestedURI);
                //si llega aqui se va al contenido directamente
                //bug($this->isPage,"page: No redirect muestra contenido");die;
            } 
           
        }//if this->arConfigRouteFound
//        else
//        {
//            //si no se ha encontrado ira el array a devolver con el modulo y metodo 404
//        }
        $this->add_params_to_get();
        return $arControlMethod;
    }

    private function add_params_to_get()
    {
        //bug($this->arRouteFound["params"]);
        if(is_array($this->arConfigRouteFound["params"]))
        {    
            foreach($this->arConfigRouteFound["params"] as $iUrlPosParam=>$sNameParam)
                $_GET[$sNameParam] = $this->arRequestedURI[$iUrlPosParam];
        }
        elseif($this->arConfigRouteFound["params"])
        {
            //string tipo "3=id,4=code_erp,5=marca";
            $arParams = explode(",",$this->arConfigRouteFound["params"]);
            foreach($arParams as $sParam)
            {
                $arPosParam = explode("=",$sParam);
                //0: Posicion, 1: el nombre a guardar en GET
                $_GET[$arPosParam[1]] = $this->arRequestedURI[$arPosParam[0]];
            }
        }
        
        //bug($this->arConfigURIFound,"config found"); bug($this->arRequestedURI,"requested"); //die;
        //puede que no tenga el array params sino que vengan los parámetros encerrados en :<parametro>: subcadena :<parametro>:
        //bug($this->isPage,"isPage en add_params_to_get");
        
        //Recorro todos los trozos de la URL configurada
        foreach($this->arConfigURIFound as $iPiece=>$sConfPiece)
        {
            $arParams = $this->get_all_matches(":(.*?):",$sConfPiece);
            
            if($arParams)
            {
                //bug($arParams,"confpiece[$iPiece]".$sConfPiece);
                $sReqPiece = $this->arRequestedURI[$iPiece];
                //bug($sReqPiece,"reqpiece");//die;
                //si hay parametros tipo :id:algo:id:
                foreach($arParams as $sParam)
                {
                    $arResult = array();
                    $sPattern = "/$sParam(.*?)$sParam/";
                    //busco el patron guardado en la configuración
                    $sPattern = preg_match($sPattern,$sConfPiece,$arResult);
                    //bug($arResult[1],"pattern found for $sParam in $sConfigPiece => $sPattern");
                    $sPattern = $arResult[1];
                    //pr($sPattern,"patron encontrado");                    
                    //$sPattern = preg_match($sPattern,$sRequestedPiece,$arResult);
                    $arResult = $this->get_all_matches($sPattern,$sReqPiece);
                    //bug($arResult,"value found for $sParam in $sRequestedPiece => $sPattern");
                    $sParam = str_replace(":","",$sParam);
                    $_GET[$sParam] = $arResult[0];
                    
                    //bug($this->isPage,"isPage");bug($sParam,"param page");die;
                    if($sParam=="page")
                    {
                        $iPage = $arResult[0];
                        if(!$iPage) $iPage=1;
                        $_GET["page"] = $iPage;
                        $_GET["selPage"] = $iPage;
                        //$_POST["selPage"] = $iPage;
                        //$_POST["page"] = $iPage;
                    }//if(sParam)
                }//fin foreach arParams
            }//if($arParams)
        }//fin foreach arRequestedURI

        //bugg();
    }//add_params_to_get
        
    
    private function get_all_matches($sPattern,$sTeststring)
    {
        $arResult = array();
        $sPattern="/$sPattern/";
        $arPregMatch = preg_match_all($sPattern,$sTeststring,$arResult);
        //solo recupero los distintos.
        //bug($arResult);
        $arResult = array_unique($arResult[0]);
        return $arResult;
    }
    
    private function remove_params(&$sConfigURI)
    {
        /*
         * http://es.wikipedia.org/wiki/Expresi%C3%B3n_regular
         * 
         * (.*?)
         * 
        De esta forma si se utiliza ".*" para encontrar cualquier cadena que se encuentre entre alguna marca y se le aplica sobre el texto 
        se esperaría que el motor de búsqueda encuentre los textos (subcadenas), sin embargo, debido a esta característica, 
        en su lugar encontrará el texto :id:esto_es_un_id:description:estoesunadescripcionmuylargacon::dospuntos y espacio:
        Esto sucede porque el asterisco le dice al motor de búsqueda que llene todos los espacios posibles entre los dos ":". 
        Para obtener el resultado deseado se debe utilizar el asterisco en conjunto con el signo de interrogación de la siguiente 
        forma: ":(.*?):" Esto es equivalente a decirle al motor de búsqueda que "Encuentre un ":" de apertura y luego encuentre cualquier 
        secuencia de caracteres hasta que encuentre un ":" de cierre".
        */        
        $sPattern = ":(.*?):";
        $arResult = $this->get_all_matches($sPattern,$sConfigURI);
        if($arResult)
            foreach($arResult as $sParam)
                $sConfigURI = str_replace($sParam,"",$sConfigURI);
        
    }
    
    private function has_page_pattern($sRequestUri){return strstr($sRequestUri,":page:");}
    
    /**
    * Redirige el trafico a la URL pasada como argumento
    * y termina el script con exit;
    * @param string $sURL http://www.example.com/  
    */
    public function redirect($sURL,$iCode=0)
    {
        if(!headers_sent())
        {   
            switch($iCode)
            {
                case 301:
                    header("HTTP/1.1 301 Moved Permanently");
                break;
            }     
            header("Location: ".$sURL);
            exit;
        }
    }
    
    /**
    * Elimina del $string el primer caracter si es igual a $c
    * @param string $sString La cadena sobre la que se operara
    * @param char $cLastChar El caracter que se desea eliminar
    * @return string String sin el primer caracter
    */
    public function remove_first_char($sString,$cLastChar="/")
    {
        $cFirstChar = $sString{0};
        if($cFirstChar == $cLastChar)
            $sString = substr($sString,1);
        return $sString;
    }
    
    public function remove_last_char($sString,$cLastChar="/")
    {
        $iStrLen = strlen($sString);
        $cLast = $sString{$iStrLen-1};
        
        if($cLast == $cLastChar)
            $sString = substr($sString,0,$iStrLen-1);
        return $sString;
    }
    
    public function is_lastchar_slash($sURL)
    {
        $iLen = strlen($sURL);
        if($iLen>0)
            $cLastChar = $sURL{$iLen-1};
        return ($cLastChar == "/");
    }
       
    private function save_post($sUrl)
    {
        if($_POST)
        {
            session_start();
            $_SESSION["tfw_redirect301"][$sUrl] = $_POST;
            session_write_close();
        }
    }
 
    private function load_post($sUrl)
    {
        session_start();
        if($_SESSION["tfw_redirect301"][$sUrl])
        {
            $_POST = $_SESSION["tfw_redirect301"][$sUrl];
            unset($_SESSION["tfw_redirect301"][$sUrl]);
        }
        session_write_close();
    }
    
}//ComponentRouter
