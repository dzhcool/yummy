set names utf8;

create database if not exists ${DB_NAME};
use ${DB_NAME};

drop table if exists sessions;
create table sessions
(
    sesskey        varchar(32) not null,
    expiry         int(11) unsigned not null default '0',
    value          text,
    primary key(sesskey)
) type = innodb;

drop table if exists id_genter;
create table id_genter
(
    id             int(11) unsigned not null default '0',
    obj            varchar(30) not null default '',
    step           int(11) unsigned not null default '0'
) type = innodb;

insert into id_genter(id, obj, step) values(1, 'other', 10);
