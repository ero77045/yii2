<?php

namespace app\modules\api\controllers;


use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\Response;


class ApiBaseController extends Controller
{
    const API_TOKEN = 'Akd*njd^^&*%bdsghd%dsndctw5643l2ndc7s6tw';
    public $enableCsrfValidation = false;
    public $sessionId = false;

    public function beforeAction($action)
    {

        \Yii::$app->response->format = Response::FORMAT_JSON;
        $headers = \Yii::$app->response->headers;
        $headers->add('Access-Control-Allow-Origin', '*');
        $headers->add('Access-Control-Allow-Methods', 'GET, POST, PATCH, PUT, DELETE, OPTIONS');
        $headers->add('Access-Control-Allow-Headers', 'Origin, Content-Type,session,token,language');
        $method = \Yii::$app->request->getMethod();
        if ($method == "OPTIONS") {
            return null;
        }
        $token = \Yii::$app->request->headers->get('token');

//        if(!$token || $token!=self::API_TOKEN){
//            throw new HttpException(500, 'Invalid token');
//        }
        $_POST = \yii\helpers\Json::decode(file_get_contents("php://input"));
        return parent::beforeAction($action); // TODO: Change the autogenerated stub
    }

    public function afterAction($action, $result)
    {
        if (\Yii::$app->response->statusCode === 200 && \Yii::$app->response->format === 'json') {
        }
        return parent::afterAction($action, $result); // TODO: Change the autogenerated stub
    }

    protected function createResponse($data)
    {

        $error = ArrayHelper::getValue($data, 'error');


        $return = [
            'respcode' => 1,
            'data' => $data
        ];
        if ($this->sessionId) {
            $return['sessionId'] = $this->sessionId;
        }
        if ($error) {
            $return = [
                'respcode' => 0,
                'respmess' => $error
            ];
        }
        return $return;
    }
}