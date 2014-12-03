select 'initializing database - start', now();
source 'init.sql';
select 'initializing database - done', now();

select 'users script - start', now();
source 'users.sql';
select 'users script - done', now();

select 'accounts script - start', now();
source 'accounts.sql';
select 'accounts script - done', now();

select 'lineitems script - start', now();
source 'lineitems.sql';
select 'lineitems script - done', now();

