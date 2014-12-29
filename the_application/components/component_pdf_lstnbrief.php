<?php
class PdfLstnBrief extends FPDF
{
    /**
     * @var ModelLstBrief 
     */
    private $oModelLstBrief;

    private $sPathOutputFile = "";
    //Objetos tipo lstn_client
    private $arObjNewClientOfac = array();
    private $arObjNewClientUn = array();
    
    private $arDelClientOfac = array();
    private $arDelClientUn = array();
    
    private $sTipoLista = "";
    
    public function __construct(
    ModelLstnBrief $oLstnBrief=null, $cOrientacion="P", $sUnidDistancia="mm", $sTamanoFolio="A4") 
    {
        //FPDF
        parent::__construct($cOrientacion, $sUnidDistancia, $sTamanoFolio);
        $this->oModelLstBrief = $oLstnBrief;
    }
    
    private function convert_to_userdate($sDbDate)
    {
        if(!empty($sDbDate))
        {
            $year = substr($sDbDate,0,4);
            $month = substr($sDbDate,4,2);
            $day = substr($sDbDate,6,2);
            return "$day/$month/$year";
        }
        return "-";
    }
    
    //Titulos de la pagina 1
    private function mostrar_titulos()
    {
         //Texto Superior
        $this->SetFont("Arial","B",11);
        $this->SetY(40);

        $sTipoLista = $this->oModelLstBrief->get_tipo_lista();
        if($sTipoLista=="ofa") $sTipo="OFAC";
        else $sTipo="ONU";
        
        $this->sTipoLista=$sTipo;
        
        $sCellText = "CERTIFICADO DE ACTUALIZACION DE LISTA $sTipo";
        // Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
        $this->Cell(0,0,$sCellText,0,0,"C",false);
        //------
        $sCellText = "Certificamos que el software utilizado por ";
        $this->SetY(50);
        $this->Cell(0,0,$sCellText,0,0,"C",false);
        
        $sCellText = "UNION CARIBE N.V.";
        $this->SetY(60);
        $this->Cell(0,0,$sCellText,0,0,"C",false);

        $sFecha=$this->oModelLstBrief->get_fecha();
        $sFecha=$this->convert_to_userdate($sFecha);
        //$sFecha = 
        $sCellText = "Opera con la lista actualizada a $sFecha";
        $this->SetY(70);
        $this->Cell(0,0,$sCellText,0,0,"C",false);
    }

    //Titulo página 2
    private function mostrar_titulos2()
    {
        $sFecha=$this->oModelLstBrief->get_fecha();
        $sFecha=$this->convert_to_userdate($sFecha);
         //Texto Superior
        $this->SetFont("Arial","B",12);
        $this->SetY(40);

        $sTipoLista = $this->oModelLstBrief->get_tipo_lista();
        if($sTipoLista=="ofa") $sTipo="OFAC";
        else $sTipo="ONU";
        
        $sCellText = "UNION CARIBE N.V.";
        $this->SetY(50);
        $this->Cell(0,0,$sCellText,0,0,"C",false);
                
        $sCellText = "VALIDACION DE CLIENTES CONTRA LISTA $sTipo";
        $this->SetY(60);
        $this->Cell(0,0,$sCellText,0,0,"C",false);
        //nuevos clientes encontrados en LSTNEGRA
        $iClientes = $this->oModelLstBrief->get_clientes();
        $sCellText = "No se han encontrado clientes reportados";
        if($iClientes>0)
            $sCellText = "Se han encontrado $iClientes clientes reportados";
        
        //$iY=100;//titulo
        $this->SetY(70);
        $this->Cell(0,0,$sCellText,0,0,"C",false);
        
        //Recorre los nuevos clientes y los imprime en pantalla
        $this->mostrar_nuevos_clientes($sTipo);
       
        /* PASA A LA ÚLTIMA HOJA
        $iClientes = $this->oModelLstBrief->get_eliminados();
        $sCellText = "No se han encontrado clientes eliminados";
        if($iClientes>0)
            $sCellText = "Se han encontrado $iClientes clientes eliminados";

        $this->SetY($this->GetY()+10);
        $this->Cell(0,0,$sCellText,0,0,"C",false);
         * 
         */
        //Los clientes eliminados son los que muestra el informe y no necesariamente
        //cumplen con los marcados como modsu 
        //Recorre los clientes eliminados y los imprime en pantalla
        //$this->mostrar_suspended_clientes($sTipo);
    }

    private function mostrar_nuevos_clientes($sType="")
    {
        /**
         * @var ModelLstnClient
         */
        $oNewOfac;
        
        if($sType=="OFAC") $arObjNews = $this->arObjNewClientOfac;
        else $arObjNews = $this->arObjNewClientUn;
        
        foreach($arObjNews as $oNewOfac)
        {
            $iY = $this->GetY();
            $this->SetY($iY+6);
            $sCellText = "";
            $sCellText .= "Id: ".$oNewOfac->get_id_cliente()."      Nombre: ".$oNewOfac->get_nombre();
            $this->Cell(0,0,$sCellText,0,0,"C",false);
        }
    }
    
    private function mostrar_suspended_clientes($sType="")
    {
        $arRows=array();
        if($sType=="OFAC") $arRows = $this->arDelClientOfac;
        else $arRows = $this->arDelClientUn;
        
        foreach($arRows as $arRow)
        {
            $iY = $this->GetY();
            $this->SetY($iY+6);
            $sCellText = "";
            $sCellText .= "Id: ".$arRow["id"]."      Nombre: ".$arRow["nombre"];
            $this->Cell(0,0,$sCellText,0,0,"C",false);
        }
    }
    
