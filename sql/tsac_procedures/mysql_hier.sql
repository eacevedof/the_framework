-- http://stackoverflow.com/questions/1382573/how-do-you-use-the-with-clause-in-mysql

DELIMITER //
DROP PROCEDURE pbase_hiergroup//

DELIMITER //
CREATE DEFINER=root@localhost 
PROCEDURE pbase_hiergroup(IN id_group_parent VARCHAR(50))
BEGIN
    DECLARE iCount INT;
    DECLARE sIdGroupParent VARCHAR(50);

    CREATE TEMPORARY TABLE oTableRes(id_group VARCHAR(50),id_group_parent VARCHAR(50))ENGINE=MEMORY;
    CREATE TEMPORARY TABLE oTableHier(id_group VARCHAR(50),id_group_parent VARCHAR(50))ENGINE=MEMORY;

    SET sIdGroupParent = id_group_parent;

    SELECT COUNT(*) INTO iCount 
    FROM base_user_group 
    WHERE id_group_parent=sIdGroupParent;

    WHILE iCount>0 DO

        INSERT INTO oTableRes 
        SELECT b.id
        ,b.id_group_parent 
        FROM base_user_group AS b
        WHERE b.id_group_parent = sIdGroupParent
        AND b.delete_date IS NULL 
        AND b.is_enabled=1;

        INSERT INTO oTableHier 
        SELECT b.id
        ,b.id_group_parent 
        FROM base_user_group AS b
        WHERE b.id_group_parent = sIdGroupParent
        AND b.delete_date IS NULL 
        AND b.is_enabled=1;

        SELECT id_group INTO sIdGroupParent FROM oTableHier LIMIT 0,1;
        
        SELECT COUNT(*) INTO iCount FROM oTableHier;

        DELETE FROM oTableHier 
        WHERE id_group=sIdGroupParent;

    END WHILE;

    SELECT * FROM oTableRes;

    DROP TEMPORARY TABLE IF EXISTS oTableRes;
    DROP TEMPORARY TABLE IF EXISTS oTableHier;
END
//

-- CALL pbase_hiergroup ('4');