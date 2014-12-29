-- v 1.0.1
-- pasado de ISNULL a COALESCE para compatibilidad con mysql
-- CREATE VIEW vapp_balance AS
ALTER VIEW vapp_balance AS

-- Ingresos sin gastos
SELECT in_date,in_total
,COALESCE(out_total,0) AS out_total
,in_total-COALESCE(out_total,0) AS total
FROM
(
    -- vapp_balancesub_1
    SELECT operation_date AS in_date
    , SUM(total_in) AS in_total 
    FROM app_balance_income
    WHERE delete_date IS NULL
    AND is_enabled=1
    AND id_type!=2 -- IN DEBT
    AND id_type!=8
    GROUP BY operation_date
) AS inco
LEFT JOIN
(
    -- vapp_balancesub_2
    SELECT operation_date AS out_date
    , SUM(total_out) AS out_total
    FROM app_balance_outcome
    WHERE delete_date IS NULL
    AND is_enabled=1
    AND id_type!=9
    -- AND is_paid!=0 -- NO PAGADO
    GROUP BY operation_date
) AS outco
ON inco.in_date = outco.out_date
WHERE outco.out_date IS NULL

UNION

-- Ingresos con Gastos
SELECT in_date,in_total
,COALESCE(out_total,0) AS out_total
,in_total-COALESCE(out_total,0) AS total
FROM
(
    -- vapp_balancesub_3
    SELECT operation_date AS in_date
    , SUM(total_in) AS in_total 
    FROM app_balance_income
    WHERE delete_date IS NULL
    AND is_enabled=1
    AND id_type!=2 -- IN DEBT
    AND id_type!=8 -- BALANCE INJECTION
    GROUP BY operation_date
) AS inco
INNER JOIN
(
    -- vapp_balancesub_4
    SELECT operation_date AS out_date
    , SUM(total_out) AS out_total
    FROM app_balance_outcome
    WHERE delete_date IS NULL
    AND is_enabled=1
    AND id_type!=9 -- BALANCE INJECTION
    -- AND is_paid!=0 -- NO PAGADO
    GROUP BY operation_date
) AS outco
ON inco.in_date = outco.out_date

UNION

-- Gastos sin ingresos
SELECT out_date, COALESCE(in_total,0) AS in_total
,out_total
,COALESCE(in_total,0)-out_total AS total
FROM
(
    -- vapp_balancesub_5
    SELECT operation_date AS in_date
    , SUM(total_in) AS in_total 
    FROM app_balance_income
    WHERE delete_date IS NULL
    AND is_enabled=1
    AND id_type!=2 -- IN DEBT
    AND id_type!=8 -- BALANCE INJECTION
    GROUP BY operation_date
) AS inco
RIGHT JOIN
(
    -- vapp_balancesub_6
    SELECT operation_date AS out_date
    , SUM(total_out) AS out_total
    FROM app_balance_outcome
    WHERE delete_date IS NULL
    AND is_enabled=1
    AND id_type!=9 -- BALANCE INJECTION
    -- AND is_paid!=0 -- NO PAGADO
    GROUP BY operation_date
) AS outco
ON inco.in_date = outco.out_date
WHERE inco.in_date IS NULL

GO

-- SELECT * FROM vapp_balance

/*    
SELECT SalesOrderID, OrderDate,
    ROW_NUMBER() OVER (ORDER BY OrderDate) AS RowNumber
    FROM Sales.SalesOrderHeader
*/

-- ================================================================================================================
--                              MYSQL v.1.0.0
-- MYSQL no entiende subconsultas en vistas como lo hace SQLSERVER mssql+1
-- ================================================================================================================
DROP VIEW vapp_balancesub_1;
CREATE VIEW vapp_balancesub_1 AS
    -- vapp_balancesub_1
    SELECT operation_date AS in_date
    , SUM(total_in) AS in_total 
    FROM app_balance_income
    WHERE delete_date IS NULL
    AND is_enabled=1
    AND id_type!=2 -- IN DEBT
    AND id_type!=8;


DROP VIEW vapp_balancesub_2;
CREATE VIEW vapp_balancesub_2 AS
    -- vapp_balancesub_2
    SELECT operation_date AS out_date
    , SUM(total_out) AS out_total
    FROM app_balance_outcome
    WHERE delete_date IS NULL
    AND is_enabled=1
    AND id_type!=9
    -- AND is_paid!=0 -- NO PAGADO
    GROUP BY operation_date;


DROP VIEW vapp_balancesub_3;
CREATE VIEW vapp_balancesub_3 AS
    -- vapp_balancesub_3
    SELECT operation_date AS in_date
    , SUM(total_in) AS in_total 
    FROM app_balance_income
    WHERE delete_date IS NULL
    AND is_enabled=1
    AND id_type!=2 -- IN DEBT
    AND id_type!=8 -- BALANCE INJECTION
    GROUP BY operation_date;


DROP VIEW vapp_balancesub_4;
CREATE VIEW vapp_balancesub_4 AS
    -- vapp_balancesub_4
    SELECT operation_date AS out_date
    , SUM(total_out) AS out_total
    FROM app_balance_outcome
    WHERE delete_date IS NULL
    AND is_enabled=1
    AND id_type!=9 -- BALANCE INJECTION
    -- AND is_paid!=0 -- NO PAGADO
    GROUP BY operation_date;


DROP VIEW vapp_balancesub_5;
CREATE VIEW vapp_balancesub_5 AS
    -- vapp_balancesub_5
    SELECT operation_date AS in_date
    , SUM(total_in) AS in_total 
    FROM app_balance_income
    WHERE delete_date IS NULL
    AND is_enabled=1
    AND id_type!=2 -- IN DEBT
    AND id_type!=8 -- BALANCE INJECTION
    GROUP BY operation_date;


DROP VIEW vapp_balancesub_6;
CREATE VIEW vapp_balancesub_6 AS
    -- vapp_balancesub_6
    SELECT operation_date AS out_date
    , SUM(total_out) AS out_total
    FROM app_balance_outcome
    WHERE delete_date IS NULL
    AND is_enabled=1
    AND id_type!=9 -- BALANCE INJECTION
    -- AND is_paid!=0 -- NO PAGADO
;

DROP VIEW vapp_balance;
CREATE VIEW vapp_balance AS

-- Ingresos sin gastos
SELECT in_date,in_total
,COALESCE(out_total,0) AS out_total
,in_total-COALESCE(out_total,0) AS total
FROM vapp_balancesub_1 AS inco
LEFT JOIN vapp_balancesub_2 AS outco
ON inco.in_date = outco.out_date
WHERE outco.out_date IS NULL

UNION

-- Ingresos con Gastos
SELECT in_date,in_total
,COALESCE(out_total,0) AS out_total
,in_total-COALESCE(out_total,0) AS total
FROM vapp_balancesub_3 AS inco
INNER JOIN vapp_balancesub_4 AS outco
ON inco.in_date = outco.out_date

UNION

-- Gastos sin ingresos
SELECT out_date, COALESCE(in_total,0) AS in_total
,out_total
,COALESCE(in_total,0)-out_total AS total
FROM vapp_balancesub_5 AS inco
RIGHT JOIN vapp_balancesub_6 AS outco
ON inco.in_date = outco.out_date
WHERE inco.in_date IS NULL
;
