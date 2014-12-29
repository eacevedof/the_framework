SELECT
so.name AS Tabla
,sc.name AS Columna
,st.name AS Tipo
,sc.max_length AS Tamaño
,CASE     
    WHEN pks.constraint_type  IS NULL THEN ''
    ELSE 'Y'
 END AS ispk
FROM sys.objects so 
INNER JOIN sys.columns sc 
ON so.object_id = sc.object_id 
INNER JOIN sys.types st 
ON st.system_type_id = sc.system_type_id 
AND st.name != 'sysname'
LEFT JOIN 
(
    --PKS
    SELECT K.table_name
    ,C.constraint_type
    ,K.column_name
    ,K.constraint_name
    --SELECT K.TABLE_NAME, C.CONSTRAINT_TYPE, K.COLUMN_NAME, K.CONSTRAINT_NAME
    FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS AS C
    JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE AS K
    ON C.TABLE_NAME = K.TABLE_NAME
    AND C.CONSTRAINT_CATALOG = K.CONSTRAINT_CATALOG
    AND C.CONSTRAINT_SCHEMA = K.CONSTRAINT_SCHEMA
    AND C.CONSTRAINT_NAME = K.CONSTRAINT_NAME
    WHERE C.CONSTRAINT_TYPE = 'PRIMARY KEY'
    --ORDER BY K.TABLE_NAME, C.CONSTRAINT_TYPE, K.CONSTRAINT_NAME	
)
AS pks
ON pks.table_name = so.name
AND pks.column_name = sc.name
WHERE so.type = 'U' --Tablas creadas por el usuario
AND so.name LIKE '%app_%'
--AND so.name NOT IN (SELECT table_name FROM core_tables)
ORDER BY so.name, sc.name


SELECT so.name AS tablename
, sc.name AS columnname
, so.type AS createtype
FROM sysobjects so 
INNER JOIN syscolumns sc
ON so.id = sc.id
WHERE 1=1
AND so.xtype = 'U' -- por el usuario
AND so.name IN --Column name
(
    '','','','','','','','',''
)
--and sc.name like '%social%'
ORDER BY so.name, sc.name
