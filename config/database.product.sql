-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 08-Jul-2023 às 20:46
-- Versão do servidor: 10.4.27-MariaDB
-- versão do PHP: 8.0.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Banco de dados: id21258140_dbspaceart
--
-- --------------------------------------------------------
DROP DATABASE IF EXISTS id21258140_dbspaceart;
CREATE DATABASE id21258140_dbspaceart;
USE id21258140_dbspaceart;

-- CRIAÇÃO DAS ENTIDADES

CREATE TABLE IF NOT EXISTS users(
  id varchar(36) PRIMARY KEY,
  token VARCHAR(36) UNIQUE KEY,
  placing int UNIQUE KEY AUTO_INCREMENT,
  name varchar(256) NOT NULL,
  email varchar(256) UNIQUE KEY NOT NULL,
  phone varchar(11) NOT NULL,
  password varchar(256) NOT NULL,
  CEP varchar(8) NOT NULL,
  state varchar(2) NOT NULL,
  city varchar(50) NOT NULL,
  image varchar(256),
  website varchar(256),
  rate float DEFAULT 0,
  description varchar(256) DEFAULT '',
  type enum ("artist", "enterprise"),
  verified boolean DEFAULT 0,

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS artist(

  id varchar(36) PRIMARY KEY,
  CPF varchar(11) UNIQUE KEY NOT NULL,
  art enum("escultura", "pintura", "dança", "música", "teatro") NOT NULL,
  wage float NOT NULL,
  birthday date NOT NULL,

  CONSTRAINT artist_user_fk FOREIGN KEY (id) REFERENCES users(id)  ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS enterprise(

  id varchar(36) PRIMARY KEY,
  CNPJ varchar(14) UNIQUE KEY NOT NULL,
  neighborhood varchar(256) NOT NULL,
  address varchar(256) NOT NULL,
  company_name varchar(256) NOT NULL,
  section varchar(256) NOT NULL,

  CONSTRAINT enterprise_user_fk FOREIGN KEY (id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS agreement(

  id varchar(36) PRIMARY KEY,
  hirer varchar(36) NOT NULL,
  hired varchar(36) NOT NULL,
  description varchar(256),
  price float unsigned NOT NULL,
  date date NOT NULL,
  start_time time NOT NULL,
  end_time  time NOT NULL,
  art varchar(256) NOT NULL,
  status enum("send", "accepted", "recused", "canceled")  DEFAULT "send",

  CONSTRAINT hirer_user_fk FOREIGN KEY (hirer) REFERENCES enterprise(id) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT hired_user_fk FOREIGN KEY (hired) REFERENCES artist(id) ON UPDATE CASCADE ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS selection(

  id varchar(36) PRIMARY KEY,
  title VARCHAR(256),
  owner varchar(36) NOT NULL,
  price float unsigned NOT NULL,
  start_timestamp timestamp NOT NULL,
  end_timestamp timestamp NOT NULL,
  art varchar(256) NOT NULL,
  locked boolean DEFAULT 1,
  
  CONSTRAINT owner_fk FOREIGN KEY (owner) REFERENCES enterprise(id) ON UPDATE CASCADE ON DELETE CASCADE


) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS selection_application(

  selection varchar(36) NOT NULL,
  artist varchar(36) NOT NULL,
  last_change timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  PRIMARY KEY (selection, artist),
  CONSTRAINT selection_fk FOREIGN KEY (selection) REFERENCES selection (id) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT artist_fk FOREIGN KEY (artist) REFERENCES artist (id) ON UPDATE CASCADE ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS report(

  id varchar(36) PRIMARY KEY,
  reporter varchar(36),
  reported varchar(36) NOT NULL,
  reason varchar(256) NOT NULL,
  accepted boolean,

  CONSTRAINT reporter_user_fk FOREIGN KEY (reporter) REFERENCES users (id) ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT reported_user_fk FOREIGN KEY (reported) REFERENCES users (id) ON UPDATE CASCADE ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS chat(
  id varchar(36) PRIMARY KEY,
  artist varchar(36),
  enterprise varchar(36),
  last_message varchar(256),
  
  CONSTRAINT artist_member_fk FOREIGN KEY (artist) REFERENCES artist (id) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT enterprise_member_fk FOREIGN KEY (enterprise) REFERENCES enterprise (id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS message(
    chat varchar(36),
    sender varchar(36),
    content varchar(256) NOT NULL,
    shipping_datetime timestamp DEFAULT CURRENT_TIMESTAMP,
    
    PRIMARY KEY (chat, sender, shipping_datetime),
    CONSTRAINT chat_fk FOREIGN KEY (chat) REFERENCES chat (id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT sender_user_fk FOREIGN KEY (sender) REFERENCES users (id) ON UPDATE CASCADE ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS rate(

    author varchar(36),
    agreement varchar(36),
    rate float NOT NULL,
    description varchar(256),

    PRIMARY KEY(author, agreement),
    CONSTRAINT author_user_fk FOREIGN KEY (author) REFERENCES users (id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT agreement_fk FOREIGN KEY (agreement) REFERENCES agreement (id) ON UPDATE CASCADE ON DELETE CASCADE
    
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS post(
    id varchar(36) PRIMARY KEY,
    author varchar(36),
    message varchar(256),
    media varchar(256),
    post_time timestamp DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT poster_fk FOREIGN KEY (author) REFERENCES users(id)
    
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- CRIA VIEWS

CREATE VIEW  artist_view AS
SELECT usr.id, usr.placing as 'index', usr.name, usr.image, usr.CEP, usr.state, usr.city, artist.art, artist.wage, usr.rate, usr.website, usr.description
FROM artist, users AS usr
WHERE usr.id = artist.id;

CREATE VIEW enterprise_view AS
SELECT usr.id, usr.placing as 'index', usr.name, ent.company_name, ent.section, usr.image, usr.CEP, usr.state, usr.city, ent.neighborhood, ent.address, usr.rate, usr.website, usr.description
FROM enterprise AS ent, users AS usr
WHERE usr.id = ent.id;

-- CRIA EVENTOS

SET GLOBAL event_scheduler = 1;
SET @@GLOBAL.event_scheduler = 1;

DELIMITER $
  
-- LIBERA SELEÇÕES CUJO TIMESTAMP INICAL FOI ALCANÇADO E O END NÃO
CREATE EVENT IF NOT EXISTS
    start_selection ON SCHEDULE EVERY 5 MINUTE DO
    UPDATE selection SET locked = 0 WHERE locked = 1
    AND start_timestamp <= CURRENT_TIMESTAMP
    AND end_timestamp > CURRENT_TIMESTAMP;



-- TRANCA SELEÇÕES CUJO TIMESTAMP END FOI ALCANÇADO
CREATE EVENT IF NOT EXISTS
 finish_selection ON SCHEDULE EVERY 5 MINUTE DO
    UPDATE selection SET locked = 1 WHERE locked = 0
    AND end_timestamp <= CURRENT_TIMESTAMP;

DELIMITER ;


-- CRIA GATILHOS

DELIMITER $

CREATE TRIGGER tgr_update_last_message_in_chat AFTER INSERT
ON message
FOR EACH ROW
BEGIN
  UPDATE chat SET last_message = NEW.content WHERE chat.id = NEW.chat
END $$

DELIMITER ;
