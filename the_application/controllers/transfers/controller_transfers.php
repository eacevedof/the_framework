<?php
/**
 * @author Module Builder 1.0.21
 * @link www.eduardoaf.com
 * @version 1.0.3
 * @name ControllerTransfers
 * @file controller_transfers.php   
 * @date 28-10-2014 10:42 (SPAIN)
 * @observations: 
 *      Se aplica permisos sin bd
 * @requires:
 */
//TFW
import_component("page,validate,filter");
import_helper("form,form_fieldset,form_legend,input_text,label,anchor,table,table_typed");
import_helper("input_password,button_basic,raw,div,javascript,form_fieldset");
//APP
//import_model("usernodb");
import_model("user");
import_model("uctabgiros,ucagencias,ucciudades,uctbpaises,uccorrespo,uctbbancos","uc");//OJO usa usernodb
import_appmain("controller,view,behaviour");
import_appbehaviour("picklist");
import_apphelper("listactionbar,controlgroup,formactions,buttontabs,formhead,alertdiv,breadscrumbs,headertabs");

class ControllerTransfers extends TheApplicationController
{
    protected $oTabgiros;

    public function __construct()
    {
        //errorson();
        $this->sModuleName = "transfers";
        //console,pgpage,clientdevice,sesuser,aftersucces,currop  | log,session,permission,view
        parent::__construct($this->sModuleName);
        
        $this->oTabgiros = new ModelTabgiros();
        $this->oTabgiros->set_platform($this->oSessionUser->get_platform());
        if($this->is_inget("gridgiro"))
        {
            $this->oTabgiros->set_id($this->get_get("gridgiro"));
            $this->oTabgiros->load_by_id();
        }
        //$this->oSessionUser->set_dataowner_table($this->oTabgiros->get_table_name());
        //$this->oSessionUser->set_dataowner_tablefield("id_customer");
        //$this->oSessionUser->set_dataowner_keys(array("gridgiro"=>$this->oTabgiros->get_id()));
    }

    //<editor-fold defaultstate="collapsed" desc="LIST">
    //list_1
    protected function build_list_scrumbs()
    {
        $arLinks = array();
        $sUrlLink = $this->build_url($this->sModuleName);
        $arLinks["list"]=array("href"=>$sUrlLink,"innerhtml"=>tr_ts_entities);
        $oScrumbs = new AppHelperBreadscrumbs($arLinks);
        return $oScrumbs;
    }

    //list_2
    protected function build_list_tabs()
    {
        $arTabs = array();
        //$sUrlTab = $this->build_url($this->sModuleName,NULL,"get_list","id=".$this->get_get("id_parent_foreign"))
        //$arTabs["list"]=array("href"=>$sUrlTab,"innerhtml"=>tr_ts_listtabs_1);
        //$sUrlTab = $this->build_url($this->sModuleName,NULL,"get_list_by_foreign","id_foreign=".$this->get_get("id_parent_foreign"))
        //$arTabs["listbyforeign"]=array("href"=>$sUrlTab,"innerhtml"=>tr_ts_listtabs_2);
        $oTabs = new AppHelperHeadertabs($arTabs,"list");
        return $oTabs;
    }

    //list_3
    protected function build_listoperation_buttons()
    {
        $arOpButtons = array();
        $arOpButtons["filters"]=array("href"=>"javascript:reset_filters();","icon"=>"awe-magic","innerhtml"=>tr_ts_listopbutton_filters);
        $arOpButtons["reload"]=array("href"=>"javascript:TfwControl.form_submit();","icon"=>"awe-refresh","innerhtml"=>tr_ts_listopbutton_reload);
        if($this->oPermission->is_insert())
            $arOpButtons["insert"]=array("href"=>$this->build_url($this->sModuleName,NULL,"insert"),"icon"=>"awe-plus","innerhtml"=>tr_ts_listopbutton_insert);
        if($this->oPermission->is_quarantine())
            $arOpButtons["multiquarantine"]=array("href"=>"javascript:multi_quarantine();","icon"=>"awe-remove","innerhtml"=>tr_ts_listopbutton_multiquarantine);
        //if($this->oPermission->is_delete())
            //$arOpButtons["multidelete"]=array("href"=>"javascript:multi_delete();","icon"=>"awe-remove","innerhtml"=>tr_ts_listopbutton_multidelete);
        //PICK WINDOWS
        //$arOpButtons["multiassign"]=array("href"=>"javascript:multiassign_window('tabgiros',null,'multiassign','tabgiros','addexternaldata');","icon"=>"awe-external-link","innerhtml"=>tr_ts_listopbutton_multiassign);
        //$arOpButtons["singleassign"]=array("href"=>"javascript:single_pick('tabgiros','singleassign','txtI','txtI');","icon"=>"awe-external-link","innerhtml"=>tr_ts_listopbutton_singleassign);
        $oOpButtons = new AppHelperButtontabs(tr_ts_entities." - ".APP_OFFICE_CURRENT_NAME);
        $oOpButtons->set_tabs($arOpButtons);
        return $oOpButtons;
    }//build_listoperation_buttons()

    //list_4
    protected function load_config_list_filters()
    {
        //gridgiro
        $this->set_filter("gridgiro","txtGridgiro",array("operator"=>"like"));
        //grsecuen
        //$this->set_filter("grsecuen","txtGrsecuen",array("operator"=>"like"));
//<editor-fold defaultstate="collapsed" desc="NIU">        
//        //grsecueo
//        $this->set_filter("grsecueo","txtGrsecueo",array("operator"=>"like"));
//        //gridremi
//        $this->set_filter("gridremi","txtGridremi",array("operator"=>"like"));
//        //gridbene
//        $this->set_filter("gridbene","txtGridbene",array("operator"=>"like"));
//        //grfactur
//        $this->set_filter("grfactur","txtGrfactur",array("operator"=>"like"));
//        //grcdcart
//        $this->set_filter("grcdcart","txtGrcdcart",array("operator"=>"like"));
//        //grcdcont
//        $this->set_filter("grcdcont","txtGrcdcont",array("operator"=>"like"));
//        //grnrolla
//        $this->set_filter("grnrolla","txtGrnrolla",array("operator"=>"like"));
//        //grconsag
//        $this->set_filter("grconsag","txtGrconsag",array("operator"=>"like"));
//        //grrembol
//        $this->set_filter("grrembol","txtGrrembol",array("operator"=>"like"));
//        //grfechag
//        $this->set_filter("grfechag","txtGrfechag",array("operator"=>"like"));
//        //grfeccon
//        $this->set_filter("grfeccon","txtGrfeccon",array("operator"=>"like"));
//        //grfecing
//        $this->set_filter("grfecing","txtGrfecing",array("operator"=>"like"));
//        //grfecmod
//        $this->set_filter("grfecmod","txtGrfecmod",array("operator"=>"like"));
//        //grfecall
//        $this->set_filter("grfecall","txtGrfecall",array("operator"=>"like"));
//        //grconpag
//        $this->set_filter("grconpag","txtGrconpag",array("operator"=>"like"));
//        //grmontod
//        $this->set_filter("grmontod","txtGrmontod",array("operator"=>"like"));
//        //grmonusd
//        $this->set_filter("grmonusd","txtGrmonusd",array("operator"=>"like"));
//        //grtasacm
//        $this->set_filter("grtasacm","txtGrtasacm",array("operator"=>"like"));
//        //grvlrcom
//        $this->set_filter("grvlrcom","txtGrvlrcom",array("operator"=>"like"));
//        //grvlrajs
//        $this->set_filter("grvlrajs","txtGrvlrajs",array("operator"=>"like"));
//        //grmontop
//        $this->set_filter("grmontop","txtGrmontop",array("operator"=>"like"));
//        //grvlrdes
//        $this->set_filter("grvlrdes","txtGrvlrdes",array("operator"=>"like"));
//        //grtipgir
//        $this->set_filter("grtipgir","txtGrtipgir",array("operator"=>"like"));
//        //grcdcoro
//        $this->set_filter("grcdcoro","txtGrcdcoro",array("operator"=>"like"));
//        //grnorden
//        $this->set_filter("grnorden","txtGrnorden",array("operator"=>"like"));
//        //grcdagen
//        $this->set_filter("grcdagen","txtGrcdagen",array("operator"=>"like"));
//        //grcdpais
//        $this->set_filter("grcdpais","txtGrcdpais",array("operator"=>"like"));
//        //grcdciud
//        $this->set_filter("grcdciud","txtGrcdciud",array("operator"=>"like"));
//        //grtipenv
//        $this->set_filter("grtipenv","txtGrtipenv",array("operator"=>"like"));
//        //gritemid
//        $this->set_filter("gritemid","txtGritemid",array("operator"=>"like"));
//        //gritdesc
//        $this->set_filter("gritdesc","txtGritdesc",array("operator"=>"like"));
//        //grrelaci
//        $this->set_filter("grrelaci","txtGrrelaci",array("operator"=>"like"));
//        //grestciv
//        $this->set_filter("grestciv","txtGrestciv",array("operator"=>"like"));
//        //grniving
//        $this->set_filter("grniving","txtGrniving",array("operator"=>"like"));
//        //gridsexo
//        $this->set_filter("gridsexo","txtGridsexo",array("operator"=>"like"));
//        //grcodnac
//        $this->set_filter("grcodnac","txtGrcodnac",array("operator"=>"like"));
//        //grtrabaj
//        $this->set_filter("grtrabaj","txtGrtrabaj",array("operator"=>"like"));
//        //grapsrem
//        $this->set_filter("grapsrem","txtGrapsrem",array("operator"=>"like"));
//        //grident
//        $this->set_filter("grident","txtGrident",array("operator"=>"like"));
//        //grcodcta
//        $this->set_filter("grcodcta","txtGrcodcta",array("operator"=>"like"));
//        //grdocpag
//        $this->set_filter("grdocpag","txtGrdocpag",array("operator"=>"like"));
//        //grnompag
//        $this->set_filter("grnompag","txtGrnompag",array("operator"=>"like"));
//        //grusauto
//        $this->set_filter("grusauto","txtGrusauto",array("operator"=>"like"));
//        //grtiptas
//        $this->set_filter("grtiptas","txtGrtiptas",array("operator"=>"like"));
//        //grnomcli
//        $this->set_filter("grnomcli","txtGrnomcli",array("operator"=>"like"));
//        //grapecli
//        $this->set_filter("grapecli","txtGrapecli",array("operator"=>"like"));
//        //grsapcli
//        $this->set_filter("grsapcli","txtGrsapcli",array("operator"=>"like"));
//        //grofiscu
//        $this->set_filter("grofiscu","txtGrofiscu",array("operator"=>"like"));
//        //grnmsrem
//        $this->set_filter("grnmsrem","txtGrnmsrem",array("operator"=>"like"));
//        //grfecenv
//        $this->set_filter("grfecenv","txtGrfecenv",array("operator"=>"like"));
//        //grhorenv
//        $this->set_filter("grhorenv","txtGrhorenv",array("operator"=>"like"));
//        //grtipreg
//        $this->set_filter("grtipreg","txtGrtipreg",array("operator"=>"like"));
//        //grcdcall
//        $this->set_filter("grcdcall","txtGrcdcall",array("operator"=>"like"));
//        //grpercon
//        $this->set_filter("grpercon","txtGrpercon",array("operator"=>"like"));
//        //grgirenv
//        $this->set_filter("grgirenv","txtGrgirenv",array("operator"=>"like"));
//        //grclave
//        $this->set_filter("grclave","txtGrclave",array("operator"=>"like"));
//        //grcorpri
//        $this->set_filter("grcorpri","txtGrcorpri",array("operator"=>"like"));
//        //grmarcad
//        $this->set_filter("grmarcad","txtGrmarcad",array("operator"=>"like"));
//        //grmoncta
//        $this->set_filter("grmoncta","txtGrmoncta",array("operator"=>"like"));
//        //grnorde2
//        $this->set_filter("grnorde2","txtGrnorde2",array("operator"=>"like"));
//        //grpagcor
//        $this->set_filter("grpagcor","txtGrpagcor",array("operator"=>"like"));
//        //grmonpag
//        $this->set_filter("grmonpag","txtGrmonpag",array("operator"=>"like"));
//        //grcdtran
//        $this->set_filter("grcdtran","txtGrcdtran",array("operator"=>"like"));
//        //grtipcon
//        $this->set_filter("grtipcon","txtGrtipcon",array("operator"=>"like"));
//        //grestado
//        $this->set_filter("grestado","txtGrestado",array("operator"=>"like"));
//        //grcausae
//        $this->set_filter("grcausae","txtGrcausae",array("operator"=>"like"));
//        //grcduser
//        $this->set_filter("grcduser","txtGrcduser",array("operator"=>"like"));
//        //grtippag
//        $this->set_filter("grtippag","txtGrtippag",array("operator"=>"like"));
//        //grcdbanc
//        $this->set_filter("grcdbanc","txtGrcdbanc",array("operator"=>"like"));
//        //grtipcta
//        $this->set_filter("grtipcta","txtGrtipcta",array("operator"=>"like"));
//        //grnrocta
//        $this->set_filter("grnrocta","txtGrnrocta",array("operator"=>"like"));
//        //grobserv
//        $this->set_filter("grobserv","txtGrobserv",array("operator"=>"like"));
//        //grmonori
//        $this->set_filter("grmonori","txtGrmonori",array("operator"=>"like"));
//        //grdrbene
//        $this->set_filter("grdrbene","txtGrdrbene",array("operator"=>"like"));
//        //grdrben2
//        $this->set_filter("grdrben2","txtGrdrben2",array("operator"=>"like"));
//        //grembene
//        $this->set_filter("grembene","txtGrembene",array("operator"=>"like"));
//        //grtlbene
//        $this->set_filter("grtlbene","txtGrtlbene",array("operator"=>"like"));
//        //grtlben2
//        $this->set_filter("grtlben2","txtGrtlben2",array("operator"=>"like"));
//        //grmensaj
//        $this->set_filter("grmensaj","txtGrmensaj",array("operator"=>"like"));
//        //grciudds
//        $this->set_filter("grciudds","txtGrciudds",array("operator"=>"like"));
//        //grcdcorr
//        $this->set_filter("grcdcorr","txtGrcdcorr",array("operator"=>"like"));
//        //gragends
//        $this->set_filter("gragends","txtGragends",array("operator"=>"like"));
//        //grtdbene
//        $this->set_filter("grtdbene","txtGrtdbene",array("operator"=>"like"));
//        //grcdbene
//        $this->set_filter("grcdbene","txtGrcdbene",array("operator"=>"like"));
//        //grnmbene
//        $this->set_filter("grnmbene","txtGrnmbene",array("operator"=>"like"));
//        //grciudor
//        $this->set_filter("grciudor","txtGrciudor",array("operator"=>"like"));
//        //gremremi
//        $this->set_filter("gremremi","txtGremremi",array("operator"=>"like"));
//        //grcdocup
//        $this->set_filter("grcdocup","txtGrcdocup",array("operator"=>"like"));
//        //grtlremi
//        $this->set_filter("grtlremi","txtGrtlremi",array("operator"=>"like"));
//        //grtlrem2
//        $this->set_filter("grtlrem2","txtGrtlrem2",array("operator"=>"like"));
//        //grpaisds
//        $this->set_filter("grpaisds","txtGrpaisds",array("operator"=>"like"));
//        //grusmodi
//        $this->set_filter("grusmodi","txtGrusmodi",array("operator"=>"like"));
//        //grtdremi
//        $this->set_filter("grtdremi","txtGrtdremi",array("operator"=>"like"));
//        //grcdremi
//        $this->set_filter("grcdremi","txtGrcdremi",array("operator"=>"like"));
//        //grnmremi
//        $this->set_filter("grnmremi","txtGrnmremi",array("operator"=>"like"));
//        //grdrremi
//        $this->set_filter("grdrremi","txtGrdrremi",array("operator"=>"like"));
//        //grdrrem2
//        $this->set_filter("grdrrem2","txtGrdrrem2",array("operator"=>"like"));
//</editor-fold>            
    }//load_config_list_filters()

    //list_5
    protected function set_listfilters_from_post()
    {
        //gridgiro
        $this->set_filter_value("gridgiro",$this->get_post("txtGridgiro"));
//<editor-fold defaultstate="collapsed" desc="NIU">        
//        //grsecueo
//        $this->set_filter_value("grsecueo",$this->get_post("txtGrsecueo"));
//        //gridremi
//        $this->set_filter_value("gridremi",$this->get_post("txtGridremi"));
//        //grsecuen
//        $this->set_filter_value("grsecuen",$this->get_post("txtGrsecuen"));
//        //gridbene
//        $this->set_filter_value("gridbene",$this->get_post("txtGridbene"));
//        //grfactur
//        $this->set_filter_value("grfactur",$this->get_post("txtGrfactur"));
//        //grcdcart
//        $this->set_filter_value("grcdcart",$this->get_post("txtGrcdcart"));
//        //grcdcont
//        $this->set_filter_value("grcdcont",$this->get_post("txtGrcdcont"));
//        //grnrolla
//        $this->set_filter_value("grnrolla",$this->get_post("txtGrnrolla"));
//        //grconsag
//        $this->set_filter_value("grconsag",$this->get_post("txtGrconsag"));
//        //grrembol
//        $this->set_filter_value("grrembol",$this->get_post("txtGrrembol"));
//        //grfechag
//        $this->set_filter_value("grfechag",$this->get_post("txtGrfechag"));
//        //grfeccon
//        $this->set_filter_value("grfeccon",$this->get_post("txtGrfeccon"));
//        //grfecing
//        $this->set_filter_value("grfecing",$this->get_post("txtGrfecing"));
//        //grfecmod
//        $this->set_filter_value("grfecmod",$this->get_post("txtGrfecmod"));
//        //grfecall
//        $this->set_filter_value("grfecall",$this->get_post("txtGrfecall"));
//        //grconpag
//        $this->set_filter_value("grconpag",$this->get_post("txtGrconpag"));
//        //grmontod
//        $this->set_filter_value("grmontod",$this->get_post("txtGrmontod"));
//        //grmonusd
//        $this->set_filter_value("grmonusd",$this->get_post("txtGrmonusd"));
//        //grtasacm
//        $this->set_filter_value("grtasacm",$this->get_post("txtGrtasacm"));
//        //grvlrcom
//        $this->set_filter_value("grvlrcom",$this->get_post("txtGrvlrcom"));
//        //grvlrajs
//        $this->set_filter_value("grvlrajs",$this->get_post("txtGrvlrajs"));
//        //grmontop
//        $this->set_filter_value("grmontop",$this->get_post("txtGrmontop"));
//        //grvlrdes
//        $this->set_filter_value("grvlrdes",$this->get_post("txtGrvlrdes"));
//        //grtipgir
//        $this->set_filter_value("grtipgir",$this->get_post("txtGrtipgir"));
//        //grcdcoro
//        $this->set_filter_value("grcdcoro",$this->get_post("txtGrcdcoro"));
//        //grnorden
//        $this->set_filter_value("grnorden",$this->get_post("txtGrnorden"));
//        //grcdagen
//        $this->set_filter_value("grcdagen",$this->get_post("txtGrcdagen"));
//        //grcdpais
//        $this->set_filter_value("grcdpais",$this->get_post("txtGrcdpais"));
//        //grcdciud
//        $this->set_filter_value("grcdciud",$this->get_post("txtGrcdciud"));
//        //grtipenv
//        $this->set_filter_value("grtipenv",$this->get_post("txtGrtipenv"));
//        //gritemid
//        $this->set_filter_value("gritemid",$this->get_post("txtGritemid"));
//        //gritdesc
//        $this->set_filter_value("gritdesc",$this->get_post("txtGritdesc"));
//        //grrelaci
//        $this->set_filter_value("grrelaci",$this->get_post("txtGrrelaci"));
//        //grestciv
//        $this->set_filter_value("grestciv",$this->get_post("txtGrestciv"));
//        //grniving
//        $this->set_filter_value("grniving",$this->get_post("txtGrniving"));
//        //gridsexo
//        $this->set_filter_value("gridsexo",$this->get_post("txtGridsexo"));
//        //grcodnac
//        $this->set_filter_value("grcodnac",$this->get_post("txtGrcodnac"));
//        //grtrabaj
//        $this->set_filter_value("grtrabaj",$this->get_post("txtGrtrabaj"));
//        //grapsrem
//        $this->set_filter_value("grapsrem",$this->get_post("txtGrapsrem"));
//        //grident
//        $this->set_filter_value("grident",$this->get_post("txtGrident"));
//        //grcodcta
//        $this->set_filter_value("grcodcta",$this->get_post("txtGrcodcta"));
//        //grdocpag
//        $this->set_filter_value("grdocpag",$this->get_post("txtGrdocpag"));
//        //grnompag
//        $this->set_filter_value("grnompag",$this->get_post("txtGrnompag"));
//        //grusauto
//        $this->set_filter_value("grusauto",$this->get_post("txtGrusauto"));
//        //grtiptas
//        $this->set_filter_value("grtiptas",$this->get_post("txtGrtiptas"));
//        //grnomcli
//        $this->set_filter_value("grnomcli",$this->get_post("txtGrnomcli"));
//        //grapecli
//        $this->set_filter_value("grapecli",$this->get_post("txtGrapecli"));
//        //grsapcli
//        $this->set_filter_value("grsapcli",$this->get_post("txtGrsapcli"));
//        //grofiscu
//        $this->set_filter_value("grofiscu",$this->get_post("txtGrofiscu"));
//        //grnmsrem
//        $this->set_filter_value("grnmsrem",$this->get_post("txtGrnmsrem"));
//        //grfecenv
//        $this->set_filter_value("grfecenv",$this->get_post("txtGrfecenv"));
//        //grhorenv
//        $this->set_filter_value("grhorenv",$this->get_post("txtGrhorenv"));
//        //grtipreg
//        $this->set_filter_value("grtipreg",$this->get_post("txtGrtipreg"));
//        //grcdcall
//        $this->set_filter_value("grcdcall",$this->get_post("txtGrcdcall"));
//        //grpercon
//        $this->set_filter_value("grpercon",$this->get_post("txtGrpercon"));
//        //grgirenv
//        $this->set_filter_value("grgirenv",$this->get_post("txtGrgirenv"));
//        //grclave
//        $this->set_filter_value("grclave",$this->get_post("txtGrclave"));
//        //grcorpri
//        $this->set_filter_value("grcorpri",$this->get_post("txtGrcorpri"));
//        //grmarcad
//        $this->set_filter_value("grmarcad",$this->get_post("txtGrmarcad"));
//        //grmoncta
//        $this->set_filter_value("grmoncta",$this->get_post("txtGrmoncta"));
//        //grnorde2
//        $this->set_filter_value("grnorde2",$this->get_post("txtGrnorde2"));
//        //grpagcor
//        $this->set_filter_value("grpagcor",$this->get_post("txtGrpagcor"));
//        //grmonpag
//        $this->set_filter_value("grmonpag",$this->get_post("txtGrmonpag"));
//        //grcdtran
//        $this->set_filter_value("grcdtran",$this->get_post("txtGrcdtran"));
//        //grtipcon
//        $this->set_filter_value("grtipcon",$this->get_post("txtGrtipcon"));
//        //grestado
//        $this->set_filter_value("grestado",$this->get_post("txtGrestado"));
//        //grcausae
//        $this->set_filter_value("grcausae",$this->get_post("txtGrcausae"));
//        //grcduser
//        $this->set_filter_value("grcduser",$this->get_post("txtGrcduser"));
//        //grtippag
//        $this->set_filter_value("grtippag",$this->get_post("txtGrtippag"));
//        //grcdbanc
//        $this->set_filter_value("grcdbanc",$this->get_post("txtGrcdbanc"));
//        //grtipcta
//        $this->set_filter_value("grtipcta",$this->get_post("txtGrtipcta"));
//        //grnrocta
//        $this->set_filter_value("grnrocta",$this->get_post("txtGrnrocta"));
//        //grobserv
//        $this->set_filter_value("grobserv",$this->get_post("txtGrobserv"));
//        //grmonori
//        $this->set_filter_value("grmonori",$this->get_post("txtGrmonori"));
//        //grdrbene
//        $this->set_filter_value("grdrbene",$this->get_post("txtGrdrbene"));
//        //grdrben2
//        $this->set_filter_value("grdrben2",$this->get_post("txtGrdrben2"));
//        //grembene
//        $this->set_filter_value("grembene",$this->get_post("txtGrembene"));
//        //grtlbene
//        $this->set_filter_value("grtlbene",$this->get_post("txtGrtlbene"));
//        //grtlben2
//        $this->set_filter_value("grtlben2",$this->get_post("txtGrtlben2"));
//        //grmensaj
//        $this->set_filter_value("grmensaj",$this->get_post("txtGrmensaj"));
//        //grciudds
//        $this->set_filter_value("grciudds",$this->get_post("txtGrciudds"));
//        //grcdcorr
//        $this->set_filter_value("grcdcorr",$this->get_post("txtGrcdcorr"));
//        //gragends
//        $this->set_filter_value("gragends",$this->get_post("txtGragends"));
//        //grtdbene
//        $this->set_filter_value("grtdbene",$this->get_post("txtGrtdbene"));
//        //grcdbene
//        $this->set_filter_value("grcdbene",$this->get_post("txtGrcdbene"));
//        //grnmbene
//        $this->set_filter_value("grnmbene",$this->get_post("txtGrnmbene"));
//        //grciudor
//        $this->set_filter_value("grciudor",$this->get_post("txtGrciudor"));
//        //gremremi
//        $this->set_filter_value("gremremi",$this->get_post("txtGremremi"));
//        //grcdocup
//        $this->set_filter_value("grcdocup",$this->get_post("txtGrcdocup"));
//        //grtlremi
//        $this->set_filter_value("grtlremi",$this->get_post("txtGrtlremi"));
//        //grtlrem2
//        $this->set_filter_value("grtlrem2",$this->get_post("txtGrtlrem2"));
//        //grpaisds
//        $this->set_filter_value("grpaisds",$this->get_post("txtGrpaisds"));
//        //grusmodi
//        $this->set_filter_value("grusmodi",$this->get_post("txtGrusmodi"));
//        //grtdremi
//        $this->set_filter_value("grtdremi",$this->get_post("txtGrtdremi"));
//        //grcdremi
//        $this->set_filter_value("grcdremi",$this->get_post("txtGrcdremi"));
//        //grnmremi
//        $this->set_filter_value("grnmremi",$this->get_post("txtGrnmremi"));
//        //grdrremi
//        $this->set_filter_value("grdrremi",$this->get_post("txtGrdrremi"));
//        //grdrrem2
//        $this->set_filter_value("grdrrem2",$this->get_post("txtGrdrrem2"));
//</editor-fold>        
    }//set_listfilters_from_post()

