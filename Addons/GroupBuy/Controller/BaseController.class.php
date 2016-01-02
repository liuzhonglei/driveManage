<?php

namespace Addons\GroupBuy\Controller;


/**
 * Class BaseController
 * @package Addons\CutPrice\Controller
 */
class BaseController extends \Addons\School\Controller\BaseController
{
    // config
    var $config;

    /**
     * init
     */
    function _initialize()
    {
        parent::_initialize();

        // 行为
        $controller = strtolower(_CONTROLLER);

        //菜单
        $res ['title'] = '团购信息';
        $res ['url'] = addons_url('GroupBuy://GroupBuy/lists');
        $res ['class'] = $controller == 'groupbuy' ? 'current' : '';
        $nav [] = $res;

        $res ['title'] = '组团人员';
        $res ['url'] = addons_url('GroupBuy://GroupBuyParty/lists');
        $res ['class'] = $controller == 'groupbuyparty' ? 'current' : '';
        $nav [] = $res;

        $res ['title'] = '团购类型';
        $res ['url'] = addons_url('GroupBuy://GroupBuyType/lists');
        $res ['class'] = $controller == 'groupbuytype' ? 'current' : '';
        $nav [] = $res;

        // 配置导航
        $this->assign('nav', $nav);

    }
    public function _search_map($model, $fields)
    {
        if(!empty($_REQUEST["searchOpenid"])){
            $openids = $this->getStudentOpenids($_REQUEST["searchOpenid"],"openid");
            $map['token'] = get_token();
            $map['openid']  = array('in',$this->getArrayStr($openids,false) );
            return $map;
        }else{
            return parent::_search_map($model, $fields); // TODO: Change the autogenerated stub
        }
    }
}
