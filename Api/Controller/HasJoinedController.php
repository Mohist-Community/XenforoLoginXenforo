<?php


namespace XenforoLoginXenforo\Api\Controller;


use XenforoLoginXenforo\Entity\XenforoLoginToken;
use XF\Entity\User;

class HasJoinedController extends AuthlibController
{
    public function actionGet(){
        try{
            return $this->handle();
        }catch (\Exception $e){
            return $this->error($e->getCode(),$e->getMessage(),$e->getTraceAsString());
        }
    }
    public function handle()
    {
        $input = $this->filter([
            'username' => 'str',
            'serverId' => 'str',
        ]);
        $db=\XF::db();
        $session=$db->fetchRow('SELECT * FROM `xf_xenforo_session` WHERE `serverId` = ?  ORDER BY `time` DESC;',$input['serverId']);
        if(!$session){
            return $this->no_content();
        }
        $token=$db->fetchRow('SELECT * FROM `xf_xenforo_login_token` WHERE `accessToken` = ?;',$session['accessToken']);
        if(!$token){
            return $this->no_content();
        }
        $user=$db->fetchRow('SELECT * FROM `xf_user` WHERE `user_id` = ?;',$token['user_id']);
        if(!$user){
            return $this->no_content();
        }
        if($user['username']!=$input['username']){
            return $this->no_content();
        }
        if($session['time']<time()-30){
            return $this->no_content();
        }

        $db->query(
            'DELETE FROM `xf_xenforo_session` WHERE `time`<?;',[
            time()-30,
        ]);
        return $this->apiSuccess($this->availableProfiles($token['user_id']));
    }
}