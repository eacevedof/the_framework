/*
NO TIRAAAAAAA :s odio mysql
*/

DROP PROCEDURE IF EXISTS get_childs;

DELIMITER GO

CREATE PROCEDURE get_childs(IN iIdParent INT)
BEGIN
    DECLARE iInsertedRows INT DEFAULT 0;

    DROP TABLE IF EXISTS table_childs;
    CREATE TABLE table_childs 
    (
        id_child INT PRIMARY KEY
        ,id_parent INT PRIMARY KEY
    ) ENGINE=HEAP;

    INSERT INTO table_childs VALUES(iIdParent,-1);
    
    SET iInsertedRows = ROW_COUNT();

    WHILE iInsertedRows > 0 DO
        -- INSERT IGNORE Inserta solo valores nuevos que no existan. Una especie de distinct en el insert

        /* 
            Recupero los hijos de los que se han insertado
        */
        INSERT IGNORE INTO table_childs (id_child,id_parent)

        SELECT DISTINCT tabhier.id_child
        ,tabtemp.id_child
        FROM tabh AS tabhier
        INNER JOIN table_childs AS tabtemp 
        ON tabhier.id_parent = tabtemp.id_child;

        SET iInsertedRows = ROW_COUNT();

    END WHILE;

    SELECT * 
    FROM table_childs
    ORDER BY 2,1 ASC;
    
    DROP TABLE table_childs;
END;
GO

DELIMITER ;

-- CALL get_childs(1);


/*
NO TIRAAAAAAA :s odio mysql
se va al infinito, ahora no con leave sMarkWhile1
*/

DROP PROCEDURE IF EXISTS get_childs;

DELIMITER GO

CREATE PROCEDURE get_childs(IN iIdParent INT)
BEGIN
    DECLARE iInsertedRows INT DEFAULT 0;

    DROP TABLE IF EXISTS table_childs;
    CREATE TABLE table_childs 
    (
        id_child INT 
        ,id_parent INT
    ) ENGINE=HEAP;

    INSERT INTO table_childs VALUES(iIdParent,-1);
    
    SET iInsertedRows = ROW_COUNT();
    
    -- Etiqueto el while para poder forzar la salida
    sMarkWhile1: 
    WHILE iInsertedRows > 0 
    DO
        -- INSERT IGNORE Inserta solo valores nuevos que no existan. Una especie de distinct en el insert

        /* 
            Recupero los hijos de los que se han insertado
        */
        INSERT INTO table_childs (id_child,id_parent)

        SELECT DISTINCT tabhier.id_child
        ,tabtemp.id_child
        FROM tabh AS tabhier
        -- Tabla temporal
        INNER JOIN table_childs AS tabtemp 
        ON tabhier.id_parent = tabtemp.id_child;

        SET iInsertedRows = ROW_COUNT();
        SELECT iInsertedRows;
        LEAVE sMarkWhile1;
    END WHILE;
    -- FIN WHILE sMarkWhile1

    SELECT * 
    FROM table_childs
    ORDER BY 2,1 ASC;
    
    DROP TABLE table_childs;
END;
GO

DELIMITER ;

-- CALL get_childs(1);