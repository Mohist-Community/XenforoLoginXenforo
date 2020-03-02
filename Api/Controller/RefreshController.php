<?php


namespace XenforoLoginXenforo\Api\Controller;


use XenforoLoginXenforo\Entity\XenforoLoginToken;
use XF\Entity\User;

class RefreshController extends AuthlibController
{
    public function handle()
    {
        $input = json_decode($this->request()->getInputRaw());
        $db=\XF::db();
        $token=$db->fetchRow('SELECT * FROM `xf_xenforo_login_token` WHERE `accessToken` = ?;',$input->accessToken);

        if(!$token){
            return $this->error('ForbiddenOperationException','Invalid token.','Incorrect token.',403);
        }
        if(@$token['clientToken'] && $token['clientToken']!=$input->clientToken){
            return $this->error('ForbiddenOperationException','Invalid token.','Incorrect token.',403);
        }
        $db->query(
            'DELETE FROM `xf_xenforo_login_token` WHERE `accessToken` = ?;',$token['accessToken']);

        $clientToken=@$input->clientToken?$input->clientToken:$this->uuid_create();
        $accessToken=$this->uuid_create();

        $db->query(
            'INSERT INTO `xf_xenforo_login_token` (`user_id`, `clientToken`, `accessToken`, `time`) VALUES (?,?,?,?);',[
            $token['user_id'],
            $clientToken,
            $accessToken,
            time(),
        ]);
        return $this->apiSuccess([
            'accessToken'=>$accessToken,
            'clientToken'=>$clientToken,
            'availableProfiles'=>[$this->availableProfiles($token['user_id'])],
            'selectedProfile'=>$this->availableProfiles($token['user_id']),
            'user'=>$this->user($token['user_id']),
        ]);
    }
}