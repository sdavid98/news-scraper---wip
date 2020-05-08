<?php

namespace app\models;

use http\Url;
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

    public static function getTopicNameByUrl($url) {
        //TODO: make table based on iab taxanomy
        $response = file_get_contents('https://sandbox.aylien.com/textapi/classify/iab-qag?taxonomy=iab-qag&language=en&url=' . $url);
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

            $myTopic = 'others';
            break;

        }

        return $myTopic;
    }

    public static function getTopicNameByText($text) {
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'header'  => "Authorization: Token PZ9PZYoSx7l7\r\nContent-Type: application/json\r\n",
                'content' => '{"texts": ["'. $text .'"]}',
            ),
        );
        $context  = stream_context_create($options);
        $result = file_get_contents('https://api.uclassify.com/v1/uClassify/iab-taxonomy/classify', false, $context);

        $result =  json_decode($result, true);
        $classifications = $result[0]['classification'];
        $max = 0;
        $topic = '';
        foreach($classifications as $classification) {
            if ($classification['p'] > $max) {
                $max = $classification['p'] - 0;
                $topic = $classification['className'];
            }
        }

        return $topic;
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
