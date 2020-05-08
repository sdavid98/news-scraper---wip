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

    public static function saveKeywords($articleId, $keywords) {
        foreach ($keywords as $keyword) {
            $key = new Keyword();
            $key->article_id = $articleId;
            $key->keyword = $keyword;
            $key->save();
        }
    }

    public static function getTitleAndKeywordsByUrl($url) {
        $postData = array(
            'text' => $url,
            'tab' => 'ae',
            'options' => [],
        );

        $context = stream_context_create(array(
            'http' => array(
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\n",
                'content' => json_encode($postData)
            )
        ));
        $response = file_get_contents('https://www.summarizebot.com/scripts/text_analysis.py', FALSE, $context);
        $title = json_decode($response)->{'article title'};
        $text = json_decode($response)->text;

        $context = stream_context_create(array(
            'http' => array(
                'method' => 'POST',
                'header' => "Content-Type: text/plain\r\n",
                'content' => $text
            )
        ));
        $response = file_get_contents('https://languages.cortical.io/rest/text/keywords?retina_name=en_general', FALSE, $context);
        $keywords = json_decode($response);

        return [$title, $keywords];
    }

    public static function getKeywordsForSearchByFirstLetters($letters) {
        return self::find()->select('keyword')->where('keyword LIKE :substr', array(':substr' => $letters.'%'))->orderBy('CHAR_LENGTH(keyword)')->limit('7')->all();
    }

    public static function getArticleIdsByKeyword($keyword) {
        return self::find()->select('article_id')->where('keyword LIKE :substr', array(':substr' => '%'.$keyword.'%'))->distinct()->all();
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
