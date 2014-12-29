<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.2.1
 * @name AppComponentSuspicionPdf
 * @file appcomponent_suspicionpdf.php   
 * @date 28-06-2014 00:46 (SPAIN)
 * @observations: 
 *      UC - Union Caribe.
 *      Meldings sobre transacciones
 * @requires:
 *      helper_fpdf_cell.php
 */
//import_plugin("makefont","fpdf/makefont");
import_plugin("fpdf","fpdf");
import_helper("fpdf_cell");

class AppComponentSuspicionPdf extends FPDF
{
    private $isDebug = FALSE;
    private $arDebug;
    
    /**
     * @var AppBehaviourSuspicion 
     */
    private $oBehaviourSuspicion;
    private $arPaths = array();
   
    private $sPathOutputFile = "";
    private $sTrLabelPrefix;
    private $iRowHeight = 6;
    
    private $iCharsLine = 96;//máximo 98
    private $arCharsForNL = array("\n","\r"," ");
    private $arMultiLine = array();

    private $sFont;
    private $iFontSize = 11;
    
    private $iPageWidth = 210;
    private $iLeftCellWidth = 60;
    private $iRightCellWidth;
    private $iLeftCellHeight;
    private $iRightCellHeight;    
    private $iRightCellX;
    
    //Zona interna con margenes por ambos lados
    private $iPageInnerWidth;
    private $iPageInnerX;
  
    private $arDateFields;
    
    private $oCell;
    /**
     * 
     * @param AppBehaviourSuspicion $oBehaviourSuspicion
     * @param char $cOrientation P:potrait L:landscape
     * @param string $sMeasureUnit pt:Points,mm:Milimeters,cm:Centimeters,in:Inches
     * @param string $sDinSize A3,A4,A5,letter,legal   
     */
    public function __construct(
    AppBehaviourSuspicion $oBehaviourSuspicion=NULL, $cOrientation="P", $sMeasureUnit="mm", $sDinSize="A4") 
    {
        /*http://laterminal.wordpress.com/2008/04/03/anexar-fuentes-extra-para-un-pdf-clase-fpdf/
         * calibri.ttf,calibrib.ttf,calibrii.ttf,calibril.ttf,calibrili.ttf
        calibriz.ttf,CALIFB.TTF,CALIFI.TTF,CALIFR.TTF,CALIST.TTF,CALISTB.TTF
        CALISTBI.TTF,CALISTI.TTF*/
//        MakeFont("C:\\Windows\\Fonts\\calibri.ttf","cp1252");
//        MakeFont("C:\\Windows\\Fonts\\calibrib.ttf","cp1252");
//        MakeFont("C:\\Windows\\Fonts\\CALIFB.TTF","cp1252");
//        MakeFont("C:\\Windows\\Fonts\\CALISTB.TTF","cp1252");

        //FPDF
        parent::__construct($cOrientation, $sMeasureUnit, $sDinSize);
        $this->oCell = new HelperFpdfCell();
        $this->oBehaviourSuspicion = $oBehaviourSuspicion;
        //Traducción fija a nivel de aplicación
        import_apptranslate("suspicionspdf","app");
        $this->sTrLabelPrefix = "tr_sss_pdf_";
        $this->arPaths["logo"] = TFW_PATH_FOLDER_PICTURESDS."suspicions/logo_mot.png";
        $this->arPaths["checked"] = TFW_PATH_FOLDER_PICTURESDS."suspicions/icon_checked.png";
        $this->arPaths["unchecked"] = TFW_PATH_FOLDER_PICTURESDS."suspicions/icon_unchecked.png";
        $this->load_config();
        //$this->isDebug = 1;
    }
    
    private function load_config()
    {
        $this->sFont = "Arial";
        //Cálculo de zona de pintado 
        $this->iPageInnerX = 5;
        $this->iPageInnerWidth = $this->iPageWidth - (2*$this->iPageInnerX);
        //Cálculo de coord de celda derecha
        $this->iRightCellWidth = $this->iPageInnerWidth - $this->iLeftCellWidth;
        $this->iRightCellX = $this->iPageInnerX + $this->iLeftCellWidth;
        $this->iLeftCellHeight = $this->iRowHeight;
        $this->iRightCellHeight = $this->iRowHeight;
        
        $this->arDateFields = array("subj_birthdate","iddoc_issue_date","iddoc_expiry_date");
    }

//<editor-fold defaultstate="collapsed" desc="DRAW METHODS">      
    
    //cell_1
    private function draw_singlecell(HelperFpdfCell $oCell)
    {
        if($oCell->get_y()!==NULL) $this->SetY($oCell->get_y());
        if($oCell->get_x()!==NULL) $this->SetX($oCell->get_x());

        if($oCell->get_font())
            $this->SetFont($oCell->get_font(),$oCell->get_fontstyle(),$oCell->get_fontsize());

        $isFill = FALSE;
        if($oCell->get_backcolor()!==NULL)
        {    
            $this->SetFillColor($oCell->get_backcolor());
            $isFill = TRUE;
        }
        
        if($oCell->get_fontcolor()!==NULL)
            $this->SetTextColor($oCell->get_fontcolor());
        
        $this->Cell($oCell->get_width(),$oCell->get_height(),$oCell->get_text()
                ,$oCell->get_border(),$oCell->get_numline_unit(),$oCell->get_type_align()
                ,$isFill,$oCell->get_pagelink());
        //$this->Cell($iAncho,$iAltura,$sTitulo,$iAnchoBorde,$iSaltosLinea,$cAlineacion,fondo?TRUE|FALSE,??);
        //$this->Cell($w, $h, $txt, $border, $ln, $align, $fill, $link);        
    }//draw_singlecell
    
