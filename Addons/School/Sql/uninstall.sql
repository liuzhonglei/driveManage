DELETE FROM `wp_attribute` WHERE model_id = (SELECT id FROM wp_model WHERE `name`='school' ORDER BY id DESC LIMIT 1);
DELETE FROM `wp_model` WHERE `name`='school' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_school`;

DELETE FROM `wp_attribute` WHERE model_id = (SELECT id FROM wp_model WHERE `name`='school_place' ORDER BY id DESC LIMIT 1);
DELETE FROM `wp_model` WHERE `name`='school_place' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_school_place`;

DELETE FROM `wp_attribute` WHERE model_id = (SELECT id FROM wp_model WHERE `name`='shop_product' ORDER BY id DESC LIMIT 1);
DELETE FROM `wp_model` WHERE `name`='shop_product' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_shop_product`;

DELETE FROM `wp_attribute` WHERE model_id = (SELECT id FROM wp_model WHERE `name`='student' ORDER BY id DESC LIMIT 1);
DELETE FROM `wp_model` WHERE `name`='student' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_student`;


DELETE FROM `wp_attribute` WHERE model_id = (SELECT id FROM wp_model WHERE `name`='student_apprise' ORDER BY id DESC LIMIT 1);
DELETE FROM `wp_model` WHERE `name`='student_apprise' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_student_apprise`;


DELETE FROM `wp_attribute` WHERE model_id = (SELECT id FROM wp_model WHERE `name`='student_event' ORDER BY id DESC LIMIT 1);
DELETE FROM `wp_model` WHERE `name`='student_event' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_student_event`;

DELETE FROM `wp_attribute` WHERE model_id = (SELECT id FROM wp_model WHERE `name`='student_question' ORDER BY id DESC LIMIT 1);
DELETE FROM `wp_model` WHERE `name`='student_question' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_student_question`;

DELETE FROM `wp_attribute` WHERE model_id = (SELECT id FROM wp_model WHERE `name`='teacher' ORDER BY id DESC LIMIT 1);
DELETE FROM `wp_model` WHERE `name`='teacher' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_teacher`;


drop view wp_teacher_rank;
drop view wp_teacher_rank_all;