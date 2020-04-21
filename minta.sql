CREATE TABLE topic
(
 id    int(7) NOT NULL AUTO_INCREMENT ,
 title varchar(25) NOT NULL UNIQUE,

PRIMARY KEY (id)
);


CREATE TABLE user
(
 email        varchar(45) NOT NULL UNIQUE,
 id           int(7) NOT NULL AUTO_INCREMENT ,
 password     varchar(100) NOT NULL ,
 created      datetime DEFAULT CURRENT_TIMESTAMP ,
 newsletter   tinyint DEFAULT 0,

PRIMARY KEY (id)
);


CREATE TABLE article
(
 id       int(7) NOT NULL AUTO_INCREMENT ,
 code     varchar(20) NOT NULL UNIQUE,
 filename varchar(45) NOT NULL UNIQUE,
 created  datetime DEFAULT CURRENT_TIMESTAMP ,
 points   int(7) DEFAULT 0 ,
 topic_id int(7) NOT NULL ,
 ext_link varchar(45) NOT NULL UNIQUE,
 user_id  int(7) NOT NULL ,

PRIMARY KEY (id),
FOREIGN KEY (topic_id) REFERENCES topic(id),
FOREIGN KEY (user_id) REFERENCES user(id)
);


CREATE TABLE keyword
(
 id         int(7) NOT NULL AUTO_INCREMENT ,
 article_id int(7) NOT NULL ,
 keyword    varchar(45) NOT NULL ,

PRIMARY KEY (id),
FOREIGN KEY (article_id) REFERENCES article(id)
);



CREATE TABLE comment
(
 id           int(10) NOT NULL AUTO_INCREMENT ,
 user_id      int(7) NOT NULL ,
 article_id   int(7) NOT NULL ,
 text         text NOT NULL ,
 date         datetime DEFAULT CURRENT_TIMESTAMP ,
 last_edited  datetime DEFAULT CURRENT_TIMESTAMP ,
 point        int(7) DEFAULT 0 ,
 comment_id   int(10) NULL ,

PRIMARY KEY (id),
FOREIGN KEY (user_id) REFERENCES user(id),
FOREIGN KEY (article_id) REFERENCES article(id),
FOREIGN KEY (comment_id) REFERENCES comment(id)
);


CREATE TABLE user_like
(
 id         int(10) NOT NULL AUTO_INCREMENT ,
 user_id    int(7) NOT NULL ,
 article_id int(7) NOT NULL ,
 comment_id int(10) NULL ,
 date       datetime DEFAULT CURRENT_TIMESTAMP ,

PRIMARY KEY (id),
FOREIGN KEY (user_id) REFERENCES user(id),
FOREIGN KEY (article_id) REFERENCES article(id),
FOREIGN KEY (comment_id) REFERENCES comment(id)
);


CREATE TABLE user_saved
(
 id         int(10) NOT NULL AUTO_INCREMENT ,
 user_id    int(7) NOT NULL ,
 article_id int(7) NOT NULL ,
 date       datetime DEFAULT CURRENT_TIMESTAMP ,

PRIMARY KEY (id),
FOREIGN KEY (user_id) REFERENCES user(id),
FOREIGN KEY (article_id) REFERENCES article(id)
);


CREATE TABLE user_topic
(
 id       int(10) NOT NULL AUTO_INCREMENT ,
 user_id  int(7) NOT NULL ,
 topic_id int(7) NOT NULL ,

PRIMARY KEY (id),
FOREIGN KEY (topic_id) REFERENCES topic(id),
FOREIGN KEY (user_id) REFERENCES user(id)
);
