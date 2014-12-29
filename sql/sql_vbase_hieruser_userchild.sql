/*USUARIOS QUE ESTÁN POR DEBAJO DEL USUARIO SE INCLUYE EL MISMO*/
--CREATE VIEW vbase_hieruser_userchild AS
ALTER VIEW vbase_hieruser_userchild AS
SELECT 
vbgrpchild.id_user
--,vbgrpchild.id_group_child
,usrgrp.id_user AS id_user_child
FROM base_users_groups AS usrgrp
INNER JOIN vbase_hieruser_groupchild AS vbgrpchild
ON vbgrpchild.id_group_child = usrgrp.id_user_group
--ORDER BY 1,2,3
UNION

-- Se incluye el usuario mismo como hijo
SELECT id_user
--, id_user_group
, id_user
FROM base_users_groups

GO

SELECT *
FROM vbase_hieruser_userchild
WHERE id_user=4
GO

SELECT *
FROM vbase_hieruser_userchild
WHERE id_user_child=4
GO