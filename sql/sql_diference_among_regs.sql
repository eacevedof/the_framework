/*actualizamos los clientes
*/
UPDATE accounts 
SET update_user=modif.update_user, update_date=modif.update_date, Code=modif.Code, Code_ofClient=modif.Code_ofClient, Name1=modif.Name1, NIF=modif.NIF, Code_Province=modif.Code_Province,
ZIP=modif.ZIP, Address1=modif.Address1, Population=modif.Population, Phone1=modif.Phone1, Fax=modif.Fax, E_mail=modif.E_mail, Code_Type=modif.Code_Type, Code_Potential=modif.Code_Potential, Code_Agrupation1=modif.Code_Agrupation1,
Code_ClassificationA=modif.Code_ClassificationA, Code_ClassificationB=modif.Code_ClassificationB, Code_ClassificationC=modif.Code_ClassificationC, Code_ClassificationD=modif.Code_ClassificationD,
Date_New=modif.Date_New, Date_Drop=modif.Date_Drop, Blocked=modif.Blocked, Observations=modif.Observations, Validated=modif.Validated, Transfered='1'
FROM 
(
SELECT 'Admin' AS update_user, View_System_Fields_ERP.update_date, mod.Code, mod.Code_ofClient, mod.Name1, mod.NIF, mod.Code_Province,
mod.ZIP, mod.Address1, mod.Population, mod.Phone1, mod.Fax, mod.E_mail, mod.Code_Type, mod.Code_Potential, mod.Code_Agrupation1,
mod.Code_ClassificationA, mod.Code_ClassificationB, mod.Code_ClassificationC, mod.Code_ClassificationD,
mod.Date_New, mod.Date_Drop, mod.Blocked, mod.Observations, mod.Validated
FROM
(
	SELECT accounts.Code, TMP_clients.Code AS Code_ofClient, LEFT(TMP_clients.Name,75) AS Name1, CIF AS NIF, TMP_clients.Code_Province,
	TMP_clients.ZIP, LEFT(Address,75) AS Address1, TMP_clients.Population, Phone AS Phone1, TMP_clients.Fax, LEFT(Email,70) AS E_mail, ModoCliente AS Code_Type, LEFT(TMP_clients.Code_Type,12) AS Code_Potential, CASE WHEN Code_DtoGroup = '' THEN NULL ELSE LEFT(Code_DtoGroup,12) END AS Code_Agrupation1,
	Tipo_Medico AS Code_ClassificationA, Code_Chain1 AS Code_ClassificationB, '' AS Code_ClassificationC, code_frec_recom AS Code_ClassificationD,
	LEFT(TMP_clients.Date_New,8) AS Date_New, LEFT(Timedata2,8) AS Date_Drop, Lock AS Blocked, TMP_clients.Description AS Observations, LEFT(code_Status_ERP,1) AS Validated
	FROM TMP_clients
	inner join accounts ON TMP_clients.Code = accounts.Code_OfClient
	
	EXCEPT
	
	SELECT Code, Code_ofClient, Name1, NIF, Code_Province,
	ZIP, Address1, Population, Phone1, Fax, E_mail, Code_Type, Code_Potential, Code_Agrupation1,
	Code_ClassificationA, Code_ClassificationB, Code_ClassificationC, Code_ClassificationD,
	Date_New, Date_Drop, Blocked, Observations, Validated
	FROM accounts
)
AS mod
CROSS JOIN View_System_Fields_ERP
) AS modif 
INNER JOIN accounts ON modif.Code_OfClient = accounts.Code_OfClient