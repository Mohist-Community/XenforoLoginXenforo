<?php


namespace XenforoLoginXenforo\Api\Controller;



use XF\Api\Mvc\Reply\ApiResult;
use XF\Api\Result\ArrayResult;

class SignoutController extends AuthlibController
{
    public function handle()
    {
        $input = json_decode($this->request()->getInputRaw());

        /** @var \XF\Service\User\Login $loginService */
        $loginService = $this->service('XF:User\Login', $input->username,$this->request->getIp());
        if ($loginService->isLoginLimited($limitType))
        {
            return $this->error(\XF::phrase('your_account_has_temporarily_been_locked_due_to_failed_login_attempts'));
        }

        $user = $loginService->validate($input->password, $error);
        if (!$user)
        {
            return $this->error('ForbiddenOperationException',$error,'Incorrect username or password.',403);
        }

        $db=\XF::db();
        $db->query(
            'DELETE FROM `xf_xenforo_login_token` WHERE `user_id` = ?;',$user->user_id);


        $result = new ArrayResult([]);
        $result = new ApiResult($result);
        $result->setResponseCode(204);
        return $result;
    }
}