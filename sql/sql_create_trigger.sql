CREATE TRIGGER _table_dates_on_insert
ON _table FOR INSERT

AS
    UPDATE  _table
    SET insert_date = CAST((CONVERT(CHAR(8), GETDATE(), 112)+ REPLACE(CONVERT(CHAR(8),GETDATE(), 114),':','')) AS CHAR(14))
    , update_date = CAST((CONVERT(CHAR(8), GETDATE(), 112)+ REPLACE(CONVERT(CHAR(8),GETDATE(), 114),':','')) AS CHAR(14))
    FROM  INSERTED
    WHERE  _table.id = INSERTED.id

GO

CREATE TRIGGER _table_date_on_update
ON _table FOR UPDATE

AS
    UPDATE  _table
    SET
        update_date = CAST((CONVERT(CHAR(8), GETDATE(), 112)+ REPLACE(CONVERT(CHAR(8),GETDATE(), 114),':','')) AS CHAR(14))
    FROM  INSERTED
    WHERE  _table.id = INSERTED.id
GO

/*
DROP TRIGGER dates_on_insert
GO

CREATE TRIGGER dates_on_insert
ON version_sql FOR INSERT

AS
    UPDATE  version_sql
    SET 
        date = CAST((CONVERT(CHAR(8), GETDATE(), 112)+ REPLACE(CONVERT(CHAR(8),GETDATE(), 114),':','')) AS CHAR(14))
    FROM  INSERTED
    WHERE  version_sql.id = INSERTED.id

DROP TRIGGER date_on_update
GO

CREATE TRIGGER date_on_update
ON version_sql FOR UPDATE

AS
    UPDATE  version_sql
    SET
        date = CAST((CONVERT(CHAR(8), GETDATE(), 112)+ REPLACE(CONVERT(CHAR(8),GETDATE(), 114),':','')) AS CHAR(14))
    FROM INSERTED
    WHERE  version_sql.id = INSERTED.id

*/