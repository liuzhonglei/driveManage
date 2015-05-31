CREATE TABLE IF NOT EXISTS `wp_school` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`contribution`  text  COMMENT '驾校简介',
`photo`  int(10) UNSIGNED  COMMENT '照片',
`condition`  varchar(100)  COMMENT '基本条件',
`phone`  int(20)  COMMENT '电话',
`name`  varchar(20) NOT NULL  COMMENT '名称',
`public_id`  varchar(100) NOT NULL  COMMENT '公众号ID',
`qq`  varchar(255)  COMMENT 'QQ',
`address`  varchar(255)  COMMENT '地址',
`recruit_place`  varchar(255) NOT NULL  COMMENT '招生地点',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_school` (`id`,`recruit_place`,`public_id`,`phone`,`name`,`contribution`,`condition`,`photo`,`qq`,`address`) VALUES ('6','111','sdfas','1111','东正','<p style="text-align: center;">asasdf</p><p style="text-align: center;"><img src="/Uploads/Editor/sdfas/2015-05-19/555aebd284704.jpg" title="IMG_0272.jpg"/></p>','1,2','0','qqq','111');
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`) VALUES ('school','驾校','0','','1','{"1":["photo","name","phone","qq","address","recruit_place","condition","contribution","lean_flow"]}','1:基础','','','','','name:名称\r\naddress:地址\r\nphone:电话\r\nrecruit_place:招生地点\r\nid:操作:[EDIT]|编辑,[DELETE]|删除,show&id=[id]|预览','10','','','1431512463','1432019236','1','MyISAM');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('contribution','驾校简介','text','editor','','','1','','0','0','1','1432021512','1431512717','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('photo','照片','int(10) UNSIGNED','picture','','','1','','0','0','1','1431656195','1431512527','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('condition','基本条件','varchar(100)','checkbox','','','1','1:可夜间练车\r\n2:可接送\r\n3:可刷卡\r\n4:是否速成\r\n5:可考自动档\r\n6:可分期','0','0','1','1431656205','1431512631','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('phone','电话','int(20)','num','','','1','','0','1','1','1431659631','1431512560','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('name','名称','varchar(20) NOT NULL','string','','','1','','0','1','1','1431512495','1431512495','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('public_id','公众号ID','varchar(100) NOT NULL','string','','','0','','0','0','1','1431681448','1431575143','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('qq','QQ','varchar(255)','string','','','1','','0','0','1','1431656189','1431575167','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('address','地址','varchar(255)','string','','','1','','0','0','1','1431656181','1431593833','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('recruit_place','招生地点','varchar(255) NOT NULL','string','','','1','','0','0','1','1431575108','1431575108','','3','','regex','','3','function');
UPDATE `wp_attribute` SET model_id= (SELECT MAX(id) FROM `wp_model`) WHERE model_id=0;


CREATE TABLE IF NOT EXISTS `wp_school_place` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`phone`  int(20) NOT NULL  COMMENT '电话',
`photo`  int(10) UNSIGNED  COMMENT '照片',
`name`  varchar(255) NOT NULL  COMMENT '名称',
`coordinate`  varchar(255) NOT NULL  COMMENT '坐标',
`address`  varchar(255) NOT NULL  COMMENT '地址',
`public_id`  varchar(100) NOT NULL  COMMENT '公众号ID',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_school_place` (`id`,`address`,`coordinate`,`phone`,`name`,`photo`,`public_id`) VALUES ('2','aaaa','116.449687,39.940608','111','1435','372','sdfas');
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`) VALUES ('school_place','驾校场地','0','','1','{"1":["photo","name","phone","address","coordinate"]}','1:基础','','','','','name:名称\r\naddress:地址\r\nphone:电话\r\nid:操作:[EDIT]|编辑,[DELETE]|删除','10','','','1431509626','1431594920','1','MyISAM');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('phone','电话','int(20) NOT NULL','num','','','1','','0','1','1','1431513976','1431513976','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('photo','照片','int(10) UNSIGNED','picture','','','1','','0','0','1','1431594035','1431594035','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('name','名称','varchar(255) NOT NULL','string','','','1','','0','1','1','1431514023','1431514009','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('coordinate','坐标','varchar(255) NOT NULL','string','',' <a href="http://api.map.baidu.com/lbsapi/getpoint/">获取经纬度</a>','1','','0','0','1','1432004936','1431509732','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('address','地址','varchar(255) NOT NULL','string','','','1','','0','0','1','1431509700','1431509700','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('public_id','公众号ID','varchar(100) NOT NULL','string','','','0','','0','0','1','1431681484','1431681484','','3','','regex','','3','function');
UPDATE `wp_attribute` SET model_id= (SELECT MAX(id) FROM `wp_model`) WHERE model_id=0;


CREATE TABLE IF NOT EXISTS `wp_shop_product` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`img_1`  int(10) unsigned NOT NULL   COMMENT '商品图片1',
`img_2`  int(10) unsigned NOT NULL   COMMENT '商品图片2',
`title`  varchar(255) NOT NULL  COMMENT '商品名称',
`intro`  text NOT NULL  COMMENT '商品简介',
`mTime`  int(10) NOT NULL  COMMENT '修改时间',
`cover`  int(10) unsigned NOT NULL   COMMENT '商品封面图片',
`content`  text NOT NULL  COMMENT '商品详情',
`cTime`  int(10) unsigned NOT NULL   COMMENT '发布时间',
`token`  varchar(255) NOT NULL  COMMENT 'Token',
`param`  text NOT NULL  COMMENT '商品参数',
`ad_url`  varchar(255) NOT NULL  COMMENT '商品广告页面',
`buy_url`  varchar(255) NOT NULL  COMMENT '购买地址',
`cate_id_1`  int(10) unsigned NOT NULL   DEFAULT 0 COMMENT '商品一级分类',
`cate_id_2`  int(10) unsigned NOT NULL   DEFAULT 0 COMMENT '商品二级分类',
`market_price`  int(10) unsigned NOT NULL   DEFAULT 0 COMMENT '市场价',
`discount_price`  int(10) unsigned NOT NULL   DEFAULT 0 COMMENT '打折价',
`view_count`  int(10) unsigned NOT NULL   DEFAULT 0 COMMENT '浏览数',
`img_3`  int(10) unsigned NOT NULL   COMMENT '商品图片3',
`img_4`  int(10) unsigned NOT NULL   COMMENT '商品图片4',
`img_5`  int(10) unsigned NOT NULL   COMMENT '商品图片5',
`cate_id`  char(50) NOT NULL  COMMENT '商品分类',
`bug_count`  int(10) unsigned NOT NULL   DEFAULT 0 COMMENT '成交量',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`) VALUES ('shop_product','微商店商品','0','','1','{"1":["title","cover","intro","cate_id","market_price","discount_price","buy_url","ad_url","content","param","img_1","img_2","img_3","img_4","img_5"]}','1:基础','','','','','id:商品ID\r\ncover|get_img_html:图片\r\ntitle:商品名称\r\ncate_id|getCommonCategoryTitle:分类\r\nmarket_price:市场价\r\ndiscount_price:市场价\r\nview_count:浏览数\r\nids:操作:[EDIT]|编辑,[DELETE]|删除','10','title','','1396061373','1398741409','1','MyISAM');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('img_1','商品图片1','int(10) unsigned NOT NULL ','picture','','','1','','0','0','1','1398598870','1398598870','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('img_2','商品图片2','int(10) unsigned NOT NULL ','picture','','','1','','0','0','1','1398598904','1398598904','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('title','商品名称','varchar(255) NOT NULL','string','','','1','','0','1','1','1397520732','1396061859','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('intro','商品简介','text NOT NULL','textarea','','','1','','0','0','1','1397521079','1396061947','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('mTime','修改时间','int(10) NOT NULL','datetime','','','0','','0','0','1','1396624664','1396624664','','3','','regex','time','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('cover','商品封面图片','int(10) unsigned NOT NULL ','picture','','','1','','0','0','1','1397521134','1396062093','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('content','商品详情','text NOT NULL','editor','','','1','','0','0','1','1397521004','1396062146','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('cTime','发布时间','int(10) unsigned NOT NULL ','datetime','','','0','','0','0','1','1396624612','1396075102','','3','','regex','time','1','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('token','Token','varchar(255) NOT NULL','string','','','0','','0','0','1','1396602871','1396602859','','3','','regex','get_token','1','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('param','商品参数','text NOT NULL','editor','','','1','','0','0','1','1397521446','1397521446','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('ad_url','商品广告页面','varchar(255) NOT NULL','string','','可为空，填写商品广告页面的地址','1','','0','0','1','1397521579','1397521579','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('buy_url','购买地址','varchar(255) NOT NULL','string','','用户点击购买按钮时跳转的地址，可以是淘宝等网店的购买地址','1','','0','0','1','1397524287','1397524287','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('cate_id_1','商品一级分类','int(10) unsigned NOT NULL ','num','0','','0','','0','0','1','1397524477','1397524433','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('cate_id_2','商品二级分类','int(10) unsigned NOT NULL ','num','0','','0','','0','0','1','1397524466','1397524466','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('market_price','市场价','int(10) unsigned NOT NULL ','num','0','','1','','0','0','1','1397525480','1397525480','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('discount_price','打折价','int(10) unsigned NOT NULL ','num','0','为空时只显示市场价','1','','0','0','1','1397525579','1397525579','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('view_count','浏览数','int(10) unsigned NOT NULL ','num','0','','0','','0','0','1','1397525660','1397525660','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('img_3','商品图片3','int(10) unsigned NOT NULL ','picture','','','1','','0','0','1','1398598938','1398598938','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('img_4','商品图片4','int(10) unsigned NOT NULL ','picture','','','1','','0','0','1','1398598960','1398598960','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('img_5','商品图片5','int(10) unsigned NOT NULL ','picture','','','1','','0','0','1','1398598981','1398598981','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('cate_id','商品分类','char(50) NOT NULL','cascade','','','1','type=db&table=common_category&module=shop_category','0','0','1','1398693036','1398599395','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('bug_count','成交量','int(10) unsigned NOT NULL ','num','0','','0','','0','0','1','1399002900','1399002900','','3','','regex','','3','function');
UPDATE `wp_attribute` SET model_id= (SELECT MAX(id) FROM `wp_model`) WHERE model_id=0;


CREATE TABLE IF NOT EXISTS `wp_student` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`number`  int(20) NOT NULL  COMMENT '电话',
`name`  varchar(255) NOT NULL  COMMENT '姓名',
`card_id`  varchar(255) NOT NULL  COMMENT '身份证号',
`sign_type`  char(10) NOT NULL  DEFAULT 0 COMMENT '报考车型',
`fee_sum`  int(10)  COMMENT '学费总额',
`fee_payed`  int(10)  COMMENT '已付金额',
`fee_unpay`  int(10)  COMMENT '未付金额',
`fee_type`  char(10)  COMMENT '付费方式',
`time_sign`  int(10)   COMMENT '报名时间',
`time_begin`  int(10)  COMMENT '开课时间',
`intro_source`  varchar(100)  COMMENT '渠道来源',
`region`  char(10)  COMMENT '地域',
`sign_place`  varchar(255)  COMMENT '报名点',
`integration`  int(10)  COMMENT '积分',
`sex`  varchar(5) NOT NULL  DEFAULT 'M' COMMENT '性别',
`photo`  int(10) UNSIGNED  COMMENT '照片',
`intro`  text  COMMENT '简介',
`public_id`  varchar(100) NOT NULL  COMMENT '公众号ID',
`status`  int(3) NOT NULL  DEFAULT 1 COMMENT '状态',
`id_in_teacher`  int(10)  COMMENT '招生教练',
`id_teacher`  int(10)  COMMENT '指导教练',
`open_id`  varchar(100)  COMMENT '微信号',
`attach`  int(10) UNSIGNED NOT NULL  COMMENT '附件',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_student` (`id`,`name`,`number`,`card_id`,`sign_type`,`fee_sum`,`fee_payed`,`fee_unpay`,`fee_type`,`time_sign`,`time_begin`,`intro_source`,`region`,`sign_place`,`integration`,`sex`,`photo`,`intro`,`status`,`id_in_teacher`,`id_teacher`,`public_id`,`open_id`,`attach`) VALUES ('2','吴汉楚','1865920234','3592009672349822231','C1','100','100','0','1','1431499800','1431567900','1','1','厦门思明区','100','M','0','<p>qwerqer</p>','1','1','1','sdfas','','0');
INSERT INTO `wp_student` (`id`,`name`,`number`,`card_id`,`sign_type`,`fee_sum`,`fee_payed`,`fee_unpay`,`fee_type`,`time_sign`,`time_begin`,`intro_source`,`region`,`sign_place`,`integration`,`sex`,`photo`,`intro`,`status`,`id_in_teacher`,`id_teacher`,`public_id`,`open_id`,`attach`) VALUES ('3','123123','2147483647','12312sss11','C1','0','0','0','1','0','0','0','0','','0','M','0','<p><img src="/Uploads/Editor/gh_ebd909d925e6/2015-05-13/555301d234705.JPG" title="IMG_0109.JPG"/></p>','-1','1','1','0','123','0');
INSERT INTO `wp_student` (`id`,`name`,`number`,`card_id`,`sign_type`,`fee_sum`,`fee_payed`,`fee_unpay`,`fee_type`,`time_sign`,`time_begin`,`intro_source`,`region`,`sign_place`,`integration`,`sex`,`photo`,`intro`,`status`,`id_in_teacher`,`id_teacher`,`public_id`,`open_id`,`attach`) VALUES ('4','1111','111','111111111','C1','0','0','0','1','0','0','0','0','','1111','M','0','','1','1','1','',' ','0');
INSERT INTO `wp_student` (`id`,`name`,`number`,`card_id`,`sign_type`,`fee_sum`,`fee_payed`,`fee_unpay`,`fee_type`,`time_sign`,`time_begin`,`intro_source`,`region`,`sign_place`,`integration`,`sex`,`photo`,`intro`,`status`,`id_in_teacher`,`id_teacher`,`public_id`,`open_id`,`attach`) VALUES ('6','1123','12312','111','C1','0','0','0','1','','0','0','0','','0','M','0','','1','1','1','sdfas','aaaaaa2','0');
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`) VALUES ('student','学员','0','','1','{"1":["name","sex","number","region","card_id","sign_type","photo","status","time_sign","intro_source","id_in_teacher","fee_sum","fee_payed","fee_unpay","sign_place","fee_type","time_begin","id_teacher","integration","intro"]}','1:基础','','','','','name:姓名\r\nstatus|get_name_by_status:状态\r\nnumber:电话\r\ncard_id:身份证号\r\nsign_type|get_name_by_status:报考车型\r\nid_teacher:指导教练\r\nfee_sum:学费总额\r\nfee_payed:已付金额\r\nfee_unpay:未付金额\r\nintegration:积分\r\nid:操作:[EDIT]|编辑,[DELETE]|删除','10','name','','1431332341','1431914324','1','MyISAM');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('number','电话','int(20) NOT NULL','num','','','1','','0','1','1','1431479027','1431479011','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('name','姓名','varchar(255) NOT NULL','string','','','1','','0','1','1','1431479034','1431332374','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('card_id','身份证号','varchar(255) NOT NULL','string','','','1','','0','1','1','1431479114','1431479114','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('sign_type','报考车型','char(10) NOT NULL','select','0','','1','C1:C1\r\nC2:C2','0','1','1','1431480898','1431479248','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('fee_sum','学费总额','int(10)','num','','','1','','0','0','1','1431931207','1431479349','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('fee_payed','已付金额','int(10)','num','','','1','','0','0','1','1431483200','1431479397','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('fee_unpay','未付金额','int(10)','num','','','1','','0','0','1','1431483192','1431479426','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('fee_type','付费方式','char(10)','select','','','1','1:现金
2:刷卡
3:转账','0','0','1','1431483184','1431479516','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('time_sign','报名时间','int(10) ','datetime','','','1','','0','0','1','1431931228','1431479551','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('time_begin','开课时间','int(10)','datetime','','','1','','0','0','1','1431481123','1431479590','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('intro_source','渠道来源','varchar(100)','select','','','1','教练介绍\r\n学员介绍\r\n门店介绍\r\n网店\r\n微信','0','0','1','1431931284','1431479736','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('region','地域','char(10)','select','','','1','0:本地\r\n1:外地','0','0','1','1431931260','1431479807','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('sign_place','报名点','varchar(255)','string','','','1','','0','0','1','1431931325','1431479834','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('integration','积分','int(10)','num','','','1','','0','0','1','1431931331','1431479927','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('sex','性别','varchar(5) NOT NULL','select','M','','1','M:男\r\nW:女','0','1','1','1431481907','1431481707','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('photo','照片','int(10) UNSIGNED','picture','','','1','','0','0','1','1431931416','1431481824','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('intro','简介','text','editor','','','1','sdf','0','0','1','1431931446','1431501704','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('public_id','公众号ID','varchar(100) NOT NULL','string','','','0','','0','0','1','1431681431','1431680616','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('status','状态','int(3) NOT NULL','select','1','','1','-1:取消\r\n0:报备\r\n1:科目一\r\n2:科目二\r\n3:科目三\r\n4:科目四\r\n99:毕业\r\n','0','0','1','1431931037','1431514151','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('id_in_teacher','招生教练','int(10)','select','','','1','','0','0','1','1431931435','1431597825','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('id_teacher','指导教练','int(10)','select','','','1','','0','0','1','1431931431','1431597858','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('open_id','微信号','varchar(100)','string','','','1','','0','0','1','1431934636','1431681707','checkWeixin','3','当前微信号，不是粉丝。','function','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('attach','附件','int(10) UNSIGNED NOT NULL','file','','','1','111','0','0','1','1432015816','1432015816','','3','','regex','','3','function');
UPDATE `wp_attribute` SET model_id= (SELECT MAX(id) FROM `wp_model`) WHERE model_id=0;



CREATE TABLE IF NOT EXISTS `wp_student_apprise` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`level`  char(10) NOT NULL  COMMENT '级别',
`id_teacher`  int(10) NOT NULL  COMMENT '教练ID',
`id_student`  int(10) NOT NULL  COMMENT '学员ID',
`apprise`  varchar(255) NOT NULL  COMMENT '评价',
`public_id`  varchar(100) NOT NULL  COMMENT '公众号ID',
`time`  int(10) NOT NULL  COMMENT '时间',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_student_apprise` (`id`,`id_teacher`,`id_student`,`level`,`apprise`,`public_id`,`time`) VALUES ('1','1','2','4','很糟糕111111111111111111111111111111111111111111111ssssssssssssssssssssssssssssssssssssssssssssssss11111111111111111111','sdfas','0');
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`) VALUES ('student_apprise','学员评价','0','','1','{"1":["level","id_teacher","id_student","apprise","time"]}','1:基础','','','','','id_teacher:教练\r\nid_student:学员\r\nlevel:评级\r\ntime|time_format:时间\r\napprise:评价','10','id_teacher','','1431515030','1432015546','1','MyISAM');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('level','级别','char(10) NOT NULL','select','','','1','1:1星\r\n2:2星\r\n3:3星\r\n4:4星\r\n5:5星','0','1','1','1431741623','1431515216','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('id_teacher','教练ID','int(10) NOT NULL','num','','','1','','0','1','1','1431515117','1431515117','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('id_student','学员ID','int(10) NOT NULL','num','','','1','','0','1','1','1431515159','1431515159','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('apprise','评价','varchar(255) NOT NULL','string','','','1','','0','0','1','1431515264','1431515264','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('public_id','公众号ID','varchar(100) NOT NULL','string','','','0','','0','0','1','1431741873','1431681570','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('time','时间','int(10) NOT NULL','datetime','','','1','','0','0','1','1432015510','1432015510','','3','','regex','','3','function');
UPDATE `wp_attribute` SET model_id= (SELECT MAX(id) FROM `wp_model`) WHERE model_id=0;


CREATE TABLE IF NOT EXISTS `wp_student_event` (
`id_student`  int(10) NOT NULL  COMMENT '学员ID',
`type`  tinyint(2) NOT NULL  COMMENT '发生事件',
`time`  int(10) NOT NULL  COMMENT '发生时间',
`id_teacher`  int(10) NOT NULL  COMMENT '教练ID',
`remark`  varchar(255) NOT NULL  COMMENT '备注',
`public_id`  varchar(100) NOT NULL  COMMENT '公众号ID',

) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`) VALUES ('student_event','学员记录','0','','0','','1:基础','','','','','','10','','','1431597081','1431597081','1','MyISAM');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('id_student','学员ID','int(10) NOT NULL','num','','','1','','0','1','1','1431597294','1431597106','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('type','发生事件','tinyint(2) NOT NULL','bool','','','1','1:招生\r\n2:科目一\r\n3:科目二\r\n4:科目三\r\n5:科目四\r\n6:毕业\r\n99:其他\r\n','0','1','1','1431597373','1431597220','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('time','发生时间','int(10) NOT NULL','datetime','','','1','','0','1','1','1431597255','1431597255','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('id_teacher','教练ID','int(10) NOT NULL','num','','','1','','0','0','1','1431597284','1431597284','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('remark','备注','varchar(255) NOT NULL','string','','','1','','0','0','1','1431597410','1431597410','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('public_id','公众号ID','varchar(100) NOT NULL','string','','','0','','0','0','1','1431681599','1431681599','','3','','regex','','3','function');
UPDATE `wp_attribute` SET model_id= (SELECT MAX(id) FROM `wp_model`) WHERE model_id=0;



CREATE TABLE IF NOT EXISTS `wp_student_question` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`weixin_question`  varchar(255) NOT NULL  COMMENT '提问人微信号',
`answer`  varchar(255) NOT NULL  COMMENT '回复',
`question`  varchar(255) NOT NULL  COMMENT '问题',
`person_question`  varchar(255) NOT NULL  COMMENT '提问人',
`public_id`  varchar(100) NOT NULL  COMMENT '公众号ID',
`time`  int(10)  COMMENT '时间',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_student_question` (`id`,`person_question`,`weixin_question`,`answer`,`question`,`public_id`,`time`) VALUES ('1','111','weixin','111','111','','');
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`) VALUES ('student_question','学员提问','0','','1','{"1":["weixin_question","answer","question","person_question","time"]}','1:基础','','','','','person_question:提问人\r\ntime|time_format:时间\r\nquestion:问题\r\nanswer: 答复\r\nid:操作:[EDIT]|回复','10','person_question','','1431514258','1432015596','1','MyISAM');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('weixin_question','提问人微信号','varchar(255) NOT NULL','string','','','1','','0','1','1','1431514347','1431514347','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('answer','回复','varchar(255) NOT NULL','string','','','1','','0','0','1','1431514412','1431514412','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('question','问题','varchar(255) NOT NULL','string','','','1','','0','0','1','1431514427','1431514427','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('person_question','提问人','varchar(255) NOT NULL','string','','','1','','0','1','1','1431514358','1431514312','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('public_id','公众号ID','varchar(100) NOT NULL','','','','0','','0','0','1','1431681628','1431681628','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('time','时间','int(10)','datetime','','','1','','0','0','1','1432015572','1432015572','','3','','regex','','3','function');
UPDATE `wp_attribute` SET model_id= (SELECT MAX(id) FROM `wp_model`) WHERE model_id=0;


CREATE TABLE IF NOT EXISTS `wp_teacher` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`photo`  int(10) UNSIGNED  COMMENT '照片',
`status`  varchar(100) NOT NULL  DEFAULT 1 COMMENT '状态',
`phone`  int(10) NOT NULL  COMMENT '电话',
`time_in`  int(10) NOT NULL  COMMENT '入职时间',
`name`  varchar(255) NOT NULL  COMMENT '姓名',
`public_id`  varchar(100) NOT NULL  COMMENT '公众号ID',
`open_id`  varchar(100) NOT NULL  COMMENT '微信ID',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_teacher` (`id`,`name`,`phone`,`time_in`,`status`,`photo`,`public_id`,`open_id`) VALUES ('1','xxxs','2147483647','1431599400','1','366','sdfas','');
INSERT INTO `wp_teacher` (`id`,`name`,`phone`,`time_in`,`status`,`photo`,`public_id`,`open_id`) VALUES ('2','教练2','1123123123','1431759600','1','372','sdfas','');
INSERT INTO `wp_teacher` (`id`,`name`,`phone`,`time_in`,`status`,`photo`,`public_id`,`open_id`) VALUES ('3','111','11','1431852000','1','0','sdfas','');
INSERT INTO `wp_teacher` (`id`,`name`,`phone`,`time_in`,`status`,`photo`,`public_id`,`open_id`) VALUES ('4','12312','123117','-2147483648','1','0','sdfas','');
INSERT INTO `wp_teacher` (`id`,`name`,`phone`,`time_in`,`status`,`photo`,`public_id`,`open_id`) VALUES ('5','1232','123123','-2147483648','1','0','sdfas','');
INSERT INTO `wp_teacher` (`id`,`name`,`phone`,`time_in`,`status`,`photo`,`public_id`,`open_id`) VALUES ('6','1111','2222','1430965500','1','0','sdfas','');
INSERT INTO `wp_teacher` (`id`,`name`,`phone`,`time_in`,`status`,`photo`,`public_id`,`open_id`) VALUES ('7','333','33','1430967000','1','0','sdfas','');
INSERT INTO `wp_teacher` (`id`,`name`,`phone`,`time_in`,`status`,`photo`,`public_id`,`open_id`) VALUES ('8','111','2222','-2147483648','1','0','sdfas','');
INSERT INTO `wp_teacher` (`id`,`name`,`phone`,`time_in`,`status`,`photo`,`public_id`,`open_id`) VALUES ('10','1111','111','1430790300','1','0','sdfas','');
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`) VALUES ('teacher','教练','0','','1','{"1":["photo","name","phone","status","time_in"]}','1:基础','','','','','name:姓名\r\nphone:电话\r\nstatus|get_name_by_status:状态\r\ntime_in|time_format:入职时间\r\nid:15%操作:[EDIT]|编辑,[DELETE]|删除,show&id=[id]|预览','10','','','1431512961','1431573045','1','MyISAM');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('photo','照片','int(10) UNSIGNED','picture','','','1','','0','0','1','1431759938','1431513145','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('status','状态','varchar(100) NOT NULL','select','1','','1','1:在职\r\n2:离职','0','1','1','1431513100','1431513100','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('phone','电话','int(10) NOT NULL','num','','','1','','0','1','1','1431513038','1431513038','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('time_in','入职时间','int(10) NOT NULL','datetime','','','1','','0','1','1','1431513068','1431513068','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('name','姓名','varchar(255) NOT NULL','string','','','1','','0','1','1','1431513022','1431513022','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('public_id','公众号ID','varchar(100) NOT NULL','','','','0','','0','0','1','1431681667','1431681667','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('open_id','微信ID','varchar(100) NOT NULL','string','','','0','','0','0','1','1431681752','1431681752','','3','','regex','','3','function');
UPDATE `wp_attribute` SET model_id= (SELECT MAX(id) FROM `wp_model`) WHERE model_id=0;



create view wp_teacher_rank as
select `wp_student`.`id_in_teacher` AS `id_in_teacher`,count(if((`wp_student`.`status` > 0),1,NULL)) AS `suc_count`,concat(left((ifnull((count(if((`wp_student`.`status` > 0),1,NULL)) / count(`wp_student`.`id`)),0) * 100),5),'%') AS `suc_rate` from `wp_student` group by `wp_student`.`id_in_teacher`;

create view wp_teacher_rank_all as
SELECT
	`T1`.`id` AS `id`,
	`T1`.`name` AS `name`,
	ifnull(T3.suc_count, 0) AS `suc_count`,
	ifnull(T3.suc_rate, '0%') AS `suc_rate`
FROM
	(
		`wp_teacher` `T1`
		LEFT JOIN `wp_teacher_rank` `T3` ON (
			(`T1`.`id` = `T3`.`id_in_teacher`)
		)
	)
ORDER BY
	ifnull(`T3`.`suc_count`, 0) desc;