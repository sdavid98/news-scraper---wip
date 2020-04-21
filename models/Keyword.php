<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "keyword".
 *
 * @property int $id
 * @property int $article_id
 * @property string $keyword
 *
 * @property Article $article
 */
class Keyword extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'keyword';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['article_id', 'keyword'], 'required'],
            [['article_id'], 'integer'],
            [['keyword'], 'string', 'max' => 45],
            [['keyword'], 'unique'],
            [['article_id'], 'exist', 'skipOnError' => true, 'targetClass' => Article::className(), 'targetAttribute' => ['article_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'article_id' => 'Article ID',
            'keyword' => 'Keyword',
        ];
    }

    /**
     * Gets query for [[Article]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getArticle()
    {
        return $this->hasOne(Article::className(), ['id' => 'article_id']);
    }
}