    //cell_2
    private function draw_multicell(HelperFpdfCell $oCell)
    {
        //MultiCell($w, $h, $txt, $border=0, $align='J', $fill=false)
        if($oCell->get_y()!==NULL) $this->SetY($oCell->get_y());
        if($oCell->get_x()!==NULL) $this->SetX($oCell->get_x());

        if($oCell->get_font())
            $this->SetFont($oCell->get_font(),$oCell->get_fontstyle(),$oCell->get_fontsize());

        $isFill = FALSE;
        if($oCell->get_backcolor()!==NULL)
        {    
            $this->SetFillColor($oCell->get_backcolor());
            $isFill = TRUE;
        }

        if($oCell->get_fontcolor()!==NULL)
            $this->SetTextColor($oCell->get_fontcolor());
        
        $this->MultiCell($oCell->get_width(),$oCell->get_height(),$oCell->get_text()
                ,$oCell->get_border(),$oCell->get_type_align(),$isFill);                   
    }//draw_multicell
    
    //cell_3
    private function draw_cell(HelperFpdfCell $oCell)
    {
        if($oCell->is_single())
            $this->draw_singlecell($oCell);
        else 
            $this->draw_multicell($oCell);
    }//drqw_cell
    
    
    //cell_4
    private function draw_leftcell($sCellText,$iFillColor=255,$iTextColor=0,$cAlign="L",$iX=NULL)
    {
        $oMultiCell = new HelperFpdfCell(FALSE);
        $oMultiCell->set_border(1);
        $oMultiCell->set_font($this->sFont);
        //$oMultiCell->set_fontstyle();
        $oMultiCell->set_fontsize($this->iFontSize);
        $oMultiCell->set_backcolor($iFillColor);//Blanco
        $oMultiCell->set_fontcolor($iTextColor);//Negro
        if($iX===NULL) $iX = $this->iPageInnerX;
        $oMultiCell->set_x($iX);
        $oMultiCell->set_width($this->iLeftCellWidth);
        $oMultiCell->set_height($this->iLeftCellHeight);
        $oMultiCell->set_type_align($cAlign);
        $oMultiCell->set_text($sCellText);
        //El metodo multicelll no se porque motivo deja incrementada y despues de pintar.
        //Así pues lo que hago es guardar una copia del Y antes de pintar para despues resetearlo
        $iTmpY = $this->GetY();
        //$this->Cell($this->iLeftCellWidth,$this->iCellHeight,$sCellText,1,0,$cAlign,TRUE);
        //$this->MultiCell($this->iLeftCellWidth,$this->iLeftCellHeight,$sCellText,1,$cAlign,TRUE);
        $this->draw_cell($oMultiCell);
        //Reseteo de Y para que rightcell se pueda pintar a la misma altura
        $this->SetY($iTmpY);
    }//draw_leftcell
    
    //cell_5
    private function draw_rightcell($sCellText,$iFillColor=255,$iTextColor=0,$cAlign="L",$iX=NULL)
    {
        $oMultiCell = new HelperFpdfCell(FALSE);
        $oMultiCell->set_border(1);
        $oMultiCell->set_font($this->sFont);
        //$oMultiCell->set_fontstyle();
        $oMultiCell->set_fontsize($this->iFontSize);
        $oMultiCell->set_backcolor($iFillColor);//Blanco
        $oMultiCell->set_fontcolor($iTextColor);//Negro
        if($iX===NULL) $iX = $this->iRightCellX;
        $oMultiCell->set_x($iX);
        
        $oMultiCell->set_width($this->iRightCellWidth);
        $oMultiCell->set_height($this->iRightCellHeight);
        
        $oMultiCell->set_type_align($cAlign);
        $oMultiCell->set_text($sCellText);

        $this->draw_cell($oMultiCell);
        //Reseteo el nuevo punto Y quitando la fila de iRowheight que se deja despues de una "MultiCell"
        $this->SetY($this->GetY()-$this->iRowHeight);
        //Reseto la altura iRowHeight por si la hubiera modificado antes en el cálculo de altura de celda
        //$this->reset_cellheights();
    }//draw_rightcell
        
    //row_1
    private function draw_blackrow($sCellText,$cAlign="L",$iX=NULL)
    {
        $oCellSingle = new HelperFpdfCell(FALSE);
        $oCellSingle->set_fontcolor(255);
        $oCellSingle->set_backcolor(0);
        $oCellSingle->set_font($this->sFont);
        $oCellSingle->set_fontstyle("B");
        $oCellSingle->set_fontsize($this->iFontSize);
        //210x249
        if($iX===NULL) $iX = $this->iPageInnerX;
        $oCellSingle->set_x($iX);
        $oCellSingle->set_width($this->iPageInnerWidth);
        $oCellSingle->set_height($this->iRowHeight);
        $oCellSingle->set_text($sCellText);
        
        $oCellSingle->set_border(1);
        $oCellSingle->set_type_align($cAlign);
        //$this->Cell($this->iPageInnerWidth,$this->iRowHeight,$sCellText,1,0,$cAlign,TRUE);
        $this->draw_cell($oCellSingle);
    }//draw_blackrow
    
    //row_2
    private function draw_greyrow($sCellText,$cAlign="L",$iX=NULL)
    {
        $iStartY = $this->GetY();
        //Celda marco con fondo
        $oCellMulti = new HelperFpdfCell();
        if($iX===NULL) $iX = $this->iPageInnerX;
        $oCellMulti->set_x($iX);
        $oCellMulti->set_y($iStartY);
        $oCellMulti->set_width($this->iPageInnerWidth);
        $oCellMulti->set_height($this->iRowHeight);
        $oCellMulti->set_fontcolor(0);
        $oCellMulti->set_backcolor(200);
        $oCellMulti->set_font($this->sFont);
        $oCellMulti->set_fontstyle("");
        $oCellMulti->set_fontsize($this->iFontSize);
        $oCellMulti->set_border(1);
        $this->draw_cell($oCellMulti);
        
        //Celda texto
        $oCellMulti->set_x($iX+5);
        $oCellMulti->set_border(0);
        $oCellMulti->set_backcolor();
        $oCellMulti->set_type_align($cAlign);
        $oCellMulti->set_text($sCellText);
        //210x249
        $this->draw_cell($oCellMulti);
    }//draw_grey_row    
    
