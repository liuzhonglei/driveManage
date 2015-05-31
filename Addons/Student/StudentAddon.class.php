<?php

namespace Addons\Student;
use Common\Controller\Addon;

/**
 * 学院管理插件
 * @author 丑陋虚
 */

    class StudentAddon extends Addon{

        public $info = array(
            'name'=>'Student',
            'title'=>'学员管理',
            'description'=>'学员管理',
            'status'=>1,
            'author'=>'丑陋虚',
            'version'=>'0.1',
            'has_adminlist'=>1,
            'type'=>1         
        );

        public $admin_list = array (
            'model' => 'student', // 要查的表
            'fields' => '*', // 要查的字段
            'map' => '', // 查询条件, 如果需要可以再插件类的构造方法里动态重置这个属性
            'order' => 'id desc', // 排序,
            'listKey' => array ( // 这里定义的是除了id序号外的表格里字段显示的表头名
                    '字段名' => '表头显示名' 
            ) 
    );


        public function install()
        {
            $install_file = array('./Addons/Student/Sql/install_Student.sql', './Addons/Student/Sql/install_apprise.sql', './Addons/Student/Sql/install_question.sql');

            foreach($install_file as $file){
                if (file_exists($file)) {
                    execute_sql_file($file);
                }
            }

            return true;
        }

        public function uninstall()
        {
            $uninstall_file = array('./Addons/Student/Sql/uninstall_Student.sql', './Addons/Student/Sql/uninstall_apprise.sql', './Addons/Student/Sql/uninstall_question.sql');

            foreach($uninstall_file as $file){
                if (file_exists($file)) {
                    execute_sql_file($file);
                }
            }

            return true;
        }

        //实现的weixin钩子方法
        public function weixin($param){

        }

    }