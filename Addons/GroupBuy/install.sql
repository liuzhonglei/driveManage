drop view wp_groupbuy_info_all;
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