    //row_3
    private function draw_multirow($sCellText,$cAlign="L",$iX=NULL)
    {
        $oCellMulti = new HelperFpdfCell();
        $oCellMulti->set_fontcolor(0);
        
        $oCellMulti->set_backcolor(255);
        $oCellMulti->set_font($this->sFont);
        $oCellMulti->set_fontstyle("");
        $oCellMulti->set_fontsize($this->iFontSize);
        //210x249
        if($iX===NULL) $iX = $this->iPageInnerX;
        $oCellMulti->set_x($iX);
        $oCellMulti->set_width($this->iPageInnerWidth);
        $oCellMulti->set_height($this->iRowHeight);
        $oCellMulti->set_text($sCellText);
        
        $oCellMulti->set_border(0);
        $oCellMulti->set_type_align($cAlign);        
        //$this->Cell($this->iPageInnerWidth,$this->iRowHeight,$sCellText,1,0,$cAlign,TRUE);
        $this->draw_cell($oCellMulti);
    }//draw_multirow
 
    private function draw_fieldvalue_row($sLabel,$sValue)
    {
        $iStartY = $this->GetY();//Y inicial
        //$this->add_debug("INVOLVED_DATA_I $iStartY: $sFieldName");
        //Celdas con textos
        //Texto izquierdo
        $oTmpCellMxL = new HelperFpdfCell();
        $oTmpCellMxL->set_fontcolor(1);
        $oTmpCellMxL->set_font($this->sFont);
        $oTmpCellMxL->set_fontstyle("");
        $oTmpCellMxL->set_backcolor();
        $oTmpCellMxL->set_border();
        $oTmpCellMxL->set_y($iStartY);
        $oTmpCellMxL->set_x($this->iPageInnerX);
        $oTmpCellMxL->set_width($this->iLeftCellWidth);
        $oTmpCellMxL->set_height($this->iRowHeight);
        $oTmpCellMxL->set_type_align("L");

        //$sLabel = $this->get_trlabel($sFieldName);              
        $oTmpCellMxL->set_text($sLabel);
        $this->draw_cell($oTmpCellMxL);                

        //Texto Derecho
        $oTmpCellMxR = new HelperFpdfCell();
        $oTmpCellMxR->set_fontcolor(1);
        $oTmpCellMxR->set_font($this->sFont);
        $oTmpCellMxR->set_fontstyle("");
        $oTmpCellMxR->set_backcolor();
        $oTmpCellMxR->set_border();                
        $oTmpCellMxR->set_y($iStartY);
        $oTmpCellMxR->set_x($this->iRightCellX);
        $oTmpCellMxR->set_width($this->iRightCellWidth);
        $oTmpCellMxR->set_height($this->iRowHeight);

//        if(in_array($sFieldName,$this->arDateFields) && $sFieldValue)
//            $sFieldValue = $this->dbmot_date($sFieldValue);

        $oTmpCellMxR->set_text($sValue);
        $this->draw_cell($oTmpCellMxR);                

        $iHeight = $this->set_cellheight_by_texts($oTmpCellMxL->get_text(),$oTmpCellMxR->get_text());
        //Celdas marco
        //Marco izquierdo
        $oTmpCellMxL->set_single();
        $oTmpCellMxL->set_border(1);
        $oTmpCellMxL->set_text();
        $oTmpCellMxL->set_height($iHeight);
        $this->draw_cell($oTmpCellMxL);
        //Marco derecho
        $oTmpCellMxR->set_single();
        $oTmpCellMxR->set_border(1);
        $oTmpCellMxR->set_text();
        $oTmpCellMxR->set_height($iHeight);
        $this->draw_cell($oTmpCellMxR);

        //Nuevo punto Y
        $this->SetY($iStartY+$iHeight);
    }
//</editor-fold>    
    
    //LOGO
    private function build_logo()
    {
        $iXmargin = 100;
        $iImageWidth = 90;
        $this->set_print_y(3);
        //MELDFORMULIER ONGREBRUIKELIJKE TRANSACTIES 
        //tr_sss_pdf_title0
        $this->draw_blackrow($this->get_trlabel("title0"),"C");
        $iTmpY = $this->GetY();
        //Imagen del logo de MOT
        $this->Image($this->arPaths["logo"],$iXmargin,$iTmpY,$iImageWidth);
        //La celda al rededor de la imágen
        $oCellSing = new HelperFpdfCell();
        $oCellSing->set_y($iTmpY);
        $oCellSing->set_x($this->iPageInnerX);
        $oCellSing->set_border(1);
        $oCellSing->set_width($this->iPageInnerWidth);
        $oCellSing->set_height(35);
        $this->draw_cell($oCellSing); 
    }//build_logo
    
//<editor-fold defaultstate="collapsed" desc="HEAD METHODS">      
    private function build_head1($arRows)
    {
        //$arFields = array("office_name","number","observations");
        $this->draw_blackrow($this->get_trlabel("title1"));
        //office_name
        $this->draw_leftcell($this->get_trlabel("office_name"));
        $this->draw_rightcell($arRows["office_name"]);
        
        //number
        $this->set_print_y();
        $this->draw_blackrow($this->get_trlabel("title2"));
        $this->draw_leftcell($this->get_trlabel("number"));
        $this->draw_rightcell($arRows["number"]);

        //observations
        $this->set_print_y(2);
        $this->draw_blackrow($this->get_trlabel("title3"));
        $iY = $this->GetY(); //se pinta de 94 a 142
        $this->SetY($iY+3);//Margen del texto dentro de la caja 
        $this->draw_multirow($arRows["observations"]);
        
        //Pintar una celda simple fija a modo marco
        $oCellSingle = new HelperFpdfCell();
        $oCellSingle->set_x($this->iPageInnerX);
        //$oCell->set_backcolor(180);
        $oCellSingle->set_border(1);
        $oCellSingle->set_y($iY);
        $oCellSingle->set_height(60);
        $oCellSingle->set_width($this->iPageInnerWidth);
        $this->draw_cell($oCellSingle);
        $this->SetY($iY+60);

    }//build_head1
    
