-- The current implementation, but using a stored procedure

DROP PROCEDURE IF EXISTS get_top_subjects_students;

DELIMITER ;;

CREATE PROCEDURE get_top_subjects_students(
    IN in_level_id INT,
    IN howmany INT
)
BEGIN
SELECT student_id, CAST(JSON_UNQUOTE(JSON_EXTRACT(mat, "$.score")) AS UNSIGNED) AS score, JSON_UNQUOTE(JSON_EXTRACT(mat, "$.grade")) AS grade
FROM `exam_information_system`.`signal-506`
WHERE level_id = in_level_id
ORDER BY score DESC
LIMIT howmany;
END;;

DELIMITER ;

-- Level 8
-- Howmany 3
-- Table signal-506
-- Subject mat

-- Get the last score for the subject

DROP PROCEDURE IF EXISTS get_top_subjects_students_last_score;

DELIMITER ;;

CREATE PROCEDURE get_top_subjects_students_last_score(
    IN in_level_id INT,
    IN howmany INT,
    OUT last_score INT
)
BEGIN
SELECT CAST(JSON_UNQUOTE(JSON_EXTRACT(mat, "$.score")) AS UNSIGNED) AS score INTO last_score
FROM `exam_information_system`.`signal-506`
WHERE level_id = in_level_id
ORDER BY score DESC
LIMIT howmany, 1;
END;;

DELIMITER ;

-- Dynamic top three students int subject for a level

DROP PROCEDURE IF EXISTS dynamic_get_top_subjects_students;

DELIMITER ;;

CREATE PROCEDURE dynamic_get_top_subjects_students(
    IN in_level_id INT,
    IN howmany INT,
    OUT last_score INT
)
BEGIN
SELECT CAST(JSON_UNQUOTE(JSON_EXTRACT(mat, "$.score")) AS UNSIGNED) AS score INTO last_score
FROM `exam_information_system`.`signal-506`
WHERE level_id = in_level_id
ORDER BY score DESC
LIMIT howmany, 1;

SELECT student_id, CAST(JSON_UNQUOTE(JSON_EXTRACT(mat, "$.score")) AS UNSIGNED) AS score, JSON_UNQUOTE(JSON_EXTRACT(mat, "$.grade")) AS grade
FROM `exam_information_system`.`signal-506`
WHERE level_id = in_level_id
AND CAST(JSON_UNQUOTE(JSON_EXTRACT(mat, "$.score")) AS UNSIGNED) >= last_score
ORDER BY score DESC;

END;;

DELIMITER ;
