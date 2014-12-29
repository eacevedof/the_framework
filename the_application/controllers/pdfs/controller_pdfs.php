<?php
/**
 * @author Eduardo Acevedo Farje
 * @link www.eduardoaf.com
 * @version 1.0.2
 * @name ControllerPdfs
 * @file controller_pdfs.php    
 * @date 27-08-2013 07:35 (SPAIN)
 * @observations:
 * @requires
 */
import_appmain("view,controller");
import_model("lstn_brief");
import_component("pdf_lstnbrief");

class ControllerPdf extends TheApplicationController
{
    public function __construct() 
    {
        $this->sModuleName = "pdfs";
        //console,pgpage,clientdevice,sesuser,aftersucces,currop  | log,session,permission,view
        parent::__construct($this->sModuleName);        
    }
    
    public function get_lstn_brief()
    {
        $codIdBrief = $_GET["brief_id"];
        if(!empty($codIdBrief))
        {
            $oPdf = new PdfLstnBrief($oModelBrief);
            $oPdf->generar(); die;   
            $pdf -> Output("/var/invoices/"."$filename",'F'); 
        }
    }
}
