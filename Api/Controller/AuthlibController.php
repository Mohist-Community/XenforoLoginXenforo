<?php


namespace XenforoLoginXenforo\Api\Controller;


use XF\Api\Controller\AbstractController;
use XF\Api\Mvc\Reply\ApiResult;
use XF\Api\Result\ArrayResult;
use XF\Mvc\ParameterBag;

class AuthlibController extends AbstractController
{
    public function allowUnauthenticatedRequest($action){
        return true;
    }
    public function actionPost(){
        try{
            return $this->handle();
        }catch (\Exception $e){
            return $this->error($e->getCode(),$e->getMessage(),$e->getTraceAsString());
        }
    }
    public function error($error='unknown',$errorMessage='Unknown error',$cause='unknown',$code=500,$result=[]){
        $result['error']=$error;
        $result['errorMessage']=$errorMessage;
        $result['cause']=$cause;
        $result = new ArrayResult($result);
        $result = new ApiResult($result);
        $result->setResponseCode($code);
        return $result;
    }
    public function handle()
    {
        return $this->apiSuccess(['handle'=>true]);
    }

    public function uuid_create(){
        mt_srand((double)microtime() * 10000);//optional for php 4.2.0 and up.
        return md5(uniqid(rand(), true));
    }

    public function availableProfiles($user_id){
        $db=\XF::db();
        $result=$db->fetchRow(
            'SELECT * FROM `xf_user` WHERE `user_id`=?;',$user_id);
        if(!$result['uuid']){
            if(\XF::options()['UuidFrom']){
                $result['uuid']=$this->uuid_create();
            }else{
                $result['uuid']=$this->uuid_from($result['username']);
            }
            $db->query(
                'UPDATE `xf_user` SET `uuid` = ? WHERE `user_id` = ?;',[
                    $result['uuid'],
                    $user_id,
            ]);
        }
        return [
            'id'=>$result['uuid'],
            'name'=>$result['username'],
            'properties'=>[]
        ];
    }

    public function user($user_id){
        return [
            'id'=>$user_id,
            'properties'=>[]
        ];
    }
    public function no_content(){
        $result = new ArrayResult([]);
        $result = new ApiResult($result);
        $result->setResponseCode(204);
        return $result;
    }

    public function apiSuccess(array $extra = []){
        $result = new ArrayResult($extra);
        $result = new ApiResult($result);
        return $result;
    }
    public function uuid_from($username){
        $data = hex2bin(md5("OfflinePlayer:" . $username));
        $data[6] = chr(ord($data[6]) & 0x0f | 0x30);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return bin2hex($data);
    }
}