<?php

namespace app\models;

use phpDocumentor\Reflection\Types\Null_;
use Yii;

/**
 * This is the model class for table "article".
 *
 * @property int $id
 * @property string $code
 * @property string $filename
 * @property string $title
 * @property string|null $created
 * @property int|null $points
 * @property int $topic_id
 * @property int $user_id
 * @property string $external_link
 * @property string $first_row
 * @property int|null $sourcelogo_id
 * @property string $summary
 *
 * @property Topic $topic
 * @property User $user
 * @property Comment[] $comments
 * @property Keyword[] $keywords
 * @property Userlike[] $userlikes
 * @property Usersaved[] $usersaveds
 */
class Article extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'article';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code', 'filename', 'topic_id', 'external_link', 'summary'], 'required'],
            [['created'], 'safe'],
            [['points', 'topic_id', 'user_id'], 'integer'],
            [['code'], 'string', 'max' => 20],
            [['filename'], 'string', 'max' => 150],
            [['external_link'], 'string', 'max' => 150],
            [['code'], 'unique'],
            [['filename'], 'unique'],
            [['topic_id'], 'exist', 'skipOnError' => true, 'targetClass' => Topic::className(), 'targetAttribute' => ['topic_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'Code',
            'filename' => 'Filename',
            'created' => 'Created',
            'points' => 'Points',
            'topic_id' => 'Topic ID',
            'user_id' => 'User ID',
            'external_link' => 'External Link',
        ];
    }

    public static function saveArticleFromGeneratedData($url) {
        [$title, $keywords] = Keyword::getTitleAndKeywordsByUrl($url);
        $topicName = Topic::getTopicNameByUrl($url);

        $article = new Article();
        $article->code = self::generateCode();
        $article->topic_id = Topic::getTopicIdByTopicName($topicName);
        $article->title = $title;
        $article->filename = str_replace('+', '-' , urlencode(strtolower(preg_replace("/[^0-9a-zA-Z \-]/", "", $article->title . '-' . $article->code))));
        [$article->summary, $article->first_row] = self::getSummaryByUrl($url);
        $article->external_link = $url;

        $article->sourcelogo_id = Sourcelogo::getFallbackImageIdByTopic($topicName);
        if (Sourcelogo::getImageIdByUrl($url)) {
            $article->sourcelogo_id = Sourcelogo::getImageIdByUrl($url);
        }

        if ($article->save()) {
            Keyword::saveKeywords($article->getPrimaryKey(), $keywords);
            return $article->getPrimaryKey();
        }

        return false;
    }

    public function writeArticleFile($text, $article) {
        $keywords = [];
        foreach (Keyword::findAll(['article_id' => $article->id]) as $keyword) {
            array_push($keywords, $keyword->keyword);
        }
        $content = [
            'created' => $article->created,
            'logo' => Sourcelogo::findOne($article->sourcelogo_id)->imagename,
            'title' => $article->title,
            'link' => $article->external_link,
            'topicName' => Topic::findOne($article->topic_id)->topic_title,
            'keywords' => $keywords,
            'text' => $text
        ];

        file_put_contents("../views/article/" . $this->filename . ".php", print_r(trim('<?php $info = ', "'"), true));
        file_put_contents("../views/article/" . $this->filename . ".php", var_export($content, true), FILE_APPEND);
        file_put_contents("../views/article/" . $this->filename . ".php", print_r(trim(";\r\ninclude '../src/article-body.php';", '"'), true), FILE_APPEND);
    }

    public static function getSummaryByUrl($url) {
        $response = file_get_contents('https://sandbox.aylien.com/textapi/summarize?language=en&sentences_number=8&url=' . $url);
        return [join(' ', json_decode($response)->sentences), substr(json_decode($response)->sentences[0], 0, 120)];
    }

    public static function generateCode()
    {
        $result = '';

        $t=time();
        foreach (str_split(strval($t)) as $n) {
            $result .= strtolower(chr(65 + $n));
        }

        return $result;
    }

    /**
     * Gets query for [[Topic]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTopic()
    {
        return $this->hasOne(Topic::className(), ['id' => 'topic_id']);
    }


    /**
     * Gets query for [[Keywords]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKeywords()
    {
        return $this->hasMany(Keyword::className(), ['article_id' => 'id']);
    }
}
