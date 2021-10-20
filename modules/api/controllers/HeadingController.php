<?php
/**
 * Created by PhpStorm.
 * User: Aram
 * Date: 12/17/2019
 * Time: 11:17 AM
 */

namespace app\modules\api\controllers;


use app\common\repositories\HeadingRepository;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class HeadingController extends ApiBaseController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'get-headings' => ['get'],
                ],
            ],
        ];
    }

    public function actionGetHeadings()
    {

        $headingRepository = new HeadingRepository();

        return $headingRepository->getHeadings();

    }
//	public function actionAddOrder(){
//		$data = \Yii::$app->request->post();
//		$icredo_loan_id = ArrayHelper::getValue($data,'icredo_loan_id');
//		$order_id = ArrayHelper::getValue($data,'id');
//		//unset($data['icredo_loan_id']);
//		if($order_id){
//			$order = Orders::findOne($order_id);
//			unset($data['id']);
//		}else{
//			$order = new Orders();
//		}
//		$order->setAttributes($data);
//		$order->prepareIcredoOrder();
//		if($order->save(false)){
//			return $this->createResponse([
//				'boId'=>$order->id,
//				'boDocumentId'=>str_pad($order->id,8,'0',STR_PAD_LEFT),
//				'icredo_loan_id'=>$icredo_loan_id
//			]);
//		}else{
//
//			$erors = $order->errors;
//			return $this->createResponse(['error'=>$erors]);
//		}
//	}
//	public function actionAcceptLoan(){
//		$data = \Yii::$app->request->post();
//		$order_id = ArrayHelper::getValue($data,'id');
//		$order = Orders::findOne($order_id);
//		$order->status = Status::IS_APPROVED;
//		if($order->save(false,['status'])){
//			return $this->createResponse([
//				'boId'=>$order->id,
//				'icredo_loan_id'=>$order->icredo_loan_id
//			]);
//		}else{
//
//			$erors = $order->errors;
//			return $this->createResponse(['error'=>$erors]);
//		}
//	}
}