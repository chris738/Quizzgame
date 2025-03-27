-- Setup

    -- Löschen der bestehenden Datenbank
    DROP DATABASE IF EXISTS quizgame;

    -- Erstellung einer neuen Datenbank
    CREATE DATABASE quizgame;

    -- Erstellung eines neuen Benutzers
    CREATE USER IF NOT EXISTS 'quizgame'@'localhost' IDENTIFIED BY 'sicheresPasswort';

    -- Berechtigungen für den Benutzer
    GRANT ALL PRIVILEGES ON quizgame.* TO 'quizgame'@'localhost';

    -- Wechsel zur neuen Datenbank
    USE quizgame;

    -- enable global event scheudler
    SET GLOBAL event_scheduler = ON;

-- Tabelle: Fragen
    CREATE TABLE Fragen (
        QuestionID INT AUTO_INCREMENT PRIMARY KEY,
        Question VARCHAR(1000) NOT NULL,
        Answer1 VARCHAR(1000) NOT NULL,
        Answer2 VARCHAR(1000) NOT NULL,
        Answer3 VARCHAR(1000) NOT NULL,
        Answer4 VARCHAR(1000) NOT NULL
    );

-- Tabelle: player
    CREATE TABLE player (
        PlayerID INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
		password VARCHAR(1000) NOT NULL,
        points Int (1000) DEFAULT 0,
    );
	
	
-- Prozedur: Spieler erstellen und initialisieren

DELIMITER $$

CREATE PROCEDURE AddPlayer(
    IN p_name VARCHAR(100),
    IN p_password VARCHAR(1000),
	IN p_points INT(1000)
)
BEGIN
    INSERT INTO player (name, password, points) 
    VALUES (p_name, p_password, 0);
END $$

DELIMITER ;



-- Prozedur: Neue Fragen und Antworten einfügen
DELIMITER //

CREATE PROCEDURE InsertQuestion(
    IN p_Question VARCHAR(1000),
    IN p_Answer1 VARCHAR(1000),
    IN p_Answer2 VARCHAR(1000),
    IN p_Answer3 VARCHAR(1000),
    IN p_Answer4 VARCHAR(1000)
)
BEGIN
    INSERT INTO Fragen (Question, Answer1, Answer2, Answer3, Answer4)
    VALUES (p_Question, p_Answer1, p_Answer2, p_Answer3, p_Answer4);
END //

DELIMITER ;



-- Prozedur: Spieler entfernen 

DELIMITER //

CREATE PROCEDURE DeletePlayer(
    IN p_PlayerID INT
)
BEGIN
    DELETE FROM player WHERE PlayerID = p_PlayerID;
END //

DELIMITER ;



-- Prozedur: Fragen entfernen

DELIMITER //

CREATE PROCEDURE DeleteQuestion(
    IN p_QuestionID INT
)
BEGIN
    DELETE FROM Fragen WHERE QuestionID = p_QuestionID;
END //

DELIMITER ;


