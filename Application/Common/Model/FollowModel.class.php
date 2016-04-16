<?php

namespace Common\Model;

use Think\Model;
use User\Api\UserApi;
use Think\Log;

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
     * @param $token
     * @return mixed
     */
    function update_follow($openid,$token = null)
    {
        $data ['token'] = $token;
        $data ['token'] || $data ['token'] = get_token();
        $data ['openid'] = $openid;
        $winfo = getWeixinUserInfo($openid, $data ['token']); //获取用户所有信息

        $info = $this->updateFollow($data,$winfo);
        return $info;
    }

    /**
     * 批量更新
     * @param $openid
     * @param null $token
     */
    function update_follow_batch($openidList,$token = null){
        $data ['token'] = $token;
        $data ['token'] || $data ['token'] = get_token();

        $num = 0;
        $lastOpenid = $openidList[count($openidList)-1];
        foreach ( $openidList as $vo ) {
            // 查询条件
            $param ['user_list'] [] = array (
                'openid' => $vo,
                'lang' => 'zh-CN'
            );
            $num++;
            if($num < 80 && $vo !== $lastOpenid){
                continue;
            }


            $url = 'https://api.weixin.qq.com/cgi-bin/user/info/batchget?access_token=' . get_access_token ();
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);//post方式提交
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($param));//要提交的信息
            $winfoList = curl_exec($ch); //执行cURL抓取页面内容
            curl_close($ch);

            // 插入
//            error_log("get weixin follow error!", 3, $winfoList);
//            Log::record('get weixin follow error!'.$winfoList, Log::NOTICE);
            $winfoList = json_decode($winfoList, true);

            if(empty($winfoList["user_info_list"])){
                Log::record('get weixin follow error!'.$winfoList, Log::ERR);
            }else{
                $winfoList = $winfoList["user_info_list"];
                foreach ($winfoList as $winfo ) {
                    if(!empty( $winfo ['openid'])){
                        $data ['openid'] = $winfo ['openid'];
                        $this->updateFollow($data,$winfo);
                    }
                }
            }



            // 还原
            $param['user_list'] = array();
            $num = 0;
        }
    }

    /**
     * 刷新微信
     * @param $data 查询条件
     * @param $winfo 用户信息
     */
    function updateFollow($data,$winfo)
    {
        // 已经不关注
        if($winfo['subscribe'] == '0'){
            return;
        }

        // 还是关注的
        $info = $this->where($data)->find();
        if ($info) {  // 如果数据库已经有该用户信息 则更新用户资料
            $save ['nickname'] = $winfo ['nickname'];
            $save ['sex'] = $winfo ['sex'];
            $save ['city'] = $winfo ['city'];
            $save ['province'] = $winfo ['province'];
            $save ['country'] = $winfo ['country'];
            $save ['headimgurl'] = $winfo ['headimgurl'];
            $save ['subscribe_time'] = $winfo ['subscribe_time'];
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
        addWeixinLog('$accessToken',$accessToken);

        $winfo =  $weChat->getOauthUserinfo($accessToken, get_openid());

        if($winfo){
            addWeixinLog('返回用户数据',$winfo);

            $data ['token'] = get_token();
            $data ['openid'] = get_openid();

            $info = $this->updateFollow($data,$winfo);
            return $info;
        }
    }
}

?>
