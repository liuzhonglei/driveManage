<?php
/**
 * Created by PhpStorm.
 * User: wuhanchu
 * Date: 15/5/13
 * Time: 10:51
 */

namespace Addons\Student\Controller;

use Addons\School\Controller\BaseController;


class StudentBaseController  extends BaseController{

    function _initialize() {
        parent::_initialize();

        $controller = strtolower ( _CONTROLLER );

        $res ['title'] = '学员信息';
        $res ['url'] = addons_url ( 'Student://Student/lists' );
        $res ['class'] = ($controller == 'student' || $controller == 'weixin') ? 'current' : '';
        $nav [] = $res;


        $res ['title'] = '学员提问';
        $res ['url'] = addons_url ( 'Student://Question/lists' );
        $res ['class'] = $controller == 'question' ? 'current' : '';
        $nav [] = $res;

        $res ['title'] = '学员点评';
        $res ['url'] = addons_url ( 'Student://Appraise/lists' );
        $res ['class'] = $controller == 'appraise' ? 'current' : '';
        $nav [] = $res;

        $res ['title'] = '考试通知';
        $res ['url'] = addons_url ( 'Student://Notification/lists' );
        $res ['class'] = ($controller == 'notification' || $controller == 'notificationaccount') ? 'current' : '';
        $nav [] = $res;

        $this->assign ( 'nav', $nav );
    }

    // 替换教师id为姓名
    public function replaceTeacerId($list_data, $field){
        // teacher data
        $list = M ( 'teacher' )->field ( 'id,name' )->select ();
        foreach ( $list as $vo ) {
            $tea [$vo ['id']] = $vo ['name'];
        }
        foreach ($list_data ['list_data'] as &$vo ) {
            $vo [$field] = intval ( $vo [$field] );
            $vo [$field] = $tea [$vo [$field]];
        }

        return $list_data;
    }

    public function replaceWeixin($list_data, $field){
        // teacher data
        $list = M ( 'follow' )->field ( 'openid,nickname' )->select ();
        foreach ( $list as $vo ) {
            $tea [$vo ['openid']] = $vo ['nickname'];
        }
        foreach ($list_data ['list_data'] as &$vo ) {
            $vo [$field] = $tea [$vo [$field]];
        }

        return $list_data;
    }

    // 替换教师id为姓名
    public function replaceStudentId($list_data, $field){
        // teacher data
        $list = M ( 'student' )->field ( 'id,name' )->select ();
        foreach ( $list as $vo ) {
            $tea [$vo ['id']] = $vo ['name'];
        }
        foreach ($list_data ['list_data'] as &$vo ) {
            $vo [$field] = intval ( $vo [$field] );
            $vo [$field] = $tea [$vo [$field]];
        }

        return $list_data;
    }
}