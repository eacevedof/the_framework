/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.2.2
 * @name prc_table
 * @file prc_table.sql
 * @date 30-06-2014 10:24 (SPAIN)
 * @observations: 
 * @requires:
 */
IF (EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID('prc_table')))
    DROP PROCEDURE dbo.prc_table;
GO

CREATE PROCEDURE [dbo].[prc_table]
    @sTableName VARCHAR(25)=''
    , @sFieldName VARCHAR(25)=''
    , @isLikeTable INT = 1
    , @isLikeField INT = 1
    , @showSysfields INT = NULL
AS

    DECLARE @sSQL VARCHAR(8000);

    SET @sSQL = 
    '
    SELECT
    so.name AS Tabla
    ,sc.name AS Columna
    ,CASE     
        WHEN pks.constraint_type  IS NULL THEN ''''
        ELSE ''Y''
     END AS ispk
    ,st.name AS Tipo
    ,sc.max_length AS Tamaño
    ,''SELECT ''+sc.name+'',* FROM ''+so.name AS selectall
    ,''<''+sc.name+'' type="''+st.name+''" length="''+CONVERT(VARCHAR,sc.max_length)+''" label="lbl_''+sc.name+''"></''+sc.name+''>'' AS xml
    FROM sys.objects so 
    INNER JOIN sys.columns sc 
    ON so.object_id = sc.object_id 
    INNER JOIN sys.types st 
    ON st.system_type_id = sc.system_type_id 
    AND st.name != ''sysname''
    LEFT JOIN 
    (
        --PKS
        SELECT K.table_name
        ,C.constraint_type
        ,K.column_name
        ,K.constraint_name
        --SELECT K.TABLE_NAME, C.CONSTRAINT_TYPE, K.COLUMN_NAME, K.CONSTRAINT_NAME
        FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS AS C
        JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE AS K
        ON C.TABLE_NAME = K.TABLE_NAME
        AND C.CONSTRAINT_CATALOG = K.CONSTRAINT_CATALOG
        AND C.CONSTRAINT_SCHEMA = K.CONSTRAINT_SCHEMA
        AND C.CONSTRAINT_NAME = K.CONSTRAINT_NAME
        WHERE C.CONSTRAINT_TYPE = ''PRIMARY KEY''
        --ORDER BY K.TABLE_NAME, C.CONSTRAINT_TYPE, K.CONSTRAINT_NAME	
    )
    AS pks
    ON pks.table_name = so.name
    AND pks.column_name = sc.name
    WHERE 1=1 
     ';

    IF(@sTableName!='' AND @sTableName IS NOT NULL)
    BEGIN
        IF(@isLikeTable>0)
        BEGIN
            SET @sSQL = @sSQL + ' AND so.name LIKE ''%'+@sTableName+'%''';
        END
        ELSE    
        BEGIN
            SET @sSQL = @sSQL + ' AND so.name = '''+@sTableName+'''';
        END
    END

    IF(@sFieldName!='')
    BEGIN
        IF(@isLikeField>0)
            SET @sSQL = @sSQL + ' AND sc.name LIKE ''%'+@sFieldName+'%''';
        ELSE    
            SET @sSQL = @sSQL + ' AND sc.name = '''+@sFieldName+'''';        
    END

    -- 0 o NULL
    -- SET @showSysfields=NULL;
    IF(@showSysfields!=1 OR @showSysfields IS NULL)
    BEGIN
--         SET @sSQL = @sSQL + ' AND sc.name NOT IN(''processflag'',''insert_platform'',''insert_user'',''insert_date'',
-- ''update_platform'',''update_user'',''update_date'',
-- ''delete_platform'',''delete_user'',''delete_date'',
-- ''cru_csvnote'',''is_erpsent'',''is_enabled'',
-- ''status_mobile'',''create_user'',''modify_user''
-- ,''create_date'',''modify_date'',''Id'')';
        SET @sSQL = @sSQL + ' AND sc.name NOT IN(''processflag'',''insert_platform'',''insert_user'',''insert_date'',
''update_platform'',''update_user'',''update_date'',
''delete_platform'',''delete_user'',''delete_date'',
''cru_csvnote'',''is_erpsent'',''is_enabled'',
''status_mobile'',''create_user'',''modify_user''
,''create_date'',''modify_date'',''i'')';
    END

    SET @sSQL = @sSQL + ' ORDER BY so.name ASC, 3 DESC, sc.name ASC';

    EXECUTE(@sSQL);
GO

-- EXEC prc_table 'drop'

-- EXEC prc_table '','partenombrecampo'

-- EXEC prc_table 'detvisa','',0