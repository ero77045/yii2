<?php


namespace app\common\repositories;


use app\common\interfaces\IRepositoryInterface;
use app\models\Heading;
use app\models\Post;
use app\models\PostHeading;
use phpDocumentor\Reflection\Types\Self_;
use yii\helpers\ArrayHelper;

/**
 * Class PostRepository
 * @package app\common\repositories
 */
class PostHeadingRepository implements IRepositoryInterface
{
    /**
     * @return array
     */
    public static function getPostsIds(array $ids): array
    {
        return  ArrayHelper::getColumn(PostHeading::find()->where(['heading_id'=>$ids])->asArray()->all(),'post_id',false);
    }
}