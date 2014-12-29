/*QUE GRUPOS ESTÁN POR DEBAJO DE id_group_parent*/
DROP PROCEDURE `prc_base_hiergroup`;

CREATE DEFINER=`root`@`localhost` 
PROCEDURE `prc_base_hiergroup`()
BEGIN
    DECLARE iCount int;
    DECLARE tmp_team_id varchar(50);
    -- CREATE TEMPORARY TABLE res_hier(user_id varchar(50),team_id varchar(50)) engine=memory;
    -- CREATE TEMPORARY TABLE tmp_hier(user_id varchar(50),team_id varchar(50)) engine=memory;
    CREATE TEMPORARY TABLE oTableGroups(id DECIMAL(18,0)) engine=memory; 

    INSERT INTO oTableGroups 
    SELECT 

    SET tmp_team_id = team_id;
    SELECT COUNT(*) INTO iCount 
    FROM user_table 
    WHERE user_table.team_id=tmp_team_id;
    
    WHILE count>0 DO
        insert into res_hier 
        select user_table.user_id,user_table.team_id from user_table where user_table.team_id=tmp_team_id;
        
        insert into tmp_hier 
        select user_table.user_id,user_table.team_id from user_table where user_table.team_id=tmp_team_id;
        
        select user_id into tmp_team_id 
        from tmp_hier limit 0,1;

        select count(*) into iCount from tmp_hier;
        delete from tmp_hier where user_id=tmp_team_id;
    END while;
    
    select * from res_hier;
    drop temporary table if exists res_hier;
    drop temporary table if exists tmp_hier;
END 


ALTER VIEW prc_base_hiergroup AS
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
FROM prc_base_hiergroup
WHERE id_group_parent=4
GO

SELECT *
FROM prc_base_hiergroup
WHERE id_group=4
GO