<?php


namespace XenforoLoginXenforo\Api\Controller;



use XF\Api\Mvc\Reply\ApiResult;
use XF\Api\Result\ArrayResult;

class ValidateController extends AuthlibController
{
    public function handle()
    {
        $input = json_decode($this->request()->getInputRaw());
        $db=\XF::db();
        $token=$db->fetchRow('SELECT * FROM `xf_xenforo_login_token` WHERE `accessToken` = ?;',$input->accessToken);

        if(!$token){
            return $this->error('ForbiddenOperationException','Invalid token.','Incorrect token.',403);
        }
        if(@$input->clientToken && $token['clientToken']!=$input->clientToken){
            return $this->error('ForbiddenOperationException','Invalid token.','Incorrect token.',403);
        }
        $result = new ArrayResult([]);
        $result = new ApiResult($result);
        $result->setResponseCode(204);
        return $result;
    }
}