create database if not exists servers;

use servers;

create table if not exists users (
     id                         bigint not null auto_increment primary key,
     username                   text not null,
     email                      text not null,
     passwordHash               text not null,
     wallet                     double not null,
     role                       enum('user', 'admin') default 'user',
     isActivated                bool default false,
     activationCode             int,
     activationCodeExpiresIn    bigint,
     rememberPasswordToken      text unique,
     unique(username)
);

alter table users add column if not exists rememberPasswordToken text unique;

create table if not exists payments (
    id                      bigint not null auto_increment primary key,
    paymentDate             bigint default null,
    createDate              bigint not null,
    ipAddress               text not null,
    status                  enum('rejected', 'incoming', 'resolved') not null,
    sum                     double not null,
    after_due               double not null,
    wallet_after_operation  double default null,
    method                  text default null,
    tid                     text not null,
    payment_status          int default null,
    payment_type            enum('fund', 'own', 'server_bought', 'server_renew') not null,

    user_id                 bigint not null,
    foreign key (user_id)   references users(id)
);

alter table payments modify if exists paymentDate bigint;
alter table payments modify if exists createDate bigint not null;
alter table payments add column if not exists tid text not null;
alter table payments add column if not exists payment_status int default null;

create table if not exists package (
   id                       int not null auto_increment primary key,
   name                     text not null,
   ram_size                 int not null,
   disk_size                int not null,
   processor_power          int not null,
   cost                     float not null,
   image_src                text not null
);


create table if not exists servers(
    id                          bigint not null auto_increment primary key,
    title                       text not null,
    status                      enum('inMagazine', 'sold', 'expired') not null,
    createDate                  bigint not null,
    expireDate                  bigint not null,
    package_id                  int not null,
    payment_id                  bigint,
    user_id                     bigint,
    pterodactyl_id              int not null,

    foreign key (payment_id)    references payments(id),
    foreign key (package_id)    references package(id),
    foreign key (user_id)       references users(id)
);

alter table servers add column if not exists pterodactyl_id int not null;

create table if not exists logs (
    id                          bigint not null auto_increment primary key,
    type                        enum('auth', 'payment', 'product') not null,
    user_id                     bigint,
    product_id                  bigint,
    payment_id                  bigint,
    date                        timestamp not null,
    message                     text not null,

    foreign key (user_id)       references users(id),
    foreign key (product_id)    references servers(id),
    foreign key (payment_id)    references payments(id)
);

create table if not exists notification (
    id                          bigint not null auto_increment primary key,
    message                     text not null ,
    isRead                      bool default false,
    date                        bigint not null,
    user_id                     bigint not null,

    foreign key (user_id)       references users(id)
);

select *
from servers.notification;
