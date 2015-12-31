<?php

namespace Common\Model;

use Think\Model;
use User\Api\UserApi;

/**
 * 粉丝操作
 */
class FollowModel extends Model
{
    function init_follow($openid)
    {
        if (empty ($openid) || $openid == -1)
            return false;

        $data ['token'] = get_token();
        $data ['openid'] = $openid;

        $info = $this->where($data)->find();

        if ($info) {
            $save ['subscribe_time'] = $info ['subscribe_time'] = time();
            $res = $this->where($data)->save($save);
        } else {
            $data ['subscribe_time'] = time();
            $uid = $this->get_uid_by_ucenter($data ['openid'], $data ['token']);
            if ($uid > 0) {
                $data ['id'] = $uid;
                $res = $this->add($data);
            }

            $info = $data;
        }
        return $info;
    }

    // 自动初始化微信用户
    function get_uid_by_ucenter($openid, $token)
    {
        static $_email_int = 0;
        $info ['openid'] = $openid;
        $info ['token'] = $token;
        $res = M('ucenter_member')->where($info)->find();

        if ($res)
            return $res ['id'];

        $email = time() . rand(01, 99) . $_email_int . '@weiphp.cn';
        $nickname = uniqid() . rand(01, 99) . $_email_int;

        /* 调用注册接口注册用户 */
        $User = new UserApi ();
        $uid = $User->register($nickname, '123456', $email, '', $openid, $token);

        $_email_int += 1;

        return $uid;
    }

    /**
     * 获取粉丝全部信息
     */
    public function getFollowInfo($id)
    {
        static $_followInfo;
        if (isset ($_followInfo [$id])) {
            return $_followInfo [$id];
        }

        $_followInfo [$id] = $this->find($id);
        return $_followInfo [$id];
    }

    /**
     * 用户中心模块特殊处理
     * @param $openid
     * @return mixed
     */
    function update_follow($openid)
    {
        $data ['token'] = get_token();
        $data ['openid'] = $openid;
        $winfo = getWeixinUserInfo($openid, $data ['token']); //获取用户所有信息

        $info = $this->updateFollow($data,$winfo);
        return $info;
    }

    /**
     * 刷新微信
     * @param $data 查询条件
     * @param $winfo 用户信息
     */
    function updateFollow($data,$winfo)
    {
        $info = $this->where($data)->find();
        if ($info) {  // 如果数据库已经有该用户信息 则更新用户资料
            $save ['subscribe_time'] = $winfo ['subscribe_time'];
            $save ['nickname'] = $winfo ['nickname'];
            $save ['sex'] = $winfo ['sex'];
            $save ['city'] = $winfo ['city'];
            $save ['province'] = $winfo ['province'];
            $save ['country'] = $winfo ['country'];
            $save ['headimgurl'] = $winfo ['headimgurl'];
            $res = $this->where($data)->save($save);
        } else {
            $data ['subscribe_time'] = time();
            $uid = $this->get_uid_by_ucenter($data ['openid'], $data ['token']);
            if ($uid > 0) {
                $data ['id'] = $uid;
                $data ['nickname'] = $winfo ['nickname'];
                $data ['sex'] = $winfo ['sex'];
                $data ['city'] = $winfo ['city'];
                $data ['province'] = $winfo ['province'];
                $data ['country'] = $winfo ['country'];
                $data ['headimgurl'] = $winfo ['headimgurl'];
                $res = $this->add($data);
            }

            $info = $data;
        }

        return $info;
    }

    /**
     * 授权用户信息注册
     * @param $code
     */
    function updateOauthFollow()
    {
        $weChat = createWeChat();
        $accessToken = $weChat->getOauthAccessToken()['access_token'];
        $winfo =  $weChat->getOauthUserinfo($accessToken, get_openid());

        if($winfo){
            $data ['token'] = get_token();
            $data ['openid'] = get_openid();

            $info = $this->updateFollow($data,$winfo);
            return $info;
        }
    }
}

?>
