-- database : umadan

-- user ---'uma_admin'@'msi' identified by 'cog_gvout'
--       |-'uma_python'@'msi' identified by 'cog_dwxpyt'
--       --'uma_php'@'msi' identified by 'cog_dpd'

-- grant --- all on umadan . * to 'uma_admin'@'msi'
--        |- select, delete, insert on umadan . * to 'uma_python'@'msi'
--        -- select on umadan . * to 'uma_php'@'msi'

/*
  ユーザー作成
*/
create user 'uma_admin'@'3.136.245.58' identified by 'cog_gvout';
create user 'uma_python'@'3.136.245.58' identified by 'cog_dwxpyt';
create user 'uma_php'@'3.136.245.58' identified by 'cog_dpd';

/*
  権限付与
*/
grant all on umadan . * to 'uma_admin'@'3.136.245.58';
grant select, delete, insert on umadan . * to 'uma_python'@'3.136.245.58';
grant select on umadan . * to 'uma_php'@'3.136.245.58';

SHOW GRANTS FOR 'uma_php'@'3.136.245.58';
