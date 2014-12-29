/*
VERSION 1.0.0
*/
SELECT *
FROM 
(
    SELECT 
    DISTINCT
    objects.name AS TableName
    --, objects.name +'.'+ Columnas.name+',' AS codigo 
    -- DATOS DE TIPOS DE SISTEMA
    ,Columnas.xtype AS codtipo-- 56 int, 167 varchar, 175 char
    --,Tipos.Name AS tipo --varchar char int numeric
    --,Columnas.length AS tamano
    -- ARRAY TYPE AND LEN
    ,'array("'+Tipos.Name+'"=>"'+CONVERT(VARCHAR,Columnas.length)+'"),' AS type_len
    --VARS
    ,'private $_'+LOWER(Columnas.name)+'; //'+Tipos.Name+'('+CONVERT(VARCHAR,Columnas.length)+')' AS vars
    -- CONSTRUCT PARAMS
    ,CASE WHEN Tipos.Name IN ('int','numeric','float','real') THEN '$'+LOWER(Columnas.name)+'=NULL,'
          ELSE '$'+LOWER(Columnas.name)+'=NULL,' 
     END AS constructor
    -- PARAMS ASSIGN
    ,'if($'+LOWER(Columnas.name)+'!=NULL) $this->_'+LOWER(Columnas.name)+' = $'+LOWER(Columnas.name)+';' AS construct_assign_params
    ,CASE WHEN Tipos.Name IN ('int','numeric','real','float') THEN '$'+LOWER(Columnas.name)+' = mssqlclean($this->_'+LOWER(Columnas.name)+',1);'
        ELSE '$'+LOWER(Columnas.name)+' = mssqlclean($this->_'+LOWER(Columnas.name)+');'
     END AS cleandata
     --INSERT FIELDS
    ,LOWER(Columnas.name)+',' AS insert_fields
     --INSERT VALUES
    ,CASE WHEN Tipos.Name IN ('int','numeric','float','real') THEN '$'+LOWER(Columnas.name)+',' 
        ELSE '''$'+LOWER(Columnas.name)+''',' 
     END AS insert_values
    --GETTERS
    ,'public function get_'+LOWER(Columnas.name)+'(){ return $this->_'+LOWER(Columnas.name)+'; }' AS getters
    --SETTERS
    ,'public function set_'+LOWER(Columnas.name)+'($value){ $this->_'+LOWER(Columnas.name)+' = $value; }' AS setters
    --FUNCTIONS LOAD
    ,'public function load_by_'+LOWER(Columnas.name)+'(){ }' AS loaders
    --SELECT *
    ,LOWER(Columnas.name)+',' AS [select_*]
    -- ASSIGN ATTRIBUTES FROM ARRAY
    ,'$this->_'+LOWER(Columnas.name)+' = $arRow["'+LOWER(Columnas.name)+'"];' AS load_by
    -- AR DATA FROM POST
    ,'$arData'+REPLACE(LOWER(Objects.name),'_','')+'["'+LOWER(Columnas.name)+'"] = $o'+REPLACE(Objects.name,'_','')+'->get_'+LOWER(Columnas.name)+'();' AS controller_load_data_array
    --SETTING OBJET IN CONTROLLER
    ,'$o'+REPLACE(Objects.name,'_','')+'->set_'+LOWER(Columnas.name)+'($value);' AS controller_set

    -- ASSIGN FROM ADD NEW POST
    ,'$o'+REPLACE(Objects.name,'_','')+'->set_'+LOWER(Columnas.name)+'('+'$_POST["ins_'+LOWER(Columnas.name)+'"]);' AS set_from_ins_post

    -- ASSIGN FROM EDIT POST
    ,'$o'+REPLACE(Objects.name,'_','')+'->set_'+LOWER(Columnas.name)+'('+'$_POST["upd_'+LOWER(Columnas.name)+'"]);' AS set_from_upd_post

    -- AR DATA FROM POST
    ,CASE WHEN Tipos.Name IN ('int','numeric','real','float') THEN '$arData'+REPLACE(LOWER(Objects.name),'_','')+'["'+LOWER(Columnas.name)+'"] = 0;' 
          ELSE '$arData'+REPLACE(LOWER(Objects.name),'_','')+'["'+LOWER(Columnas.name)+'"] = "";'
     END AS data_array
    --,'''$this->_'+LOWER(Columnas.name)+''',' AS insert_val

    ,CASE WHEN Tipos.Name IN ('int','numeric','real','float') THEN LOWER(Columnas.name)+' = $this->_'+LOWER(Columnas.name)+','
        ELSE LOWER(Columnas.name)+' = ''$this->_'+LOWER(Columnas.name)+''','
     END AS update_query

    FROM syscolumns AS Columnas
    ,sysobjects AS Objects
    ,systypes AS Tipos

    WHERE 1=1
    AND Columnas.ID = Objects.ID 
    AND Columnas.xtype=Tipos.xtype
    AND Objects.name = 'base_user'
    /**
    AND Columnas.name NOT IN
    ('insert_user','delete_user','id','update_user','code_erp','insert_date','delete_date'
     ,'enabled','is_erpsent','update_date','insert_platform','processflag')
     --*/

)AS models
ORDER BY codtipo ASC, vars ASC

/*-- concatenar filas
--http://sqlandme.com/2011/04/27/tsql-concatenate-rows-using-for-xml-path/
SELECT  
    STUFF
    (
        ( 
            SELECT  ',' + CL.CITIPDOC AS [text()]
            FROM clientes CL
            FOR XML PATH('') 
        )
    , 1, 1, '' 
    ) AS X
*/