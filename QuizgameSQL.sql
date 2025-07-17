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
	    CREATE TABLE Question (
	        QuestionID INT AUTO_INCREMENT PRIMARY KEY,
	        Question VARCHAR(1000) NOT NULL,
		    Category VARCHAR(100) NOT NULL,
	        Answer1 VARCHAR(1000) NOT NULL,
	        Answer2 VARCHAR(1000) NOT NULL,
	        Answer3 VARCHAR(1000) NOT NULL,
	        Answer4 VARCHAR(1000) NOT NULL,
		    correctAnswer VARCHAR(1000) NOT NULL
	    );
	
	-- Tabelle: player
    CREATE TABLE player (
        PlayerID INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(1000) NOT NULL,
        points INT DEFAULT 0
    );

    CREATE TABLE MultiplayerGame (
        GameID INT AUTO_INCREMENT PRIMARY KEY,
        RoomCode VARCHAR(10) NOT NULL UNIQUE,
        Player1ID INT NOT NULL,
        Player2ID INT DEFAULT NULL,
        IsStarted BOOLEAN DEFAULT FALSE,
        CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (Player1ID) REFERENCES player(PlayerID),
        FOREIGN KEY (Player2ID) REFERENCES player(PlayerID)
    );

    CREATE TABLE MultiplayerQuestion (
        ID INT AUTO_INCREMENT PRIMARY KEY,
        GameID INT NOT NULL,
        QuestionNumber INT NOT NULL,     -- 1 bis 16
        QuestionID INT NOT NULL,
        RoundNumber INT NOT NULL,        -- 1 bis 4
        AnsweredBy INT,                  -- Player1ID oder Player2ID
        ShownAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (GameID) REFERENCES MultiplayerGame(GameID),
        FOREIGN KEY (QuestionID) REFERENCES Question(QuestionID),
        FOREIGN KEY (AnsweredBy) REFERENCES player(PlayerID)
    );

    CREATE TABLE MultiplayerAnswer (
        ID INT AUTO_INCREMENT PRIMARY KEY,
        GameID INT NOT NULL,
        PlayerID INT NOT NULL,
        QuestionID INT NOT NULL,
        SelectedAnswer INT,
        IsCorrect BOOLEAN,
        QuestionNumber INT,  -- NULL erlaubt
        AnsweredAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (GameID) REFERENCES MultiplayerGame(GameID),
        FOREIGN KEY (PlayerID) REFERENCES player(PlayerID),
        FOREIGN KEY (QuestionID) REFERENCES Question(QuestionID)
    );


    -- Neue Tabelle: Game (gespielte Spiele)
    CREATE TABLE Game (
        GameID INT AUTO_INCREMENT PRIMARY KEY,
        PlayerID INT NOT NULL,
        QuestionID INT NOT NULL,
        SelectedAnswer INT NOT NULL,
        CorrectAnswer INT NOT NULL,
        IsCorrect BOOLEAN NOT NULL,
        Score INT NULL,
        Timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (PlayerID) REFERENCES player(PlayerID),
        FOREIGN KEY (QuestionID) REFERENCES Question(QuestionID)
    );
		

    CREATE OR REPLACE VIEW TopHighscores AS
    SELECT 
        p.name AS username,
        SUM(g.Score) AS totalScore
    FROM Game g
    JOIN player p ON g.PlayerID = p.PlayerID
    WHERE g.Score IS NOT NULL
    GROUP BY g.PlayerID
    ORDER BY totalScore DESC;


DELIMITER $$

CREATE PROCEDURE InsertNewPlayer(
    IN player_name VARCHAR(100),
    IN player_password VARCHAR(1000)
)
BEGIN
    -- Neuen Spieler in die Tabelle 'player' einfügen
    INSERT INTO player (name, password)
    VALUES (player_name, player_password);

    -- Rückmeldung
    SELECT 'Neuer Spieler wurde erfolgreich hinzugefügt!' AS message;
