CREATE DATABASE IF NOT EXISTS rwdd;
USE rwdd;

CREATE TABLE IF NOT EXISTS `users` (
  `user_ID` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` int(11) NOT NULL DEFAULT 1, -- 1 for user, 2 for admin
  PRIMARY KEY (`user_ID`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `projects` (
  `Project_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Title` varchar(255) NOT NULL,
  `Description` text,
  `Project_Start_Date` date DEFAULT NULL,
  `Project_End_Date` date DEFAULT NULL,
  `Project_Status` varchar(50) DEFAULT 'Not Started',
  `Progress_Percent` int(11) DEFAULT 0,
  `User_ID` int(11) DEFAULT NULL,
  PRIMARY KEY (`Project_ID`),
  KEY `User_ID` (`User_ID`),
  CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`User_ID`) REFERENCES `users` (`user_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `project_members` (
  `Member_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Project_ID` int(11) DEFAULT NULL,
  `User_ID` int(11) DEFAULT NULL,
  PRIMARY KEY (`Member_ID`),
  KEY `Project_ID` (`Project_ID`),
  KEY `User_ID` (`User_ID`),
  CONSTRAINT `project_members_ibfk_1` FOREIGN KEY (`Project_ID`) REFERENCES `projects` (`Project_ID`),
  CONSTRAINT `project_members_ibfk_2` FOREIGN KEY (`User_ID`) REFERENCES `users` (`user_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `tasks` (
  `Task_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Title` varchar(255) NOT NULL,
  `Description` text,
  `Status` varchar(50) DEFAULT 'Open',
  `Category` varchar(255) DEFAULT NULL,
  `Priority` varchar(50) DEFAULT NULL,
  `Task_Start_Time` date DEFAULT NULL,
  `Task_End_Time` date DEFAULT NULL,
  `Task_Created_Date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Task_Completed_Date` date DEFAULT NULL,
  `Assigner_ID` int(11) DEFAULT NULL,
  `User_ID` int(11) DEFAULT NULL,
  `Project_ID` int(11) DEFAULT NULL,
  PRIMARY KEY (`Task_ID`),
  KEY `Assigner_ID` (`Assigner_ID`),
  KEY `User_ID` (`User_ID`),
  KEY `Project_ID` (`Project_ID`),
  CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`Assigner_ID`) REFERENCES `users` (`user_ID`),
  CONSTRAINT `tasks_ibfk_2` FOREIGN KEY (`User_ID`) REFERENCES `users` (`user_ID`),
  CONSTRAINT `tasks_ibfk_3` FOREIGN KEY (`Project_ID`) REFERENCES `projects` (`Project_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `files` (
  `File_ID` int(11) NOT NULL AUTO_INCREMENT,
  `File_Name` varchar(255) DEFAULT NULL,
  `File_Path` varchar(255) DEFAULT NULL,
  `File_Type` varchar(255) DEFAULT NULL,
  `File_Upload_Time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Task_ID` int(11) DEFAULT NULL,
  `User_ID` int(11) DEFAULT NULL,
  PRIMARY KEY (`File_ID`),
  KEY `Task_ID` (`Task_ID`),
  KEY `User_ID` (`User_ID`),
  CONSTRAINT `files_ibfk_1` FOREIGN KEY (`Task_ID`) REFERENCES `tasks` (`Task_ID`),
  CONSTRAINT `files_ibfk_2` FOREIGN KEY (`User_ID`) REFERENCES `users` (`user_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `Goal` (
  `Goal_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Title` varchar(255) DEFAULT NULL,
  `Description` text,
  `Status` varchar(255) DEFAULT NULL,
  `Goal_Start_Time` date DEFAULT NULL,
  `Goal_End_Time` date DEFAULT NULL,
  `User_ID` int(11) DEFAULT NULL,
  PRIMARY KEY (`Goal_ID`),
  KEY `User_ID` (`User_ID`),
  CONSTRAINT `goal_ibfk_1` FOREIGN KEY (`User_ID`) REFERENCES `users` (`user_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create a project
INSERT INTO `projects` (`Title`, `Description`) VALUES ('Test Project', 'This is a test project.');
