/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.0
 * @name prc_add_user
 * @file prc_add_user.sql
 * @date 09-11-2013 09:03 (SPAIN)
 * @observations: 
 * @requires:
 */
-- =====================================
--          prc_add_user 
-- =====================================
IF(EXISTS(SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID('prc_add_user')))
    DROP PROCEDURE dbo.prc_add_user;
GO

CREATE PROCEDURE prc_add_user
    @sFirstName VARCHAR(100), @sLastName VARCHAR(100)='',@idGroup NUMERIC(18,0),@codAgrupation VARCHAR(25)
    ,@codType VARCHAR(25)=NULL, @idSeller NUMERIC(18,0)=NULL, @idStartModule NUMERIC(18,0)=6
AS

    DECLARE @iIdUserGroup NUMERIC(18,0);
    DECLARE @iIdUser INT;
    DECLARE @sDateNow VARCHAR(14);

    SET @sFirstName = LTRIM(RTRIM(UPPER(@sFirstName)));
    SET @sLastName = LTRIM(RTRIM(UPPER(@sLastName)));

    SET @sDateNow = (SELECT CAST((CONVERT(CHAR(8), GETDATE(), 112)+ REPLACE(CONVERT(CHAR(8),GETDATE(), 114),':','')) AS CHAR(14)));
    /*
    1   BY USER ON DB (1)
    2   DTS (2)
    3   BACKOFFICE (3)
    4   MOVIL DEVICE (4)
    */
    IF(NOT EXISTS(SELECT * FROM base_user WHERE (first_name = @sFirstName AND last_name=@sLastName) ))
    BEGIN
        /* NUEVO USUARIO */
        INSERT INTO [dbo].[base_user]
        ([processflag]
        ,[insert_platform],[insert_user],[insert_date]
        ,[update_platform],[update_user],[update_date]
        ,[delete_platform],[delete_user],[delete_date]
        ,[is_erpsent],[is_enabled],[code_erp]
        ,[first_name],[last_name],[description],[bo_login],[bo_password],[md_login],[md_password],[language]
        ,[id_start_module],[path_picture],[id_profile],[code_type],[id_seller])

        SELECT NULL
        , 1,1,@sDateNow
        , 1,1,@sDateNow
        ,NULL,NULL,NULL
        ,0,1
        ,'' AS code_erp
        ,@sFirstName
        ,@sLastName
        ,@sFirstName+' '+@sLastName
        ,LOWER(REPLACE(@sFirstName,' ','')),'bo123'
        ,@sFirstName,'bo123'
        ,'english'
        ,6,'images/pictures/users/user_5/user_5_0.png'
        ,NULL,@codType,@idSeller
      
        SET @iIdUserGroup = (SELECT id FROM base_user_group WHERE id=@idGroup);
        
        IF (@iIdUserGroup IS NOT NULL)
        BEGIN
            /* NUEVO USUARIO EN GRUPO */
            SET @iIdUser = (SELECT id FROM base_user WHERE insert_date=@sDateNow AND update_date=@sDateNow AND first_name=@sFirstName AND last_name=@sLastName);
            
            INSERT INTO [base_users_groups]
            ([processflag]
            ,[insert_platform],[insert_user],[insert_date]
            ,[update_platform],[update_user],[update_date]
            ,[delete_platform],[delete_user],[delete_date]
            ,[is_erpsent],[is_enabled],[code_erp]

            ,[description],[id_user],[id_user_group]
            )
            
            SELECT NULL
            , 1,1,@sDateNow
            , 1,1,@sDateNow
            ,NULL,NULL,NULL
            ,0,1
            ,NULL AS code_erp

            ,CONVERT(VARCHAR,@iIdUser)+' - '
                +(SELECT UPPER(description) FROM base_user WHERE id=@iIdUser)+' - '
                +(SELECT UPPER(description) FROM base_user_group WHERE id=@iIdUserGroup)

            ,@iIdUser
            ,@iIdUserGroup            
        END --finif
    END--finif
    ELSE
    BEGIN
        PRINT 'USER '+@sFirstName+' NOT CREATED!. THIS USER ALREADY EXISTS!';
    END
    PRINT 'id_user:'+CONVERT(VARCHAR,@iIdUser);
GO

-- EXEC  prc_add_user
--   @sFirstName VARCHAR(100), @sLastName VARCHAR(100)='',@idGroup NUMERIC(18,0),@codAgrupation VARCHAR(25)
--  ,@codType VARCHAR(25)=NULL,@idSeller NUMERIC(18,0)=NULL,@idStartModule NUMERIC(18,0)=6

-- EXEC prc_add_user 

