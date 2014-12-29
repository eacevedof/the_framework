--Localizacion: bd->programación->Funciones->Funciones con valores de tabla
--CREATE FUNCTION dbo.fnapp_hierseller_desc(@id_seller NUMERIC(18,0))
ALTER FUNCTION dbo.fnapp_hierseller_desc(@id_seller NUMERIC(18,0)=-1)
    RETURNS TABLE
AS
    RETURN
    (
        -- CTE: Common Table Expression
        WITH ctegroup (id, id_superior)
        AS
        (
            SELECT id, id_superior
            FROM app_seller
            WHERE 1=1 
            --AND id_superior IS NOT NULL
            AND delete_date IS NULL
            AND is_enabled=1
            AND id = @id_seller

            UNION ALL -- debe usarse UNION ALL no permite UNION sin ALL

            -- Recursividad
            SELECT sel.id, sel.id_superior
            FROM app_seller AS sel 
            INNER JOIN ctegroup AS cte
            --ON sel.id = cte.id_superior /*devuelve quienes están por encima el incluido*/
            ON sel.id_superior = cte.id /*devuelve quienes están por debajo el incluido*/
            AND sel.delete_date IS NULL
            AND sel.is_enabled=1
        )-- Fin ctegroup
        SELECT id,id_superior
        FROM  ctegroup 
    )--Fin return
GO
    
SELECT id, id_superior
FROM fnapp_hierseller_desc(1)
GO

SELECT id, id_superior
FROM fnapp_hierseller_desc(6)
GO
