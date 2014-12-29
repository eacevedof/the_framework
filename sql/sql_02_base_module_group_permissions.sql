TRUNCATE TABLE base_modules_permissions
GO

INSERT INTO base_modules_permissions
([insert_platform],[insert_user],[insert_date],[update_user],[update_date]
,[delete_user],[delete_date],[is_erpsent],[is_enabled]
,[code_erp]
,[description],[id_user_group],[id_module]
)

SELECT 
'1',1,CAST((CONVERT(CHAR(8), GETDATE(), 112)+ REPLACE(CONVERT(CHAR(8),GETDATE(), 114),':','')) AS CHAR(14))
,1,CAST((CONVERT(CHAR(8), GETDATE(), 112)+ REPLACE(CONVERT(CHAR(8),GETDATE(), 114),':','')) AS CHAR(14))
,NULL,NULL,0,1
,'' AS code_erp
,usrgp.description+' - '+modl.description AS description
, usrgp.id AS id_user_group
, modl.id AS id_module

FROM base_user_group AS usrgp
CROSS JOIN base_module AS modl
GO

SELECT * FROM base_modules_permissions
GO

SELECT [is_enabled]
,[code_erp]
,[id_user_group],[id_module]
,[description]
FROM base_modules_permissions
GO