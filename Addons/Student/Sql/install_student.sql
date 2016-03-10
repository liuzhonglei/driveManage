CREATE TABLE IF NOT EXISTS `wp_student` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`number`  int(20) NOT NULL  COMMENT '电话',
`name`  varchar(255) NOT NULL  COMMENT '姓名',
`card_id`  varchar(255) NOT NULL  COMMENT '身份证号',
`sign_type`  char(10) NOT NULL  DEFAULT 0 COMMENT '报考车型',
`fee_sum`  int(10) NOT NULL  COMMENT '学费总额',
`fee_payed`  int(10)  COMMENT '已付金额',
`fee_unpay`  int(10)  COMMENT '未付金额',
`fee_type`  char(10)  COMMENT '付费方式',
`time_sign`  int(10) NOT NULL  COMMENT '报名时间',
`time_begin`  int(10) NOT NULL  COMMENT '开课事件',
`intro_source`  varchar(100) NOT NULL  COMMENT '渠道来源',
`region`  char(10) NOT NULL  COMMENT '地域',
`sign_place`  varchar(255) NOT NULL  COMMENT '报名点',
`integration`  int(10) NOT NULL  COMMENT '积分',
`sex`  varchar(5) NOT NULL  DEFAULT 'M' COMMENT '性别',
`photo`  int(10) UNSIGNED NOT NULL  COMMENT '照片',
`intro`  text NOT NULL  COMMENT '简介',
`status`  char(10) NOT NULL  DEFAULT 1 COMMENT '状态',
`id_in_teacher`  int(10) NOT NULL  COMMENT '招生教练ID',
`id_teacher`  int(10) NOT NULL  COMMENT '指导教练ID',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_student` (`id`,`name`,`number`,`card_id`,`sign_type`,`fee_sum`,`fee_payed`,`fee_unpay`,`fee_type`,`time_sign`,`time_begin`,`intro_source`,`region`,`sign_place`,`integration`,`sex`,`photo`,`intro`,`status`,`id_in_teacher`,`id_teacher`) VALUES ('2','吴汉楚','1865920234','359200967234982223','C1','100','100','0','0','1431499800','1431567900','1','1','厦门思明区','100','M','0','<p>qwerqer</p>','1','0','0');
INSERT INTO `wp_student` (`id`,`name`,`number`,`card_id`,`sign_type`,`fee_sum`,`fee_payed`,`fee_unpay`,`fee_type`,`time_sign`,`time_begin`,`intro_source`,`region`,`sign_place`,`integration`,`sex`,`photo`,`intro`,`status`,`id_in_teacher`,`id_teacher`) VALUES ('3','123123','123','12312sss','C1','0','0','0','1','0','0','0','0','','0','M','0','<p><img src="/Uploads/Editor/gh_ebd909d925e6/2015-05-13/555301d234705.JPG" title="IMG_0109.JPG"/></p>','3','0','0');
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`) VALUES ('student','学员','0','','1','{"1":["name","sex","number","region","card_id","sign_type","photo","status","time_sign","intro_source","id_in_teacher","fee_sum","fee_payed","fee_unpay","sign_place","fee_type","time_begin","id_teacher","integration","intro"]}','1:基础','','','','','name:姓名\r\nstatus:状态\r\nnumber:电话\r\ncard_id:身份证号\r\nsign_type:报考车型\r\nfee_sum:学费总额\r\nfee_payed:已付金额\r\nfee_unpay:未付金额\r\nintegration:积分\r\nid:操作:[EDIT]|编辑,[DELETE]|删除','10','name','','1431332341','1431599722','1','MyISAM');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('number','电话','int(20) NOT NULL','num','','','1','','0','1','1','1431479027','1431479011','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('name','姓名','varchar(255) NOT NULL','string','','','1','','0','1','1','1431479034','1431332374','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('card_id','身份证号','varchar(255) NOT NULL','string','','','1','','0','1','1','1431479114','1431479114','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('sign_type','报考车型','char(10) NOT NULL','select','0','','1','C1:C1\r\nC2:C2','0','1','1','1431480898','1431479248','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('fee_sum','学费总额','int(10) NOT NULL','num','','','1','','0','0','1','1431479349','1431479349','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('fee_payed','已付金额','int(10)','num','','','1','','0','0','1','1431483200','1431479397','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('fee_unpay','未付金额','int(10)','num','','','1','','0','0','1','1431483192','1431479426','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('fee_type','付费方式','char(10)','select','','','1','1:现金
2:刷卡
3:转账','0','0','1','1431483184','1431479516','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('time_sign','报名时间','int(10) NOT NULL','datetime','','','1','','0','0','1','1431481103','1431479551','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('time_begin','开课事件','int(10) NOT NULL','datetime','','','1','','0','0','1','1431481123','1431479590','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('intro_source','渠道来源','varchar(100) NOT NULL','select','','','1','教练介绍\r\n学员介绍\r\n门店介绍\r\n网店\r\n微信','0','0','1','1431481093','1431479736','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('region','地域','char(10) NOT NULL','select','','','1','0:本地\r\n1:外地','0','0','1','1431481208','1431479807','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('sign_place','报名点','varchar(255) NOT NULL','string','','','1','','0','0','1','1431481067','1431479834','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('integration','积分','int(10) NOT NULL','num','','','1','','0','0','1','1431481075','1431479927','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('sex','性别','varchar(5) NOT NULL','select','M','','1','M:男\r\nW:女','0','1','1','1431481907','1431481707','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('photo','照片','int(10) UNSIGNED NOT NULL','picture','','','1','','0','0','1','1431481824','1431481824','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('intro','简介','text NOT NULL','editor','','','1','sdf','0','0','1','1431501704','1431501704','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('status','状态','char(10) NOT NULL','select','1','','1','1:报备\r\n2:科目一\r\n3:科目二\r\n4:科目三\r\n5:科目四\r\n6:毕业\r\n','0','1','1','1431514769','1431514151','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('id_in_teacher','招生教练ID','int(10) NOT NULL','num','','','1','','0','0','1','1431597825','1431597825','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('id_teacher','指导教练ID','int(10) NOT NULL','num','','','1','','0','0','1','1431599557','1431597858','','3','','regex','','3','function');
UPDATE `wp_attribute` SET model_id= (SELECT MAX(id) FROM `wp_model`) WHERE model_id=0;

create view wp_student_all as
select t1.*,t2.name teacher,t3.name in_teacher from wp_student t1 left join wp_teacher t2 on t1.id_teacher = t2.id left join wp_teacher t3 on  t1.id_in_teacher = t3.id;




/**
  * 首页:学员图:报名流水数据
  */
DROP PROCEDURE IF EXISTS statics_date_student_sign;
DELIMITER //
CREATE PROCEDURE statics_date_student_sign(
  IN token     VARCHAR(50),
  IN date_type VARCHAR(50))
  BEGIN
    DECLARE
    date_type_format VARCHAR(50);
    -- 设置格式
    SET date_type_format = getDateFormat(date_type);

    select * from (
    SELECT
      FROM_UNIXTIME(t.time_sign, date_type_format) time,
      Count(*) AS                    count
    FROM
      wp_student t
    WHERE
      t.token = token AND
      t.time_sign IS NOT NULL AND t.time_sign != ""
      and t.time_sign <  UNIX_TIMESTAMP(now())
    GROUP BY
      FROM_UNIXTIME(t.time_sign, date_type_format)
    ORDER BY FROM_UNIXTIME(t.time_sign, date_type_format) DESC
    LIMIT 0, 4) t order by t.time;
  END;
//
CALL statics_date_student_sign('gh_36a5c6958de0', 'day');