<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "posts_headings".
 *
 * @property int $id
 * @property int $post_id
 * @property int $heading_id
 *
 * @property Heading $heading
 * @property Post $post
 */
class PostHeading extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'posts_headings';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['post_id', 'heading_id'], 'required'],
            [['post_id', 'heading_id'], 'integer'],
            [['heading_id'], 'exist', 'skipOnError' => true, 'targetClass' => Heading::className(), 'targetAttribute' => ['heading_id' => 'id']],
            [['post_id'], 'exist', 'skipOnError' => true, 'targetClass' => Post::className(), 'targetAttribute' => ['post_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'post_id' => 'Post ID',
            'heading_id' => 'Heading ID',
        ];
    }

    /**
     * Gets query for [[Heading]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHeading()
    {
        return $this->hasOne(Heading::class, ['id' => 'heading_id']);
    }

    /**
     * Gets query for [[Post]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPost()
    {
        return $this->hasOne(Post::class, ['id' => 'post_id']);
    }

    /**
     * @param $heading_ids
     * @param $post_id
     */
    public static function createAll($heading_ids, $post_id)
    {
        self::deleteAll(['post_id' => $post_id]);

        foreach ($heading_ids as $heading_id) {
            $model = new self();

            if (!empty($heading_id)) {
                $model->heading_id = $heading_id;
                $model->post_id = $post_id;
                $model->save();
            }
        }
    }
}
