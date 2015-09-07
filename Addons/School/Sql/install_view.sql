/**
 * view
 */
#wp_student_all
drop view wp_student_all;
create view wp_student_all as
SELECT
	t.*, t1. NAME teacher_k2_name,
	t2. NAME in_teacher_name,
	t3. NAME course_name,
	t4.nickname weixin_name,
	t5. NAME status_name,
	t6. NAME teacher_k3_name,
	(
		CASE t.intro_source
		WHEN '1' THEN
			t2. NAME
		ELSE
			t.in_student_openid
		END
	) in_name,
	t8.total_fee
FROM
	wp_student t
LEFT JOIN wp_teacher t1 ON  t.token = t1.token and t.id_teacher_k2 = t1.id
LEFT JOIN wp_teacher t2 ON  t.token = t2.token and t.id_in_teacher = t2.id
LEFT JOIN wp_school_course t3 ON  t.token = t3.token and  t.course_id = t3.id
LEFT JOIN wp_follow t4 ON t.openid = t4.openid AND t.token = t4.token
LEFT JOIN wp_school_dict t5 ON t5.dic_type = 'student_status' AND t.STATUS = t5.VALUE
LEFT JOIN wp_teacher t6 ON  t.token = t6.token and t.id_teacher_k3 = t6.id
left join wp_eo2o_payment_count t8 on t.token = t8.token and  t.openid = t8.openid;

#wp_student_apprise_all
drop view wp_student_apprise_all;
create view wp_student_apprise_all as
select t1.*,t2.name student ,t3.name teacher from wp_student_apprise t1 left join wp_student t2 on t1.id_student = t2.id left join wp_teacher t3 on t1.id_teacher = t3.id;

#wp_teacher_rank
drop view wp_teacher_rank;
create view wp_teacher_rank as
select wp_student.id_in_teacher AS id_in_teacher,count(if((wp_student.status > 0),1,NULL)) AS suc_count,concat(left((ifnull((count(if((wp_student.status > 0),1,NULL)) / count(wp_student.id)),0) * 100),5),'%') AS suc_rate from wp_student group by wp_student.id_in_teacher;

#wp_teacher_rank
drop view wp_teacher_rank_all;
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

#wp_student_question_all
drop view wp_student_question_all;
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

#wp_teacher_all
drop view wp_teacher_all;
create view wp_teacher_all as
SELECT
	t.*,t1.nickname  weixin_name
FROM
	wp_teacher t
LEFT JOIN wp_follow t1 ON t.openid = t1.openid  and  t.token = t1.token
AND t.token = t1.token;

#wp_student_banner_all
drop view wp_student_banner_all;
create view wp_student_banner_all as
SELECT
	t.*, t1. NAME teacher_name,
	t2. NAME student_name
FROM
	wp_student_banner t
LEFT JOIN wp_teacher t1 ON t.teacher_id = t1.id LEFT join wp_student t2 ON t.student_id = t2.id;

#wp_qingqing_coupon_all
drop view wp_qingqing_coupon_all;
create view wp_qingqing_coupon_all as
select t.*,t1.nickname from wp_qingqing_coupon t left join wp_follow t1 on t1.token = t.token and t1.openid = t.openid;



#wp_eo2o_payment_all
DROP VIEW wp_eo2o_payment_all;
CREATE VIEW wp_eo2o_payment_all AS
SELECT
DISTINCT
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

#wp_eo2o_payment_count
drop view wp_eo2o_payment_count;
create view wp_eo2o_payment_count as
select t.token,t.openid, SUM(t.total_fee)/100 total_fee
from wp_eo2o_payment_all t
where length(t.transaction_id) > 0 and t.return_code = "SUCCESS"
group by t.token,t.openid;

/**
 *index
 */
DROP  INDEX wp_student_index_search ON wp_student ;
DROP  INDEX wp_student_index_token ON wp_student ;
CREATE INDEX wp_student_index_token ON wp_student (token);
CREATE INDEX wp_student_index_search ON wp_student  (token,name,phone,id_in_teacher,status,openid);

DROP  INDEX wp_follow_index_search ON wp_follow ;
CREATE INDEX wp_follow_index_search on wp_follow (token);
DROP  INDEX wp_follow_index_openid ON wp_follow ;
CREATE INDEX wp_follow_index_openid on wp_follow (openid);
