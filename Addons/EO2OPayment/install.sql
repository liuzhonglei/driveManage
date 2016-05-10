CREATE TABLE IF NOT EXISTS `wp_eo2o_payment` (
  `id`             INT(10) UNSIGNED NOT NULL AUTO_INCREMENT
  COMMENT '主键',
  `transaction_id` VARCHAR(255)     NOT NULL
  COMMENT '微信支付订单号',
  `out_trade_no`   VARCHAR(255)     NOT NULL
  COMMENT '商户订单号',
  `attach`         VARCHAR(255)     NOT NULL
  COMMENT '商家数据包',
  `token`          VARCHAR(255)     NOT NULL
  COMMENT 'TOKEN',
  `time_end`       VARCHAR(255)     NOT NULL
  COMMENT '支付完成时间',
  `total_fee`      VARCHAR(255)     NOT NULL
  COMMENT '总金额',
  `openid`         VARCHAR(255)     NOT NULL
  COMMENT '用户标识',
  `err_code`       VARCHAR(255)     NOT NULL
  COMMENT '错误代码',
  `err_code_des`   VARCHAR(255)     NOT NULL
  COMMENT '错误代码描述',
  `result_code`    VARCHAR(255) COMMENT '业务结果',
  `mch_id`         VARCHAR(255)     NOT NULL
  COMMENT '商户号',
  `appid`          VARCHAR(255)     NOT NULL
  COMMENT '公众账号ID',
  `paytype`        VARCHAR(25)      NOT NULL
  COMMENT '划款类型',
  `time_begin`     INT(10) COMMENT '开始时间',
  `return_code`    VARCHAR(255) COMMENT '返回代码',
  `return_msg`     VARCHAR(255) COMMENT '返回字符串',
  PRIMARY KEY (`id`)
)
  ENGINE = MyISAM
  DEFAULT CHARACTER SET = utf8
  COLLATE = utf8_general_ci
  CHECKSUM = 0
  ROW_FORMAT = DYNAMIC
  DELAY_KEY_WRITE = 0;
INSERT INTO `wp_model` (`name`, `title`, `extend`, `relation`, `need_pk`, `field_sort`, `field_group`, `attribute_list`, `template_list`, `template_add`, `template_edit`, `list_grid`, `list_row`, `search_key`, `search_list`, `create_time`, `update_time`, `status`, `engine_type`)
VALUES ('eo2o_payment', '鑫无线支付插件默认日志', '0', '', '1',
                        '{"1":["transaction_id","out_trade_no","attach","time_end","total_fee","openid","err_code","err_code_des","result_code","mch_id","appid"]}',
                        '1:基础', '', '', '', '',
        'transaction_id:微信支付订单号\r\nout_trade_no:商户订单号\r\nmch_id:商户号\r\ntotal_fee:总金额\r\nresult_code:业务结果\r\ntime_end:支付完成时间\r\nid:操作:[EDIT]|编辑,[DELETE]|删除',
        '10', 'out_trade_no', 'out_trade_no', '1436522571', '1436544317', '1', 'MyISAM');
