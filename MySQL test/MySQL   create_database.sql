MySQL 設定１

CREATE DATABASE ideastock;

use ideastock;

CREATE TABLE user(
id int AUTO_INCREMENT,
loginId  varchar(10) Not Null UNIQUE,
password varchar(10) Not Null UNIQUE,
name     varchar(10) Not NUll,
PRIMARY KEY(id)
);

CREATE TABLE question(
id     int AUTO_INCREMENT,
userId int Not Null,
question varchar(256) Not Null,
date DateTime Not Null,
deleteflg tinyint(1) Not Null DEFAULT 0,
PRIMARY KEY(id),
FOREIGN KEY(userId) REFERENCES user(id)
);

CREATE TABLE answer(
id BigINT  AUTO_INCREMENT,
questionId int Not Null,
userId int Not Null,
answer varchar(256) Not Null,
date DateTime Not Null,
deleteFlg tinyint(1) Not Null DEFAULT 0,
PRIMARY KEY(id),
FOREIGN KEY(questionId) REFERENCES question(id),
FOREIGN KEY(userId) REFERENCES user(id)
);