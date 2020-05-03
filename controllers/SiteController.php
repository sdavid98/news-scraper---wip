<?php

namespace app\controllers;

use app\models\Article;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    public $topicName = 'main';

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
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
    public function actionIndex()
    {
        return $this->render('index', [
            'model' => Article::find()
                ->select('article.*, topic.*')
                ->leftJoin('topic', 'topic.id = article.topic_id')
                ->orderBy('created DESC')
                ->all(),
            'displayConfig' => [
                'showTopic' => true
            ]
        ]);
    }

    /**
     * @return string
     */
    public function actionComputers()
    {
        $this->topicName = 'computers';

        return $this->render('index', [
            'model' => Article::find()
                ->select('article.*, topic.*')
                ->leftJoin('topic', 'topic.id = article.topic_id')
                ->where(['article.topic_id' => '2'])
                ->orderBy('created DESC')
                ->all()
        ]);
    }

    /**
     * @param $id
     * @return string
     */
    public function actionArticle($id)
    {
        return $this->render('article/'.$id, ['model' => ['id' => $id]]);
    }


    /**
     * @return string
     */
    public function actionGenerate()
    {

        if (Yii::$app->request->post('link')) {
            $url = Yii::$app->request->post('link');
            //$createdArticleData = Article::createArticleDataFromUrl($url);
            $articleId = Article::saveArticleFromGeneratedData($url);

            return $this->render('generate', ['model' => ['id' => $articleId]]);
        }
        return $this->render('generate', ['model' => ['id' => 'GENERATE']]);
    }


    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
