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
DROP DATABASE IF EXISTS id21492551_spaceart;
CREATE DATABASE id21492551_spaceart;
USE id21492551_spaceart;

-- CRIAÇÃO DAS ENTIDADES

CREATE TABLE users(
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
  image text,
  website varchar(256),
  rate float DEFAULT 0,
  description varchar(256) DEFAULT '',
  type enum ("artist", "enterprise"),
  verified boolean DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE artist(
  id varchar(36) PRIMARY KEY,
  CPF varchar(11) UNIQUE KEY NOT NULL,
  art enum("sculpture", "painting", "dance", "music", "acting") NOT NULL,
  wage float NOT NULL,
  birthday date NOT NULL,

  CONSTRAINT artist_user_fk FOREIGN KEY (id) REFERENCES users(id)  ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE enterprise(
  id varchar(36) PRIMARY KEY,
  CNPJ varchar(14) UNIQUE KEY NOT NULL,
  neighborhood varchar(256) NOT NULL,
  address varchar(256) NOT NULL,
  company_name varchar(256) NOT NULL,
  section varchar(256) NOT NULL,

  CONSTRAINT enterprise_user_fk FOREIGN KEY (id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE agreement(
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

CREATE TABLE selection(
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

CREATE TABLE selection_application(
  selection varchar(36) NOT NULL,
  artist varchar(36) NOT NULL,
  last_change timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  PRIMARY KEY (selection, artist),
  CONSTRAINT selection_fk FOREIGN KEY (selection) REFERENCES selection (id) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT artist_fk FOREIGN KEY (artist) REFERENCES artist (id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE report(
  id varchar(36) PRIMARY KEY,
  reporter varchar(36),
  reported varchar(36) NOT NULL,
  reason varchar(256) NOT NULL,
  accepted boolean,

  CONSTRAINT reporter_user_fk FOREIGN KEY (reporter) REFERENCES users (id) ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT reported_user_fk FOREIGN KEY (reported) REFERENCES users (id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE chat(
  id varchar(36) PRIMARY KEY,
  artist varchar(36),
  enterprise varchar(36),
  last_message varchar(256),
  
  CONSTRAINT artist_member_fk FOREIGN KEY (artist) REFERENCES artist (id) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT enterprise_member_fk FOREIGN KEY (enterprise) REFERENCES enterprise (id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE message(
    chat varchar(36),
    sender varchar(36),
    content varchar(256) NOT NULL,
    shipping_datetime timestamp DEFAULT CURRENT_TIMESTAMP,
    
    PRIMARY KEY (chat, sender, shipping_datetime),
    CONSTRAINT chat_fk FOREIGN KEY (chat) REFERENCES chat (id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT sender_user_fk FOREIGN KEY (sender) REFERENCES users (id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE rate(
    author varchar(36),
    agreement varchar(36),
    rate float NOT NULL,
    description varchar(256),

    PRIMARY KEY(author, agreement),
    CONSTRAINT author_user_fk FOREIGN KEY (author) REFERENCES users (id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT agreement_fk FOREIGN KEY (agreement) REFERENCES agreement (id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE post(
    id varchar(36) PRIMARY KEY,
    author varchar(36),
    message varchar(256),
    media text,
    post_time timestamp DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT poster_fk FOREIGN KEY (author) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- CRIA VIEWS

CREATE VIEW artist_view AS
SELECT usr.id, usr.placing as 'index', usr.verified, usr.name, artist.birthday,usr.image, usr.CEP, usr.state, usr.city, artist.art, artist.wage, usr.rate, usr.website, usr.description
FROM artist, users AS usr
WHERE usr.id = artist.id;

CREATE VIEW enterprise_view AS
SELECT usr.id, usr.placing as 'index', usr.verified, usr.name, ent.company_name, ent.section, usr.image, usr.CEP, usr.state, usr.city, ent.neighborhood, ent.address, usr.rate, usr.website, usr.description
FROM enterprise AS ent, users AS usr
WHERE usr.id = ent.id;


-- CRIA GATILHOS-- CRIA PROCEDIMENTOS

DELIMITER $$

  CREATE PROCEDURE
  prc_find_rated_user (
    INOUT user_id VARCHAR(36),
    IN agreement_id VARCHAR(36)
  )
  BEGIN
    DECLARE author_type VARCHAR(10);
    SELECT type INTO author_type FROM users WHERE id = user_id;
    SELECT
      CASE
        WHEN author_type = 'artist' THEN hirer
        ELSE hired
      END
    INTO user_id
    FROM agreement
    WHERE id = agreement_id;
  END $$

DELIMITER ;


DELIMITER $$

  CREATE  PROCEDURE
  prc_update_user_rating_average (IN user_id VARCHAR(36))
  BEGIN
      DECLARE rating_average FLOAT;
      -- TRANFERE VALOR DA MÉDIA PARA rating_average
      SELECT AVG(rate.rate)
      INTO rating_average
      FROM rate
      WHERE rate.agreement IN (
          SELECT id
          FROM agreement
          WHERE user_id IN (hirer, hired) AND status = "accepted"
      ) AND rate.author <> user_id;
      -- CONSIDERA TODOS OS CONTRATOS ACEITOS QUE O USUÁRIO PARTICIPA

      UPDATE users
      SET rate = rating_average
      WHERE id = user_id;
  END $$

DELIMITER ;

-- CRIA GATILHOS

DELIMITER $$

  -- Atualiza média de um usuário em caso de nova avaliação sobre ele
  CREATE TRIGGER
  tgr_update_user_rating_avarage_on_insert AFTER INSERT
  ON rate
  FOR EACH ROW
  BEGIN
    DECLARE user_id VARCHAR(36) DEFAULT NEW.author;
    CALL prc_find_rated_user(user_id, NEW.agreement);
    CALL prc_update_user_rating_average(user_id);
  END $$

DELIMITER ;


DELIMITER $$

    -- Atualiza média de um usuário em caso de atualização numa avaliação sobre ele
    CREATE TRIGGER
    tgr_update_user_rating_avarage_on_update AFTER UPDATE
    ON rate
    FOR EACH ROW
    BEGIN
      DECLARE user_id VARCHAR(36) DEFAULT NEW.author;
      CALL prc_find_rated_user(user_id, NEW.agreement);
      CALL prc_update_user_rating_average(user_id);
    END $$

DELIMITER ;


DELIMITER $

    -- Atualiza média de um usuário em caso do apagamento de uma avaliação sobre ele
    CREATE TRIGGER
    tgr_update_user_rating_avarage_on_delete BEFORE DELETE
    ON rate
    FOR EACH ROW
    BEGIN
      DECLARE user_id VARCHAR(36) DEFAULT OLD.author;
      CALL prc_find_rated_user(user_id, OLD.agreement);
      CALL prc_update_user_rating_average(user_id);
    END $$

DELIMITER ;


DELIMITER $$

    CREATE TRIGGER IF NOT EXISTS
    tgr_update_last_message_in_chat AFTER INSERT
    ON message
    FOR EACH ROW
    BEGIN
    UPDATE chat SET last_message = NEW.content WHERE chat.id = NEW.chat;
    END $$

DELIMITER ;
