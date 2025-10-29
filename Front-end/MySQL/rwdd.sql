-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 29, 2025 at 01:14 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rwdd`
--

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE `files` (
  `File_ID` int(11) NOT NULL,
  `File_Name` varchar(255) NOT NULL,
  `File_URL` varchar(255) DEFAULT NULL,
  `File_Type` varchar(50) DEFAULT NULL,
  `File_Upload_Time` datetime DEFAULT current_timestamp(),
  `Task_ID` int(11) DEFAULT NULL,
  `Project_ID` int(11) DEFAULT NULL,
  `User_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `files`
--

INSERT INTO `files` (`File_ID`, `File_Name`, `File_URL`, `File_Type`, `File_Upload_Time`, `Task_ID`, `Project_ID`, `User_ID`) VALUES
(4, 'AssignmentProposal.docx', '../uploads/1761664023_AssignmentProposal.docx', 'application/vnd.openxmlformats-officedocument.word', '2025-10-28 23:07:03', 2, 5, 1),
(5, 'AAPP012-4-2-RWDD-AssignmentUCDF2405ICT.pdf', '../uploads/1761664036_AAPP012-4-2-RWDD-AssignmentUCDF2405ICT.pdf', 'application/pdf', '2025-10-28 23:07:16', 2, 5, 1),
(6, '01.Introduction-To-Internet--WWW.pdf', '../uploads/1761664067_01.Introduction-To-Internet--WWW.pdf', 'application/pdf', '2025-10-28 23:07:47', 3, 5, 1);

-- --------------------------------------------------------

--
-- Table structure for table `goals`
--

CREATE TABLE `goals` (
  `Goal_ID` int(11) NOT NULL,
  `Title` varchar(255) NOT NULL,
  `Description` text DEFAULT NULL,
  `Type` varchar(50) DEFAULT NULL,
  `Status` varchar(50) DEFAULT 'In Progress',
  `Progress` int(11) DEFAULT 0,
  `Reminder_Time` datetime DEFAULT NULL,
  `Goal_Start_Time` datetime DEFAULT NULL,
  `Goal_End_Time` datetime DEFAULT NULL,
  `Goal_Created_Date` datetime DEFAULT current_timestamp(),
  `Goal_Completed_Date` datetime DEFAULT NULL,
  `User_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `goals`
--

INSERT INTO `goals` (`Goal_ID`, `Title`, `Description`, `Type`, `Status`, `Progress`, `Reminder_Time`, `Goal_Start_Time`, `Goal_End_Time`, `Goal_Created_Date`, `Goal_Completed_Date`, `User_ID`) VALUES
(10, 'lunch', 'spegetti, chicken chop', NULL, 'In Progress', 0, NULL, '2025-10-29 00:00:00', '2025-10-30 17:00:00', '2025-10-29 00:46:09', NULL, 1),
(11, 'Meeting', 'Presentation with Business Department', NULL, 'Not Started', 0, NULL, '2025-10-29 10:30:00', '2025-10-29 11:00:00', '2025-10-29 07:25:12', NULL, 7);

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `Project_ID` int(11) NOT NULL,
  `Title` varchar(255) NOT NULL,
  `Description` text DEFAULT NULL,
  `Reminder_Time` datetime DEFAULT NULL,
  `Project_Start_Date` date DEFAULT NULL,
  `Project_End_Date` date DEFAULT NULL,
  `Project_Status` varchar(50) DEFAULT 'Pending',
  `User_ID` int(11) DEFAULT NULL,
  `Progress_Percent` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`Project_ID`, `Title`, `Description`, `Reminder_Time`, `Project_Start_Date`, `Project_End_Date`, `Project_Status`, `User_ID`, `Progress_Percent`) VALUES
(5, 'Web Design', 'HTML, CSS, JS', NULL, '2025-10-28', '2025-10-31', 'Completed', 1, 100),
(7, 'Mobile App Development', 'Flutter and Firebase', NULL, '2025-10-29', '2025-11-05', 'In Progress', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `project_members`
--

CREATE TABLE `project_members` (
  `Member_ID` int(11) NOT NULL,
  `Member_Position` varchar(50) DEFAULT NULL,
  `Member_Join_Date` date DEFAULT curdate(),
  `User_ID` int(11) NOT NULL,
  `Project_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project_members`
--

INSERT INTO `project_members` (`Member_ID`, `Member_Position`, `Member_Join_Date`, `User_ID`, `Project_ID`) VALUES
(5, NULL, '2025-10-28', 1, 5),
(6, NULL, '2025-10-28', 7, 5),
(8, NULL, '2025-10-29', 1, 7),
(9, NULL, '2025-10-29', 7, 7),
(10, NULL, '2025-10-29', 9, 7);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `Role_ID` int(11) NOT NULL,
  `Role_Name` varchar(50) NOT NULL,
  `Role_Type` varchar(50) DEFAULT NULL,
  `Description` text DEFAULT NULL,
  `Role_Given_Date` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`Role_ID`, `Role_Name`, `Role_Type`, `Description`, `Role_Given_Date`) VALUES
(1, 'user', NULL, NULL, '2025-10-16'),
(2, 'admin', NULL, NULL, '2025-10-16');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `Task_ID` int(11) NOT NULL,
  `Title` varchar(255) NOT NULL,
  `Description` text DEFAULT NULL,
  `Category` varchar(50) DEFAULT NULL,
  `Priority` varchar(20) DEFAULT 'Medium',
  `Status` varchar(50) DEFAULT 'To Do',
  `Reminder_Time` datetime DEFAULT NULL,
  `Task_Start_Time` datetime DEFAULT NULL,
  `Task_End_Time` datetime DEFAULT NULL,
  `Task_Created_Date` datetime DEFAULT current_timestamp(),
  `Task_Completed_Date` datetime DEFAULT NULL,
  `User_ID` int(11) DEFAULT NULL,
  `Project_ID` int(11) NOT NULL,
  `Assigner_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`Task_ID`, `Title`, `Description`, `Category`, `Priority`, `Status`, `Reminder_Time`, `Task_Start_Time`, `Task_End_Time`, `Task_Created_Date`, `Task_Completed_Date`, `User_ID`, `Project_ID`, `Assigner_ID`) VALUES
