/*GRUPOS QUE ESTÁN POR DEBAJO DEL USUARIO*/
--CREATE VIEW vbase_hieruser_groupchild AS
ALTER VIEW vbase_hieruser_groupchild AS
SELECT DISTINCT usgrp.id_user
--, usgrp.id_user_group
, vhgrp.id_group AS id_group_child
--, vhgrp.id_group_parent
FROM base_users_groups AS usgrp
INNER JOIN vbase_hiergroup AS vhgrp
ON usgrp.id_user_group = vhgrp.id_group_parent 
--WHERE 1=1


SELECT *
FROM vbase_hieruser_groupchild
WHERE id_user=4
GO

SELECT *
FROM vbase_hieruser_groupchild
WHERE id_group_child=4
GO