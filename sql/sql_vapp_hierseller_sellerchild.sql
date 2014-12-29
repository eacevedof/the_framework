/*QUE VENDEDORES ESTÁN POR DEBAJO DEL VENDEDOR SE INCLUYE EL MISMO (por la vista recursiva)*/
--CREATE VIEW vapp_hierseller_sellerchild AS
ALTER VIEW vapp_hierseller_sellerchild AS

SELECT sel.id AS id_seller, vhsel.id_seller AS id_seller_child
FROM app_seller AS sel
INNER JOIN vapp_hierseller AS vhsel
ON sel.id = vhsel.id_superior
--ORDER BY 1, 2

GO

SELECT *
FROM vapp_hierseller_sellerchild
WHERE id_seller=2
GO

SELECT *
FROM vapp_hierseller_sellerchild
WHERE id_seller_child=2
GO