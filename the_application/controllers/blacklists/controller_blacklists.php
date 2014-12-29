<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.1.1
 * @name ControllerBlacklists
 * @file controller_blacklists.php 
 * @date 04-07-2014 10:01 (SPAIN)
 * @observations: update blacklist table
 */
import_model("user");
import_model("ofac,un,lstnegra,lstn_brief,lstn_client,lstn_ofac,lstn_un","uc");
import_component("page,filter");

import_helper("form,form_fieldset,form_legend,input_text,label,anchor,table,table_typed,textarea,input_date");
import_helper("input_password,button_basic,raw,div,javascript");

import_appmain("view,controller");
import_appcomponent("pdf_lstnbrief");
import_apphelper("controlgroup");

class ControllerBlacklists extends TheApplicationController
{
    //ofac
    private $sTmpXmlUri = "http://www.treasury.gov/ofac/downloads/sdn.xml";
    private $sFilenameXmlOfa;
    private $sFilenameXmlUn;

    private $isXmlErrorOfac = false;
    private $isXmlErrorUn = false;
    //Variables necesarias a pasar para informes en pdf
    private $arObjNewClientOfac = array();
    private $arObjNewClientUn = array();
    
    private $arDelClientOfac = array();
    private $arDelClientUn = array();
    //alqaeda: http://www.un.org/sc/committees/1267/AQList.xml
    
    public function __construct()
    {
        $this->sModuleName = "blacklists";
        //crea oLog, oView, oSession
        parent::__construct();
        $this->oSessionUser = $this->oSession->get("oSessionUser");
        $this->oView->set_session_user($this->oSessionUser);        
        $this->oView->set_layout("twocolumn");
        
        //se usa para guardar lo que se pasa $this->output
        $this->_path_folder_log = $this->get_fixed_syspath(TFW_PATH_FOLDER_LOGDS."shellscripts");
        $this->_path_filename_log = "blacklist_updating_web_".date("Ymd").".log";
        
        $this->sFilenameXmlOfa = date("Ymd")."_ofa.xml";
        $this->sFilenameXmlUn = date("Ymd")."_un.xml";
//        ob_end_flush();
//        ob_end_clean();
    }
    
    private function load_ofac()
    {
        $oModelLstnOfac = new ModelLstnOfac();
        $oModelLstnOfac->truncate_table();
        $this->sTmpXmlUri = TFW_PATH_FOL_ROOTDS."the_public/cache/$this->sFilenameXmlOfa";
        //pr($this->sTmpXmlUri);
        $oXml = simplexml_load_file($this->sTmpXmlUri);
        
        if(is_object($oXml))
        {    
            //bug($oXml->publshInformation);
            $sPublishDate = $oXml->publshInformation->Publish_Date;
            $sRecordCount = $oXml->publshInformation->Record_Count;
            $arNodesObj = $oXml->sdnEntry;

            foreach($arNodesObj as $oNodeObj)
            {
                //bug($oSdnEntry->akaList);
                $oModelLstnOfac = new ModelLstnOfac();
                $oModelLstnOfac->set_publish_date($this->trim_upper($sPublishDate));
                $oModelLstnOfac->set_record_count($sRecordCount);

                $oModelLstnOfac->set_prim_uid($this->trim_upper($oNodeObj->uid));
                $oModelLstnOfac->set_prim_firstname($this->trim_upper($oNodeObj->firstName));
                $oModelLstnOfac->set_prim_lastname($this->trim_upper($oNodeObj->lastName));
                $oModelLstnOfac->set_prim_sdntype($this->trim_upper($oNodeObj->sdnType));
                $oModelLstnOfac->set_prim_programlist_program($this->trim_upper($oNodeObj->programList->program));

                $oModelLstnOfac->set_address_uid($this->trim_upper($oNodeObj->addressList->address->uid));
                $oModelLstnOfac->set_address_type_country($this->trim_upper($oNodeObj->addressList->address->country));

                //se añade el individuo sin su aka
                //$oModelLstnOfac->autoinsert_mssql();
                $oModelLstnOfac->insert();

                //He tenido que transformarlo en array porque aun cuando akaList tenia este tipo
                //no permitia recorrerlo. Cuando se recupera el nodo aka a veces es array y otras object o null
                $mxNodes = (array)$oNodeObj->akaList;
                $mxNodes = $mxNodes["aka"];

                $oModelLstnOfac->set_prim_firstname("");
                $oModelLstnOfac->set_prim_lastname("");

                if(is_array($mxNodes))
                {    
                    //bug("array");
                    foreach($mxNodes as $i=>$oAka)
                    {
                        //bug($oAka,"array_$i");//bug(is_array($oAka));
                        $oModelLstnOfac->set_aka_uid($oAka->uid);
                        $oModelLstnOfac->set_aka_type($this->trim_upper($oAka->type));
                        $oModelLstnOfac->set_aka_category($this->trim_upper($oAka->category));
                        $oModelLstnOfac->set_aka_firstname($this->trim_upper($oAka->firstName));
                        $oModelLstnOfac->set_aka_lastname($this->trim_upper($oAka->lastName));
                        $oModelLstnOfac->insert();
                    }
                }
                //is_object
                elseif(is_object($mxNodes))
                {
                    //bug($mxNodes,"object");
                    $oModelLstnOfac->set_aka_uid($mxNodes->uid);
                    $oModelLstnOfac->set_aka_type($this->trim_upper($mxNodes->type));
                    $oModelLstnOfac->set_aka_category($this->trim_upper($mxNodes->category));
                    $oModelLstnOfac->set_aka_firstname($this->trim_upper($mxNodes->firstName));
                    $oModelLstnOfac->set_aka_lastname($this->trim_upper($mxNodes->lastName));
                    $oModelLstnOfac->insert();
                }
                //$mxNodes = null;
            }//die;
            //elimina los prim_sdntype Vessel y Aircraft
            $oModelLstnOfac->delete_types();
        }//fin if($oxml)
        else
        {
            $this->isXmlErrorOfac=true;
            $sMessage = "blacklists->load_ofac(): Error al recuperar $this->sTmpXmlUri";
            $this->log_error($sMessage);
            $this->add_error($sMessage);
        }
    }
    
