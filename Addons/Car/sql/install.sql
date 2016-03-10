/**
  * 首页:操作通知
  */
DROP PROCEDURE IF EXISTS action_required;
DELIMITER //
CREATE PROCEDURE action_required(
  IN token VARCHAR(50))
  BEGIN
    DECLARE
    check_date INT(50);
    SET check_date = UNIX_TIMESTAMP(date_sub(now(), INTERVAL 11 MONTH));

    SELECT
      "/car/list.html" as href,
      CONCAT("车号[",t.number,"]需要进行年检,上一次教练检日期[",  DATE_FORMAT(FROM_UNIXTIME(t.inspection_date), "%Y-%m-%d") ,"],营运检日期[", DATE_FORMAT(FROM_UNIXTIME(t.operate_check_date), "%Y-%m-%d"),"]!") as message
    FROM
      wp_car t
    WHERE
      t.token = token
      AND (t.inspection_date < check_date OR t.operate_check_date < check_date);
  END;
//
CALL action_required('gh_36a5c6958de0');