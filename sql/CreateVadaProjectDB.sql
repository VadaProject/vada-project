/* This SQL script populates a database with required tables. */
/* In PHPMyAdmin, create a new database, then  */

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `Claim` (
  `id` int(11) NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `targetP` varchar(255) DEFAULT NULL,
  `supportMeans` varchar(255) DEFAULT NULL,
  `example` varchar(255) DEFAULT NULL,
  `URL` varchar(255) DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `vidtimestamp` longtext DEFAULT NULL,
  `citation` longtext DEFAULT NULL,
  `transcription` longtext DEFAULT NULL,
  `ts` timestamp NOT NULL DEFAULT current_timestamp(),
  `COS` varchar(10) DEFAULT NULL,
  `topic_id` int(11) DEFAULT NULL,
  `flag_type` varchar(50) DEFAULT NULL,
  `flagged_id` int(11) DEFAULT NULL,
  `rival_id` int(11) DEFAULT NULL,
  `isRootRival` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `Group` (
  `id` int(11) NOT NULL,
  `name` tinytext NOT NULL,
  `description` tinytext DEFAULT NULL,
  `access_code` tinytext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `GroupTopic` (
  `id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL,
  `read_only` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `Topic` (
  `id` int(11) NOT NULL,
  `name` tinytext NOT NULL,
  `description` tinytext DEFAULT NULL,
  `ts` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


ALTER TABLE `Claim`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `claimID_2` (`id`),
  ADD KEY `claimID` (`id`),
  ADD KEY `fk_claim_topic` (`topic_id`);

ALTER TABLE `Group`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `GroupTopic`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_topic_group_pair` (`topic_id`,`group_id`),
  ADD KEY `FK_GroupTopic_Group` (`group_id`);

ALTER TABLE `Topic`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `Claim`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `Group`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `GroupTopic`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `Topic`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `Claim`
  ADD CONSTRAINT `fk_claim_topic` FOREIGN KEY (`topic_id`) REFERENCES `Topic` (`id`) ON DELETE CASCADE;

ALTER TABLE `GroupTopic`
  ADD CONSTRAINT `FK_GroupTopic_Group` FOREIGN KEY (`group_id`) REFERENCES `Group` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_GroupTopic_Topic` FOREIGN KEY (`topic_id`) REFERENCES `Topic` (`id`) ON DELETE CASCADE;
COMMIT;

INSERT INTO `Group` (`id`, `name`, `description`, `access_code`) VALUES
(1, 'Test Group', 'This is the default group. Might wanna delete this.', 'DEADBEEF!');

-- Creates a view that includes topic-local claim display_id
CREATE VIEW ClaimDisplayID AS
SELECT 
  c1.*, 
  (
    SELECT COUNT(*) 
    FROM Claim c2 
    WHERE c2.topic_id = c1.topic_id AND c2.id <= c1.id
  ) AS display_id
FROM 
  Claim c1
ORDER BY 
  c1.topic_id, 
  c1.id;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
