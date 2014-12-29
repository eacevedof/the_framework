CREATE PROCEDURE db_search(@sSearch nvarchar(100))
AS
BEGIN
    -- http://vyaskn.tripod.com/search_all_columns_in_all_tables.htm
    DECLARE @oTableResult TABLE(sColumnName nvarchar(370), ColumnValue nvarchar(3630))

    SET NOCOUNT ON
    DECLARE @sTableName nvarchar(256), @sColumnName nvarchar(128), @sSearch2 nvarchar(110)

    SET @sTableName = ''
    SET @sSearch2 = QUOTENAME('%'+@sSearch+'%','''')

    WHILE @sTableName IS NOT NULL
    BEGIN

        SET @sColumnName = ''
        SET @sTableName =
        (
            SELECT MIN(QUOTENAME(TABLE_SCHEMA)+'.'+QUOTENAME(TABLE_NAME))
            FROM INFORMATION_SCHEMA.TABLES
            WHERE TABLE_TYPE = 'BASE TABLE'
            AND QUOTENAME(TABLE_SCHEMA)+'.'+QUOTENAME(TABLE_NAME) > @sTableName
            AND OBJECTPROPERTY(OBJECT_ID(QUOTENAME(TABLE_SCHEMA)+'.'+QUOTENAME(TABLE_NAME)), 'IsMSShipped') = 0
        )

        WHILE(@sTableName IS NOT NULL) AND (@sColumnName IS NOT NULL)
        BEGIN
            SET @sColumnName =
            (
                SELECT MIN(QUOTENAME(COLUMN_NAME))
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_SCHEMA = PARSENAME(@sTableName, 2)
                AND TABLE_NAME = PARSENAME(@sTableName, 1)
                AND DATA_TYPE IN ('char', 'varchar', 'nchar', 'nvarchar')
                AND QUOTENAME(COLUMN_NAME) > @sColumnName
            )

            IF @sColumnName IS NOT NULL
            BEGIN
                INSERT INTO @oTableResult
                EXEC
                (
                    'SELECT '''+@sTableName+'.'+@sColumnName+''',LEFT('+@sColumnName+',3630)
                    FROM '+@sTableName+' (NOLOCK) ' +
                    ' WHERE '+@sColumnName+' LIKE '+@sSearch2
                )
            END
        END
    END

    SELECT sColumnName, ColumnValue 
    FROM @oTableResult
END