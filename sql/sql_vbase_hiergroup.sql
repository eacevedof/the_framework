/*QUE GRUPOS ESTÁN POR DEBAJO DE id_group_parent*/
--CREATE VIEW vbase_hiergroup AS
ALTER VIEW vbase_hiergroup AS
-- CTE: Common Table Expression. Es importante que el nombre del cte sea único en toda la bd
/*
Esta vista con cada hilo recursivo crea id_group con el id_group_padre del padre_original
*/
WITH cteusergroup 
AS
(
    SELECT id AS id_group
    , id_group_parent
    FROM base_user_group
    WHERE delete_date IS NULL
    AND is_enabled=1

    UNION ALL -- debe usarse UNION ALL no permite UNION sin ALL

    -- Recursividad. El campo 2 cteusergroup.id_group_parent es la clave

    SELECT ug.id AS id_group
    , cteusergroup.id_group_parent /*importante q tire de la tabla recursiva*/
    --, cteusergroup.id_group /*importante q tire de la tabla recursiva*/
    FROM base_user_group AS ug 
    INNER JOIN cteusergroup 
    --ON ug.id = cteusergroup.id_group_parent /*devuelve quienes están por encima el incluido*/
    ON ug.id_group_parent = cteusergroup.id_group /*devuelve quienes están por debajo el incluido*/
)-- Fin cteusergroup
SELECT DISTINCT id_group, id_group_parent
FROM  cteusergroup 
--WHERE id_group_parent NOT IN (1)

UNION

-- El padre inicial configurado
SELECT id AS id_group, id_group_parent 
FROM base_user_group

UNION

-- El mismo grupo como padre e hijo.
SELECT id AS id_group, id AS id_group_parent
FROM base_user_group
--OPTION (MAXRECURSION 1000) --OPTION NO SE PUEDE USAR EN ISTAS
GO

SELECT *
FROM vbase_hiergroup
WHERE id_group_parent=4
GO

SELECT *
FROM vbase_hiergroup
WHERE id_group=4
GO