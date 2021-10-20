<?php


namespace app\common\repositories;


use app\common\interfaces\IRepositoryInterface;
use app\models\Heading;
use app\models\Post;
use phpDocumentor\Reflection\Types\Self_;
use yii\helpers\ArrayHelper;

/**
 * Class PostRepository
 * @package app\common\repositories
 */
class PostRepository implements IRepositoryInterface
{
    /**
     * @param int $parentId
     * @return array
     */
    public function getPosts(int $id): array
    {
        $heading_ids = HeadingRepository::getHeadingIds($id);

        $posts_ids = PostHeadingRepository::getPostsIds($heading_ids);

        $data = Post::find()->where(['id' => $posts_ids])->asArray()->all();

        $normalize_data = $this->normalizeData($data);

        if (count($normalize_data)) {
            return $normalize_data;
        }

        return [];
    }

    /**
     * @param array $data
     * @return array
     */
    public function normalizeData(array $data): array
    {
        $new_data = [];

        foreach ($data as $row) {
            $new_data[] = array_values($row);
        }

        return $new_data;
    }
}