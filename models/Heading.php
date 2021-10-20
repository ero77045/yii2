<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "headings".
 *
 * @property int $id
 * @property string $name Имя
 * @property string $created_at Создано на
 * @property int|null $parent_id
 *
 * @property Heading $parent
 * @property Heading[] $parents
 * @property PostHeading[] $postsHeadings
 */
class Heading extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'headings';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['parent_id'], 'integer'],
            [['name', 'url'], 'string', 'max' => 255],
            [['name'], 'unique'],
            [['url'], 'unique'],
            [['created_at'], 'safe'],
            [['url'], 'default', 'value' => null],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'created_at' => 'Created At',
            'parent_id' => 'Parent ID',
        ];
    }

    /**
     * Gets query for [[Parent]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Heading::class, ['id' => 'parent_id']);
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->created_at = date('Y-m-d H:i:s');
        }
        return parent::beforeSave($insert);
    }

    /**
     * Gets query for [[Parents]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParents()
    {
        return $this->hasMany(Heading::class, ['parent_id' => 'id']);
    }

    /**
     * Gets query for [[PostsHeadings]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPostsHeadings()
    {
        return $this->hasMany(PostHeading::class, ['heading_id' => 'id']);
    }

}