    private function load_un()
    {
        $oModelLstUn = new ModelLstnUn();
        $oModelLstUn->truncate_table();

        $this->sTmpXmlUri = TFW_PATH_FOL_ROOTDS."the_public/cache/$this->sFilenameXmlUn";
        $oXml = simplexml_load_file($this->sTmpXmlUri);

        if(is_object($oXml))
        {
            //bug($oXml,"alqaeda");
            //$sxml->elem["attrib"]
            $sPublishDate = $oXml["dateGenerated"];
            //2013-01-15
            $sPublishDate = substr($sPublishDate, 0, 10);
            //$sRecordCount = count($oXml->INDIVIDUALS->INDIVIDUAL)+count($oXml->ENTITIES->ENTITY);
            $arNodes = $oXml->INDIVIDUALS->INDIVIDUAL;
            foreach($arNodes as $oNode)
            {
                $oModelLstUn = new ModelLstnUn();
                $oModelLstUn->set_date_generated($sPublishDate);
                $oModelLstUn->set_type("INDIVIDUAL");            
                $oModelLstUn->set_dataid($oNode->DATAID);
                $iLastUpdate = count($oNode->LAST_DAY_UPDATED->VALUE)-1;

                $oModelLstUn->set_first_name($this->trim_upper($oNode->FIRST_NAME));
                $oModelLstUn->set_second_name($this->trim_upper($oNode->SECOND_NAME));
                $oModelLstUn->set_sort_key($this->trim_upper($oNode->SORT_KEY));//NOMBRE COMPLETO            
                //"2011-12-13T00:00:00"
                $oModelLstUn->set_last_day_updated($this->trim_upper($oNode->LAST_DAY_UPDATED->VALUE[$iLastUpdate]));
                $oModelLstUn->set_list_type($this->trim_upper($oNode->LIST_TYPE->VALUE));
                $oModelLstUn->set_nationality($this->trim_upper($oNode->NATIONALITY->VALUE));
                $oModelLstUn->set_reference_number($this->trim_upper($oNode->REFERENCE_NUMBER));

                $oModelLstUn->set_un_list_type($this->trim_upper($oNode->UN_LIST_TYPE));
                $oModelLstUn->insert();

                $oModelLstUn->set_first_name("");
                $oModelLstUn->set_second_name("");
                $oModelLstUn->set_sort_key("");//NOMBRE COMPLETO                        

                //LISTADO DE ALIAS
                $arObjAliases = $oNode->INDIVIDUAL_ALIAS;
                foreach($arObjAliases as $oAlias)
                {
                    $oModelLstUn->set_sort_key($this->trim_upper($oAlias->ALIAS_NAME));
                    //$oModelLstUn->autoinsert_mssql();
                    $oModelLstUn->insert();
                }
                //$oAlias=null;
            }//fin foreach nodes

            $oModelLstUn = new ModelLstnUn();
            $oModelLstUn->set_date_generated($sPublishDate);
            $arNodes = $oXml->ENTITIES->ENTITY;
            $oModelLstUn->set_type("ENTITY");
            foreach($arNodes as $oNode)
            {
                $oModelLstUn->set_dataid($oNode->DATAID);
                $oModelLstUn->set_first_name($this->trim_upper($oNode->FIRST_NAME));
                $iLastUpdate = count($oNode->LAST_DAY_UPDATED->VALUE)-1;
                $oModelLstUn->set_last_day_updated($this->trim_upper($oNode->LAST_DAY_UPDATED->VALUE[$iLastUpdate]));
                $oModelLstUn->set_list_type($this->trim_upper($oNode->LIST_TYPE->VALUE));
                $oModelLstUn->set_nationality($this->trim_upper($oNode->NATIONALITY->VALUE));
                $oModelLstUn->set_reference_number($this->trim_upper($oNode->REFERENCE_NUMBER));
                $oModelLstUn->set_second_name($this->trim_upper($oNode->SECOND_NAME));
                $oModelLstUn->set_sort_key($this->trim_upper($oNode->SORT_KEY));//NOMBRE COMPLETO
                $oModelLstUn->set_un_list_type($this->trim_upper($oNode->UN_LIST_TYPE));
                $oModelLstUn->insert();

                $oModelLstUn->set_first_name("");
                $oModelLstUn->set_second_name("");
                $oModelLstUn->set_sort_key("");//NOMBRE COMPLETO                        

                $arObjAliases = $oNode->ENTITY_ALIAS;
                foreach($arObjAliases as $oAlias)
                {
                    //SORT_KEY es el nombre completo
                    $oModelLstUn->set_sort_key($this->trim_upper($oAlias->ALIAS_NAME));
                    $oModelLstUn->insert();
                }//fin foreach arObjAliases
            }//fin foreach nodes
        }//fin is_object oXml
        //oXml==false
        else
        {
            $this->isXmlErrorUn = true;
            $sMessage = "blacklists->load_un(): Error al recuperar $this->sTmpXmlUri";
            $this->log_error($sMessage);
            $this->add_error($sMessage);
        }
    }//fin load_un
    
