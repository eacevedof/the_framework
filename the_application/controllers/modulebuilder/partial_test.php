<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.7
 * @name PartialTest
 * @file partial_test.php 
 * @date 24-10-2014 13:56 (SPAIN)
 * @observations:
 * @requires: 
 */
import_apptranslate("modulebuilder");
import_controller("modulebuilder","modulebuilder");
import_helper("table_raw");
import_appmain("behaviour");

//bugif();
class PartialTest extends ControllerModuleBuilder
{

    public function __construct()
    {
        $this->sModuleName = "modulebuilder";
        //console,pgpage,clientdevice,sesuser,aftersucces,currop  | log,session,permission,view
        parent::__construct($this->sModuleName);
        $oAnchor = new HelperAnchor();
        $oAnchor->set_href($this->build_url("modulebuilder"));
        $oAnchor->set_innerhtml("back");
        $oAnchor->show();
    }//__construct()

    public function get_list_()
    {
        $oAppBehaviour = new TheApplicationBehaviour();
        if($this->get_get("recursive"))
        {
            $sSQL = "SELECT id, id_group_parent AS id_parent FROM base_user_group ORDER BY 2,1 ASC";
            $arGroups = $oAppBehaviour->query($sSQL);
            pr($arGroups);
            $arChilds = $this->get_childs($arGroups,1);
            pr($arChilds);
        }
        else
        {
            $idTransfer = $this->get_get("id_transfer");
            //$arIdTransfer = explode(",",$idTransfer);
            $oAppBehaviour = new TheApplicationBehaviour(1);
            $sSQL = "SELECT gridgiro,grsecueo,gridremi,grsecuen,gridbene
                    ,grfactur,grcdcart,grcdcont,grnrolla
                    ,grconsag,grrembol,LEFT(CONVERT(VARCHAR,grfechag,120),20) AS grfechag
                    ,LEFT(CONVERT(VARCHAR,grfeccon,120),20) AS grfeccon
                    ,LEFT(CONVERT(VARCHAR,grfecing,120),20) AS grfecing
                    ,LEFT(CONVERT(VARCHAR,grfecmod,120),20) AS grfecmod
                    ,LEFT(CONVERT(VARCHAR,grfecall,120),20) AS grfecall
                    ,LEFT(CONVERT(VARCHAR,grconpag,120),20) AS grconpag
                    ,grmontod,grmonusd,grtasacm
                    ,grvlrcom,grvlrajs,grmontop
                    ,grvlrdes,grtipgir,grcdcoro
                    ,grnorden,grcdagen,grcdpais,grcdciud
                    ,grtipenv,gritemid,gritdesc,grrelaci
                    ,grestciv,grniving,gridsexo,grcodnac
                    ,grtrabaj,grapsrem,grident,grcodcta
                    ,grdocpag,grnompag,grusauto,grtiptas
                    ,grnomcli,grapecli,grsapcli,grofiscu
                    ,grnmsrem,grfecenv,grhorenv,grtipreg
                    ,grcdcall,grpercon,grgirenv,grclave,grcorpri
                    ,grmarcad,grmoncta,grnorde2,grpagcor,grmonpag
                    ,grcdtran,grtipcon,grestado,grcausae,grcduser,grtippag,grcdbanc,grtipcta,grnrocta
                    ,CONVERT(TEXT,grobserv) AS grobserv,grmonori,grdrbene,grdrben2
                    ,grembene,grtlbene,grtlben2,CONVERT(TEXT,grmensaj) AS grmensaj,grciudds,grcdcorr
                    ,gragends,grtdbene,grcdbene,grnmbene,grciudor,gremremi
                    ,grcdocup,grtlremi,grtlrem2,grpaisds,grusmodi,grtdremi,grcdremi,grnmremi,grdrremi,grdrrem2
                    FROM tabgiros
                    WHERE 1=1
                    ";
            if($idTransfer) 
                $sSQL .= " AND gridgiro IN ($idTransfer)";
            
            //$arRows = $oAppBehaviour->query($sSQL);
            pr($arRows);
        }
    }
    