END $$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE InsertNewQuestion(
    IN question_text VARCHAR(1000),
    IN category VARCHAR(100),
    IN answer1 VARCHAR(1000),
    IN answer2 VARCHAR(1000),
    IN answer3 VARCHAR(1000),
    IN answer4 VARCHAR(1000),
    IN correct_answer VARCHAR(1000)
)
BEGIN
    -- Neue Frage in die Tabelle 'Question' einfügen
    INSERT INTO Question (Question, Category, Answer1, Answer2, Answer3, Answer4, correctAnswer)
    VALUES (question_text, category, answer1, answer2, answer3, answer4, correct_answer);

    -- Rückmeldung
    SELECT 'Neue Frage wurde erfolgreich hinzugefügt!' AS message;
END $$

DELIMITER ;	

-- Prozedur: Spieler erstellen und initialisieren

DELIMITER $$

CREATE PROCEDURE AddPlayer(
    IN p_name VARCHAR(100),
    IN p_password VARCHAR(1000),
	IN p_points INT(255)
)
BEGIN
    INSERT INTO player (name, password, points) 
    VALUES (p_name, p_password, 0);
END $$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE GetRandomQuestion()
BEGIN
    SELECT * FROM Question ORDER BY RAND() LIMIT 1;
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
    INSERT INTO Question (Question, Answer1, Answer2, Answer3, Answer4)
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
    DELETE FROM Question WHERE QuestionID = p_QuestionID;
END //

DELIMITER ;

-- Achievement System Tables

CREATE TABLE Achievement (
    AchievementID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(100) NOT NULL,
    Description VARCHAR(500) NOT NULL,
    Icon VARCHAR(50) DEFAULT 'trophy',
    RequirementType ENUM('QUESTIONS_ANSWERED', 'CORRECT_ANSWERS', 'GAMES_PLAYED', 'MULTIPLAYER_WINS', 'STREAK', 'CATEGORY_MASTER') NOT NULL,
    RequirementValue INT NOT NULL,
    RequirementCategory VARCHAR(100) DEFAULT NULL,
    Points INT DEFAULT 10,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE PlayerAchievement (
    PlayerAchievementID INT AUTO_INCREMENT PRIMARY KEY,
    PlayerID INT NOT NULL,
    AchievementID INT NOT NULL,
    UnlockedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (PlayerID) REFERENCES player(PlayerID) ON DELETE CASCADE,
    FOREIGN KEY (AchievementID) REFERENCES Achievement(AchievementID) ON DELETE CASCADE,
    UNIQUE KEY unique_player_achievement (PlayerID, AchievementID)
);

-- Insert default achievements
INSERT INTO Achievement (Name, Description, RequirementType, RequirementValue, Points) VALUES
('Erste Schritte', 'Beantworte deine erste Frage', 'QUESTIONS_ANSWERED', 1, 5),
('Quiz-Neuling', 'Beantworte 10 Fragen', 'QUESTIONS_ANSWERED', 10, 10),
('Quiz-Enthusiast', 'Beantworte 50 Fragen', 'QUESTIONS_ANSWERED', 50, 25),
('Quiz-Experte', 'Beantworte 100 Fragen', 'QUESTIONS_ANSWERED', 100, 50),
('Quiz-Meister', 'Beantworte 500 Fragen', 'QUESTIONS_ANSWERED', 500, 100),
('Treffsicher', 'Beantworte 10 Fragen richtig', 'CORRECT_ANSWERS', 10, 15),
('Präzise', 'Beantworte 50 Fragen richtig', 'CORRECT_ANSWERS', 50, 30),
('Perfektionist', 'Beantworte 100 Fragen richtig', 'CORRECT_ANSWERS', 100, 60),
('Spieler', 'Spiele 5 komplette Spiele', 'GAMES_PLAYED', 5, 15),
('Ausdauernd', 'Spiele 25 komplette Spiele', 'GAMES_PLAYED', 25, 40),
('Unermüdlich', 'Spiele 100 komplette Spiele', 'GAMES_PLAYED', 100, 80),
('Mehrspieler-Neuling', 'Gewinne dein erstes Mehrspieler-Spiel', 'MULTIPLAYER_WINS', 1, 20),
('Mehrspieler-Champion', 'Gewinne 10 Mehrspieler-Spiele', 'MULTIPLAYER_WINS', 10, 50),
('Glückssträhne', 'Beantworte 5 Fragen hintereinander richtig', 'STREAK', 5, 25),
('Unschlagbar', 'Beantworte 10 Fragen hintereinander richtig', 'STREAK', 10, 50);

