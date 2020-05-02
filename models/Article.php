<?php

namespace app\models;

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
            [['code', 'filename', 'topic_id', 'user_id', 'external_link'], 'required'],
            [['created'], 'safe'],
            [['points', 'topic_id', 'user_id'], 'integer'],
            [['code'], 'string', 'max' => 20],
            [['filename'], 'string', 'max' => 60],
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

    public static function createArticleDataFromUrl($url)
    {
        $article = array();
        $article['external_link'] = $url;
        $article['topic_id'] = self::getTopicIdByUrl($url);
        [$article['summary'], $article['first_row']] = self::getSummaryByUrl($url);
        [$article['title'], $article['keywords']] = self::getTitleAndKeywordsByUrl($url);
        $article['logo'] = self::getSourceLogoByUrl($url);
        $article['code'] = self::generateCode();
        $article['filename'] = $article['title'] . $article['code'];

    }

    public static function getTopicIdByUrl($url) {
        $response = file_get_contents('https://sandbox.aylien.com/textapi/classify/iab-qag?taxonomy=iab-qag&language=en&url=' . $url);
        //$topic = array_values(json_decode($response)->categories)[0]->label;
        $myTopic = '';
        $topics = json_decode($response)->categories;
        foreach ($topics as $topic) {
            if (in_array($topic->label, ['Business', 'Careers'])) {
                $myTopic = 'business';
                break;
            }
            if (in_array($topic->label, ['Health & Fitness', 'Food & Drink'])) {
                $myTopic = 'health';
                break;
            }
            if (in_array($topic->label, ['Arts & Entertainment', 'Hobbies & Interests'])) {
                $myTopic = 'entertainment';
                break;
            }
            if (in_array($topic->label, ['Education', 'Careers', 'Family & Parenting'])) {
                $myTopic = 'society';
                break;
            }
            if (in_array($topic->label, ['Law,Gov\'t & Politics', 'Careers'])) {
                $myTopic = 'politics';
                break;
            }
            if (in_array($topic->label, ['News', 'Home & Garden', 'Automotive'])) {
                $myTopic = 'others';
                break;
            }
        }

        return Topic::getTopicIdByTopicName($myTopic);
    }

    public static function getSummaryByUrl($url) {
        $response = file_get_contents('https://sandbox.aylien.com/textapi/summarize?language=en&sentences_number=8&url=' . $url);
        return [join(' ', json_decode($response)->sentences), json_decode($response)->sentences[0]];
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
        $title = json_decode($response)->title;
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

    public static function getSourceLogoByUrl($url) {
        $saveto = '../src/images/source-logos/logo.jpg';

        $ch = curl_init ('https://logo.clearbit.com/'.parse_url($url)['host']);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $raw=curl_exec($ch);
        curl_close ($ch);

        $fp = fopen($saveto,'w');
        fwrite($fp, $raw);
        fclose($fp);

        return true;
    }

    public static function generateCode()
    {
        $result = '';

        $t=time();
        foreach (str_split(strval($t)) as $n) {
            $result .= strtolower(chr(64 + $n));
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
