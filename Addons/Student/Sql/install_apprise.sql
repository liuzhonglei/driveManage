CREATE TABLE IF NOT EXISTS `wp_student_apprise` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`level`  char(10) NOT NULL  COMMENT '级别',
`id_teacher`  int(10) NOT NULL  COMMENT '教练ID',
`id_student`  int(10) NOT NULL  COMMENT '学员ID',
`apprise`  varchar(255) NOT NULL  COMMENT '评价',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`) VALUES ('student_apprise','学员评价','0','','1','{"1":["level","id_teacher","id_student","apprise"]}','1:基础','','','','','teacher:教练\r\nstudent:学员\r\nlevel:评价\r\nid:10%操作:[EDIT]|查看','10','','','1431515030','1431595115','1','MyISAM');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('level','级别','char(10) NOT NULL','select','','','1','1:1星\r\n2:2星\r\n3:3星\r\n4:4星\r\n5:5星','0','1','1','1431515229','1431515216','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('id_teacher','教练ID','int(10) NOT NULL','num','','','1','','0','1','1','1431515117','1431515117','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('id_student','学员ID','int(10) NOT NULL','num','','','1','','0','1','1','1431515159','1431515159','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('apprise','评价','varchar(255) NOT NULL','string','','','1','','0','0','1','1431515264','1431515264','','3','','regex','','3','function');
UPDATE `wp_attribute` SET model_id= (SELECT MAX(id) FROM `wp_model`) WHERE model_id=0;

#view
create view wp_student_apprise_all as
select t1.*,t2.name student ,t3.name teacher from wp_student_apprise t1 left join wp_student t2 on t1.id_student = t2.id left join wp_teacher t3 on t1.id_teacher = t3.id;