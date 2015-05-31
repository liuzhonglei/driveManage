DELETE FROM `wp_attribute` WHERE model_id = (SELECT id FROM wp_model WHERE `name`='student' ORDER BY id DESC LIMIT 1);
DELETE FROM `wp_model` WHERE `name`='student' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_student`;

drop view wp_student_all;