    private function draw_code_description($arRows)
    {
        foreach($arRows as $arRow)
        {
            $iStartY = $this->GetY();//Y inicial
            
            //Celdas con textos
            $oTmpCellMxL = new HelperFpdfCell();
            $oTmpCellMxL->set_y($iStartY);
            $oTmpCellMxL->set_x($this->iPageInnerX);
            $oTmpCellMxL->set_width($this->iLeftCellWidth);
            $oTmpCellMxL->set_height($this->iRowHeight);
            $oTmpCellMxL->set_type_align("R");
            $oTmpCellMxL->set_text("    ".$arRow["code"]."  ");
            $this->draw_cell($oTmpCellMxL);
            
            $oTmpCellMxR = new HelperFpdfCell();
            $oTmpCellMxR->set_y($iStartY);
            $oTmpCellMxR->set_x($this->iRightCellX);
            $oTmpCellMxR->set_width($this->iRightCellWidth);
            $oTmpCellMxR->set_height($this->iRowHeight);
            $oTmpCellMxR->set_text($arRow["description"]);
            $this->draw_cell($oTmpCellMxR);
            
            $iHeight = $this->set_cellheight_by_texts($oTmpCellMxL->get_text(),$oTmpCellMxR->get_text());
            //Celdas marco
            $oTmpCellMxL->set_single();
            $oTmpCellMxL->set_border(1);
            $oTmpCellMxL->set_text();
            $oTmpCellMxL->set_height($iHeight);
            $this->draw_cell($oTmpCellMxL);
            
            $oTmpCellMxR->set_single();
            $oTmpCellMxR->set_border(1);
            $oTmpCellMxR->set_text();
            $oTmpCellMxR->set_height($iHeight);
            $this->draw_cell($oTmpCellMxR);

            $sImagePath = $this->arPaths["unchecked"];
            if($arRow["id_type"]!="") $sImagePath = $this->arPaths["checked"];
            $this->Image($sImagePath,$this->iPageInnerX+5,$iStartY+1,4,4); 
            $this->SetY($iStartY+$iHeight);
        }//foreach $arRows 
        
        //Reseteo la altura
        $this->reset_cellheights();
    }//draw_code_description
    
    /**
     * Objective indicatoren Subjective indicatoren
     */
    private function build_head_1_1($arRows)
    {
        //Objective indicatoren
        $arRows = $this->oBehaviourSuspicion->get_data_head1();
        $this->set_print_y();
        //Kruis de indicator die van toepassing is
        $this->draw_blackrow($this->get_trlabel("title4"));
        
        $this->draw_leftcell($this->get_trlabel("title5_l"),200,0,"R");
        $this->draw_rightcell($this->get_trlabel("title5_r"),200);
        $this->set_print_y();
        //$arRow campos: id,code_erp,description_nl,id_type,type
        $this->draw_code_description($arRows);
        
        //Subjective indicatoren
        $arRows = $this->oBehaviourSuspicion->get_data_head2();
        //ERROR DESCUADRE DE ALTURA DE CELDA
        $this->draw_leftcell($this->get_trlabel("title6_l"),200,0,"R");
        $this->draw_rightcell($this->get_trlabel("title6_r"),200);
        $this->set_print_y();
        $this->draw_code_description($arRows);
    }//build_head_1_1 objective indicatoren, subjective indicatoren
    
    private function build_head_checks($arRows,$iRows=3)
    {
        //Donde dibujare la celda que encerrará los checks
        $iStartY = $this->GetY();        
        $iCheckRow = 0;
        $iCheckCol = 0;
        
        $this->set_print_y();
        foreach($arRows as $arRow)
        {
            $iCellY = $this->GetY();
            $oCellMulti = new HelperFpdfCell();
            $oCellMulti->set_y($iCellY);
            
            //20 es el ancho de la celda check
            $iCellX = $this->iRightCellX+($iCheckCol*20);
            
            $oCellMulti->set_x($iCellX);
            $oCellMulti->set_text(" ".$arRow["description"]);
            $oCellMulti->set_type_align("L");
            $oCellMulti->set_border(0);
            $oCellMulti->set_width(15);
            $oCellMulti->set_height(6);
            $oCellMulti->set_backcolor(NULL);
            $this->draw_cell($oCellMulti);
            
            //$this->SetY($iCellY);
            $sImagePath = $this->arPaths["unchecked"];
            if($arRow["id_type"]!="") $sImagePath = $this->arPaths["checked"];
            $this->Image($sImagePath,$iCellX+9,$iCellY+1,4,4);
            
            //$this->set_print_y();
            //si la columna es multiplo de 4 y es distinta de 0 se resetea la posicion y se salta
            //a la siguiente linea
            if($iCheckRow%$iRows==0 && $iCheckRow!=0) 
            {   
                //$iCheckRows++;
                $iCheckRow = 0;
                $iCheckCol++;
                //Desde la "segunda" linea
                $this->SetY($iStartY+6);
            }
            else
                $iCheckRow++;
        }//Foreach $arRows
        
        //Dibujo la celda marco al rededor de los checks
        $oCellSingle = new HelperFpdfCell(TRUE);
        $oCellSingle->set_border(1);
        $oCellSingle->set_x($this->iRightCellX);
        $oCellSingle->set_y($iStartY);
        $oCellSingle->set_width($this->iRightCellWidth);
        //$oCellSingle->set_type_align("L");
        $oCellSingle->set_height((($iRows+1)*6)+12);//dejando linea por arriba y por abajo
        $this->draw_cell($oCellSingle);
        //Pongo la coordenada Y en el punto desde el cual se debe pintar el siguiente contenido
        $this->SetY($iStartY+($iRows*12));
        $this->reset_cellheights();        
    }//build_checks()
    
