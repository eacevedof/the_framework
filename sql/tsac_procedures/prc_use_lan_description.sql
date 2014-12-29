/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.0
 * @name prc_use_lan_description
 * @file prc_use_lan_description.sql
 * @date 29-10-2014 13:02 (SPAIN)
 * @observations: 
        Hydra
        Busca todos los paquetes que tengan vistas de traducciones asociadas en tablas y cambia el 
        campo de descripcion "description" por el de la vista
 * @requires:
 */
-- =====================================
--          prc_use_lan_description 
-- =====================================
IF(EXISTS(SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID('prc_use_lan_description')))
    DROP PROCEDURE dbo.prc_use_lan_description;
GO

CREATE PROCEDURE prc_use_lan_description
AS
    DECLARE @isTable INT;
    DECLARE @codPack VARCHAR(12), @codView VARCHAR(12), @sViewName VARCHAR(50)
    DECLARE @codTablePack VARCHAR(12), @codFieldDesc VARCHAR(12), @codFieldDescLan VARCHAR(12), @sPack VARCHAR(250)

    DECLARE @oTablePacksWithLan TABLE(codPack VARCHAR(12),codView VARCHAR(12),sViewName VARCHAR(50))
    DECLARE @oTableDescFields TABLE(codTable VARCHAR(12),codFieldDesc VARCHAR(12))

    -- Guardo todos los paquetes a procesar. Los que tienen vistas tipo viw_lan en tablas asociadas
    INSERT INTO @oTablePacksWithLan (codPack,codView,sViewName)
    SELECT DISTINCT pt.Code_Pack, pt.Code_Table, lan.Table_Name
    FROM core_comms_packs_tables AS pt
    INNER JOIN
    (
        SELECT Code,Table_Name FROM core_tables WHERE Table_Name like 'view_lan_%'
            UNION
        SELECT Code,Table_Name FROM core_bun_tables WHERE Table_Name like 'view_lan_%'
    ) AS lan
    ON lan.Code = pt.Code_Table

    SET @isTable = (SELECT COUNT(*) FROM @oTablePacksWithLan)
    -- IF(@isTable>0)
    -- Si hay paquetes
    IF(@isTable>0)
    BEGIN
        -- Cargo todos los códigos de los campos que se llamen description
        INSERT INTO @oTableDescFields(codTable,codFieldDesc)
        SELECT Code_Table,Code AS Code_Field FROM core_tables_fields WHERE Name='description'
            UNION
	SELECT Code_Table,Code AS Code_Field FROM core_bun_tables_fields WHERE Name='description'

        DECLARE oCursorDb CURSOR FOR 
            SELECT codPack,codView,sViewName 
            FROM @oTablePacksWithLan 

        OPEN oCursorDb  
        FETCH NEXT FROM oCursorDb INTO @codPack,@codView,@sViewName

        WHILE @@FETCH_STATUS = 0  
        BEGIN
            SET @sPack = (SELECT Description FROM core_comms_packs WHERE Code=@codPack)
            PRINT '===============================================================================================';
            PRINT 'PROCESING: pack:'+@sPack+'('+@codPack+'), table: '+@sViewName+'('+@codView+')';
            PRINT '===============================================================================================';
            --Recupero el codigo de la tabla asociada al paquete
            SET @codTablePack = (SELECT Code_Table FROM core_comms_packs WHERE Code=@codPack)
            -- busco el campo description de la tabla del paquete
            SET @codFieldDesc = (SELECT codFieldDesc FROM @oTableDescFields WHERE codTable=@codTablePack)
            -- busco el campo descripcion de la vista de traduccion
            SET @codFieldDescLan = (SELECT codFieldDesc FROM @oTableDescFields WHERE codTable=@codView)

            IF(@codTablePack IS NOT NULL AND @codFieldDesc IS NOT NULL AND @codFieldDescLan IS NOT NULL)
            BEGIN
                
                UPDATE core_comms_packs_fields
                SET Code_Table=@codView
                ,Code_Field=@codFieldDescLan
                WHERE Code_Pack=@codPack
                AND Code_Table=@codTablePack
                AND Code_Field=@codFieldDesc

                PRINT 'ACTUALIZADO: codTablePack: '+@codTablePack+', codFieldDesc:'+@codFieldDesc +', codFieldDescLan:'+@codFieldDescLan
            END
            ELSE
            BEGIN
                PRINT 'No se ha podido traducir el campo descripcion del paquete '+@codPack
                PRINT 'codTablePack: '+ISNULL(@codTablePack,'-')+', codFieldDesc:'+ISNULL(@codFieldDesc,'-2') +', codFieldDescLan:'+@codFieldDescLan
            END
            FETCH NEXT FROM oCursorDb INTO @codPack,@codView,@sViewName
        END  

        CLOSE oCursorDb  
        DEALLOCATE oCursorDb
    END
    ELSE
    BEGIN
        PRINT 'No se han encontrado paquetes con vistas tipo view_lan%'
    END
GO

-- llamada
-- EXEC prc_use_lan_description 
