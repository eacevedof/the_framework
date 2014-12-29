/*QUE GRUPOS ESTÁN POR DEBAJO DE id_superior*/
--CREATE VIEW vapp_hierseller AS
ALTER VIEW vapp_hierseller AS
-- CTE: Common Table Expression. Es importante que el nombre del cte sea único en toda la bd
WITH cteseller 
AS
(
    SELECT id AS id_seller
    , id_superior
    FROM app_seller
    WHERE delete_date IS NULL
    AND is_enabled=1

    UNION ALL -- debe usarse UNION ALL no permite UNION sin ALL

    -- Recursividad. El campo 2 cteseller.id_superior es la clave
    SELECT sel.id AS id_seller
    , cteseller.id_superior
    FROM app_seller AS sel 
    INNER JOIN cteseller 
    --ON sel.id = cte.id_superior /*devuelve quienes están por encima el incluido*/
    ON sel.id_superior = cteseller.id_seller /*devuelve quienes están por debajo el incluido*/
)-- Fin cteseller
SELECT DISTINCT id_seller, id_superior
FROM  cteseller 


UNION

SELECT id AS id_seller, id_superior
FROM app_seller

UNION

SELECT id AS id_seller, id AS id_superior
FROM app_seller
GO

SELECT *
FROM vapp_hierseller
WHERE id_superior=2
GO

SELECT *
FROM vapp_hierseller
WHERE id_seller=2
GO