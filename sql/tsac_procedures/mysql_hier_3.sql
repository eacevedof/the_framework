/*
NO FUNCIONA 
se va al infinito incluso con la marca
con leave sMarkWhile1
*/

-- DROP PROCEDURE IF EXISTS get_childs;

DELIMITER GO

CREATE PROCEDURE get_childs(IN iIdParent INT)
BEGIN
    -- Contador de filas insertadas
    DECLARE iInsertedRows INT DEFAULT 0;
    DECLARE iCount INT DEFAULT 0;

    -- Tabla temporal donde se creará la recursividad
    DROP TABLE IF EXISTS table_childs;
    CREATE TABLE table_childs 
    (
        id_child INT 
        ,id_parent INT
    ) ENGINE=HEAP;

    -- Guardo como hijo y padre el mismo id
    INSERT INTO table_childs VALUES(iIdParent,iIdParent);
    
    -- Inicializo el contador
    SET iInsertedRows = ROW_COUNT();

    -- Etiqueto el while para poder forzar la salida
    sMarkWhile1: 
    -- Siempre que se vaya encontrando hijos y se inserten esto será >0 por lo tanto ira buscando
    -- hasta el último nodo
    WHILE (iInsertedRows > 0)
    DO
        SET iCount = iCount+1;
        -- INSERT IGNORE -- Inserta solo valores nuevos que no existan. Una especie de distinct en el insert
        /* 
            Recupero los hijos de los que se han insertado
        */
        INSERT IGNORE INTO table_childs (id_child,id_parent)

        SELECT DISTINCT tabhier.id_child
        ,tabtemp.id_child
        FROM tabh AS tabhier
        -- Tabla temporal
        INNER JOIN table_childs AS tabtemp 
        ON tabhier.id_parent = tabtemp.id_child;

        SET iInsertedRows = ROW_COUNT();
         -- SELECT iInsertedRows;
        IF (iCount>=3) THEN
            LEAVE sMarkWhile1;
        END IF;

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