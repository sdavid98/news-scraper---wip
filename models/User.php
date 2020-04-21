<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $email
 * @property string $password
 * @property string|null $created
 * @property int|null $newsletter
 *
 * @property Article[] $articles
 * @property Comment[] $comments
 * @property Userlike[] $userlikes
 * @property Usersaved[] $usersaveds
 * @property Usertopic[] $usertopics
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email', 'password'], 'required'],
            [['created'], 'safe'],
            [['newsletter'], 'integer'],
            [['email'], 'string', 'max' => 70],
            [['password'], 'string', 'max' => 80],
            [['email'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'Email',
            'password' => 'Password',
            'created' => 'Created',
            'newsletter' => 'Newsletter',
        ];
    }

    /**
     * Gets query for [[Articles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getArticles()
    {
        return $this->hasMany(Article::className(), ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Comments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comment::className(), ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Userlikes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserlikes()
    {
        return $this->hasMany(Userlike::className(), ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Usersaveds]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsersaveds()
    {
        return $this->hasMany(Usersaved::className(), ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Usertopics]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsertopics()
    {
        return $this->hasMany(Usertopic::className(), ['user_id' => 'id']);
    }
}
