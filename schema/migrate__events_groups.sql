
/* Adapt the name of the database according to your reqirements */
USE club_manager;

CREATE TABLE events_groups (
  event_id INT(11) NOT NULL,
  group_id INT(11) NOT NULL
)COMMENT 'This join-table resolves the HABTM relation between events and groups';

INSERT INTO events_groups (event_id,group_id)
  SELECT id, group_id FROM `events`
  WHERE 1;

ALTER TABLE events DROP COLUMN group_id;


