-- Run this in your sec_system database to create the advising_slots table.
-- Example: mysql -u root sec_system < advising_slots.sql
-- If the table already exists with old column names, this will drop and recreate it.

DROP TABLE IF EXISTS advising_slots;

CREATE TABLE advising_slots (
    slot_id     INT          NOT NULL AUTO_INCREMENT,
    faculty_id INT          NOT NULL,
    slot_date   DATE         NOT NULL,
    slot_time   TIME         NOT NULL,
    location    VARCHAR(100) NOT NULL,
    notes       TEXT,
    is_booked   TINYINT(1)   NOT NULL DEFAULT 0,
    student_id   INT          DEFAULT NULL,

    PRIMARY KEY (slot_id),

    -- A professor cannot post two slots at the exact same date + time
    UNIQUE KEY uq_professor_slot  (faculty_id, slot_date, slot_time),

    CONSTRAINT fk_slot_professor FOREIGN KEY (faculty_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_slot_student   FOREIGN KEY (student_id)   REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
