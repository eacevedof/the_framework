/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.3
 * @name prc_to_packsmobile
 * @file prc_to_packsmobile.sql
 * @date 07-11-2014 10:50 (SPAIN)
 * @observations: 
        Hydra
        Pasa los paquetes definidos para las comunicaciones al formato del TMAEDiccionarioDatos
 * @requires:
 */
-- =====================================
--          prc_to_packsmobile 
-- =====================================
IF(EXISTS(SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID('prc_to_packsmobile')))
    DROP PROCEDURE dbo.prc_to_packsmobile;
GO

CREATE PROCEDURE prc_to_packsmobile @sPackName VARCHAR(100)=''
AS
    DECLARE @isTable INT;
    DECLARE @codPack VARCHAR(12), @codTable VARCHAR(12), @sTableName VARCHAR(50);

    DECLARE @oTablePacks TABLE(codPack VARCHAR(12), codTable VARCHAR(12),sTableName VARCHAR(50))

    -- Guardo todos los paquetes a procesar
    INSERT INTO @oTablePacks (codPack,codTable,sTableName)
    SELECT DISTINCT Code AS Code_Pack
    ,Code_Table
    ,Description
    FROM core_comms_packs
    WHERE Active='1'
    ORDER BY Description, Code_Pack,Code_Table

    IF(@sPackName!='' AND @sPackName IS NOT NULL)
    BEGIN
        DELETE FROM @oTablePacks;
        INSERT INTO @oTablePacks (codPack,codTable,sTableName)
        SELECT DISTINCT Code AS Code_Pack
        ,Code_Table
        ,Description
        FROM core_comms_packs
        WHERE Active='1'
        AND Description=@sPackName
        ORDER BY Description,Code_Pack,Code_Table
    END

    SET @isTable = (SELECT COUNT(*) FROM @oTablePacks)
    -- IF(@isTable>0)
    -- Si hay paquetes
    IF(@isTable>0)
    BEGIN

        IF(EXISTS(SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID('TMAEDiccionarioDatos')))
        BEGIN
            TRUNCATE TABLE TMAEDiccionarioDatos
        END
        ELSE
        BEGIN
            CREATE TABLE [dbo].[TMAEDiccionarioDatos]
            (
                [C_id] [int] IDENTITY(1,1) NOT NULL,
                [NomTabla] [varchar](50) NULL,
                [NomCampo] [varchar](50) NULL,
                [Longitud] [varchar](50) NULL,
                [NDecimales] [varchar](50) NULL,
                [TipoDatos] [varchar](50) NULL,
                [Indice] [varchar](50) NULL,
                [Import] [varchar](50) NULL,
                [Export] [varchar](50) NULL,
                [ValDefecto] [varchar](50) NULL,
                [Orden] [int] NULL,
                PRIMARY KEY CLUSTERED 
                (
                        [C_id] ASC
                )WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
            ) ON [PRIMARY]
        END

        -- En este punto la tabla ya debería existir
        DECLARE oCursorDb CURSOR FOR 
            SELECT codPack,codTable,sTableName 
            FROM @oTablePacks 

        OPEN oCursorDb  
        FETCH NEXT FROM oCursorDb INTO  @codPack,@codTable,@sTableName

        WHILE @@FETCH_STATUS = 0  
        BEGIN
            PRINT '===============================================================================================';
            PRINT 'PROCESING: pack:'+@codPack+', table: '+@sTableName+'('+@codTable+')';
            PRINT '===============================================================================================';

            -- En los paquetes el código suele ser igual que el id pero para asegurarme mejor hago un max code
            SET @isTable = (SELECT COUNT(C_id) FROM TMAEDiccionarioDatos WHERE NomTabla=@sTableName)
            
            IF(@isTable=0)
            BEGIN
                PRINT 'status_moible para '+@sTableName+'('+@codTable+')';
                --CREO EL STATUS MOBILE
                INSERT INTO TMAEDiccionarioDatos
                (Export,Import,Indice,Longitud,NDecimales,NomCampo,NomTabla,Orden,TipoDatos,ValDefecto)
                VALUES
                (1,1,0,2,0,'status_mobile',@sTableName,-1,'A',NULL)
            END

            -- CREO EL RESTO DE CAMPOS
            INSERT INTO TMAEDiccionarioDatos
            (Export,Import,Indice,Longitud,NDecimales,NomCampo,NomTabla,Orden,TipoDatos,ValDefecto)

            SELECT
            '0' AS Export
            ,'1' AS Import
            ,Flag_Keyfield AS indice
            ,Length AS Longitud
            ,Decimals AS NDecimales
            ,cf.Description AS NomCampo
            ,cp.Description AS NomTabla
            ,Field_Order+1 AS Orden
            ,CASE WHEN DataType IN (1,14,15,18,20,7,10,16,26,27,8)
                    THEN
                        'A'
                    ELSE
                        'N'
                    END
            AS TipoDatos
            ,Value AS ValDefecto
            FROM core_comms_packs AS cp
            LEFT JOIN core_comms_packs_fields AS cf
            ON cp.Code = cf.Code_Pack
            WHERE cp.Description = @sTableName
            ORDER BY cp.Description ASC,cf.Field_Order

            FETCH NEXT FROM oCursorDb INTO @codPack,@codTable,@sTableName
            PRINT ''
            PRINT ''
        END  

        CLOSE oCursorDb  
        DEALLOCATE oCursorDb

        UPDATE TMAEDiccionarioDatos
        SET Orden = 0
        WHERE NomCampo='status_mobile'
        -- Muestro los datos procesados
        SELECT * FROM TMAEDiccionarioDatos
    END
    ELSE
    BEGIN
        PRINT 'No se han encontrado paquetes'
    END
GO

-- EXEC prc_to_packsmobile 