    private function update_un()
    {
        $sSoloFecha = date("Ymd");
        $sSoloHora = date("His");
        //2013-02-23 05:55:49.000  Fromato vladi: 24/11/2011 1:20:57 PM
        $sFechaHoy = date("Y-m-d H:i:s").".000";
        //$sFechaHoyUc = date("Y-m-d H:i:s").".000";
        $sFechaHoyUc = date("d/m/Y h:i:s A");
        $this->log_custom("fecha a guardar en bd: $sFechaHoy");
        $this->log_custom("fecha hoy uc $sFechaHoy");
        //bug($sFechaHoy);
        $oModelLstnBrief = new ModelLstnBrief();
        $oModelLstnBrief->set_fecha($sSoloFecha);
        $oModelLstnBrief->set_hora($sSoloHora);
        $oModelLstnBrief->set_tipo_lista("un");
        $oModelLstnBrief->set_log_file("blacklist_updating_$sSoloFecha.log");
        $oModelLstnBrief->set_fuente_url("http://www.un.org/sc/committees/1267/AQList.xml");

        $this->output("[***$sFechaHoy***]: UPDATING BLACKLIST UN\n");
        $this->load_un();//
        
        if(!$this->isXmlErrorUn)
        {    /**/
            //Los que hay que modificar son los que tienen su estado SU.
            //Dejaron de estar en UN y nuevamente se han incluido
            $oModelLstnUn = new ModelLstnUn();
            $oModelLstNegra = new ModelLstnegra(); 
            $arRows = $oModelLstNegra->get_active_in_un();
            //bug($arRows,"exists sus",1);
            $iNumRows = count($arRows);
            $this->output("MODIFICAR UN: $iNumRows\n");
            if($iNumRows>0)
            {
                $this->output("line - un_id - ln_id - ln_nombre [oModelLstNegra->get_active_in_un()]\n");
                foreach($arRows as $i=>$arField)
                {
                    $codUn = $arField["un_id"];
                    $codListaNegra = $arField["ln_id"];
                    $sFullName = $arField["ln_nombre"];
                    $this->output("$i: $codUn - $codListaNegra - $sFullName\n");
                    $oModelLstNegra->set_lnidlist($codListaNegra);
                    $oModelLstNegra->set_lnestado("AC");
                    //marca que indica los añadidos
                    $oModelLstNegra->set_lncontro("modac");
                    $oModelLstNegra->set_lncduser("CUMPLIMIENTO");
                    $oModelLstNegra->set_lnfecmod($sFechaHoyUc);
                    //devuleve 2013-02-22
                    $oModelLstNegra->set_lnfecofi($oModelLstnUn->get_raw_fecha());
                    $oModelLstNegra->update_estado();
                }
            }
            $this->output("FIN MODIFICAR UN\n");

            /**/
            //Los nuevos son los que no existen (activos o inactivos) en UN
            //$oModelLstnUn = new ModelLstnUn();
            $sPublicDate = $oModelLstnUn->get_fecha();
            //20130124
            $sPublicDate = substr($sPublicDate,4,4).substr($sPublicDate,2,2).substr($sPublicDate,0,2);
            $oModelLstnBrief->set_fuente_fecha($sPublicDate);

            $arRows = $oModelLstnUn->get_new_blacklisters();
            $iNumRows = count($arRows);
            $this->output("NUEVOS REGISTROS UN: $iNumRows\n");
            if($iNumRows>0)
            {
                //bug($sFechaHoy,"fecha hoy",1);
                $oModelLstNegra = new ModelLstnegra();
                $this->output("line - un_id - un_nombre [oModelLstnUn->get_new_blacklisters()]\n");
                foreach($arRows as $i=>$arField)
                {
                    $sFullName = $arField["full_name"];
                    $sIdNewer = $arField["dataid"];
                    $this->output("$i: $sIdNewer - $sFullName\n");
                    //$oModelLstNegra->set_lnidlist();//clave primaria
                    $oModelLstNegra->set_lnnrodoc($sIdNewer);
                    $oModelLstNegra->set_lnnombre($sFullName);
                    $oModelLstNegra->set_lncduser("CUMPLIMIENTO");
                    $oModelLstNegra->set_lnsidigi("N");
                    $oModelLstNegra->set_lnestado("AC"); //SU
                    $oModelLstNegra->set_lnhiperv("http://www.un.org/sc/committees/1267/AQList.xml");
                    //2013-02-23 05:55:49.000  Fromato vladi: 24/11/2011 1:20:57 PM
                    $oModelLstNegra->set_lnfecing($sFechaHoyUc);
                    $oModelLstNegra->set_lnfecmod($sFechaHoyUc);
                    $oModelLstNegra->set_lncontro("new");
                    $oModelLstNegra->set_lnfecofi($oModelLstnUn->get_raw_fecha());
                    $oModelLstNegra->set_lntiplst("O");
                    $oModelLstNegra->insert();
                }
            }
            $this->output("FIN NUEVOS UN\n");

            //Los eliminados son aquellos que ya no están 
            //en UN. Hay que cambiar su estado a SU
            $oModelLstNegra=new ModelLstnegra();
            $arRows = $oModelLstNegra->get_deleted_in_un();
            $iNumRows = count($arRows);
            $this->output("ELIMINADOS UN: $iNumRows\n");
            if($iNumRows>0)
            {
                $this->output("line - ln_id - ln_nombre [oModelLstNegra->get_deleted_in_un()]\n");
                foreach($arRows as $i=>$arField)
                {
                   $codListaNegra = $arField["ln_id"];
                   $sFullName = $arField["ln_nombre"];
                   $this->output("$i: $codListaNegra - $sFullName\n");
                   $oModelLstNegra->set_lnidlist($codListaNegra);
                   $oModelLstNegra->set_lnestado("SU");
                   $oModelLstNegra->set_lncduser("CUMPLIMIENTO");
                   $oModelLstNegra->set_lncontro("modsu");
                   $oModelLstNegra->set_lnfecmod($sFechaHoyUc);
                   //$oModelLstNegra->set_lnhiperv("http://www.un.org/sc/committees/1267/AQList.xml");
                   $oModelLstNegra->update_estado();
                }
            }
            $this->output("FIN ELIMINADOS UN\n");

            //Los clientes que existen en lstnegra con estado activo
            $oModelLstNegra=new ModelLstnegra();
            $arRows = $oModelLstNegra->get_clientes_in_un();
            //si existe un error en la consulta se sale y muestra en patnalla
            //if($oModelLstNegra->is_error()) $this->quit_error($oModelLstNegra->get_error_message());
            $iNumRows = count($arRows);
            //si no es array puede que haya ocurrido un error
            if(!is_array($arRows))$iNumRows=0;

            $this->output("CLIENTES EN UN: $iNumRows\n");
            $oModelLstnClient = new ModelLstnClient();
            //los que ya no estan activos en la lista negra
            $this->arDelClientUn = $oModelLstnClient->get_new_suspended_un();
            //bug($this->arDelClientUn,"del client un");
            if($iNumRows>0)
            {
                $this->output("line - cl_id - ln_id - ln_nombre [oModelLstNegra->get_clientes_in_un()]\n");
                foreach($arRows as $i=>$arField)
                {
                    $codCliente = $arField["cl_id"];
                    $codListaNegra = $arField["ln_id"];
                    $sFullName = $arField["ln_nombre"];
                    $oModelLstnClient->set_id_cliente($codCliente);
                    $oModelLstnClient->set_id_lstnegra($codListaNegra);
                    $oModelLstnClient->set_nombre($sFullName);
                    $oModelLstnClient->insert();
                    if(!$oModelLstnClient->is_error()) $this->arObjNewClientUn[]=$oModelLstnClient;
                    $this->output("$i: $codCliente - $codListaNegra - $sFullName\n");
                }
            }
            $this->output("FIN CLIENTES EN UN\n");
            //En este punto se han insertado y modificado los tres tipos de individuos
            //Elimino "lstn_client" los que han dejado de pertenecer a lstnegra
            $oModelLstnClient->delete_suspended();
            //El total de registros conteo de distinct id
            $oModelLstnBrief->set_total($oModelLstnUn->get_count());

            $oModelLstnBrief->set_clientes($iNumRows);
            $oModelLstnBrief->set_modificados($oModelLstNegra->get_modified());//modac
            $oModelLstnBrief->set_nuevos($oModelLstNegra->get_newers());//new
            $oModelLstnBrief->set_eliminados($oModelLstNegra->get_deleted());//modsu
            $sSoloHora = date("His");
            $oModelLstnBrief->set_hora_fin($sSoloHora);
            $oModelLstnBrief->insert();
            //quita las marcas que indica los nuevos, modificados y eliminados
            $oModelLstNegra->reset_lncontro();
        }//fin if !isErrorXml
        else
            $this->output("No se pudo recuperar xml");

        $this->output("[***$sFechaHoy***] END UN\n");
    }//fin update_un    
    
