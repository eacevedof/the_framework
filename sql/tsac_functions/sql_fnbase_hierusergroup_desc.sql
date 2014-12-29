--Localizacion: bd->programación->Funciones->Funciones con valores de tabla
--CREATE FUNCTION dbo.fnbase_hierusergroup_desc(@id_group NUMERIC(18,0))
ALTER FUNCTION dbo.fnbase_hierusergroup_desc(@id_group NUMERIC(18,0))
    RETURNS TABLE
AS
    RETURN
    (
        -- CTE: Common Table Expression
        WITH ctegroup (id, id_group_parent)
        AS
        (
            SELECT id, id_group_parent
            FROM base_user_group
            WHERE 1=1 
            --AND id_group_parent IS NOT NULL
            AND delete_date IS NULL
            AND is_enabled=1
            --AND id = @id_group

            UNION ALL -- debe usarse UNION ALL no permite UNION sin ALL

            -- Recursividad
            SELECT ug.id, ug.id_group_parent
            FROM base_user_group AS ug 
            INNER JOIN ctegroup AS cte
            --ON ug.id = cte.id_group_parent /*devuelve quienes están por encima el incluido*/
            ON ug.id_group_parent = cte.id /*devuelve quienes están por debajo el incluido*/
            AND ug.delete_date IS NULL
            AND ug.is_enabled=1
        )-- Fin ctegroup
        SELECT id,id_group_parent
        FROM  ctegroup 
    )--Fin return
GO
    
SELECT id, id_group_parent
FROM fnbase_hierusergroup_desc(1)
GO

SELECT id, id_group_parent
FROM fnbase_hierusergroup_desc(6)
GO
