<?php

namespace app\controllers;

use app\models\Article;
use app\models\Keyword;
use app\models\Sourcelogo;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;

class ArticleController extends Controller
{
    public $topicName = 'main';

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex($id)
    {
        $model = [];
        $model['article'] = Article::find()
            ->select('article.*, topic.topic_title')
            ->leftJoin('topic', 'topic.id = article.topic_id')
            ->where(['article.filename' => $id])
            ->one();
        $model['keywords'] = Keyword::find()->select('keyword')->where(['article_id' => $model['article']->id])->all();
        $model['logo'] = Sourcelogo::find()->select('imagename')->where(['id' => $model['article']->sourcelogo_id])->one();

        return $this->render('article', ['model' => $model]);
    }

}