    public function get_list()
    {
        $arData[] = array("id"=>"1","id_parent"=>NULL);
        $arData[] = array("id"=>"2","id_parent"=>"1");
        $arData[] = array("id"=>"3","id_parent"=>"1");
        $arData[] = array("id"=>"4","id_parent"=>"2");
        $arData[] = array("id"=>"5","id_parent"=>"4");
        $arData[] = array("id"=>"6","id_parent"=>"5");
        $arData[] = array("id"=>"7","id_parent"=>"5");
        $arData[] = array("id"=>"8","id_parent"=>"5");
        $arData[] = array("id"=>"9","id_parent"=>"4");

        bug($arData,"arData");
        $oTable = new HelperTableRaw($arData);
        $oTable->show();
        
        $arChilds = array();
        $idTest = $this->get_get("idtest");
        if(!$idTest)
            $idTest="4";
        echo "::::::::::::::";
        echo "::TESTING:::: $idTest";
        echo "::::::::::::::";
        $this->get_vhierarchy($idTest,$arData,$arChilds);
        bug($arChilds);
        //$oTable = new HelperTableRaw($arChilds);
        //$oTable->show();
        
        //$this->log_custom("hello kitty");
    }
    
    public function test_match()
    {
        //(.*?)
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
        bug(strstr("customers/:page:^(\s*|\d+)$:page:",":page:"),":page:");die;
        
        bug($this->get_all_matches("customers","customers",1));
        bug($this->get_all_matches("^(\s*|\d+)$","5",1));
        bug($this->get_all_matches("^(\s*|\d+)$","",1));
        die;
        
        $this->arRequestedURI = array("albums","galeria_435","mazda_ztw_8752");
        $this->arConfigURIFoud = array("albums","([a-z,_]+[a-z]+):id_gallery:[\d]+:id_gallery:",":description:([a-z,_]+[a-z]+):description:_:id_model:[\d]+:id_model:");
        foreach($this->arRequestedURI as $i=>$sRequestedPiece)
        {
            $sConfigPiece = $this->arConfigURIFoud[$i];
            $arParams = $this->get_all_matches(":(.*?):",$sConfigPiece);
            if($arParams)
            {
                foreach($arParams as $sParam)
                {

                    $sPattern = "/$sParam(.*?)$sParam/";
                    $sPattern = preg_match($sPattern,$sConfigPiece,$arResult);
                    bug($arResult[1],"pattern found for $sParam in $sConfigPiece => $sPattern");
                    
                    $sPattern = $arResult[1];
                    pr($sPattern,"patron encontrado");
                    
                    $arResult = array();
                    //$sPattern = preg_match($sPattern,$sRequestedPiece,$arResult);
                    $arResult = $this->get_all_matches($sPattern, $sRequestedPiece);
                    bug($arResult,"value found for $sParam in $sRequestedPiece => $sPattern");
                    
                    $sParam = str_replace(":","",$sParam);
                    $_GET[$sParam] = $arResult[0];
                }
            }
        }        
        
        //bugg(); die;
        
        $sPatron = ":(.*?):";
        $sTest = ":id_customer:[0-9]+:id_customer:_:description:[a-z,A-Z,-]+";
        $arResult = $this->get_all_matches($sPatron,$sTest);

        if(!$arResult) bug("no matches found!");
        
        foreach($arResult as $i=>$sKey)
            $sTest=str_replace($sKey,"",$sTest);

        bug($sTest,"limpio test");
        
        $arResult = explode(":id:",":id:esto es un gran id:id:");
        bug($arResult,"xplode de id");
        
    }
    
    private function get_all_matches($sPattern,$sTeststring,$iTitle=0)
    {
        $sPattern="/$sPattern/";
        if($iTitle)
        {    
            bug("PATRON: $sPattern");
            bug("TEST STRING: $sTeststring");
        }
        $arResult = array();
        //$arPregMatch = preg_match($sPatron,$sTeststring,$arResult);
        $arPregMatch = preg_match_all($sPattern,$sTeststring,$arResult);
        if($iTitle)
            bug($arPregMatch,"preg_match_all");
        $arResult = array_unique($arResult[0]);
        
        if($iTitle)
            bug($arResult,"result");
        return $arResult;
    }
 
}
