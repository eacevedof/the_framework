SELECT MIN(is_pick) AS is_pick
,MIN(is_select) AS is_select
,MIN(is_update) AS is_update
,MIN(is_insert) AS is_insert
,MIN(is_delete) AS is_delete
FROM 
(
    -- Permisos por grupo de usuario y módulo
    SELECT DISTINCT per.id_module, modl.description AS module
    ,per.is_pick ,per.is_select, per.is_update, per.is_insert, per.is_delete
    FROM base_modules_permissions AS per
    INNER JOIN
    (
        -- modules activos y no borrados
        SELECT id, description 
        FROM base_module
        WHERE 1=1
        AND is_enabled = '1'
        AND delete_date IS NULL
    ) 
    AS modl
    ON per.id_module = modl.id 

    WHERE 1=1
    AND per.is_enabled = '1'
    AND per.delete_date IS NULL
    AND per.id_user_group IN
    ( 
        -- grupos a los que pertenece el usuario
        SELECT id_user_group 
        FROM base_users_groups
        WHERE 1=1
        AND is_enabled = '1'
        AND delete_date IS NULL
        AND id_user = '2'
    )
    --AND per.id_module = '1'
    AND modl.description = 'orders'
) AS permission
GROUP BY id_module, module


/*
PARA EL MENU
*/
SELECT id_module, module
,MIN(is_pick) AS is_pick
,MIN(is_select) AS is_select
,MIN(is_update) AS is_update
,MIN(is_insert) AS is_insert
,MIN(is_delete) AS is_delete
FROM 
(
    /* Permisos por grupo de usuario y módulo */
    SELECT DISTINCT per.id_module, modl.description AS module
    ,per.is_pick ,per.is_select, per.is_update, per.is_insert, per.is_delete
    FROM base_modules_permissions AS per
    INNER JOIN
    (
        /* modules activos y no borrados */
        SELECT id, description 
        FROM base_module
        WHERE 1=1
        AND is_enabled = '1'
        AND delete_date IS NULL
    ) 
    AS modl
    ON per.id_module = modl.id 

    WHERE 1=1
    AND per.is_enabled = '1'
    AND per.delete_date IS NULL
    AND per.id_user_group IN
    ( 
        /* grupos a los que pertenece el usuario */
        SELECT id_user_group 
        FROM base_users_groups
        WHERE 1=1
        AND is_enabled = '1'
        AND delete_date IS NULL
        AND id_user = '2'
    )
    AND (per.is_select = '1' OR per.is_insert='1')	
) AS permission
GROUP BY id_module, module