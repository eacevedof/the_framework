/* QUE CLIENTES PUEDE VER EL USUARIO */
--CREATE VIEW vbase_hieruser_customer AS
ALTER VIEW vbase_hieruser_customer AS

SELECT DISTINCT 
vbhusrsel.id_user 
, cus.id AS id_customer
FROM vbase_hieruser_seller AS vbhusrsel
INNER JOIN app_customer AS cus
ON vbhusrsel.id_seller = cus.id_seller
GO

SELECT *
FROM vbase_hieruser_customer
WHERE id_user=6
GO

SELECT *
FROM vbase_hieruser_customer
WHERE id_customer=4
GO