    private function update_ofac()
    {
        $sSoloFecha = date("Ymd");
        $sSoloHora = date("His");
        //2013-02-23 05:55:49.000  Fromato vladi: 24/11/2011 1:20:57 PM
        $sFechaHoy = date("Y-m-d H:i:s").".000";
        //$sFechaHoyUc = date("Y-m-d H:i:s").".000";
        $sFechaHoyUc = date("d/m/Y h:i:s A");
        $this->log_custom("fecha a guardar en bd: $sFechaHoy");
        $this->log_custom("fecha hoy uc $sFechaHoy");
        $oModelLstnBrief = new ModelLstnBrief();
        $oModelLstnBrief->set_fecha($sSoloFecha);
        $oModelLstnBrief->set_hora($sSoloHora);
        $oModelLstnBrief->set_tipo_lista("ofa");
        //$oModelLstnBrief->set_fuente_fecha($value);
        $oModelLstnBrief->set_log_file("blacklist_updating_$sSoloFecha.log");
        $oModelLstnBrief->set_fuente_url("http://www.treasury.gov/ofac/downloads/sdn.xml");
        
        $this->output("[***$sFechaHoy***]: UPDATING BLACKLIST OFAC\n");
        $this->load_ofac();//
        
        if(!$this->isXmlErrorOfac)
        {
            /**/
            //Los que hay que modificar son los que tienen su estado SU.
            //Dejaron de estar en OFAC y nuevamente se han incluido
            $oModelLstnOfac = new ModelLstnOfac();
            $oModelLstNegra = new ModelLstnegra(); 
            $oModelLstNegra->log_save_insert();
            $arRows = $oModelLstNegra->get_active_in_ofac();
            $iNumRows = count($arRows);
            $this->output("MODIFICAR OFAC: $iNumRows\n");        
            if($iNumRows>0)
            {
                $this->output("line - ofac_id - ln_id - ln_nombre [oModelLstNegra->get_active_in_ofac()]\n");
                foreach($arRows as $i=>$arField)
                {
                    $codOfac = $arField["ofac_id"];
                    $codListaNegra = $arField["ln_id"];
                    $sFullName = $arField["ln_nombre"];
                    $this->output("$i: $codOfac - $codListaNegra - $sFullName\n");
                    $oModelLstNegra->set_lnidlist($codListaNegra);
                    $oModelLstNegra->set_lnestado("AC");
                    $oModelLstNegra->set_lncontro("modac");
                    $oModelLstNegra->set_lncduser("CUMPLIMIENTO");
                    $oModelLstNegra->set_lnfecmod($sFechaHoyUc);
                    $oModelLstNegra->set_lnfecofi($oModelLstnOfac->get_raw_fecha());
                    $oModelLstNegra->update_estado();
                }
            }
            $this->output("FIN MODIFICAR OFAC\n");

            /**/
            //Los nuevos son los que no existen (activos o inactivos) en OFAC
            $arRows = $oModelLstnOfac->get_new_blacklisters();
            $iNumRows = count($arRows);
            $this->output("NUEVOS REGISTROS OFAC: $iNumRows\n");
            if($iNumRows>0)
            {
                $this->output("line - prim_uid - ofac_fullname [oModelLstnOfac->get_new_blacklisters()]\n");
                $oModelLstNegra = new ModelLstnegra();
                foreach($arRows as $i=>$arField)
                {
                    $sFullName = $arField["full_name"];
                    $sIdNewer = $arField["prim_uid"];
                    $this->output("$i: $sIdNewer - $sFullName\n");
                    //$oModelLstNegra->set_lnidlist();//clave primaria
                    $oModelLstNegra->set_lnnrodoc($sIdNewer);
                    $oModelLstNegra->set_lnnombre($sFullName);
                    $oModelLstNegra->set_lnsidigi("N");
                    $oModelLstNegra->set_lnestado("AC"); //SU
                    $oModelLstNegra->set_lncontro("new");
                    $oModelLstNegra->set_lncduser("CUMPLIMIENTO");
                    $oModelLstNegra->set_lnhiperv("http://www.treasury.gov/ofac/downloads/sdn.xml");
                    //2013-02-23 05:55:49.000  Fromato vladi: 24/11/2011 1:20:57 PM
                    $oModelLstNegra->set_lnfecing($sFechaHoyUc);
                    $oModelLstNegra->set_lnfecmod($sFechaHoyUc);
                    $oModelLstNegra->set_lnfecofi($oModelLstnOfac->get_raw_fecha());
                    $oModelLstNegra->set_lntiplst("C");
                    $oModelLstNegra->insert();
                }
            }
            $this->output("FIN NUEVOS OFAC\n");

            //Los eliminados son aquellos que ya no están 
            //en OFAC. Hay que cambiar su estado a SU
            $oModelLstNegra=new ModelLstnegra();
            $arRows = $oModelLstNegra->get_deleted_in_ofac();
            $iNumRows = count($arRows);
            $this->output("ELIMINADOS OFAC: $iNumRows\n");
            if($iNumRows>0)
            {
                $this->output("line - ln_id - ln_nombre [oModelLstNegra->get_deleted_in_ofac()]\n");
                foreach($arRows as $i=>$arField)
                {
                    $codListaNegra = $arField["ln_id"];
                    $sFullName = $arField["ln_nombre"];
                    $this->output("$i: $codListaNegra - $sFullName\n");
                    $oModelLstNegra->set_lnidlist($codListaNegra);
                    $oModelLstNegra->set_lnestado("SU");
                    $oModelLstNegra->set_lncontro("modsu");
                    $oModelLstNegra->set_lncduser("CUMPLIMIENTO");
                    $oModelLstNegra->set_lnfecmod($sFechaHoyUc);
                    $oModelLstNegra->update_estado();
                }
            }
            $this->output("FIN ELIMINADOS OFAC\n");

            //Los clientes que existen en lstnegra con estado activo
            $oModelLstNegra=new ModelLstnegra();
            $arRows = $oModelLstNegra->get_clientes_in_ofac();
            //if($oModelLstNegra->is_error()) $this->quit_error($oModelLstNegra->get_error_message());
            $iNumRows = count($arRows);
            if(!is_array($arRows))$iNumRows=0;

            $this->output("CLIENTES EN OFAC: $iNumRows\n");
            $oModelLstnClient = new ModelLstnClient();
            $this->arDelClientOfac = $oModelLstNegra->get_deleted_in_ofac();
            //bug($this->arDelClientOfac,"del client ofac");

            if($iNumRows>0)
            {
                $this->output("line - cl_id - ln_id - ln_nombre [oModelLstNegra->get_clientes_in_ofac()]n");
                foreach($arRows as $i=>$arField)
                {
                    $codCliente = $arField["cl_id"];
                    $codListaNegra = $arField["ln_id"];
                    $sFullName = $arField["ln_nombre"];
                    $oModelLstnClient->set_id_cliente($codCliente);
                    $oModelLstnClient->set_id_lstnegra($codListaNegra);
                    $oModelLstnClient->set_nombre($sFullName);
                    $oModelLstnClient->insert();
                    if(!$oModelLstnClient->is_error()) $this->arObjNewClientOfac[]=$oModelLstnClient;
                    $this->output("$i: $codCliente - $codListaNegra - $sFullName\n");
                }
            }
            $this->output("FIN CLIENTES EN OFAC\n");
            $oModelLstnClient->delete_suspended();

            $oModelLstnBrief->set_total($oModelLstnOfac->get_count());
            $oModelLstnBrief->set_clientes($iNumRows);
            $oModelLstnBrief->set_modificados($oModelLstNegra->get_modified());
            $oModelLstnBrief->set_nuevos($oModelLstNegra->get_newers());
            $oModelLstnBrief->set_eliminados($oModelLstNegra->get_deleted());
            $oModelLstnBrief->set_fuente_fecha($oModelLstnOfac->get_fecha());
            $sSoloHora = date("His");
            $oModelLstnBrief->set_hora_fin($sSoloHora);
            $oModelLstnBrief->insert();
            $oModelLstNegra->reset_lncontro();
        }//fin !XmlError
        else
            $this->output("No se pudo recuperar xml");
        
        $this->output("[***$sFechaHoy***] END OFAC\n");
    }//fin update_ofac
    
