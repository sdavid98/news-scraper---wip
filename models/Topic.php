<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "topic".
 *
 * @property int $id
 * @property string $title
 *
 * @property Article[] $articles
 * @property Usertopic[] $usertopics
 */
class Topic extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'topic';
    }

    public static function getTopicIdByTopicName($name) {
        return self::findOne(['topic_title' => trim(strtolower($name))])->id;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['title'], 'string', 'max' => 25],
            [['title'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
        ];
    }

    /**
     * Gets query for [[Articles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getArticles()
    {
        return $this->hasMany(Article::className(), ['topic_id' => 'id']);
    }

    /**
     * Gets query for [[Usertopics]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsertopics()
    {
        return $this->hasMany(Usertopic::className(), ['topic_id' => 'id']);
    }
}
