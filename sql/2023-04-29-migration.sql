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

-- Rename flag types
UPDATE Claim
SET flag_type = "Unestablished Universal"
WHERE flag_type = "Too Broad (Unestablished Universal)";

UPDATE Claim
SET flag_type = "Counterexample"
WHERE flag_type = "Too Broad (Counterexample)";