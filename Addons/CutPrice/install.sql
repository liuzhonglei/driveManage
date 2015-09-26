
DROP INDEX wp_cut_price_index  ON wp_cut_price;
CREATE INDEX wp_cut_price_index ON wp_cut_price (token, openid, friend_openid);
select * from wp_cut_price_all;
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
limit 1
	) AS nickname,
	(
		SELECT
			NAME
		FROM
			wp_student
		WHERE
			t.openid = openid
		AND t.token = token
limit 1
	) AS NAME,
	(
		SELECT
			nickname
		FROM
			wp_follow
		WHERE
			 openid = t.friend_openid 
		AND t.token = token
limit 1
	) AS friend_nickname,
	(
		SELECT
			NAME
		FROM
			wp_student
		WHERE
			t.friend_openid = openid
		AND t.token = token
limit 1
	) AS friend_name
FROM
	wp_cut_price t;

DROP VIEW wp_cut_price_count;
CREATE VIEW wp_cut_price_count AS
SELECT
	t.openid id,
	t.token,
	t.openid,
	count(*) count,
	sum(t.fee) fee,
	(
		SELECT
			nickname
		FROM
			wp_follow
		WHERE
			t.openid = openid
		AND t.token = token
limit 1
	) AS nickname,
	(
		SELECT
			NAME
		FROM
			wp_student
		WHERE
			t.openid = openid
		AND t.token = token
limit 1
	) AS NAME,
	(
		SELECT
			phone
		FROM
			wp_student
		WHERE
			t.openid = openid
		AND t.token = token
limit 1
	) AS phone,
	(
		SELECT
			remark
		FROM
			wp_student
		WHERE
			t.openid = openid
		AND t.token = token
limit 1
	) AS remark
FROM
	wp_cut_price t
GROUP BY
	t.token,
	t.openid;

delete from wp_model where name = "cut_price_count";
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`) VALUES ('cut_price_count','砍价统计','0','','1','{"1":["student_openid","friend_openid","fee","time"]}','1:基础','','','','','name:学员名称\r\nphone:电话\r\nnickname:微信昵称\r\n\r\nfee:砍价总金额(元)\r\nremark:备注','10','name,nickname','','1442649630','1442653767','1','MyISAM');
