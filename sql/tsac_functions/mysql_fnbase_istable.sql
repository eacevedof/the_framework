DELIMITER //

CREATE FUNCTION fnbase_istable(sTableName VARCHAR(45))
RETURNS BOOLEAN
DETERMINISTIC READS SQL DATA
BEGIN
    DECLARE iCount TINYINT(1) DEFAULT 0;

    SELECT COUNT(*) INTO iCount
    FROM information_schema.tables 
    WHERE table_schema =  DATABASE()
    AND table_name =  sTableName;

    RETURN iCount;

END//

DELIMITER ;
-- SELECT fn_istable('yousTableName') as iCount