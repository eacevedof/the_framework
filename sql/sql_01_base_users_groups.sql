TRUNCATE TABLE base_users_groups
GO

INSERT INTO base_users_groups
([insert_platform],[insert_user],[insert_date],[update_user],[update_date]
,[delete_user],[delete_date],[is_erpsent],[is_enabled]
,[code_erp]
,[description],[id_user],[id_user_group]
)

SELECT 
'1',1,CAST((CONVERT(CHAR(8), GETDATE(), 112)+ REPLACE(CONVERT(CHAR(8),GETDATE(), 114),':','')) AS CHAR(14))
,1,CAST((CONVERT(CHAR(8), GETDATE(), 112)+ REPLACE(CONVERT(CHAR(8),GETDATE(), 114),':','')) AS CHAR(14))
,NULL,NULL,0,1
,'' AS code_erp
, usr.description+' - '+usrgp.description AS description
, usr.id AS id_user
, usrgp.id AS id_user_group
FROM base_user AS usr
CROSS JOIN base_user_group AS usrgp

GO

SELECT * FROM base_users_groups
GO

SELECT [is_enabled]
,[code_erp]
,[description],[id_user],[id_user_group] 
FROM base_users_groups
GO