    private function mostrar_firmas()
    {
         //Texto Superior
        $this->SetFont("Arial","B",11);
        $this->SetY(120);
        $sCellText = "________________________________                   _______________________________";
        // Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
        $this->Cell(0,0,$sCellText,0,0,"C",false);
        
        $this->SetY(130);
        $sCellText = "Oficial de cumplimiento";
        $this->SetX(38);
        $this->Cell(0,0,$sCellText,0,0,"L",false);
    }
    
    private function mostrar_firmas2()
    {
         //Texto Superior
        $this->SetFont("Arial","B",11);
        $this->SetY(130);
        $sCellText = "________________________________";
        // Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
        $this->Cell(0,0,$sCellText,0,0,"C",false);
        
        $this->SetY(135);
        $sCellText = "Oficial de cumplimiento";
        $this->Cell(0,0,$sCellText,0,0,"C",false);
    }
   
    private function mostrar_datos()
    {
        $arBrief = $this->oModelLstBrief->get_last_newers_by_type();
        $sFecha = $arBrief["fecha"];
        $sFecha = $this->convert_to_userdate($sFecha);
        $iQuantity = (int)$arBrief["nuevos"];

        $sCellText = "Ultima fecha - Registros Nuevos - Fecha ";
        $sCellText .= "$sFecha - Cantidad $iQuantity";
        $this->SetXY(15,160);
        $this->Cell(0,0,$sCellText,0,0,"L",false);
        
        $arBrief = $this->oModelLstBrief->get_last_modfiers_by_type();
        $sFecha = $arBrief["fecha"];
        $sFecha = $this->convert_to_userdate($sFecha);
        $iQuantity = (int)$arBrief["modificados"];
        
        $sCellText = "Ultima fecha - Registros Modificados - Fecha ";
        $sCellText .= "$sFecha - Cantidad $iQuantity";
        $this->SetXY(15,170);
        $this->Cell(0,0,$sCellText,0,0,"L",false);        
        
        $arBrief = $this->oModelLstBrief->get_last_deleted_by_type();
        $sFecha = $arBrief["fecha"];
        $sFecha = $this->convert_to_userdate($sFecha);
        $iQuantity = (int)$arBrief["eliminados"];
        
        $sCellText = "Ultima fecha - Registros Eliminados - Fecha ";
        $sCellText .= "$sFecha - Cantidad $iQuantity";
        $this->SetXY(15,180);
        $this->Cell(0,0,$sCellText,0,0,"L",false);    
        
        $sFecha = $this->oModelLstBrief->get_fecha();
        $sFecha = $this->convert_to_userdate($sFecha);
        $sCellText = "Actualizacion Vigente - Fecha $sFecha";
        $this->SetXY(15,190);
        $this->Cell(0,0,$sCellText,0,0,"L",false);        
    }

    private function mostrar_pie()
    {
        $sFecha = date("d/m/Y");
        $sHora = date("H:i:s");
        $sCellText = "Fecha y Hora de Impresion: $sFecha $sHora";
        $this->SetXY(0,250);
        $this->Cell(0,0,$sCellText,0,0,"R",false);        
    }

    private function mostrar_pie2()
    {
        $sFecha = $this->oModelLstBrief->get_fecha();
        $sFecha = $this->convert_to_userdate($sFecha);
        $sTipo="ONU";
        $sTipoLista = $this->oModelLstBrief->get_tipo_lista();
        if($sTipoLista=="ofa")$sTipo="OFAC";
        
        $sCellText = "Lista $sTipo Actualizada a: $sFecha";
        $this->SetXY(0,195);
        $this->Cell(0,0,$sCellText,0,0,"R",false);        

        $sFecha = date("d/m/Y");
        $sHora = date("H:i:s");
        $sCellText = "Fecha y Hora de Impresion: $sFecha $sHora";
        $this->SetXY(0,200);
        $this->Cell(0,0,$sCellText,0,0,"R",false);        
    }

    public function generar($asFile=0)
    {
        $this->AddPage();
        $this->AliasNbPages();
        $this->SetXY(0,0);
        $this->mostrar_titulos();
        
        $this->mostrar_firmas();
        $this->mostrar_datos();
        $this->mostrar_pie();
        
        $this->AddPage();
        //Titulos 2 muestra los contadores y el listado de los usuarios
        $this->mostrar_titulos2();
        //$this->mostrar_nuevos_clientes($this->sTipoLista);
        $this->mostrar_firmas2();
        $this->mostrar_pie2();
        
        if($asFile)
            $this->Output($this->sPathOutputFile,"F");
        else
            $this->Output();
    }
    
//===========
//   SETS
//===========
    public function set_new_clients_in_ofac($arObjLstnClient=array()){$this->arObjNewClientOfac = $arObjLstnClient;}
    public function set_new_clients_in_onu($arObjLstnOnu=array()){$this->arObjNewClientUn=$arObjLstnOnu;}
    public function set_suspended_clients_in_ofac($arClientes=array()){$this->arDelClientOfac = $arClientes;}
    public function set_suspended_clients_in_un($arClientes=array()){$this->arDelClientUn=$arClientes;}
    public function set_path_output_file($sPath){$this->sPathOutputFile=$sPath;}
    public function set_lstnbrief(ModelLstnBrief $oModelLstBrief){$this->oModelLstBrief = $oModelLstBrief;}
}
