-- This script updates existing instances of the Vada Project 2.0 to the new schema. --

-- Convert charsets to utf8mb4 --
ALTER TABLE claimsdb CONVERT TO CHARACTER
SET
    utf8mb4 COLLATE utf8mb4_general_ci;

ALTER TABLE flagsdb CONVERT TO CHARACTER
SET
    utf8mb4 COLLATE utf8mb4_general_ci;

-- Drop unused columns --
ALTER TABLE `claimsdb`
DROP IF EXISTS `thesisST`,
DROP IF EXISTS `reasonST`,
DROP IF EXISTS `ruleST`,
DROP IF EXISTS `supportID`;

-- Allow null values --
ALTER TABLE `claimsdb`
MODIFY `subject` varchar(255),
MODIFY `targetP` varchar(255),
MODIFY `supportMeans` varchar(255),
MODIFY `example` varchar(255),
MODIFY `URL` varchar(255),
MODIFY `reason` varchar(255),
MODIFY `active` int (1) NOT NULL DEFAULT 1,
MODIFY `vidtimestamp` longtext,
MODIFY `citation` longtext,
MODIFY `transcription` longtext,
MODIFY `COS` varchar(10);

-- Nullify all NA values --
UPDATE claimsdb
SET
    subject = NULLIF(subject, 'NA'),
    targetP = NULLIF(targetP, 'NA'),
    supportMeans = NULLIF(supportMeans, 'NA'),
    example = NULLIF(example, 'NA'),
    URL = NULLIF(URL, 'NA'),
    reason = NULLIF(reason, 'NA'),
    topic = NULLIF(topic, 'NA'),
    vidtimestamp = NULLIF(vidtimestamp, 'NA'),
    citation = NULLIF(citation, 'NA'),
    transcription = NULLIF(transcription, 'NA'),
    COS = NULLIF(COS, 'NA');

-- Change active to a Boolean --
ALTER TABLE claimsdb MODIFY active BOOLEAN NOT NULL DEFAULT 1;
UPDATE claimsdb
SET
    active = CASE
        WHEN active = '1' THEN TRUE
        ELSE FALSE
    END;

ALTER TABLE flagsdb MODIFY isRootRival BOOLEAN NOT NULL DEFAULT 0;

UPDATE claimsdb
SET
    active = CASE
        WHEN active = '1' THEN TRUE
        ELSE FALSE
    END;

-- Remove invalid flag rows --
DELETE FROM flagsdb
WHERE flagsdb.claimIDFlagger NOT IN (
  SELECT claimsdb.claimID FROM claimsdb
)
OR WHERE isRootRival != 0
AND flagsdb.claimIDFlagged NOT IN (
  SELECT claimsdb.claimID FROM claimsdb
);

-- Create foreign key constraints for flagsdb --
ALTER TABLE `flagsdb`
ADD FOREIGN KEY (`claimIDFlagged`) REFERENCES `claimsdb`(`claimID`) ON DELETE CASCADE,
ADD FOREIGN KEY (`claimIDFlagger`) REFERENCES `claimsdb`(`claimID`) ON DELETE CASCADE;

-- Rename tables --
ALTER TABLE `flagsdb`
RENAME TO `Flag`;
ALTER TABLE `claimsdb`
RENAME TO `Claim`;

-- Create the Topic table
CREATE TABLE IF NOT EXISTS Topic (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    name TINYTEXT NOT NULL UNIQUE,
    description TINYTEXT
);
-- Add a new column "topic_id" to the Claim table
ALTER TABLE Claim
ADD COLUMN IF NOT EXISTS topic_id INT NOT NULL;

-- Populate the Topic table with distinct topic names from the Claim table
INSERT IGNORE INTO Topic (name)
SELECT DISTINCT topic FROM Claim;
-- Update the Claim table with corresponding topic ids from the Topic table
UPDATE Claim c
JOIN Topic t ON c.topic = t.name
SET c.topic_id = t.id;

-- Create a foreign key constraint for Claim's topic_id --
ADD CONSTRAINT fk_claim_topic
  FOREIGN KEY (topic_id)
  REFERENCES Topic(id)
  ON DELETE CASCADE;

-- Drop old topic column
ALTER TABLE Claim
DROP COLUMN topic;

-- Complete topic table; make name non-unique --
ALTER TABLE Topic
MODIFY `name` TINYTEXT NOT NULL;
ALTER TABLE `Topic` DROP INDEX `name`; 

-- Create Group table --

CREATE TABLE IF NOT EXISTS `Group` (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    name TINYTEXT NOT NULL,
    access_code TINYTEXT NOT NULL,
)

-- Rename ID columns --
ALTER TABLE `Claim`
CHANGE `claimID` `id` int AUTO_INCREMENT;
ALTER TABLE `Flag`
CHANGE `flagID` `id` int AUTO_INCREMENT;

-- Add timestamp column --
ALTER TABLE `Topic`
ADD COLUMN `ts` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;
-- Backport existing timestamps -- 
UPDATE Topic
SET ts = COALESCE((SELECT MIN(ts) FROM Claim WHERE Claim.topic_id = Topic.id), CURRENT_TIMESTAMP);