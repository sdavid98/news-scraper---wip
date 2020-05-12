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
        [$title, $keywords, $topicName] = Keyword::getTitleAndKeywordsAndTopicNameByUrl($url);

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


    public static function getSummaryByUrl($url) {
        $postData = array(
            'text' => $url,
            'tab' => 'sm',
            'options' => ['size' => '50', 'domain' => ''],
        );

        $context = stream_context_create(array(
            'http' => array(
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\n",
                'content' => json_encode($postData)
            )
        ));
        $response = file_get_contents('https://www.summarizebot.com/scripts/text_analysis.py', FALSE, $context);
        $summaryArray = json_decode($response)[0]->summary;
        $summary = '';
        foreach ($summaryArray as $sumItem) {
            $summary .= $sumItem->sentence . ' ';
        }
        $myfile = fopen("../sumsum.txt", "w");
        fwrite($myfile, $summary);
        fclose($myfile);

        return [$summary, substr($summaryArray[0]->sentence, 0, 140)];
        //$response = file_get_contents('https://sandbox.aylien.com/textapi/summarize?language=en&sentences_number=10&url=' . $url);
        //return [join(' ', json_decode($response)->sentences), substr(json_decode($response)->sentences[0], 0, 120)];
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
