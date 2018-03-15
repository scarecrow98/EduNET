-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Gép: 127.0.0.1
-- Létrehozás ideje: 2018. Már 15. 23:57
-- Kiszolgáló verziója: 10.1.25-MariaDB
-- PHP verzió: 7.1.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Adatbázis: `edunet`
--
CREATE DATABASE IF NOT EXISTS `edunet` DEFAULT CHARACTER SET utf8 COLLATE utf8_hungarian_ci;
USE `edunet`;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `evaluated_tests`
--

DROP TABLE IF EXISTS `evaluated_tests`;
CREATE TABLE `evaluated_tests` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `test_instance_id` int(10) UNSIGNED NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

--
-- A tábla adatainak kiíratása `evaluated_tests`
--

INSERT INTO `evaluated_tests` (`user_id`, `test_instance_id`, `date`) VALUES
(44, 12, '2018-03-15 19:45:07');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `groups`
--

DROP TABLE IF EXISTS `groups`;
CREATE TABLE `groups` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci NOT NULL,
  `author_id` int(10) UNSIGNED NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci DEFAULT NULL,
  `avatar` varchar(37) COLLATE utf8_hungarian_ci NOT NULL DEFAULT 'group-default.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

--
-- A tábla adatainak kiíratása `groups`
--

INSERT INTO `groups` (`id`, `name`, `author_id`, `description`, `avatar`) VALUES
(15, 'Teszt csoport 1', 42, '', 'group-default.png');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `group_members`
--

