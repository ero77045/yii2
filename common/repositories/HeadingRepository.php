<?php


namespace app\common\repositories;


use app\common\interfaces\IRepositoryInterface;
use app\models\Heading;
use phpDocumentor\Reflection\Types\Self_;
use yii\helpers\ArrayHelper;

/**
 * Class HeadingRepository
 * @package app\common\repositories
 */
class HeadingRepository implements IRepositoryInterface
{
    /**
     * @param int $parentId
     * @return array
     */
    public function getHeadings(): array
    {
        $data = Heading::find()->asArray()->all();
        if (count($data)) {
            return self::buildTree($data);
        }
        return [];
    }

    /**
     * @param array $elements
     * @param null $parentId
     * @param array $selected_nodes
     * @param string $parent_for_tree
     * @param string $main_field
     * @return array
     */
    public static function buildTree(array $elements, $parentId = null, $selected_nodes = [],
                                     $parent_for_tree = 'parent_id', $main_field = 'name')
    {

        $branch = array();

        foreach ($elements as $index => $element) {

            $element['key'] = $element['id'];
            if (!empty($selected_nodes) && in_array($element['id'], $selected_nodes)) {
                $element['selected'] = true;
            }
            $element['name'] = array_key_exists($main_field, $element) ? $element[$main_field] : '';
            if (array_key_exists($parent_for_tree, $element) && $element[$parent_for_tree] == $parentId) {
                unset($elements[$index]);
                $children = self::buildTree($elements, $element['id'], $selected_nodes, $parent_for_tree, $main_field);
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[] = $element;
            }
        }

        return $branch;
    }

    /**
     * @return array
     */
    public static function getHeadingIds(int $id): array
    {
        $arr = ArrayHelper::getColumn(Heading::find()->where(['parent_id' => $id])
            ->asArray()->all(), 'id', false);
        array_push($arr, $id);

        return $arr;
    }

    public static function getFirstHeadingId(): ?int
    {
        return Heading::find()->one()->id ?? 1;
    }
}