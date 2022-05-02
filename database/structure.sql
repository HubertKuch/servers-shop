create database if not exists servers;

create table if not exists users (
     id              bigint not null auto_increment primary key,
     username        text not null,
     email           text not null,
     passwordHash    text not null,
     wallet          int not null
);

create table if not exists payments (
    id              bigint not null auto_increment primary key,
    paymentDate     timestamp not null,
    createDate      timestamp not null,
    ipAddress       text not null,
    status          enum('rejected', 'incoming', 'resolved') not null,
    sum             double not null,
    method          text not null,

    user_id         bigint not null,
    foreign key (user_id) references users(id)
    );

create table if not exists products(
    id              bigint not null auto_increment primary key,
    title           text not null,
    status          enum('inMagazine', 'sold') not null,
    createDate      timestamp not null,
    expireDate      timestamp not null,
    package         text not null,
    payment_id      bigint,

    foreign key (payment_id) references payments(id)
    );
