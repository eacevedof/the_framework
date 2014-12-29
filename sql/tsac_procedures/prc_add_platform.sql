/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.0
 * @name prc_add_platform
 * @file prc_add_platform.sql
 * @date 23-10-2014 17:30 (SPAIN)
 * @observations: 
        Hydra
        Añade automaticamente el campo platform al paquete que le falte
 * @requires:
 */
-- =====================================
--          prc_add_platform 
-- =====================================
IF(EXISTS(SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID('prc_add_platform')))
    DROP PROCEDURE dbo.prc_add_platform;
GO

CREATE PROCEDURE prc_add_platform
AS
    DECLARE @isTable INT;
    DECLARE @codPack VARCHAR(12), @codTable VARCHAR(12), @sTableName VARCHAR(50), @codPlatform VARCHAR(12);
    DECLARE @iLastCode INT;

    DECLARE @oTableNoPlatform TABLE(codPack VARCHAR(12), codTable VARCHAR(12),sTableName VARCHAR(50))
    INSERT INTO @oTableNoPlatform (codPack,codTable,sTableName)
    SELECT DISTINCT CODE AS Code_Pack
    ,Code_Table
    ,Description
    FROM core_comms_packs
    WHERE Code_Table NOT IN
    (
        SELECT Code_Table from core_comms_packs_fields where Description='platform'
    )

    SET @isTable = (SELECT COUNT(*) FROM @oTableNoPlatform)
    
    -- IF(@isTable>0)
    IF(@isTable>0)
    BEGIN
        -- En los paquetes el código suele ser igual que el id pero para asegurarme mejor hago un max code
        SET @iLastCode = (SELECT MAX(CONVERT(INT,Code)) FROM core_comms_packs_fields WHERE ISNUMERIC(Code)=1)

        DECLARE oCursorDb CURSOR FOR 
            SELECT codPack,codTable,sTableName 
            FROM @oTableNoPlatform 

        OPEN oCursorDb  
        FETCH NEXT FROM oCursorDb INTO  @codPack,@codTable,@sTableName

        WHILE @@FETCH_STATUS = 0  
        BEGIN
            SET @iLastCode = @iLastCode + 1;
            PRINT '===============================================================================================';
            PRINT 'PROCESING: pack:'+@codPack+', table: '+@sTableName+'('+@codTable+'), New Field Code:'+CONVERT(VARCHAR,@iLastCode);
            PRINT '===============================================================================================';
            
            -- Recupero el codigo del campo platform de las tablas donde puede existir (core y bun)
            SET @codPlatform = 
            (-- seleccionamos tabla,campo y descripcion
                SELECT 
                f.Code AS Code_Field_Platform
                --,f.Code_Table
                --,f.Name AS description
                FROM core_tables_fields f
                INNER JOIN core_comms_packs_fields AS pf
                ON f.Code_Table = pf.Code_Table
                WHERE 1=1
                AND f.Name='platform'
                AND f.Code_Table = @codTable

                        UNION

                SELECT 
                f.Code AS Code_Field_Platform
                --,f.Code_Table
                --,f.Name 
                FROM core_bun_tables_fields f
                INNER JOIN core_comms_packs_fields AS pf
                ON f.Code_Table = pf.Code_Table
                WHERE 1=1
                AND f.Name='platform'
                AND f.Code_Table = @codTable
            );

            IF(@codPlatform IS NULL OR LTRIM(RTRIM(@codPlatform))='')
            BEGIN
                PRINT '================================================================='
                PRINT 'ERROR: Campo no creado!. El campo "platform" no existe para la tabla '+@sTableName+' posiblemente esta tabla sea una vista'
                PRINT '================================================================='
            END
            ELSE
            BEGIN
                -- Creo el espacio en primera posición para el nuevo campo
                UPDATE core_comms_packs_fields
                SET Field_Order=Field_Order+10
                WHERE Code_Pack=@codPack

                /*
                Code	Description	Field_Order	Code_Table	Code_Field	Length	Decimals	Type	Flag_Keyfield	DataType
                1881	Platform	0		5121		9348		4	0		2	0		2
                */
                INSERT INTO core_comms_packs_fields
                (Code_Pack,Code,Description,Field_Order,Code_Table,Code_Field,Length,Decimals,Type,Flag_Keyfield,DataType)
                VALUES(@codPack,@iLastCode,'Platform',0,@codTable,@codPlatform,4,0,'2','0','2')
                /**/
                PRINT '===============================================================================================';
                PRINT 'END OF: pack:'+@codPack+', table: '+@sTableName+'('+@codTable+'), New Field Code:'+CONVERT(VARCHAR,@iLastCode);
                PRINT '===============================================================================================';
            END
            FETCH NEXT FROM oCursorDb INTO  @codPack,@codTable,@sTableName
            PRINT ''
            PRINT ''
        END  

        CLOSE oCursorDb  
        DEALLOCATE oCursorDb
        -- Muestro los datos procesados
        -- SELECT * FROM @oTableNoPlatform
    END
    ELSE
    BEGIN
        PRINT 'No se han encontrado tablas sin platform'
    END
GO

-- EXEC prc_add_platform 
