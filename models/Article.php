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

    public function generateCode()
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
