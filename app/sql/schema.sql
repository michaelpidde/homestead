use homestead;

create table if not exists `page` (
    id int unsigned not null primary key auto_increment,
    parentId int unsigned default null,
    stub varchar(255) not null,
    title varchar(255) not null,
    content text not null,
    isHome bit not null default 0,
    published bit not null default 0,
    created datetime not null default now(),
    updated datetime
);

create table if not exists `user` (
    id int unsigned not null primary key auto_increment,
    email varchar(255) not null,
    password binary(32) not null
);

create table if not exists `page` (
    id int unsigned not null primary key auto_increment,
    parentId int unsigned,
    stub varchar(255) not null,
    title varchar(255) not null,
    content text not null,
    isHome bit not null default 0,
    published bit not null default 0,
    created datetime not null default now(),
    updated datetime
);