    /**
     * Financial and no financial data 
     */
    private function build_head_1_2()
    {
        //Si el punto actual es mayor o igual a 273. Se está en el límite por tanto se dibuja en una página nueva
        //if($this->GetY()>=273)
            $this->AddPage();
        //Title: Transactiegegevens
        $this->draw_blackrow($this->get_trlabel("title10"));
        
        //FINANCIELE
        $arRows = $this->oBehaviourSuspicion->get_data_head3();
        $this->fix_rows_coding($arRows);
        //bug($arRows,"financiele");
        
        $iStartY = $this->GetY();
        $oMxCell = new HelperFpdfCell();
        $oMxCell->set_font($this->sFont);
        $oMxCell->set_fontsize($this->iFontSize);
        $oMxCell->set_fontstyle();
        $oMxCell->set_backcolor(255);//Blanco
        $oMxCell->set_fontcolor(1);//Negro
        $oMxCell->set_x($this->iPageInnerX);
        $oMxCell->set_width($this->iLeftCellWidth);
        $oMxCell->set_height($this->iRowHeight);
        $oMxCell->set_text($this->get_trlabel("head3"));
        $this->draw_cell($oMxCell);
        //Marco
        $oMxCell->set_single();
        $oMxCell->set_y($iStartY);
        $oMxCell->set_text();
        $oMxCell->set_backcolor();
        $oMxCell->set_height(36);//6 altura * 6(filas)
        $oMxCell->set_border(1);
        $this->draw_cell($oMxCell);
        $this->build_head_checks($arRows);

        //NIET FINANCIELE
        $arRows = $this->oBehaviourSuspicion->get_data_head4();
        $this->fix_rows_coding($arRows);
        //bug($arRows);die;
        
        $iStartY = $this->GetY();
        $oMxCell = new HelperFpdfCell();
        $oMxCell->set_fontsize($this->iFontSize);
        $oMxCell->set_fontstyle();
        $oMxCell->set_backcolor(255);//Blanco
        $oMxCell->set_fontcolor(1);//Negro
        $oMxCell->set_x($this->iPageInnerX);
        $oMxCell->set_width($this->iLeftCellWidth);
        $oMxCell->set_height($this->iRowHeight);
        $oMxCell->set_text($this->get_trlabel("head4"));
        $this->draw_cell($oMxCell);
        //Marco
        $oMxCell->set_single();
        $oMxCell->set_y($iStartY);
        $oMxCell->set_text();
        $oMxCell->set_backcolor();
        $oMxCell->set_height(36);//6 altura * 6(filas)
        $oMxCell->set_border(1);
        $this->draw_cell($oMxCell);
        $this->build_head_checks($arRows,2);
    }//build_head_1_2 Financial and no financial data 
    
    private function build_head2($arRows)
    {
        //date_creation
        $this->set_print_y();
        $sCellText = $arRows["date_creation"];
        $sCellText = $this->dbmot_date($sCellText);
        $this->set_cellheight_by_texts($this->get_trlabel("date_creation"),$sCellText);
        $this->draw_leftcell($this->get_trlabel("date_creation"));
        $this->draw_rightcell($sCellText);
        $this->reset_cellheights();
             
        //hour_creation
        $this->set_print_y();
        $sCellText = $arRows["hour_creation"];
        $sCellText = $this->dbmot_time4($sCellText);
        $this->set_cellheight_by_texts($this->get_trlabel("hour_creation"),$sCellText);
        $this->draw_leftcell($this->get_trlabel("hour_creation"));
        $this->draw_rightcell($sCellText);
        $this->reset_cellheights();
        
        //status
        $this->set_print_y();
        $sIdStatus = $arRows["idstatus"];
        //bug($sIdStatus);die;
        $iStartY = $this->GetY();
        $this->draw_leftcell($this->get_trlabel("status"));
        $oCellSingle = new HelperFpdfCell(TRUE);
        $oCellSingle->set_y($iStartY);
        $oCellSingle->set_x($this->iRightCellX);
        $oCellSingle->set_width(70);
        $oCellSingle->set_height($this->iRowHeight);
        $oCellSingle->set_border(1);
        $oCellSingle->set_text("       voorgenomen");
        $this->draw_cell($oCellSingle);
        
        $sImagePath = $this->arPaths["unchecked"];
        if($sIdStatus=="voorgenomen") $sImagePath = $this->arPaths["checked"];
        $this->Image($sImagePath,$this->iRightCellX+3,$iStartY+1,4,4);
        
        $oCellSingle->set_x($this->iRightCellX+70);
        $oCellSingle->set_text("       uitgevoerd");
        $this->draw_cell($oCellSingle);
        $sImagePath = $this->arPaths["unchecked"];
        if($sIdStatus=="uitgevoerd") $sImagePath = $this->arPaths["checked"];
        $this->Image($sImagePath,$this->iRightCellX+3+70,$iStartY+1,4,4);        
//        $sCellText = $arRows["status"];
//        $this->set_cellheight_by_texts($this->get_trlabel("status"),$sCellText);
//        $this->draw_leftcell($this->get_trlabel("status"));
//        $this->draw_rightcell($sCellText);
//        $this->reset_cellheights();
        
        //type_char
        $this->set_print_y();
        $sCellText = $arRows["type_char"];
        $this->draw_leftcell($this->get_trlabel("type_char"));
        $this->draw_rightcell($sCellText);
        $this->reset_cellheights();
        
        //amount
        $this->set_print_y();
        $sCellText = $arRows["amount"];
        $this->set_cellheight_by_texts($this->get_trlabel("amount"),$sCellText);
        $this->draw_leftcell($this->get_trlabel("amount"));
        $this->draw_rightcell($sCellText);
        $this->reset_cellheights();
        
        //amount_cash
        $this->set_print_y();
        $sCellText = $arRows["amount_cash"];
        $this->set_cellheight_by_texts($this->get_trlabel("amount_cash"),$sCellText);
        $this->draw_leftcell($this->get_trlabel("amount_cash"));
        $this->draw_rightcell($sCellText);
        $this->reset_cellheights();
        
        //filial_name
        $this->set_print_y();
        $this->draw_greyrow($this->get_trlabel("subtitle1"));
        $this->draw_leftcell($this->get_trlabel("filial_name"));
        $sCellText = $arRows["filial_name"];
        $this->draw_rightcell($sCellText);
        $this->reset_cellheights();
    }//build_head2
    
