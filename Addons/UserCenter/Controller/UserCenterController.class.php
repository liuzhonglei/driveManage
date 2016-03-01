<?php

namespace Addons\UserCenter\Controller;

use Addons\School\Controller\BaseController;


use Home\Controller\AddonsController;
use User\Api\UserApi;

class UserCenterController extends BaseController
{

    /**
     * init
     */
    function _initialize()
    {
        parent::_initialize();
        $this->model = $this->getModel('follow');
    }


    /**
     * 显示微信用户列表数据
     */
    public function lists()
    {
        $this->assign('add_button', false);
        $this->assign('del_button', false);
        $this->assign('check_all', false);
        //拉取粉丝
        $url = addons_url('UserCenter://UserCenter/Alllistsinfo');
        $this->assign('addlisturl', $url);
        //获取粉丝总数
        $token = get_token();
        $content = getWeixinUserAllInfo($token);
        $this->assign('total', $content['total']);
        $model = $this->getModel('follow');
        parent::common_lists($model);
    }

    //拉取用户列表并获得基本信息
    public function Alllistsinfo($token = null, $ajaxReturn = true)
    {
        $token || $token = get_token();
        $content = getWeixinUserAllInfo($token);
        $openids = $content['data']['openid'];
        if (is_array($openids)) {
//            foreach ($openids as $openid) {
//                $info = D('Common/Follow')->update_follow($openid,$token);
//            }

            D('Common/Follow')->update_follow_batch($openids, $token);
            if ($ajaxReturn) {
                $url = addons_url('UserCenter://UserCenter/lists');
                $this->success('总共拉取了' . $content['count'] . '个粉丝！', $url);
            }
        } else {
            if ($ajaxReturn) {
                $this->error('订阅号无拉取粉丝接口');
            }
        }
    }

    /**
     * 同步所有驾校信息
     */
    public function syncWeixinFollow()
    {
        $data = M("member_public")->select();
        foreach ($data as $item) {
            if (!empty($item["token"])) {
                $this->Alllistsinfo($item["token"], false);
            }
        }
        $this->success();
    }

    // 用户绑定
    public function edit()
    {
        $is_admin_edit = false;
        if (!empty($_REQUEST['id'])) {
            $map['id'] = intval($_REQUEST['id']);
            $is_admin_edit = true;
            $msg = '编辑';
            $html = 'edit';
        } else {
            $msg = '绑定';
            $openid = $map ['openid'] = get_openid();
            $html = 'moblieForm';
        }
        $token = $map ['token'] = get_token();
        $model = $this->getModel('follow');

        if (IS_POST) {
            $is_admin_edit && $_POST['status'] = 2;
            $Model = D(parse_name(get_table_name($model ['id']), 1));
            // 获取模型的字段信息
            $Model = $this->checkAttr($Model, $model ['id']);
            if ($Model->create() && $Model->where($map)->save()) {
                //lastsql();exit;
                $url = '';
                $bind_backurl = cookie('__forward__');
                $config = getAddonConfig('UserCenter');
                $jumpurl = $config['jumpurl'];

                if (!empty($bind_backurl)) {
                    $url = $bind_backurl;
                    cookie('__forward__', NULL);
                } elseif (!empty($jumpurl)) {
                    $url = $jumpurl;
                } elseif (!$is_admin_edit) {
                    $url = addons_url('WeiSite://WeiSite/index', $map);
                }

                $this->success($msg . '成功！', $url);
            } else {
                //lastsql();
                //dump($map);exit;
                $this->error($Model->getError());
            }
        } else {
            $fields = get_model_attribute($model ['id']);
            if (!$is_admin_edit) {
                $fieldArr = array('nickname', 'sex', 'mobile'); //headimgurl
                foreach ($fields[1] as $k => $vo) {
                    if (!in_array($vo['name'], $fieldArr))
                        unset($fields[1][$k]);
                }
            }

            // 获取数据
            $data = M(get_table_name($model ['id']))->where($map)->find();

            $token = get_token();
            if (isset ($data ['token']) && $token != $data ['token'] && defined('ADDON_PUBLIC_PATH')) {
                $this->error('非法访问！');
            }

            // 自动从微信接口获取用户信息
            empty($openid) || $info = getWeixinUserInfo($openid, $token);
            if (is_array($info)) {
                if (empty ($data ['headimgurl']) && !empty ($info ['headimgurl'])) {
                    // 把微信头像转到WeiPHP的通用图片ID保存 TODO
                    $data ['headimgurl'] = $info ['headimgurl'];
                }
                $data = array_merge($info, $data);
            }

            $this->assign('fields', $fields);
            $this->assign('data', $data);
            $this->meta_title = $msg . '用户消息';

            $this->assign('post_url', U('edit'));

            $this->display($html);
        }


    }

    public function userCenter()
    {
        $this->display();
    }

    function config()
    {
        // 使用提示
        $normal_tips = '如需用户关注时提示先绑定，请进入‘欢迎语’插件按提示进行配置提示语';
        $this->assign('normal_tips', $normal_tips);

        parent::config();
    }

    /**
     * 查询条件
     * @param $model
     * @param $fields
     */
    public function _search_map($model, $fields)
    {
        $map = parent::_search_map($model, $fields);
        $map || $map = array();
        if (empty($map["nickname"])) {
            $map['nickname'] = array('neq', "");
        }
        return $map;
    }
}
