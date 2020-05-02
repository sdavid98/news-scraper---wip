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
        $article['topic'] = Topic::getTopicNameByUrl($url);
        $article['topic_id'] = Topic::getTopicIdByTopicName($article['topic']);
        [$article['summary'], $article['first_row']] = self::getSummaryByUrl($url);
        [$article['title'], $article['keywords']] = Keyword::getTitleAndKeywordsByUrl($url);
        $article['logo'] = $article['topic'] . '-fallback.png';
        if (Sourcelogo::getImageByUrl($url)) {
            $article['logo'] = Sourcelogo::getImageByUrl($url);
        }
        $article['code'] = self::generateCode();
        $article['filename'] = $article['title'] . $article['code'];

        return $article;
    }

    public static function getSummaryByUrl($url) {
        $response = file_get_contents('https://sandbox.aylien.com/textapi/summarize?language=en&sentences_number=8&url=' . $url);
        return [join(' ', json_decode($response)->sentences), json_decode($response)->sentences[0]];
    }

    public static function getDomain($url) {
        $host = parse_url($url, PHP_URL_HOST);

        if(filter_var($host,FILTER_VALIDATE_IP)) {
            // IP address returned as domain
            return $host; //* or replace with null if you don't want an IP back
        }

        $domain_array = explode(".", str_replace('www.', '', $host));
        $count = count($domain_array);
        if( $count>=3 && strlen($domain_array[$count-2])==2 ) {
            // SLD (example.co.uk)
            return implode('.', array_splice($domain_array, $count-3,3));
        } else if( $count>=2 ) {
            // TLD (example.com)
            return implode('.', array_splice($domain_array, $count-2,2));
        }
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