    private function build_head()
    {
        $this->oBehaviourSuspicion->log_save_select();
        $arRows = $this->oBehaviourSuspicion->get_data_head();
        //bug($arRows,"head");die;
        $arRows["observations"] = utf8_decode($arRows["observations"]);
        //$this->fix_rows_coding($arRows);
        //bug($arRows);die;
        //office_name, number, observations
        $this->build_head1($arRows);

        //objective and subjective indicacions-->tabla head_details
        $this->build_head_1_1();
        
        //Financial and no financial data 
        $this->build_head_1_2();
        
        //date,hour,status,type,amount,amount2,filialname
        $this->build_head2($arRows);
    }//build_head
//</editor-fold>
    
//<editor-fold defaultstate="collapsed" desc="INVOLVED METHODS">

    private function build_involved_checks($arRows)
    {
        //TODO: El otro metodo de checks habría que optimizarlo
        $iCols = 3;
        $iCellCheckWidth = $this->iPageInnerWidth/$iCols;
        $iColPos = 1;
        $iStartY = $this->GetY();//Donde dibujare la celda que encerrará los checks
        $this->add_debug("Y en istarty: $iStartY");
        $oCellSingle = new HelperFpdfCell(TRUE);
        $oCellSingle->set_font($this->sFont);
        $oCellSingle->set_fontcolor(0);//Negro
        $oCellSingle->set_fontsize($this->iFontSize);
        $oCellSingle->set_height($this->iRowHeight);
        $oCellSingle->set_type_align("L");
        
        //$this->SetFont($this->sFont,"",$this->iFontSize);
        //$this->SetTextColor(0);//Negro  
        foreach($arRows as $arRow)
        {   
            $iY = $this->GetY();
            $this->add_debug("ceyll_y: $iY");
            $iX = $this->iPageInnerX+(($iColPos-1)*$iCellCheckWidth);
            
            $oCellSingle->set_y($iY);
            $oCellSingle->set_x($iX+6);
            //$oCellSingle->set_text($arRow["description"]." istart:$iStartY - cellY:$iX ");
            $oCellSingle->set_text($arRow["description"]);
            //la posicion de la celda virtual continente del texto
            $this->draw_cell($oCellSingle);
            
            $this->SetY($iY);
            $sImagePath = $this->arPaths["unchecked"];
            if($arRow["id_type"]!="") $sImagePath = $this->arPaths["checked"];
            $this->Image($sImagePath,$iX+1,$iY+1,4,4);
            
            //Si la columna es multiplo de las columnas configuradas se ha llegado a la última columna
            //así que hay que aplicar un salto en Y
            if($iColPos%$iCols==0) 
            {   
                //$iCheckRows++;
                $iColPos = 1;
                $this->set_print_y();
            }
            else
                $iColPos++;
        }
        //Dibujo la celda marco al rededor de los checks
        $iY = $this->GetY();
        $iHeight = $iStartY - $iY;
        
        $oCellSingle = new HelperFpdfCell(TRUE);
        $oCellSingle->set_y($iY);
        //$oCellSingle->set_y($iStartY+12);
        $oCellSingle->set_border(1);
        $oCellSingle->set_height($iHeight);
        $oCellSingle->set_backcolor();
        $oCellSingle->set_width($iCellCheckWidth);
        //Dibujo las celdas marco
        //$i Numero de marcos
        for($i=0;$i<3;$i++)
        {
            //80 es el ancho de cada marco
            $oCellSingle->set_x($this->iPageInnerX+($i*$iCellCheckWidth));
            $this->draw_cell($oCellSingle);
        }
        
//        $this->SetY($iStartY);
//        $this->SetX($this->iPageInnerX);
//        $this->Cell($this->iPageInnerWidth,4*6,"",1,0,"L",FALSE);
        //$this->SetY($iY);
        $this->reset_cellheights();          
    }//build_involved_checks
    
    /**
     * Numbers
     * @param array $arRow
     */
    private function build_involved_data_i($arRow,$iFrom=1,$iTo=1)
    {
        foreach($arRow as $sFieldName=>$sFieldValue)
        {
            $iPos = array_key_position($sFieldName,$arRow);
            
            if($iPos>=$iFrom && $iPos<=$iTo)
            {   
                $sLabel = $this->get_trlabel($sFieldName);
                if(in_array($sFieldName,$this->arDateFields) && $sFieldValue)
                    $sFieldValue = $this->dbmot_date($sFieldValue);
                $this->draw_fieldvalue_row($sLabel,$sFieldValue);
            }//fin if $iPos in rango
            elseif($iPos>$iTo) 
               break;
        }//foreach $arRow
        $this->reset_cellheights();
    }//buid_involved_data_i
    
