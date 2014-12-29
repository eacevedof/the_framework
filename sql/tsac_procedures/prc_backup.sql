/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.3
 * @sDbName prc_backup
 * @file prc_backup.sql
 * @date 10-09-2014 09:09 (SPAIN)
 * @observations: 
 * @requires:
 */
IF (EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID('prc_backup')))
    DROP PROCEDURE dbo.prc_backup;
GO

CREATE PROCEDURE prc_backup
    @sDbName VARCHAR(50)='' -- vacio todas las bases de datos
    ,@sPathSavedirDS VARCHAR(256)='C:/'
AS
    --DECLARE @sDbName VARCHAR(50) -- database name 
    --DECLARE @sPathSavedirDS VARCHAR(256) -- path for backup files 
    DECLARE @sFileNameBack VARCHAR(256) -- filename for backup 
    DECLARE @sFileDateBack VARCHAR(20) -- used for file name
    DECLARE @sDbVersion VARCHAR(15);

    SET @sDbVersion = '';
    IF(EXISTS(SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID('version_db')))
    BEGIN
        SET @sDbVersion = (SELECT TOP 1 version FROM version_db ORDER BY id DESC);
        SET @sDbVersion = '_v'+@sDbVersion;
    END

    --SET @sPathSavedirDS = 'C:\';
    IF(@sPathSavedirDS IS NULL OR LTRIM(RTRIM(@sPathSavedirDS))='')
        SET @sPathSavedirDS = 'C:/'; 

    SELECT @sFileDateBack = CONVERT(VARCHAR(20),GETDATE(),112)

    DECLARE oCursorDb CURSOR FOR 
        SELECT name 
        FROM master.dbo.sysdatabases 
        WHERE name LIKE '%'+@sDbName+'%'

    OPEN oCursorDb  
    FETCH NEXT FROM oCursorDb INTO @sDbName  

    WHILE @@FETCH_STATUS = 0  
    BEGIN
        PRINT '============================================';
        PRINT 'SAVING DATABASE: '+@sDbName;
        PRINT '============================================';

        SET @sFileNameBack = @sPathSavedirDS + @sDbName + @sDbVersion+'_' + @sFileDateBack + '.bak' 
        -- Guarda en disco
        BACKUP DATABASE @sDbName TO DISK = @sFileNameBack 
        
        PRINT '============================================';
        PRINT 'DATABASE FILE '+@sFileNameBack+' SAVED IN '+@sPathSavedirDS;
        PRINT '============================================';
        PRINT '';
        FETCH NEXT FROM oCursorDb INTO @sDbName  
    END  

    CLOSE oCursorDb  
    DEALLOCATE oCursorDb
    PRINT '=== END prc_backup ===';
GO

-- EXEC prc_backup 'theframework'

-- EXEC prc_backup 'theframework','C:\'
