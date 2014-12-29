-- DUPLICAR TABLA:
SELECT * 
INTO app_table
FROM _template 
WHERE 1 = 1


ALTER TABLE app_table ADD DEFAULT ('1') FOR [insert_platform]
ALTER TABLE app_table ADD DEFAULT ('1') FOR [update_platform]

ALTER TABLE app_table ADD DEFAULT ('0') FOR [is_erpsent]
ALTER TABLE app_table ADD DEFAULT ('1') FOR [is_enabled]

ALTER TABLE app_table ADD DEFAULT ('1') FOR [orderby]

