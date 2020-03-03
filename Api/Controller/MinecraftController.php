<?php


namespace XenforoLoginXenforo\Api\Controller;


use XenforoLoginXenforo\Entity\XenforoLoginToken;
use XF\Entity\User;
use XF\Mvc\ParameterBag;

class MinecraftController extends AuthlibController
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
        $user=$db->fetchRow('SELECT * FROM `xf_user` WHERE `username` = ?;',$params->username);
        return $this->apiSuccess($this->availableProfiles($user['user_id']));
    }
    public function handle()
    {
        $input = json_decode($this->request()->getInputRaw());
        $result=[];
        foreach($input as $name){
            $db=\XF::db();
            $user=$db->fetchRow('SELECT * FROM `xf_user` WHERE `username` = ?;',$name);
            $per= @$this->availableProfiles(@$user['user_id']);
            if($per){
                $result[]=$per;
            }
        }
        return $this->apiSuccess($result);
    }
}