    private function trim_upper($sString)
    {
        $sString = trim($sString);
        //if(mb_detect_encoding($sString,"ASCII,UTF-8,ISO-8859-1")=="UTF-8")
        $sString = utf8_decode($sString);
        $sString = trim($sString);
        $sString = strtoupper($sString);
        $arChars = array
        ("'",",",";","Ø","¹","¨","¯","§","Ù","„","±","…","†","„","³","Š",
         "Â","€","™","³","º","Ù","ˆ","„","£","´","¡","¬","","®","?","¿","*"
         ,")","(",":","\"","“","”","‘","’");
        $sString = str_replace($arChars,"",$sString);
        return $sString;
    }
    
    public function make_brief_on_pdf()
    {
        //errorson("e");
        $oModelBrief = new ModelLstnBrief();
        $arIds = $oModelBrief->get_lasts_briefs_ids();
        //bugfile(TFW_PATH_FOL_ROOTDS."html\images\custom\logo_unioncaribe_496_133.jpg"); 
        foreach($arIds as $arId)
        {
            $iIdn=$arId["idn"];
            $oModelBrief->set_idn($iIdn);
            $oModelBrief->load_by_id();
            $sTipo = $oModelBrief->get_tipo_lista();
            //$sFecha = date("YmdHis");
            $sFecha = $oModelBrief->get_fecha().$oModelBrief->get_hora_fin();
            $oPdf = new PdfLstnBrief($oModelBrief);
            //Nuevos clientes en ofac
            $oPdf->set_new_clients_in_ofac($this->arObjNewClientOfac);
            $oPdf->set_new_clients_in_onu($this->arObjNewClientUn);
            //Clientes marcados como su en lstnegra
            $oPdf->set_suspended_clients_in_ofac($this->arDelClientOfac);
            $oPdf->set_suspended_clients_in_un($this->arDelClientUn);
            //bug(TFW_PATH_FOL_ROOTDS); //C:\Inetpub\wwwroot\proy_tasks\the_public\..\
            //$sPathPdfFile = TFW_PATH_FOL_ROOTDS."downloads/blacklist_$sFecha"."_$iIdn"."_$sTipo.pdf";
            //if($this->isConsoleCalled)
            $sPathPdfFile = TFW_PATH_FOL_ROOTDS."the_public/downloads/blacklist_$sFecha"."_$iIdn"."_$sTipo.pdf";
            //$this->log_error("blacklists: pathpdf file $sPathPdfFile");
            $oPdf->set_path_output_file($sPathPdfFile);
            $oPdf->generar(1);   
        }       
    }
    
