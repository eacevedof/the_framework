/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.0
 * @name prc_add_usergroup
 * @file prc_add_usergroup.sql
 * @date 07-11-2013 09:29 (SPAIN)
 * @observations: Añade un nuevo grupo a base_user_group
 *                Asigna permisos grant_all al nuevo grupo en base_modules_permissions sobre todos los módulos 
                  existentes en base_module
 * @requires:
 */
-- =====================================
--          prc_add_usergroup 
-- =====================================
IF(EXISTS(SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID('prc_add_usergroup')))
    DROP PROCEDURE dbo.prc_add_usergroup;
GO

CREATE PROCEDURE prc_add_usergroup
    @sGroupName VARCHAR(200), @idGroupParent NUMERIC(18,0)=NULL, @codAgrupation VARCHAR(25)=NULL
AS

    DECLARE @iIdGroup INT;
    DECLARE @sDateNow VARCHAR(14);

    SET @sGroupName = LTRIM(RTRIM(UPPER(@sGroupName)));
    SET @sDateNow = (SELECT CAST((CONVERT(CHAR(8), GETDATE(), 112)+ REPLACE(CONVERT(CHAR(8),GETDATE(), 114),':','')) AS CHAR(14)));
    /*
    1   BY USER ON DB (1)
    2   DTS (2)
    3   BACKOFFICE (3)
    4   MOVIL DEVICE (4)
    */
    IF(NOT EXISTS(SELECT * FROM base_user_group WHERE (description = @sGroupName OR code_erp=@sGroupName) ))
    BEGIN
        /* NUEVO GRUPO */
        INSERT INTO [base_user_group]
        ([processflag],[insert_platform],[insert_user],[insert_date],[update_platform],[update_user],[update_date],[delete_platform],[delete_user],[delete_date]
        ,[is_erpsent],[is_enabled]
        ,[code_erp],[description],[id_group_parent],[code_agrupation])
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
        ,NULL AS code_erp
        ,@sGroupName AS description
        ,@idGroupParent AS id_group_parent
        ,@codAgrupation AS code_agrupation
      
        SET @iIdGroup = (SELECT id FROM base_user_group WHERE insert_date=@sDateNow AND update_date=@sDateNow AND description=@sGroupName);
        /**/
        INSERT INTO [base_modules_permissions]
        ([processflag]
        ,[insert_platform],[insert_user],[insert_date]
        ,[update_platform],[update_user],[update_date]
        ,[delete_platform],[delete_user],[delete_date]
        ,[is_erpsent],[is_enabled]
        ,[code_erp],[description],[id_user_group],[id_module]
        ,[is_select_menu],[is_insert_menu],[is_pick],[is_select]
        ,[is_insert],[is_read],[is_update],[is_delete],[is_quarantine],[is_excelexport],[is_print])

        SELECT NULL
        , 1,1,@sDateNow
        , 1,1,@sDateNow
        ,NULL,NULL,NULL
        ,0,1
        ,'' AS code_erp
        ,@sGroupName+' - '+description AS description
        ,@iIdGroup AS id_user_group
        ,id AS id_module
        ,1,1,1,1,1,1,1,1,1,1,1
        FROM base_module AS modl
    END
    ELSE
    BEGIN
        PRINT 'USER GROUP '+@sGroupName+' NOT CREATED!. THIS USER GROUP ALREADY EXISTS!';
    END
    PRINT 'id_group:'+CONVERT(VARCHAR,@iIdGroup);
GO

/*
SELECT is_erpsent,is_enabled,id,code_erp,description,id_group_parent,code_agrupation 
FROM base_user_group
ORDER BY id DESC

prc_add_usergroup
    @sGroupName VARCHAR(200), @idGroupParent NUMERIC(18,0)=NULL, @codAgrupation VARCHAR(25)=NULL
*/
-- EXEC prc_add_usergroup 'my module'
