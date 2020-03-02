<?php


namespace XenforoLoginXenforo\Api\Controller;


use XenforoLoginXenforo\Entity\XenforoLoginToken;
use XF\Entity\User;
use XF\Mvc\ParameterBag;

class ProfileController extends AuthlibController
{
    public function actionGet(ParameterBag $params){
        try{
            return $this->handleGet($params);
        }catch (\Exception $e){
            return $this->error($e->getCode(),$e->getMessage(),$e->getTraceAsString());
        }
    }
    public function handleGet(ParameterBag $params)
    {
        $db=\XF::db();
        $user=$db->fetchRow('SELECT * FROM `xf_user` WHERE `uuid` = ?;',$params->uuid);
        return $this->apiSuccess($this->availableProfiles($user['user_id']));
    }
}