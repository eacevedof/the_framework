TRUNCATE TABLE base_users_sellers
GO

INSERT INTO base_users_sellers
([insert_platform],[insert_user],[insert_date],[update_user],[update_date]
,[delete_user],[delete_date],[is_erpsent],[is_enabled]
,[code_erp]
,[description],[id_user],[id_seller]
)

SELECT 
'1',1,CAST((CONVERT(CHAR(8), GETDATE(), 112)+ REPLACE(CONVERT(CHAR(8),GETDATE(), 114),':','')) AS CHAR(14))
,1,CAST((CONVERT(CHAR(8), GETDATE(), 112)+ REPLACE(CONVERT(CHAR(8),GETDATE(), 114),':','')) AS CHAR(14))
,NULL,NULL,0,1
,'' AS code_erp
, usr.description+' - '+sel.description AS description
, usr.id AS id_user
, sel.id AS id_seller
FROM base_user AS usr
CROSS JOIN app_seller AS sel

GO

SELECT * FROM base_users_sellers
GO

SELECT [is_enabled]
,[code_erp]
,[description],[id_user],[id_seller] 
FROM base_users_sellers
GO