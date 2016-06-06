<?php
return array(
    'begin_date' => array(
        'title' => '活动开始日期:',
        'type' => 'text',
        'tip' => '格式如 1989-07-08'
    ),
    'end_date' => array(
        'title' => '活动结束日期:',
        'type' => 'text',
        'tip' => '格式如 1989-07-08'
    ),
    'c1_price' => array(
        'title' => '手动挡价格:',
        'type' => 'text',
        'tip' => '替代费用信息中包含C1的价格，不填写按原价格'
    ),
    'c2_price' => array(
        'title' => '自动挡价格:',
        'type' => 'text',
        'tip' => '替代费用信息中包含C2的价格，不填写按原价格'
    ),
    'max_sum_money' => array(
        'title' => '最高砍价总金额:',
        'type' => 'text'
    ),
    'max_number_people' => array(
        'title' => '可砍价人数:',
        'type' => 'text'
    ),
    'money_options' => array(
        'title' => '可砍价金额:',
        'type' => 'checkbox',
        'options' => array(      //select 和radion、checkbox的子选项
            '5' => '5元',         //值=>文字
            '10' => '10元',         //值=>文字
            '20' => '20元',
            '30' => '30元',         //值=>文字
            '40' => '40元',
            '50' => '50元'
        ),
        'value' => '10',          //表单的默认值
    )
);
