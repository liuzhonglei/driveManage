DROP INDEX wp_cut_price_index ON wp_cut_price;

CREATE INDEX wp_cut_price_index ON wp_cut_price (token, openid, friend_openid);

DROP VIEW wp_cut_price_all;

CREATE VIEW wp_cut_price_all AS SELECT
                                  *, (
                                       SELECT
                                     nickname
                                       FROM
                                         wp_follow
                                       WHERE
                                         t.openid = openid
                                         AND t.token = token
                                       LIMIT 1
  ) AS nickname,
                                     (
                                       SELECT
                                     name
                                       FROM
                                         wp_student
                                       WHERE
                                         t.openid = openid
                                         AND t.token = token
                                       LIMIT 1
                                     ) AS name,
                                     (
                                       SELECT
                                     nickname
                                       FROM
                                         wp_follow
                                       WHERE
                                         openid = t.friend_openid
                                         AND t.token = token
                                       LIMIT 1
                                     ) AS friend_nickname,
                                     (
                                       SELECT
                                     name
                                       FROM
                                         wp_student
                                       WHERE
                                         t.friend_openid = openid
                                         AND t.token = token
                                       LIMIT 1
                                     ) AS friend_name
                                FROM
                                  wp_cut_price t;

DROP VIEW wp_cut_price_count_detail;

CREATE VIEW wp_cut_price_count_detail AS SELECT
                                           t.openid id,
                                           t.token,
                                           t.openid,
                                           count(*) count,
                                           sum(t.fee) fee
                                         FROM
                                           wp_cut_price t
                                         GROUP BY
                                           t.token,
                                           t.openid;

SELECT
  *
FROM
  wp_cut_price_count_detail;

DROP VIEW wp_cut_price_count;

CREATE VIEW wp_cut_price_count AS SELECT
                                    t.id,
                                    t.token,
                                    t.openid,
                                    t.count,
                                    t.fee,
                                    t1.name,
                                    t1.phone,
                                    t1.remark,
                                    t1.time_sign,
                                    (
                                      SELECT
                                    nickname
                                      FROM
                                        wp_follow
                                      WHERE
                                        t.openid = openid
                                        AND t.token = token
                                      LIMIT 1
                                    ) AS nickname
                                  FROM
                                    wp_cut_price_count_detail t
                                    LEFT JOIN wp_student t1 ON t.token = t1.token and t.openid = t1.openid;



DELETE
FROM
  wp_model
WHERE
  NAME = "cut_price_count";

INSERT INTO `wp_model` (
  `name`,
  `title`,
  `extend`,
  `relation`,
  `need_pk`,
  `field_sort`,
  `field_group`,
  `attribute_list`,
  `template_list`,
  `template_add`,
  `template_edit`,
  `list_grid`,
  `list_row`,
  `search_key`,
  `search_list`,
  `create_time`,
  `update_time`,
  `status`,
  `engine_type`
)
VALUES
  (
    'cut_price_count',
    '砍价统计',
    '0',
    '',
    '1',
    '{"1":["student_openid","friend_openid","fee","time"]}',
    '1:基础',
    '',
    '',
    '',
    '',
    'name:学员名称\r\nphone:电话\r\nnickname:微信昵称\r\nfee:砍价总金额(元)\r\nremark:备注\r\ntime_sign|day_format:注册日期',
    '10',
    'name,nickname',
    '',
    '1442649630',
    '1442653767',
    '1',
    'MyISAM'
  );