DROP TABLE IF EXISTS `group_members`;
CREATE TABLE `group_members` (
  `group_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

--
-- A tábla adatainak kiíratása `group_members`
--

INSERT INTO `group_members` (`group_id`, `user_id`) VALUES
(15, 43),
(15, 44);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
  `id` int(10) UNSIGNED NOT NULL,
  `sender_id` int(10) UNSIGNED NOT NULL,
  `receiver_id` int(10) UNSIGNED NOT NULL,
  `text` text COLLATE utf8_hungarian_ci NOT NULL,
  `date` datetime NOT NULL,
  `is_seen` tinyint(4) NOT NULL DEFAULT '0',
  `is_delivered` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` int(10) UNSIGNED NOT NULL,
  `author_id` int(10) UNSIGNED NOT NULL,
  `group_id` int(10) UNSIGNED NOT NULL,
  `subject_id` int(10) UNSIGNED NOT NULL,
  `text` varchar(100) COLLATE utf8_hungarian_ci NOT NULL,
  `date` date NOT NULL,
  `type` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `subjects`
--

DROP TABLE IF EXISTS `subjects`;
CREATE TABLE `subjects` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

--
-- A tábla adatainak kiíratása `subjects`
--

INSERT INTO `subjects` (`id`, `name`) VALUES
(1, 'Matematika'),
(2, 'Informatika'),
(3, 'Magyar nyelv');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `tasks`
--

DROP TABLE IF EXISTS `tasks`;
CREATE TABLE `tasks` (
  `id` int(10) UNSIGNED NOT NULL,
  `test_id` int(10) UNSIGNED NOT NULL,
  `task_number` tinyint(4) NOT NULL,
  `question` text COLLATE utf8_hungarian_ci NOT NULL,
  `text` text COLLATE utf8_hungarian_ci,
  `max_points` tinyint(4) NOT NULL,
  `image` varchar(37) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `type` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

--
-- A tábla adatainak kiíratása `tasks`
--

INSERT INTO `tasks` (`id`, `test_id`, `task_number`, `question`, `text`, `max_points`, `image`, `type`) VALUES
(14, 10, 1, 'Írd le Petőfi Sándor ars poeticaját!', '', 12, '0fcd2105b2baeacccb7eeced3e4ef71b.jpg', 2),
(15, 10, 2, 'Mikor halt meg a költő?', '', 5, NULL, 1),
(16, 10, 3, 'Töltsd fel a házi feladatként készített fogalmazásodat!', '', 20, NULL, 5),
(17, 11, 1, 'Jellemezd a Sociel Engeneering-et!', '', 8, NULL, 2);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `task_options`
--

DROP TABLE IF EXISTS `task_options`;
CREATE TABLE `task_options` (
  `id` int(10) UNSIGNED NOT NULL,
  `task_id` int(10) UNSIGNED NOT NULL,
  `text` text COLLATE utf8_hungarian_ci NOT NULL,
  `correct_ans` char(1) COLLATE utf8_hungarian_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

--
-- A tábla adatainak kiíratása `task_options`
--

INSERT INTO `task_options` (`id`, `task_id`, `text`, `correct_ans`) VALUES
(29, 15, '1834', '0'),
(30, 15, '1848', '1'),
(31, 15, '1923', '0'),
(32, 15, '1776', '0');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `task_results`
--

DROP TABLE IF EXISTS `task_results`;
CREATE TABLE `task_results` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `task_id` int(10) UNSIGNED NOT NULL,
  `test_instance_id` int(10) UNSIGNED NOT NULL,
  `result` char(5) COLLATE utf8_hungarian_ci NOT NULL,
  `comment` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

--
-- A tábla adatainak kiíratása `task_results`
--

INSERT INTO `task_results` (`user_id`, `task_id`, `test_instance_id`, `result`, `comment`) VALUES
(44, 14, 12, '12/2', 'Ez igen gyenge megfogalmazás lett!'),
(44, 15, 12, '5/5', NULL),
(44, 16, 12, '20/20', 'A házi feladatod szép munka!');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `tests`
--

DROP TABLE IF EXISTS `tests`;
CREATE TABLE `tests` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(100) COLLATE utf8_hungarian_ci NOT NULL,
  `text` text COLLATE utf8_hungarian_ci,
  `subject_id` int(10) UNSIGNED NOT NULL,
  `task_count` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

--
-- A tábla adatainak kiíratása `tests`
--

INSERT INTO `tests` (`id`, `title`, `text`, `subject_id`, `task_count`) VALUES
(10, 'Teszt feladatlap', 'Boldogtalan voltam\r\nTeljes életemben;\r\nCsak az vigasztal, hogy\r\nMeg nem érdemeltem.\r\n\r\nBoldogtalan leszek,\r\nKoporsóm zártáig;\r\nCsak az vigasztal, hogy\r\nNincs messze odáig.\r\n\r\nHiába biztattok\r\nHiába beszéltek -\r\nTudom azt az egyet,\r\nHogy nem soká élek.', 3, 3),
(11, 'Teszt feladatlap 2', '', 2, 1);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `test_instances`
--

DROP TABLE IF EXISTS `test_instances`;
CREATE TABLE `test_instances` (
  `id` int(10) UNSIGNED NOT NULL,
  `test_id` int(10) UNSIGNED NOT NULL,
  `original_author_id` int(10) UNSIGNED NOT NULL,
  `current_author_id` int(10) UNSIGNED NOT NULL,
  `group_id` int(10) UNSIGNED NOT NULL,
  `creation_date` date NOT NULL,
  `description` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

--
-- A tábla adatainak kiíratása `test_instances`
--

INSERT INTO `test_instances` (`id`, `test_id`, `original_author_id`, `current_author_id`, `group_id`, `creation_date`, `description`, `status`) VALUES
(12, 10, 42, 42, 15, '2018-03-16', NULL, 2),
(13, 11, 42, 42, 15, '2018-03-15', '', 2);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `login_id` char(4) COLLATE utf8_hungarian_ci NOT NULL,
  `name` varchar(50) COLLATE utf8_hungarian_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_hungarian_ci NOT NULL,
  `is_subscribed` tinyint(4) NOT NULL DEFAULT '0',
  `pass_hash` char(64) COLLATE utf8_hungarian_ci NOT NULL,
  `pass_salt` char(32) COLLATE utf8_hungarian_ci NOT NULL,
  `avatar` varchar(37) COLLATE utf8_hungarian_ci NOT NULL DEFAULT 'user-default.png',
  `type` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

--
-- A tábla adatainak kiíratása `users`
--

INSERT INTO `users` (`id`, `login_id`, `name`, `email`, `is_subscribed`, `pass_hash`, `pass_salt`, `avatar`, `type`) VALUES
(32, 'aa00', 'Révész Ferenc', 'info.edunet@gmail.com', 0, '002209d2f770bbd0a97533cf7a5863420a7ba438650ba5a04a48126d5828dcaa', '88e7c4945d2f9dfa8ec0fcf130bdc3c9', 'user-default.png', 2),
(42, 'aa03', 'Révész Ferenc', 'r_ferenc98@onbox.hu', 1, '14c3136d017b956407434551bad6e6c70d424d2562fe732264938ebbfa07b710', '2cbfa18ade19e1e348f58ed6da7d9911', 'user-default.png', 1),
(43, 'aa04', 'Váli Dániel', 'dani@gmail.com', 0, 'b5c75331ec2641cd8e2e3d27404f2289126c494e326b887748a832afda71ea6f', '40a79c70fb86fdf2c3de670d18390c0e', 'user-default.png', 0),
(44, 'aa05', 'Szapu Norbert', 'szapuka@gmail.com', 0, '3346620fe9af987cc2dd2cdb343cd20024ce849099b386291cffcb78445ff298', '9dcda8524fe9fb5b35463be185014152', 'user-default.png', 0),
(45, 'aa06', 'Kiss Gábor', 'gabor@gmail.com', 1, '78d68f70e29e4fedef90b46f92f051735fae43c9f6d0c4ff388ec3d0ec50f1d2', '9cc664acca7559c0dbe74986bb128a22', 'user-default.png', 1),
(46, 'aa07', 'Váli Dániel', 'alfa@gmail.com', 0, 'bd3156c8ac8fe8ae168bb492bc0e771fd28ffe4bcb43bf95db9d25a5fe39194c', 'f6b2564c0d615bc5e6e13b754fc1a7f7', 'user-default.png', 0),
(47, 'aa08', 'Váli Dániel', 'betasa@gmail.com', 0, '291d32909b3028f3e8f12d4473fcdbb26505d389c61eb912429edb84468a32cf', 'b559ee8bf80458a9139f12effa38b25d', 'user-default.png', 0);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `user_answers`
--

DROP TABLE IF EXISTS `user_answers`;
CREATE TABLE `user_answers` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `task_option_id` int(10) UNSIGNED NOT NULL,
  `test_instance_id` int(10) UNSIGNED NOT NULL,
  `answer` char(1) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `is_correct` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

--
-- A tábla adatainak kiíratása `user_answers`
--

INSERT INTO `user_answers` (`id`, `user_id`, `task_option_id`, `test_instance_id`, `answer`, `is_correct`) VALUES
(1, 44, 29, 12, '0', 1),
(2, 44, 30, 12, '1', 1),
(3, 44, 31, 12, '0', 1),
(4, 44, 32, 12, '0', 1);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `user_file_answers`
--

DROP TABLE IF EXISTS `user_file_answers`;
CREATE TABLE `user_file_answers` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `task_id` int(10) UNSIGNED NOT NULL,
  `test_instance_id` int(10) UNSIGNED NOT NULL,
  `file_name` char(36) COLLATE utf8_hungarian_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

--
-- A tábla adatainak kiíratása `user_file_answers`
--

INSERT INTO `user_file_answers` (`id`, `user_id`, `task_id`, `test_instance_id`, `file_name`) VALUES
(1, 44, 16, 12, 'ec82ce96a46bcda35f24493e09162f56.zip');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `user_text_answers`
--

DROP TABLE IF EXISTS `user_text_answers`;
CREATE TABLE `user_text_answers` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `task_id` int(10) UNSIGNED NOT NULL,
  `test_instance_id` int(10) UNSIGNED NOT NULL,
  `answer` text COLLATE utf8_hungarian_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

--
-- A tábla adatainak kiíratása `user_text_answers`
--

INSERT INTO `user_text_answers` (`id`, `user_id`, `task_id`, `test_instance_id`, `answer`) VALUES
(1, 44, 14, 12, 'Lorem ipsum dolor sit amet.');

--
-- Indexek a kiírt táblákhoz
--

--
-- A tábla indexei `evaluated_tests`
--
ALTER TABLE `evaluated_tests`
  ADD UNIQUE KEY `unqiue_evaluated_test` (`user_id`,`test_instance_id`),
  ADD KEY `test_instance_id` (`test_instance_id`),
  ADD KEY `user_id` (`user_id`);

--
-- A tábla indexei `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `group_members`
--
ALTER TABLE `group_members`
  ADD UNIQUE KEY `unique_group_member` (`user_id`,`group_id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `user_id` (`user_id`);

--
-- A tábla indexei `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- A tábla indexei `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `author_id` (`author_id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- A tábla indexei `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `test_id` (`test_id`);

--
-- A tábla indexei `task_options`
--
ALTER TABLE `task_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`);

--
-- A tábla indexei `task_results`
--
ALTER TABLE `task_results`
  ADD UNIQUE KEY `unique_task_result` (`user_id`,`task_id`,`test_instance_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `test_instance_id` (`test_instance_id`);

--
-- A tábla indexei `tests`
--
ALTER TABLE `tests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- A tábla indexei `test_instances`
--
ALTER TABLE `test_instances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `original_author_id` (`original_author_id`),
  ADD KEY `current_author_id` (`current_author_id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `test_id` (`test_id`);

--
-- A tábla indexei `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login_id` (`login_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- A tábla indexei `user_answers`
--
ALTER TABLE `user_answers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_answer` (`user_id`,`test_instance_id`,`task_option_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `task_option_id` (`task_option_id`),
  ADD KEY `test_instance_id` (`test_instance_id`);

--
-- A tábla indexei `user_file_answers`
--
ALTER TABLE `user_file_answers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_file_answer` (`user_id`,`test_instance_id`,`task_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `test_instance_id` (`test_instance_id`);

--
-- A tábla indexei `user_text_answers`
--
ALTER TABLE `user_text_answers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_text_answer` (`user_id`,`test_instance_id`,`task_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `test_instance_id` (`test_instance_id`);

--
-- A kiírt táblák AUTO_INCREMENT értéke
--

--
-- AUTO_INCREMENT a táblához `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT a táblához `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT a táblához `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT a táblához `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT a táblához `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT a táblához `task_options`
--
ALTER TABLE `task_options`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;
--
-- AUTO_INCREMENT a táblához `tests`
--
ALTER TABLE `tests`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT a táblához `test_instances`
--
ALTER TABLE `test_instances`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT a táblához `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;
--
-- AUTO_INCREMENT a táblához `user_answers`
--
ALTER TABLE `user_answers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT a táblához `user_file_answers`
--
ALTER TABLE `user_file_answers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT a táblához `user_text_answers`
--
ALTER TABLE `user_text_answers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- Megkötések a kiírt táblákhoz
--

--
-- Megkötések a táblához `evaluated_tests`
--
ALTER TABLE `evaluated_tests`
  ADD CONSTRAINT `evaluated_tests_ibfk_1` FOREIGN KEY (`test_instance_id`) REFERENCES `test_instances` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `evaluated_tests_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Megkötések a táblához `group_members`
--
ALTER TABLE `group_members`
  ADD CONSTRAINT `group_members_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `group_members_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Megkötések a táblához `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Megkötések a táblához `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `notifications_ibfk_3` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Megkötések a táblához `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`test_id`) REFERENCES `tests` (`id`);

--
-- Megkötések a táblához `task_options`
--
ALTER TABLE `task_options`
  ADD CONSTRAINT `task_options_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`);

--
-- Megkötések a táblához `task_results`
--
ALTER TABLE `task_results`
  ADD CONSTRAINT `task_results_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `task_results_ibfk_2` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `task_results_ibfk_3` FOREIGN KEY (`test_instance_id`) REFERENCES `test_instances` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Megkötések a táblához `tests`
--
ALTER TABLE `tests`
  ADD CONSTRAINT `tests_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`);

--
-- Megkötések a táblához `test_instances`
--
ALTER TABLE `test_instances`
  ADD CONSTRAINT `test_instances_ibfk_1` FOREIGN KEY (`original_author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `test_instances_ibfk_2` FOREIGN KEY (`current_author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `test_instances_ibfk_3` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `test_instances_ibfk_4` FOREIGN KEY (`test_id`) REFERENCES `tests` (`id`);

--
-- Megkötések a táblához `user_answers`
--
ALTER TABLE `user_answers`
  ADD CONSTRAINT `user_answers_ibfk_1` FOREIGN KEY (`test_instance_id`) REFERENCES `test_instances` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_answers_ibfk_2` FOREIGN KEY (`task_option_id`) REFERENCES `task_options` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_answers_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Megkötések a táblához `user_file_answers`
--
ALTER TABLE `user_file_answers`
  ADD CONSTRAINT `user_file_answers_ibfk_1` FOREIGN KEY (`test_instance_id`) REFERENCES `test_instances` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_file_answers_ibfk_2` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_file_answers_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Megkötések a táblához `user_text_answers`
--
ALTER TABLE `user_text_answers`
  ADD CONSTRAINT `user_text_answers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_text_answers_ibfk_2` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_text_answers_ibfk_3` FOREIGN KEY (`test_instance_id`) REFERENCES `test_instances` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
