create view wp_student_all as
SELECT
	t.*,
	t1.name teacher_k2_name,
	t2.name in_teacher_name,
	t3.name course_name,
	t4.nickname weixin_name,
	t5.name status_name,
  t6.name teacher_k3_name

FROM
	wp_student t
LEFT JOIN wp_teacher t1 ON t.id_teacher_k2 = t1.id
LEFT JOIN wp_teacher t2 ON t.id_in_teacher = t2.id
LEFT JOIN wp_school_course t3 ON t.course_id = t3.id
left join wp_follow t4 on t.openid = t4.openid  and t.token = t4.token
left join wp_school_dict t5 on t5.dic_type='student_status' and t.status = t5.value
LEFT JOIN wp_teacher t6 ON t.id_teacher_k3 = t6.id;


create view wp_student_apprise_all as
select t1.*,t2.name student ,t3.name teacher from wp_student_apprise t1 left join wp_student t2 on t1.id_student = t2.id left join wp_teacher t3 on t1.id_teacher = t3.id;

create view wp_teacher_rank as
select wp_student.id_in_teacher AS id_in_teacher,count(if((wp_student.status > 0),1,NULL)) AS suc_count,concat(left((ifnull((count(if((wp_student.status > 0),1,NULL)) / count(wp_student.id)),0) * 100),5),'%') AS suc_rate from wp_student group by wp_student.id_in_teacher;

create view wp_teacher_rank_all as
SELECT
	T1.id AS id,
	T1.name AS name,
	ifnull(T3.suc_count, 0) AS suc_count,
	ifnull(T3.suc_rate, '0%') AS suc_rate
FROM
	(
		wp_teacher T1
		LEFT JOIN wp_teacher_rank T3 ON (
			(T1.id = T3.id_in_teacher)
		)
	)
ORDER BY
	ifnull(T3.suc_count, 0) desc;

create view wp_student_question_all as
SELECT
	t.id AS id,
	t.openid AS openid,
	t.answer AS answer,
	t.question AS question,
	t.token AS token,
	t.time AS time,
	t1.nickname AS weixin_name
FROM
	(
		wp_student_question t
		LEFT JOIN wp_follow t1 ON (
			(
				(t.openid = t1.openid)
				AND (t.token = t1.token)
			)
		)
	);

create view wp_teacher_all as
SELECT
	t.*,t1.nickname  weixin_name
FROM
	wp_teacher t
LEFT JOIN wp_follow t1 ON t.openid = t1.openid  and  t.token = t1.token
AND t.token = t1.token;

drop view wp_student_banner_all;
create view wp_student_banner_all as
SELECT
	t.*, t1. NAME teacher_name,
	t2. NAME student_name
FROM
	wp_student_banner t
LEFT JOIN wp_teacher t1 ON t.teacher_id = t1.id LEFT join wp_student t2 ON t.student_id = t2.id;

drop view wp_qingqing_coupon_all;
create view wp_qingqing_coupon_all as
select t.*,t1.nickname from wp_qingqing_coupon t left join wp_follow t1 on t1.token = t.token and t1.openid = t.openid;

DROP  INDEX wp_student_index_search ON wp_student ;
DROP  INDEX wp_student_index_token ON wp_student ;
CREATE INDEX wp_student_index_token ON wp_student (token);
CREATE INDEX wp_student_index_search ON wp_student  (token,name,phone,id_in_teacher,status,openid);

DROP  INDEX wp_follow_index_search ON wp_follow ;
CREATE INDEX wp_follow_index_search on wp_follow (token);
DROP  INDEX wp_follow_index_openid ON wp_follow ;
CREATE INDEX wp_follow_index_openid on wp_follow (openid);


DROP VIEW wp_eo2o_payment_all;
CREATE VIEW wp_eo2o_payment_all AS
SELECT
	t.*,
	t3.name student_name,
	t4.nickname,
	FROM_UNIXTIME(t.time_end, "%Y-%m-%d %H:%i:%S") pay_time,
	ROUND(t.total_fee / 100, 2) pay_fee,
	(
		CASE t.paytype
		WHEN "banner" THEN
			"锦旗支付"
		ELSE
			t1. NAME
		END
	) pay_item_name,
	t2. NAME school_name,
	"微信支付" pay_channel,
	(
		CASE t.result_code
		WHEN "SUCCESS"
		THEN
			"支付成功"
		WHEN "FAIL" THEN
			"支付失败"
		ELSE
			"划款中"
		END
	) pay_result
FROM
	wp_eo2o_payment t
LEFT JOIN wp_school_payitem t1 ON t.payitem_id = t1.id
LEFT JOIN wp_school t2 ON t.token = t2.token
left join wp_student t3 on  t.token = t3.token and t.openid = t3.openid
left join wp_follow t4 on t.token = t4.token and t.openid = t4.openid
WHERE   LENGTH(trim(transaction_id))>0 ;