    private function build_involved_i($arRow)
    {
        //Numbers
        $this->draw_blackrow($this->get_trlabel("title12"));
        $this->build_involved_data_i($arRow,2,8);
        
        //Subject
        $this->draw_blackrow($this->get_trlabel("title13"));
        $this->build_involved_data_i($arRow,9,20);
        
        //legitimatie
        $this->draw_blackrow($this->get_trlabel("title14"));
        $this->build_involved_data_i($arRow,21,27);
        
        //Adres
        $this->draw_blackrow($this->get_trlabel("title15"));
        $this->build_involved_data_i($arRow,28,33);
        
        //Telefoon
        //phone,phone_type,phone_type_country
        $this->draw_blackrow($this->get_trlabel("title16"));
        $sTrLabel = $this->get_trlabel("phone");
        $sTrLabel .= $this->get_trlabel("phone_type");
        $sTrLabel .= $this->get_trlabel("phone_type_country");
        $sFieldValue = $arRow["phone"]." / ".$arRow["phone_type"]." / ".$arRow["phone_type_country"];
        $this->draw_fieldvalue_row($sTrLabel,$sFieldValue);
        $this->reset_cellheights();        
    }
    
    private function build_involved()
    {
        //Recupero los datos de cabecera de los involucrados
        $arRows = $this->oBehaviourSuspicion->get_involved();
        //Primer involucrado
        $arRow = $arRows[0];
        $this->oBehaviourSuspicion->set_id_involved($arRow["id"]);
        $arRowsChecks = $this->oBehaviourSuspicion->get_involved_details();
        $this->AddPage();
        $this->draw_blackrow($this->get_trlabel("title11")."1");
        //$this->isDebug = 1;
        //$this->add_debug($this->GetY());
        $this->build_involved_checks($arRowsChecks);
        $this->build_involved_i($arRow);
        
        //Segundo involucrado
        $arRow = $arRows[1];
        $this->oBehaviourSuspicion->set_id_involved($arRow["id"]);
        $arRowsChecks = $this->oBehaviourSuspicion->get_involved_details();
        
        $this->set_print_y(2);
        $this->draw_blackrow($this->get_trlabel("title11")."2");
        $this->build_involved_checks($arRowsChecks);
        $this->build_involved_i($arRow);
    }
//</editor-fold>    
    
//<editor-fold defaultstate="collapsed" desc="get_multiline,get_endpos_lastword,is_clean_nl DEPRECATED"> 
    //MultiCell(float w, float h, string txt [, mixed border [, string align [, boolean fill]]])
    private function get_endpos_lastword($sText)
    {
        $iFrom = $this->iCharsLine;
        //substring empieza a contar desde 0 el primer caracter está en la posicion 0
        $sText = substr($sText,0,$this->iCharsLine);
        $iLen = strlen($sText);
        for($i=0; $i<$iLen; $i++)
        {
            $iFrom--;
            $cChar = substr($sText,-$iFrom,1);
            if(in_array($cChar,$this->arCharsForNL));
                return $iFrom;
        }
        return 0;
    }
    
    private function is_clean_nl($sText)
    {
        $cChar = substr($sText,$this->iCharsLine,1);
        //Si el último caracter permitido es un espacio entonces es válido
        if(in_array($cChar,$this->arCharsForNL))
        {        
            return TRUE;
        }
        else 
        {
            $cChar = substr($sText,$this->iCharsLine+1,1);
            return (in_array($cChar,$this->arCharsForNL));
        }
    }
    
    /**
     * ¡¡¡¡¡DEPRECATED !!!!!
     * No hace falta usarlo puesto que la celda multiline ya gestiona los textos grandes
     * @param type $sText
     * @return type
     */
    private function get_multiline($sText)
    {
        $this->reset_multiline();
        $sText = trim($sText);
        
        //$this->add_debug($sText,"texto a evaluar");
        //Marca auxiliar de fin
        $iOff=0;
        while($sText!="" || $iOff==100) 
        {
            $iOff++;
            //Extraigo la linea de x caracteres
            $sLine = substr($sText,0,$this->iCharsLine);
            $this->add_debug($sLine);
            //si la linea acaba en blanco o nueva linea se añade al array de multilinea
            if($this->is_clean_nl($sText))
            {
                $iLastPos = $this->iCharsLine+1;
            }
            else
            {
                $iLastPos = $this->get_endpos_lastword($sLine);
                $sLine = substr($sText,0,$iLastPos);
            }
            //$this->add_debug($sLine,"lineafinal_$iOff");

            $this->add_multiline($sLine);
            //Quito la linea añadida
            $sText = substr($sText,$iLastPos);
        }
        
        $sText = implode("\n",$this->arMultiLine);
        $this->reset_multiline();
        return $sText;
    }//deprecated
//</editor-fold>     

    private function dbmot_date($sDbDate,$sSeparator="/")
    {
        $sBoDate = "";
        //0123 45 67
        if(strlen($sDbDate)>7 && is_numeric($sDbDate))
        {    
            $sYear = substr($sDbDate,0,4);
            $sMonth = substr($sDbDate,4,2);
            $sDay = substr($sDbDate,6,2);

            $sMonth = two_positions($sMonth);
            $sDay = two_positions($sDay);

            $sBoDate = "$sMonth$sSeparator$sDay$sSeparator$sYear";
        }
        return $sBoDate;
    }//dbmot_date
    
    private function dbmot_time4($sDbDate,$sSeparator="/")
    {
        $sTime4 = "";
        //0123 45 67 80 11 22
        $iLen = strlen($sDbDate);
        if($iLen && is_numeric($sDbDate))
        {
            //datetime yyyymmddhhmmss
            if($iLen>8)
            {
                $sH = substr($sDbDate,8,2);
                $sM = substr($sDbDate,10,2);
                //$sS = substr($sDbDate,12,2);
            }
            //time sec hhmm ó hh:mm:ss
            elseif($iLen>3 )
            {
                $sH = substr($sDbDate,0,2);
                $sM = substr($sDbDate,2,2);
            }
            $sH = two_positions($sH);
            $sM = two_positions($sM);
            //$sS = two_positions($sS);        
            $sTime4 = "$sH$sSeparator$sM";  
        }
        return $sTime4;
    }//dbmot_time4
    
