/**
 * @author Eduardo Acevedo Farje
 * @link www.eduardoaf.com
 * @version 1.0.4
 * @name prc_get_as_inserts
 * @file prc_get_as_inserts.sql   
 * @date 03-10-2014 12:33 (SPAIN)
 * @observations: 
 *          Este procedimiento tiene como fin recuperar el conjunto de filas de una tabla en forma de sentencia SQL tipo INSERT de modo que
            utilizando el resultado desde la consola se pueda hacer la importación en otra tabla identica de destino.
            Suele ser útil cuando se desea migrar desde un motor de bd superior, ejemplo 2012 a 2008.

            La tabla a tratar debe contar con un campo con nombre "id" autonumerico con valores no nulos y que no se repitan
            Es importante que este campo sea tipo INT ó NUMERIC.  Podría aceptar tipo varchar siempre y cuando no exista ningun valor en esta columna
            que confirme ISNUMERIC(valor)=0
            
            Parámetros con límites añadidos: idfrom, idto en caso de existir se aplican a la condición. Sirve para recuperar inserts por tramos
 * @requires:
 */
IF (EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID('prc_get_as_inserts')))
    DROP PROCEDURE dbo.prc_get_as_inserts;
GO

CREATE PROCEDURE prc_get_as_inserts
    @sTableName VARCHAR(100), @iIdTo NUMERIC(35,0)= NULL, @iIdFrom NUMERIC(35,0)=1
