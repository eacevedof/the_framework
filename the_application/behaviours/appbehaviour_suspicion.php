<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.3
 * @name AppBehaviourSuspicion
 * @file appbehaviour_report.php   
 * @date 28-06-2014 00:48 (SPAIN)
 * @observations: 
 * @requires:
 */
class AppBehaviourSuspicion extends TheApplicationBehaviour
{
    protected $iIdSuspicion;
    protected $iIdInvolved;
    
    public function __construct($idSuspicion) 
    {
        //ruta logs,objeto query,objeto db, objeto log
        parent::__construct();
        $this->iIdSuspicion = $idSuspicion;
        $this->_id_language = "3";//Dutch
    }
    
    public function get_data_head()
    {
        $this->oQuery->set_comment("AppBehaviourSuspicion.get_data_head");
        //$this->oQuery->set_fields("id,code_erp,,description,,id_isr,id_transfer,path_logo,user_creation");
        $this->oQuery->set_fields("office_name,number,CONVERT(TEXT,observations) AS observations");
        $this->oQuery->add_fields("date_creation,hour_creation,type_char,amount,amount_cash,filial_name");
        $this->oQuery->add_fields("susarlg.description AS status");
        $this->oQuery->add_fields("susar.id_tosave AS idstatus");
        $this->oQuery->set_fromtables("app_suspicion_head");
        $this->oQuery->add_joins("LEFT JOIN app_suspicion_array AS susar 
                                  ON status=susar.id ");
        $this->oQuery->add_joins("LEFT JOIN app_suspicion_array_lang AS susarlg 
                                 ON status=susarlg.id_source AND susarlg.id_language=$this->_id_language");
        $this->oQuery->set_where("app_suspicion_head.id=$this->iIdSuspicion");
        $sSQL = $this->oQuery->get_select();
        //bug($sSQL);die;
        $arRows = $this->query($sSQL,1);
        return $arRows;
    }
    
    public function get_data_head1()
    {
        if($this->iIdSuspicion)
        {    
            $sSQL = "-- AppBehaviourSuspicion.get_data_head1 
            SELECT susar.id
            ,susar.code_erp AS code
            ,CONVERT(TEXT,lang.description) AS description
            ,susdet.id_type
            ,susdet.type
            FROM app_suspicion_array AS susar
            LEFT JOIN app_suspicions_details AS susdet
            ON susar.type=susdet.type AND susar.type='head1'            
            AND susdet.id_type=susar.id
            AND susdet.id_suspicion=$this->iIdSuspicion    
            -- traduccion
            LEFT JOIN app_suspicion_array_lang AS lang
            ON susar.id = lang.id_source AND lang.id_language=$this->_id_language
            WHERE 1=1
            AND susar.type='head1' 
            ORDER BY susar.order_by,susar.description ASC
            ";
            $arRows = $this->query($sSQL);
        }
        //bug($arRows,$sSQL);die;
        return $arRows;
    }
    
    public function get_data_head2()
    {
        if($this->iIdSuspicion)
        {    
            $sSQL = "-- AppBehaviourSuspicion.get_data_head2 
            SELECT susar.id
            ,susar.code_erp AS code
            ,CONVERT(TEXT,lang.description) AS description
            ,susdet.id_type
            ,susdet.type
            FROM app_suspicion_array AS susar
            LEFT JOIN app_suspicions_details AS susdet
            ON susar.type=susdet.type AND susar.type='head2'
            AND susdet.id_type=susar.id
            AND susdet.id_suspicion=$this->iIdSuspicion
            -- traduccion
            LEFT JOIN app_suspicion_array_lang AS lang
            ON susar.id = lang.id_source AND lang.id_language=$this->_id_language                
            WHERE 1=1
            AND susar.type='head2'
            ORDER BY susar.order_by,susar.description ASC
            ";
            $arRows = $this->query($sSQL);
        }
        return $arRows;
    }
    
    public function get_data_head3()
    {
        if($this->iIdSuspicion)
        {    
            $sSQL = "-- AppBehaviourSuspicion.get_data_head3 
            SELECT susar.id
            ,CONVERT(TEXT,susar.description) AS description
            ,susdet.id_type
            FROM app_suspicion_array AS susar
            LEFT JOIN app_suspicions_details AS susdet
            ON susar.type=susdet.type AND susar.type='head3'
            AND susdet.id_type=susar.id
            AND susdet.id_suspicion=$this->iIdSuspicion
            -- sin traduccion, son ordinales
            -- LEFT JOIN app_suspicion_array_lang AS lang
            -- ON susar.id = lang.id_source AND lang.id_language=$this->_id_language                  
            WHERE 1=1
            AND susar.type='head3'
            ORDER BY susar.order_by,susar.description ASC
            ";
            $arRows = $this->query($sSQL);
        }
        return $arRows;
    }
    
    public function get_data_head4()
    {
        if($this->iIdSuspicion)
        {    
            $sSQL = "-- AppBehaviourSuspicion.get_data_head4 
            SELECT susar.id
            ,CONVERT(TEXT,susar.description) AS description
            ,susdet.id_type
            FROM app_suspicion_array AS susar
            LEFT JOIN app_suspicions_details AS susdet
            ON susar.type=susdet.type AND susar.type='head4'
            AND susdet.id_type=susar.id
            AND susdet.id_suspicion=$this->iIdSuspicion
            -- sin traduccion, son ordinales
            -- LEFT JOIN app_suspicion_array_lang AS lang
            -- ON susar.id = lang.id_source AND lang.id_language=$this->_id_language                
            WHERE 1=1
            AND susar.type='head4'
            ORDER BY susar.order_by,susar.description ASC
            ";
            $arRows = $this->query($sSQL);
        }
        return $arRows;
    }    
    
    public function get_data_by_type()
    {
        
    }

    /**
     * Devuelve un array con los nombres de los campos pero guardando cadenas finales a imprimir en el
     * PDF. 
     * @return array de filas sin códigos
     */
    public function get_involved()
    {
        if($this->iIdSuspicion)
        {    
            $sSQL = "-- AppBehaviourSuspicion.get_involved() 
            SELECT 
            inv.id,number
            ,nr_account,nr_creditcard,nr_mtcn,nr_police,nr_brief,nr_client,nr_extra,subj_name
            ,subj_infix1,subj_married_name,subj_infix2,subj_initials,subj_full_name
            --,subj_type_sex 
            ,arsex.description AS subj_type_sex
            ,subj_birthdate,subj_birthplace,subj_birth_country
            --,subj_type_nation -- Gentilicio
            ,gentilic.description AS subj_type_nation
            --,subj_type_profession 
            ,arprofession.description AS subj_type_profession
            ,iddoc
            --,iddoc_type 
            ,ardocument.description AS iddoc_type
            ,iddoc_issue_date,iddoc_expiry_date,iddoc_issue_place,iddoc_issue_country
            
            ,gentilic2.description AS iddoc_nationality -- Gentilicio DEPENDIENTE DE SUBJ_TYPE_NATION
            
            ,address,address_nr,address_chars,address_place,address_zip
            --,address_type_country
            ,arcountry2.description AS address_type_country
            ,phone
            --,phone_type
            ,phone.description AS phone_type
            --,phone_type_country
            ,arcountry3.description AS phone_type_country
            FROM app_suspicion_involved AS inv
            LEFT JOIN
            (
                SELECT ar.id, CONVERT(TEXT,lang.description) AS description
                FROM app_suspicion_array AS ar
                LEFT JOIN app_suspicion_array_lang AS lang
                ON ar.id = lang.id_source AND lang.id_language=$this->_id_language
                WHERE ar.type='sex'
            )
            AS arsex
            ON inv.subj_type_sex = arsex.id
            
            LEFT JOIN 
            (
                -- SACO EL Gentilicio TRADUCIDO
                SELECT gen.id_country, lang.description
                FROM app_gentilic AS gen
                LEFT JOIN app_gentilic_lang AS lang
                ON gen.id = lang.id_source
                AND lang.id_language=$this->_id_language
                AND gen.id_country IS NOT NULL
            )AS gentilic
            ON inv.subj_type_nation = gentilic.id_country

            LEFT JOIN 
            (
                -- SACO EL Gentilicio TRADUCIDO
                SELECT gen.id_country, lang.description
                FROM app_gentilic AS gen
                LEFT JOIN app_gentilic_lang AS lang
                ON gen.id = lang.id_source
                AND lang.id_language=$this->_id_language
                AND gen.id_country IS NOT NULL
            )AS gentilic2
            ON inv.iddoc_nationality = gentilic2.id_country
            
            LEFT JOIN 
            (
                SELECT ar.id, CONVERT(TEXT,lang.description) AS description
                FROM app_suspicion_array AS ar
                LEFT JOIN app_suspicion_array_lang AS lang
                ON ar.id = lang.id_source AND lang.id_language=$this->_id_language 
                WHERE ar.type='profession'            
            )AS arprofession
            ON inv.subj_type_profession = arprofession.id
            
            LEFT JOIN 
            (
                SELECT ar.id, CONVERT(TEXT,lang.description) AS description
                FROM app_suspicion_array AS ar
                LEFT JOIN app_suspicion_array_lang AS lang
                ON ar.id = lang.id_source AND lang.id_language=$this->_id_language 
                WHERE ar.type='iddocument'              
            )AS ardocument
            ON inv.iddoc_type = ardocument.id
            
            LEFT JOIN
            (
                SELECT ar.id, lang.description
                FROM app_country AS ar
                LEFT JOIN app_country_lang AS lang
                ON ar.id = lang.id_source AND lang.id_language=$this->_id_language              
            )AS arcountry2
            ON inv.address_type_country = arcountry2.id
            
            LEFT JOIN
            (
                SELECT ar.id, CONVERT(TEXT,lang.description) AS description
                FROM app_suspicion_array AS ar
                LEFT JOIN app_suspicion_array_lang AS lang
                ON ar.id = lang.id_source AND lang.id_language=$this->_id_language 
                WHERE ar.type='phone'              
            ) AS phone
            ON inv.phone_type = phone.id
            LEFT JOIN 
            (
                SELECT ar.id, lang.description
                FROM app_country AS ar
                LEFT JOIN app_country_lang AS lang
                ON ar.id = lang.id_source AND lang.id_language=$this->_id_language            
            )AS arcountry3
            ON inv.phone_type_country = arcountry3.id
            WHERE 1=1 
            AND id_suspicion=$this->iIdSuspicion
            ORDER BY number ASC
            ";
            $arRows = $this->query($sSQL);
        }
        return $arRows;        
    }//get_involved
    
    public function get_involved_details($sType="involved1")
    {
        if($this->iIdInvolved)
        {    
            $sSQL = "-- AppBehaviourSuspicion.get_involved_details 
            SELECT susar.id
            ,CONVERT(TEXT,susar.description) AS description
            ,susdet.id_type
            FROM app_suspicion_array AS susar
            LEFT JOIN app_suspicions_involved_details AS susdet
            ON susar.type=susdet.type 
            AND susdet.id_type=susar.id
            AND susdet.id_involved=$this->iIdInvolved
            WHERE 1=1
            AND susar.type='$sType'
            ORDER BY susar.order_by,susar.description ASC
            ";
            $arRows = $this->query($sSQL);
        }
        return $arRows;        
    }//get_involved_details

    
    //**********************************
    //             SETS
    //**********************************
    public function set_id_suspicion($iValue){$this->iIdSuspicion = $iValue;}
    public function set_id_involved($iValue){$this->iIdInvolved = $iValue;}
    //**********************************
    //             GETS
    //**********************************
    
    //**********************************
    //           MAKE PUBLIC
    //**********************************
}
