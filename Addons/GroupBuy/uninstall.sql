DELETE FROM `wp_attribute` WHERE model_id = (SELECT id FROM wp_model WHERE `name`='groupbuy_info' ORDER BY id DESC LIMIT 1);
DELETE FROM `wp_model` WHERE `name`='groupbuy_info' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_groupbuy_info`;

DELETE FROM `wp_attribute` WHERE model_id = (SELECT id FROM wp_model WHERE `name`='groupbuy_party' ORDER BY id DESC LIMIT 1);
DELETE FROM `wp_model` WHERE `name`='groupbuy_party' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_groupbuy_party`;

DELETE FROM `wp_attribute` WHERE model_id = (SELECT id FROM wp_model WHERE `name`='groupbuy_type' ORDER BY id DESC LIMIT 1);
DELETE FROM `wp_model` WHERE `name`='groupbuy_type' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_groupbuy_type`;

drop view wp_groupbuy_info_all;