    private function print_debug()
    {
        $this->set_print_y(3);
        if($this->isDebug)
        {
            $arExclude = "oBehaviourSuspicion,pages,StdPageSizes,DefPageSize,CurPageSize,fontpath,CoreFonts,fonts";
            $arExclude = explode(",",$arExclude);
            $arInclude = "isDebug,sPathLogo,sPathOutputFile,sTrLabelPrefix,iCellHeight6,iCharsLine,arCharsForNL"
                       . ",arMultiLine,iPageWidth,iLeftCellWidth,iRightCellWidth,iRightCellX,iPageInnerWidth,iPageInnerX";
            $arInclude = explode(",",$arInclude);
            $arObject = get_object_vars($this);
            
            foreach($arObject as $sProperty=>$mxValue)
                if(!in_array($sProperty,$arExclude) && in_array($sProperty,$arInclude))
                    $sCellText[$sProperty] = $mxValue;
            
            if(count($this->arDebug))
                $sCellText = array_merge ($sCellText,$this->arDebug);
            
            $sCellText = var_export($sCellText,TRUE);
            $this->draw_multirow($sCellText);
        }
    }//print_debug
    
    private function header_nocache()
    {
        $sDateNow = gmdate("D, d M Y H:i:s")." GMT";
        header("Expires: $sDateNow");
        header("Last-Modified: $sDateNow");
        header("Pragma: no-cache");
        header("Cache-Control: no-cache, must-revalidate"); 
    }//header_nocache
        
    //============
    //MAIN PUBLIC
    //============
    public function generate($asFile=0)
    {
        //Página 1
        $this->AddPage();
        $this->AliasNbPages();
        $this->SetXY(0,0);
        
        $this->build_logo();
        $this->build_head();
        
        $this->build_involved();

        //$this->AddPage();
        $this->print_debug();
        
        if($asFile)
            $this->Output($this->sPathOutputFile,"F");
        else
            $this->Output();
    }//generate
    
    //===========
    //   SETS
    //===========
    private function set_print_y($iTimes=1){$this->SetY($this->GetY()+($iTimes*$this->iRowHeight));}
    private function reset_multiline(){$this->arMultiLine=array();}
    //private function set_multiline($sLine){$this->arMultiLine[]=$sLine;}
    private function add_multiline($sLine){$this->arMultiLine[]=$sLine;}
    private function add_debug($mxValue,$mxIndex=NULL){if($mxIndex)$this->arDebug[$mxIndex]=$mxValue;else$this->arDebug[]=$mxValue;}
    private function reset_cellheights(){$this->iLeftCellHeight = $this->iRowHeight; $this->iRightCellHeight = $this->iRowHeight;}
    
    /**
     * Segun el tamaño del texto se calcula la altura y se asigna la altura máxima 
     * a la de menor valor dejando la otra con la altura por defecto
     * @param string $sLeftText
     * @param string $sRightText
     * @return int
     */
    private function set_cellheight_by_texts($sLeftText,$sRightText)
    {
        $iLeftHeight = $this->get_cellheight_by_text($sLeftText); 
        $iRightHeight = $this->get_cellheight_by_text($sRightText,"R"); 
        if($iLeftHeight>$iRightHeight)
        {
            $this->iRightCellHeight = $iLeftHeight;
            $this->iLeftCellHeight = $this->iRowHeight;
        }
        elseif($iLeftHeight<$iRightHeight)
        {    
            $this->iLeftCellHeight = $iRightHeight;
            $this->iRightCellHeight = $this->iRowHeight;
            return $iRightHeight;
        }
        return $iLeftHeight;
        //$this->add_debug("l: $sLeftText,r: $sRightText, lh:$iLeftHeight,rh:$iRightHeight");
    }//set_cellheight_by_texts
    
    public function set_path_output_file($sPath){$this->sPathOutputFile=$sPath;}
    public function set_behaviour_suspicion(AppBehaviourSuspicion $oAppBehaviourSuspicion){$this->oBehaviourSuspicion = $oAppBehaviourSuspicion;}
    
    //===========
    //   GETS
    //===========
    private function get_cellheight_by_text($sText,$sCell="L")
    {
        $iTextWidth = $this->GetStringWidth($sText);
        $sCell = trim(strtoupper($sCell));
        $iWidth = $this->iLeftCellWidth;
        if($sCell=="R") $iWidth = $this->iRightCellWidth;
        $iNumLines = ceil($iTextWidth/($iWidth-1));
        $iCellHeight = ceil($iNumLines*$this->iRowHeight);
        //$this->Ln();
        //$this->add_debug(" $sCell height: ".$iCellHeight);
        return $iCellHeight;
    }
        
    private function get_trlabel($sName,$usePrefix=TRUE)
    {
        $sTrLabel = $sName;
        if($usePrefix)
            $sTrLabel = $this->sTrLabelPrefix.$sName;
        
        $sTrLabel = get_tr($sTrLabel);
        $sTrLabel = $this->get_fixed_coding($sTrLabel);
        return $sTrLabel;
    }
    
    /**
     * Pasa a ISO
     * @param string $sValue
     * @return string
     */
    private function get_fixed_coding($sValue)
    {
        $sCoding = mb_detect_encoding($sValue,mb_list_encodings(),TRUE);
        if($sCoding=="UTF-8")
            return utf8_decode($sValue); //ESTO PASA A ISO
            //return utf8_encode($sValue); //ESTO PASA A UTF8
        return $sValue;
    }
    
    private function fix_rows_coding(&$arRows)
    {
        $arTmp = array();
        if(is_array($arRows[0]))
        {
            foreach($arRows as $i=>$arRow)
                foreach($arRow as $sFieldName=>$sValue)
                    //se guarda en iso
                    $arTmp[$i][$sFieldName] = $this->get_fixed_coding($sValue);            
        }   
        else
        {
            foreach($arRows as $sFieldName=>$sValue)
                $arTmp[$sFieldName] = $this->get_fixed_coding($sValue);            
        }
        $arRows = $arTmp;
    }
        
}
