-- Paste into phpMyAdmin SQL tab to create the messaging tables.

CREATE TABLE IF NOT EXISTS advisee_list (
    id           INT NOT NULL AUTO_INCREMENT,
    professor_id INT NOT NULL,
    student_id   INT NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_pair (professor_id, student_id),
    FOREIGN KEY (professor_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id)   REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS messages (
    message_id   INT          NOT NULL AUTO_INCREMENT,
    sender_id    INT          NOT NULL,
    recipient_id INT          NOT NULL,
    subject      VARCHAR(150) NOT NULL,
    body         TEXT         NOT NULL,
    sent_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    is_read      TINYINT(1)   NOT NULL DEFAULT 0,
    PRIMARY KEY (message_id),
    FOREIGN KEY (sender_id)    REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (recipient_id) REFERENCES users(id) ON DELETE CASCADE
);