    //list_6
    protected function get_list_filters()
    {
        //CAMPOS
        $arFields = array();
        //grsecuen
        $oAuxField = new HelperInputText("txtGridgiro","txtGridgiro");
        $oAuxField->set_value($this->get_post("txtGridgiro"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGridgiro",tr_ts_fil_gridgiro));
        $arFields[] = $oAuxWrapper;
//<editor-fold defaultstate="collapsed" desc="NIU">  
//        //gridgiro
//        $oAuxField = new HelperInputText("txtGridgiro","txtGridgiro");
//        $oAuxField->set_value($this->get_post("txtGridgiro"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGridgiro",tr_ts_fil_gridgiro));
//        $arFields[] = $oAuxWrapper;
//        //grsecueo
//        $oAuxField = new HelperInputText("txtGrsecueo","txtGrsecueo");
//        $oAuxField->set_value($this->get_post("txtGrsecueo"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrsecueo",tr_ts_fil_grsecueo));
//        $arFields[] = $oAuxWrapper;
//        //gridremi
//        $oAuxField = new HelperInputText("txtGridremi","txtGridremi");
//        $oAuxField->set_value($this->get_post("txtGridremi"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGridremi",tr_ts_fil_gridremi));
//        $arFields[] = $oAuxWrapper; 
//        //gridbene
//        $oAuxField = new HelperInputText("txtGridbene","txtGridbene");
//        $oAuxField->set_value($this->get_post("txtGridbene"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGridbene",tr_ts_fil_gridbene));
//        $arFields[] = $oAuxWrapper;
//        //grfactur
//        $oAuxField = new HelperInputText("txtGrfactur","txtGrfactur");
//        $oAuxField->set_value($this->get_post("txtGrfactur"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrfactur",tr_ts_fil_grfactur));
//        $arFields[] = $oAuxWrapper;
//        //grcdcart
//        $oAuxField = new HelperInputText("txtGrcdcart","txtGrcdcart");
//        $oAuxField->set_value($this->get_post("txtGrcdcart"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrcdcart",tr_ts_fil_grcdcart));
//        $arFields[] = $oAuxWrapper;
//        //grcdcont
//        $oAuxField = new HelperInputText("txtGrcdcont","txtGrcdcont");
//        $oAuxField->set_value($this->get_post("txtGrcdcont"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrcdcont",tr_ts_fil_grcdcont));
//        $arFields[] = $oAuxWrapper;
//        //grnrolla
//        $oAuxField = new HelperInputText("txtGrnrolla","txtGrnrolla");
//        $oAuxField->set_value($this->get_post("txtGrnrolla"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrnrolla",tr_ts_fil_grnrolla));
//        $arFields[] = $oAuxWrapper;
//        //grconsag
//        $oAuxField = new HelperInputText("txtGrconsag","txtGrconsag");
//        $oAuxField->set_value($this->get_post("txtGrconsag"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrconsag",tr_ts_fil_grconsag));
//        $arFields[] = $oAuxWrapper;
//        //grrembol
//        $oAuxField = new HelperInputText("txtGrrembol","txtGrrembol");
//        $oAuxField->set_value($this->get_post("txtGrrembol"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrrembol",tr_ts_fil_grrembol));
//        $arFields[] = $oAuxWrapper;
//        //grfechag
//        $oAuxField = new HelperInputText("txtGrfechag","txtGrfechag");
//        $oAuxField->set_value($this->get_post("txtGrfechag"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrfechag",tr_ts_fil_grfechag));
//        $arFields[] = $oAuxWrapper;
//        //grfeccon
//        $oAuxField = new HelperInputText("txtGrfeccon","txtGrfeccon");
//        $oAuxField->set_value($this->get_post("txtGrfeccon"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrfeccon",tr_ts_fil_grfeccon));
//        $arFields[] = $oAuxWrapper;
//        //grfecing
//        $oAuxField = new HelperInputText("txtGrfecing","txtGrfecing");
//        $oAuxField->set_value($this->get_post("txtGrfecing"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrfecing",tr_ts_fil_grfecing));
//        $arFields[] = $oAuxWrapper;
//        //grfecmod
//        $oAuxField = new HelperInputText("txtGrfecmod","txtGrfecmod");
//        $oAuxField->set_value($this->get_post("txtGrfecmod"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrfecmod",tr_ts_fil_grfecmod));
//        $arFields[] = $oAuxWrapper;
//        //grfecall
//        $oAuxField = new HelperInputText("txtGrfecall","txtGrfecall");
//        $oAuxField->set_value($this->get_post("txtGrfecall"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrfecall",tr_ts_fil_grfecall));
//        $arFields[] = $oAuxWrapper;
//        //grconpag
//        $oAuxField = new HelperInputText("txtGrconpag","txtGrconpag");
//        $oAuxField->set_value($this->get_post("txtGrconpag"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrconpag",tr_ts_fil_grconpag));
//        $arFields[] = $oAuxWrapper;
//        //grmontod
//        $oAuxField = new HelperInputText("txtGrmontod","txtGrmontod");
//        $oAuxField->set_value($this->get_post("txtGrmontod"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrmontod",tr_ts_fil_grmontod));
//        $arFields[] = $oAuxWrapper;
//        //grmonusd
//        $oAuxField = new HelperInputText("txtGrmonusd","txtGrmonusd");
//        $oAuxField->set_value($this->get_post("txtGrmonusd"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrmonusd",tr_ts_fil_grmonusd));
//        $arFields[] = $oAuxWrapper;
//        //grtasacm
//        $oAuxField = new HelperInputText("txtGrtasacm","txtGrtasacm");
//        $oAuxField->set_value($this->get_post("txtGrtasacm"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrtasacm",tr_ts_fil_grtasacm));
//        $arFields[] = $oAuxWrapper;
//        //grvlrcom
//        $oAuxField = new HelperInputText("txtGrvlrcom","txtGrvlrcom");
//        $oAuxField->set_value($this->get_post("txtGrvlrcom"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrvlrcom",tr_ts_fil_grvlrcom));
//        $arFields[] = $oAuxWrapper;
//        //grvlrajs
//        $oAuxField = new HelperInputText("txtGrvlrajs","txtGrvlrajs");
//        $oAuxField->set_value($this->get_post("txtGrvlrajs"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrvlrajs",tr_ts_fil_grvlrajs));
//        $arFields[] = $oAuxWrapper;
//        //grmontop
//        $oAuxField = new HelperInputText("txtGrmontop","txtGrmontop");
//        $oAuxField->set_value($this->get_post("txtGrmontop"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrmontop",tr_ts_fil_grmontop));
//        $arFields[] = $oAuxWrapper;
//        //grvlrdes
//        $oAuxField = new HelperInputText("txtGrvlrdes","txtGrvlrdes");
//        $oAuxField->set_value($this->get_post("txtGrvlrdes"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrvlrdes",tr_ts_fil_grvlrdes));
//        $arFields[] = $oAuxWrapper;
//        //grtipgir
//        $oAuxField = new HelperInputText("txtGrtipgir","txtGrtipgir");
//        $oAuxField->set_value($this->get_post("txtGrtipgir"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrtipgir",tr_ts_fil_grtipgir));
//        $arFields[] = $oAuxWrapper;
//        //grcdcoro
//        $oAuxField = new HelperInputText("txtGrcdcoro","txtGrcdcoro");
//        $oAuxField->set_value($this->get_post("txtGrcdcoro"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrcdcoro",tr_ts_fil_grcdcoro));
//        $arFields[] = $oAuxWrapper;
//        //grnorden
//        $oAuxField = new HelperInputText("txtGrnorden","txtGrnorden");
//        $oAuxField->set_value($this->get_post("txtGrnorden"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrnorden",tr_ts_fil_grnorden));
//        $arFields[] = $oAuxWrapper;
//        //grcdagen
//        $oAuxField = new HelperInputText("txtGrcdagen","txtGrcdagen");
//        $oAuxField->set_value($this->get_post("txtGrcdagen"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrcdagen",tr_ts_fil_grcdagen));
//        $arFields[] = $oAuxWrapper;
//        //grcdpais
//        $oAuxField = new HelperInputText("txtGrcdpais","txtGrcdpais");
//        $oAuxField->set_value($this->get_post("txtGrcdpais"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrcdpais",tr_ts_fil_grcdpais));
//        $arFields[] = $oAuxWrapper;
//        //grcdciud
//        $oAuxField = new HelperInputText("txtGrcdciud","txtGrcdciud");
//        $oAuxField->set_value($this->get_post("txtGrcdciud"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrcdciud",tr_ts_fil_grcdciud));
//        $arFields[] = $oAuxWrapper;
//        //grtipenv
//        $oAuxField = new HelperInputText("txtGrtipenv","txtGrtipenv");
//        $oAuxField->set_value($this->get_post("txtGrtipenv"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrtipenv",tr_ts_fil_grtipenv));
//        $arFields[] = $oAuxWrapper;
//        //gritemid
//        $oAuxField = new HelperInputText("txtGritemid","txtGritemid");
//        $oAuxField->set_value($this->get_post("txtGritemid"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGritemid",tr_ts_fil_gritemid));
//        $arFields[] = $oAuxWrapper;
//        //gritdesc
//        $oAuxField = new HelperInputText("txtGritdesc","txtGritdesc");
//        $oAuxField->set_value($this->get_post("txtGritdesc"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGritdesc",tr_ts_fil_gritdesc));
//        $arFields[] = $oAuxWrapper;
//        //grrelaci
//        $oAuxField = new HelperInputText("txtGrrelaci","txtGrrelaci");
//        $oAuxField->set_value($this->get_post("txtGrrelaci"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrrelaci",tr_ts_fil_grrelaci));
//        $arFields[] = $oAuxWrapper;
//        //grestciv
//        $oAuxField = new HelperInputText("txtGrestciv","txtGrestciv");
//        $oAuxField->set_value($this->get_post("txtGrestciv"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrestciv",tr_ts_fil_grestciv));
//        $arFields[] = $oAuxWrapper;
//        //grniving
//        $oAuxField = new HelperInputText("txtGrniving","txtGrniving");
//        $oAuxField->set_value($this->get_post("txtGrniving"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrniving",tr_ts_fil_grniving));
//        $arFields[] = $oAuxWrapper;
//        //gridsexo
//        $oAuxField = new HelperInputText("txtGridsexo","txtGridsexo");
//        $oAuxField->set_value($this->get_post("txtGridsexo"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGridsexo",tr_ts_fil_gridsexo));
//        $arFields[] = $oAuxWrapper;
//        //grcodnac
//        $oAuxField = new HelperInputText("txtGrcodnac","txtGrcodnac");
//        $oAuxField->set_value($this->get_post("txtGrcodnac"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrcodnac",tr_ts_fil_grcodnac));
//        $arFields[] = $oAuxWrapper;
//        //grtrabaj
//        $oAuxField = new HelperInputText("txtGrtrabaj","txtGrtrabaj");
//        $oAuxField->set_value($this->get_post("txtGrtrabaj"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrtrabaj",tr_ts_fil_grtrabaj));
//        $arFields[] = $oAuxWrapper;
//        //grapsrem
//        $oAuxField = new HelperInputText("txtGrapsrem","txtGrapsrem");
//        $oAuxField->set_value($this->get_post("txtGrapsrem"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrapsrem",tr_ts_fil_grapsrem));
//        $arFields[] = $oAuxWrapper;
//        //grident
//        $oAuxField = new HelperInputText("txtGrident","txtGrident");
//        $oAuxField->set_value($this->get_post("txtGrident"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrident",tr_ts_fil_grident));
//        $arFields[] = $oAuxWrapper;
//        //grcodcta
//        $oAuxField = new HelperInputText("txtGrcodcta","txtGrcodcta");
//        $oAuxField->set_value($this->get_post("txtGrcodcta"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrcodcta",tr_ts_fil_grcodcta));
//        $arFields[] = $oAuxWrapper;
//        //grdocpag
//        $oAuxField = new HelperInputText("txtGrdocpag","txtGrdocpag");
//        $oAuxField->set_value($this->get_post("txtGrdocpag"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrdocpag",tr_ts_fil_grdocpag));
//        $arFields[] = $oAuxWrapper;
//        //grnompag
//        $oAuxField = new HelperInputText("txtGrnompag","txtGrnompag");
//        $oAuxField->set_value($this->get_post("txtGrnompag"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrnompag",tr_ts_fil_grnompag));
//        $arFields[] = $oAuxWrapper;
//        //grusauto
//        $oAuxField = new HelperInputText("txtGrusauto","txtGrusauto");
//        $oAuxField->set_value($this->get_post("txtGrusauto"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrusauto",tr_ts_fil_grusauto));
//        $arFields[] = $oAuxWrapper;
//        //grtiptas
//        $oAuxField = new HelperInputText("txtGrtiptas","txtGrtiptas");
//        $oAuxField->set_value($this->get_post("txtGrtiptas"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrtiptas",tr_ts_fil_grtiptas));
//        $arFields[] = $oAuxWrapper;
//        //grnomcli
//        $oAuxField = new HelperInputText("txtGrnomcli","txtGrnomcli");
//        $oAuxField->set_value($this->get_post("txtGrnomcli"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrnomcli",tr_ts_fil_grnomcli));
//        $arFields[] = $oAuxWrapper;
//        //grapecli
//        $oAuxField = new HelperInputText("txtGrapecli","txtGrapecli");
//        $oAuxField->set_value($this->get_post("txtGrapecli"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrapecli",tr_ts_fil_grapecli));
//        $arFields[] = $oAuxWrapper;
//        //grsapcli
//        $oAuxField = new HelperInputText("txtGrsapcli","txtGrsapcli");
//        $oAuxField->set_value($this->get_post("txtGrsapcli"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrsapcli",tr_ts_fil_grsapcli));
//        $arFields[] = $oAuxWrapper;
//        //grofiscu
//        $oAuxField = new HelperInputText("txtGrofiscu","txtGrofiscu");
//        $oAuxField->set_value($this->get_post("txtGrofiscu"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrofiscu",tr_ts_fil_grofiscu));
//        $arFields[] = $oAuxWrapper;
//        //grnmsrem
//        $oAuxField = new HelperInputText("txtGrnmsrem","txtGrnmsrem");
//        $oAuxField->set_value($this->get_post("txtGrnmsrem"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrnmsrem",tr_ts_fil_grnmsrem));
//        $arFields[] = $oAuxWrapper;
//        //grfecenv
//        $oAuxField = new HelperInputText("txtGrfecenv","txtGrfecenv");
//        $oAuxField->set_value($this->get_post("txtGrfecenv"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrfecenv",tr_ts_fil_grfecenv));
//        $arFields[] = $oAuxWrapper;
//        //grhorenv
//        $oAuxField = new HelperInputText("txtGrhorenv","txtGrhorenv");
//        $oAuxField->set_value($this->get_post("txtGrhorenv"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrhorenv",tr_ts_fil_grhorenv));
//        $arFields[] = $oAuxWrapper;
//        //grtipreg
//        $oAuxField = new HelperInputText("txtGrtipreg","txtGrtipreg");
//        $oAuxField->set_value($this->get_post("txtGrtipreg"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrtipreg",tr_ts_fil_grtipreg));
//        $arFields[] = $oAuxWrapper;
//        //grcdcall
//        $oAuxField = new HelperInputText("txtGrcdcall","txtGrcdcall");
//        $oAuxField->set_value($this->get_post("txtGrcdcall"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrcdcall",tr_ts_fil_grcdcall));
//        $arFields[] = $oAuxWrapper;
//        //grpercon
//        $oAuxField = new HelperInputText("txtGrpercon","txtGrpercon");
//        $oAuxField->set_value($this->get_post("txtGrpercon"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrpercon",tr_ts_fil_grpercon));
//        $arFields[] = $oAuxWrapper;
//        //grgirenv
//        $oAuxField = new HelperInputText("txtGrgirenv","txtGrgirenv");
//        $oAuxField->set_value($this->get_post("txtGrgirenv"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrgirenv",tr_ts_fil_grgirenv));
//        $arFields[] = $oAuxWrapper;
//        //grclave
//        $oAuxField = new HelperInputText("txtGrclave","txtGrclave");
//        $oAuxField->set_value($this->get_post("txtGrclave"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrclave",tr_ts_fil_grclave));
//        $arFields[] = $oAuxWrapper;
//        //grcorpri
//        $oAuxField = new HelperInputText("txtGrcorpri","txtGrcorpri");
//        $oAuxField->set_value($this->get_post("txtGrcorpri"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrcorpri",tr_ts_fil_grcorpri));
//        $arFields[] = $oAuxWrapper;
//        //grmarcad
//        $oAuxField = new HelperInputText("txtGrmarcad","txtGrmarcad");
//        $oAuxField->set_value($this->get_post("txtGrmarcad"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrmarcad",tr_ts_fil_grmarcad));
//        $arFields[] = $oAuxWrapper;
//        //grmoncta
//        $oAuxField = new HelperInputText("txtGrmoncta","txtGrmoncta");
//        $oAuxField->set_value($this->get_post("txtGrmoncta"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrmoncta",tr_ts_fil_grmoncta));
//        $arFields[] = $oAuxWrapper;
//        //grnorde2
//        $oAuxField = new HelperInputText("txtGrnorde2","txtGrnorde2");
//        $oAuxField->set_value($this->get_post("txtGrnorde2"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrnorde2",tr_ts_fil_grnorde2));
//        $arFields[] = $oAuxWrapper;
//        //grpagcor
//        $oAuxField = new HelperInputText("txtGrpagcor","txtGrpagcor");
//        $oAuxField->set_value($this->get_post("txtGrpagcor"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrpagcor",tr_ts_fil_grpagcor));
//        $arFields[] = $oAuxWrapper;
//        //grmonpag
//        $oAuxField = new HelperInputText("txtGrmonpag","txtGrmonpag");
//        $oAuxField->set_value($this->get_post("txtGrmonpag"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrmonpag",tr_ts_fil_grmonpag));
//        $arFields[] = $oAuxWrapper;
//        //grcdtran
//        $oAuxField = new HelperInputText("txtGrcdtran","txtGrcdtran");
//        $oAuxField->set_value($this->get_post("txtGrcdtran"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrcdtran",tr_ts_fil_grcdtran));
//        $arFields[] = $oAuxWrapper;
//        //grtipcon
//        $oAuxField = new HelperInputText("txtGrtipcon","txtGrtipcon");
//        $oAuxField->set_value($this->get_post("txtGrtipcon"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrtipcon",tr_ts_fil_grtipcon));
//        $arFields[] = $oAuxWrapper;
//        //grestado
//        $oAuxField = new HelperInputText("txtGrestado","txtGrestado");
//        $oAuxField->set_value($this->get_post("txtGrestado"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrestado",tr_ts_fil_grestado));
//        $arFields[] = $oAuxWrapper;
//        //grcausae
//        $oAuxField = new HelperInputText("txtGrcausae","txtGrcausae");
//        $oAuxField->set_value($this->get_post("txtGrcausae"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrcausae",tr_ts_fil_grcausae));
//        $arFields[] = $oAuxWrapper;
//        //grcduser
//        $oAuxField = new HelperInputText("txtGrcduser","txtGrcduser");
//        $oAuxField->set_value($this->get_post("txtGrcduser"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrcduser",tr_ts_fil_grcduser));
//        $arFields[] = $oAuxWrapper;
//        //grtippag
//        $oAuxField = new HelperInputText("txtGrtippag","txtGrtippag");
//        $oAuxField->set_value($this->get_post("txtGrtippag"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrtippag",tr_ts_fil_grtippag));
//        $arFields[] = $oAuxWrapper;
//        //grcdbanc
//        $oAuxField = new HelperInputText("txtGrcdbanc","txtGrcdbanc");
//        $oAuxField->set_value($this->get_post("txtGrcdbanc"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrcdbanc",tr_ts_fil_grcdbanc));
//        $arFields[] = $oAuxWrapper;
//        //grtipcta
//        $oAuxField = new HelperInputText("txtGrtipcta","txtGrtipcta");
//        $oAuxField->set_value($this->get_post("txtGrtipcta"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrtipcta",tr_ts_fil_grtipcta));
//        $arFields[] = $oAuxWrapper;
//        //grnrocta
//        $oAuxField = new HelperInputText("txtGrnrocta","txtGrnrocta");
//        $oAuxField->set_value($this->get_post("txtGrnrocta"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrnrocta",tr_ts_fil_grnrocta));
//        $arFields[] = $oAuxWrapper;
//        //grobserv
//        $oAuxField = new HelperInputText("txtGrobserv","txtGrobserv");
//        $oAuxField->set_value($this->get_post("txtGrobserv"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrobserv",tr_ts_fil_grobserv));
//        $arFields[] = $oAuxWrapper;
//        //grmonori
//        $oAuxField = new HelperInputText("txtGrmonori","txtGrmonori");
//        $oAuxField->set_value($this->get_post("txtGrmonori"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrmonori",tr_ts_fil_grmonori));
//        $arFields[] = $oAuxWrapper;
//        //grdrbene
//        $oAuxField = new HelperInputText("txtGrdrbene","txtGrdrbene");
//        $oAuxField->set_value($this->get_post("txtGrdrbene"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrdrbene",tr_ts_fil_grdrbene));
//        $arFields[] = $oAuxWrapper;
//        //grdrben2
//        $oAuxField = new HelperInputText("txtGrdrben2","txtGrdrben2");
//        $oAuxField->set_value($this->get_post("txtGrdrben2"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrdrben2",tr_ts_fil_grdrben2));
//        $arFields[] = $oAuxWrapper;
//        //grembene
//        $oAuxField = new HelperInputText("txtGrembene","txtGrembene");
//        $oAuxField->set_value($this->get_post("txtGrembene"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrembene",tr_ts_fil_grembene));
//        $arFields[] = $oAuxWrapper;
//        //grtlbene
//        $oAuxField = new HelperInputText("txtGrtlbene","txtGrtlbene");
//        $oAuxField->set_value($this->get_post("txtGrtlbene"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrtlbene",tr_ts_fil_grtlbene));
//        $arFields[] = $oAuxWrapper;
//        //grtlben2
//        $oAuxField = new HelperInputText("txtGrtlben2","txtGrtlben2");
//        $oAuxField->set_value($this->get_post("txtGrtlben2"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrtlben2",tr_ts_fil_grtlben2));
//        $arFields[] = $oAuxWrapper;
//        //grmensaj
//        $oAuxField = new HelperInputText("txtGrmensaj","txtGrmensaj");
//        $oAuxField->set_value($this->get_post("txtGrmensaj"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrmensaj",tr_ts_fil_grmensaj));
//        $arFields[] = $oAuxWrapper;
//        //grciudds
//        $oAuxField = new HelperInputText("txtGrciudds","txtGrciudds");
//        $oAuxField->set_value($this->get_post("txtGrciudds"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrciudds",tr_ts_fil_grciudds));
//        $arFields[] = $oAuxWrapper;
//        //grcdcorr
//        $oAuxField = new HelperInputText("txtGrcdcorr","txtGrcdcorr");
//        $oAuxField->set_value($this->get_post("txtGrcdcorr"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrcdcorr",tr_ts_fil_grcdcorr));
//        $arFields[] = $oAuxWrapper;
//        //gragends
//        $oAuxField = new HelperInputText("txtGragends","txtGragends");
//        $oAuxField->set_value($this->get_post("txtGragends"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGragends",tr_ts_fil_gragends));
//        $arFields[] = $oAuxWrapper;
//        //grtdbene
//        $oAuxField = new HelperInputText("txtGrtdbene","txtGrtdbene");
//        $oAuxField->set_value($this->get_post("txtGrtdbene"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrtdbene",tr_ts_fil_grtdbene));
//        $arFields[] = $oAuxWrapper;
//        //grcdbene
//        $oAuxField = new HelperInputText("txtGrcdbene","txtGrcdbene");
//        $oAuxField->set_value($this->get_post("txtGrcdbene"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrcdbene",tr_ts_fil_grcdbene));
//        $arFields[] = $oAuxWrapper;
//        //grnmbene
//        $oAuxField = new HelperInputText("txtGrnmbene","txtGrnmbene");
//        $oAuxField->set_value($this->get_post("txtGrnmbene"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrnmbene",tr_ts_fil_grnmbene));
//        $arFields[] = $oAuxWrapper;
//        //grciudor
//        $oAuxField = new HelperInputText("txtGrciudor","txtGrciudor");
//        $oAuxField->set_value($this->get_post("txtGrciudor"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrciudor",tr_ts_fil_grciudor));
//        $arFields[] = $oAuxWrapper;
//        //gremremi
//        $oAuxField = new HelperInputText("txtGremremi","txtGremremi");
//        $oAuxField->set_value($this->get_post("txtGremremi"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGremremi",tr_ts_fil_gremremi));
//        $arFields[] = $oAuxWrapper;
//        //grcdocup
//        $oAuxField = new HelperInputText("txtGrcdocup","txtGrcdocup");
//        $oAuxField->set_value($this->get_post("txtGrcdocup"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrcdocup",tr_ts_fil_grcdocup));
//        $arFields[] = $oAuxWrapper;
//        //grtlremi
//        $oAuxField = new HelperInputText("txtGrtlremi","txtGrtlremi");
//        $oAuxField->set_value($this->get_post("txtGrtlremi"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrtlremi",tr_ts_fil_grtlremi));
//        $arFields[] = $oAuxWrapper;
//        //grtlrem2
//        $oAuxField = new HelperInputText("txtGrtlrem2","txtGrtlrem2");
//        $oAuxField->set_value($this->get_post("txtGrtlrem2"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrtlrem2",tr_ts_fil_grtlrem2));
//        $arFields[] = $oAuxWrapper;
//        //grpaisds
//        $oAuxField = new HelperInputText("txtGrpaisds","txtGrpaisds");
//        $oAuxField->set_value($this->get_post("txtGrpaisds"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrpaisds",tr_ts_fil_grpaisds));
//        $arFields[] = $oAuxWrapper;
//        //grusmodi
//        $oAuxField = new HelperInputText("txtGrusmodi","txtGrusmodi");
//        $oAuxField->set_value($this->get_post("txtGrusmodi"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrusmodi",tr_ts_fil_grusmodi));
//        $arFields[] = $oAuxWrapper;
//        //grtdremi
//        $oAuxField = new HelperInputText("txtGrtdremi","txtGrtdremi");
//        $oAuxField->set_value($this->get_post("txtGrtdremi"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrtdremi",tr_ts_fil_grtdremi));
//        $arFields[] = $oAuxWrapper;
//        //grcdremi
//        $oAuxField = new HelperInputText("txtGrcdremi","txtGrcdremi");
//        $oAuxField->set_value($this->get_post("txtGrcdremi"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrcdremi",tr_ts_fil_grcdremi));
//        $arFields[] = $oAuxWrapper;
//        //grnmremi
//        $oAuxField = new HelperInputText("txtGrnmremi","txtGrnmremi");
//        $oAuxField->set_value($this->get_post("txtGrnmremi"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrnmremi",tr_ts_fil_grnmremi));
//        $arFields[] = $oAuxWrapper;
//        //grdrremi
//        $oAuxField = new HelperInputText("txtGrdrremi","txtGrdrremi");
//        $oAuxField->set_value($this->get_post("txtGrdrremi"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrdrremi",tr_ts_fil_grdrremi));
//        $arFields[] = $oAuxWrapper;
//        //grdrrem2
//        $oAuxField = new HelperInputText("txtGrdrrem2","txtGrdrrem2");
//        $oAuxField->set_value($this->get_post("txtGrdrrem2"));
//        $oAuxField->on_entersubmit();
//        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrdrrem2",tr_ts_fil_grdrrem2));
//        $arFields[] = $oAuxWrapper;
//</editor-fold>         
        return $arFields;
    }//get_list_filters()

    //list_7
    protected function get_list_columns()
    {
        $arColumns = array();
//<editor-fold defaultstate="collapsed" desc="NIU">        
        //$arColumns["gridgiro"] = tr_ts_col_gridgiro;
        //$arColumns["grsecuen"] = tr_ts_col_grsecuen;

//        $arColumns["grsecueo"] = tr_ts_col_grsecueo;
//        $arColumns["gridremi"] = tr_ts_col_gridremi;   
//        $arColumns["gridbene"] = tr_ts_col_gridbene;
//        $arColumns["grfactur"] = tr_ts_col_grfactur;
//        $arColumns["grcdcart"] = tr_ts_col_grcdcart;
//        $arColumns["grcdcont"] = tr_ts_col_grcdcont;
//        $arColumns["grnrolla"] = tr_ts_col_grnrolla;
//        $arColumns["grconsag"] = tr_ts_col_grconsag;
//        $arColumns["grrembol"] = tr_ts_col_grrembol;
//        $arColumns["grfechag"] = tr_ts_col_grfechag;
//        $arColumns["grfeccon"] = tr_ts_col_grfeccon;
//        $arColumns["grfecing"] = tr_ts_col_grfecing;
//        $arColumns["grfecmod"] = tr_ts_col_grfecmod;
//        $arColumns["grfecall"] = tr_ts_col_grfecall;
//        $arColumns["grconpag"] = tr_ts_col_grconpag;
//        $arColumns["grmontod"] = tr_ts_col_grmontod;
//        $arColumns["grmonusd"] = tr_ts_col_grmonusd;
//        $arColumns["grtasacm"] = tr_ts_col_grtasacm;
//        $arColumns["grvlrcom"] = tr_ts_col_grvlrcom;
//        $arColumns["grvlrajs"] = tr_ts_col_grvlrajs;
//        $arColumns["grmontop"] = tr_ts_col_grmontop;
//        $arColumns["grvlrdes"] = tr_ts_col_grvlrdes;
//        $arColumns["grtipgir"] = tr_ts_col_grtipgir;
//        $arColumns["grcdcoro"] = tr_ts_col_grcdcoro;
//        $arColumns["grnorden"] = tr_ts_col_grnorden;
//        $arColumns["grcdagen"] = tr_ts_col_grcdagen;
//        $arColumns["grcdpais"] = tr_ts_col_grcdpais;
//        $arColumns["grcdciud"] = tr_ts_col_grcdciud;
//        $arColumns["grtipenv"] = tr_ts_col_grtipenv;
//        $arColumns["gritemid"] = tr_ts_col_gritemid;
//        $arColumns["gritdesc"] = tr_ts_col_gritdesc;
//        $arColumns["grrelaci"] = tr_ts_col_grrelaci;
//        $arColumns["grestciv"] = tr_ts_col_grestciv;
//        $arColumns["grniving"] = tr_ts_col_grniving;
//        $arColumns["gridsexo"] = tr_ts_col_gridsexo;
//        $arColumns["grcodnac"] = tr_ts_col_grcodnac;
//        $arColumns["grtrabaj"] = tr_ts_col_grtrabaj;
//        $arColumns["grapsrem"] = tr_ts_col_grapsrem;
//        $arColumns["grident"] = tr_ts_col_grident;
//        $arColumns["grcodcta"] = tr_ts_col_grcodcta;
//        $arColumns["grdocpag"] = tr_ts_col_grdocpag;
//        $arColumns["grnompag"] = tr_ts_col_grnompag;
//        $arColumns["grusauto"] = tr_ts_col_grusauto;
//        $arColumns["grtiptas"] = tr_ts_col_grtiptas;
//        $arColumns["grnomcli"] = tr_ts_col_grnomcli;
//        $arColumns["grapecli"] = tr_ts_col_grapecli;
//        $arColumns["grsapcli"] = tr_ts_col_grsapcli;
//        $arColumns["grofiscu"] = tr_ts_col_grofiscu;
//        $arColumns["grnmsrem"] = tr_ts_col_grnmsrem;
//        $arColumns["grfecenv"] = tr_ts_col_grfecenv;
//        $arColumns["grhorenv"] = tr_ts_col_grhorenv;
//        $arColumns["grtipreg"] = tr_ts_col_grtipreg;
//        $arColumns["grcdcall"] = tr_ts_col_grcdcall;
//        $arColumns["grpercon"] = tr_ts_col_grpercon;
//        $arColumns["grgirenv"] = tr_ts_col_grgirenv;
//        $arColumns["grclave"] = tr_ts_col_grclave;
//        $arColumns["grcorpri"] = tr_ts_col_grcorpri;
//        $arColumns["grmarcad"] = tr_ts_col_grmarcad;
//        $arColumns["grmoncta"] = tr_ts_col_grmoncta;
//        $arColumns["grnorde2"] = tr_ts_col_grnorde2;
//        $arColumns["grpagcor"] = tr_ts_col_grpagcor;
//        $arColumns["grmonpag"] = tr_ts_col_grmonpag;
//        $arColumns["grcdtran"] = tr_ts_col_grcdtran;
//        $arColumns["grtipcon"] = tr_ts_col_grtipcon;
//        $arColumns["grestado"] = tr_ts_col_grestado;
//        $arColumns["grcausae"] = tr_ts_col_grcausae;
//        $arColumns["grcduser"] = tr_ts_col_grcduser;
//        $arColumns["grtippag"] = tr_ts_col_grtippag;
//        $arColumns["grcdbanc"] = tr_ts_col_grcdbanc;
//        $arColumns["grtipcta"] = tr_ts_col_grtipcta;
//        $arColumns["grnrocta"] = tr_ts_col_grnrocta;
//        $arColumns["grobserv"] = tr_ts_col_grobserv;
//        $arColumns["grmonori"] = tr_ts_col_grmonori;
//        $arColumns["grdrbene"] = tr_ts_col_grdrbene;
//        $arColumns["grdrben2"] = tr_ts_col_grdrben2;
//        $arColumns["grembene"] = tr_ts_col_grembene;
//        $arColumns["grtlbene"] = tr_ts_col_grtlbene;
//        $arColumns["grtlben2"] = tr_ts_col_grtlben2;
//        $arColumns["grmensaj"] = tr_ts_col_grmensaj;
//        $arColumns["grciudds"] = tr_ts_col_grciudds;
//        $arColumns["grcdcorr"] = tr_ts_col_grcdcorr;
//        $arColumns["gragends"] = tr_ts_col_gragends;
//        $arColumns["grtdbene"] = tr_ts_col_grtdbene;
//        $arColumns["grcdbene"] = tr_ts_col_grcdbene;
//        $arColumns["grnmbene"] = tr_ts_col_grnmbene;
//        $arColumns["grciudor"] = tr_ts_col_grciudor;
//        $arColumns["gremremi"] = tr_ts_col_gremremi;
//        $arColumns["grcdocup"] = tr_ts_col_grcdocup;
//        $arColumns["grtlremi"] = tr_ts_col_grtlremi;
//        $arColumns["grtlrem2"] = tr_ts_col_grtlrem2;
//        $arColumns["grpaisds"] = tr_ts_col_grpaisds;
//        $arColumns["grusmodi"] = tr_ts_col_grusmodi;
//        $arColumns["grtdremi"] = tr_ts_col_grtdremi;
//        $arColumns["grcdremi"] = tr_ts_col_grcdremi;
//        $arColumns["grnmremi"] = tr_ts_col_grnmremi;
//        $arColumns["grdrremi"] = tr_ts_col_grdrremi;
//        $arColumns["grdrrem2"] = tr_ts_col_grdrrem2;
//</editor-fold>  
        return $arColumns;
    }//get_list_columns()

    //list_7.1
    private function build_list_detail()
    {
        $arFields = array(); $oAuxField = NULL; $oAuxLabel = NULL; $oGroup = NULL;
        
        $oFieldSet = new HelperFieldset();
        $oFieldSet->add_style("padding:0;margin:0;margin-top:2px;border:1px solid #8E8E8E;");
        
        //grsecuen - SECUENCIA
        $oAuxField = new HelperInputText("txtGrsecuen","txtGrsecuen");
        $oAuxField->set_value($this->oTabgiros->get_grsecuen());
        $oAuxLabel = new HelperLabel("txtGrsecuen",tr_ts_upd_grsecuen,"lblGrsecuen");
        $oAuxLabel->add_class("labelpk");
        $oAuxField->readonly();$oAuxField->add_class("readonly");
        $oGroup = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        $oGroup->set_span(3);
        $oFieldSet->add_inner_object($oGroup);
        //$arFields[] = $oGroup;

        //grcdagen - AGENCIA
        $oPicklist = new ModelAgencias();
        $sPicklistAnd = "accdagen='".$this->oTabgiros->get_grcdagen()."'";
        $arOptions = $oPicklist->get_picklist_custom("accdagen","acnombre",$sPicklistAnd);
        $oAuxField = new HelperSelect($arOptions,"selGrcdagen","selGrcdagen");
        $oAuxField->set_value_to_select($this->oTabgiros->get_grcdagen());
        $oAuxLabel = new HelperLabel("selGrcdagen",tr_ts_upd_grcdagen,"lblGrcdagen");
        $oAuxField->readonly();$oAuxField->add_class("readonly");
        $oGroup = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        $oFieldset = new HelperFieldset();
        $oGroup->set_span(3);
        $oFieldSet->add_inner_object($oGroup);
        //$arFields[] = $oGroup;
        
        //grsecueo - GIROS RECIBIDOS
//        $oAuxField = new HelperInputText("txtGrsecueo","txtGrsecueo");
//        $oAuxField->set_value($this->oTabgiros->get_grsecueo());
//        $oAuxLabel = new HelperLabel("txtGrsecueo",tr_ts_upd_grsecueo,"lblGrsecueo");
//        $oAuxField->readonly();$oAuxField->add_class("readonly");
//        $oGroup = new ApphelperControlGroup($oAuxField,$oAuxLabel);
//        $oGroup->set_span(3);
//        $arFields[] = $oGroup;
        
        //grfechag - FECHA: HORA
        $oAuxField = new HelperInputText("txtGrfechag","txtGrfechag");
        $oAuxField->set_value($this->oTabgiros->get_grfechag());
        $oAuxLabel = new HelperLabel("txtGrfechag",tr_ts_upd_grfechag,"lblGrfechag");
        $oAuxField->readonly();$oAuxField->add_class("readonly");
        $oGroup = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        $oGroup->set_span(3);
        $oFieldSet->add_inner_object($oGroup);
        //$arFields[] = $oGroup;

        //grcdcorr - CORRESPONSAL
        $oPicklist = new ModelCorrespo();
        $sPicklistAnd = "crcdcorr='".$this->oTabgiros->get_grcdcorr()."'";
        $arOptions = $oPicklist->get_picklist_custom("crcdcorr","crnombre",$sPicklistAnd);
        $oAuxField = new HelperSelect($arOptions,"selGrcdcorr","selGrcdcorr");
        $oAuxField->set_value_to_select($this->oTabgiros->get_grcdcorr());
        $oAuxLabel = new HelperLabel("selGrcdcorr",tr_ts_upd_grcdcorr,"lblGrcdcorr");
        $oAuxField->readonly();$oAuxField->add_class("readonly");
        $oGroup = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        $oGroup->set_span(3);
        $oFieldSet->add_inner_object($oGroup);
        //$arFields[] = $oGroup;

        //grtipgir - TIPO DE ENVIO
        $oAuxField = new HelperInputText("txtGrtipgir","txtGrtipgir");
        if($this->oTabgiros->get_grtipgir()=="E") $oAuxField->set_value("ENVIADO");
        if($this->oTabgiros->get_grtipgir()=="R") $oAuxField->set_value("RECIBIDO");
        $oAuxLabel = new HelperLabel("txtGrtipgir",tr_ts_upd_grtipgir,"lblGrtipgir");
        //$oAuxField->add_style("background:green;color:white;font-weight:bold;");
        $oAuxField->readonly();$oAuxField->add_class("readonly");
        $oGroup = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        $oGroup->set_span(3);
        $oFieldSet->add_inner_object($oGroup);
        $arFields[] = $oFieldSet;
        //========================= FIN GRUPO 1 ================================
        $oFieldSet = new HelperFieldset();
        $oFieldSet->add_style("padding:0;margin:0;margin-top:2px;border:1px solid red;");
        
        //gremremi - REMITENTE
        $oAuxField = new HelperInputText("txtGrnmremi","txtGrnmremi");
        $oAuxField->set_value($this->oTabgiros->get_grnmremi());
        $oAuxLabel = new HelperLabel("txtGrnmremi",tr_ts_upd_grnmremi,"lblGremremi");
        $oAuxField->add_class("span3");
        $oAuxField->add_style("background:red;color:white;font-weight:bold;");
        $oAuxField->readonly();//$oAuxField->add_class("readonly");
        $oGroup = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        $oGroup->set_span(3);
        $oFieldSet->add_inner_object($oGroup);
        //$arFields[] = $oGroup;

        //grtlremi - REMITENTE - TELEFONO 
        $oAuxField = new HelperInputText("txtGrtlremi","txtGrtlremi");
        $oAuxField->set_value($this->oTabgiros->get_grtlremi());
        $oAuxLabel = new HelperLabel("txtGrtlremi",tr_ts_upd_grtlremi,"lblGrtlremi");
        $oAuxField->add_style("background:red;color:white;font-weight:bold;");
        $oAuxField->readonly();//$oAuxField->add_class("readonly");
        $oGroup = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        $oGroup->set_span(3);
        $oFieldSet->add_inner_object($oGroup);
        $arFields[] = $oFieldSet;
        //========================= FIN GRUPO 2 ================================
        $oFieldSet = new HelperFieldset();
        $oFieldSet->add_style("padding:0;margin:0;margin-top:2px;border:1px solid blue;");
        
        //grnmbene - BENEFICIARIO
        $oAuxField = new HelperInputText("txtGrnmbene","txtGrnmbene");
        $oAuxField->set_value($this->oTabgiros->get_grnmbene());
        $oAuxField->add_class("span3");
        $oAuxLabel = new HelperLabel("txtGrnmbene",tr_ts_upd_grnmbene,"lblGrnmbene");
        $oAuxField->add_style("background:blue;color:white;font-weight:bold;");
        $oAuxField->readonly();//$oAuxField->add_class("readonly");
        $oGroup = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        $oGroup->set_span(3);
        $oFieldSet->add_inner_object($oGroup);
        //$arFields[] = $oGroup;
        
        //grtlbene BENEFICIARIO - TELEFONO
        $oAuxField = new HelperInputText("txtGrtlbene","txtGrtlbene");
        $oAuxField->set_value($this->oTabgiros->get_grtlbene());
        $oAuxLabel = new HelperLabel("txtGrtlbene",tr_ts_upd_grtlbene,"lblGrtlbene");
        $oAuxField->add_style("background:blue;color:white;font-weight:bold;");
        $oAuxField->readonly();//$oAuxField->add_class("readonly");
        $oGroup = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        $oGroup->set_span(3);
        $oFieldSet->add_inner_object($oGroup);
        //$arFields[] = $oGroup;

        //grmonusd - MONTO EN DOLARES
        $oAuxField = new HelperInputText("txtGrmonusd","txtGrmonusd");
        $oAuxField->set_value(dbbo_numeric2($this->oTabgiros->get_grmonusd()));
        $oAuxLabel = new HelperLabel("txtGrmonusd",tr_ts_upd_grmonusd,"lblGrmonusd");
        $oAuxField->add_style("background:blue;color:white;font-weight:bold;");
        $oAuxField->readonly();//$oAuxField->add_class("readonly");
        $oGroup = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        $oGroup->set_span(3);
        $oFieldSet->add_inner_object($oGroup);
        //$arFields[] = $oGroup;

        //grpaisds - PAIS
        $oPicklist = new ModelTbpaises();
        $sPicklistAnd = "pscdpais='".$this->oTabgiros->get_grpaisds()."'";
        $arOptions = $oPicklist->get_picklist_custom("pscdpais","psnombre",$sPicklistAnd);
        $oAuxField = new HelperSelect($arOptions,"selGrpaisds","selGrpaisds");
        $oAuxField->set_value_to_select($this->oTabgiros->get_grpaisds());
        $oAuxField->add_style("background:blue;color:white;font-weight:bold;");
        $oAuxField->readonly();//$oAuxField->add_class("readonly");
        $oAuxLabel = new HelperLabel("txtGrpaisds",tr_ts_upd_grpaisds,"lblGrpaisds");
        $oGroup = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        $oGroup->set_span(3);
        $oFieldSet->add_inner_object($oGroup);
        //$arFields[] = $oGroup;
                
        //grciudds - CIUDAD
        $oPicklist = new ModelCiudades();
        $sPicklistAnd = "cdcdciud='".$this->oTabgiros->get_grciudds()."'";
        $arOptions = $oPicklist->get_picklist_custom("cdcdciud","cdnombre",$sPicklistAnd);
        $oAuxField = new HelperSelect($arOptions,"selGrciudds","selGrciudds");
        $oAuxField->set_value_to_select($this->oTabgiros->get_grciudds());
        $oAuxField->add_style("background:blue;color:white;font-weight:bold;");
        $oAuxField->readonly();//$oAuxField->add_class("readonly");
        $oAuxLabel = new HelperLabel("txtGrciudds",tr_ts_upd_grciudds,"lblGrciudds");
        $oGroup = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        $oGroup->set_span(3);
        $oFieldSet->add_inner_object($oGroup);
        //$arFields[] = $oGroup;
        
        //grdrbene - BENEFICIARIO - DIRECCION
        $oAuxField = new HelperInputText("txtGrdrbene","txtGrdrbene");
        $oAuxField->set_value($this->oTabgiros->get_grdrbene());
        $oAuxLabel = new HelperLabel("txtGrdrbene",tr_ts_upd_grdrbene,"lblGrdrbene");
        $oAuxField->add_style("background:blue;color:white;font-weight:bold;");
        $oAuxField->readonly();//$oAuxField->add_class("readonly");
        $oGroup = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        $oGroup->set_span(3);
        $oFieldSet->add_inner_object($oGroup);
        //$arFields[] = $oGroup;
        
        //grtippag FORMA PAGO EF=EFECTIVO, CS=CONSIGNACION, AD=LLEVAR (A DOMICILIO)
        $oAuxField = new HelperInputText("txtGrtippag","txtGrtippag");
        if($this->oTabgiros->get_grtippag()=="EF") $oAuxField->set_value("EFECTIVO");
        if($this->oTabgiros->get_grtippag()=="CS") $oAuxField->set_value("CONSIGNACION");
        if($this->oTabgiros->get_grtippag()=="AD") $oAuxField->set_value("LLEVAR");
        $oAuxLabel = new HelperLabel("txtGrtippag",tr_ts_upd_grtippag,"lblGrtippag");
        $oAuxField->add_style("background:blue;color:white;font-weight:bold;");
        $oAuxField->readonly();//$oAuxField->add_class("readonly");
        $oGroup = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        $oGroup->set_span(3);
        $oFieldSet->add_inner_object($oGroup);
        //$arFields[] = $oGroup;
        
        //grcdbanc - BANCO
        $oPicklist = new ModelTbbancos();
        $sPicklistAnd = "bccdbanc='".$this->oTabgiros->get_grcdbanc()."'";
        $arOptions = $oPicklist->get_picklist_custom("bccdbanc","bcnombre",$sPicklistAnd);
        $oAuxField = new HelperSelect($arOptions,"selGrcdbanc","selGrcdbanc");
        $oAuxField->set_value_to_select($this->oTabgiros->get_grcdbanc());
        $oAuxField->add_style("background:blue;color:white;font-weight:bold;");
        $oAuxField->readonly();//$oAuxField->add_class("readonly");
        $oAuxLabel = new HelperLabel("txtGrcdbanc",tr_ts_upd_grcdbanc,"lblGrcdbanc");
        $oGroup = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        $oGroup->set_span(3);
        $oFieldSet->add_inner_object($oGroup);
        //$arFields[] = $oGroup;
        
        //grnrocta - Nº CUENTA
        $oAuxField = new HelperInputText("txtGrnrocta","txtGrnrocta");
        $oAuxField->set_value($this->oTabgiros->get_grnrocta());
        $oAuxLabel = new HelperLabel("txtGrnrocta",tr_ts_upd_grnrocta,"lblGrnrocta");
        $oAuxField->add_style("background:blue;color:white;font-weight:bold;");
        $oAuxField->readonly();//$oAuxField->add_class("readonly");
        $oGroup = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        $oGroup->set_span(3);
        $oFieldSet->add_inner_object($oGroup);
        $arFields[] = $oFieldSet;
        
        //========================= FIN GRUPO 3 ================================
        $oFieldSet = new HelperFieldset();
        $oFieldSet->add_style("padding:0;margin:0;margin-top:2px;border:1px solid green;");
        
        //grcduser - CAJERO
        $oAuxField = new HelperInputText("txtGrcduser","txtGrcduser");
        $oAuxField->set_value($this->oTabgiros->get_grcduser());
        $oAuxLabel = new HelperLabel("txtGrcduser",tr_ts_upd_grcduser,"lblGrcduser");
        $oAuxField->add_style("background:green;color:white;font-weight:bold;");
        $oAuxField->readonly();//$oAuxField->add_class("readonly");
        $oGroup = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        $oGroup->set_span(3);
        $oFieldSet->add_inner_object($oGroup);
        //$arFields[] = $oGroup;
        
        //grestado - ESTADO AC:ACTIVO, CE:PAGADO, AN:ANULADO
        $oAuxField = new HelperInputText("txtGrestado","txtGrestado");
        if($this->oTabgiros->get_grestado()=="AC") $oAuxField->set_value("ACTIVO");
        if($this->oTabgiros->get_grestado()=="CE") $oAuxField->set_value("PAGADO");
        if($this->oTabgiros->get_grestado()=="AN") $oAuxField->set_value("ANULADO");
        $oAuxLabel = new HelperLabel("txtGrestado",tr_ts_upd_grestado,"lblGrestado");
        $oAuxField->add_style("background:green;color:white;font-weight:bold;");
        $oAuxField->readonly();//$oAuxField->add_class("readonly");
        $oGroup = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        $oGroup->set_span(3);
        $oFieldSet->add_inner_object($oGroup);
        $arFields[] = $oFieldSet;

        //gridgiro
        $oAuxField = new HelperInputHidden("hidGridgiro","hidGridgiro");
        $oAuxField->set_value($this->oTabgiros->get_gridgiro());
        $arFields[] = $oAuxField;
        
        $oForm = new HelperForm("frmDetail");
        $oForm->add_class("form-horizontal");
        $oForm->add_style("margin-bottom:0");
        $oForm->add_controls($arFields);
        return $oForm;
    }
    
    //list_8
    public function get_list()
    {
        $this->go_to_401($this->oPermission->is_not_select());
        $oAlert = new AppHelperAlertdiv();
        $oAlert->use_close_button();
        $sMessage = $this->get_session_message($sMessage);
        if($sMessage)
            $oAlert->set_title($sMessage);
        $sMessage = $this->get_session_message($sMessage,"e");
        if($sMessage)
        {
            $oAlert->set_type();
            $oAlert->set_title($sMessage);
        }
        $arColumns = $this->get_list_columns(); 

        //Carga en la variable global la configuración de los campos que se utilizarán
        //FILTERS
        $this->load_config_list_filters();
        $oFilter = new ComponentFilter();
        $oFilter->set_fieldnames($this->get_filter_fieldnames());
        //Indica que no se guardara en sesion por nombre de campo sino por nombre de control
        //para esto es necesario respetar el estricto camelcase
        $oFilter->use_field_prefix();
        //Guarda en sesion y post los campos enviados, los de orden y página
        $oFilter->refresh();
        $this->set_listfilters_from_post();
        $arObjFilter = $this->get_list_filters();

        //RECOVER DATALIST
        $this->oTabgiros->set_gridgiro($this->get_post("txtGridgiro"));
        //$this->oTabgiros->set_grsecuen($this->get_post("txtGrsecuen"));
        $this->oTabgiros->load_by_gridgiro();
        //TABLE
        //This method adds objects controls to search list form
        $oTableList = new HelperTableTyped();
        $oTableList->set_fields($arObjFilter);
        $oTableList->set_no_paginatebar();
        if($this->oTabgiros->get_gridgiro())
            $oForm = $this->build_list_detail();
        //SCRUMBS
        $oScrumbs = $this->build_list_scrumbs();
        //TABS
        $oTabs = $this->build_list_tabs();
        //OPER BUTTONS
        $oOpButtons = $this->build_listoperation_buttons();
        //JAVASCRIPT
        $oJavascript = new HelperJavascript();
        $oJavascript->set_filters($this->get_filter_controls_id());
        $oJavascript->set_focusid("id_all");
        //VIEW SET
        $this->oView->add_var($oScrumbs,"oScrumbs");
        $this->oView->add_var($oTabs,"oTabs");
        $this->oView->add_var($oOpButtons,"oOpButtons");
        $this->oView->add_var($oAlert,"oAlert");
        $this->oView->add_var($oJavascript,"oJavascript");
        $this->oView->add_var($oTableList,"oTableList");
        $this->oView->add_var($oForm,"oForm");
        $this->oView->set_path_view("transfers/view_index");
        $this->oView->show_page();
    }//get_list()
    //</editor-fold>

    //<editor-fold defaultstate="collapsed" desc="INSERT">
    //insert_1
    protected function build_insert_scrumbs()
    {
        $arLinks = array();
        $sUrlLink = $this->build_url($this->sModuleName);
        $arLinks["list"]=array("href"=>$sUrlLink,"innerhtml"=>tr_ts_entities);
        $sUrlLink = $this->build_url($this->sModuleName,NULL,"insert");
        $arLinks["insert"]=array("href"=>$sUrlLink,"innerhtml"=>tr_ts_entity_insert);
        $oScrumbs = new AppHelperBreadscrumbs($arLinks);
        return $oScrumbs;
    }//build_insert_scrumbs()

    //insert_2
    protected function build_insert_tabs()
    {
        $arTabs = array();
        //$sUrlTab = $this->build_url($this->sModuleName,NULL,"insert");
        //$arTabs["insert1"]=array("href"=>$sUrlTab,"innerhtml"=>tr_ts_instabs_1);
        //$sUrlTab = $this->build_url($this->sModuleName,NULL,"insert2");
        //$arTabs["insert2"]=array("href"=>$sUrlTab,"innerhtml"=>tr_ts_instabs_2);
        $oTabs = new AppHelperHeadertabs($arTabs,"insert1");
        return $oTabs;
    }//build_insert_tabs()
    //insert_3
    protected function build_insert_opbuttons()
    {
        $arOpButtons = array();
        $arOpButtons["list"] = array("href"=>$this->build_url($this->sModuleName),"icon"=>"awe-search","innerhtml"=>tr_ts_insopbutton_list);
        //$arOpButtons["extra"] = array("href"=>$this->build_ur(),"icon"=>"awe-xxxx","innerhtml"=>tr_ts_insopbutton_extra1);
        $oOpButtons = new AppHelperButtontabs(tr_ts_entities);
        $oOpButtons->set_tabs($arOpButtons);
        return $oOpButtons;
    }//build_insert_opbuttons()

    //insert_4
    protected function build_insert_fields($usePost=0)
    {
        $arFields = array(); $oAuxField = NULL; $oAuxLabel = NULL;
        $arFields[]= new AppHelperFormhead(tr_ts_entity_new);
        //gridgiro
        $oAuxField = new HelperInputText("txtGridgiro","txtGridgiro");
        $oAuxField->set_value(0);
        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtGridgiro")));
        $oAuxLabel = new HelperLabel("txtGridgiro",tr_ts_ins_gridgiro,"lblGridgiro");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grsecueo
        $oAuxField = new HelperInputText("txtGrsecueo","txtGrsecueo");
        $oAuxField->set_value(0);
        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtGrsecueo")));
        $oAuxLabel = new HelperLabel("txtGrsecueo",tr_ts_ins_grsecueo,"lblGrsecueo");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //gridremi
        $oAuxField = new HelperInputText("txtGridremi","txtGridremi");
        $oAuxField->set_value(0);
        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtGridremi")));
        $oAuxLabel = new HelperLabel("txtGridremi",tr_ts_ins_gridremi,"lblGridremi");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grsecuen
        $oAuxField = new HelperInputText("txtGrsecuen","txtGrsecuen");
        $oAuxField->set_value(0);
        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtGrsecuen")));
        $oAuxLabel = new HelperLabel("txtGrsecuen",tr_ts_ins_grsecuen,"lblGrsecuen");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //gridbene
        $oAuxField = new HelperInputText("txtGridbene","txtGridbene");
        $oAuxField->set_value(0);
        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtGridbene")));
        $oAuxLabel = new HelperLabel("txtGridbene",tr_ts_ins_gridbene,"lblGridbene");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grfactur
        $oAuxField = new HelperInputText("txtGrfactur","txtGrfactur");
        $oAuxField->set_value(0);
        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtGrfactur")));
        $oAuxLabel = new HelperLabel("txtGrfactur",tr_ts_ins_grfactur,"lblGrfactur");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grcdcart
        $oAuxField = new HelperInputText("txtGrcdcart","txtGrcdcart");
        $oAuxField->set_value(0);
        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtGrcdcart")));
        $oAuxLabel = new HelperLabel("txtGrcdcart",tr_ts_ins_grcdcart,"lblGrcdcart");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grcdcont
        $oAuxField = new HelperInputText("txtGrcdcont","txtGrcdcont");
        $oAuxField->set_value(0);
        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtGrcdcont")));
        $oAuxLabel = new HelperLabel("txtGrcdcont",tr_ts_ins_grcdcont,"lblGrcdcont");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grnrolla
        $oAuxField = new HelperInputText("txtGrnrolla","txtGrnrolla");
        $oAuxField->set_value(0);
        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtGrnrolla")));
        $oAuxLabel = new HelperLabel("txtGrnrolla",tr_ts_ins_grnrolla,"lblGrnrolla");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grconsag
        $oAuxField = new HelperInputText("txtGrconsag","txtGrconsag");
        $oAuxField->set_value(0);
        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtGrconsag")));
        $oAuxLabel = new HelperLabel("txtGrconsag",tr_ts_ins_grconsag,"lblGrconsag");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grrembol
        $oAuxField = new HelperInputText("txtGrrembol","txtGrrembol");
        $oAuxField->set_value(0);
        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtGrrembol")));
        $oAuxLabel = new HelperLabel("txtGrrembol",tr_ts_ins_grrembol,"lblGrrembol");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grfechag
        $oAuxField = new HelperInputText("txtGrfechag","txtGrfechag");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrfechag"));
        $oAuxLabel = new HelperLabel("txtGrfechag",tr_ts_ins_grfechag,"lblGrfechag");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grfeccon
        $oAuxField = new HelperInputText("txtGrfeccon","txtGrfeccon");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrfeccon"));
        $oAuxLabel = new HelperLabel("txtGrfeccon",tr_ts_ins_grfeccon,"lblGrfeccon");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grfecing
        $oAuxField = new HelperInputText("txtGrfecing","txtGrfecing");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrfecing"));
        $oAuxLabel = new HelperLabel("txtGrfecing",tr_ts_ins_grfecing,"lblGrfecing");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grfecmod
        $oAuxField = new HelperInputText("txtGrfecmod","txtGrfecmod");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrfecmod"));
        $oAuxLabel = new HelperLabel("txtGrfecmod",tr_ts_ins_grfecmod,"lblGrfecmod");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grfecall
        $oAuxField = new HelperInputText("txtGrfecall","txtGrfecall");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrfecall"));
        $oAuxLabel = new HelperLabel("txtGrfecall",tr_ts_ins_grfecall,"lblGrfecall");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grconpag
        $oAuxField = new HelperInputText("txtGrconpag","txtGrconpag");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrconpag"));
        $oAuxLabel = new HelperLabel("txtGrconpag",tr_ts_ins_grconpag,"lblGrconpag");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grmontod
        $oAuxField = new HelperInputText("txtGrmontod","txtGrmontod");
        $oAuxField->set_value("0.00");
        if($usePost) $oAuxField->set_value(dbbo_numeric2($this->get_post("txtGrmontod")));
        $oAuxLabel = new HelperLabel("txtGrmontod",tr_ts_ins_grmontod,"lblGrmontod");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grmonusd
        $oAuxField = new HelperInputText("txtGrmonusd","txtGrmonusd");
        $oAuxField->set_value("0.00");
        if($usePost) $oAuxField->set_value(dbbo_numeric2($this->get_post("txtGrmonusd")));
        $oAuxLabel = new HelperLabel("txtGrmonusd",tr_ts_ins_grmonusd,"lblGrmonusd");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grtasacm
        $oAuxField = new HelperInputText("txtGrtasacm","txtGrtasacm");
        $oAuxField->set_value("0.00");
        if($usePost) $oAuxField->set_value(dbbo_numeric2($this->get_post("txtGrtasacm")));
        $oAuxLabel = new HelperLabel("txtGrtasacm",tr_ts_ins_grtasacm,"lblGrtasacm");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grvlrcom
        $oAuxField = new HelperInputText("txtGrvlrcom","txtGrvlrcom");
        $oAuxField->set_value("0.00");
        if($usePost) $oAuxField->set_value(dbbo_numeric2($this->get_post("txtGrvlrcom")));
        $oAuxLabel = new HelperLabel("txtGrvlrcom",tr_ts_ins_grvlrcom,"lblGrvlrcom");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grvlrajs
        $oAuxField = new HelperInputText("txtGrvlrajs","txtGrvlrajs");
        $oAuxField->set_value("0.00");
        if($usePost) $oAuxField->set_value(dbbo_numeric2($this->get_post("txtGrvlrajs")));
        $oAuxLabel = new HelperLabel("txtGrvlrajs",tr_ts_ins_grvlrajs,"lblGrvlrajs");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grmontop
        $oAuxField = new HelperInputText("txtGrmontop","txtGrmontop");
        $oAuxField->set_value("0.00");
        if($usePost) $oAuxField->set_value(dbbo_numeric2($this->get_post("txtGrmontop")));
        $oAuxLabel = new HelperLabel("txtGrmontop",tr_ts_ins_grmontop,"lblGrmontop");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grvlrdes
        $oAuxField = new HelperInputText("txtGrvlrdes","txtGrvlrdes");
        $oAuxField->set_value("0.00");
        if($usePost) $oAuxField->set_value(dbbo_numeric2($this->get_post("txtGrvlrdes")));
        $oAuxLabel = new HelperLabel("txtGrvlrdes",tr_ts_ins_grvlrdes,"lblGrvlrdes");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grtipgir
        $oAuxField = new HelperInputText("txtGrtipgir","txtGrtipgir");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrtipgir"));
        $oAuxLabel = new HelperLabel("txtGrtipgir",tr_ts_ins_grtipgir,"lblGrtipgir");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grcdcoro
        $oAuxField = new HelperInputText("txtGrcdcoro","txtGrcdcoro");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrcdcoro"));
        $oAuxLabel = new HelperLabel("txtGrcdcoro",tr_ts_ins_grcdcoro,"lblGrcdcoro");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grnorden
        $oAuxField = new HelperInputText("txtGrnorden","txtGrnorden");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrnorden"));
        $oAuxLabel = new HelperLabel("txtGrnorden",tr_ts_ins_grnorden,"lblGrnorden");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grcdagen
        $oAuxField = new HelperInputText("txtGrcdagen","txtGrcdagen");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrcdagen"));
        $oAuxLabel = new HelperLabel("txtGrcdagen",tr_ts_ins_grcdagen,"lblGrcdagen");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grcdpais
        $oAuxField = new HelperInputText("txtGrcdpais","txtGrcdpais");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrcdpais"));
        $oAuxLabel = new HelperLabel("txtGrcdpais",tr_ts_ins_grcdpais,"lblGrcdpais");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grcdciud
        $oAuxField = new HelperInputText("txtGrcdciud","txtGrcdciud");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrcdciud"));
        $oAuxLabel = new HelperLabel("txtGrcdciud",tr_ts_ins_grcdciud,"lblGrcdciud");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grtipenv
        $oAuxField = new HelperInputText("txtGrtipenv","txtGrtipenv");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrtipenv"));
        $oAuxLabel = new HelperLabel("txtGrtipenv",tr_ts_ins_grtipenv,"lblGrtipenv");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //gritemid
        $oAuxField = new HelperInputText("txtGritemid","txtGritemid");
        if($usePost) $oAuxField->set_value($this->get_post("txtGritemid"));
        $oAuxLabel = new HelperLabel("txtGritemid",tr_ts_ins_gritemid,"lblGritemid");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //gritdesc
        $oAuxField = new HelperInputText("txtGritdesc","txtGritdesc");
        if($usePost) $oAuxField->set_value($this->get_post("txtGritdesc"));
        $oAuxLabel = new HelperLabel("txtGritdesc",tr_ts_ins_gritdesc,"lblGritdesc");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grrelaci
        $oAuxField = new HelperInputText("txtGrrelaci","txtGrrelaci");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrrelaci"));
        $oAuxLabel = new HelperLabel("txtGrrelaci",tr_ts_ins_grrelaci,"lblGrrelaci");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grestciv
        $oAuxField = new HelperInputText("txtGrestciv","txtGrestciv");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrestciv"));
        $oAuxLabel = new HelperLabel("txtGrestciv",tr_ts_ins_grestciv,"lblGrestciv");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grniving
        $oAuxField = new HelperInputText("txtGrniving","txtGrniving");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrniving"));
        $oAuxLabel = new HelperLabel("txtGrniving",tr_ts_ins_grniving,"lblGrniving");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //gridsexo
        $oAuxField = new HelperInputText("txtGridsexo","txtGridsexo");
        if($usePost) $oAuxField->set_value($this->get_post("txtGridsexo"));
        $oAuxLabel = new HelperLabel("txtGridsexo",tr_ts_ins_gridsexo,"lblGridsexo");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grcodnac
        $oAuxField = new HelperInputText("txtGrcodnac","txtGrcodnac");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrcodnac"));
        $oAuxLabel = new HelperLabel("txtGrcodnac",tr_ts_ins_grcodnac,"lblGrcodnac");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grtrabaj
        $oAuxField = new HelperInputText("txtGrtrabaj","txtGrtrabaj");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrtrabaj"));
        $oAuxLabel = new HelperLabel("txtGrtrabaj",tr_ts_ins_grtrabaj,"lblGrtrabaj");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grapsrem
        $oAuxField = new HelperInputText("txtGrapsrem","txtGrapsrem");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrapsrem"));
        $oAuxLabel = new HelperLabel("txtGrapsrem",tr_ts_ins_grapsrem,"lblGrapsrem");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grident
        $oAuxField = new HelperInputText("txtGrident","txtGrident");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrident"));
        $oAuxLabel = new HelperLabel("txtGrident",tr_ts_ins_grident,"lblGrident");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grcodcta
        $oAuxField = new HelperInputText("txtGrcodcta","txtGrcodcta");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrcodcta"));
        $oAuxLabel = new HelperLabel("txtGrcodcta",tr_ts_ins_grcodcta,"lblGrcodcta");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grdocpag
        $oAuxField = new HelperInputText("txtGrdocpag","txtGrdocpag");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrdocpag"));
        $oAuxLabel = new HelperLabel("txtGrdocpag",tr_ts_ins_grdocpag,"lblGrdocpag");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grnompag
        $oAuxField = new HelperInputText("txtGrnompag","txtGrnompag");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrnompag"));
        $oAuxLabel = new HelperLabel("txtGrnompag",tr_ts_ins_grnompag,"lblGrnompag");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grusauto
        $oAuxField = new HelperInputText("txtGrusauto","txtGrusauto");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrusauto"));
        $oAuxLabel = new HelperLabel("txtGrusauto",tr_ts_ins_grusauto,"lblGrusauto");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grtiptas
        $oAuxField = new HelperInputText("txtGrtiptas","txtGrtiptas");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrtiptas"));
        $oAuxLabel = new HelperLabel("txtGrtiptas",tr_ts_ins_grtiptas,"lblGrtiptas");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grnomcli
        $oAuxField = new HelperInputText("txtGrnomcli","txtGrnomcli");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrnomcli"));
        $oAuxLabel = new HelperLabel("txtGrnomcli",tr_ts_ins_grnomcli,"lblGrnomcli");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grapecli
        $oAuxField = new HelperInputText("txtGrapecli","txtGrapecli");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrapecli"));
        $oAuxLabel = new HelperLabel("txtGrapecli",tr_ts_ins_grapecli,"lblGrapecli");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grsapcli
        $oAuxField = new HelperInputText("txtGrsapcli","txtGrsapcli");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrsapcli"));
        $oAuxLabel = new HelperLabel("txtGrsapcli",tr_ts_ins_grsapcli,"lblGrsapcli");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grofiscu
        $oAuxField = new HelperInputText("txtGrofiscu","txtGrofiscu");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrofiscu"));
        $oAuxLabel = new HelperLabel("txtGrofiscu",tr_ts_ins_grofiscu,"lblGrofiscu");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grnmsrem
        $oAuxField = new HelperInputText("txtGrnmsrem","txtGrnmsrem");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrnmsrem"));
        $oAuxLabel = new HelperLabel("txtGrnmsrem",tr_ts_ins_grnmsrem,"lblGrnmsrem");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grfecenv
        $oAuxField = new HelperInputText("txtGrfecenv","txtGrfecenv");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrfecenv"));
        $oAuxLabel = new HelperLabel("txtGrfecenv",tr_ts_ins_grfecenv,"lblGrfecenv");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grhorenv
        $oAuxField = new HelperInputText("txtGrhorenv","txtGrhorenv");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrhorenv"));
        $oAuxLabel = new HelperLabel("txtGrhorenv",tr_ts_ins_grhorenv,"lblGrhorenv");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grtipreg
        $oAuxField = new HelperInputText("txtGrtipreg","txtGrtipreg");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrtipreg"));
        $oAuxLabel = new HelperLabel("txtGrtipreg",tr_ts_ins_grtipreg,"lblGrtipreg");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grcdcall
        $oAuxField = new HelperInputText("txtGrcdcall","txtGrcdcall");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrcdcall"));
        $oAuxLabel = new HelperLabel("txtGrcdcall",tr_ts_ins_grcdcall,"lblGrcdcall");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grpercon
        $oAuxField = new HelperInputText("txtGrpercon","txtGrpercon");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrpercon"));
        $oAuxLabel = new HelperLabel("txtGrpercon",tr_ts_ins_grpercon,"lblGrpercon");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grgirenv
        $oAuxField = new HelperInputText("txtGrgirenv","txtGrgirenv");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrgirenv"));
        $oAuxLabel = new HelperLabel("txtGrgirenv",tr_ts_ins_grgirenv,"lblGrgirenv");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grclave
        $oAuxField = new HelperInputText("txtGrclave","txtGrclave");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrclave"));
        $oAuxLabel = new HelperLabel("txtGrclave",tr_ts_ins_grclave,"lblGrclave");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grcorpri
        $oAuxField = new HelperInputText("txtGrcorpri","txtGrcorpri");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrcorpri"));
        $oAuxLabel = new HelperLabel("txtGrcorpri",tr_ts_ins_grcorpri,"lblGrcorpri");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grmarcad
        $oAuxField = new HelperInputText("txtGrmarcad","txtGrmarcad");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrmarcad"));
        $oAuxLabel = new HelperLabel("txtGrmarcad",tr_ts_ins_grmarcad,"lblGrmarcad");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grmoncta
        $oAuxField = new HelperInputText("txtGrmoncta","txtGrmoncta");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrmoncta"));
        $oAuxLabel = new HelperLabel("txtGrmoncta",tr_ts_ins_grmoncta,"lblGrmoncta");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grnorde2
        $oAuxField = new HelperInputText("txtGrnorde2","txtGrnorde2");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrnorde2"));
        $oAuxLabel = new HelperLabel("txtGrnorde2",tr_ts_ins_grnorde2,"lblGrnorde2");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grpagcor
        $oAuxField = new HelperInputText("txtGrpagcor","txtGrpagcor");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrpagcor"));
        $oAuxLabel = new HelperLabel("txtGrpagcor",tr_ts_ins_grpagcor,"lblGrpagcor");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grmonpag
        $oAuxField = new HelperInputText("txtGrmonpag","txtGrmonpag");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrmonpag"));
        $oAuxLabel = new HelperLabel("txtGrmonpag",tr_ts_ins_grmonpag,"lblGrmonpag");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grcdtran
        $oAuxField = new HelperInputText("txtGrcdtran","txtGrcdtran");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrcdtran"));
        $oAuxLabel = new HelperLabel("txtGrcdtran",tr_ts_ins_grcdtran,"lblGrcdtran");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grtipcon
        $oAuxField = new HelperInputText("txtGrtipcon","txtGrtipcon");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrtipcon"));
        $oAuxLabel = new HelperLabel("txtGrtipcon",tr_ts_ins_grtipcon,"lblGrtipcon");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grestado
        $oAuxField = new HelperInputText("txtGrestado","txtGrestado");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrestado"));
        $oAuxLabel = new HelperLabel("txtGrestado",tr_ts_ins_grestado,"lblGrestado");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grcausae
        $oAuxField = new HelperInputText("txtGrcausae","txtGrcausae");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrcausae"));
        $oAuxLabel = new HelperLabel("txtGrcausae",tr_ts_ins_grcausae,"lblGrcausae");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grcduser
        $oAuxField = new HelperInputText("txtGrcduser","txtGrcduser");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrcduser"));
        $oAuxLabel = new HelperLabel("txtGrcduser",tr_ts_ins_grcduser,"lblGrcduser");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grtippag
        $oAuxField = new HelperInputText("txtGrtippag","txtGrtippag");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrtippag"));
        $oAuxLabel = new HelperLabel("txtGrtippag",tr_ts_ins_grtippag,"lblGrtippag");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grcdbanc
        $oAuxField = new HelperInputText("txtGrcdbanc","txtGrcdbanc");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrcdbanc"));
        $oAuxLabel = new HelperLabel("txtGrcdbanc",tr_ts_ins_grcdbanc,"lblGrcdbanc");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grtipcta
        $oAuxField = new HelperInputText("txtGrtipcta","txtGrtipcta");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrtipcta"));
        $oAuxLabel = new HelperLabel("txtGrtipcta",tr_ts_ins_grtipcta,"lblGrtipcta");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grnrocta
        $oAuxField = new HelperInputText("txtGrnrocta","txtGrnrocta");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrnrocta"));
        $oAuxLabel = new HelperLabel("txtGrnrocta",tr_ts_ins_grnrocta,"lblGrnrocta");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grobserv
        $oAuxField = new HelperInputText("txtGrobserv","txtGrobserv");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrobserv"));
        $oAuxLabel = new HelperLabel("txtGrobserv",tr_ts_ins_grobserv,"lblGrobserv");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grmonori
        $oAuxField = new HelperInputText("txtGrmonori","txtGrmonori");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrmonori"));
        $oAuxLabel = new HelperLabel("txtGrmonori",tr_ts_ins_grmonori,"lblGrmonori");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grdrbene
        $oAuxField = new HelperInputText("txtGrdrbene","txtGrdrbene");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrdrbene"));
        $oAuxLabel = new HelperLabel("txtGrdrbene",tr_ts_ins_grdrbene,"lblGrdrbene");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grdrben2
        $oAuxField = new HelperInputText("txtGrdrben2","txtGrdrben2");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrdrben2"));
        $oAuxLabel = new HelperLabel("txtGrdrben2",tr_ts_ins_grdrben2,"lblGrdrben2");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grembene
        $oAuxField = new HelperInputText("txtGrembene","txtGrembene");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrembene"));
        $oAuxLabel = new HelperLabel("txtGrembene",tr_ts_ins_grembene,"lblGrembene");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grtlbene
        $oAuxField = new HelperInputText("txtGrtlbene","txtGrtlbene");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrtlbene"));
        $oAuxLabel = new HelperLabel("txtGrtlbene",tr_ts_ins_grtlbene,"lblGrtlbene");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grtlben2
        $oAuxField = new HelperInputText("txtGrtlben2","txtGrtlben2");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrtlben2"));
        $oAuxLabel = new HelperLabel("txtGrtlben2",tr_ts_ins_grtlben2,"lblGrtlben2");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grmensaj
        $oAuxField = new HelperInputText("txtGrmensaj","txtGrmensaj");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrmensaj"));
        $oAuxLabel = new HelperLabel("txtGrmensaj",tr_ts_ins_grmensaj,"lblGrmensaj");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grciudds
        $oAuxField = new HelperInputText("txtGrciudds","txtGrciudds");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrciudds"));
        $oAuxLabel = new HelperLabel("txtGrciudds",tr_ts_ins_grciudds,"lblGrciudds");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grcdcorr
        $oAuxField = new HelperInputText("txtGrcdcorr","txtGrcdcorr");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrcdcorr"));
        $oAuxLabel = new HelperLabel("txtGrcdcorr",tr_ts_ins_grcdcorr,"lblGrcdcorr");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //gragends
        $oAuxField = new HelperInputText("txtGragends","txtGragends");
        if($usePost) $oAuxField->set_value($this->get_post("txtGragends"));
        $oAuxLabel = new HelperLabel("txtGragends",tr_ts_ins_gragends,"lblGragends");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grtdbene
        $oAuxField = new HelperInputText("txtGrtdbene","txtGrtdbene");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrtdbene"));
        $oAuxLabel = new HelperLabel("txtGrtdbene",tr_ts_ins_grtdbene,"lblGrtdbene");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grcdbene
        $oAuxField = new HelperInputText("txtGrcdbene","txtGrcdbene");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrcdbene"));
        $oAuxLabel = new HelperLabel("txtGrcdbene",tr_ts_ins_grcdbene,"lblGrcdbene");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grnmbene
        $oAuxField = new HelperInputText("txtGrnmbene","txtGrnmbene");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrnmbene"));
        $oAuxLabel = new HelperLabel("txtGrnmbene",tr_ts_ins_grnmbene,"lblGrnmbene");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grciudor
        $oAuxField = new HelperInputText("txtGrciudor","txtGrciudor");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrciudor"));
        $oAuxLabel = new HelperLabel("txtGrciudor",tr_ts_ins_grciudor,"lblGrciudor");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //gremremi
        $oAuxField = new HelperInputText("txtGremremi","txtGremremi");
        if($usePost) $oAuxField->set_value($this->get_post("txtGremremi"));
        $oAuxLabel = new HelperLabel("txtGremremi",tr_ts_ins_gremremi,"lblGremremi");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grcdocup
        $oAuxField = new HelperInputText("txtGrcdocup","txtGrcdocup");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrcdocup"));
        $oAuxLabel = new HelperLabel("txtGrcdocup",tr_ts_ins_grcdocup,"lblGrcdocup");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grtlremi
        $oAuxField = new HelperInputText("txtGrtlremi","txtGrtlremi");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrtlremi"));
        $oAuxLabel = new HelperLabel("txtGrtlremi",tr_ts_ins_grtlremi,"lblGrtlremi");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grtlrem2
        $oAuxField = new HelperInputText("txtGrtlrem2","txtGrtlrem2");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrtlrem2"));
        $oAuxLabel = new HelperLabel("txtGrtlrem2",tr_ts_ins_grtlrem2,"lblGrtlrem2");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grpaisds
        $oAuxField = new HelperInputText("txtGrpaisds","txtGrpaisds");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrpaisds"));
        $oAuxLabel = new HelperLabel("txtGrpaisds",tr_ts_ins_grpaisds,"lblGrpaisds");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grusmodi
        $oAuxField = new HelperInputText("txtGrusmodi","txtGrusmodi");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrusmodi"));
        $oAuxLabel = new HelperLabel("txtGrusmodi",tr_ts_ins_grusmodi,"lblGrusmodi");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grtdremi
        $oAuxField = new HelperInputText("txtGrtdremi","txtGrtdremi");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrtdremi"));
        $oAuxLabel = new HelperLabel("txtGrtdremi",tr_ts_ins_grtdremi,"lblGrtdremi");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grcdremi
        $oAuxField = new HelperInputText("txtGrcdremi","txtGrcdremi");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrcdremi"));
        $oAuxLabel = new HelperLabel("txtGrcdremi",tr_ts_ins_grcdremi,"lblGrcdremi");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grnmremi
        $oAuxField = new HelperInputText("txtGrnmremi","txtGrnmremi");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrnmremi"));
        $oAuxLabel = new HelperLabel("txtGrnmremi",tr_ts_ins_grnmremi,"lblGrnmremi");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grdrremi
        $oAuxField = new HelperInputText("txtGrdrremi","txtGrdrremi");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrdrremi"));
        $oAuxLabel = new HelperLabel("txtGrdrremi",tr_ts_ins_grdrremi,"lblGrdrremi");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grdrrem2
        $oAuxField = new HelperInputText("txtGrdrrem2","txtGrdrrem2");
        if($usePost) $oAuxField->set_value($this->get_post("txtGrdrrem2"));
        $oAuxLabel = new HelperLabel("txtGrdrrem2",tr_ts_ins_grdrrem2,"lblGrdrrem2");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //SAVE BUTTON
        $oAuxField = new HelperButtonBasic("butSave",tr_ts_ins_savebutton);
        $oAuxField->add_class("btn btn-primary");
        $oAuxField->set_js_onclick("insert();");
        $arFields[] = new ApphelperFormactions(array($oAuxField));
        //POST INFO
        $oAuxField = new HelperInputHidden("hidAction","hidAction");
        $arFields[] = $oAuxField;
        $oAuxField = new HelperInputHidden("hidPostback","hidPostback");
        $arFields[] = $oAuxField;
        return $arFields;
    }//build_insert_fields()

    //insert_5
    protected function get_insert_validate()
    {
        $arFieldsConfig = array();
        $arFieldsConfig["gridgiro"] = array("controlid"=>"txtGridgiro","label"=>tr_ts_ins_gridgiro,"length"=>4,"type"=>array());
        $arFieldsConfig["grsecueo"] = array("controlid"=>"txtGrsecueo","label"=>tr_ts_ins_grsecueo,"length"=>4,"type"=>array());
        $arFieldsConfig["gridremi"] = array("controlid"=>"txtGridremi","label"=>tr_ts_ins_gridremi,"length"=>4,"type"=>array());
        $arFieldsConfig["grsecuen"] = array("controlid"=>"txtGrsecuen","label"=>tr_ts_ins_grsecuen,"length"=>4,"type"=>array());
        $arFieldsConfig["gridbene"] = array("controlid"=>"txtGridbene","label"=>tr_ts_ins_gridbene,"length"=>4,"type"=>array());
        $arFieldsConfig["grfactur"] = array("controlid"=>"txtGrfactur","label"=>tr_ts_ins_grfactur,"length"=>4,"type"=>array());
        $arFieldsConfig["grcdcart"] = array("controlid"=>"txtGrcdcart","label"=>tr_ts_ins_grcdcart,"length"=>4,"type"=>array());
        $arFieldsConfig["grcdcont"] = array("controlid"=>"txtGrcdcont","label"=>tr_ts_ins_grcdcont,"length"=>4,"type"=>array());
        $arFieldsConfig["grnrolla"] = array("controlid"=>"txtGrnrolla","label"=>tr_ts_ins_grnrolla,"length"=>4,"type"=>array());
        $arFieldsConfig["grconsag"] = array("controlid"=>"txtGrconsag","label"=>tr_ts_ins_grconsag,"length"=>4,"type"=>array());
        $arFieldsConfig["grrembol"] = array("controlid"=>"txtGrrembol","label"=>tr_ts_ins_grrembol,"length"=>4,"type"=>array());
        $arFieldsConfig["grfechag"] = array("controlid"=>"txtGrfechag","label"=>tr_ts_ins_grfechag,"length"=>8,"type"=>array());
        $arFieldsConfig["grfeccon"] = array("controlid"=>"txtGrfeccon","label"=>tr_ts_ins_grfeccon,"length"=>8,"type"=>array());
        $arFieldsConfig["grfecing"] = array("controlid"=>"txtGrfecing","label"=>tr_ts_ins_grfecing,"length"=>8,"type"=>array());
        $arFieldsConfig["grfecmod"] = array("controlid"=>"txtGrfecmod","label"=>tr_ts_ins_grfecmod,"length"=>8,"type"=>array());
        $arFieldsConfig["grfecall"] = array("controlid"=>"txtGrfecall","label"=>tr_ts_ins_grfecall,"length"=>8,"type"=>array());
        $arFieldsConfig["grconpag"] = array("controlid"=>"txtGrconpag","label"=>tr_ts_ins_grconpag,"length"=>8,"type"=>array());
        $arFieldsConfig["grmontod"] = array("controlid"=>"txtGrmontod","label"=>tr_ts_ins_grmontod,"length"=>9,"type"=>array("numeric"));
        $arFieldsConfig["grmonusd"] = array("controlid"=>"txtGrmonusd","label"=>tr_ts_ins_grmonusd,"length"=>9,"type"=>array("numeric"));
        $arFieldsConfig["grtasacm"] = array("controlid"=>"txtGrtasacm","label"=>tr_ts_ins_grtasacm,"length"=>9,"type"=>array("numeric"));
        $arFieldsConfig["grvlrcom"] = array("controlid"=>"txtGrvlrcom","label"=>tr_ts_ins_grvlrcom,"length"=>9,"type"=>array("numeric"));
        $arFieldsConfig["grvlrajs"] = array("controlid"=>"txtGrvlrajs","label"=>tr_ts_ins_grvlrajs,"length"=>9,"type"=>array("numeric"));
        $arFieldsConfig["grmontop"] = array("controlid"=>"txtGrmontop","label"=>tr_ts_ins_grmontop,"length"=>9,"type"=>array("numeric"));
        $arFieldsConfig["grvlrdes"] = array("controlid"=>"txtGrvlrdes","label"=>tr_ts_ins_grvlrdes,"length"=>9,"type"=>array("numeric"));
        $arFieldsConfig["grtipgir"] = array("controlid"=>"txtGrtipgir","label"=>tr_ts_ins_grtipgir,"length"=>1,"type"=>array());
        $arFieldsConfig["grcdcoro"] = array("controlid"=>"txtGrcdcoro","label"=>tr_ts_ins_grcdcoro,"length"=>6,"type"=>array());
        $arFieldsConfig["grnorden"] = array("controlid"=>"txtGrnorden","label"=>tr_ts_ins_grnorden,"length"=>20,"type"=>array());
        $arFieldsConfig["grcdagen"] = array("controlid"=>"txtGrcdagen","label"=>tr_ts_ins_grcdagen,"length"=>6,"type"=>array());
        $arFieldsConfig["grcdpais"] = array("controlid"=>"txtGrcdpais","label"=>tr_ts_ins_grcdpais,"length"=>3,"type"=>array());
        $arFieldsConfig["grcdciud"] = array("controlid"=>"txtGrcdciud","label"=>tr_ts_ins_grcdciud,"length"=>6,"type"=>array());
        $arFieldsConfig["grtipenv"] = array("controlid"=>"txtGrtipenv","label"=>tr_ts_ins_grtipenv,"length"=>1,"type"=>array());
        $arFieldsConfig["gritemid"] = array("controlid"=>"txtGritemid","label"=>tr_ts_ins_gritemid,"length"=>20,"type"=>array());
        $arFieldsConfig["gritdesc"] = array("controlid"=>"txtGritdesc","label"=>tr_ts_ins_gritdesc,"length"=>60,"type"=>array());
        $arFieldsConfig["grrelaci"] = array("controlid"=>"txtGrrelaci","label"=>tr_ts_ins_grrelaci,"length"=>1,"type"=>array());
        $arFieldsConfig["grestciv"] = array("controlid"=>"txtGrestciv","label"=>tr_ts_ins_grestciv,"length"=>3,"type"=>array());
        $arFieldsConfig["grniving"] = array("controlid"=>"txtGrniving","label"=>tr_ts_ins_grniving,"length"=>3,"type"=>array());
        $arFieldsConfig["gridsexo"] = array("controlid"=>"txtGridsexo","label"=>tr_ts_ins_gridsexo,"length"=>1,"type"=>array());
        $arFieldsConfig["grcodnac"] = array("controlid"=>"txtGrcodnac","label"=>tr_ts_ins_grcodnac,"length"=>6,"type"=>array());
        $arFieldsConfig["grtrabaj"] = array("controlid"=>"txtGrtrabaj","label"=>tr_ts_ins_grtrabaj,"length"=>40,"type"=>array());
        $arFieldsConfig["grapsrem"] = array("controlid"=>"txtGrapsrem","label"=>tr_ts_ins_grapsrem,"length"=>60,"type"=>array());
        $arFieldsConfig["grident"] = array("controlid"=>"txtGrident","label"=>tr_ts_ins_grident,"length"=>15,"type"=>array());
        $arFieldsConfig["grcodcta"] = array("controlid"=>"txtGrcodcta","label"=>tr_ts_ins_grcodcta,"length"=>20,"type"=>array());
        $arFieldsConfig["grdocpag"] = array("controlid"=>"txtGrdocpag","label"=>tr_ts_ins_grdocpag,"length"=>50,"type"=>array());
        $arFieldsConfig["grnompag"] = array("controlid"=>"txtGrnompag","label"=>tr_ts_ins_grnompag,"length"=>60,"type"=>array());
        $arFieldsConfig["grusauto"] = array("controlid"=>"txtGrusauto","label"=>tr_ts_ins_grusauto,"length"=>12,"type"=>array());
        $arFieldsConfig["grtiptas"] = array("controlid"=>"txtGrtiptas","label"=>tr_ts_ins_grtiptas,"length"=>10,"type"=>array());
        $arFieldsConfig["grnomcli"] = array("controlid"=>"txtGrnomcli","label"=>tr_ts_ins_grnomcli,"length"=>60,"type"=>array());
        $arFieldsConfig["grapecli"] = array("controlid"=>"txtGrapecli","label"=>tr_ts_ins_grapecli,"length"=>60,"type"=>array());
        $arFieldsConfig["grsapcli"] = array("controlid"=>"txtGrsapcli","label"=>tr_ts_ins_grsapcli,"length"=>50,"type"=>array());
        $arFieldsConfig["grofiscu"] = array("controlid"=>"txtGrofiscu","label"=>tr_ts_ins_grofiscu,"length"=>6,"type"=>array());
        $arFieldsConfig["grnmsrem"] = array("controlid"=>"txtGrnmsrem","label"=>tr_ts_ins_grnmsrem,"length"=>60,"type"=>array());
        $arFieldsConfig["grfecenv"] = array("controlid"=>"txtGrfecenv","label"=>tr_ts_ins_grfecenv,"length"=>10,"type"=>array());
        $arFieldsConfig["grhorenv"] = array("controlid"=>"txtGrhorenv","label"=>tr_ts_ins_grhorenv,"length"=>10,"type"=>array());
        $arFieldsConfig["grtipreg"] = array("controlid"=>"txtGrtipreg","label"=>tr_ts_ins_grtipreg,"length"=>3,"type"=>array());
        $arFieldsConfig["grcdcall"] = array("controlid"=>"txtGrcdcall","label"=>tr_ts_ins_grcdcall,"length"=>12,"type"=>array());
        $arFieldsConfig["grpercon"] = array("controlid"=>"txtGrpercon","label"=>tr_ts_ins_grpercon,"length"=>120,"type"=>array());
        $arFieldsConfig["grgirenv"] = array("controlid"=>"txtGrgirenv","label"=>tr_ts_ins_grgirenv,"length"=>1,"type"=>array());
        $arFieldsConfig["grclave"] = array("controlid"=>"txtGrclave","label"=>tr_ts_ins_grclave,"length"=>15,"type"=>array());
        $arFieldsConfig["grcorpri"] = array("controlid"=>"txtGrcorpri","label"=>tr_ts_ins_grcorpri,"length"=>10,"type"=>array());
        $arFieldsConfig["grmarcad"] = array("controlid"=>"txtGrmarcad","label"=>tr_ts_ins_grmarcad,"length"=>1,"type"=>array());
        $arFieldsConfig["grmoncta"] = array("controlid"=>"txtGrmoncta","label"=>tr_ts_ins_grmoncta,"length"=>3,"type"=>array());
        $arFieldsConfig["grnorde2"] = array("controlid"=>"txtGrnorde2","label"=>tr_ts_ins_grnorde2,"length"=>20,"type"=>array());
        $arFieldsConfig["grpagcor"] = array("controlid"=>"txtGrpagcor","label"=>tr_ts_ins_grpagcor,"length"=>3,"type"=>array());
        $arFieldsConfig["grmonpag"] = array("controlid"=>"txtGrmonpag","label"=>tr_ts_ins_grmonpag,"length"=>3,"type"=>array());
        $arFieldsConfig["grcdtran"] = array("controlid"=>"txtGrcdtran","label"=>tr_ts_ins_grcdtran,"length"=>100,"type"=>array());
        $arFieldsConfig["grtipcon"] = array("controlid"=>"txtGrtipcon","label"=>tr_ts_ins_grtipcon,"length"=>2,"type"=>array());
        $arFieldsConfig["grestado"] = array("controlid"=>"txtGrestado","label"=>tr_ts_ins_grestado,"length"=>2,"type"=>array());
        $arFieldsConfig["grcausae"] = array("controlid"=>"txtGrcausae","label"=>tr_ts_ins_grcausae,"length"=>3,"type"=>array());
        $arFieldsConfig["grcduser"] = array("controlid"=>"txtGrcduser","label"=>tr_ts_ins_grcduser,"length"=>12,"type"=>array());
        $arFieldsConfig["grtippag"] = array("controlid"=>"txtGrtippag","label"=>tr_ts_ins_grtippag,"length"=>3,"type"=>array());
        $arFieldsConfig["grcdbanc"] = array("controlid"=>"txtGrcdbanc","label"=>tr_ts_ins_grcdbanc,"length"=>3,"type"=>array());
        $arFieldsConfig["grtipcta"] = array("controlid"=>"txtGrtipcta","label"=>tr_ts_ins_grtipcta,"length"=>1,"type"=>array());
        $arFieldsConfig["grnrocta"] = array("controlid"=>"txtGrnrocta","label"=>tr_ts_ins_grnrocta,"length"=>20,"type"=>array());
        $arFieldsConfig["grobserv"] = array("controlid"=>"txtGrobserv","label"=>tr_ts_ins_grobserv,"length"=>255,"type"=>array());
        $arFieldsConfig["grmonori"] = array("controlid"=>"txtGrmonori","label"=>tr_ts_ins_grmonori,"length"=>3,"type"=>array());
        $arFieldsConfig["grdrbene"] = array("controlid"=>"txtGrdrbene","label"=>tr_ts_ins_grdrbene,"length"=>120,"type"=>array());
        $arFieldsConfig["grdrben2"] = array("controlid"=>"txtGrdrben2","label"=>tr_ts_ins_grdrben2,"length"=>120,"type"=>array());
        $arFieldsConfig["grembene"] = array("controlid"=>"txtGrembene","label"=>tr_ts_ins_grembene,"length"=>120,"type"=>array());
        $arFieldsConfig["grtlbene"] = array("controlid"=>"txtGrtlbene","label"=>tr_ts_ins_grtlbene,"length"=>20,"type"=>array());
        $arFieldsConfig["grtlben2"] = array("controlid"=>"txtGrtlben2","label"=>tr_ts_ins_grtlben2,"length"=>20,"type"=>array());
        $arFieldsConfig["grmensaj"] = array("controlid"=>"txtGrmensaj","label"=>tr_ts_ins_grmensaj,"length"=>255,"type"=>array());
        $arFieldsConfig["grciudds"] = array("controlid"=>"txtGrciudds","label"=>tr_ts_ins_grciudds,"length"=>6,"type"=>array());
        $arFieldsConfig["grcdcorr"] = array("controlid"=>"txtGrcdcorr","label"=>tr_ts_ins_grcdcorr,"length"=>6,"type"=>array());
        $arFieldsConfig["gragends"] = array("controlid"=>"txtGragends","label"=>tr_ts_ins_gragends,"length"=>6,"type"=>array());
        $arFieldsConfig["grtdbene"] = array("controlid"=>"txtGrtdbene","label"=>tr_ts_ins_grtdbene,"length"=>1,"type"=>array());
        $arFieldsConfig["grcdbene"] = array("controlid"=>"txtGrcdbene","label"=>tr_ts_ins_grcdbene,"length"=>15,"type"=>array());
        $arFieldsConfig["grnmbene"] = array("controlid"=>"txtGrnmbene","label"=>tr_ts_ins_grnmbene,"length"=>120,"type"=>array());
        $arFieldsConfig["grciudor"] = array("controlid"=>"txtGrciudor","label"=>tr_ts_ins_grciudor,"length"=>6,"type"=>array());
        $arFieldsConfig["gremremi"] = array("controlid"=>"txtGremremi","label"=>tr_ts_ins_gremremi,"length"=>120,"type"=>array());
        $arFieldsConfig["grcdocup"] = array("controlid"=>"txtGrcdocup","label"=>tr_ts_ins_grcdocup,"length"=>3,"type"=>array());
        $arFieldsConfig["grtlremi"] = array("controlid"=>"txtGrtlremi","label"=>tr_ts_ins_grtlremi,"length"=>20,"type"=>array());
        $arFieldsConfig["grtlrem2"] = array("controlid"=>"txtGrtlrem2","label"=>tr_ts_ins_grtlrem2,"length"=>20,"type"=>array());
        $arFieldsConfig["grpaisds"] = array("controlid"=>"txtGrpaisds","label"=>tr_ts_ins_grpaisds,"length"=>3,"type"=>array());
        $arFieldsConfig["grusmodi"] = array("controlid"=>"txtGrusmodi","label"=>tr_ts_ins_grusmodi,"length"=>12,"type"=>array());
        $arFieldsConfig["grtdremi"] = array("controlid"=>"txtGrtdremi","label"=>tr_ts_ins_grtdremi,"length"=>1,"type"=>array());
        $arFieldsConfig["grcdremi"] = array("controlid"=>"txtGrcdremi","label"=>tr_ts_ins_grcdremi,"length"=>15,"type"=>array());
        $arFieldsConfig["grnmremi"] = array("controlid"=>"txtGrnmremi","label"=>tr_ts_ins_grnmremi,"length"=>120,"type"=>array());
        $arFieldsConfig["grdrremi"] = array("controlid"=>"txtGrdrremi","label"=>tr_ts_ins_grdrremi,"length"=>120,"type"=>array());
        $arFieldsConfig["grdrrem2"] = array("controlid"=>"txtGrdrrem2","label"=>tr_ts_ins_grdrrem2,"length"=>120,"type"=>array());
        return $arFieldsConfig;
    }//get_insert_validate

    //insert_6
    protected function build_insert_form($usePost=0)
    {
        $oForm = new HelperForm("frmInsert");
        $oForm->add_class("form-horizontal");
        $oForm->add_style("margin-bottom:0");
        $arFields = $this->build_insert_fields($usePost);
        $oForm->add_controls($arFields);
        return $oForm;
    }//build_insert_form()

    //insert_7
    public function insert()
    {
        $this->go_to_401($this->oPermission->is_not_insert());
        //php and js validation
        $arFieldsConfig = $this->get_insert_validate();
        if($this->is_inserting())
        {
            $oAlert = new AppHelperAlertdiv();
            $oAlert->use_close_button();
            $arFieldsValues = $this->get_fields_from_post();
            $oValidate = new ComponentValidate($arFieldsConfig,$arFieldsValues);
            $arErrData = $oValidate->get_error_field();
            if($arErrData)
            {
                    $oAlert->set_type("e");
                    $oAlert->set_title(tr_data_not_saved);
                    $oAlert->set_content("Field <b>".$arErrData["label"]."</b> ".$arErrData["message"]);
            }
            else
            {
                    //$this->oTabgiros->log_save_insert();
                    $this->oTabgiros->set_attrib_value($arFieldsValues);
                    $this->oTabgiros->set_insert_user($this->oSessionUser->get_id());
                    //$this->oTabgiros->set_platform($this->oSessionUser->get_platform());
                    $this->oTabgiros->autoinsert();
                    if($this->oTabgiros->is_error())
                    {
                        $oAlert->set_type("e");
                        $oAlert->set_title(tr_data_not_saved);
                        $oAlert->set_content(tr_error_trying_to_save);
                    }
                    else//insert ok
                    {
                        $this->set_get("gridgiro",$this->oTabgiros->get_last_insert_id());
                        $oAlert->set_title(tr_data_saved);
                        $this->reset_post();
                        //$this->go_to_after_succes_cud();
                    }
            }//no error
        }//fin if is_inserting (post action=save)
        //Si hay errores se recupera desde post
        if($arErrData) $oForm = $this->build_insert_form(1);
        else $oForm = $this->build_insert_form();
        //SCRUMBS
        $oScrumbs = $this->build_insert_scrumbs();
        //TABS
        $oTabs = $this->build_insert_tabs();
        //OPER BUTTONS
        $oOpButtons = $this->build_insert_opbuttons();
        //JAVASCRIPT
        $oJavascript = new HelperJavascript();
        $oJavascript->set_validate_config($arFieldsConfig);
        $oJavascript->set_formid("frmInsert");
        //$oJavascript->set_focusid("id_all");
        //VIEW SET
        $this->oView->add_var($oScrumbs,"oScrumbs");
        $this->oView->add_var($oTabs,"oTabs");
        $this->oView->add_var($oOpButtons,"oOpButtons");
        $this->oView->add_var($oAlert,"oAlert");
        $this->oView->add_var($oForm,"oForm");
        $this->oView->add_var($oJavascript,"oJavascript");
        $this->oView->show_page();
    }//insert()
    //</editor-fold>

    //<editor-fold defaultstate="collapsed" desc="UPDATE">
    //update_1
    protected function build_update_scrumbs()
    {
        $arLinks = array();
        $sUrlLink = $this->build_url($this->sModuleName);
        $arLinks["list"]=array("href"=>$sUrlLink,"innerhtml"=>tr_ts_entities);
        $sUrlLink = $this->build_ur($this->sModuleName,NULL,"update","id=".$this->get_get("gridgiro"));
        $arLinks["detail"]=array("href"=>$sUrlLink,"innerhtml"=>tr_ts_entity.": ".$this->oTabgiros->get_id()." - ".$this->oTabgiros->get_description());
        $oScrumbs = new AppHelperBreadscrumbs($arLinks);
        return $oScrumbs;
    }//build_update_scrumbs()

    //update_2
    protected function build_update_tabs()
    {
        $arTabs = array();
        $sUrlTab = $this->build_ur($this->sModuleName,NULL,"update","id=".$this->get_get("gridgiro"));
        $arTabs["detail"]=array("href"=>$sUrlTab,"innerhtml"=>tr_ts_updtabs_detail);
        //$sUrlTab = $this->build_url($this->sModuleName,"foreignamodule","get_list_by_foreign","id_foreign=".$this->get_get("id_parent_foreign"))
        //$arTabs["foreigndata"]=array("href"=>$sUrlTab,"innerhtml"=>tr_ts_updtabs_foreigndata);
        $oTabs = new AppHelperHeadertabs($arTabs,"detail");
        return $oTabs;
    }//build_update_tabs()

    //update_3
    protected function build_update_opbuttons()
    {
        $arOpButtons = array();
        if($this->oPermission->is_select())
            $arOpButtons["list"]=array("href"=>$this->build_url($this->sModuleName),"icon"=>"awe-search","innerhtml"=>tr_ts_updopbutton_list);
        //if($this->oPermission->is_insert())
            //$arOpButtons["insert"]=array("href"=>$this->build_url($this->sModuleName,NULL,"insert"),"icon"=>"awe-plus","innerhtml"=>tr_ts_updopbutton_insert);
        if($this->oPermission->is_quarantine())
            $arOpButtons["delete"]=array("href"=>$this->build_url($this->sModuleName,NULL,"quarantine","id=".$this->get_get("gridgiro")),"icon"=>"awe-remove","innerhtml"=>tr_ts_updopbutton_quarantine);
        //if($this->oPermission->is_delete())
            //$arOpButtons["delete"]=array("href"=>$this->build_url($this->sModuleName,NULL,"delete","id=".$this->get_get("gridgiro")),"icon"=>"awe-remove","innerhtml"=>tr_ts_updopbutton_delete);
        $oOpButtons = new AppHelperButtontabs(tr_ts_entities);
        $oOpButtons->set_tabs($arOpButtons);
        return $oOpButtons;
    }//build_update_opbuttons()

    //update_4
    protected function build_update_fields($usePost=0)
    {
        $arFields = array(); $oAuxField = NULL; $oAuxLabel = NULL;
        //gridgiro
        $oAuxField = new HelperInputText("txtGridgiro","txtGridgiro");
        $oAuxField->set_value($this->oTabgiros->get_gridgiro());
        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtGridgiro")));
        $oAuxLabel = new HelperLabel("txtGridgiro",tr_ts_upd_gridgiro,"lblGridgiro");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grsecueo
        $oAuxField = new HelperInputText("txtGrsecueo","txtGrsecueo");
        $oAuxField->set_value($this->oTabgiros->get_grsecueo());
        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtGrsecueo")));
        $oAuxLabel = new HelperLabel("txtGrsecueo",tr_ts_upd_grsecueo,"lblGrsecueo");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //gridremi
        $oAuxField = new HelperInputText("txtGridremi","txtGridremi");
        $oAuxField->set_value($this->oTabgiros->get_gridremi());
        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtGridremi")));
        $oAuxLabel = new HelperLabel("txtGridremi",tr_ts_upd_gridremi,"lblGridremi");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grsecuen
        $oAuxField = new HelperInputText("txtGrsecuen","txtGrsecuen");
        $oAuxField->set_value($this->oTabgiros->get_grsecuen());
        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtGrsecuen")));
        $oAuxLabel = new HelperLabel("txtGrsecuen",tr_ts_upd_grsecuen,"lblGrsecuen");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //gridbene
        $oAuxField = new HelperInputText("txtGridbene","txtGridbene");
        $oAuxField->set_value($this->oTabgiros->get_gridbene());
        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtGridbene")));
        $oAuxLabel = new HelperLabel("txtGridbene",tr_ts_upd_gridbene,"lblGridbene");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grfactur
        $oAuxField = new HelperInputText("txtGrfactur","txtGrfactur");
        $oAuxField->set_value($this->oTabgiros->get_grfactur());
        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtGrfactur")));
        $oAuxLabel = new HelperLabel("txtGrfactur",tr_ts_upd_grfactur,"lblGrfactur");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grcdcart
        $oAuxField = new HelperInputText("txtGrcdcart","txtGrcdcart");
        $oAuxField->set_value($this->oTabgiros->get_grcdcart());
        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtGrcdcart")));
        $oAuxLabel = new HelperLabel("txtGrcdcart",tr_ts_upd_grcdcart,"lblGrcdcart");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grcdcont
        $oAuxField = new HelperInputText("txtGrcdcont","txtGrcdcont");
        $oAuxField->set_value($this->oTabgiros->get_grcdcont());
        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtGrcdcont")));
        $oAuxLabel = new HelperLabel("txtGrcdcont",tr_ts_upd_grcdcont,"lblGrcdcont");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grnrolla
        $oAuxField = new HelperInputText("txtGrnrolla","txtGrnrolla");
        $oAuxField->set_value($this->oTabgiros->get_grnrolla());
        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtGrnrolla")));
        $oAuxLabel = new HelperLabel("txtGrnrolla",tr_ts_upd_grnrolla,"lblGrnrolla");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grconsag
        $oAuxField = new HelperInputText("txtGrconsag","txtGrconsag");
        $oAuxField->set_value($this->oTabgiros->get_grconsag());
        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtGrconsag")));
        $oAuxLabel = new HelperLabel("txtGrconsag",tr_ts_upd_grconsag,"lblGrconsag");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grrembol
        $oAuxField = new HelperInputText("txtGrrembol","txtGrrembol");
        $oAuxField->set_value($this->oTabgiros->get_grrembol());
        if($usePost) $oAuxField->set_value(dbbo_int($this->get_post("txtGrrembol")));
        $oAuxLabel = new HelperLabel("txtGrrembol",tr_ts_upd_grrembol,"lblGrrembol");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grfechag
        $oAuxField = new HelperInputText("txtGrfechag","txtGrfechag");
        $oAuxField->set_value($this->oTabgiros->get_grfechag());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrfechag"));
        $oAuxLabel = new HelperLabel("txtGrfechag",tr_ts_upd_grfechag,"lblGrfechag");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grfeccon
        $oAuxField = new HelperInputText("txtGrfeccon","txtGrfeccon");
        $oAuxField->set_value($this->oTabgiros->get_grfeccon());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrfeccon"));
        $oAuxLabel = new HelperLabel("txtGrfeccon",tr_ts_upd_grfeccon,"lblGrfeccon");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grfecing
        $oAuxField = new HelperInputText("txtGrfecing","txtGrfecing");
        $oAuxField->set_value($this->oTabgiros->get_grfecing());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrfecing"));
        $oAuxLabel = new HelperLabel("txtGrfecing",tr_ts_upd_grfecing,"lblGrfecing");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grfecmod
        $oAuxField = new HelperInputText("txtGrfecmod","txtGrfecmod");
        $oAuxField->set_value($this->oTabgiros->get_grfecmod());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrfecmod"));
        $oAuxLabel = new HelperLabel("txtGrfecmod",tr_ts_upd_grfecmod,"lblGrfecmod");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grfecall
        $oAuxField = new HelperInputText("txtGrfecall","txtGrfecall");
        $oAuxField->set_value($this->oTabgiros->get_grfecall());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrfecall"));
        $oAuxLabel = new HelperLabel("txtGrfecall",tr_ts_upd_grfecall,"lblGrfecall");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grconpag
        $oAuxField = new HelperInputText("txtGrconpag","txtGrconpag");
        $oAuxField->set_value($this->oTabgiros->get_grconpag());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrconpag"));
        $oAuxLabel = new HelperLabel("txtGrconpag",tr_ts_upd_grconpag,"lblGrconpag");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grmontod
        $oAuxField = new HelperInputText("txtGrmontod","txtGrmontod");
        $oAuxField->set_value(dbbo_numeric2($this->oTabgiros->get_grmontod()));
        if($usePost) $oAuxField->set_value(dbbo_numeric2($this->get_post("txtGrmontod")));
        $oAuxLabel = new HelperLabel("txtGrmontod",tr_ts_upd_grmontod,"lblGrmontod");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grmonusd
        $oAuxField = new HelperInputText("txtGrmonusd","txtGrmonusd");
        $oAuxField->set_value(dbbo_numeric2($this->oTabgiros->get_grmonusd()));
        if($usePost) $oAuxField->set_value(dbbo_numeric2($this->get_post("txtGrmonusd")));
        $oAuxLabel = new HelperLabel("txtGrmonusd",tr_ts_upd_grmonusd,"lblGrmonusd");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grtasacm
        $oAuxField = new HelperInputText("txtGrtasacm","txtGrtasacm");
        $oAuxField->set_value(dbbo_numeric2($this->oTabgiros->get_grtasacm()));
        if($usePost) $oAuxField->set_value(dbbo_numeric2($this->get_post("txtGrtasacm")));
        $oAuxLabel = new HelperLabel("txtGrtasacm",tr_ts_upd_grtasacm,"lblGrtasacm");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grvlrcom
        $oAuxField = new HelperInputText("txtGrvlrcom","txtGrvlrcom");
        $oAuxField->set_value(dbbo_numeric2($this->oTabgiros->get_grvlrcom()));
        if($usePost) $oAuxField->set_value(dbbo_numeric2($this->get_post("txtGrvlrcom")));
        $oAuxLabel = new HelperLabel("txtGrvlrcom",tr_ts_upd_grvlrcom,"lblGrvlrcom");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grvlrajs
        $oAuxField = new HelperInputText("txtGrvlrajs","txtGrvlrajs");
        $oAuxField->set_value(dbbo_numeric2($this->oTabgiros->get_grvlrajs()));
        if($usePost) $oAuxField->set_value(dbbo_numeric2($this->get_post("txtGrvlrajs")));
        $oAuxLabel = new HelperLabel("txtGrvlrajs",tr_ts_upd_grvlrajs,"lblGrvlrajs");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grmontop
        $oAuxField = new HelperInputText("txtGrmontop","txtGrmontop");
        $oAuxField->set_value(dbbo_numeric2($this->oTabgiros->get_grmontop()));
        if($usePost) $oAuxField->set_value(dbbo_numeric2($this->get_post("txtGrmontop")));
        $oAuxLabel = new HelperLabel("txtGrmontop",tr_ts_upd_grmontop,"lblGrmontop");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grvlrdes
        $oAuxField = new HelperInputText("txtGrvlrdes","txtGrvlrdes");
        $oAuxField->set_value(dbbo_numeric2($this->oTabgiros->get_grvlrdes()));
        if($usePost) $oAuxField->set_value(dbbo_numeric2($this->get_post("txtGrvlrdes")));
        $oAuxLabel = new HelperLabel("txtGrvlrdes",tr_ts_upd_grvlrdes,"lblGrvlrdes");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grtipgir
        $oAuxField = new HelperInputText("txtGrtipgir","txtGrtipgir");
        $oAuxField->set_value($this->oTabgiros->get_grtipgir());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrtipgir"));
        $oAuxLabel = new HelperLabel("txtGrtipgir",tr_ts_upd_grtipgir,"lblGrtipgir");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grcdcoro
        $oAuxField = new HelperInputText("txtGrcdcoro","txtGrcdcoro");
        $oAuxField->set_value($this->oTabgiros->get_grcdcoro());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrcdcoro"));
        $oAuxLabel = new HelperLabel("txtGrcdcoro",tr_ts_upd_grcdcoro,"lblGrcdcoro");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grnorden
        $oAuxField = new HelperInputText("txtGrnorden","txtGrnorden");
        $oAuxField->set_value($this->oTabgiros->get_grnorden());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrnorden"));
        $oAuxLabel = new HelperLabel("txtGrnorden",tr_ts_upd_grnorden,"lblGrnorden");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grcdagen
        $oAuxField = new HelperInputText("txtGrcdagen","txtGrcdagen");
        $oAuxField->set_value($this->oTabgiros->get_grcdagen());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrcdagen"));
        $oAuxLabel = new HelperLabel("txtGrcdagen",tr_ts_upd_grcdagen,"lblGrcdagen");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grcdpais
        $oAuxField = new HelperInputText("txtGrcdpais","txtGrcdpais");
        $oAuxField->set_value($this->oTabgiros->get_grcdpais());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrcdpais"));
        $oAuxLabel = new HelperLabel("txtGrcdpais",tr_ts_upd_grcdpais,"lblGrcdpais");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grcdciud
        $oAuxField = new HelperInputText("txtGrcdciud","txtGrcdciud");
        $oAuxField->set_value($this->oTabgiros->get_grcdciud());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrcdciud"));
        $oAuxLabel = new HelperLabel("txtGrcdciud",tr_ts_upd_grcdciud,"lblGrcdciud");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grtipenv
        $oAuxField = new HelperInputText("txtGrtipenv","txtGrtipenv");
        $oAuxField->set_value($this->oTabgiros->get_grtipenv());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrtipenv"));
        $oAuxLabel = new HelperLabel("txtGrtipenv",tr_ts_upd_grtipenv,"lblGrtipenv");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //gritemid
        $oAuxField = new HelperInputText("txtGritemid","txtGritemid");
        $oAuxField->set_value($this->oTabgiros->get_gritemid());
        if($usePost) $oAuxField->set_value($this->get_post("txtGritemid"));
        $oAuxLabel = new HelperLabel("txtGritemid",tr_ts_upd_gritemid,"lblGritemid");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //gritdesc
        $oAuxField = new HelperInputText("txtGritdesc","txtGritdesc");
        $oAuxField->set_value($this->oTabgiros->get_gritdesc());
        if($usePost) $oAuxField->set_value($this->get_post("txtGritdesc"));
        $oAuxLabel = new HelperLabel("txtGritdesc",tr_ts_upd_gritdesc,"lblGritdesc");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grrelaci
        $oAuxField = new HelperInputText("txtGrrelaci","txtGrrelaci");
        $oAuxField->set_value($this->oTabgiros->get_grrelaci());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrrelaci"));
        $oAuxLabel = new HelperLabel("txtGrrelaci",tr_ts_upd_grrelaci,"lblGrrelaci");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grestciv
        $oAuxField = new HelperInputText("txtGrestciv","txtGrestciv");
        $oAuxField->set_value($this->oTabgiros->get_grestciv());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrestciv"));
        $oAuxLabel = new HelperLabel("txtGrestciv",tr_ts_upd_grestciv,"lblGrestciv");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grniving
        $oAuxField = new HelperInputText("txtGrniving","txtGrniving");
        $oAuxField->set_value($this->oTabgiros->get_grniving());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrniving"));
        $oAuxLabel = new HelperLabel("txtGrniving",tr_ts_upd_grniving,"lblGrniving");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //gridsexo
        $oAuxField = new HelperInputText("txtGridsexo","txtGridsexo");
        $oAuxField->set_value($this->oTabgiros->get_gridsexo());
        if($usePost) $oAuxField->set_value($this->get_post("txtGridsexo"));
        $oAuxLabel = new HelperLabel("txtGridsexo",tr_ts_upd_gridsexo,"lblGridsexo");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grcodnac
        $oAuxField = new HelperInputText("txtGrcodnac","txtGrcodnac");
        $oAuxField->set_value($this->oTabgiros->get_grcodnac());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrcodnac"));
        $oAuxLabel = new HelperLabel("txtGrcodnac",tr_ts_upd_grcodnac,"lblGrcodnac");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grtrabaj
        $oAuxField = new HelperInputText("txtGrtrabaj","txtGrtrabaj");
        $oAuxField->set_value($this->oTabgiros->get_grtrabaj());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrtrabaj"));
        $oAuxLabel = new HelperLabel("txtGrtrabaj",tr_ts_upd_grtrabaj,"lblGrtrabaj");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grapsrem
        $oAuxField = new HelperInputText("txtGrapsrem","txtGrapsrem");
        $oAuxField->set_value($this->oTabgiros->get_grapsrem());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrapsrem"));
        $oAuxLabel = new HelperLabel("txtGrapsrem",tr_ts_upd_grapsrem,"lblGrapsrem");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grident
        $oAuxField = new HelperInputText("txtGrident","txtGrident");
        $oAuxField->set_value($this->oTabgiros->get_grident());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrident"));
        $oAuxLabel = new HelperLabel("txtGrident",tr_ts_upd_grident,"lblGrident");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grcodcta
        $oAuxField = new HelperInputText("txtGrcodcta","txtGrcodcta");
        $oAuxField->set_value($this->oTabgiros->get_grcodcta());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrcodcta"));
        $oAuxLabel = new HelperLabel("txtGrcodcta",tr_ts_upd_grcodcta,"lblGrcodcta");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grdocpag
        $oAuxField = new HelperInputText("txtGrdocpag","txtGrdocpag");
        $oAuxField->set_value($this->oTabgiros->get_grdocpag());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrdocpag"));
        $oAuxLabel = new HelperLabel("txtGrdocpag",tr_ts_upd_grdocpag,"lblGrdocpag");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grnompag
        $oAuxField = new HelperInputText("txtGrnompag","txtGrnompag");
        $oAuxField->set_value($this->oTabgiros->get_grnompag());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrnompag"));
        $oAuxLabel = new HelperLabel("txtGrnompag",tr_ts_upd_grnompag,"lblGrnompag");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grusauto
        $oAuxField = new HelperInputText("txtGrusauto","txtGrusauto");
        $oAuxField->set_value($this->oTabgiros->get_grusauto());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrusauto"));
        $oAuxLabel = new HelperLabel("txtGrusauto",tr_ts_upd_grusauto,"lblGrusauto");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grtiptas
        $oAuxField = new HelperInputText("txtGrtiptas","txtGrtiptas");
        $oAuxField->set_value($this->oTabgiros->get_grtiptas());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrtiptas"));
        $oAuxLabel = new HelperLabel("txtGrtiptas",tr_ts_upd_grtiptas,"lblGrtiptas");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grnomcli
        $oAuxField = new HelperInputText("txtGrnomcli","txtGrnomcli");
        $oAuxField->set_value($this->oTabgiros->get_grnomcli());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrnomcli"));
        $oAuxLabel = new HelperLabel("txtGrnomcli",tr_ts_upd_grnomcli,"lblGrnomcli");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grapecli
        $oAuxField = new HelperInputText("txtGrapecli","txtGrapecli");
        $oAuxField->set_value($this->oTabgiros->get_grapecli());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrapecli"));
        $oAuxLabel = new HelperLabel("txtGrapecli",tr_ts_upd_grapecli,"lblGrapecli");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grsapcli
        $oAuxField = new HelperInputText("txtGrsapcli","txtGrsapcli");
        $oAuxField->set_value($this->oTabgiros->get_grsapcli());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrsapcli"));
        $oAuxLabel = new HelperLabel("txtGrsapcli",tr_ts_upd_grsapcli,"lblGrsapcli");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grofiscu
        $oAuxField = new HelperInputText("txtGrofiscu","txtGrofiscu");
        $oAuxField->set_value($this->oTabgiros->get_grofiscu());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrofiscu"));
        $oAuxLabel = new HelperLabel("txtGrofiscu",tr_ts_upd_grofiscu,"lblGrofiscu");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grnmsrem
        $oAuxField = new HelperInputText("txtGrnmsrem","txtGrnmsrem");
        $oAuxField->set_value($this->oTabgiros->get_grnmsrem());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrnmsrem"));
        $oAuxLabel = new HelperLabel("txtGrnmsrem",tr_ts_upd_grnmsrem,"lblGrnmsrem");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grfecenv
        $oAuxField = new HelperInputText("txtGrfecenv","txtGrfecenv");
        $oAuxField->set_value($this->oTabgiros->get_grfecenv());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrfecenv"));
        $oAuxLabel = new HelperLabel("txtGrfecenv",tr_ts_upd_grfecenv,"lblGrfecenv");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grhorenv
        $oAuxField = new HelperInputText("txtGrhorenv","txtGrhorenv");
        $oAuxField->set_value($this->oTabgiros->get_grhorenv());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrhorenv"));
        $oAuxLabel = new HelperLabel("txtGrhorenv",tr_ts_upd_grhorenv,"lblGrhorenv");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grtipreg
        $oAuxField = new HelperInputText("txtGrtipreg","txtGrtipreg");
        $oAuxField->set_value($this->oTabgiros->get_grtipreg());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrtipreg"));
        $oAuxLabel = new HelperLabel("txtGrtipreg",tr_ts_upd_grtipreg,"lblGrtipreg");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grcdcall
        $oAuxField = new HelperInputText("txtGrcdcall","txtGrcdcall");
        $oAuxField->set_value($this->oTabgiros->get_grcdcall());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrcdcall"));
        $oAuxLabel = new HelperLabel("txtGrcdcall",tr_ts_upd_grcdcall,"lblGrcdcall");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grpercon
        $oAuxField = new HelperInputText("txtGrpercon","txtGrpercon");
        $oAuxField->set_value($this->oTabgiros->get_grpercon());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrpercon"));
        $oAuxLabel = new HelperLabel("txtGrpercon",tr_ts_upd_grpercon,"lblGrpercon");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grgirenv
        $oAuxField = new HelperInputText("txtGrgirenv","txtGrgirenv");
        $oAuxField->set_value($this->oTabgiros->get_grgirenv());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrgirenv"));
        $oAuxLabel = new HelperLabel("txtGrgirenv",tr_ts_upd_grgirenv,"lblGrgirenv");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grclave
        $oAuxField = new HelperInputText("txtGrclave","txtGrclave");
        $oAuxField->set_value($this->oTabgiros->get_grclave());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrclave"));
        $oAuxLabel = new HelperLabel("txtGrclave",tr_ts_upd_grclave,"lblGrclave");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grcorpri
        $oAuxField = new HelperInputText("txtGrcorpri","txtGrcorpri");
        $oAuxField->set_value($this->oTabgiros->get_grcorpri());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrcorpri"));
        $oAuxLabel = new HelperLabel("txtGrcorpri",tr_ts_upd_grcorpri,"lblGrcorpri");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grmarcad
        $oAuxField = new HelperInputText("txtGrmarcad","txtGrmarcad");
        $oAuxField->set_value($this->oTabgiros->get_grmarcad());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrmarcad"));
        $oAuxLabel = new HelperLabel("txtGrmarcad",tr_ts_upd_grmarcad,"lblGrmarcad");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grmoncta
        $oAuxField = new HelperInputText("txtGrmoncta","txtGrmoncta");
        $oAuxField->set_value($this->oTabgiros->get_grmoncta());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrmoncta"));
        $oAuxLabel = new HelperLabel("txtGrmoncta",tr_ts_upd_grmoncta,"lblGrmoncta");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grnorde2
        $oAuxField = new HelperInputText("txtGrnorde2","txtGrnorde2");
        $oAuxField->set_value($this->oTabgiros->get_grnorde2());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrnorde2"));
        $oAuxLabel = new HelperLabel("txtGrnorde2",tr_ts_upd_grnorde2,"lblGrnorde2");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grpagcor
        $oAuxField = new HelperInputText("txtGrpagcor","txtGrpagcor");
        $oAuxField->set_value($this->oTabgiros->get_grpagcor());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrpagcor"));
        $oAuxLabel = new HelperLabel("txtGrpagcor",tr_ts_upd_grpagcor,"lblGrpagcor");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grmonpag
        $oAuxField = new HelperInputText("txtGrmonpag","txtGrmonpag");
        $oAuxField->set_value($this->oTabgiros->get_grmonpag());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrmonpag"));
        $oAuxLabel = new HelperLabel("txtGrmonpag",tr_ts_upd_grmonpag,"lblGrmonpag");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grcdtran
        $oAuxField = new HelperInputText("txtGrcdtran","txtGrcdtran");
        $oAuxField->set_value($this->oTabgiros->get_grcdtran());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrcdtran"));
        $oAuxLabel = new HelperLabel("txtGrcdtran",tr_ts_upd_grcdtran,"lblGrcdtran");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grtipcon
        $oAuxField = new HelperInputText("txtGrtipcon","txtGrtipcon");
        $oAuxField->set_value($this->oTabgiros->get_grtipcon());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrtipcon"));
        $oAuxLabel = new HelperLabel("txtGrtipcon",tr_ts_upd_grtipcon,"lblGrtipcon");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grestado
        $oAuxField = new HelperInputText("txtGrestado","txtGrestado");
        $oAuxField->set_value($this->oTabgiros->get_grestado());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrestado"));
        $oAuxLabel = new HelperLabel("txtGrestado",tr_ts_upd_grestado,"lblGrestado");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grcausae
        $oAuxField = new HelperInputText("txtGrcausae","txtGrcausae");
        $oAuxField->set_value($this->oTabgiros->get_grcausae());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrcausae"));
        $oAuxLabel = new HelperLabel("txtGrcausae",tr_ts_upd_grcausae,"lblGrcausae");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grcduser
        $oAuxField = new HelperInputText("txtGrcduser","txtGrcduser");
        $oAuxField->set_value($this->oTabgiros->get_grcduser());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrcduser"));
        $oAuxLabel = new HelperLabel("txtGrcduser",tr_ts_upd_grcduser,"lblGrcduser");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grtippag
        $oAuxField = new HelperInputText("txtGrtippag","txtGrtippag");
        $oAuxField->set_value($this->oTabgiros->get_grtippag());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrtippag"));
        $oAuxLabel = new HelperLabel("txtGrtippag",tr_ts_upd_grtippag,"lblGrtippag");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grcdbanc
        $oAuxField = new HelperInputText("txtGrcdbanc","txtGrcdbanc");
        $oAuxField->set_value($this->oTabgiros->get_grcdbanc());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrcdbanc"));
        $oAuxLabel = new HelperLabel("txtGrcdbanc",tr_ts_upd_grcdbanc,"lblGrcdbanc");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grtipcta
        $oAuxField = new HelperInputText("txtGrtipcta","txtGrtipcta");
        $oAuxField->set_value($this->oTabgiros->get_grtipcta());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrtipcta"));
        $oAuxLabel = new HelperLabel("txtGrtipcta",tr_ts_upd_grtipcta,"lblGrtipcta");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grnrocta
        $oAuxField = new HelperInputText("txtGrnrocta","txtGrnrocta");
        $oAuxField->set_value($this->oTabgiros->get_grnrocta());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrnrocta"));
        $oAuxLabel = new HelperLabel("txtGrnrocta",tr_ts_upd_grnrocta,"lblGrnrocta");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grobserv
        $oAuxField = new HelperInputText("txtGrobserv","txtGrobserv");
        $oAuxField->set_value($this->oTabgiros->get_grobserv());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrobserv"));
        $oAuxLabel = new HelperLabel("txtGrobserv",tr_ts_upd_grobserv,"lblGrobserv");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grmonori
        $oAuxField = new HelperInputText("txtGrmonori","txtGrmonori");
        $oAuxField->set_value($this->oTabgiros->get_grmonori());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrmonori"));
        $oAuxLabel = new HelperLabel("txtGrmonori",tr_ts_upd_grmonori,"lblGrmonori");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grdrbene
        $oAuxField = new HelperInputText("txtGrdrbene","txtGrdrbene");
        $oAuxField->set_value($this->oTabgiros->get_grdrbene());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrdrbene"));
        $oAuxLabel = new HelperLabel("txtGrdrbene",tr_ts_upd_grdrbene,"lblGrdrbene");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grdrben2
        $oAuxField = new HelperInputText("txtGrdrben2","txtGrdrben2");
        $oAuxField->set_value($this->oTabgiros->get_grdrben2());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrdrben2"));
        $oAuxLabel = new HelperLabel("txtGrdrben2",tr_ts_upd_grdrben2,"lblGrdrben2");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grembene
        $oAuxField = new HelperInputText("txtGrembene","txtGrembene");
        $oAuxField->set_value($this->oTabgiros->get_grembene());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrembene"));
        $oAuxLabel = new HelperLabel("txtGrembene",tr_ts_upd_grembene,"lblGrembene");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grtlbene
        $oAuxField = new HelperInputText("txtGrtlbene","txtGrtlbene");
        $oAuxField->set_value($this->oTabgiros->get_grtlbene());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrtlbene"));
        $oAuxLabel = new HelperLabel("txtGrtlbene",tr_ts_upd_grtlbene,"lblGrtlbene");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grtlben2
        $oAuxField = new HelperInputText("txtGrtlben2","txtGrtlben2");
        $oAuxField->set_value($this->oTabgiros->get_grtlben2());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrtlben2"));
        $oAuxLabel = new HelperLabel("txtGrtlben2",tr_ts_upd_grtlben2,"lblGrtlben2");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grmensaj
        $oAuxField = new HelperInputText("txtGrmensaj","txtGrmensaj");
        $oAuxField->set_value($this->oTabgiros->get_grmensaj());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrmensaj"));
        $oAuxLabel = new HelperLabel("txtGrmensaj",tr_ts_upd_grmensaj,"lblGrmensaj");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grciudds
        $oAuxField = new HelperInputText("txtGrciudds","txtGrciudds");
        $oAuxField->set_value($this->oTabgiros->get_grciudds());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrciudds"));
        $oAuxLabel = new HelperLabel("txtGrciudds",tr_ts_upd_grciudds,"lblGrciudds");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grcdcorr
        $oAuxField = new HelperInputText("txtGrcdcorr","txtGrcdcorr");
        $oAuxField->set_value($this->oTabgiros->get_grcdcorr());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrcdcorr"));
        $oAuxLabel = new HelperLabel("txtGrcdcorr",tr_ts_upd_grcdcorr,"lblGrcdcorr");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //gragends
        $oAuxField = new HelperInputText("txtGragends","txtGragends");
        $oAuxField->set_value($this->oTabgiros->get_gragends());
        if($usePost) $oAuxField->set_value($this->get_post("txtGragends"));
        $oAuxLabel = new HelperLabel("txtGragends",tr_ts_upd_gragends,"lblGragends");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grtdbene
        $oAuxField = new HelperInputText("txtGrtdbene","txtGrtdbene");
        $oAuxField->set_value($this->oTabgiros->get_grtdbene());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrtdbene"));
        $oAuxLabel = new HelperLabel("txtGrtdbene",tr_ts_upd_grtdbene,"lblGrtdbene");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grcdbene
        $oAuxField = new HelperInputText("txtGrcdbene","txtGrcdbene");
        $oAuxField->set_value($this->oTabgiros->get_grcdbene());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrcdbene"));
        $oAuxLabel = new HelperLabel("txtGrcdbene",tr_ts_upd_grcdbene,"lblGrcdbene");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grnmbene
        $oAuxField = new HelperInputText("txtGrnmbene","txtGrnmbene");
        $oAuxField->set_value($this->oTabgiros->get_grnmbene());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrnmbene"));
        $oAuxLabel = new HelperLabel("txtGrnmbene",tr_ts_upd_grnmbene,"lblGrnmbene");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grciudor
        $oAuxField = new HelperInputText("txtGrciudor","txtGrciudor");
        $oAuxField->set_value($this->oTabgiros->get_grciudor());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrciudor"));
        $oAuxLabel = new HelperLabel("txtGrciudor",tr_ts_upd_grciudor,"lblGrciudor");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //gremremi
        $oAuxField = new HelperInputText("txtGremremi","txtGremremi");
        $oAuxField->set_value($this->oTabgiros->get_gremremi());
        if($usePost) $oAuxField->set_value($this->get_post("txtGremremi"));
        $oAuxLabel = new HelperLabel("txtGremremi",tr_ts_upd_gremremi,"lblGremremi");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grcdocup
        $oAuxField = new HelperInputText("txtGrcdocup","txtGrcdocup");
        $oAuxField->set_value($this->oTabgiros->get_grcdocup());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrcdocup"));
        $oAuxLabel = new HelperLabel("txtGrcdocup",tr_ts_upd_grcdocup,"lblGrcdocup");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grtlremi
        $oAuxField = new HelperInputText("txtGrtlremi","txtGrtlremi");
        $oAuxField->set_value($this->oTabgiros->get_grtlremi());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrtlremi"));
        $oAuxLabel = new HelperLabel("txtGrtlremi",tr_ts_upd_grtlremi,"lblGrtlremi");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grtlrem2
        $oAuxField = new HelperInputText("txtGrtlrem2","txtGrtlrem2");
        $oAuxField->set_value($this->oTabgiros->get_grtlrem2());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrtlrem2"));
        $oAuxLabel = new HelperLabel("txtGrtlrem2",tr_ts_upd_grtlrem2,"lblGrtlrem2");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grpaisds
        $oAuxField = new HelperInputText("txtGrpaisds","txtGrpaisds");
        $oAuxField->set_value($this->oTabgiros->get_grpaisds());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrpaisds"));
        $oAuxLabel = new HelperLabel("txtGrpaisds",tr_ts_upd_grpaisds,"lblGrpaisds");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grusmodi
        $oAuxField = new HelperInputText("txtGrusmodi","txtGrusmodi");
        $oAuxField->set_value($this->oTabgiros->get_grusmodi());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrusmodi"));
        $oAuxLabel = new HelperLabel("txtGrusmodi",tr_ts_upd_grusmodi,"lblGrusmodi");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grtdremi
        $oAuxField = new HelperInputText("txtGrtdremi","txtGrtdremi");
        $oAuxField->set_value($this->oTabgiros->get_grtdremi());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrtdremi"));
        $oAuxLabel = new HelperLabel("txtGrtdremi",tr_ts_upd_grtdremi,"lblGrtdremi");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grcdremi
        $oAuxField = new HelperInputText("txtGrcdremi","txtGrcdremi");
        $oAuxField->set_value($this->oTabgiros->get_grcdremi());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrcdremi"));
        $oAuxLabel = new HelperLabel("txtGrcdremi",tr_ts_upd_grcdremi,"lblGrcdremi");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grnmremi
        $oAuxField = new HelperInputText("txtGrnmremi","txtGrnmremi");
        $oAuxField->set_value($this->oTabgiros->get_grnmremi());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrnmremi"));
        $oAuxLabel = new HelperLabel("txtGrnmremi",tr_ts_upd_grnmremi,"lblGrnmremi");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grdrremi
        $oAuxField = new HelperInputText("txtGrdrremi","txtGrdrremi");
        $oAuxField->set_value($this->oTabgiros->get_grdrremi());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrdrremi"));
        $oAuxLabel = new HelperLabel("txtGrdrremi",tr_ts_upd_grdrremi,"lblGrdrremi");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //grdrrem2
        $oAuxField = new HelperInputText("txtGrdrrem2","txtGrdrrem2");
        $oAuxField->set_value($this->oTabgiros->get_grdrrem2());
        if($usePost) $oAuxField->set_value($this->get_post("txtGrdrrem2"));
        $oAuxLabel = new HelperLabel("txtGrdrrem2",tr_ts_upd_grdrrem2,"lblGrdrrem2");
        $oAuxLabel->add_class("labelpk");
        //$oAuxField->readonly();$oAuxField->add_class("readonly");
        $arFields[] = new ApphelperControlGroup($oAuxField,$oAuxLabel);
        //BUTTON SAVE
        $oAuxField = new HelperButtonBasic("butSave",tr_ts_upd_savebutton);
        $oAuxField->add_class("btn btn-primary");
        $oAuxField->set_js_onclick("update();");
        if($this->oPermission->is_update())
            $arFields[] = new ApphelperFormactions(array($oAuxField));
        //AUDIT INFO
        $sRegInfo = $this->get_audit_info($this->oTabgiros->get_insert_user(),$this->oTabgiros->get_insert_date()
        ,$this->oTabgiros->get_update_user(),$this->oTabgiros->get_update_date());
        $arFields[]= new AppHelperFormhead(null,$sRegInfo);
        //POST INFO
        $oAuxField = new HelperInputHidden("hidAction","hidAction");
        $arFields[] = $oAuxField;
        $oAuxField = new HelperInputHidden("hidPostback","hidPostback");
        $arFields[] = $oAuxField;
        return $arFields;
    }//build_update_fields()

    //update_5
    protected function get_update_validate()
    {
        $arFieldsConfig = array();
        $arFieldsConfig["gridgiro"] = array("controlid"=>"txtGridgiro","label"=>tr_ts_upd_gridgiro,"length"=>4,"type"=>array());
        $arFieldsConfig["grsecueo"] = array("controlid"=>"txtGrsecueo","label"=>tr_ts_upd_grsecueo,"length"=>4,"type"=>array());
        $arFieldsConfig["gridremi"] = array("controlid"=>"txtGridremi","label"=>tr_ts_upd_gridremi,"length"=>4,"type"=>array());
        $arFieldsConfig["grsecuen"] = array("controlid"=>"txtGrsecuen","label"=>tr_ts_upd_grsecuen,"length"=>4,"type"=>array());
        $arFieldsConfig["gridbene"] = array("controlid"=>"txtGridbene","label"=>tr_ts_upd_gridbene,"length"=>4,"type"=>array());
        $arFieldsConfig["grfactur"] = array("controlid"=>"txtGrfactur","label"=>tr_ts_upd_grfactur,"length"=>4,"type"=>array());
        $arFieldsConfig["grcdcart"] = array("controlid"=>"txtGrcdcart","label"=>tr_ts_upd_grcdcart,"length"=>4,"type"=>array());
        $arFieldsConfig["grcdcont"] = array("controlid"=>"txtGrcdcont","label"=>tr_ts_upd_grcdcont,"length"=>4,"type"=>array());
        $arFieldsConfig["grnrolla"] = array("controlid"=>"txtGrnrolla","label"=>tr_ts_upd_grnrolla,"length"=>4,"type"=>array());
        $arFieldsConfig["grconsag"] = array("controlid"=>"txtGrconsag","label"=>tr_ts_upd_grconsag,"length"=>4,"type"=>array());
        $arFieldsConfig["grrembol"] = array("controlid"=>"txtGrrembol","label"=>tr_ts_upd_grrembol,"length"=>4,"type"=>array());
        $arFieldsConfig["grfechag"] = array("controlid"=>"txtGrfechag","label"=>tr_ts_upd_grfechag,"length"=>8,"type"=>array());
        $arFieldsConfig["grfeccon"] = array("controlid"=>"txtGrfeccon","label"=>tr_ts_upd_grfeccon,"length"=>8,"type"=>array());
        $arFieldsConfig["grfecing"] = array("controlid"=>"txtGrfecing","label"=>tr_ts_upd_grfecing,"length"=>8,"type"=>array());
        $arFieldsConfig["grfecmod"] = array("controlid"=>"txtGrfecmod","label"=>tr_ts_upd_grfecmod,"length"=>8,"type"=>array());
        $arFieldsConfig["grfecall"] = array("controlid"=>"txtGrfecall","label"=>tr_ts_upd_grfecall,"length"=>8,"type"=>array());
        $arFieldsConfig["grconpag"] = array("controlid"=>"txtGrconpag","label"=>tr_ts_upd_grconpag,"length"=>8,"type"=>array());
        $arFieldsConfig["grmontod"] = array("controlid"=>"txtGrmontod","label"=>tr_ts_upd_grmontod,"length"=>9,"type"=>array("numeric"));
        $arFieldsConfig["grmonusd"] = array("controlid"=>"txtGrmonusd","label"=>tr_ts_upd_grmonusd,"length"=>9,"type"=>array("numeric"));
        $arFieldsConfig["grtasacm"] = array("controlid"=>"txtGrtasacm","label"=>tr_ts_upd_grtasacm,"length"=>9,"type"=>array("numeric"));
        $arFieldsConfig["grvlrcom"] = array("controlid"=>"txtGrvlrcom","label"=>tr_ts_upd_grvlrcom,"length"=>9,"type"=>array("numeric"));
        $arFieldsConfig["grvlrajs"] = array("controlid"=>"txtGrvlrajs","label"=>tr_ts_upd_grvlrajs,"length"=>9,"type"=>array("numeric"));
        $arFieldsConfig["grmontop"] = array("controlid"=>"txtGrmontop","label"=>tr_ts_upd_grmontop,"length"=>9,"type"=>array("numeric"));
        $arFieldsConfig["grvlrdes"] = array("controlid"=>"txtGrvlrdes","label"=>tr_ts_upd_grvlrdes,"length"=>9,"type"=>array("numeric"));
        $arFieldsConfig["grtipgir"] = array("controlid"=>"txtGrtipgir","label"=>tr_ts_upd_grtipgir,"length"=>1,"type"=>array());
        $arFieldsConfig["grcdcoro"] = array("controlid"=>"txtGrcdcoro","label"=>tr_ts_upd_grcdcoro,"length"=>6,"type"=>array());
        $arFieldsConfig["grnorden"] = array("controlid"=>"txtGrnorden","label"=>tr_ts_upd_grnorden,"length"=>20,"type"=>array());
        $arFieldsConfig["grcdagen"] = array("controlid"=>"txtGrcdagen","label"=>tr_ts_upd_grcdagen,"length"=>6,"type"=>array());
        $arFieldsConfig["grcdpais"] = array("controlid"=>"txtGrcdpais","label"=>tr_ts_upd_grcdpais,"length"=>3,"type"=>array());
        $arFieldsConfig["grcdciud"] = array("controlid"=>"txtGrcdciud","label"=>tr_ts_upd_grcdciud,"length"=>6,"type"=>array());
        $arFieldsConfig["grtipenv"] = array("controlid"=>"txtGrtipenv","label"=>tr_ts_upd_grtipenv,"length"=>1,"type"=>array());
        $arFieldsConfig["gritemid"] = array("controlid"=>"txtGritemid","label"=>tr_ts_upd_gritemid,"length"=>20,"type"=>array());
        $arFieldsConfig["gritdesc"] = array("controlid"=>"txtGritdesc","label"=>tr_ts_upd_gritdesc,"length"=>60,"type"=>array());
        $arFieldsConfig["grrelaci"] = array("controlid"=>"txtGrrelaci","label"=>tr_ts_upd_grrelaci,"length"=>1,"type"=>array());
        $arFieldsConfig["grestciv"] = array("controlid"=>"txtGrestciv","label"=>tr_ts_upd_grestciv,"length"=>3,"type"=>array());
        $arFieldsConfig["grniving"] = array("controlid"=>"txtGrniving","label"=>tr_ts_upd_grniving,"length"=>3,"type"=>array());
        $arFieldsConfig["gridsexo"] = array("controlid"=>"txtGridsexo","label"=>tr_ts_upd_gridsexo,"length"=>1,"type"=>array());
        $arFieldsConfig["grcodnac"] = array("controlid"=>"txtGrcodnac","label"=>tr_ts_upd_grcodnac,"length"=>6,"type"=>array());
        $arFieldsConfig["grtrabaj"] = array("controlid"=>"txtGrtrabaj","label"=>tr_ts_upd_grtrabaj,"length"=>40,"type"=>array());
        $arFieldsConfig["grapsrem"] = array("controlid"=>"txtGrapsrem","label"=>tr_ts_upd_grapsrem,"length"=>60,"type"=>array());
        $arFieldsConfig["grident"] = array("controlid"=>"txtGrident","label"=>tr_ts_upd_grident,"length"=>15,"type"=>array());
        $arFieldsConfig["grcodcta"] = array("controlid"=>"txtGrcodcta","label"=>tr_ts_upd_grcodcta,"length"=>20,"type"=>array());
        $arFieldsConfig["grdocpag"] = array("controlid"=>"txtGrdocpag","label"=>tr_ts_upd_grdocpag,"length"=>50,"type"=>array());
        $arFieldsConfig["grnompag"] = array("controlid"=>"txtGrnompag","label"=>tr_ts_upd_grnompag,"length"=>60,"type"=>array());
        $arFieldsConfig["grusauto"] = array("controlid"=>"txtGrusauto","label"=>tr_ts_upd_grusauto,"length"=>12,"type"=>array());
        $arFieldsConfig["grtiptas"] = array("controlid"=>"txtGrtiptas","label"=>tr_ts_upd_grtiptas,"length"=>10,"type"=>array());
        $arFieldsConfig["grnomcli"] = array("controlid"=>"txtGrnomcli","label"=>tr_ts_upd_grnomcli,"length"=>60,"type"=>array());
        $arFieldsConfig["grapecli"] = array("controlid"=>"txtGrapecli","label"=>tr_ts_upd_grapecli,"length"=>60,"type"=>array());
        $arFieldsConfig["grsapcli"] = array("controlid"=>"txtGrsapcli","label"=>tr_ts_upd_grsapcli,"length"=>50,"type"=>array());
        $arFieldsConfig["grofiscu"] = array("controlid"=>"txtGrofiscu","label"=>tr_ts_upd_grofiscu,"length"=>6,"type"=>array());
        $arFieldsConfig["grnmsrem"] = array("controlid"=>"txtGrnmsrem","label"=>tr_ts_upd_grnmsrem,"length"=>60,"type"=>array());
        $arFieldsConfig["grfecenv"] = array("controlid"=>"txtGrfecenv","label"=>tr_ts_upd_grfecenv,"length"=>10,"type"=>array());
        $arFieldsConfig["grhorenv"] = array("controlid"=>"txtGrhorenv","label"=>tr_ts_upd_grhorenv,"length"=>10,"type"=>array());
        $arFieldsConfig["grtipreg"] = array("controlid"=>"txtGrtipreg","label"=>tr_ts_upd_grtipreg,"length"=>3,"type"=>array());
        $arFieldsConfig["grcdcall"] = array("controlid"=>"txtGrcdcall","label"=>tr_ts_upd_grcdcall,"length"=>12,"type"=>array());
        $arFieldsConfig["grpercon"] = array("controlid"=>"txtGrpercon","label"=>tr_ts_upd_grpercon,"length"=>120,"type"=>array());
        $arFieldsConfig["grgirenv"] = array("controlid"=>"txtGrgirenv","label"=>tr_ts_upd_grgirenv,"length"=>1,"type"=>array());
        $arFieldsConfig["grclave"] = array("controlid"=>"txtGrclave","label"=>tr_ts_upd_grclave,"length"=>15,"type"=>array());
        $arFieldsConfig["grcorpri"] = array("controlid"=>"txtGrcorpri","label"=>tr_ts_upd_grcorpri,"length"=>10,"type"=>array());
        $arFieldsConfig["grmarcad"] = array("controlid"=>"txtGrmarcad","label"=>tr_ts_upd_grmarcad,"length"=>1,"type"=>array());
        $arFieldsConfig["grmoncta"] = array("controlid"=>"txtGrmoncta","label"=>tr_ts_upd_grmoncta,"length"=>3,"type"=>array());
        $arFieldsConfig["grnorde2"] = array("controlid"=>"txtGrnorde2","label"=>tr_ts_upd_grnorde2,"length"=>20,"type"=>array());
        $arFieldsConfig["grpagcor"] = array("controlid"=>"txtGrpagcor","label"=>tr_ts_upd_grpagcor,"length"=>3,"type"=>array());
        $arFieldsConfig["grmonpag"] = array("controlid"=>"txtGrmonpag","label"=>tr_ts_upd_grmonpag,"length"=>3,"type"=>array());
        $arFieldsConfig["grcdtran"] = array("controlid"=>"txtGrcdtran","label"=>tr_ts_upd_grcdtran,"length"=>100,"type"=>array());
        $arFieldsConfig["grtipcon"] = array("controlid"=>"txtGrtipcon","label"=>tr_ts_upd_grtipcon,"length"=>2,"type"=>array());
        $arFieldsConfig["grestado"] = array("controlid"=>"txtGrestado","label"=>tr_ts_upd_grestado,"length"=>2,"type"=>array());
        $arFieldsConfig["grcausae"] = array("controlid"=>"txtGrcausae","label"=>tr_ts_upd_grcausae,"length"=>3,"type"=>array());
        $arFieldsConfig["grcduser"] = array("controlid"=>"txtGrcduser","label"=>tr_ts_upd_grcduser,"length"=>12,"type"=>array());
        $arFieldsConfig["grtippag"] = array("controlid"=>"txtGrtippag","label"=>tr_ts_upd_grtippag,"length"=>3,"type"=>array());
        $arFieldsConfig["grcdbanc"] = array("controlid"=>"txtGrcdbanc","label"=>tr_ts_upd_grcdbanc,"length"=>3,"type"=>array());
        $arFieldsConfig["grtipcta"] = array("controlid"=>"txtGrtipcta","label"=>tr_ts_upd_grtipcta,"length"=>1,"type"=>array());
        $arFieldsConfig["grnrocta"] = array("controlid"=>"txtGrnrocta","label"=>tr_ts_upd_grnrocta,"length"=>20,"type"=>array());
        $arFieldsConfig["grobserv"] = array("controlid"=>"txtGrobserv","label"=>tr_ts_upd_grobserv,"length"=>255,"type"=>array());
        $arFieldsConfig["grmonori"] = array("controlid"=>"txtGrmonori","label"=>tr_ts_upd_grmonori,"length"=>3,"type"=>array());
        $arFieldsConfig["grdrbene"] = array("controlid"=>"txtGrdrbene","label"=>tr_ts_upd_grdrbene,"length"=>120,"type"=>array());
        $arFieldsConfig["grdrben2"] = array("controlid"=>"txtGrdrben2","label"=>tr_ts_upd_grdrben2,"length"=>120,"type"=>array());
        $arFieldsConfig["grembene"] = array("controlid"=>"txtGrembene","label"=>tr_ts_upd_grembene,"length"=>120,"type"=>array());
        $arFieldsConfig["grtlbene"] = array("controlid"=>"txtGrtlbene","label"=>tr_ts_upd_grtlbene,"length"=>20,"type"=>array());
        $arFieldsConfig["grtlben2"] = array("controlid"=>"txtGrtlben2","label"=>tr_ts_upd_grtlben2,"length"=>20,"type"=>array());
        $arFieldsConfig["grmensaj"] = array("controlid"=>"txtGrmensaj","label"=>tr_ts_upd_grmensaj,"length"=>255,"type"=>array());
        $arFieldsConfig["grciudds"] = array("controlid"=>"txtGrciudds","label"=>tr_ts_upd_grciudds,"length"=>6,"type"=>array());
        $arFieldsConfig["grcdcorr"] = array("controlid"=>"txtGrcdcorr","label"=>tr_ts_upd_grcdcorr,"length"=>6,"type"=>array());
        $arFieldsConfig["gragends"] = array("controlid"=>"txtGragends","label"=>tr_ts_upd_gragends,"length"=>6,"type"=>array());
        $arFieldsConfig["grtdbene"] = array("controlid"=>"txtGrtdbene","label"=>tr_ts_upd_grtdbene,"length"=>1,"type"=>array());
        $arFieldsConfig["grcdbene"] = array("controlid"=>"txtGrcdbene","label"=>tr_ts_upd_grcdbene,"length"=>15,"type"=>array());
        $arFieldsConfig["grnmbene"] = array("controlid"=>"txtGrnmbene","label"=>tr_ts_upd_grnmbene,"length"=>120,"type"=>array());
        $arFieldsConfig["grciudor"] = array("controlid"=>"txtGrciudor","label"=>tr_ts_upd_grciudor,"length"=>6,"type"=>array());
        $arFieldsConfig["gremremi"] = array("controlid"=>"txtGremremi","label"=>tr_ts_upd_gremremi,"length"=>120,"type"=>array());
        $arFieldsConfig["grcdocup"] = array("controlid"=>"txtGrcdocup","label"=>tr_ts_upd_grcdocup,"length"=>3,"type"=>array());
        $arFieldsConfig["grtlremi"] = array("controlid"=>"txtGrtlremi","label"=>tr_ts_upd_grtlremi,"length"=>20,"type"=>array());
        $arFieldsConfig["grtlrem2"] = array("controlid"=>"txtGrtlrem2","label"=>tr_ts_upd_grtlrem2,"length"=>20,"type"=>array());
        $arFieldsConfig["grpaisds"] = array("controlid"=>"txtGrpaisds","label"=>tr_ts_upd_grpaisds,"length"=>3,"type"=>array());
        $arFieldsConfig["grusmodi"] = array("controlid"=>"txtGrusmodi","label"=>tr_ts_upd_grusmodi,"length"=>12,"type"=>array());
        $arFieldsConfig["grtdremi"] = array("controlid"=>"txtGrtdremi","label"=>tr_ts_upd_grtdremi,"length"=>1,"type"=>array());
        $arFieldsConfig["grcdremi"] = array("controlid"=>"txtGrcdremi","label"=>tr_ts_upd_grcdremi,"length"=>15,"type"=>array());
        $arFieldsConfig["grnmremi"] = array("controlid"=>"txtGrnmremi","label"=>tr_ts_upd_grnmremi,"length"=>120,"type"=>array());
        $arFieldsConfig["grdrremi"] = array("controlid"=>"txtGrdrremi","label"=>tr_ts_upd_grdrremi,"length"=>120,"type"=>array());
        $arFieldsConfig["grdrrem2"] = array("controlid"=>"txtGrdrrem2","label"=>tr_ts_upd_grdrrem2,"length"=>120,"type"=>array());
        return $arFieldsConfig;
    }//get_update_validate

    //update_6
    protected function build_update_form($usePost=0)
    {
        $id = $this->oTabgiros->get_id();
        if($id)
        {
            $oForm = new HelperForm("frmUpdate");
            $oForm->add_class("form-horizontal");
            $oForm->add_style("margin-bottom:0");
            if($this->oPermission->is_read()&&$this->oPermission->is_not_update())
                    $oForm->readonly();
            $arFields = $this->build_update_fields($usePost);
            $oForm->add_controls($arFields);
        }//if(id)
        else//!id
            $this->go_to_404();
        return $oForm;
    }//build_update_form()

    //update_7
    public function update()
    {
        //$this->go_to_401(($this->oPermission->is_not_read() && $this->oPermission->is_not_update())||$this->oSessionUser->is_not_dataowner());
        $this->go_to_401($this->oPermission->is_not_read() && $this->oPermission->is_not_update());
        //Validacion con PHP y JS
        $arFieldsConfig = $this->get_update_validate();
        if($this->is_updating())
        {
            $oAlert = new AppHelperAlertdiv();
            $oAlert->use_close_button();
            $arFieldsValues = $this->get_fields_from_post();
            $oValidate = new ComponentValidate($arFieldsConfig,$arFieldsValues);
            $arErrData = $oValidate->get_error_field();
            if($arErrData)
            {
                    $oAlert->set_type("e");
                    $oAlert->set_title(tr_data_not_saved);
                    $oAlert->set_content("Field <b>".$arErrData["label"]."</b> ".$arErrData["message"]);
            }
            else
            {
                    $this->oTabgiros->set_attrib_value($arFieldsValues);
                    //$this->oTabgiros->set_description($oTabgiros->get_field1()." ".$oTabgiros->get_field2());
                    $this->oTabgiros->set_update_user($this->oSessionUser->get_id());
                    $this->oTabgiros->autoupdate();
                    if($this->oTabgiros->is_error())
                    {
                        $oAlert->set_type("e");
                        $oAlert->set_title(tr_data_not_saved);
                        $oAlert->set_content(tr_error_trying_to_save);
                    }//no error
                    else//update ok
                    {
                        //$this->oTabgiros->load_by_id();
                        $oAlert->set_title(tr_data_saved);
                        $this->reset_post();
                        //$this->go_to_after_succes_cud();
                    }//error save
            }//error validation
        }//is_updating()
        if($arErrData) $oForm = $this->build_update_form(1);
        else $oForm = $this->build_update_form(); 
        //SCRUMBS
        $oScrumbs = $this->build_update_scrumbs();
        //TABS
        $oTabs = $this->build_update_tabs();
        //OPER BUTTONS
        $oOpButtons = $this->build_update_opbuttons();
        //JAVASCRIPT
        $oJavascript = new HelperJavascript();
        $oJavascript->set_updateaction();
        $oJavascript->set_validate_config($arFieldsConfig);
        $oJavascript->set_formid("frmUpdate");
        //$oJavascript->set_focusid("id_all");
        //VIEW SET
        $this->oView->add_var($oScrumbs,"oScrumbs");
        $this->oView->add_var($oTabs,"oTabs");
        $this->oView->add_var($oOpButtons,"oOpButtons");
        $this->oView->add_var($oAlert,"oAlert");
        $this->oView->add_var($oForm,"oForm");
        $this->oView->add_var($oJavascript,"oJavascript");
        $this->oView->show_page();
    }//update()
    //</editor-fold>

    //<editor-fold defaultstate="collapsed" desc="DELETE">
    //delete_1
    protected function single_delete()
    {
        $id = $this->get_get("gridgiro");
        if($id)
        {
            $this->oTabgiros->set_id($id);
            $this->oTabgiros->autodelete();
            if($this->oTabgiros->is_error())
            {
                    $this->isError = TRUE;
                    $this->set_session_message(tr_error_trying_to_delete);
            }
            else
            {
                    $this->set_session_message(tr_data_deleted);
            }
        }//si existe el id
        else
            $this->set_session_message(tr_error_key_not_supplied,"e");
    }//single_delete()

    //delete_2
    protected function multi_delete()
    {
        //Intenta recuperar pkeys sino pasa a recuperar el id. En ultimo caso lo que se ha pasado por parametro
        $arKeys = $this->get_listkeys();
        foreach($arKeys as $sKey)
        {
            $id = $sKey;
            $this->oTabgiros->set_id($id);
            $this->oTabgiros->autodelete();
            if($this->oTabgiros->is_error())
            {
                    $this->isError = true;
                    $this->set_session_message(tr_error_trying_to_delete,"e");
            }
        }//foreach arkeys
        if(!$this->isError)
            $this->set_session_message(tr_data_deleted);
    }//multi_delete()

    //delete_3
    private function delete()
    {
        //$this->go_to_401($this->oPermission->is_not_delete()||$this->oSessionUser->is_not_dataowner());
        $this->go_to_401($this->oPermission->is_not_delete());
        $this->isError = FALSE;
        //Si ocurre un error se guarda en isError
        if($this->is_multidelete())
            $this->multi_delete();
        else
            $this->single_delete();
        //Si no ocurrio errores en el intento de borrado
        if(!$this->isError)
            $this->go_to_after_succes_cud();
        else//delete ok
            $this->go_to_list();
    }	//delete()
    //</editor-fold>

    //<editor-fold defaultstate="collapsed" desc="QUARANTINE">
    //quarantine_1
    protected function single_quarantine()
    {
        $id = $this->get_get("gridgiro");
        if($id)
        {
            $this->oTabgiros->set_id($id);
            $this->oTabgiros->autoquarantine();
            if($this->oTabgiros->is_error())
                    $this->set_session_message(tr_error_trying_to_delete);
            else
                    $this->set_session_message(tr_data_deleted);
        }//else no id
        else
            $this->set_session_message(tr_error_key_not_supplied,"e");
    }//single_quarantine()

    //quarantine_2
    protected function multi_quarantine()
    {
        $this->isError = FALSE;
        //Intenta recuperar pkeys sino pasa a id, y en ultimo caso lo que se ha pasado por parametro
        $arKeys = $this->get_listkeys();
        foreach($arKeys as $sKey)
        {
            $id = $sKey;
            $this->oTabgiros->set_id($id);
            $this->oTabgiros->autoquarantine();
            if($this->oTabgiros->is_error())
            {
                    $isError = true;
                    $this->set_session_message(tr_error_trying_to_delete,"e");
            }
        }
        if(!$isError)
            $this->set_session_message(tr_data_deleted);
    }//multi_quarantine()

    //quarantine_3
    private function quarantine()
    {
        //$this->go_to_401($this->oPermission->is_not_quarantine()||$this->oSessionUser->is_not_dataowner());
        $this->go_to_401($this->oPermission->is_not_quarantine());
        if($this->is_multiquarantine())
            $this->multi_quarantine();
        else
            $this->single_quarantine();
        $this->go_to_list();
        if(!$this->isError)
            $this-go_to_after_succes_cud();
        else //quarantine ok
            $this->go_to_list();
    }//quarantine()

    //</editor-fold>

    //<editor-fold defaultstate="collapsed" desc="MULTIASSIGN">
    //multiassign_1
    protected function build_multiassign_buttons()
    {
        $arOpButtons = array();
        $arOpButtons["filters"]=array("href"=>"javascript:reset_filters();","icon"=>"awe-magic","innerhtml"=>tr_ts_clear_filters);
        $arOpButtons["reload"]=array("href"=>"javascript:TfwControl.form_submit();","icon"=>"awe-refresh","innerhtml"=>tr_ts_refresh);
        $arOpButtons["multiadd"]=array("href"=>"javascript:multiadd();","icon"=>"awe-external-link","innerhtml"=>tr_ts_multiadd);
        $arOpButtons["closeme"]=array("href"=>"javascript:closeme();","icon"=>"awe-remove-sign","innerhtml"=>tr_ts_closeme);
        $oOpButtons = new AppHelperButtontabs(tr_ts_entities);
        $oOpButtons->set_tabs($arOpButtons);
        return $oOpButtons;
    }//build_multiassign_buttons()

    //multiassign_2
    protected function load_config_multiassign_filters()
    {
        //gridgiro
        $this->set_filter("gridgiro","txtGridgiro",array("operator"=>"like"));
        //grsecueo
        $this->set_filter("grsecueo","txtGrsecueo",array("operator"=>"like"));
        //gridremi
        $this->set_filter("gridremi","txtGridremi",array("operator"=>"like"));
        //grsecuen
        $this->set_filter("grsecuen","txtGrsecuen",array("operator"=>"like"));
        //gridbene
        $this->set_filter("gridbene","txtGridbene",array("operator"=>"like"));
        //grfactur
        $this->set_filter("grfactur","txtGrfactur",array("operator"=>"like"));
        //grcdcart
        $this->set_filter("grcdcart","txtGrcdcart",array("operator"=>"like"));
        //grcdcont
        $this->set_filter("grcdcont","txtGrcdcont",array("operator"=>"like"));
        //grnrolla
        $this->set_filter("grnrolla","txtGrnrolla",array("operator"=>"like"));
        //grconsag
        $this->set_filter("grconsag","txtGrconsag",array("operator"=>"like"));
        //grrembol
        $this->set_filter("grrembol","txtGrrembol",array("operator"=>"like"));
        //grfechag
        $this->set_filter("grfechag","txtGrfechag",array("operator"=>"like"));
        //grfeccon
        $this->set_filter("grfeccon","txtGrfeccon",array("operator"=>"like"));
        //grfecing
        $this->set_filter("grfecing","txtGrfecing",array("operator"=>"like"));
        //grfecmod
        $this->set_filter("grfecmod","txtGrfecmod",array("operator"=>"like"));
        //grfecall
        $this->set_filter("grfecall","txtGrfecall",array("operator"=>"like"));
        //grconpag
        $this->set_filter("grconpag","txtGrconpag",array("operator"=>"like"));
        //grmontod
        $this->set_filter("grmontod","txtGrmontod",array("operator"=>"like"));
        //grmonusd
        $this->set_filter("grmonusd","txtGrmonusd",array("operator"=>"like"));
        //grtasacm
        $this->set_filter("grtasacm","txtGrtasacm",array("operator"=>"like"));
        //grvlrcom
        $this->set_filter("grvlrcom","txtGrvlrcom",array("operator"=>"like"));
        //grvlrajs
        $this->set_filter("grvlrajs","txtGrvlrajs",array("operator"=>"like"));
        //grmontop
        $this->set_filter("grmontop","txtGrmontop",array("operator"=>"like"));
        //grvlrdes
        $this->set_filter("grvlrdes","txtGrvlrdes",array("operator"=>"like"));
        //grtipgir
        $this->set_filter("grtipgir","txtGrtipgir",array("operator"=>"like"));
        //grcdcoro
        $this->set_filter("grcdcoro","txtGrcdcoro",array("operator"=>"like"));
        //grnorden
        $this->set_filter("grnorden","txtGrnorden",array("operator"=>"like"));
        //grcdagen
        $this->set_filter("grcdagen","txtGrcdagen",array("operator"=>"like"));
        //grcdpais
        $this->set_filter("grcdpais","txtGrcdpais",array("operator"=>"like"));
        //grcdciud
        $this->set_filter("grcdciud","txtGrcdciud",array("operator"=>"like"));
        //grtipenv
        $this->set_filter("grtipenv","txtGrtipenv",array("operator"=>"like"));
        //gritemid
        $this->set_filter("gritemid","txtGritemid",array("operator"=>"like"));
        //gritdesc
        $this->set_filter("gritdesc","txtGritdesc",array("operator"=>"like"));
        //grrelaci
        $this->set_filter("grrelaci","txtGrrelaci",array("operator"=>"like"));
        //grestciv
        $this->set_filter("grestciv","txtGrestciv",array("operator"=>"like"));
        //grniving
        $this->set_filter("grniving","txtGrniving",array("operator"=>"like"));
        //gridsexo
        $this->set_filter("gridsexo","txtGridsexo",array("operator"=>"like"));
        //grcodnac
        $this->set_filter("grcodnac","txtGrcodnac",array("operator"=>"like"));
        //grtrabaj
        $this->set_filter("grtrabaj","txtGrtrabaj",array("operator"=>"like"));
        //grapsrem
        $this->set_filter("grapsrem","txtGrapsrem",array("operator"=>"like"));
        //grident
        $this->set_filter("grident","txtGrident",array("operator"=>"like"));
        //grcodcta
        $this->set_filter("grcodcta","txtGrcodcta",array("operator"=>"like"));
        //grdocpag
        $this->set_filter("grdocpag","txtGrdocpag",array("operator"=>"like"));
        //grnompag
        $this->set_filter("grnompag","txtGrnompag",array("operator"=>"like"));
        //grusauto
        $this->set_filter("grusauto","txtGrusauto",array("operator"=>"like"));
        //grtiptas
        $this->set_filter("grtiptas","txtGrtiptas",array("operator"=>"like"));
        //grnomcli
        $this->set_filter("grnomcli","txtGrnomcli",array("operator"=>"like"));
        //grapecli
        $this->set_filter("grapecli","txtGrapecli",array("operator"=>"like"));
        //grsapcli
        $this->set_filter("grsapcli","txtGrsapcli",array("operator"=>"like"));
        //grofiscu
        $this->set_filter("grofiscu","txtGrofiscu",array("operator"=>"like"));
        //grnmsrem
        $this->set_filter("grnmsrem","txtGrnmsrem",array("operator"=>"like"));
        //grfecenv
        $this->set_filter("grfecenv","txtGrfecenv",array("operator"=>"like"));
        //grhorenv
        $this->set_filter("grhorenv","txtGrhorenv",array("operator"=>"like"));
        //grtipreg
        $this->set_filter("grtipreg","txtGrtipreg",array("operator"=>"like"));
        //grcdcall
        $this->set_filter("grcdcall","txtGrcdcall",array("operator"=>"like"));
        //grpercon
        $this->set_filter("grpercon","txtGrpercon",array("operator"=>"like"));
        //grgirenv
        $this->set_filter("grgirenv","txtGrgirenv",array("operator"=>"like"));
        //grclave
        $this->set_filter("grclave","txtGrclave",array("operator"=>"like"));
        //grcorpri
        $this->set_filter("grcorpri","txtGrcorpri",array("operator"=>"like"));
        //grmarcad
        $this->set_filter("grmarcad","txtGrmarcad",array("operator"=>"like"));
        //grmoncta
        $this->set_filter("grmoncta","txtGrmoncta",array("operator"=>"like"));
        //grnorde2
        $this->set_filter("grnorde2","txtGrnorde2",array("operator"=>"like"));
        //grpagcor
        $this->set_filter("grpagcor","txtGrpagcor",array("operator"=>"like"));
        //grmonpag
        $this->set_filter("grmonpag","txtGrmonpag",array("operator"=>"like"));
        //grcdtran
        $this->set_filter("grcdtran","txtGrcdtran",array("operator"=>"like"));
        //grtipcon
        $this->set_filter("grtipcon","txtGrtipcon",array("operator"=>"like"));
        //grestado
        $this->set_filter("grestado","txtGrestado",array("operator"=>"like"));
        //grcausae
        $this->set_filter("grcausae","txtGrcausae",array("operator"=>"like"));
        //grcduser
        $this->set_filter("grcduser","txtGrcduser",array("operator"=>"like"));
        //grtippag
        $this->set_filter("grtippag","txtGrtippag",array("operator"=>"like"));
        //grcdbanc
        $this->set_filter("grcdbanc","txtGrcdbanc",array("operator"=>"like"));
        //grtipcta
        $this->set_filter("grtipcta","txtGrtipcta",array("operator"=>"like"));
        //grnrocta
        $this->set_filter("grnrocta","txtGrnrocta",array("operator"=>"like"));
        //grobserv
        $this->set_filter("grobserv","txtGrobserv",array("operator"=>"like"));
        //grmonori
        $this->set_filter("grmonori","txtGrmonori",array("operator"=>"like"));
        //grdrbene
        $this->set_filter("grdrbene","txtGrdrbene",array("operator"=>"like"));
        //grdrben2
        $this->set_filter("grdrben2","txtGrdrben2",array("operator"=>"like"));
        //grembene
        $this->set_filter("grembene","txtGrembene",array("operator"=>"like"));
        //grtlbene
        $this->set_filter("grtlbene","txtGrtlbene",array("operator"=>"like"));
        //grtlben2
        $this->set_filter("grtlben2","txtGrtlben2",array("operator"=>"like"));
        //grmensaj
        $this->set_filter("grmensaj","txtGrmensaj",array("operator"=>"like"));
        //grciudds
        $this->set_filter("grciudds","txtGrciudds",array("operator"=>"like"));
        //grcdcorr
        $this->set_filter("grcdcorr","txtGrcdcorr",array("operator"=>"like"));
        //gragends
        $this->set_filter("gragends","txtGragends",array("operator"=>"like"));
        //grtdbene
        $this->set_filter("grtdbene","txtGrtdbene",array("operator"=>"like"));
        //grcdbene
        $this->set_filter("grcdbene","txtGrcdbene",array("operator"=>"like"));
        //grnmbene
        $this->set_filter("grnmbene","txtGrnmbene",array("operator"=>"like"));
        //grciudor
        $this->set_filter("grciudor","txtGrciudor",array("operator"=>"like"));
        //gremremi
        $this->set_filter("gremremi","txtGremremi",array("operator"=>"like"));
        //grcdocup
        $this->set_filter("grcdocup","txtGrcdocup",array("operator"=>"like"));
        //grtlremi
        $this->set_filter("grtlremi","txtGrtlremi",array("operator"=>"like"));
        //grtlrem2
        $this->set_filter("grtlrem2","txtGrtlrem2",array("operator"=>"like"));
        //grpaisds
        $this->set_filter("grpaisds","txtGrpaisds",array("operator"=>"like"));
        //grusmodi
        $this->set_filter("grusmodi","txtGrusmodi",array("operator"=>"like"));
        //grtdremi
        $this->set_filter("grtdremi","txtGrtdremi",array("operator"=>"like"));
        //grcdremi
        $this->set_filter("grcdremi","txtGrcdremi",array("operator"=>"like"));
        //grnmremi
        $this->set_filter("grnmremi","txtGrnmremi",array("operator"=>"like"));
        //grdrremi
        $this->set_filter("grdrremi","txtGrdrremi",array("operator"=>"like"));
        //grdrrem2
        $this->set_filter("grdrrem2","txtGrdrrem2",array("operator"=>"like"));
    }//load_config_multiassign_filters()

    //multiassign_3
    protected function get_multiassign_filters()
    {
        //CAMPOS
        $arFields = array();
        //gridgiro
        $oAuxField = new HelperInputText("txtGridgiro","txtGridgiro");
        $oAuxField->set_value($this->get_post("txtGridgiro"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGridgiro",tr_ts_fil_gridgiro));
        $arFields[] = $oAuxWrapper;
        //grsecueo
        $oAuxField = new HelperInputText("txtGrsecueo","txtGrsecueo");
        $oAuxField->set_value($this->get_post("txtGrsecueo"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrsecueo",tr_ts_fil_grsecueo));
        $arFields[] = $oAuxWrapper;
        //gridremi
        $oAuxField = new HelperInputText("txtGridremi","txtGridremi");
        $oAuxField->set_value($this->get_post("txtGridremi"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGridremi",tr_ts_fil_gridremi));
        $arFields[] = $oAuxWrapper;
        //grsecuen
        $oAuxField = new HelperInputText("txtGrsecuen","txtGrsecuen");
        $oAuxField->set_value($this->get_post("txtGrsecuen"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrsecuen",tr_ts_fil_grsecuen));
        $arFields[] = $oAuxWrapper;
        //gridbene
        $oAuxField = new HelperInputText("txtGridbene","txtGridbene");
        $oAuxField->set_value($this->get_post("txtGridbene"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGridbene",tr_ts_fil_gridbene));
        $arFields[] = $oAuxWrapper;
        //grfactur
        $oAuxField = new HelperInputText("txtGrfactur","txtGrfactur");
        $oAuxField->set_value($this->get_post("txtGrfactur"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrfactur",tr_ts_fil_grfactur));
        $arFields[] = $oAuxWrapper;
        //grcdcart
        $oAuxField = new HelperInputText("txtGrcdcart","txtGrcdcart");
        $oAuxField->set_value($this->get_post("txtGrcdcart"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrcdcart",tr_ts_fil_grcdcart));
        $arFields[] = $oAuxWrapper;
        //grcdcont
        $oAuxField = new HelperInputText("txtGrcdcont","txtGrcdcont");
        $oAuxField->set_value($this->get_post("txtGrcdcont"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrcdcont",tr_ts_fil_grcdcont));
        $arFields[] = $oAuxWrapper;
        //grnrolla
        $oAuxField = new HelperInputText("txtGrnrolla","txtGrnrolla");
        $oAuxField->set_value($this->get_post("txtGrnrolla"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrnrolla",tr_ts_fil_grnrolla));
        $arFields[] = $oAuxWrapper;
        //grconsag
        $oAuxField = new HelperInputText("txtGrconsag","txtGrconsag");
        $oAuxField->set_value($this->get_post("txtGrconsag"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrconsag",tr_ts_fil_grconsag));
        $arFields[] = $oAuxWrapper;
        //grrembol
        $oAuxField = new HelperInputText("txtGrrembol","txtGrrembol");
        $oAuxField->set_value($this->get_post("txtGrrembol"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrrembol",tr_ts_fil_grrembol));
        $arFields[] = $oAuxWrapper;
        //grfechag
        $oAuxField = new HelperInputText("txtGrfechag","txtGrfechag");
        $oAuxField->set_value($this->get_post("txtGrfechag"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrfechag",tr_ts_fil_grfechag));
        $arFields[] = $oAuxWrapper;
        //grfeccon
        $oAuxField = new HelperInputText("txtGrfeccon","txtGrfeccon");
        $oAuxField->set_value($this->get_post("txtGrfeccon"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrfeccon",tr_ts_fil_grfeccon));
        $arFields[] = $oAuxWrapper;
        //grfecing
        $oAuxField = new HelperInputText("txtGrfecing","txtGrfecing");
        $oAuxField->set_value($this->get_post("txtGrfecing"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrfecing",tr_ts_fil_grfecing));
        $arFields[] = $oAuxWrapper;
        //grfecmod
        $oAuxField = new HelperInputText("txtGrfecmod","txtGrfecmod");
        $oAuxField->set_value($this->get_post("txtGrfecmod"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrfecmod",tr_ts_fil_grfecmod));
        $arFields[] = $oAuxWrapper;
        //grfecall
        $oAuxField = new HelperInputText("txtGrfecall","txtGrfecall");
        $oAuxField->set_value($this->get_post("txtGrfecall"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrfecall",tr_ts_fil_grfecall));
        $arFields[] = $oAuxWrapper;
        //grconpag
        $oAuxField = new HelperInputText("txtGrconpag","txtGrconpag");
        $oAuxField->set_value($this->get_post("txtGrconpag"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrconpag",tr_ts_fil_grconpag));
        $arFields[] = $oAuxWrapper;
        //grmontod
        $oAuxField = new HelperInputText("txtGrmontod","txtGrmontod");
        $oAuxField->set_value($this->get_post("txtGrmontod"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrmontod",tr_ts_fil_grmontod));
        $arFields[] = $oAuxWrapper;
        //grmonusd
        $oAuxField = new HelperInputText("txtGrmonusd","txtGrmonusd");
        $oAuxField->set_value($this->get_post("txtGrmonusd"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrmonusd",tr_ts_fil_grmonusd));
        $arFields[] = $oAuxWrapper;
        //grtasacm
        $oAuxField = new HelperInputText("txtGrtasacm","txtGrtasacm");
        $oAuxField->set_value($this->get_post("txtGrtasacm"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrtasacm",tr_ts_fil_grtasacm));
        $arFields[] = $oAuxWrapper;
        //grvlrcom
        $oAuxField = new HelperInputText("txtGrvlrcom","txtGrvlrcom");
        $oAuxField->set_value($this->get_post("txtGrvlrcom"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrvlrcom",tr_ts_fil_grvlrcom));
        $arFields[] = $oAuxWrapper;
        //grvlrajs
        $oAuxField = new HelperInputText("txtGrvlrajs","txtGrvlrajs");
        $oAuxField->set_value($this->get_post("txtGrvlrajs"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrvlrajs",tr_ts_fil_grvlrajs));
        $arFields[] = $oAuxWrapper;
        //grmontop
        $oAuxField = new HelperInputText("txtGrmontop","txtGrmontop");
        $oAuxField->set_value($this->get_post("txtGrmontop"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrmontop",tr_ts_fil_grmontop));
        $arFields[] = $oAuxWrapper;
        //grvlrdes
        $oAuxField = new HelperInputText("txtGrvlrdes","txtGrvlrdes");
        $oAuxField->set_value($this->get_post("txtGrvlrdes"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrvlrdes",tr_ts_fil_grvlrdes));
        $arFields[] = $oAuxWrapper;
        //grtipgir
        $oAuxField = new HelperInputText("txtGrtipgir","txtGrtipgir");
        $oAuxField->set_value($this->get_post("txtGrtipgir"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrtipgir",tr_ts_fil_grtipgir));
        $arFields[] = $oAuxWrapper;
        //grcdcoro
        $oAuxField = new HelperInputText("txtGrcdcoro","txtGrcdcoro");
        $oAuxField->set_value($this->get_post("txtGrcdcoro"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrcdcoro",tr_ts_fil_grcdcoro));
        $arFields[] = $oAuxWrapper;
        //grnorden
        $oAuxField = new HelperInputText("txtGrnorden","txtGrnorden");
        $oAuxField->set_value($this->get_post("txtGrnorden"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrnorden",tr_ts_fil_grnorden));
        $arFields[] = $oAuxWrapper;
        //grcdagen
        $oAuxField = new HelperInputText("txtGrcdagen","txtGrcdagen");
        $oAuxField->set_value($this->get_post("txtGrcdagen"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrcdagen",tr_ts_fil_grcdagen));
        $arFields[] = $oAuxWrapper;
        //grcdpais
        $oAuxField = new HelperInputText("txtGrcdpais","txtGrcdpais");
        $oAuxField->set_value($this->get_post("txtGrcdpais"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrcdpais",tr_ts_fil_grcdpais));
        $arFields[] = $oAuxWrapper;
        //grcdciud
        $oAuxField = new HelperInputText("txtGrcdciud","txtGrcdciud");
        $oAuxField->set_value($this->get_post("txtGrcdciud"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrcdciud",tr_ts_fil_grcdciud));
        $arFields[] = $oAuxWrapper;
        //grtipenv
        $oAuxField = new HelperInputText("txtGrtipenv","txtGrtipenv");
        $oAuxField->set_value($this->get_post("txtGrtipenv"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrtipenv",tr_ts_fil_grtipenv));
        $arFields[] = $oAuxWrapper;
        //gritemid
        $oAuxField = new HelperInputText("txtGritemid","txtGritemid");
        $oAuxField->set_value($this->get_post("txtGritemid"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGritemid",tr_ts_fil_gritemid));
        $arFields[] = $oAuxWrapper;
        //gritdesc
        $oAuxField = new HelperInputText("txtGritdesc","txtGritdesc");
        $oAuxField->set_value($this->get_post("txtGritdesc"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGritdesc",tr_ts_fil_gritdesc));
        $arFields[] = $oAuxWrapper;
        //grrelaci
        $oAuxField = new HelperInputText("txtGrrelaci","txtGrrelaci");
        $oAuxField->set_value($this->get_post("txtGrrelaci"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrrelaci",tr_ts_fil_grrelaci));
        $arFields[] = $oAuxWrapper;
        //grestciv
        $oAuxField = new HelperInputText("txtGrestciv","txtGrestciv");
        $oAuxField->set_value($this->get_post("txtGrestciv"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrestciv",tr_ts_fil_grestciv));
        $arFields[] = $oAuxWrapper;
        //grniving
        $oAuxField = new HelperInputText("txtGrniving","txtGrniving");
        $oAuxField->set_value($this->get_post("txtGrniving"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrniving",tr_ts_fil_grniving));
        $arFields[] = $oAuxWrapper;
        //gridsexo
        $oAuxField = new HelperInputText("txtGridsexo","txtGridsexo");
        $oAuxField->set_value($this->get_post("txtGridsexo"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGridsexo",tr_ts_fil_gridsexo));
        $arFields[] = $oAuxWrapper;
        //grcodnac
        $oAuxField = new HelperInputText("txtGrcodnac","txtGrcodnac");
        $oAuxField->set_value($this->get_post("txtGrcodnac"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrcodnac",tr_ts_fil_grcodnac));
        $arFields[] = $oAuxWrapper;
        //grtrabaj
        $oAuxField = new HelperInputText("txtGrtrabaj","txtGrtrabaj");
        $oAuxField->set_value($this->get_post("txtGrtrabaj"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrtrabaj",tr_ts_fil_grtrabaj));
        $arFields[] = $oAuxWrapper;
        //grapsrem
        $oAuxField = new HelperInputText("txtGrapsrem","txtGrapsrem");
        $oAuxField->set_value($this->get_post("txtGrapsrem"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrapsrem",tr_ts_fil_grapsrem));
        $arFields[] = $oAuxWrapper;
        //grident
        $oAuxField = new HelperInputText("txtGrident","txtGrident");
        $oAuxField->set_value($this->get_post("txtGrident"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrident",tr_ts_fil_grident));
        $arFields[] = $oAuxWrapper;
        //grcodcta
        $oAuxField = new HelperInputText("txtGrcodcta","txtGrcodcta");
        $oAuxField->set_value($this->get_post("txtGrcodcta"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrcodcta",tr_ts_fil_grcodcta));
        $arFields[] = $oAuxWrapper;
        //grdocpag
        $oAuxField = new HelperInputText("txtGrdocpag","txtGrdocpag");
        $oAuxField->set_value($this->get_post("txtGrdocpag"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrdocpag",tr_ts_fil_grdocpag));
        $arFields[] = $oAuxWrapper;
        //grnompag
        $oAuxField = new HelperInputText("txtGrnompag","txtGrnompag");
        $oAuxField->set_value($this->get_post("txtGrnompag"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrnompag",tr_ts_fil_grnompag));
        $arFields[] = $oAuxWrapper;
        //grusauto
        $oAuxField = new HelperInputText("txtGrusauto","txtGrusauto");
        $oAuxField->set_value($this->get_post("txtGrusauto"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrusauto",tr_ts_fil_grusauto));
        $arFields[] = $oAuxWrapper;
        //grtiptas
        $oAuxField = new HelperInputText("txtGrtiptas","txtGrtiptas");
        $oAuxField->set_value($this->get_post("txtGrtiptas"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrtiptas",tr_ts_fil_grtiptas));
        $arFields[] = $oAuxWrapper;
        //grnomcli
        $oAuxField = new HelperInputText("txtGrnomcli","txtGrnomcli");
        $oAuxField->set_value($this->get_post("txtGrnomcli"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrnomcli",tr_ts_fil_grnomcli));
        $arFields[] = $oAuxWrapper;
        //grapecli
        $oAuxField = new HelperInputText("txtGrapecli","txtGrapecli");
        $oAuxField->set_value($this->get_post("txtGrapecli"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrapecli",tr_ts_fil_grapecli));
        $arFields[] = $oAuxWrapper;
        //grsapcli
        $oAuxField = new HelperInputText("txtGrsapcli","txtGrsapcli");
        $oAuxField->set_value($this->get_post("txtGrsapcli"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrsapcli",tr_ts_fil_grsapcli));
        $arFields[] = $oAuxWrapper;
        //grofiscu
        $oAuxField = new HelperInputText("txtGrofiscu","txtGrofiscu");
        $oAuxField->set_value($this->get_post("txtGrofiscu"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrofiscu",tr_ts_fil_grofiscu));
        $arFields[] = $oAuxWrapper;
        //grnmsrem
        $oAuxField = new HelperInputText("txtGrnmsrem","txtGrnmsrem");
        $oAuxField->set_value($this->get_post("txtGrnmsrem"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrnmsrem",tr_ts_fil_grnmsrem));
        $arFields[] = $oAuxWrapper;
        //grfecenv
        $oAuxField = new HelperInputText("txtGrfecenv","txtGrfecenv");
        $oAuxField->set_value($this->get_post("txtGrfecenv"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrfecenv",tr_ts_fil_grfecenv));
        $arFields[] = $oAuxWrapper;
        //grhorenv
        $oAuxField = new HelperInputText("txtGrhorenv","txtGrhorenv");
        $oAuxField->set_value($this->get_post("txtGrhorenv"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrhorenv",tr_ts_fil_grhorenv));
        $arFields[] = $oAuxWrapper;
        //grtipreg
        $oAuxField = new HelperInputText("txtGrtipreg","txtGrtipreg");
        $oAuxField->set_value($this->get_post("txtGrtipreg"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrtipreg",tr_ts_fil_grtipreg));
        $arFields[] = $oAuxWrapper;
        //grcdcall
        $oAuxField = new HelperInputText("txtGrcdcall","txtGrcdcall");
        $oAuxField->set_value($this->get_post("txtGrcdcall"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrcdcall",tr_ts_fil_grcdcall));
        $arFields[] = $oAuxWrapper;
        //grpercon
        $oAuxField = new HelperInputText("txtGrpercon","txtGrpercon");
        $oAuxField->set_value($this->get_post("txtGrpercon"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrpercon",tr_ts_fil_grpercon));
        $arFields[] = $oAuxWrapper;
        //grgirenv
        $oAuxField = new HelperInputText("txtGrgirenv","txtGrgirenv");
        $oAuxField->set_value($this->get_post("txtGrgirenv"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrgirenv",tr_ts_fil_grgirenv));
        $arFields[] = $oAuxWrapper;
        //grclave
        $oAuxField = new HelperInputText("txtGrclave","txtGrclave");
        $oAuxField->set_value($this->get_post("txtGrclave"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrclave",tr_ts_fil_grclave));
        $arFields[] = $oAuxWrapper;
        //grcorpri
        $oAuxField = new HelperInputText("txtGrcorpri","txtGrcorpri");
        $oAuxField->set_value($this->get_post("txtGrcorpri"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrcorpri",tr_ts_fil_grcorpri));
        $arFields[] = $oAuxWrapper;
        //grmarcad
        $oAuxField = new HelperInputText("txtGrmarcad","txtGrmarcad");
        $oAuxField->set_value($this->get_post("txtGrmarcad"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrmarcad",tr_ts_fil_grmarcad));
        $arFields[] = $oAuxWrapper;
        //grmoncta
        $oAuxField = new HelperInputText("txtGrmoncta","txtGrmoncta");
        $oAuxField->set_value($this->get_post("txtGrmoncta"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrmoncta",tr_ts_fil_grmoncta));
        $arFields[] = $oAuxWrapper;
        //grnorde2
        $oAuxField = new HelperInputText("txtGrnorde2","txtGrnorde2");
        $oAuxField->set_value($this->get_post("txtGrnorde2"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrnorde2",tr_ts_fil_grnorde2));
        $arFields[] = $oAuxWrapper;
        //grpagcor
        $oAuxField = new HelperInputText("txtGrpagcor","txtGrpagcor");
        $oAuxField->set_value($this->get_post("txtGrpagcor"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrpagcor",tr_ts_fil_grpagcor));
        $arFields[] = $oAuxWrapper;
        //grmonpag
        $oAuxField = new HelperInputText("txtGrmonpag","txtGrmonpag");
        $oAuxField->set_value($this->get_post("txtGrmonpag"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrmonpag",tr_ts_fil_grmonpag));
        $arFields[] = $oAuxWrapper;
        //grcdtran
        $oAuxField = new HelperInputText("txtGrcdtran","txtGrcdtran");
        $oAuxField->set_value($this->get_post("txtGrcdtran"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrcdtran",tr_ts_fil_grcdtran));
        $arFields[] = $oAuxWrapper;
        //grtipcon
        $oAuxField = new HelperInputText("txtGrtipcon","txtGrtipcon");
        $oAuxField->set_value($this->get_post("txtGrtipcon"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrtipcon",tr_ts_fil_grtipcon));
        $arFields[] = $oAuxWrapper;
        //grestado
        $oAuxField = new HelperInputText("txtGrestado","txtGrestado");
        $oAuxField->set_value($this->get_post("txtGrestado"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrestado",tr_ts_fil_grestado));
        $arFields[] = $oAuxWrapper;
        //grcausae
        $oAuxField = new HelperInputText("txtGrcausae","txtGrcausae");
        $oAuxField->set_value($this->get_post("txtGrcausae"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrcausae",tr_ts_fil_grcausae));
        $arFields[] = $oAuxWrapper;
        //grcduser
        $oAuxField = new HelperInputText("txtGrcduser","txtGrcduser");
        $oAuxField->set_value($this->get_post("txtGrcduser"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrcduser",tr_ts_fil_grcduser));
        $arFields[] = $oAuxWrapper;
        //grtippag
        $oAuxField = new HelperInputText("txtGrtippag","txtGrtippag");
        $oAuxField->set_value($this->get_post("txtGrtippag"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrtippag",tr_ts_fil_grtippag));
        $arFields[] = $oAuxWrapper;
        //grcdbanc
        $oAuxField = new HelperInputText("txtGrcdbanc","txtGrcdbanc");
        $oAuxField->set_value($this->get_post("txtGrcdbanc"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrcdbanc",tr_ts_fil_grcdbanc));
        $arFields[] = $oAuxWrapper;
        //grtipcta
        $oAuxField = new HelperInputText("txtGrtipcta","txtGrtipcta");
        $oAuxField->set_value($this->get_post("txtGrtipcta"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrtipcta",tr_ts_fil_grtipcta));
        $arFields[] = $oAuxWrapper;
        //grnrocta
        $oAuxField = new HelperInputText("txtGrnrocta","txtGrnrocta");
        $oAuxField->set_value($this->get_post("txtGrnrocta"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrnrocta",tr_ts_fil_grnrocta));
        $arFields[] = $oAuxWrapper;
        //grobserv
        $oAuxField = new HelperInputText("txtGrobserv","txtGrobserv");
        $oAuxField->set_value($this->get_post("txtGrobserv"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrobserv",tr_ts_fil_grobserv));
        $arFields[] = $oAuxWrapper;
        //grmonori
        $oAuxField = new HelperInputText("txtGrmonori","txtGrmonori");
        $oAuxField->set_value($this->get_post("txtGrmonori"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrmonori",tr_ts_fil_grmonori));
        $arFields[] = $oAuxWrapper;
        //grdrbene
        $oAuxField = new HelperInputText("txtGrdrbene","txtGrdrbene");
        $oAuxField->set_value($this->get_post("txtGrdrbene"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrdrbene",tr_ts_fil_grdrbene));
        $arFields[] = $oAuxWrapper;
        //grdrben2
        $oAuxField = new HelperInputText("txtGrdrben2","txtGrdrben2");
        $oAuxField->set_value($this->get_post("txtGrdrben2"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrdrben2",tr_ts_fil_grdrben2));
        $arFields[] = $oAuxWrapper;
        //grembene
        $oAuxField = new HelperInputText("txtGrembene","txtGrembene");
        $oAuxField->set_value($this->get_post("txtGrembene"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrembene",tr_ts_fil_grembene));
        $arFields[] = $oAuxWrapper;
        //grtlbene
        $oAuxField = new HelperInputText("txtGrtlbene","txtGrtlbene");
        $oAuxField->set_value($this->get_post("txtGrtlbene"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrtlbene",tr_ts_fil_grtlbene));
        $arFields[] = $oAuxWrapper;
        //grtlben2
        $oAuxField = new HelperInputText("txtGrtlben2","txtGrtlben2");
        $oAuxField->set_value($this->get_post("txtGrtlben2"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrtlben2",tr_ts_fil_grtlben2));
        $arFields[] = $oAuxWrapper;
        //grmensaj
        $oAuxField = new HelperInputText("txtGrmensaj","txtGrmensaj");
        $oAuxField->set_value($this->get_post("txtGrmensaj"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrmensaj",tr_ts_fil_grmensaj));
        $arFields[] = $oAuxWrapper;
        //grciudds
        $oAuxField = new HelperInputText("txtGrciudds","txtGrciudds");
        $oAuxField->set_value($this->get_post("txtGrciudds"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrciudds",tr_ts_fil_grciudds));
        $arFields[] = $oAuxWrapper;
        //grcdcorr
        $oAuxField = new HelperInputText("txtGrcdcorr","txtGrcdcorr");
        $oAuxField->set_value($this->get_post("txtGrcdcorr"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrcdcorr",tr_ts_fil_grcdcorr));
        $arFields[] = $oAuxWrapper;
        //gragends
        $oAuxField = new HelperInputText("txtGragends","txtGragends");
        $oAuxField->set_value($this->get_post("txtGragends"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGragends",tr_ts_fil_gragends));
        $arFields[] = $oAuxWrapper;
        //grtdbene
        $oAuxField = new HelperInputText("txtGrtdbene","txtGrtdbene");
        $oAuxField->set_value($this->get_post("txtGrtdbene"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrtdbene",tr_ts_fil_grtdbene));
        $arFields[] = $oAuxWrapper;
        //grcdbene
        $oAuxField = new HelperInputText("txtGrcdbene","txtGrcdbene");
        $oAuxField->set_value($this->get_post("txtGrcdbene"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrcdbene",tr_ts_fil_grcdbene));
        $arFields[] = $oAuxWrapper;
        //grnmbene
        $oAuxField = new HelperInputText("txtGrnmbene","txtGrnmbene");
        $oAuxField->set_value($this->get_post("txtGrnmbene"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrnmbene",tr_ts_fil_grnmbene));
        $arFields[] = $oAuxWrapper;
        //grciudor
        $oAuxField = new HelperInputText("txtGrciudor","txtGrciudor");
        $oAuxField->set_value($this->get_post("txtGrciudor"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrciudor",tr_ts_fil_grciudor));
        $arFields[] = $oAuxWrapper;
        //gremremi
        $oAuxField = new HelperInputText("txtGremremi","txtGremremi");
        $oAuxField->set_value($this->get_post("txtGremremi"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGremremi",tr_ts_fil_gremremi));
        $arFields[] = $oAuxWrapper;
        //grcdocup
        $oAuxField = new HelperInputText("txtGrcdocup","txtGrcdocup");
        $oAuxField->set_value($this->get_post("txtGrcdocup"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrcdocup",tr_ts_fil_grcdocup));
        $arFields[] = $oAuxWrapper;
        //grtlremi
        $oAuxField = new HelperInputText("txtGrtlremi","txtGrtlremi");
        $oAuxField->set_value($this->get_post("txtGrtlremi"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrtlremi",tr_ts_fil_grtlremi));
        $arFields[] = $oAuxWrapper;
        //grtlrem2
        $oAuxField = new HelperInputText("txtGrtlrem2","txtGrtlrem2");
        $oAuxField->set_value($this->get_post("txtGrtlrem2"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrtlrem2",tr_ts_fil_grtlrem2));
        $arFields[] = $oAuxWrapper;
        //grpaisds
        $oAuxField = new HelperInputText("txtGrpaisds","txtGrpaisds");
        $oAuxField->set_value($this->get_post("txtGrpaisds"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrpaisds",tr_ts_fil_grpaisds));
        $arFields[] = $oAuxWrapper;
        //grusmodi
        $oAuxField = new HelperInputText("txtGrusmodi","txtGrusmodi");
        $oAuxField->set_value($this->get_post("txtGrusmodi"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrusmodi",tr_ts_fil_grusmodi));
        $arFields[] = $oAuxWrapper;
        //grtdremi
        $oAuxField = new HelperInputText("txtGrtdremi","txtGrtdremi");
        $oAuxField->set_value($this->get_post("txtGrtdremi"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrtdremi",tr_ts_fil_grtdremi));
        $arFields[] = $oAuxWrapper;
        //grcdremi
        $oAuxField = new HelperInputText("txtGrcdremi","txtGrcdremi");
        $oAuxField->set_value($this->get_post("txtGrcdremi"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrcdremi",tr_ts_fil_grcdremi));
        $arFields[] = $oAuxWrapper;
        //grnmremi
        $oAuxField = new HelperInputText("txtGrnmremi","txtGrnmremi");
        $oAuxField->set_value($this->get_post("txtGrnmremi"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrnmremi",tr_ts_fil_grnmremi));
        $arFields[] = $oAuxWrapper;
        //grdrremi
        $oAuxField = new HelperInputText("txtGrdrremi","txtGrdrremi");
        $oAuxField->set_value($this->get_post("txtGrdrremi"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrdrremi",tr_ts_fil_grdrremi));
        $arFields[] = $oAuxWrapper;
        //grdrrem2
        $oAuxField = new HelperInputText("txtGrdrrem2","txtGrdrrem2");
        $oAuxField->set_value($this->get_post("txtGrdrrem2"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrdrrem2",tr_ts_fil_grdrrem2));
        $arFields[] = $oAuxWrapper;
        return $arFields;
    }//get_multiassign_filters()

    //multiassign_4
    protected function set_multiassignfilters_from_post()
    {
        //gridgiro
        $this->set_filter_value("gridgiro",$this->get_post("txtGridgiro"));
        //grsecueo
        $this->set_filter_value("grsecueo",$this->get_post("txtGrsecueo"));
        //gridremi
        $this->set_filter_value("gridremi",$this->get_post("txtGridremi"));
        //grsecuen
        $this->set_filter_value("grsecuen",$this->get_post("txtGrsecuen"));
        //gridbene
        $this->set_filter_value("gridbene",$this->get_post("txtGridbene"));
        //grfactur
        $this->set_filter_value("grfactur",$this->get_post("txtGrfactur"));
        //grcdcart
        $this->set_filter_value("grcdcart",$this->get_post("txtGrcdcart"));
        //grcdcont
        $this->set_filter_value("grcdcont",$this->get_post("txtGrcdcont"));
        //grnrolla
        $this->set_filter_value("grnrolla",$this->get_post("txtGrnrolla"));
        //grconsag
        $this->set_filter_value("grconsag",$this->get_post("txtGrconsag"));
        //grrembol
        $this->set_filter_value("grrembol",$this->get_post("txtGrrembol"));
        //grfechag
        $this->set_filter_value("grfechag",$this->get_post("txtGrfechag"));
        //grfeccon
        $this->set_filter_value("grfeccon",$this->get_post("txtGrfeccon"));
        //grfecing
        $this->set_filter_value("grfecing",$this->get_post("txtGrfecing"));
        //grfecmod
        $this->set_filter_value("grfecmod",$this->get_post("txtGrfecmod"));
        //grfecall
        $this->set_filter_value("grfecall",$this->get_post("txtGrfecall"));
        //grconpag
        $this->set_filter_value("grconpag",$this->get_post("txtGrconpag"));
        //grmontod
        $this->set_filter_value("grmontod",$this->get_post("txtGrmontod"));
        //grmonusd
        $this->set_filter_value("grmonusd",$this->get_post("txtGrmonusd"));
        //grtasacm
        $this->set_filter_value("grtasacm",$this->get_post("txtGrtasacm"));
        //grvlrcom
        $this->set_filter_value("grvlrcom",$this->get_post("txtGrvlrcom"));
        //grvlrajs
        $this->set_filter_value("grvlrajs",$this->get_post("txtGrvlrajs"));
        //grmontop
        $this->set_filter_value("grmontop",$this->get_post("txtGrmontop"));
        //grvlrdes
        $this->set_filter_value("grvlrdes",$this->get_post("txtGrvlrdes"));
        //grtipgir
        $this->set_filter_value("grtipgir",$this->get_post("txtGrtipgir"));
        //grcdcoro
        $this->set_filter_value("grcdcoro",$this->get_post("txtGrcdcoro"));
        //grnorden
        $this->set_filter_value("grnorden",$this->get_post("txtGrnorden"));
        //grcdagen
        $this->set_filter_value("grcdagen",$this->get_post("txtGrcdagen"));
        //grcdpais
        $this->set_filter_value("grcdpais",$this->get_post("txtGrcdpais"));
        //grcdciud
        $this->set_filter_value("grcdciud",$this->get_post("txtGrcdciud"));
        //grtipenv
        $this->set_filter_value("grtipenv",$this->get_post("txtGrtipenv"));
        //gritemid
        $this->set_filter_value("gritemid",$this->get_post("txtGritemid"));
        //gritdesc
        $this->set_filter_value("gritdesc",$this->get_post("txtGritdesc"));
        //grrelaci
        $this->set_filter_value("grrelaci",$this->get_post("txtGrrelaci"));
        //grestciv
        $this->set_filter_value("grestciv",$this->get_post("txtGrestciv"));
        //grniving
        $this->set_filter_value("grniving",$this->get_post("txtGrniving"));
        //gridsexo
        $this->set_filter_value("gridsexo",$this->get_post("txtGridsexo"));
        //grcodnac
        $this->set_filter_value("grcodnac",$this->get_post("txtGrcodnac"));
        //grtrabaj
        $this->set_filter_value("grtrabaj",$this->get_post("txtGrtrabaj"));
        //grapsrem
        $this->set_filter_value("grapsrem",$this->get_post("txtGrapsrem"));
        //grident
        $this->set_filter_value("grident",$this->get_post("txtGrident"));
        //grcodcta
        $this->set_filter_value("grcodcta",$this->get_post("txtGrcodcta"));
        //grdocpag
        $this->set_filter_value("grdocpag",$this->get_post("txtGrdocpag"));
        //grnompag
        $this->set_filter_value("grnompag",$this->get_post("txtGrnompag"));
        //grusauto
        $this->set_filter_value("grusauto",$this->get_post("txtGrusauto"));
        //grtiptas
        $this->set_filter_value("grtiptas",$this->get_post("txtGrtiptas"));
        //grnomcli
        $this->set_filter_value("grnomcli",$this->get_post("txtGrnomcli"));
        //grapecli
        $this->set_filter_value("grapecli",$this->get_post("txtGrapecli"));
        //grsapcli
        $this->set_filter_value("grsapcli",$this->get_post("txtGrsapcli"));
        //grofiscu
        $this->set_filter_value("grofiscu",$this->get_post("txtGrofiscu"));
        //grnmsrem
        $this->set_filter_value("grnmsrem",$this->get_post("txtGrnmsrem"));
        //grfecenv
        $this->set_filter_value("grfecenv",$this->get_post("txtGrfecenv"));
        //grhorenv
        $this->set_filter_value("grhorenv",$this->get_post("txtGrhorenv"));
        //grtipreg
        $this->set_filter_value("grtipreg",$this->get_post("txtGrtipreg"));
        //grcdcall
        $this->set_filter_value("grcdcall",$this->get_post("txtGrcdcall"));
        //grpercon
        $this->set_filter_value("grpercon",$this->get_post("txtGrpercon"));
        //grgirenv
        $this->set_filter_value("grgirenv",$this->get_post("txtGrgirenv"));
        //grclave
        $this->set_filter_value("grclave",$this->get_post("txtGrclave"));
        //grcorpri
        $this->set_filter_value("grcorpri",$this->get_post("txtGrcorpri"));
        //grmarcad
        $this->set_filter_value("grmarcad",$this->get_post("txtGrmarcad"));
        //grmoncta
        $this->set_filter_value("grmoncta",$this->get_post("txtGrmoncta"));
        //grnorde2
        $this->set_filter_value("grnorde2",$this->get_post("txtGrnorde2"));
        //grpagcor
        $this->set_filter_value("grpagcor",$this->get_post("txtGrpagcor"));
        //grmonpag
        $this->set_filter_value("grmonpag",$this->get_post("txtGrmonpag"));
        //grcdtran
        $this->set_filter_value("grcdtran",$this->get_post("txtGrcdtran"));
        //grtipcon
        $this->set_filter_value("grtipcon",$this->get_post("txtGrtipcon"));
        //grestado
        $this->set_filter_value("grestado",$this->get_post("txtGrestado"));
        //grcausae
        $this->set_filter_value("grcausae",$this->get_post("txtGrcausae"));
        //grcduser
        $this->set_filter_value("grcduser",$this->get_post("txtGrcduser"));
        //grtippag
        $this->set_filter_value("grtippag",$this->get_post("txtGrtippag"));
        //grcdbanc
        $this->set_filter_value("grcdbanc",$this->get_post("txtGrcdbanc"));
        //grtipcta
        $this->set_filter_value("grtipcta",$this->get_post("txtGrtipcta"));
        //grnrocta
        $this->set_filter_value("grnrocta",$this->get_post("txtGrnrocta"));
        //grobserv
        $this->set_filter_value("grobserv",$this->get_post("txtGrobserv"));
        //grmonori
        $this->set_filter_value("grmonori",$this->get_post("txtGrmonori"));
        //grdrbene
        $this->set_filter_value("grdrbene",$this->get_post("txtGrdrbene"));
        //grdrben2
        $this->set_filter_value("grdrben2",$this->get_post("txtGrdrben2"));
        //grembene
        $this->set_filter_value("grembene",$this->get_post("txtGrembene"));
        //grtlbene
        $this->set_filter_value("grtlbene",$this->get_post("txtGrtlbene"));
        //grtlben2
        $this->set_filter_value("grtlben2",$this->get_post("txtGrtlben2"));
        //grmensaj
        $this->set_filter_value("grmensaj",$this->get_post("txtGrmensaj"));
        //grciudds
        $this->set_filter_value("grciudds",$this->get_post("txtGrciudds"));
        //grcdcorr
        $this->set_filter_value("grcdcorr",$this->get_post("txtGrcdcorr"));
        //gragends
        $this->set_filter_value("gragends",$this->get_post("txtGragends"));
        //grtdbene
        $this->set_filter_value("grtdbene",$this->get_post("txtGrtdbene"));
        //grcdbene
        $this->set_filter_value("grcdbene",$this->get_post("txtGrcdbene"));
        //grnmbene
        $this->set_filter_value("grnmbene",$this->get_post("txtGrnmbene"));
        //grciudor
        $this->set_filter_value("grciudor",$this->get_post("txtGrciudor"));
        //gremremi
        $this->set_filter_value("gremremi",$this->get_post("txtGremremi"));
        //grcdocup
        $this->set_filter_value("grcdocup",$this->get_post("txtGrcdocup"));
        //grtlremi
        $this->set_filter_value("grtlremi",$this->get_post("txtGrtlremi"));
        //grtlrem2
        $this->set_filter_value("grtlrem2",$this->get_post("txtGrtlrem2"));
        //grpaisds
        $this->set_filter_value("grpaisds",$this->get_post("txtGrpaisds"));
        //grusmodi
        $this->set_filter_value("grusmodi",$this->get_post("txtGrusmodi"));
        //grtdremi
        $this->set_filter_value("grtdremi",$this->get_post("txtGrtdremi"));
        //grcdremi
        $this->set_filter_value("grcdremi",$this->get_post("txtGrcdremi"));
        //grnmremi
        $this->set_filter_value("grnmremi",$this->get_post("txtGrnmremi"));
        //grdrremi
        $this->set_filter_value("grdrremi",$this->get_post("txtGrdrremi"));
        //grdrrem2
        $this->set_filter_value("grdrrem2",$this->get_post("txtGrdrrem2"));
    }//set_multiassignfilters_from_post()

    //multiassign_5
    protected function get_multiassign_columns()
    {
        $arColumns["gridgiro"] = tr_ts_col_gridgiro;
        $arColumns["grsecueo"] = tr_ts_col_grsecueo;
        $arColumns["gridremi"] = tr_ts_col_gridremi;
        $arColumns["grsecuen"] = tr_ts_col_grsecuen;
        $arColumns["gridbene"] = tr_ts_col_gridbene;
        $arColumns["grfactur"] = tr_ts_col_grfactur;
        $arColumns["grcdcart"] = tr_ts_col_grcdcart;
        $arColumns["grcdcont"] = tr_ts_col_grcdcont;
        $arColumns["grnrolla"] = tr_ts_col_grnrolla;
        $arColumns["grconsag"] = tr_ts_col_grconsag;
        $arColumns["grrembol"] = tr_ts_col_grrembol;
        $arColumns["grfechag"] = tr_ts_col_grfechag;
        $arColumns["grfeccon"] = tr_ts_col_grfeccon;
        $arColumns["grfecing"] = tr_ts_col_grfecing;
        $arColumns["grfecmod"] = tr_ts_col_grfecmod;
        $arColumns["grfecall"] = tr_ts_col_grfecall;
        $arColumns["grconpag"] = tr_ts_col_grconpag;
        $arColumns["grmontod"] = tr_ts_col_grmontod;
        $arColumns["grmonusd"] = tr_ts_col_grmonusd;
        $arColumns["grtasacm"] = tr_ts_col_grtasacm;
        $arColumns["grvlrcom"] = tr_ts_col_grvlrcom;
        $arColumns["grvlrajs"] = tr_ts_col_grvlrajs;
        $arColumns["grmontop"] = tr_ts_col_grmontop;
        $arColumns["grvlrdes"] = tr_ts_col_grvlrdes;
        $arColumns["grtipgir"] = tr_ts_col_grtipgir;
        $arColumns["grcdcoro"] = tr_ts_col_grcdcoro;
        $arColumns["grnorden"] = tr_ts_col_grnorden;
        $arColumns["grcdagen"] = tr_ts_col_grcdagen;
        $arColumns["grcdpais"] = tr_ts_col_grcdpais;
        $arColumns["grcdciud"] = tr_ts_col_grcdciud;
        $arColumns["grtipenv"] = tr_ts_col_grtipenv;
        $arColumns["gritemid"] = tr_ts_col_gritemid;
        $arColumns["gritdesc"] = tr_ts_col_gritdesc;
        $arColumns["grrelaci"] = tr_ts_col_grrelaci;
        $arColumns["grestciv"] = tr_ts_col_grestciv;
        $arColumns["grniving"] = tr_ts_col_grniving;
        $arColumns["gridsexo"] = tr_ts_col_gridsexo;
        $arColumns["grcodnac"] = tr_ts_col_grcodnac;
        $arColumns["grtrabaj"] = tr_ts_col_grtrabaj;
        $arColumns["grapsrem"] = tr_ts_col_grapsrem;
        $arColumns["grident"] = tr_ts_col_grident;
        $arColumns["grcodcta"] = tr_ts_col_grcodcta;
        $arColumns["grdocpag"] = tr_ts_col_grdocpag;
        $arColumns["grnompag"] = tr_ts_col_grnompag;
        $arColumns["grusauto"] = tr_ts_col_grusauto;
        $arColumns["grtiptas"] = tr_ts_col_grtiptas;
        $arColumns["grnomcli"] = tr_ts_col_grnomcli;
        $arColumns["grapecli"] = tr_ts_col_grapecli;
        $arColumns["grsapcli"] = tr_ts_col_grsapcli;
        $arColumns["grofiscu"] = tr_ts_col_grofiscu;
        $arColumns["grnmsrem"] = tr_ts_col_grnmsrem;
        $arColumns["grfecenv"] = tr_ts_col_grfecenv;
        $arColumns["grhorenv"] = tr_ts_col_grhorenv;
        $arColumns["grtipreg"] = tr_ts_col_grtipreg;
        $arColumns["grcdcall"] = tr_ts_col_grcdcall;
        $arColumns["grpercon"] = tr_ts_col_grpercon;
        $arColumns["grgirenv"] = tr_ts_col_grgirenv;
        $arColumns["grclave"] = tr_ts_col_grclave;
        $arColumns["grcorpri"] = tr_ts_col_grcorpri;
        $arColumns["grmarcad"] = tr_ts_col_grmarcad;
        $arColumns["grmoncta"] = tr_ts_col_grmoncta;
        $arColumns["grnorde2"] = tr_ts_col_grnorde2;
        $arColumns["grpagcor"] = tr_ts_col_grpagcor;
        $arColumns["grmonpag"] = tr_ts_col_grmonpag;
        $arColumns["grcdtran"] = tr_ts_col_grcdtran;
        $arColumns["grtipcon"] = tr_ts_col_grtipcon;
        $arColumns["grestado"] = tr_ts_col_grestado;
        $arColumns["grcausae"] = tr_ts_col_grcausae;
        $arColumns["grcduser"] = tr_ts_col_grcduser;
        $arColumns["grtippag"] = tr_ts_col_grtippag;
        $arColumns["grcdbanc"] = tr_ts_col_grcdbanc;
        $arColumns["grtipcta"] = tr_ts_col_grtipcta;
        $arColumns["grnrocta"] = tr_ts_col_grnrocta;
        $arColumns["grobserv"] = tr_ts_col_grobserv;
        $arColumns["grmonori"] = tr_ts_col_grmonori;
        $arColumns["grdrbene"] = tr_ts_col_grdrbene;
        $arColumns["grdrben2"] = tr_ts_col_grdrben2;
        $arColumns["grembene"] = tr_ts_col_grembene;
        $arColumns["grtlbene"] = tr_ts_col_grtlbene;
        $arColumns["grtlben2"] = tr_ts_col_grtlben2;
        $arColumns["grmensaj"] = tr_ts_col_grmensaj;
        $arColumns["grciudds"] = tr_ts_col_grciudds;
        $arColumns["grcdcorr"] = tr_ts_col_grcdcorr;
        $arColumns["gragends"] = tr_ts_col_gragends;
        $arColumns["grtdbene"] = tr_ts_col_grtdbene;
        $arColumns["grcdbene"] = tr_ts_col_grcdbene;
        $arColumns["grnmbene"] = tr_ts_col_grnmbene;
        $arColumns["grciudor"] = tr_ts_col_grciudor;
        $arColumns["gremremi"] = tr_ts_col_gremremi;
        $arColumns["grcdocup"] = tr_ts_col_grcdocup;
        $arColumns["grtlremi"] = tr_ts_col_grtlremi;
        $arColumns["grtlrem2"] = tr_ts_col_grtlrem2;
        $arColumns["grpaisds"] = tr_ts_col_grpaisds;
        $arColumns["grusmodi"] = tr_ts_col_grusmodi;
        $arColumns["grtdremi"] = tr_ts_col_grtdremi;
        $arColumns["grcdremi"] = tr_ts_col_grcdremi;
        $arColumns["grnmremi"] = tr_ts_col_grnmremi;
        $arColumns["grdrremi"] = tr_ts_col_grdrremi;
        $arColumns["grdrrem2"] = tr_ts_col_grdrrem2;
        return $arColumns;
    }//get_multiassign_columns()

    //multiassign_6
    private function multiassign()
    {
        $this->go_to_401($this->oPermission->is_not_pick());
        $oAlert = new AppHelperAlertdiv();
        $oAlert->use_close_button();
        $sMessage = $this->get_session_message($sMessage);
        if($sMessage)
            $oAlert->set_title($sMessage);
        $sMessage = $this->get_session_message($sMessage,"e");
        if($sMessage)
        {
            $oAlert->set_type();
            $oAlert->set_title($sMessage);
        }
        //build controls and add data to global arFilterControls and arFilterFields
        $arColumns = $this->get_multiassign_columns();
        //FILTERS
        //Indica los filtros que se recuperarán. Hace un $this->arFilters = arra(fieldname=>value=>..)
        $this->load_config_multiassign_filters();
        $oFilter = new ComponentFilter();
        $oFilter->set_fieldnames($this->get_filter_fieldnames());
        //Indica que no se guardara en sesion por nombre de campo sino por nombre de control
        //para esto es necesario respetar el estricto camelcase
        $oFilter->use_field_prefix();
        //Guarda en sesion y post los campos enviados, los de orden y página
        $oFilter->refresh();
        $this->set_multiassignfilters_from_post();
        $arObjFilter = $this->get_multiassign_filters();
        $this->oTabgiros->set_orderby($this->get_orderby());
        $this->oTabgiros->set_ordertype($this->get_ordertype());
        $this->oTabgiros->set_filters($this->get_filter_searchconfig());
        //hierarchy recover
        //$this->oTabgiros->set_select_user($this->oSessionUser->get_id());
        //RECOVER DATALIST
        $arList = $this->oTabgiros->get_select_all_ids();
        $iRequestPage = $this->get_post("selPage");
        $oPage = new ComponentPage($arList,$iRequestPage);
        $arList = $oPage->get_items_to_show();
        $arList = $this->oTabgiros->get_select_all_by_ids($arList);
        //TABLE
        //This method adds objects controls to search list form
        $oTableAssign = new HelperTableTyped($arList,$arColumns);
        $oTableAssign->set_fields($arObjFilter);
        $oTableAssign->set_module($this->get_current_module());
        $oTableAssign->add_class("table table-striped table-bordered table-condensed");
        $oTableAssign->set_keyfields(array("gridgiro"));
        $oTableAssign->set_orderby($this->get_orderby());
        $oTableAssign->set_orderby_type($this->get_ordertype());
        $oTableAssign->set_column_pickmultiple();//columna checks
        $oTableAssign->merge_pks();//claves separadas por coma
        $oTableAssign->set_column_picksingle();//crea funcion
        $oTableAssign->set_column_detail();//detail column
        //esto se define en el padre
        //$oTableAssign->set_multiassign(array("keys"=>array("k"=>1,"k2"=>2)));
        $oTableAssign->set_multiadd(array("keys"=>array("k"=>$this->get_get("k"),"k2"=>$this->get_get("k2"))));
        $oTableAssign->set_current_page($oPage->get_current());
        $oTableAssign->set_next_page($oPage->get_next());
        $oTableAssign->set_first_page($oPage->get_first());
        $oTableAssign->set_last_page($oPage->get_last());
        $oTableAssign->set_total_regs($oPage->get_total_regs());
        $oTableAssign->set_total_pages($oPage->get_total());
        //CRUD BUTTONS BAR
        $oOpButtons = new AppHelperButtontabs(tr_ts_entities);
        $oOpButtons->set_tabs($this->build_multiassign_buttons());
        $oJavascript = new HelperJavascript();
        $oJavascript->set_filters($this->get_filter_controls_id());
        $oJavascript->set_focusid("id_all");
        $this->oView->add_var($oJavascript,"oJavascript");
        $this->oView->set_layout("onecolumn");
        $this->oView->add_var($oOpButtons,"oOpButtons");
        $this->oView->add_var($oAlert,"oAlert");
        $this->oView->add_var($oTableAssign,"oTableAssign");
        $this->oView->show_page();
    }//multiassign()
    //</editor-fold>

    //<editor-fold defaultstate="collapsed" desc="SINGLEASSIGN">
    //singleassign_1
    protected function build_singleassign_buttons()
    {
        $arButTabs = array();
        $arButTabs["filters"]=array("href"=>"javascript:reset_filters();","icon"=>"awe-magic","innerhtml"=>tr_ts_clear_filters);
        $arButTabs["reload"]=array("href"=>"javascript:TfwControl.form_submit();","icon"=>"awe-refresh","innerhtml"=>tr_ts_refresh);
        $arButTabs["closeme"]=array("href"=>"javascript:closeme();","icon"=>"awe-remove-sign","innerhtml"=>tr_ts_closeme);
        return $arButTabs;
    }//build_singleassign_buttons()

    //singleassign_2
    protected function load_config_singleassign_filters()
    {
        //gridgiro
        $this->set_filter("gridgiro","txtGridgiro",array("operator"=>"like"));
        //grsecueo
        $this->set_filter("grsecueo","txtGrsecueo",array("operator"=>"like"));
        //gridremi
        $this->set_filter("gridremi","txtGridremi",array("operator"=>"like"));
        //grsecuen
        $this->set_filter("grsecuen","txtGrsecuen",array("operator"=>"like"));
        //gridbene
        $this->set_filter("gridbene","txtGridbene",array("operator"=>"like"));
        //grfactur
        $this->set_filter("grfactur","txtGrfactur",array("operator"=>"like"));
        //grcdcart
        $this->set_filter("grcdcart","txtGrcdcart",array("operator"=>"like"));
        //grcdcont
        $this->set_filter("grcdcont","txtGrcdcont",array("operator"=>"like"));
        //grnrolla
        $this->set_filter("grnrolla","txtGrnrolla",array("operator"=>"like"));
        //grconsag
        $this->set_filter("grconsag","txtGrconsag",array("operator"=>"like"));
        //grrembol
        $this->set_filter("grrembol","txtGrrembol",array("operator"=>"like"));
        //grfechag
        $this->set_filter("grfechag","txtGrfechag",array("operator"=>"like"));
        //grfeccon
        $this->set_filter("grfeccon","txtGrfeccon",array("operator"=>"like"));
        //grfecing
        $this->set_filter("grfecing","txtGrfecing",array("operator"=>"like"));
        //grfecmod
        $this->set_filter("grfecmod","txtGrfecmod",array("operator"=>"like"));
        //grfecall
        $this->set_filter("grfecall","txtGrfecall",array("operator"=>"like"));
        //grconpag
        $this->set_filter("grconpag","txtGrconpag",array("operator"=>"like"));
        //grmontod
        $this->set_filter("grmontod","txtGrmontod",array("operator"=>"like"));
        //grmonusd
        $this->set_filter("grmonusd","txtGrmonusd",array("operator"=>"like"));
        //grtasacm
        $this->set_filter("grtasacm","txtGrtasacm",array("operator"=>"like"));
        //grvlrcom
        $this->set_filter("grvlrcom","txtGrvlrcom",array("operator"=>"like"));
        //grvlrajs
        $this->set_filter("grvlrajs","txtGrvlrajs",array("operator"=>"like"));
        //grmontop
        $this->set_filter("grmontop","txtGrmontop",array("operator"=>"like"));
        //grvlrdes
        $this->set_filter("grvlrdes","txtGrvlrdes",array("operator"=>"like"));
        //grtipgir
        $this->set_filter("grtipgir","txtGrtipgir",array("operator"=>"like"));
        //grcdcoro
        $this->set_filter("grcdcoro","txtGrcdcoro",array("operator"=>"like"));
        //grnorden
        $this->set_filter("grnorden","txtGrnorden",array("operator"=>"like"));
        //grcdagen
        $this->set_filter("grcdagen","txtGrcdagen",array("operator"=>"like"));
        //grcdpais
        $this->set_filter("grcdpais","txtGrcdpais",array("operator"=>"like"));
        //grcdciud
        $this->set_filter("grcdciud","txtGrcdciud",array("operator"=>"like"));
        //grtipenv
        $this->set_filter("grtipenv","txtGrtipenv",array("operator"=>"like"));
        //gritemid
        $this->set_filter("gritemid","txtGritemid",array("operator"=>"like"));
        //gritdesc
        $this->set_filter("gritdesc","txtGritdesc",array("operator"=>"like"));
        //grrelaci
        $this->set_filter("grrelaci","txtGrrelaci",array("operator"=>"like"));
        //grestciv
        $this->set_filter("grestciv","txtGrestciv",array("operator"=>"like"));
        //grniving
        $this->set_filter("grniving","txtGrniving",array("operator"=>"like"));
        //gridsexo
        $this->set_filter("gridsexo","txtGridsexo",array("operator"=>"like"));
        //grcodnac
        $this->set_filter("grcodnac","txtGrcodnac",array("operator"=>"like"));
        //grtrabaj
        $this->set_filter("grtrabaj","txtGrtrabaj",array("operator"=>"like"));
        //grapsrem
        $this->set_filter("grapsrem","txtGrapsrem",array("operator"=>"like"));
        //grident
        $this->set_filter("grident","txtGrident",array("operator"=>"like"));
        //grcodcta
        $this->set_filter("grcodcta","txtGrcodcta",array("operator"=>"like"));
        //grdocpag
        $this->set_filter("grdocpag","txtGrdocpag",array("operator"=>"like"));
        //grnompag
        $this->set_filter("grnompag","txtGrnompag",array("operator"=>"like"));
        //grusauto
        $this->set_filter("grusauto","txtGrusauto",array("operator"=>"like"));
        //grtiptas
        $this->set_filter("grtiptas","txtGrtiptas",array("operator"=>"like"));
        //grnomcli
        $this->set_filter("grnomcli","txtGrnomcli",array("operator"=>"like"));
        //grapecli
        $this->set_filter("grapecli","txtGrapecli",array("operator"=>"like"));
        //grsapcli
        $this->set_filter("grsapcli","txtGrsapcli",array("operator"=>"like"));
        //grofiscu
        $this->set_filter("grofiscu","txtGrofiscu",array("operator"=>"like"));
        //grnmsrem
        $this->set_filter("grnmsrem","txtGrnmsrem",array("operator"=>"like"));
        //grfecenv
        $this->set_filter("grfecenv","txtGrfecenv",array("operator"=>"like"));
        //grhorenv
        $this->set_filter("grhorenv","txtGrhorenv",array("operator"=>"like"));
        //grtipreg
        $this->set_filter("grtipreg","txtGrtipreg",array("operator"=>"like"));
        //grcdcall
        $this->set_filter("grcdcall","txtGrcdcall",array("operator"=>"like"));
        //grpercon
        $this->set_filter("grpercon","txtGrpercon",array("operator"=>"like"));
        //grgirenv
        $this->set_filter("grgirenv","txtGrgirenv",array("operator"=>"like"));
        //grclave
        $this->set_filter("grclave","txtGrclave",array("operator"=>"like"));
        //grcorpri
        $this->set_filter("grcorpri","txtGrcorpri",array("operator"=>"like"));
        //grmarcad
        $this->set_filter("grmarcad","txtGrmarcad",array("operator"=>"like"));
        //grmoncta
        $this->set_filter("grmoncta","txtGrmoncta",array("operator"=>"like"));
        //grnorde2
        $this->set_filter("grnorde2","txtGrnorde2",array("operator"=>"like"));
        //grpagcor
        $this->set_filter("grpagcor","txtGrpagcor",array("operator"=>"like"));
        //grmonpag
        $this->set_filter("grmonpag","txtGrmonpag",array("operator"=>"like"));
        //grcdtran
        $this->set_filter("grcdtran","txtGrcdtran",array("operator"=>"like"));
        //grtipcon
        $this->set_filter("grtipcon","txtGrtipcon",array("operator"=>"like"));
        //grestado
        $this->set_filter("grestado","txtGrestado",array("operator"=>"like"));
        //grcausae
        $this->set_filter("grcausae","txtGrcausae",array("operator"=>"like"));
        //grcduser
        $this->set_filter("grcduser","txtGrcduser",array("operator"=>"like"));
        //grtippag
        $this->set_filter("grtippag","txtGrtippag",array("operator"=>"like"));
        //grcdbanc
        $this->set_filter("grcdbanc","txtGrcdbanc",array("operator"=>"like"));
        //grtipcta
        $this->set_filter("grtipcta","txtGrtipcta",array("operator"=>"like"));
        //grnrocta
        $this->set_filter("grnrocta","txtGrnrocta",array("operator"=>"like"));
        //grobserv
        $this->set_filter("grobserv","txtGrobserv",array("operator"=>"like"));
        //grmonori
        $this->set_filter("grmonori","txtGrmonori",array("operator"=>"like"));
        //grdrbene
        $this->set_filter("grdrbene","txtGrdrbene",array("operator"=>"like"));
        //grdrben2
        $this->set_filter("grdrben2","txtGrdrben2",array("operator"=>"like"));
        //grembene
        $this->set_filter("grembene","txtGrembene",array("operator"=>"like"));
        //grtlbene
        $this->set_filter("grtlbene","txtGrtlbene",array("operator"=>"like"));
        //grtlben2
        $this->set_filter("grtlben2","txtGrtlben2",array("operator"=>"like"));
        //grmensaj
        $this->set_filter("grmensaj","txtGrmensaj",array("operator"=>"like"));
        //grciudds
        $this->set_filter("grciudds","txtGrciudds",array("operator"=>"like"));
        //grcdcorr
        $this->set_filter("grcdcorr","txtGrcdcorr",array("operator"=>"like"));
        //gragends
        $this->set_filter("gragends","txtGragends",array("operator"=>"like"));
        //grtdbene
        $this->set_filter("grtdbene","txtGrtdbene",array("operator"=>"like"));
        //grcdbene
        $this->set_filter("grcdbene","txtGrcdbene",array("operator"=>"like"));
        //grnmbene
        $this->set_filter("grnmbene","txtGrnmbene",array("operator"=>"like"));
        //grciudor
        $this->set_filter("grciudor","txtGrciudor",array("operator"=>"like"));
        //gremremi
        $this->set_filter("gremremi","txtGremremi",array("operator"=>"like"));
        //grcdocup
        $this->set_filter("grcdocup","txtGrcdocup",array("operator"=>"like"));
        //grtlremi
        $this->set_filter("grtlremi","txtGrtlremi",array("operator"=>"like"));
        //grtlrem2
        $this->set_filter("grtlrem2","txtGrtlrem2",array("operator"=>"like"));
        //grpaisds
        $this->set_filter("grpaisds","txtGrpaisds",array("operator"=>"like"));
        //grusmodi
        $this->set_filter("grusmodi","txtGrusmodi",array("operator"=>"like"));
        //grtdremi
        $this->set_filter("grtdremi","txtGrtdremi",array("operator"=>"like"));
        //grcdremi
        $this->set_filter("grcdremi","txtGrcdremi",array("operator"=>"like"));
        //grnmremi
        $this->set_filter("grnmremi","txtGrnmremi",array("operator"=>"like"));
        //grdrremi
        $this->set_filter("grdrremi","txtGrdrremi",array("operator"=>"like"));
        //grdrrem2
        $this->set_filter("grdrrem2","txtGrdrrem2",array("operator"=>"like"));
    }//load_config_singleassign_filters()

    //singleassign_3
    protected function get_singleassign_filters()
    {
        //CAMPOS
        $arFields = array();
        //gridgiro
        $oAuxField = new HelperInputText("txtGridgiro","txtGridgiro");
        $oAuxField->set_value($this->get_post("txtGridgiro"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGridgiro",tr_ts_fil_gridgiro));
        $arFields[] = $oAuxWrapper;
        //grsecueo
        $oAuxField = new HelperInputText("txtGrsecueo","txtGrsecueo");
        $oAuxField->set_value($this->get_post("txtGrsecueo"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrsecueo",tr_ts_fil_grsecueo));
        $arFields[] = $oAuxWrapper;
        //gridremi
        $oAuxField = new HelperInputText("txtGridremi","txtGridremi");
        $oAuxField->set_value($this->get_post("txtGridremi"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGridremi",tr_ts_fil_gridremi));
        $arFields[] = $oAuxWrapper;
        //grsecuen
        $oAuxField = new HelperInputText("txtGrsecuen","txtGrsecuen");
        $oAuxField->set_value($this->get_post("txtGrsecuen"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrsecuen",tr_ts_fil_grsecuen));
        $arFields[] = $oAuxWrapper;
        //gridbene
        $oAuxField = new HelperInputText("txtGridbene","txtGridbene");
        $oAuxField->set_value($this->get_post("txtGridbene"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGridbene",tr_ts_fil_gridbene));
        $arFields[] = $oAuxWrapper;
        //grfactur
        $oAuxField = new HelperInputText("txtGrfactur","txtGrfactur");
        $oAuxField->set_value($this->get_post("txtGrfactur"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrfactur",tr_ts_fil_grfactur));
        $arFields[] = $oAuxWrapper;
        //grcdcart
        $oAuxField = new HelperInputText("txtGrcdcart","txtGrcdcart");
        $oAuxField->set_value($this->get_post("txtGrcdcart"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrcdcart",tr_ts_fil_grcdcart));
        $arFields[] = $oAuxWrapper;
        //grcdcont
        $oAuxField = new HelperInputText("txtGrcdcont","txtGrcdcont");
        $oAuxField->set_value($this->get_post("txtGrcdcont"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrcdcont",tr_ts_fil_grcdcont));
        $arFields[] = $oAuxWrapper;
        //grnrolla
        $oAuxField = new HelperInputText("txtGrnrolla","txtGrnrolla");
        $oAuxField->set_value($this->get_post("txtGrnrolla"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrnrolla",tr_ts_fil_grnrolla));
        $arFields[] = $oAuxWrapper;
        //grconsag
        $oAuxField = new HelperInputText("txtGrconsag","txtGrconsag");
        $oAuxField->set_value($this->get_post("txtGrconsag"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrconsag",tr_ts_fil_grconsag));
        $arFields[] = $oAuxWrapper;
        //grrembol
        $oAuxField = new HelperInputText("txtGrrembol","txtGrrembol");
        $oAuxField->set_value($this->get_post("txtGrrembol"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrrembol",tr_ts_fil_grrembol));
        $arFields[] = $oAuxWrapper;
        //grfechag
        $oAuxField = new HelperInputText("txtGrfechag","txtGrfechag");
        $oAuxField->set_value($this->get_post("txtGrfechag"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrfechag",tr_ts_fil_grfechag));
        $arFields[] = $oAuxWrapper;
        //grfeccon
        $oAuxField = new HelperInputText("txtGrfeccon","txtGrfeccon");
        $oAuxField->set_value($this->get_post("txtGrfeccon"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrfeccon",tr_ts_fil_grfeccon));
        $arFields[] = $oAuxWrapper;
        //grfecing
        $oAuxField = new HelperInputText("txtGrfecing","txtGrfecing");
        $oAuxField->set_value($this->get_post("txtGrfecing"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrfecing",tr_ts_fil_grfecing));
        $arFields[] = $oAuxWrapper;
        //grfecmod
        $oAuxField = new HelperInputText("txtGrfecmod","txtGrfecmod");
        $oAuxField->set_value($this->get_post("txtGrfecmod"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrfecmod",tr_ts_fil_grfecmod));
        $arFields[] = $oAuxWrapper;
        //grfecall
        $oAuxField = new HelperInputText("txtGrfecall","txtGrfecall");
        $oAuxField->set_value($this->get_post("txtGrfecall"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrfecall",tr_ts_fil_grfecall));
        $arFields[] = $oAuxWrapper;
        //grconpag
        $oAuxField = new HelperInputText("txtGrconpag","txtGrconpag");
        $oAuxField->set_value($this->get_post("txtGrconpag"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrconpag",tr_ts_fil_grconpag));
        $arFields[] = $oAuxWrapper;
        //grmontod
        $oAuxField = new HelperInputText("txtGrmontod","txtGrmontod");
        $oAuxField->set_value($this->get_post("txtGrmontod"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrmontod",tr_ts_fil_grmontod));
        $arFields[] = $oAuxWrapper;
        //grmonusd
        $oAuxField = new HelperInputText("txtGrmonusd","txtGrmonusd");
        $oAuxField->set_value($this->get_post("txtGrmonusd"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrmonusd",tr_ts_fil_grmonusd));
        $arFields[] = $oAuxWrapper;
        //grtasacm
        $oAuxField = new HelperInputText("txtGrtasacm","txtGrtasacm");
        $oAuxField->set_value($this->get_post("txtGrtasacm"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrtasacm",tr_ts_fil_grtasacm));
        $arFields[] = $oAuxWrapper;
        //grvlrcom
        $oAuxField = new HelperInputText("txtGrvlrcom","txtGrvlrcom");
        $oAuxField->set_value($this->get_post("txtGrvlrcom"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrvlrcom",tr_ts_fil_grvlrcom));
        $arFields[] = $oAuxWrapper;
        //grvlrajs
        $oAuxField = new HelperInputText("txtGrvlrajs","txtGrvlrajs");
        $oAuxField->set_value($this->get_post("txtGrvlrajs"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrvlrajs",tr_ts_fil_grvlrajs));
        $arFields[] = $oAuxWrapper;
        //grmontop
        $oAuxField = new HelperInputText("txtGrmontop","txtGrmontop");
        $oAuxField->set_value($this->get_post("txtGrmontop"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrmontop",tr_ts_fil_grmontop));
        $arFields[] = $oAuxWrapper;
        //grvlrdes
        $oAuxField = new HelperInputText("txtGrvlrdes","txtGrvlrdes");
        $oAuxField->set_value($this->get_post("txtGrvlrdes"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrvlrdes",tr_ts_fil_grvlrdes));
        $arFields[] = $oAuxWrapper;
        //grtipgir
        $oAuxField = new HelperInputText("txtGrtipgir","txtGrtipgir");
        $oAuxField->set_value($this->get_post("txtGrtipgir"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrtipgir",tr_ts_fil_grtipgir));
        $arFields[] = $oAuxWrapper;
        //grcdcoro
        $oAuxField = new HelperInputText("txtGrcdcoro","txtGrcdcoro");
        $oAuxField->set_value($this->get_post("txtGrcdcoro"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrcdcoro",tr_ts_fil_grcdcoro));
        $arFields[] = $oAuxWrapper;
        //grnorden
        $oAuxField = new HelperInputText("txtGrnorden","txtGrnorden");
        $oAuxField->set_value($this->get_post("txtGrnorden"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrnorden",tr_ts_fil_grnorden));
        $arFields[] = $oAuxWrapper;
        //grcdagen
        $oAuxField = new HelperInputText("txtGrcdagen","txtGrcdagen");
        $oAuxField->set_value($this->get_post("txtGrcdagen"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrcdagen",tr_ts_fil_grcdagen));
        $arFields[] = $oAuxWrapper;
        //grcdpais
        $oAuxField = new HelperInputText("txtGrcdpais","txtGrcdpais");
        $oAuxField->set_value($this->get_post("txtGrcdpais"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrcdpais",tr_ts_fil_grcdpais));
        $arFields[] = $oAuxWrapper;
        //grcdciud
        $oAuxField = new HelperInputText("txtGrcdciud","txtGrcdciud");
        $oAuxField->set_value($this->get_post("txtGrcdciud"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrcdciud",tr_ts_fil_grcdciud));
        $arFields[] = $oAuxWrapper;
        //grtipenv
        $oAuxField = new HelperInputText("txtGrtipenv","txtGrtipenv");
        $oAuxField->set_value($this->get_post("txtGrtipenv"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrtipenv",tr_ts_fil_grtipenv));
        $arFields[] = $oAuxWrapper;
        //gritemid
        $oAuxField = new HelperInputText("txtGritemid","txtGritemid");
        $oAuxField->set_value($this->get_post("txtGritemid"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGritemid",tr_ts_fil_gritemid));
        $arFields[] = $oAuxWrapper;
        //gritdesc
        $oAuxField = new HelperInputText("txtGritdesc","txtGritdesc");
        $oAuxField->set_value($this->get_post("txtGritdesc"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGritdesc",tr_ts_fil_gritdesc));
        $arFields[] = $oAuxWrapper;
        //grrelaci
        $oAuxField = new HelperInputText("txtGrrelaci","txtGrrelaci");
        $oAuxField->set_value($this->get_post("txtGrrelaci"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrrelaci",tr_ts_fil_grrelaci));
        $arFields[] = $oAuxWrapper;
        //grestciv
        $oAuxField = new HelperInputText("txtGrestciv","txtGrestciv");
        $oAuxField->set_value($this->get_post("txtGrestciv"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrestciv",tr_ts_fil_grestciv));
        $arFields[] = $oAuxWrapper;
        //grniving
        $oAuxField = new HelperInputText("txtGrniving","txtGrniving");
        $oAuxField->set_value($this->get_post("txtGrniving"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrniving",tr_ts_fil_grniving));
        $arFields[] = $oAuxWrapper;
        //gridsexo
        $oAuxField = new HelperInputText("txtGridsexo","txtGridsexo");
        $oAuxField->set_value($this->get_post("txtGridsexo"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGridsexo",tr_ts_fil_gridsexo));
        $arFields[] = $oAuxWrapper;
        //grcodnac
        $oAuxField = new HelperInputText("txtGrcodnac","txtGrcodnac");
        $oAuxField->set_value($this->get_post("txtGrcodnac"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrcodnac",tr_ts_fil_grcodnac));
        $arFields[] = $oAuxWrapper;
        //grtrabaj
        $oAuxField = new HelperInputText("txtGrtrabaj","txtGrtrabaj");
        $oAuxField->set_value($this->get_post("txtGrtrabaj"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrtrabaj",tr_ts_fil_grtrabaj));
        $arFields[] = $oAuxWrapper;
        //grapsrem
        $oAuxField = new HelperInputText("txtGrapsrem","txtGrapsrem");
        $oAuxField->set_value($this->get_post("txtGrapsrem"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrapsrem",tr_ts_fil_grapsrem));
        $arFields[] = $oAuxWrapper;
        //grident
        $oAuxField = new HelperInputText("txtGrident","txtGrident");
        $oAuxField->set_value($this->get_post("txtGrident"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrident",tr_ts_fil_grident));
        $arFields[] = $oAuxWrapper;
        //grcodcta
        $oAuxField = new HelperInputText("txtGrcodcta","txtGrcodcta");
        $oAuxField->set_value($this->get_post("txtGrcodcta"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrcodcta",tr_ts_fil_grcodcta));
        $arFields[] = $oAuxWrapper;
        //grdocpag
        $oAuxField = new HelperInputText("txtGrdocpag","txtGrdocpag");
        $oAuxField->set_value($this->get_post("txtGrdocpag"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrdocpag",tr_ts_fil_grdocpag));
        $arFields[] = $oAuxWrapper;
        //grnompag
        $oAuxField = new HelperInputText("txtGrnompag","txtGrnompag");
        $oAuxField->set_value($this->get_post("txtGrnompag"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrnompag",tr_ts_fil_grnompag));
        $arFields[] = $oAuxWrapper;
        //grusauto
        $oAuxField = new HelperInputText("txtGrusauto","txtGrusauto");
        $oAuxField->set_value($this->get_post("txtGrusauto"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrusauto",tr_ts_fil_grusauto));
        $arFields[] = $oAuxWrapper;
        //grtiptas
        $oAuxField = new HelperInputText("txtGrtiptas","txtGrtiptas");
        $oAuxField->set_value($this->get_post("txtGrtiptas"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrtiptas",tr_ts_fil_grtiptas));
        $arFields[] = $oAuxWrapper;
        //grnomcli
        $oAuxField = new HelperInputText("txtGrnomcli","txtGrnomcli");
        $oAuxField->set_value($this->get_post("txtGrnomcli"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrnomcli",tr_ts_fil_grnomcli));
        $arFields[] = $oAuxWrapper;
        //grapecli
        $oAuxField = new HelperInputText("txtGrapecli","txtGrapecli");
        $oAuxField->set_value($this->get_post("txtGrapecli"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrapecli",tr_ts_fil_grapecli));
        $arFields[] = $oAuxWrapper;
        //grsapcli
        $oAuxField = new HelperInputText("txtGrsapcli","txtGrsapcli");
        $oAuxField->set_value($this->get_post("txtGrsapcli"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrsapcli",tr_ts_fil_grsapcli));
        $arFields[] = $oAuxWrapper;
        //grofiscu
        $oAuxField = new HelperInputText("txtGrofiscu","txtGrofiscu");
        $oAuxField->set_value($this->get_post("txtGrofiscu"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrofiscu",tr_ts_fil_grofiscu));
        $arFields[] = $oAuxWrapper;
        //grnmsrem
        $oAuxField = new HelperInputText("txtGrnmsrem","txtGrnmsrem");
        $oAuxField->set_value($this->get_post("txtGrnmsrem"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrnmsrem",tr_ts_fil_grnmsrem));
        $arFields[] = $oAuxWrapper;
        //grfecenv
        $oAuxField = new HelperInputText("txtGrfecenv","txtGrfecenv");
        $oAuxField->set_value($this->get_post("txtGrfecenv"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrfecenv",tr_ts_fil_grfecenv));
        $arFields[] = $oAuxWrapper;
        //grhorenv
        $oAuxField = new HelperInputText("txtGrhorenv","txtGrhorenv");
        $oAuxField->set_value($this->get_post("txtGrhorenv"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrhorenv",tr_ts_fil_grhorenv));
        $arFields[] = $oAuxWrapper;
        //grtipreg
        $oAuxField = new HelperInputText("txtGrtipreg","txtGrtipreg");
        $oAuxField->set_value($this->get_post("txtGrtipreg"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrtipreg",tr_ts_fil_grtipreg));
        $arFields[] = $oAuxWrapper;
        //grcdcall
        $oAuxField = new HelperInputText("txtGrcdcall","txtGrcdcall");
        $oAuxField->set_value($this->get_post("txtGrcdcall"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrcdcall",tr_ts_fil_grcdcall));
        $arFields[] = $oAuxWrapper;
        //grpercon
        $oAuxField = new HelperInputText("txtGrpercon","txtGrpercon");
        $oAuxField->set_value($this->get_post("txtGrpercon"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrpercon",tr_ts_fil_grpercon));
        $arFields[] = $oAuxWrapper;
        //grgirenv
        $oAuxField = new HelperInputText("txtGrgirenv","txtGrgirenv");
        $oAuxField->set_value($this->get_post("txtGrgirenv"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrgirenv",tr_ts_fil_grgirenv));
        $arFields[] = $oAuxWrapper;
        //grclave
        $oAuxField = new HelperInputText("txtGrclave","txtGrclave");
        $oAuxField->set_value($this->get_post("txtGrclave"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrclave",tr_ts_fil_grclave));
        $arFields[] = $oAuxWrapper;
        //grcorpri
        $oAuxField = new HelperInputText("txtGrcorpri","txtGrcorpri");
        $oAuxField->set_value($this->get_post("txtGrcorpri"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrcorpri",tr_ts_fil_grcorpri));
        $arFields[] = $oAuxWrapper;
        //grmarcad
        $oAuxField = new HelperInputText("txtGrmarcad","txtGrmarcad");
        $oAuxField->set_value($this->get_post("txtGrmarcad"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrmarcad",tr_ts_fil_grmarcad));
        $arFields[] = $oAuxWrapper;
        //grmoncta
        $oAuxField = new HelperInputText("txtGrmoncta","txtGrmoncta");
        $oAuxField->set_value($this->get_post("txtGrmoncta"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrmoncta",tr_ts_fil_grmoncta));
        $arFields[] = $oAuxWrapper;
        //grnorde2
        $oAuxField = new HelperInputText("txtGrnorde2","txtGrnorde2");
        $oAuxField->set_value($this->get_post("txtGrnorde2"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrnorde2",tr_ts_fil_grnorde2));
        $arFields[] = $oAuxWrapper;
        //grpagcor
        $oAuxField = new HelperInputText("txtGrpagcor","txtGrpagcor");
        $oAuxField->set_value($this->get_post("txtGrpagcor"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrpagcor",tr_ts_fil_grpagcor));
        $arFields[] = $oAuxWrapper;
        //grmonpag
        $oAuxField = new HelperInputText("txtGrmonpag","txtGrmonpag");
        $oAuxField->set_value($this->get_post("txtGrmonpag"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrmonpag",tr_ts_fil_grmonpag));
        $arFields[] = $oAuxWrapper;
        //grcdtran
        $oAuxField = new HelperInputText("txtGrcdtran","txtGrcdtran");
        $oAuxField->set_value($this->get_post("txtGrcdtran"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrcdtran",tr_ts_fil_grcdtran));
        $arFields[] = $oAuxWrapper;
        //grtipcon
        $oAuxField = new HelperInputText("txtGrtipcon","txtGrtipcon");
        $oAuxField->set_value($this->get_post("txtGrtipcon"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrtipcon",tr_ts_fil_grtipcon));
        $arFields[] = $oAuxWrapper;
        //grestado
        $oAuxField = new HelperInputText("txtGrestado","txtGrestado");
        $oAuxField->set_value($this->get_post("txtGrestado"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrestado",tr_ts_fil_grestado));
        $arFields[] = $oAuxWrapper;
        //grcausae
        $oAuxField = new HelperInputText("txtGrcausae","txtGrcausae");
        $oAuxField->set_value($this->get_post("txtGrcausae"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrcausae",tr_ts_fil_grcausae));
        $arFields[] = $oAuxWrapper;
        //grcduser
        $oAuxField = new HelperInputText("txtGrcduser","txtGrcduser");
        $oAuxField->set_value($this->get_post("txtGrcduser"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrcduser",tr_ts_fil_grcduser));
        $arFields[] = $oAuxWrapper;
        //grtippag
        $oAuxField = new HelperInputText("txtGrtippag","txtGrtippag");
        $oAuxField->set_value($this->get_post("txtGrtippag"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrtippag",tr_ts_fil_grtippag));
        $arFields[] = $oAuxWrapper;
        //grcdbanc
        $oAuxField = new HelperInputText("txtGrcdbanc","txtGrcdbanc");
        $oAuxField->set_value($this->get_post("txtGrcdbanc"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrcdbanc",tr_ts_fil_grcdbanc));
        $arFields[] = $oAuxWrapper;
        //grtipcta
        $oAuxField = new HelperInputText("txtGrtipcta","txtGrtipcta");
        $oAuxField->set_value($this->get_post("txtGrtipcta"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrtipcta",tr_ts_fil_grtipcta));
        $arFields[] = $oAuxWrapper;
        //grnrocta
        $oAuxField = new HelperInputText("txtGrnrocta","txtGrnrocta");
        $oAuxField->set_value($this->get_post("txtGrnrocta"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrnrocta",tr_ts_fil_grnrocta));
        $arFields[] = $oAuxWrapper;
        //grobserv
        $oAuxField = new HelperInputText("txtGrobserv","txtGrobserv");
        $oAuxField->set_value($this->get_post("txtGrobserv"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrobserv",tr_ts_fil_grobserv));
        $arFields[] = $oAuxWrapper;
        //grmonori
        $oAuxField = new HelperInputText("txtGrmonori","txtGrmonori");
        $oAuxField->set_value($this->get_post("txtGrmonori"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrmonori",tr_ts_fil_grmonori));
        $arFields[] = $oAuxWrapper;
        //grdrbene
        $oAuxField = new HelperInputText("txtGrdrbene","txtGrdrbene");
        $oAuxField->set_value($this->get_post("txtGrdrbene"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrdrbene",tr_ts_fil_grdrbene));
        $arFields[] = $oAuxWrapper;
        //grdrben2
        $oAuxField = new HelperInputText("txtGrdrben2","txtGrdrben2");
        $oAuxField->set_value($this->get_post("txtGrdrben2"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrdrben2",tr_ts_fil_grdrben2));
        $arFields[] = $oAuxWrapper;
        //grembene
        $oAuxField = new HelperInputText("txtGrembene","txtGrembene");
        $oAuxField->set_value($this->get_post("txtGrembene"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrembene",tr_ts_fil_grembene));
        $arFields[] = $oAuxWrapper;
        //grtlbene
        $oAuxField = new HelperInputText("txtGrtlbene","txtGrtlbene");
        $oAuxField->set_value($this->get_post("txtGrtlbene"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrtlbene",tr_ts_fil_grtlbene));
        $arFields[] = $oAuxWrapper;
        //grtlben2
        $oAuxField = new HelperInputText("txtGrtlben2","txtGrtlben2");
        $oAuxField->set_value($this->get_post("txtGrtlben2"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrtlben2",tr_ts_fil_grtlben2));
        $arFields[] = $oAuxWrapper;
        //grmensaj
        $oAuxField = new HelperInputText("txtGrmensaj","txtGrmensaj");
        $oAuxField->set_value($this->get_post("txtGrmensaj"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrmensaj",tr_ts_fil_grmensaj));
        $arFields[] = $oAuxWrapper;
        //grciudds
        $oAuxField = new HelperInputText("txtGrciudds","txtGrciudds");
        $oAuxField->set_value($this->get_post("txtGrciudds"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrciudds",tr_ts_fil_grciudds));
        $arFields[] = $oAuxWrapper;
        //grcdcorr
        $oAuxField = new HelperInputText("txtGrcdcorr","txtGrcdcorr");
        $oAuxField->set_value($this->get_post("txtGrcdcorr"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrcdcorr",tr_ts_fil_grcdcorr));
        $arFields[] = $oAuxWrapper;
        //gragends
        $oAuxField = new HelperInputText("txtGragends","txtGragends");
        $oAuxField->set_value($this->get_post("txtGragends"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGragends",tr_ts_fil_gragends));
        $arFields[] = $oAuxWrapper;
        //grtdbene
        $oAuxField = new HelperInputText("txtGrtdbene","txtGrtdbene");
        $oAuxField->set_value($this->get_post("txtGrtdbene"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrtdbene",tr_ts_fil_grtdbene));
        $arFields[] = $oAuxWrapper;
        //grcdbene
        $oAuxField = new HelperInputText("txtGrcdbene","txtGrcdbene");
        $oAuxField->set_value($this->get_post("txtGrcdbene"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrcdbene",tr_ts_fil_grcdbene));
        $arFields[] = $oAuxWrapper;
        //grnmbene
        $oAuxField = new HelperInputText("txtGrnmbene","txtGrnmbene");
        $oAuxField->set_value($this->get_post("txtGrnmbene"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrnmbene",tr_ts_fil_grnmbene));
        $arFields[] = $oAuxWrapper;
        //grciudor
        $oAuxField = new HelperInputText("txtGrciudor","txtGrciudor");
        $oAuxField->set_value($this->get_post("txtGrciudor"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrciudor",tr_ts_fil_grciudor));
        $arFields[] = $oAuxWrapper;
        //gremremi
        $oAuxField = new HelperInputText("txtGremremi","txtGremremi");
        $oAuxField->set_value($this->get_post("txtGremremi"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGremremi",tr_ts_fil_gremremi));
        $arFields[] = $oAuxWrapper;
        //grcdocup
        $oAuxField = new HelperInputText("txtGrcdocup","txtGrcdocup");
        $oAuxField->set_value($this->get_post("txtGrcdocup"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrcdocup",tr_ts_fil_grcdocup));
        $arFields[] = $oAuxWrapper;
        //grtlremi
        $oAuxField = new HelperInputText("txtGrtlremi","txtGrtlremi");
        $oAuxField->set_value($this->get_post("txtGrtlremi"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrtlremi",tr_ts_fil_grtlremi));
        $arFields[] = $oAuxWrapper;
        //grtlrem2
        $oAuxField = new HelperInputText("txtGrtlrem2","txtGrtlrem2");
        $oAuxField->set_value($this->get_post("txtGrtlrem2"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrtlrem2",tr_ts_fil_grtlrem2));
        $arFields[] = $oAuxWrapper;
        //grpaisds
        $oAuxField = new HelperInputText("txtGrpaisds","txtGrpaisds");
        $oAuxField->set_value($this->get_post("txtGrpaisds"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrpaisds",tr_ts_fil_grpaisds));
        $arFields[] = $oAuxWrapper;
        //grusmodi
        $oAuxField = new HelperInputText("txtGrusmodi","txtGrusmodi");
        $oAuxField->set_value($this->get_post("txtGrusmodi"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrusmodi",tr_ts_fil_grusmodi));
        $arFields[] = $oAuxWrapper;
        //grtdremi
        $oAuxField = new HelperInputText("txtGrtdremi","txtGrtdremi");
        $oAuxField->set_value($this->get_post("txtGrtdremi"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrtdremi",tr_ts_fil_grtdremi));
        $arFields[] = $oAuxWrapper;
        //grcdremi
        $oAuxField = new HelperInputText("txtGrcdremi","txtGrcdremi");
        $oAuxField->set_value($this->get_post("txtGrcdremi"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrcdremi",tr_ts_fil_grcdremi));
        $arFields[] = $oAuxWrapper;
        //grnmremi
        $oAuxField = new HelperInputText("txtGrnmremi","txtGrnmremi");
        $oAuxField->set_value($this->get_post("txtGrnmremi"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrnmremi",tr_ts_fil_grnmremi));
        $arFields[] = $oAuxWrapper;
        //grdrremi
        $oAuxField = new HelperInputText("txtGrdrremi","txtGrdrremi");
        $oAuxField->set_value($this->get_post("txtGrdrremi"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrdrremi",tr_ts_fil_grdrremi));
        $arFields[] = $oAuxWrapper;
        //grdrrem2
        $oAuxField = new HelperInputText("txtGrdrrem2","txtGrdrrem2");
        $oAuxField->set_value($this->get_post("txtGrdrrem2"));
        $oAuxField->on_entersubmit();
        $oAuxWrapper = new ApphelperControlGroup($oAuxField,new HelperLabel("txtGrdrrem2",tr_ts_fil_grdrrem2));
        $arFields[] = $oAuxWrapper;
        return $arFields;
    }//get_singleassign_filters()

    //singleassign_4
    protected function set_singleassignfilters_from_post()
    {
        //gridgiro
        $this->set_filter_value("gridgiro",$this->get_post("txtGridgiro"));
        //grsecueo
        $this->set_filter_value("grsecueo",$this->get_post("txtGrsecueo"));
        //gridremi
        $this->set_filter_value("gridremi",$this->get_post("txtGridremi"));
        //grsecuen
        $this->set_filter_value("grsecuen",$this->get_post("txtGrsecuen"));
        //gridbene
        $this->set_filter_value("gridbene",$this->get_post("txtGridbene"));
        //grfactur
        $this->set_filter_value("grfactur",$this->get_post("txtGrfactur"));
        //grcdcart
        $this->set_filter_value("grcdcart",$this->get_post("txtGrcdcart"));
        //grcdcont
        $this->set_filter_value("grcdcont",$this->get_post("txtGrcdcont"));
        //grnrolla
        $this->set_filter_value("grnrolla",$this->get_post("txtGrnrolla"));
        //grconsag
        $this->set_filter_value("grconsag",$this->get_post("txtGrconsag"));
        //grrembol
        $this->set_filter_value("grrembol",$this->get_post("txtGrrembol"));
        //grfechag
        $this->set_filter_value("grfechag",$this->get_post("txtGrfechag"));
        //grfeccon
        $this->set_filter_value("grfeccon",$this->get_post("txtGrfeccon"));
        //grfecing
        $this->set_filter_value("grfecing",$this->get_post("txtGrfecing"));
        //grfecmod
        $this->set_filter_value("grfecmod",$this->get_post("txtGrfecmod"));
        //grfecall
        $this->set_filter_value("grfecall",$this->get_post("txtGrfecall"));
        //grconpag
        $this->set_filter_value("grconpag",$this->get_post("txtGrconpag"));
        //grmontod
        $this->set_filter_value("grmontod",$this->get_post("txtGrmontod"));
        //grmonusd
        $this->set_filter_value("grmonusd",$this->get_post("txtGrmonusd"));
        //grtasacm
        $this->set_filter_value("grtasacm",$this->get_post("txtGrtasacm"));
        //grvlrcom
        $this->set_filter_value("grvlrcom",$this->get_post("txtGrvlrcom"));
        //grvlrajs
        $this->set_filter_value("grvlrajs",$this->get_post("txtGrvlrajs"));
        //grmontop
        $this->set_filter_value("grmontop",$this->get_post("txtGrmontop"));
        //grvlrdes
        $this->set_filter_value("grvlrdes",$this->get_post("txtGrvlrdes"));
        //grtipgir
        $this->set_filter_value("grtipgir",$this->get_post("txtGrtipgir"));
        //grcdcoro
        $this->set_filter_value("grcdcoro",$this->get_post("txtGrcdcoro"));
        //grnorden
        $this->set_filter_value("grnorden",$this->get_post("txtGrnorden"));
        //grcdagen
        $this->set_filter_value("grcdagen",$this->get_post("txtGrcdagen"));
        //grcdpais
        $this->set_filter_value("grcdpais",$this->get_post("txtGrcdpais"));
        //grcdciud
        $this->set_filter_value("grcdciud",$this->get_post("txtGrcdciud"));
        //grtipenv
        $this->set_filter_value("grtipenv",$this->get_post("txtGrtipenv"));
        //gritemid
        $this->set_filter_value("gritemid",$this->get_post("txtGritemid"));
        //gritdesc
        $this->set_filter_value("gritdesc",$this->get_post("txtGritdesc"));
        //grrelaci
        $this->set_filter_value("grrelaci",$this->get_post("txtGrrelaci"));
        //grestciv
        $this->set_filter_value("grestciv",$this->get_post("txtGrestciv"));
        //grniving
        $this->set_filter_value("grniving",$this->get_post("txtGrniving"));
        //gridsexo
        $this->set_filter_value("gridsexo",$this->get_post("txtGridsexo"));
        //grcodnac
        $this->set_filter_value("grcodnac",$this->get_post("txtGrcodnac"));
        //grtrabaj
        $this->set_filter_value("grtrabaj",$this->get_post("txtGrtrabaj"));
        //grapsrem
        $this->set_filter_value("grapsrem",$this->get_post("txtGrapsrem"));
        //grident
        $this->set_filter_value("grident",$this->get_post("txtGrident"));
        //grcodcta
        $this->set_filter_value("grcodcta",$this->get_post("txtGrcodcta"));
        //grdocpag
        $this->set_filter_value("grdocpag",$this->get_post("txtGrdocpag"));
        //grnompag
        $this->set_filter_value("grnompag",$this->get_post("txtGrnompag"));
        //grusauto
        $this->set_filter_value("grusauto",$this->get_post("txtGrusauto"));
        //grtiptas
        $this->set_filter_value("grtiptas",$this->get_post("txtGrtiptas"));
        //grnomcli
        $this->set_filter_value("grnomcli",$this->get_post("txtGrnomcli"));
        //grapecli
        $this->set_filter_value("grapecli",$this->get_post("txtGrapecli"));
        //grsapcli
        $this->set_filter_value("grsapcli",$this->get_post("txtGrsapcli"));
        //grofiscu
        $this->set_filter_value("grofiscu",$this->get_post("txtGrofiscu"));
        //grnmsrem
        $this->set_filter_value("grnmsrem",$this->get_post("txtGrnmsrem"));
        //grfecenv
        $this->set_filter_value("grfecenv",$this->get_post("txtGrfecenv"));
        //grhorenv
        $this->set_filter_value("grhorenv",$this->get_post("txtGrhorenv"));
        //grtipreg
        $this->set_filter_value("grtipreg",$this->get_post("txtGrtipreg"));
        //grcdcall
        $this->set_filter_value("grcdcall",$this->get_post("txtGrcdcall"));
        //grpercon
        $this->set_filter_value("grpercon",$this->get_post("txtGrpercon"));
        //grgirenv
        $this->set_filter_value("grgirenv",$this->get_post("txtGrgirenv"));
        //grclave
        $this->set_filter_value("grclave",$this->get_post("txtGrclave"));
        //grcorpri
        $this->set_filter_value("grcorpri",$this->get_post("txtGrcorpri"));
        //grmarcad
        $this->set_filter_value("grmarcad",$this->get_post("txtGrmarcad"));
        //grmoncta
        $this->set_filter_value("grmoncta",$this->get_post("txtGrmoncta"));
        //grnorde2
        $this->set_filter_value("grnorde2",$this->get_post("txtGrnorde2"));
        //grpagcor
        $this->set_filter_value("grpagcor",$this->get_post("txtGrpagcor"));
        //grmonpag
        $this->set_filter_value("grmonpag",$this->get_post("txtGrmonpag"));
        //grcdtran
        $this->set_filter_value("grcdtran",$this->get_post("txtGrcdtran"));
        //grtipcon
        $this->set_filter_value("grtipcon",$this->get_post("txtGrtipcon"));
        //grestado
        $this->set_filter_value("grestado",$this->get_post("txtGrestado"));
        //grcausae
        $this->set_filter_value("grcausae",$this->get_post("txtGrcausae"));
        //grcduser
        $this->set_filter_value("grcduser",$this->get_post("txtGrcduser"));
        //grtippag
        $this->set_filter_value("grtippag",$this->get_post("txtGrtippag"));
        //grcdbanc
        $this->set_filter_value("grcdbanc",$this->get_post("txtGrcdbanc"));
        //grtipcta
        $this->set_filter_value("grtipcta",$this->get_post("txtGrtipcta"));
        //grnrocta
        $this->set_filter_value("grnrocta",$this->get_post("txtGrnrocta"));
        //grobserv
        $this->set_filter_value("grobserv",$this->get_post("txtGrobserv"));
        //grmonori
        $this->set_filter_value("grmonori",$this->get_post("txtGrmonori"));
        //grdrbene
        $this->set_filter_value("grdrbene",$this->get_post("txtGrdrbene"));
        //grdrben2
        $this->set_filter_value("grdrben2",$this->get_post("txtGrdrben2"));
        //grembene
        $this->set_filter_value("grembene",$this->get_post("txtGrembene"));
        //grtlbene
        $this->set_filter_value("grtlbene",$this->get_post("txtGrtlbene"));
        //grtlben2
        $this->set_filter_value("grtlben2",$this->get_post("txtGrtlben2"));
        //grmensaj
        $this->set_filter_value("grmensaj",$this->get_post("txtGrmensaj"));
        //grciudds
        $this->set_filter_value("grciudds",$this->get_post("txtGrciudds"));
        //grcdcorr
        $this->set_filter_value("grcdcorr",$this->get_post("txtGrcdcorr"));
        //gragends
        $this->set_filter_value("gragends",$this->get_post("txtGragends"));
        //grtdbene
        $this->set_filter_value("grtdbene",$this->get_post("txtGrtdbene"));
        //grcdbene
        $this->set_filter_value("grcdbene",$this->get_post("txtGrcdbene"));
        //grnmbene
        $this->set_filter_value("grnmbene",$this->get_post("txtGrnmbene"));
        //grciudor
        $this->set_filter_value("grciudor",$this->get_post("txtGrciudor"));
        //gremremi
        $this->set_filter_value("gremremi",$this->get_post("txtGremremi"));
        //grcdocup
        $this->set_filter_value("grcdocup",$this->get_post("txtGrcdocup"));
        //grtlremi
        $this->set_filter_value("grtlremi",$this->get_post("txtGrtlremi"));
        //grtlrem2
        $this->set_filter_value("grtlrem2",$this->get_post("txtGrtlrem2"));
        //grpaisds
        $this->set_filter_value("grpaisds",$this->get_post("txtGrpaisds"));
        //grusmodi
        $this->set_filter_value("grusmodi",$this->get_post("txtGrusmodi"));
        //grtdremi
        $this->set_filter_value("grtdremi",$this->get_post("txtGrtdremi"));
        //grcdremi
        $this->set_filter_value("grcdremi",$this->get_post("txtGrcdremi"));
        //grnmremi
        $this->set_filter_value("grnmremi",$this->get_post("txtGrnmremi"));
        //grdrremi
        $this->set_filter_value("grdrremi",$this->get_post("txtGrdrremi"));
        //grdrrem2
        $this->set_filter_value("grdrrem2",$this->get_post("txtGrdrrem2"));
    }//set_singleassignfilters_from_post()

    //singleassign_5
    protected function get_singleassign_columns()
    {
        $arColumns["gridgiro"] = tr_ts_col_gridgiro;
        $arColumns["grsecueo"] = tr_ts_col_grsecueo;
        $arColumns["gridremi"] = tr_ts_col_gridremi;
        $arColumns["grsecuen"] = tr_ts_col_grsecuen;
        $arColumns["gridbene"] = tr_ts_col_gridbene;
        $arColumns["grfactur"] = tr_ts_col_grfactur;
        $arColumns["grcdcart"] = tr_ts_col_grcdcart;
        $arColumns["grcdcont"] = tr_ts_col_grcdcont;
        $arColumns["grnrolla"] = tr_ts_col_grnrolla;
        $arColumns["grconsag"] = tr_ts_col_grconsag;
        $arColumns["grrembol"] = tr_ts_col_grrembol;
        $arColumns["grfechag"] = tr_ts_col_grfechag;
        $arColumns["grfeccon"] = tr_ts_col_grfeccon;
        $arColumns["grfecing"] = tr_ts_col_grfecing;
        $arColumns["grfecmod"] = tr_ts_col_grfecmod;
        $arColumns["grfecall"] = tr_ts_col_grfecall;
        $arColumns["grconpag"] = tr_ts_col_grconpag;
        $arColumns["grmontod"] = tr_ts_col_grmontod;
        $arColumns["grmonusd"] = tr_ts_col_grmonusd;
        $arColumns["grtasacm"] = tr_ts_col_grtasacm;
        $arColumns["grvlrcom"] = tr_ts_col_grvlrcom;
        $arColumns["grvlrajs"] = tr_ts_col_grvlrajs;
        $arColumns["grmontop"] = tr_ts_col_grmontop;
        $arColumns["grvlrdes"] = tr_ts_col_grvlrdes;
        $arColumns["grtipgir"] = tr_ts_col_grtipgir;
        $arColumns["grcdcoro"] = tr_ts_col_grcdcoro;
        $arColumns["grnorden"] = tr_ts_col_grnorden;
        $arColumns["grcdagen"] = tr_ts_col_grcdagen;
        $arColumns["grcdpais"] = tr_ts_col_grcdpais;
        $arColumns["grcdciud"] = tr_ts_col_grcdciud;
        $arColumns["grtipenv"] = tr_ts_col_grtipenv;
        $arColumns["gritemid"] = tr_ts_col_gritemid;
        $arColumns["gritdesc"] = tr_ts_col_gritdesc;
        $arColumns["grrelaci"] = tr_ts_col_grrelaci;
        $arColumns["grestciv"] = tr_ts_col_grestciv;
        $arColumns["grniving"] = tr_ts_col_grniving;
        $arColumns["gridsexo"] = tr_ts_col_gridsexo;
        $arColumns["grcodnac"] = tr_ts_col_grcodnac;
        $arColumns["grtrabaj"] = tr_ts_col_grtrabaj;
        $arColumns["grapsrem"] = tr_ts_col_grapsrem;
        $arColumns["grident"] = tr_ts_col_grident;
        $arColumns["grcodcta"] = tr_ts_col_grcodcta;
        $arColumns["grdocpag"] = tr_ts_col_grdocpag;
        $arColumns["grnompag"] = tr_ts_col_grnompag;
        $arColumns["grusauto"] = tr_ts_col_grusauto;
        $arColumns["grtiptas"] = tr_ts_col_grtiptas;
        $arColumns["grnomcli"] = tr_ts_col_grnomcli;
        $arColumns["grapecli"] = tr_ts_col_grapecli;
        $arColumns["grsapcli"] = tr_ts_col_grsapcli;
        $arColumns["grofiscu"] = tr_ts_col_grofiscu;
        $arColumns["grnmsrem"] = tr_ts_col_grnmsrem;
        $arColumns["grfecenv"] = tr_ts_col_grfecenv;
        $arColumns["grhorenv"] = tr_ts_col_grhorenv;
        $arColumns["grtipreg"] = tr_ts_col_grtipreg;
        $arColumns["grcdcall"] = tr_ts_col_grcdcall;
        $arColumns["grpercon"] = tr_ts_col_grpercon;
        $arColumns["grgirenv"] = tr_ts_col_grgirenv;
        $arColumns["grclave"] = tr_ts_col_grclave;
        $arColumns["grcorpri"] = tr_ts_col_grcorpri;
        $arColumns["grmarcad"] = tr_ts_col_grmarcad;
        $arColumns["grmoncta"] = tr_ts_col_grmoncta;
        $arColumns["grnorde2"] = tr_ts_col_grnorde2;
        $arColumns["grpagcor"] = tr_ts_col_grpagcor;
        $arColumns["grmonpag"] = tr_ts_col_grmonpag;
        $arColumns["grcdtran"] = tr_ts_col_grcdtran;
        $arColumns["grtipcon"] = tr_ts_col_grtipcon;
        $arColumns["grestado"] = tr_ts_col_grestado;
        $arColumns["grcausae"] = tr_ts_col_grcausae;
        $arColumns["grcduser"] = tr_ts_col_grcduser;
        $arColumns["grtippag"] = tr_ts_col_grtippag;
        $arColumns["grcdbanc"] = tr_ts_col_grcdbanc;
        $arColumns["grtipcta"] = tr_ts_col_grtipcta;
        $arColumns["grnrocta"] = tr_ts_col_grnrocta;
        $arColumns["grobserv"] = tr_ts_col_grobserv;
        $arColumns["grmonori"] = tr_ts_col_grmonori;
        $arColumns["grdrbene"] = tr_ts_col_grdrbene;
        $arColumns["grdrben2"] = tr_ts_col_grdrben2;
        $arColumns["grembene"] = tr_ts_col_grembene;
        $arColumns["grtlbene"] = tr_ts_col_grtlbene;
        $arColumns["grtlben2"] = tr_ts_col_grtlben2;
        $arColumns["grmensaj"] = tr_ts_col_grmensaj;
        $arColumns["grciudds"] = tr_ts_col_grciudds;
        $arColumns["grcdcorr"] = tr_ts_col_grcdcorr;
        $arColumns["gragends"] = tr_ts_col_gragends;
        $arColumns["grtdbene"] = tr_ts_col_grtdbene;
        $arColumns["grcdbene"] = tr_ts_col_grcdbene;
        $arColumns["grnmbene"] = tr_ts_col_grnmbene;
        $arColumns["grciudor"] = tr_ts_col_grciudor;
        $arColumns["gremremi"] = tr_ts_col_gremremi;
        $arColumns["grcdocup"] = tr_ts_col_grcdocup;
        $arColumns["grtlremi"] = tr_ts_col_grtlremi;
        $arColumns["grtlrem2"] = tr_ts_col_grtlrem2;
        $arColumns["grpaisds"] = tr_ts_col_grpaisds;
        $arColumns["grusmodi"] = tr_ts_col_grusmodi;
        $arColumns["grtdremi"] = tr_ts_col_grtdremi;
        $arColumns["grcdremi"] = tr_ts_col_grcdremi;
        $arColumns["grnmremi"] = tr_ts_col_grnmremi;
        $arColumns["grdrremi"] = tr_ts_col_grdrremi;
        $arColumns["grdrrem2"] = tr_ts_col_grdrrem2;
        return $arColumns;
    }//get_singleassign_columns()

    //singleassign_6
    private function singleassign()
    {
        $this->go_to_401($this->oPermission->is_not_pick());
        $oAlert = new AppHelperAlertdiv();
        $oAlert->use_close_button();
        $sMessage = $this->get_session_message($sMessage);
        if($sMessage) $oAlert->set_title($sMessage);
        $sMessage = $this->get_session_message($sMessage,"e");
        if($sMessage)
        {
            $oAlert->set_type();
            $oAlert->set_title($sMessage);
        }
        //build controls and add data to global arFilterControls and arFilterFields
        $arColumns = $this->get_singleassign_columns();
        //Indica los filtros que se recuperarán. Hace un $this->arFilters = arra(fieldname=>value=>..)
        $this->load_config_singleassign_filters();
        $oFilter = new ComponentFilter();
        $oFilter->set_fieldnames($this->get_filter_fieldnames());
        //Indica que no se guardara en sesion por nombre de campo sino por nombre de control
        //para esto es necesario respetar el estricto camelcase
        $oFilter->use_field_prefix();
        //Guarda en sesion y post los campos enviados, los de orden y página
        $oFilter->refresh();
        $this->set_singleassignfilters_from_post();
        $arObjFilter = $this->get_singleassign_filters();
        $this->oTabgiros->set_orderby($this->get_orderby());
        $this->oTabgiros->set_ordertype($this->get_ordertype());
        $this->oTabgiros->set_filters($this->get_filter_searchconfig());
        $arList = $this->oTabgiros->get_select_all_ids();
        $iRequestPage = $this->get_post("selPage");
        $oPage = new ComponentPage($arList,$iRequestPage);
        $arList = $oPage->get_items_to_show();
        $arList = $this->oTabgiros->get_select_all_by_ids($arList);
        //TABLE
        $oTableAssign = new HelperTableTyped($arList,$arColumns);
        $oTableAssign->set_fields($arObjFilter);
        $oTableAssign->set_module($this->get_current_module());
        $oTableAssign->add_class("table table-striped table-bordered table-condensed");
        $oTableAssign->set_keyfields(array("gridgiro"));
        $oTableAssign->set_orderby($this->get_orderby());
        $oTableAssign->set_orderby_type($this->get_ordertype());
        $oTableAssign->set_column_picksingle();
        $oTableAssign->set_singleadd(array("destkey"=>"txtCode","destdesc"=>"Desc","keys"=>"gridgiro","descs"=>"description,bo_login","close"=>1));
        $oTableAssign->set_current_page($oPage->get_current());
        $oTableAssign->set_next_page($oPage->get_next());
        $oTableAssign->set_first_page($oPage->get_first());
        $oTableAssign->set_last_page($oPage->get_last());
        $oTableAssign->set_total_regs($oPage->get_total_regs());
        $oTableAssign->set_total_pages($oPage->get_total());
        //BARRA CRUD
        $oOpButtons = new AppHelperButtontabs(tr_ts_entities);
        $oOpButtons->set_tabs($this->build_singleassign_buttons());
        //JAVASCRIPT
        $oJavascript = new HelperJavascript();
        $oJavascript->set_filters($this->get_filter_controls_id());
        $oJavascript->set_focusid("id_all");
        //TO VIEW
        $this->oView->add_var($oJavascript,"oJavascript");
        $this->oView->set_layout("onecolumn");
        $this->oView->add_var($oOpButtons,"oOpButtons");
        $this->oView->add_var($oAlert,"oAlert");
        $this->oView->add_var($oTableAssign,"oTableAssign");
        $this->oView->show_page();
    }//singleassign()
    //</editor-fold>

}//end controller
