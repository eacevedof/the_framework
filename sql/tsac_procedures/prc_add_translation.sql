/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.0.0
 * @name prc_add_translation
 * @file prc_add_translation.sql
 * @date 25-08-2014 05:30 (SPAIN)
 * @observations: 
 * @requires:
 */
IF (EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID('prc_add_translation')))
    DROP PROCEDURE dbo.prc_add_translation;
GO

/*
1:english
2:spanish
3:dutch
4:papiaments
*/
CREATE PROCEDURE [dbo].[prc_add_translation]
    @sTableFrom VARCHAR(25)=''
    , @sType VARCHAR(15)=''
    , @sIdLanguage VARCHAR(15) = '2' --spanish
AS

    DECLARE @sSQL VARCHAR(8000);
    DECLARE @id INT;

    SET @id=(SELECT id FROM base_language WHERE id=CONVERT(INT,@sIdLanguage));

    IF(@id IS NOT NULL)
    BEGIN

        UPDATE app_temp_translation
        SET id_language=@sIdLanguage

        SET @sSQL = 
        '
        INSERT INTO '+@sTableFrom+'_lang (id_source,id_language,description,order_by)

        SELECT ar.id AS id_source
        ,tr.id_language
        ,UPPER(RTRIM(LTRIM(description_tr))) AS description
        ,''1'' AS order_by
        FROM '+@sTableFrom+' AS ar
        INNER JOIN app_temp_translation AS tr
        ON tr.code_erp = ar.code_erp
        WHERE ar.type = '''+@sType+'''
        AND ar.id NOT IN
        (
            -- las que no se encuentran traducidas
            SELECT id_source 
            FROM '+@sTableFrom+'_lang
            WHERE id_language='+@sIdLanguage+'
        )
        ORDER BY ar.id,tr.code_erp,tr.description_tr
        ';
        -- PRINT @sSQL;
        EXECUTE(@sSQL);

        SET @sTableFrom = @sTableFrom+'_lang';

        EXEC prc_set_sysvars @sTableFrom

        -- Elimino las traducciones que ya no existen
        SET @sSQL = 'DELETE FROM '+@sTableFrom+'_lang
                     WHERE id_source 
                     NOT IN 
                    (SELECT id FROM '+@sTableFrom+')'
        PRINT @sSQL;
        EXECUTE(@sSQL);

        SET @sSQL = 'SELECT * FROM '+@sTableFrom+' ORDER BY id DESC'
        PRINT @sSQL;
        EXECUTE(@sSQL);
    END
    ELSE
    BEGIN
        PRINT 'LANGUAGE '+@sIdLanguage+' DOES NOT EXIST!'
    END
GO

-- EXEC prc_add_translation 'app_suspicion_array','profession','2'