INSERT INTO `wp_attribute` (`name`, `title`, `field`, `type`, `value`, `remark`, `is_show`, `extra`, `model_id`, `is_must`, `status`, `update_time`, `create_time`, `validate_rule`, `validate_time`, `error_info`, `validate_type`, `auto_rule`, `auto_time`, `auto_type`)
VALUES ('transaction_id', '微信支付订单号', 'varchar(255) NOT NULL', 'string', '', '', '1', '', '0', '0', '1', '1437388349',
        '1436522885', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` (`name`, `title`, `field`, `type`, `value`, `remark`, `is_show`, `extra`, `model_id`, `is_must`, `status`, `update_time`, `create_time`, `validate_rule`, `validate_time`, `error_info`, `validate_type`, `auto_rule`, `auto_time`, `auto_type`)
VALUES ('out_trade_no', '商户订单号', 'varchar(255) NOT NULL', 'string', '', '', '1', '', '0', '0', '1', '1437388340',
        '1436522902', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` (`name`, `title`, `field`, `type`, `value`, `remark`, `is_show`, `extra`, `model_id`, `is_must`, `status`, `update_time`, `create_time`, `validate_rule`, `validate_time`, `error_info`, `validate_type`, `auto_rule`, `auto_time`, `auto_type`)
VALUES
  ('attach', '商家数据包', 'varchar(255) NOT NULL', 'string', '', '', '1', '', '0', '0', '1', '1436522926', '1436522926', '',
   '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` (`name`, `title`, `field`, `type`, `value`, `remark`, `is_show`, `extra`, `model_id`, `is_must`, `status`, `update_time`, `create_time`, `validate_rule`, `validate_time`, `error_info`, `validate_type`, `auto_rule`, `auto_time`, `auto_type`)
VALUES
  ('token', 'TOKEN', 'varchar(255) NOT NULL', 'string', '', '', '0', '', '0', '1', '1', '1436536893', '1436536893', '',
   '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` (`name`, `title`, `field`, `type`, `value`, `remark`, `is_show`, `extra`, `model_id`, `is_must`, `status`, `update_time`, `create_time`, `validate_rule`, `validate_time`, `error_info`, `validate_type`, `auto_rule`, `auto_time`, `auto_type`)
VALUES
  ('time_end', '支付完成时间', 'varchar(255) NOT NULL', 'string', '', '', '1', '', '0', '0', '1', '1437388364', '1436522942',
   '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` (`name`, `title`, `field`, `type`, `value`, `remark`, `is_show`, `extra`, `model_id`, `is_must`, `status`, `update_time`, `create_time`, `validate_rule`, `validate_time`, `error_info`, `validate_type`, `auto_rule`, `auto_time`, `auto_type`)
VALUES
  ('total_fee', '总金额', 'varchar(255) NOT NULL', 'string', '', '', '1', '', '0', '1', '1', '1436522864', '1436522864',
   '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` (`name`, `title`, `field`, `type`, `value`, `remark`, `is_show`, `extra`, `model_id`, `is_must`, `status`, `update_time`, `create_time`, `validate_rule`, `validate_time`, `error_info`, `validate_type`, `auto_rule`, `auto_time`, `auto_type`)
VALUES
  ('openid', '用户标识', 'varchar(255) NOT NULL', 'string', '', '', '1', '', '0', '1', '1', '1436522833', '1436522833', '',
   '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` (`name`, `title`, `field`, `type`, `value`, `remark`, `is_show`, `extra`, `model_id`, `is_must`, `status`, `update_time`, `create_time`, `validate_rule`, `validate_time`, `error_info`, `validate_type`, `auto_rule`, `auto_time`, `auto_type`)
VALUES
  ('err_code', '错误代码', 'varchar(255) NOT NULL', 'string', '', '', '1', '', '0', '0', '1', '1437388280', '1436522799',
   '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` (`name`, `title`, `field`, `type`, `value`, `remark`, `is_show`, `extra`, `model_id`, `is_must`, `status`, `update_time`, `create_time`, `validate_rule`, `validate_time`, `error_info`, `validate_type`, `auto_rule`, `auto_time`, `auto_type`)
VALUES ('err_code_des', '错误代码描述', 'varchar(255) NOT NULL', 'string', '', '', '1', '', '0', '0', '1', '1437388272',
        '1436522815', '', '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` (`name`, `title`, `field`, `type`, `value`, `remark`, `is_show`, `extra`, `model_id`, `is_must`, `status`, `update_time`, `create_time`, `validate_rule`, `validate_time`, `error_info`, `validate_type`, `auto_rule`, `auto_time`, `auto_type`)
VALUES
  ('result_code', '业务结果', 'varchar(255)', 'string', '', '', '1', '', '0', '0', '1', '1437388301', '1436522778', '', '3',
   '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` (`name`, `title`, `field`, `type`, `value`, `remark`, `is_show`, `extra`, `model_id`, `is_must`, `status`, `update_time`, `create_time`, `validate_rule`, `validate_time`, `error_info`, `validate_type`, `auto_rule`, `auto_time`, `auto_type`)
VALUES
  ('mch_id', '商户号', 'varchar(255) NOT NULL', 'string', '', '', '1', '', '0', '0', '1', '1437388329', '1436522751', '',
   '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` (`name`, `title`, `field`, `type`, `value`, `remark`, `is_show`, `extra`, `model_id`, `is_must`, `status`, `update_time`, `create_time`, `validate_rule`, `validate_time`, `error_info`, `validate_type`, `auto_rule`, `auto_time`, `auto_type`)
VALUES
  ('appid', '公众账号ID', 'varchar(255) NOT NULL', 'string', '', '', '1', '', '0', '0', '1', '1437388318', '1436522724', '',
   '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` (`name`, `title`, `field`, `type`, `value`, `remark`, `is_show`, `extra`, `model_id`, `is_must`, `status`, `update_time`, `create_time`, `validate_rule`, `validate_time`, `error_info`, `validate_type`, `auto_rule`, `auto_time`, `auto_type`)
VALUES
  ('paytype', '划款类型', 'varchar(25) NOT NULL', 'string', '', '', '1', '', '0', '0', '1', '1437386084', '1437386084', '',
   '3', '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` (`name`, `title`, `field`, `type`, `value`, `remark`, `is_show`, `extra`, `model_id`, `is_must`, `status`, `update_time`, `create_time`, `validate_rule`, `validate_time`, `error_info`, `validate_type`, `auto_rule`, `auto_time`, `auto_type`)
VALUES
  ('time_begin', '开始时间', 'int(10)', 'datetime', '', '', '1', '', '0', '0', '1', '1437386921', '1437386921', '', '3', '',
   'regex', '', '3', 'function');
INSERT INTO `wp_attribute` (`name`, `title`, `field`, `type`, `value`, `remark`, `is_show`, `extra`, `model_id`, `is_must`, `status`, `update_time`, `create_time`, `validate_rule`, `validate_time`, `error_info`, `validate_type`, `auto_rule`, `auto_time`, `auto_type`)
VALUES
  ('return_code', '返回代码', 'varchar(255)', 'string', '', '', '1', '', '0', '0', '1', '1437387917', '1437387917', '', '3',
   '', 'regex', '', '3', 'function');
INSERT INTO `wp_attribute` (`name`, `title`, `field`, `type`, `value`, `remark`, `is_show`, `extra`, `model_id`, `is_must`, `status`, `update_time`, `create_time`, `validate_rule`, `validate_time`, `error_info`, `validate_type`, `auto_rule`, `auto_time`, `auto_type`)
VALUES
  ('return_msg', '返回字符串', 'varchar(255)', 'string', '', '', '1', '', '0', '0', '1', '1437387953', '1437387953', '', '3',
   '', 'regex', '', '3', 'function');
UPDATE `wp_attribute`
SET model_id = (SELECT MAX(id)
                FROM `wp_model`)
WHERE model_id = 0;


#wp_eo2o_payment_all
DROP VIEW wp_eo2o_payment_all;

CREATE VIEW wp_eo2o_payment_all AS
  SELECT DISTINCT
    t.id,
    t.transaction_id,
    t.out_trade_no,
    t.attach,
    t.pay_channel,
    t.in_or_out,
    t.student_id,
    t.token,
    t.time_end,
    t.total_fee,
    t.openid,
    t.err_code,
    t.err_code_des,
    t.result_code,
    t.mch_id,
    t.appid,
    t.time_begin,
    t.return_code,
    t.return_msg,
    IF(
        LENGTH(trim(t.paytype)) > 1,
        t.paytype,
        t1.type
    )           AS              paytype,
    t.openid                    student_name,
    t.openid                    nickname,
    if(
        CHAR_LENGTH(t.time_end) > 0,
        FROM_UNIXTIME(t.time_end, "%Y-%m-%d %H:%i:%S")
        , NULL
    )                           pay_time,
    ROUND(t.total_fee / 100, 2) pay_fee,
    (
      CASE t.pay_channel
      WHEN "human"
        THEN "现金"
      WHEN "weixin"
        THEN "微信"
      WHEN "alipay"
        THEN "支付宝"
      ELSE t1.NAME END
    )                           pay_channel_name,
    (
      CASE t1.type
      WHEN "banner"
        THEN "锦旗支付"
      ELSE t1.NAME END
    )                           pay_item_name,
    t2.NAME                     school_name,
    (
      CASE t.result_code
      WHEN "SUCCESS"
        THEN "支付成功"
      WHEN "FAIL"
        THEN "支付失败"
      WHEN "WAIT"
        THEN "等待划款"
      ELSE "划款中" END
    )                           pay_result,
    t3.username AS              user_name
  FROM wp_eo2o_payment t LEFT JOIN wp_school_payitem t1 ON t.payitem_id = t1.id
    LEFT JOIN wp_school t2 ON t.token = t2.token
    LEFT JOIN wp_ucenter_member t3 ON t.user_id = t3.id
  WHERE LENGTH(trim(transaction_id)) > 0 OR pay_channel IN ("human", "alipay") OR result_code = "WAIT";
#
# CREATE VIEW wp_eo2o_pay_list AS
#   SELECT *
#   FROM wp_eo2o_payment_all;

/**
 * 划款统计
 */
DROP PROCEDURE IF EXISTS pay_fast_statics;
CREATE PROCEDURE test(IN duration VARCHAR(26))
  BEGIN
    SET pResult := NULL;
    SET pResult := 'test';
  END;

# 获取当前日期
# 的日期格式

/**
 * 获取当前日期
 * 的日期格式
 */
DROP FUNCTION getDateFormat;
CREATE FUNCTION getDateFormat(date_type VARCHAR(50))
  RETURNS VARCHAR(50)
  BEGIN
    DECLARE date_type_format VARCHAR(50);
    CASE date_type
      WHEN "year"
      THEN
        SET date_type_format = "%Y";
      WHEN "month"
      THEN
        SET date_type_format = "%Y-%m";
    ELSE
      SET date_type_format = "%Y-%m-%d";
    END CASE;
    RETURN (date_type_format);
  END;


/**
  * 首页:资金图:流水数据
  */
DROP PROCEDURE IF EXISTS statics_date_pay;
DELIMITER //
CREATE PROCEDURE statics_date_pay(
  IN token     VARCHAR(50),
  IN date_type VARCHAR(50))
  BEGIN
    DECLARE
    date_type_format VARCHAR(50);
    -- 设置格式
    SET date_type_format = getDateFormat(date_type);

    SELECT
      FROM_UNIXTIME(t.time_end, date_type_format) time,
      SUM(CASE WHEN t.in_or_out = "IN"
        THEN total_fee
          ELSE 0 END) / 100 AS                    `in_fee`,
      SUM(CASE WHEN t.in_or_out = "OUT"
        THEN total_fee
          ELSE 0 END) / 100 AS                    `out_fee`
    FROM
      wp_eo2o_payment t
    WHERE
      t.token = token AND
      t.time_end IS NOT NULL AND t.time_end != "" AND
      t.result_code = "SUCCESS"
    GROUP BY
      FROM_UNIXTIME(t.time_end, date_type_format)
    ORDER BY FROM_UNIXTIME(t.time_end, date_type_format) DESC
    LIMIT 0, 5;
  END;
//
CALL statics_date_pay('gh_36a5c6958de0', 'year');

/**
  * 首页:资金图:支付类型数据
  */
DROP PROCEDURE IF EXISTS statics_type_pay;
DELIMITER //
CREATE PROCEDURE statics_type_pay(
  IN token     VARCHAR(50),
  IN date_type VARCHAR(50))
  BEGIN
    DECLARE
    date_type_format VARCHAR(50);
    -- 设置格式
    SET date_type_format = getDateFormat(date_type);
    SELECT
      paytype,
      sum(total_fee) / 100 AS total_fee
    FROM
      wp_eo2o_payment t
    WHERE
      t.token = token
      AND t.result_code = "SUCCESS"
      AND FROM_UNIXTIME(t.time_end, date_type_format) = date_format(now(), date_type_format)
    GROUP BY t.paytype;
  END;
//
# CALL statics_type_pay('gh_36a5c6958de0', 'year');

/**
 *获取资金流水,分支出和流入,根据时间和机构
 */

DROP PROCEDURE IF EXISTS statics_complex_type_pay;
DELIMITER //
CREATE PROCEDURE statics_complex_type_pay(
  IN token      VARCHAR(50),
  IN date_type  VARCHAR(50),
  IN limitNum   VARCHAR(50),
  IN begin_date VARCHAR(50),
  IN end_date   VARCHAR(50),
  IN pay_place  INT,
  IN paytype    VARCHAR(50)
)
  BEGIN
    DECLARE
    date_type_format VARCHAR(50);
    DECLARE
    externalMap VARCHAR(500);

    -- 设置格式
    SET date_type_format = getDateFormat(date_type);
    SET externalMap = "";
    IF !isnull(begin_date)
    THEN
      SET externalMap = concat(externalMap, " and  time_end >=", UNIX_TIMESTAMP(begin_date));
    END IF;
    IF !isnull(end_date)
    THEN
      SET externalMap = concat(externalMap, " and time_end <=", UNIX_TIMESTAMP(end_date));
    END IF;
    IF !isnull(pay_place)
    THEN
      SET externalMap = concat(externalMap, " and school_place_id = ", pay_place);
    END IF;
    IF !isnull(paytype)
    THEN
      SET externalMap = concat(externalMap, " and paytype in ", paytype);
    END IF;


    SET @sql = '';
    SET @sql = concat(' SELECT',
                      ' FROM_UNIXTIME(t.time_end, \'', date_type_format, '\') time,',
                      ' SUM(CASE WHEN t.paytype = "sign" THEN pay_fee  ELSE 0 END)  AS                    "sign_fee",',
                      ' SUM(CASE WHEN t.paytype = "banner"  THEN pay_fee ELSE 0 END)  AS                    banner_fee,',
                      ' SUM(CASE WHEN t.in_or_out = "supplementary"  THEN pay_fee    ELSE 0 END)  AS        supplementary_fee,',
                      ' SUM(CASE WHEN t.paytype = "activity" THEN pay_fee    ELSE 0 END)  AS                    activity_fee,',
                      ' SUM(CASE WHEN t.paytype = "wage" THEN pay_fee    ELSE 0 END)  AS                    wage_fee,',
                      ' SUM(CASE WHEN t.paytype = "reward" THEN pay_fee ELSE 0 END)  AS                    reward_fee,',
                      ' SUM(CASE WHEN t.paytype = "car"   THEN pay_fee   ELSE 0 END)  AS                    car_fee',
                      ' FROM  wp_eo2o_payment_all t',
                      ' WHERE t.token = token AND  FROM_UNIXTIME(t.time_end, \'', date_type_format,
                      '\') IS NOT NULL AND  t.result_code = "SUCCESS"', externalMap,
                      ' GROUP BY FROM_UNIXTIME(t.time_end,  \'', date_type_format, ' \') ',
                      ' ORDER BY FROM_UNIXTIME(t.time_end, \'', date_type_format, '\')',
                      ' LIMIT 0, ', limitNum, ';');

    PREPARE stmt FROM @sql;
    -- 预处理动态sql语句
    EXECUTE stmt;
    -- 执行sql语句
    DEALLOCATE PREPARE stmt;
    -- 释放prepare
  END;
//
# call statics_complex_type_pay('gh_36a5c6958de0','month','10',null,null,null,"('activity','wage','reward')");