AS

    DECLARE @sSQLEval NVARCHAR(4000), @sSQL VARCHAR(8000),@sFieldName VARCHAR(25), @sFieldType VARCHAR(25), @sValues VARCHAR(8000);
    DECLARE @iId NUMERIC(35,0), @iFields INT, @iRow INT;
    DECLARE @tmpVarchar NVARCHAR(4000);
    -- Tabla con campos tipo clave autonumericos 
    DECLARE @oTableIds TABLE (Id NUMERIC(35,0));
    DECLARE @oTableFields TABLE
    (
        idn INT IDENTITY(1,1),fieldname VARCHAR(25), fieldtype VARCHAR(25), fieldlen VARCHAR(5)
    );
  
    INSERT INTO @oTableFields(fieldname,fieldtype,fieldlen)
    SELECT
    sc.name AS fieldname
    ,st.name AS fieldtype
    ,sc.max_length AS fieldlen
    FROM sys.objects so 
    INNER JOIN sys.columns sc 
    ON so.object_id = sc.object_id 
    INNER JOIN sys.types st 
    ON st.system_type_id = sc.system_type_id 
    AND st.name != 'sysname'
    WHERE 1=1 
    AND sc.name !='id'
    AND so.name = @sTableName
    ORDER BY sc.name ASC

    SET @iFields = (SELECT COUNT(*) FROM @oTableFields);

    IF (@iFields>0)
    BEGIN
        --PRINT 'TOTAL CAMPOS:'+CONVERT(VARCHAR,@iFields);
        --PRINT '[INSERTING TMPIDS] '
        --PRINT 'http://stackoverflow.com/questions/803211/how-to-get-sp-executesql-result-into-a-variable'
        SET @sSQLEval = 'SELECT id FROM '+@sTableName+' WHERE ISNUMERIC(id)=1 AND id>='+CONVERT(VARCHAR,@iIdFrom);
        
        IF(@iIdTo IS NOT NULL AND @iIdTo>=@iIdFrom)
            SET @sSQLEval = @sSQLEval+' AND id<='+CONVERT(VARCHAR,@iIdTo)
        
        SET @sSQLEval = @sSQLEval+' ORDER BY 1'
        PRINT @sSQLEval;
        INSERT INTO @oTableIds(id)
        EXECUTE SP_EXECUTESQL @sSQLEval;

        --PRINT '[END INSERTING TMPIDS]'
        PRINT '-- ======================='
        PRINT '-- STARTING PROCESS'
        PRINT '-- ======================='
        DECLARE oCursorId 
        CURSOR FOR 
            SELECT id
            FROM @oTableIds;

        OPEN oCursorId
        FETCH NEXT FROM oCursorId INTO @iId
        WHILE @@FETCH_STATUS = 0
        BEGIN
            SET @sSQL = 'INSERT INTO '+@sTableName+' (id,';

            -- PRINT CONVERT(VARCHAR,@iId);
            -- Recorrer los campos y meterlos como texto
            DECLARE oCursorField 
            CURSOR FOR 
                SELECT idn, fieldname
                FROM @oTableFields
                ORDER BY idn ASC

            OPEN oCursorField
            FETCH NEXT FROM oCursorField INTO @iRow,@sFieldName
            WHILE @@FETCH_STATUS = 0
            BEGIN
                --PRINT 'FETCH_STATUS:'+CONVERT(VARCHAR,@@FETCH_STATUS);
                IF(@iRow!=@iFields)
                    SET @sSQL = @sSQL + @sFieldName+',';
                ELSE --ultimo campo
                    SET @sSQL = @sSQL + @sFieldName+')';

                FETCH NEXT FROM oCursorField INTO @iRow,@sFieldName
            END
            CLOSE oCursorField
            DEALLOCATE oCursorField

            -- agregar values (
            SET @sSQL = @sSQL +' VALUES('+CONVERT(VARCHAR,@iId)+',';

            SET @sValues = '';
            -- Recorrer los campos y tipo y añadir el valor con '' o sin más ,
            DECLARE oCursorField 
            CURSOR FOR 
                SELECT idn, fieldname, fieldtype
                FROM @oTableFields
                ORDER BY idn ASC

            OPEN oCursorField
            FETCH NEXT FROM oCursorField INTO @iRow,@sFieldName,@sFieldType
            WHILE @@FETCH_STATUS = 0
            BEGIN
                SET @tmpVarchar='';

                --PRINT @sFieldName+'='+@sFieldType+'='+@tmpVarchar+',ID='+CONVERT(VARCHAR,@iId);
                -- Consulta que recupera el valor guardado en cada campo
                SET @sSQLEval = 'SELECT TOP 1 @tmpVarchar=CONVERT(VARCHAR,'+@sFieldName+') FROM '+@sTableName+' WHERE Id='+CONVERT(VARCHAR,@iId)+'';
                --PRINT @sSQLEval; Genera un error:  '57-506682876719704449115138503052', este bug no deberia existir si el campo id es tipo numerico.
                -- El maximo permitido es 4000
                EXECUTE SP_EXECUTESQL @sSQLEval, N'@tmpVarchar NVARCHAR(4000) OUTPUT',@tmpVarchar OUTPUT;
                
                IF(@sFieldType NOT IN ('decimal','float','int','money','numeric','real'))
                BEGIN
                    SET @tmpVarchar = (SELECT REPLACE(@tmpVarchar,'''',''''''));
                    IF(@tmpVarchar IS NULL)
                        SET @tmpVarchar = 'NULL';
                    ELSE
                        SET @tmpVarchar = ''''+@tmpVarchar+'''';
                END 
                ELSE--Tipo numerico
                BEGIN
                    IF(@tmpVarchar IS NULL)
                        SET @tmpVarchar = 'NULL';
                END

                IF(@iRow!=@iFields)
                    SET @tmpVarchar = @tmpVarchar+',';
                ELSE
                    SET @tmpVarchar = @tmpVarchar+');';

                SET @sValues = @sValues+@tmpVarchar;

                FETCH NEXT FROM oCursorField INTO @iRow,@sFieldName,@sFieldType
            END
            CLOSE oCursorField
            DEALLOCATE oCursorField

            SET @sSQL = @sSQL + @sValues;
            PRINT '/*id='+CONVERT(VARCHAR,@iId)+'*/';
            PRINT @sSQL;

            --PRINT 'TOTAL TAMANO CADENA SQL:'+CONVERT(VARCHAR,LEN(@sSQL));
            FETCH NEXT FROM oCursorId INTO @iId

        END
        CLOSE oCursorId
        DEALLOCATE oCursorId

        PRINT '-- ======================='
        PRINT '-- END PROCESS'
        PRINT '-- ======================='
    END--finif
    ELSE --No hay campos en esta table
    BEGIN
        PRINT 'TABLE '+@sTableName+' DOES NOT EXIST OR IT HAS NO FIELDS'
    END


-- EXEC prc_get_as_inserts 'nombretabla',idto=NULL,idfrom=1
-- EXEC prc_get_as_inserts 'products',16000,15000
