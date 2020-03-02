<?php


namespace XenforoLoginXenforo\Api\Controller;


use XF\Api\Mvc\Reply\ApiResult;
use XF\Api\Result\ArrayResult;

class JoinController extends AuthlibController
{
    public function handle()
    {
        $input = json_decode($this->request()->getInputRaw());
        $db=\XF::db();
        $token=$db->fetchRow('SELECT * FROM `xf_xenforo_login_token` WHERE `accessToken` = ?;',$input->accessToken);

        if(!$token){
            return $this->error('ForbiddenOperationException','Invalid token.','Incorrect token.',403);
        }
        $user=$db->fetchRow('SELECT * FROM `xf_user` WHERE `user_id` = ?;',$token['user_id']);
        if(!$user['uuid']){
            if(\XF::options()['UuidFrom']){
                $user['uuid']=$this->uuid_create();
            }else{
                $user['uuid']=$this->uuid_from($user['username']);
            }
            $db->query(
                'UPDATE `xf_user` SET `uuid` = ? WHERE `user_id` = ?;',[
                $user['uuid'],
                $user['user_id'],
            ]);
        }
        if($user['uuid']!=$input->selectedProfile){
            return $this->error('ForbiddenOperationException','Invalid token.','Incorrect token.',403);
        }
        $db->query(
            'INSERT INTO `xf_xenforo_session` (`serverId`, `accessToken`, `time`) VALUES (?,?,?);',[
            $input->serverId,
            $input->accessToken,
            time(),]);
        $db->query(
            'DELETE FROM `xf_xenforo_session` WHERE `time` < ?;',[
            time()-30,]);
        return $this->no_content();
    }
}