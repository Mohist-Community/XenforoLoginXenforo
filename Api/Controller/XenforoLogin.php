<?php


namespace XenforoLoginXenforo\Api\Controller;


use XF\Api\Controller\AbstractController;
use XF\Mvc\ParameterBag;

class XenforoLogin extends AuthlibController
{
    public function actionGet(){
        //try{
        return $this->handle();
        //}catch (\Exception $e){
        //    return $this->error($e->getCode(),$e->getMessage(),$e->getTraceAsString());
        //}
    }
    public function handle()
    {
        return $this->apiSuccess([
            'meta'=>[
                'serverName'=>\XF::options()['boardTitle'],
                'implementationName'=>'XenforoLogin',
                'implementationVersion'=>'0',
            ],
            'skinDomains'=>explode("\n",\XF::options()['skinDomains']),
            'signaturePublickey'=>\XF::options()['signaturePublickey'],
        ]);
    }
}