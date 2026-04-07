-- Drops the foreign key constraints on StudentNote that require StudentID/ProfessorID
-- to exist in the separate Student/Professor tables.
-- The app uses users.id for both, so these constraints must be removed.

ALTER TABLE StudentNote DROP FOREIGN KEY studentnote_ibfk_1;
ALTER TABLE StudentNote DROP FOREIGN KEY studentnote_ibfk_2;
