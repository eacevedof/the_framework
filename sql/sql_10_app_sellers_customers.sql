TRUNCATE TABLE app_sellers_customers
GO

INSERT INTO app_sellers_customers
([insert_platform],[insert_user],[insert_date],[update_user],[update_date]
,[delete_user],[delete_date],[is_erpsent],[is_enabled]
,[code_erp]
,[description],[id_seller],[id_customer]
)

SELECT 
'1',1,CAST((CONVERT(CHAR(8), GETDATE(), 112)+ REPLACE(CONVERT(CHAR(8),GETDATE(), 114),':','')) AS CHAR(14))
,1,CAST((CONVERT(CHAR(8), GETDATE(), 112)+ REPLACE(CONVERT(CHAR(8),GETDATE(), 114),':','')) AS CHAR(14))
,NULL,NULL,0,1
,'' AS code_erp
, sel.description+' - '+cust.description AS description
, sel.id AS id_seller
, cust.id AS id_customer
FROM app_seller AS sel
CROSS JOIN app_customer AS cust

GO

SELECT * FROM app_sellers_customers
GO

SELECT [is_enabled]
,[code_erp]
,[description],[id_seller],[id_customer] 
FROM app_sellers_customers
GO