/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.1
 * @name prc_set_sysvars
 * @file prc_set_sysvars.sql
 * @date 25-04-2014 07:23 (SPAIN)
 * @observations: 
 * @requires:
 */
-- =====================================
--          prc_set_sysvars 
-- =====================================
IF(EXISTS(SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID('prc_set_sysvars')))
    DROP PROCEDURE dbo.prc_set_sysvars;
GO

CREATE PROCEDURE prc_set_sysvars
    @sTableName VARCHAR(100)
    ,@isFull INT = 0
AS

    DECLARE @sSQL VARCHAR(8000);
    /*processflag, insert_platform, insert_user, insert_date, update_platform, update_user, update_date, delete_platform, delete_user, delete_date, 
    cru_csvnote, is_erpsent, is_enabled, */
    DECLARE @sDateNow VARCHAR(14);
    

    SET @sDateNow = (SELECT CAST((CONVERT(CHAR(8), GETDATE(), 112)+ REPLACE(CONVERT(CHAR(8),GETDATE(), 114),':','')) AS CHAR(14)));
    /*
    1   BY USER ON DB (1)
    2   DTS (2)
    3   BACKOFFICE (3)
    4   MOVIL DEVICE (4)
    */
    IF(EXISTS(SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(@sTableName)))
    BEGIN
        SET @sSQL = 'UPDATE '+@sTableName+'
        SET 
            insert_platform=''1''
            ,insert_user=''1''
            ,insert_date='+@sDateNow+'
            ,update_platform=''1''
            ,update_user=''1''
            ,update_date='+@sDateNow+'
            ,is_erpsent=''0''
            ,is_enabled=''1''
         WHERE 1=1';
         IF(@isFull=0)
            SET @sSQL = @sSQL + ' AND insert_date IS NULL' 
         EXECUTE(@sSQL);
    END
    PRINT 'fields updated!!';
GO

-- EXEC prc_set_sysvars 'ARRAY' 

