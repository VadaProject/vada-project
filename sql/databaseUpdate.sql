START TRANSACTION;

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

-- Boolean --
ALTER TABLE claimsdb MODIFY active BOOLEAN;

UPDATE claimsdb
SET
    active = CASE
        WHEN active = '1' THEN TRUE
        ELSE FALSE
    END;

ALTER TABLE flagsdb MODIFY isRootRival BOOLEAN;

UPDATE claimsdb
SET
    active = CASE
        WHEN active = '1' THEN TRUE
        ELSE FALSE
    END;

COMMIT;