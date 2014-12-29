CREATE VIEW [dbo].[vapp_sysoptimizer] AS
select sysobjects.name 
, sum(case when sysindexes.indid<2 then rows 
else 0 end) as rows 
, sum(case when sysindexes.indid in (0,1,255) then sysindexes.reserved 
else 0 end) * 8 as reserved 
, sum(case when sysindexes.indid in (0,1) then sysindexes.dpages 
when sysindexes.indid=255 then sysindexes.used 
else 0 end) * 8 as Data 
, (sum(case when sysindexes.indid in (0,1,255) then sysindexes.used 
else 0 end) 
- sum(case when sysindexes.indid in (0,1) then sysindexes.dpages 
when sysindexes.indid=255 then sysindexes.used 
else 0 end))*8 as index_size 
, (sum(case when sysindexes.indid in (0,1,255) then 
sysindexes.reserved-sysindexes.used 
else 0 end)*8) as unused 
from sysobjects 
join sysindexes 
on sysobjects.id=sysindexes.id 
where xtype='U' 
group by sysobjects.name