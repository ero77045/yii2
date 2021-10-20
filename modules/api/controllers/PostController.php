<?php
/**
 * Created by PhpStorm.
 * User: Aram
 * Date: 12/17/2019
 * Time: 11:17 AM
 */

namespace app\modules\api\controllers;


use app\common\repositories\HeadingRepository;
use app\common\repositories\PostRepository;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class PostController extends ApiBaseController
{
    protected function verbs()
    {
        return [
            'get-posts' => ['GET', 'HEAD'],
        ];
    }

    public function actionGetPosts()
    {
        $data = \Yii::$app->request->get();
        $heading_id = ArrayHelper::getValue($data, 'heading_id');

        $postRepository = new PostRepository();

        return $postRepository->getPosts($heading_id);
    }
}