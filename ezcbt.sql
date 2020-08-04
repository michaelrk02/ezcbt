-- Adminer 4.7.1 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `courses`;
CREATE TABLE `courses` (
  `course_id` char(8) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(500) NOT NULL,
  `locked` tinyint(4) NOT NULL,
  `duration` int(11) NOT NULL,
  `num_questions` int(11) NOT NULL,
  `num_choices` int(11) NOT NULL,
  `correct_answers` varchar(500) NOT NULL,
  `allow_empty` tinyint(4) NOT NULL,
  `score_correct` int(11) NOT NULL,
  `score_empty` int(11) NOT NULL,
  `score_wrong` int(11) NOT NULL,
  `signature` char(32) NOT NULL,
  PRIMARY KEY (`course_id`),
  UNIQUE KEY `title` (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `session_id` char(8) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `course_id` char(8) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `user_id` char(8) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `answer_data` varchar(500) NOT NULL,
  `start_time` bigint(20) NOT NULL,
  `state` char(20) NOT NULL,
  `score` int(11) NOT NULL,
  `details` varchar(50) NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `course_id` (`course_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`),
  CONSTRAINT `sessions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `user_id` char(8) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- 2020-08-04 09:23:41
