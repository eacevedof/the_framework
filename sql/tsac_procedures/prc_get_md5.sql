/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.0
 * @name prc_get_md5
 * @file prc_get_md5.sql
 * @date 31-03-2014 18:43 (SPAIN)
 * @observations: 
 * @requires:
 */
IF (EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID('prc_get_md5')))
    DROP PROCEDURE dbo.prc_get_md5;
GO

CREATE PROCEDURE prc_get_md5
    @sString VARCHAR(8000)=''
AS

    DECLARE @sMd5 VARCHAR(8000);
    SET @sMd5 = (SELECT SUBSTRING(sys.fn_sqlvarbasetostr(HASHBYTES('MD5',@sString)),3,32));
    PRINT @sMd5;

GO

-- EXEC prc_get_md5 'text_to_md5'

-- EXEC prc_get_md5 'p'
