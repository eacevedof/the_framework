<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.5
 * @name AppComponentSuspicion
 * @file appcomponent_involved.php   
 * @date 22-08-2014 03:40 (SPAIN)
 * @observations: 
 *      Para aplicación de Unión Caribe. Meldings sobre transacciones
 * @requires:
 *      
 */

include_once("theapplication_component.php");
class AppComponentSuspicion extends TheApplicationComponent
{
    private $iIdSuspicion;
    protected $oBehaviourSuspicion;
    protected $oComponentPdf;
    protected $sPathToSave;
    
    public function __construct()
    {
        parent::__construct();
    }
    
    public function is_dollars($arAmount)
    {
        foreach($arAmount as $arOperation)
            if($arOperation["cotipmov"]=="003") 
                if($arOperation["comoneda"]=="USD")
                    return TRUE;
        return FALSE;  
    }
    //**********************************
    //             SETS
    //**********************************
    public function set_id_suspicion($value){$this->iIdSuspicion=$value;}

    //**********************************
    //             GETS
    //**********************************
    public function get_amount($arAmount)
    {
        //Correccion: En el monto solo deben mostrarse los florines pq los $ se muestran abajo
        $sAmount = "";
        
        $fFlorins = $this->get_afl($arAmount);
        //$fDollars = $this->get_us_dollars($arAmount);
        
        $arDevolution = $this->get_devolution($arAmount);
        //si hay devolucion
        if($arDevolution)
        {
            if($arDevolution["comoneda"]=="COP") $fFlorins = $fFlorins - $arDevolution["covalcre"];
            //elseif($arDevolution["comoneda"]=="USD") $fDollars = $fDollars - $arDevolution["covalcre"];
        }
        $fFlorins = number_format($fFlorins,2,",",".");
        //$fDollars = number_format($fDollars,2,",",".");
        
        //if($fDollars!=0) $sAmount .= "USD $fDollars ";
        if($fFlorins!=0) $sAmount .= "AWG $fFlorins "; 
        
        return $sAmount;
    }
    
    public function get_bills_breakdown($arAmount)
    {
        $sAmount = "";
        //30 X AWG100; 1 X AWG10
        foreach($arAmount as $arBills)
        {   
            $sAmount .= (int)$arBills["oddebito"];
            $sAmount .= " X ";
            $sAmount .= "USD";
            $sAmount .= (int)$arBills["oddenomi"];
            $sAmount .= "; ";
        }
        return $sAmount;
    }
    
    public function get_observations($arObservations,$sIdObdocume="")
    {
        $sObservations = "";
        foreach($arObservations as $arObservation)
            $sObservations .= $arObservation["ocobserv"]." ";
                
        //quito la marca
        if($sIdObdocume)
            $sObservations = str_replace ("@$sIdObdocume#","",$sObservations);
        
        return $sObservations;
    }
    
    /**
     * Extrae la linea de devolucion que siempre sera única
     * @param type $arAmount
     * @return null
     */
    private function get_devolution($arAmount)
    {
        foreach($arAmount as $arOperation)
            if($arOperation["cotipmov"]=="DEV")
                return $arOperation;
        return NULL;
    }
    
    /**
     * Recupera la suma de todas las lineas recibidas en dolares
     * @param type $arAmount
     * @return type
     */
    private function get_us_dollars($arAmount)
    {
        $fDollars = 0;
        foreach($arAmount as $arOperation)
            if($arOperation["cotipmov"]=="003") 
                if($arOperation["comoneda"]=="USD")
                    $fDollars = $fDollars + $arOperation["covaldeb"];
        return $fDollars;        
    }
    
    /**
     * Recupera la suma de todas las lineas recibidas en florines
     * @param type $arAmount
     * @return type
     */
    private function get_afl($arAmount)
    {
        $fFlorins = 0;
        foreach($arAmount as $arOperation)
            if($arOperation["cotipmov"]=="003") 
                if($arOperation["comoneda"]=="COP")
                    $fFlorins = $fFlorins + $arOperation["covaldeb"];
        return $fFlorins;        
    }
    
    public function get_date_converted($sDbDateTime,$sDateSep="/")
    {
        //2014-06-20 20:19:38
        // 2012-04-03 00:00:00.000 =   "3-4-2012  0:00"
        $sDbDateTime = trim($sDbDateTime);
        if($sDbDateTime)
        {    
            $sDbDateTime = str_replace("  "," ",$sDbDateTime);
            $arTimeDate = explode(" ",$sDbDateTime);

            //yyyy - mm - dd
            $arTimeDate = explode("-",$arTimeDate[0]); 

            //pasa yyyy-mm-dd ->dd-mm-yyyy 
            $arReturn[0] = sprintf("%02d",$arTimeDate[2]);
            $arReturn[1] = sprintf("%02d",$arTimeDate[1]);
            $arReturn[2] = sprintf("%04d",$arTimeDate[0]);

            return implode($sDateSep,$arReturn);
        }
        return NULL;
    }
    
    public function get_hour_converted($sDbDateTime,$sDateSep=":")
    {
        /*update tabgiros set GRFECHAG=CONVERT(datetime,'2014-06-20 20:19:38.00',101)
         * CONVERT(datetime,'2014-06-20 20:19:38.00',101)
        // 24-hour time to 12-hour time 
        $time_in_12_hour_format  = date("g:i a", strtotime("13:30"));
        // 12-hour time to 24-hour time 
        $time_in_24_hour_format  = date("H:i", strtotime("1:30 PM"));
        */
        // 2012-04-03 00:00:00.000 =   "3-4-2012  0:00"
        $sDbDateTime = trim($sDbDateTime);
        $sDbDateTime = str_replace("  "," ",$sDbDateTime);
        $arTimeDate = explode(" ",$sDbDateTime);
        $arTimeDate = explode(":",$arTimeDate[1]); 
        //quito los segundos
        unset($arTimeDate[2]);
        //bug($arTimeDate);die;
        //hh
        $arTimeDate[0] = sprintf("%02d",$arTimeDate[0]);
        //mm
        $arTimeDate[1] = sprintf("%02d",$arTimeDate[1]);
        
        //$sReturn = date("H:i",strtotime());
        return implode($sDateSep,$arTimeDate);
    }    
    
    //**********************************
    //           MAKE PUBLIC
    //**********************************
    public function generate_pdf()
    {
        //errorson();
        //C:\inetpub\wwwroot\proy_tasks\the_public\..\the_public/suspicions/suspicion_5.pdf error
        $sPathPdfFile = TFW_PATH_FOL_ROOTDS."the_public/downloads/suspicions/suspicion_$this->iIdSuspicion.pdf";
        $this->oBehaviourSuspicion = new AppBehaviourSuspicion($this->iIdSuspicion);
        $this->oBehaviourSuspicion->log_save_select();

        $oPdf = new AppComponentSuspicionPdf();
        $oPdf->set_behaviour_suspicion($this->oBehaviourSuspicion);
        $oPdf->set_path_output_file($sPathPdfFile);
        $oPdf->generate(1);
    }
    
    private function originalpdf()
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

}
