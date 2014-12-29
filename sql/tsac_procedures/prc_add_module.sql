/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.1
 * @name prc_add_module
 * @file prc_add_module.sql
 * @date 05-04-2014 16:51 (SPAIN)
 * @observations: 
 * @requires:
 */
-- =====================================
--          prc_add_module 
-- =====================================
IF(EXISTS(SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID('prc_add_module')))
    DROP PROCEDURE dbo.prc_add_module;
GO

CREATE PROCEDURE prc_add_module
    @sModuleName VARCHAR(15)
AS

    DECLARE @iIdModule INT;
    DECLARE @sDateNow VARCHAR(14);

    SET @sDateNow = (SELECT CAST((CONVERT(CHAR(8), GETDATE(), 112)+ REPLACE(CONVERT(CHAR(8),GETDATE(), 114),':','')) AS CHAR(14)));
    /*
    1   BY USER ON DB (1)
    2   DTS (2)
    3   BACKOFFICE (3)
    4   MOVIL DEVICE (4)
    */
    IF(NOT EXISTS(SELECT * FROM base_module WHERE (description = @sModuleName OR code_erp=@sModuleName) ))
    BEGIN
        /* NUEVO MODULO */
        INSERT INTO [base_module]
        ([processflag],[insert_platform],[insert_user],[insert_date],[update_platform],[update_user],[update_date]
        ,[delete_platform],[delete_user],[delete_date],[is_erpsent],[is_enabled],[code_erp],[description])
   
        SELECT
        NULL AS processflag
        ,1 AS insert_platform
        ,1 AS insert_user
        ,@sDateNow AS insert_date
        ,1 AS update_platform
        ,1 AS update_user
        ,@sDateNow AS update_date
        ,NULL AS delete_platform
        ,NULL AS delete_user
        ,NULL AS delete_date
        ,0 AS is_erpsent
        ,1 AS is_enabled
        ,@sModuleName AS code_erp
        ,@sModuleName AS description
      
        SET @iIdModule = (SELECT id FROM base_module WHERE insert_date=@sDateNow AND update_date=@sDateNow AND code_erp=@sModuleName AND description=@sModuleName);

        INSERT INTO base_modules_permissions
        ([insert_platform],[insert_user],[insert_date],[update_platform],[update_user],[update_date]
        ,[delete_user],[delete_date],[is_erpsent],[is_enabled]
        ,[code_erp]
        ,[description],[id_user_group],[id_module]
        )

        SELECT 
        '1',1,@sDateNow,'1',1,@sDateNow
        ,NULL,NULL,0,1
        ,'' AS code_erp
        ,usrgp.description+' - '+@sModuleName AS description
        , usrgp.id AS id_user_group
        , @iIdModule AS id_module
        FROM base_user_group AS usrgp
    END
    ELSE
    BEGIN
        PRINT 'MODULE '+@sModuleName+' NOT CREATED!. THIS MODULE ALREADY EXISTS!';
    END
GO

-- EXEC prc_add_module 'my module'
