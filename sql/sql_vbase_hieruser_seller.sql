/*QUE VENDEDORES ESTAN DISPONIBLES PARA EL USUARIO. 
SE APLICA LA JERARQUIA VERTICAL A NIVEL DE GRUPOS Y VENDEDORES*/
--CREATE VIEW vbase_hieruser_seller AS
ALTER VIEW vbase_hieruser_seller AS

SELECT DISTINCT 
usrsel.id_user --el usuario que tiene asignado el vendedor
--, vhusrchild.id_user_child --los usuarios hijos que puede ver
, vhselchild.id_seller_child AS id_seller --los vendedores relacionados con los usuarios hijos que puede ver
FROM base_users_sellers AS usrsel
/*En base a la jerarquia de grupos muestra todos los usuarios
que están por debajo de el y el mismo*/
INNER JOIN vbase_hieruser_userchild AS vhusrchild
ON usrsel.id_user = vhusrchild.id_user
/*los vededores que estan a su cargo y el mismo. 
Si no fuera superior de nadie solo se muestra el*/
INNER JOIN vapp_hierseller_sellerchild AS vhselchild
ON usrsel.id_seller = vhselchild.id_seller
GO

SELECT *
FROM vbase_hieruser_seller
WHERE id_user=6
GO

SELECT *
FROM vbase_hieruser_seller
WHERE id_seller=4
GO