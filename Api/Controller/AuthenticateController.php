<?php


namespace XenforoLoginXenforo\Api\Controller;



class AuthenticateController extends AuthlibController
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

        $clientToken=@$input->clientToken?$input->clientToken:$this->uuid_create();
        $accessToken=$this->uuid_create();

        $db=\XF::db();
        $db->query(
            'DELETE FROM `xf_xenforo_login_token` WHERE `user_id` = ?;',$user->user_id);
        $db->query(
            'INSERT INTO `xf_xenforo_login_token` (`user_id`, `clientToken`, `accessToken`, `time`) VALUES (?,?,?,?);',[
                $user->user_id,
                $clientToken,
                $accessToken,
                time(),
        ]);


        return $this->apiSuccess([
            'accessToken'=>$accessToken,
            'clientToken'=>$clientToken,
            'availableProfiles'=>[$this->availableProfiles($user->user_id)],
            'selectedProfile'=>$this->availableProfiles($user->user_id),
            'user'=>$this->user($user->user_id),
        ]);
    }
}