(2, 'UIUX', 'Layout and Button', NULL, 'Medium', 'Done', NULL, NULL, '2025-10-31 00:00:00', '2025-10-28 23:06:51', NULL, 7, 5, 1),
(3, 'Back end', 'PHP', NULL, 'Medium', 'Done', NULL, NULL, '2025-10-31 00:00:00', '2025-10-28 23:07:36', NULL, 7, 5, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_ID` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `company` varchar(255) NOT NULL,
  `position` varchar(100) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL COMMENT 'i think is url path?',
  `description` text DEFAULT NULL,
  `working_hours` int(11) DEFAULT 0,
  `account_creation_date` datetime DEFAULT current_timestamp(),
  `last_login_time` datetime DEFAULT NULL,
  `last_logout_time` datetime DEFAULT NULL,
  `subscription_tier` varchar(50) DEFAULT NULL,
  `Role_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_ID`, `username`, `password`, `email`, `full_name`, `company`, `position`, `profile_picture`, `description`, `working_hours`, `account_creation_date`, `last_login_time`, `last_logout_time`, `subscription_tier`, `Role_ID`) VALUES
(1, 'Admin', '$2y$10$dZOdilJ7zAzgGaYqK6Oe2uLq8yYMeL9eBzolXZjAzTuuHkqJUZ7TC', 'jasonteo1408@gmail.com', NULL, 'Asia Pacific University', 'Manager', NULL, NULL, 0, '2025-10-26 00:01:11', NULL, NULL, NULL, 2),
(7, 'Jason', '$2y$10$6HgA8LySordvPWlmMDPrtOiaaIG111OdPmh1ZxFWIrTdO0Kgjzc/C', 'teojunjie1408@gmail.com', NULL, 'Asia Pacific University', 'Phd. Dc', NULL, NULL, 0, '2025-10-16 11:27:34', NULL, NULL, NULL, 1),
(9, 'Vera', '$2y$10$Dth1smLV72Myt1T/97/zbu64Ov8ezUDyRv5n7tsqPjcosBZXsB4r2', 'tianxin0406@gmail.com', NULL, 'Asia Pacific University', 'Lecturer', NULL, NULL, 0, '2025-10-28 23:09:24', NULL, NULL, NULL, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`File_ID`),
  ADD KEY `Project_ID` (`Project_ID`),
  ADD KEY `User_ID` (`User_ID`),
  ADD KEY `Task_ID` (`Task_ID`);

--
-- Indexes for table `goals`
--
ALTER TABLE `goals`
  ADD PRIMARY KEY (`Goal_ID`),
  ADD KEY `User_ID` (`User_ID`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`Project_ID`),
  ADD KEY `User_ID` (`User_ID`);

--
-- Indexes for table `project_members`
--
ALTER TABLE `project_members`
  ADD PRIMARY KEY (`Member_ID`),
  ADD KEY `User_ID` (`User_ID`),
  ADD KEY `Project_ID` (`Project_ID`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`Role_ID`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`Task_ID`),
  ADD KEY `User_ID` (`User_ID`),
  ADD KEY `Project_ID` (`Project_ID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_ID`),
  ADD UNIQUE KEY `Username` (`username`),
  ADD UNIQUE KEY `Email` (`email`),
  ADD KEY `Role_ID` (`Role_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `File_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `goals`
--
ALTER TABLE `goals`
  MODIFY `Goal_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `Project_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `project_members`
--
ALTER TABLE `project_members`
  MODIFY `Member_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `Task_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `files`
--
ALTER TABLE `files`
  ADD CONSTRAINT `files_ibfk_1` FOREIGN KEY (`Project_ID`) REFERENCES `projects` (`Project_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `files_ibfk_2` FOREIGN KEY (`User_ID`) REFERENCES `users` (`user_ID`) ON DELETE CASCADE;

--
-- Constraints for table `goals`
--
ALTER TABLE `goals`
  ADD CONSTRAINT `goals_ibfk_1` FOREIGN KEY (`User_ID`) REFERENCES `users` (`user_ID`) ON DELETE SET NULL;

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`User_ID`) REFERENCES `users` (`user_ID`) ON DELETE CASCADE;

--
-- Constraints for table `project_members`
--
ALTER TABLE `project_members`
  ADD CONSTRAINT `project_members_ibfk_1` FOREIGN KEY (`User_ID`) REFERENCES `users` (`user_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_members_ibfk_2` FOREIGN KEY (`Project_ID`) REFERENCES `projects` (`Project_ID`) ON DELETE CASCADE;

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`User_ID`) REFERENCES `users` (`user_ID`) ON DELETE SET NULL,
  ADD CONSTRAINT `tasks_ibfk_2` FOREIGN KEY (`Project_ID`) REFERENCES `projects` (`Project_ID`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`Role_ID`) REFERENCES `roles` (`Role_ID`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
