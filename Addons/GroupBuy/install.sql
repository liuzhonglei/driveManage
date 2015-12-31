CREATE TABLE IF NOT EXISTS `wp_groupbuy_party` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `time`  int(10)  COMMENT '参与时间',
  `is_refund`  char(2)   DEFAULT 0 COMMENT '是否退款',
  `groupbuy_info_id`  int(10)  COMMENT '团购信息',
  `openid`  varchar(50)  COMMENT 'openid',
  `token`  varchar(50)  COMMENT 'token',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT  INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`psstest`,`list_grid_text`,`list_grid_admin`) VALUES ('groupbuy_party','团购参与人','0','','1','{"1":["time","is_refund","token"]}','1:基础','','lists','','','openid:姓名\r\nphone:电话\r\nprivilege:优惠金额（元）\r\ntime|day_format:参与时间\r\nis_refund|get_name_by_status:是否退款\r\nid:操作:refund&id=[id]|确认退款','10','','','1450171645','1451448548','1','MyISAM','','','');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('time','参与时间','int(10)','datetime','','','1','','0','0','1','1450581590','1450581590','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('is_refund','是否退款','char(2) ','select','0','','1','0:否\r\n1:是','0','0','1','1451448476','1450581774','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('groupbuy_info_id','团购信息','int(10)','num','','','0','','0','0','1','1450777803','1450777803','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('openid','openid','varchar(50)','string','','','0','','0','0','1','1451145743','1450941233','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('token','token','varchar(50)','string','','','1','','0','0','1','1451137951','1451137951','','3','','regex','','3','function');
UPDATE `wp_attribute` SET model_id= (SELECT MAX(id) FROM `wp_model`) WHERE model_id=0;


CREATE TABLE IF NOT EXISTS `wp_groupbuy_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name`  varchar(50)  COMMENT '名称',
  `payitem_id`  int(10)  COMMENT '支付项目',
  `duration`  int(5)  COMMENT '持续时间(天)',
  `people_num`  int(5)  COMMENT '参与人数',
  `remark`  text  COMMENT '备注说明',
  `title`  text  COMMENT '活动标题',
  `introduce`  text  COMMENT '活动简介',
  `strategy`  text  COMMENT '优惠策略',
  `token`  varchar(50)  COMMENT 'token',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`psstest`,`list_grid_text`,`list_grid_admin`) VALUES ('groupbuy_type','团购类型','0','','1','{"1":["name","title","introduce","people_num","duration","payitem_id","strategy","remark"]}','1:基础','','','','','name:名称\r\npayitem_id:支付项目\r\nduration:持续天数\r\npeople_num:可参与人数\r\nid:操作:[EDIT]|编辑,[DELETE]|删除','10','name','','1450171580','1451443721','1','MyISAM','','','');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('name','名称','varchar(50)','string','','','1','','0','0','1','1450171802','1450171802','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('payitem_id','支付项目','int(10)','select','','','1','','0','0','1','1450939543','1450171909','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('duration','持续时间(天)','int(5)','num','','','1','','0','0','1','1450172004','1450172004','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('people_num','参与人数','int(5)','num','','','1','','0','0','1','1450172042','1450172042','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('remark','备注说明','text','textarea','','活动的相关说明','1','','0','0','1','1450172077','1450172077','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('title','活动标题','text','textarea','','','1','','0','0','1','1451442825','1451442825','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('introduce','活动简介','text','textarea','','','1','','0','0','1','1451442852','1451442852','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('strategy','优惠策略','text','textarea','','可直接填写参与人数满时的金额，或者 1:50;2:100  (表示一个人优惠50，两个人优惠100)','1','','0','0','1','1451378561','1450939737','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('token','token','varchar(50)','string','','','0','','0','0','1','1450941408','1450941408','','3','','regex','','3','function');
UPDATE `wp_attribute` SET model_id= (SELECT MAX(id) FROM `wp_model`) WHERE model_id=0;


CREATE TABLE IF NOT EXISTS `wp_groupbuy_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `time`  int(10) NOT NULL  COMMENT '发起时间',
  `type_id`  int(20)  COMMENT '团购类型',
  `token`  varchar(50)  COMMENT 'token',
  `openid`  varchar(50)  COMMENT 'openid',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`psstest`,`list_grid_text`,`list_grid_admin`) VALUES ('groupbuy_info','团购信息','0','','1','{"1":["type_id","time","token"]}','1:基础','','','','','name:名称\r\nopenid:发起人\r\npeople_num:可参与人数\r\nsub_num:还可参与人数\r\ntime|day_format:发起日期\r\nsub_day:剩余天数\r\nid:15%操作:listsParty&groupbuy_info_id=[id]|参与人','10','','','1450171593','1451446348','1','MyISAM','','','');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('time','发起时间','int(10) NOT NULL','datetime','','','1','','0','0','1','1451116123','1450580404','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('type_id','团购类型','int(20)','select','','','1','','0','0','1','1450775827','1450580455','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('token','token','varchar(50)','string','','','1','','0','0','1','1450941446','1450941446','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('openid','openid','varchar(50)','string','','','0','','0','0','1','1450941474','1450941474','','3','','regex','','3','function');
UPDATE `wp_attribute` SET model_id= (SELECT MAX(id) FROM `wp_model`) WHERE model_id=0;


CREATE VIEW wp_groupbuy_info_all AS
SELECT
	t.id,
	t.time,
	t.token,
	t1.name,
	t.openid,
	t1.people_num,
	t1.strategy,
	t1.remark,
	to_days(from_unixtime(t.time)) +t1.duration - to_days(from_unixtime(unix_timestamp()))  as sub_day,
	t1.people_num - (
		SELECT
			 count(*)
		FROM
			wp_groupbuy_party t2
		WHERE
			t2.groupbuy_info_id = t.id
	) AS sub_num
FROM
	wp_groupbuy_info t
LEFT JOIN wp_groupbuy_type t1 ON t.token = t1.token and t.type_id = t1.id;