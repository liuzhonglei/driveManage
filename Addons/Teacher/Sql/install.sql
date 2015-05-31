CREATE TABLE IF NOT EXISTS `wp_teacher` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`photo`  int(10) UNSIGNED  COMMENT '照片',
`status`  varchar(100) NOT NULL  DEFAULT 1 COMMENT '状态',
`phone`  int(10) NOT NULL  COMMENT '电话',
`time_in`  int(10) NOT NULL  COMMENT '入职时间',
`name`  varchar(255) NOT NULL  COMMENT '姓名',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_teacher` (`id`,`name`,`phone`,`time_in`,`status`,`photo`) VALUES ('1','xxx','1863224189','1431599400','1','366');
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`) VALUES ('teacher','教练','0','','1','{"1":["photo","name","phone","status","time_in"]}','1:基础','','','','','name:姓名\r\nphone:电话\r\nstatus:状态\r\ntime_in:入职时间\r\nid:15%操作:[EDIT]|编辑,score&id=[id]|评分,stu&id=[id]|学员','10','','','1431512961','1431573045','1','MyISAM');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('photo','照片','int(10) UNSIGNED','picture','','','1','','0','1','1','1431513145','1431513145','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('status','状态','varchar(100) NOT NULL','select','1','','1','1:在职\r\n2:离职','0','1','1','1431513100','1431513100','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('phone','电话','int(10) NOT NULL','num','','','1','','0','1','1','1431513038','1431513038','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('time_in','入职时间','int(10) NOT NULL','datetime','','','1','','0','1','1','1431513068','1431513068','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('name','姓名','varchar(255) NOT NULL','string','','','1','','0','1','1','1431513022','1431513022','','3','','regex','','3','function');
UPDATE `wp_attribute` SET model_id= (SELECT MAX(id) FROM `wp_model`) WHERE model_id=0;