    public function updating()
    {
        pr("..updating in progress!!");
        //escupe errores graves en pantalla que los capturará el log de consola
        errorson("e");
        $this->download_xmls();
        $this->load_ofac();
        $this->load_un();
        $this->update_ofac();
        $this->update_un();
        $this->make_brief_on_pdf();
        
        $iSeconds=15;
        $sMessage="Lists updated ";
        if($this->is_error())
        {
            $iSeconds=45;
            $sMessage.=" with errors ";
            $sMessage.=$this->get_error_message(1);
            pr($sMessage);
        }
        pr("...ending");
        $this->js_colseme_and_parent_refresh($iSeconds,$sMessage);
    }
    
    private function get_hours()
    {
        $arHours = array(""=>"None");
        for($i=0; $i<24; $i++)
        {
            $sHour = sprintf("%02d",$i);
            $arHours[$sHour]=$sHour.":00";
        }
        return $arHours;
    }
    
    private function get_filter_objects()
    {
        //CAMPOS
        $oAuxField = new HelperInputText("idn","idn");
        $oAuxField->set_value($this->get_post("idn"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("idn","Idn"));
        $arFields[] = $oAuxWrapper;
        
        $oAuxField = new HelperInputText("fecha","fecha");
        $oAuxField->set_value($this->get_post("fecha"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("fecha","Fecha"));
        $arFields[] = $oAuxWrapper;
        
        $arHours = $this->get_hours();
        $oAuxField = new HelperSelect($arHours,"hora","hora",NULL,"a");
        $oAuxField->set_value_to_select($this->get_post("hora"));
        $oAuxWrapper = new ApphelperControlGroup($oAuxField
                ,new HelperLabel("hora","Hora"));
        $arFields[] = $oAuxWrapper;
        
        return $arFields;
    }

    public function get_list()
    {
        //errorson("e");
        //bugpg();
        $arColumns = array("idn"=>"Id","fecha"=>"Fecha","hora"=>"H. Inicio","hora_fin"=>"H. Fin"
            ,"total"=>"Tot.","clientes"=>"Client.","nuevos"=>"Nuev.","eliminados"=>"Elim."
            ,"modificados"=>"Modif","tipo_lista"=>"Tipo(log)","fuente_fecha"=>"Fec. Fuente"
            ,"file_pdf"=>"Pdf","fuente_url"=>"Url");
        
        $oFilter = new ComponentFilter();
        $oFilter->set_fieldnames(array_keys($arColumns));
        //Guarda en sesion y post los campos enviados, los de orden y página
        $oFilter->refresh();
        
        $oModelBrief = new ModelLstnBrief();
        $oModelBrief->set_orderby($this->get_orderby());
        $oModelBrief->set_ordertype($this->get_ordertype());
        $arFilters = array
        (
            "idn"=>array("operator"=>"likel","value"=>$this->get_post("idn")),
            "fecha"=>array("operator"=>"liker","value"=>$this->get_post("fecha")),
            "hora"=>array("operator"=>"liker","value"=>$this->get_post("hora"))
        );
        $oModelBrief->set_filters($arFilters);
        //Listado de ids
        $arList = $oModelBrief->get_select_all_idns();
        
        $iRequestPage = $this->get_post("selPage");
        $oPage = new ComponentPage($arList,$iRequestPage);
        //Ids que corresponden a la página
        $arList = $oPage->get_items_to_show();
        //bugp();
        $arList = $oModelBrief->get_select_by_idns($arList);
        //bug($oPage); 
        $oTableBasic = new HelperTableTyped($arList,$arColumns);
        //Campos para filtros
        $oTableBasic->set_fields($this->get_filter_objects());
        $oTableBasic->set_module($this->get_current_module());
        $oTableBasic->add_class("table table-striped table-bordered table-condensed");
        $oTableBasic->set_keyfields(array("idn"));
        $oTableBasic->is_ordenable();
        $oTableBasic->set_orderby($this->get_orderby());//Order fields
        $oTableBasic->set_orderby_type($this->get_ordertype());//ASC DESC
        //$oTableBasic->set_column_picksingle();
        $oTableBasic->set_current_page($oPage->get_current());
        $oTableBasic->set_next_page($oPage->get_next());
        $oTableBasic->set_first_page($oPage->get_first());
        $oTableBasic->set_previous_page($oPage->get_previous());
        $oTableBasic->set_last_page($oPage->get_last());
        $oTableBasic->set_total_regs($oPage->get_total_regs());
        $oTableBasic->set_total_pages($oPage->get_total());
        $arAnchorCols = array
            ("tipo_lista"=>array("href"=>"file_log","innerhtml"=>"tipo_lista"),
            "file_pdf"=>array("href"=>"file_pdf","innerhtml"=>"pdf"),
            "fuente_url"=>array("href"=>"fuente_url","innerhtml"=>"xml"),
            );
        $oTableBasic->set_column_anchor($arAnchorCols);
        $oTableBasic->set_format_columns(array("fecha"=>"date","hora"=>"time6","hora_fin"=>"time6","fuente_fecha"=>"date"));

        $this->oView->add_var($oTableBasic,"oTableBasic");
        //custom view
        $this->oView->set_path_view("blacklists/view_index");
        $this->oView->show_page();
    }
    
    public function download_xmls()
    {
        //TFW_PATH_FOL_ROOTDS="termina con ../"
        $sTargetFolder=TFW_PATH_FOL_THEPUBLICDS."cache";
        $oFile = new ComponentFile("windows");
        $oFile->set_path_folder_target($sTargetFolder);
        
        //========  OFAC
        $oFile->set_filename_target($this->sFilenameXmlOfa);
        $oFile->target_remove();
        //xml ofac
        $this->sTmpXmlUri = "http://www.treasury.gov/ofac/downloads/sdn.xml";
        $sFileContent = file_get_contents($this->sTmpXmlUri);
        if(!$sFileContent)
        {
            $sMessage = "blacklists->download_xmls(): ofac content error!";
            $this->log_error($sMessage);
            $this->add_error($sMessage);
        }
        else $oFile->add_content($sFileContent);
        //bug($oFile);
        
        //========  ONU
        $oFile->set_filename_target($this->sFilenameXmlUn);
        $oFile->target_remove();
        //xml ofac
        $this->sTmpXmlUri = "http://www.un.org/sc/committees/1267/AQList.xml";
        $sFileContent = file_get_contents($this->sTmpXmlUri);
        if(!$sFileContent)
        {
            $sMessage = "blacklists->download_xmls(): onu content error!";
            $this->log_error($sMessage);
            $this->add_error($sMessage);
        }
        else $oFile->add_content($sFileContent);
        pr("..xml download finished");
    }
}
