<?php
return array(
    'temp_id' => array(
        'title' => '消息模板ID:',
        'type' => 'text'
    ),
    'sync_account' => array(
        'title' => '培训管理系统帐号:',
        'type' => 'text'
    ),
    'sync_password' => array(
        'title' => '培训管理系统密码:',
        'type' => 'text'
    ),
    'sync_q_account' => array(
        'title' => '备用培训管理系统帐号:',
        'type' => 'text'
    ),
    'sync_q_password' => array(
        'title' => '备用培训管理系统密码:',
        'type' => 'text'
    ),
    'sync_task' => array(
        'title' => '同步任务启动:',
        'type' => 'radio',
        'options' => array(
            '1' => '是',
            '0' => '否'
        ),
        'value' => '0',
    ),
    'inPay_bonus' => array(
        'title' => '推荐费支付_红包开关',
        'type' => 'radio',
        'options' => array(
            '1' => '是',
            '0' => '否'
        ),
        'value' => '0',
    ),
    'inPay_bonus_amount' => array(
        'title' => '推荐费支付_红包金额',
        'type' => 'text'
    )
);
					