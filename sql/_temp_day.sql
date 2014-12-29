SELECT OBIDGIRO,OBIDOPER FROM TBOBSERV
WHERE OBCODCLI
IN
( SELECT cmobscli.occodcli

FROM cmobscli)

giros:
1090014,1074221,1071613,1028553,1025978,1019584,1016182,1006895,1003842,1003466,993587,993194,965146
,953841,943923,919193,905556,902617,902448,902385,888984,884473,875774,856397,853623,807011,807011
,798265,787122,778495,775631,761707,761540,754536,753351,748836,748836,737552,714292,709147,705239
,697492,695450,687647,686996,676581,660187,658313,642773,623891,606340,603600,599114,598669,597718
,560702,547448,536116,518058,512822,497622,493301,413043,386450,380242,380242,379437,374862,371764
,340992,336783,325611,292486,286968,285558,285430,283241,222989

select *
from app_order_array

select *
from app_order_promotion

select *
from app_order_head
order by id desc

select *
from app_order_line

SELECT * 
FROM app_order_promotion        
WHERE delete_date IS NULL 
AND id_type_payment=4


select distinct email
from app_seller


select grfechag from tabgiros where gridgiro='778495'

-- 2012-04-03 00:00:00.000 =   "3-4-2012  0:00"

exec prc_table 'tbobserv','observ'
select * from tbobserv

select GRAPSREM,GRNMREMI
,GRAPECLI,GRNMBENE
,GRCDCIUD
,GRCIUDDS
,GRCIUDOR

--,GRNOMCLI
--,GRNMBENE,GRNOMPAG
from tabgiros where gridgiro='778495'

SELECT * FROM tabgiros where gridgiro='778495'

--TABGIROS.GRAPSREM)  - BENEFICIARIO: APELLIDOS DEL BENEF. (TABGIROS.GRAPCLI)
EXEC PRC_TABLE 'tabgiros','ci'

SELECT * FROM CIUDADES where CDCDCIUD='D0001'

-- traducciones de gentilicios
SELECT bl.id id_language
,ag.id id_source,bl.code_erp+'-'+ag.description
FROM base_language bl
CROSS JOIN app_gentilic ag
order by 1,2

-- traducciones de suspicion_array
SELECT ar.id as id_source
,lng.id AS id_language
,lng.code_erp + ' - '+isnull(ar.description,'nodata') as tofix
FROM base_language AS lng
CROSS JOIN
(
    select top 2000 id,code_erp,description
    ,type
    from app_suspicion_array
    where type in('profession')
    ORDER BY id,code_erp,description
) AS ar
WHERE lng.id='3' -- holandes
order by lng.id,1
--delete from 

-- para hcer copy_paste en app_country_lang
SELECT ar.id as id_source
,lng.id AS id_language
,lng.code_erp + ' - '+isnull(ar.description,'') as tofix
FROM base_language AS lng
CROSS JOIN
(
    select top 2000 id,code_erp,description
    ,type
    from app_country
    WHERE id NOT IN () 
    ORDER BY id,code_erp,description
) AS ar
order by lng.id,1

SELECT ar.id as id_source
,lng.id AS id_language
,lng.code_erp + ' - '+isnull(ar.description,'')+' gentilic' as tofix
FROM base_language AS lng
CROSS JOIN
(
    select top 5000 id, id_country,description
    from app_gentilic
    WHERE id NOT IN (SELECT id_source FROM app_gentilic_lang) 
    ORDER BY id,code_erp,description
) AS ar
order by lng.id,1