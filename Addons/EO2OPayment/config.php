<?php
return array(
	'MCHID' => array (
				'title' => '商户编号:',
				'type' => 'text',
				'value' => '',
				'tip' => '' 
		),
	'KEY' => array (
				'title' => '商户Key:',
				'type' => 'text',
				'value' => '',
				'tip' => '' 
		),
	'NOTIFY_URL' => array (
				'title' => '回调URL:',
				'type' => 'text',
				'value' => 'EO2OPayment://EO2OPayment/CallBackLog',
				'tip' => '回调通知地址格式： 插件名://控制器/方法名 , 可以通过URL传人： NOTIFY_URL=urlencode(...)' 
		),
	'SSLCERT_PATH' => array (
				'title' => '证书CERT:',
				'type' => 'text',
				'value' => '',
				'tip' => '绝对路径' 
		),
	'SSLKEY_PATH' => array (
				'title' => '证书KEY:',
				'type' => 'text',
				'value' => '',
				'tip' => '绝对路径' 
		)
);
					