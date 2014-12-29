-- update source filename
UPDATE app_picture
SET source_filename=SUBSTRING(information_extra,46,CHARINDEX('exten',information_extra)-52) 
WHERE shortname IS NULL