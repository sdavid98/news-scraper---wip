<?php

namespace app\controllers;

use app\models\Article;
use app\models\Keyword;
use Yii;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
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
        $query = new Query();
        $query->select('article.*, topic.topic_title, sourcelogo.imagename, GROUP_CONCAT(keyword.keyword) as keywords')
            ->from('article')
            ->leftJoin('topic', 'topic.id = article.topic_id')
            ->leftJoin('sourcelogo', 'sourcelogo.id = article.sourcelogo_id')
            ->leftJoin('keyword', 'keyword.article_id = article.id')
            ->groupBy('article.id')
            ->orderBy('article.created DESC');
        return $this->render('index', [
            'model' => $query->all(),
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

    public function actionKeyword()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        //TODO: validation before passing value to query
        if (Yii::$app->request->post('keyword')) {
            return Keyword::getKeywordsForSearchByFirstLetters(Yii::$app->request->post('keyword'));
        }

        if (Yii::$app->request->get('keyword')) {
            $keywords = Keyword::getArticleIdsByKeyword(Yii::$app->request->get('keyword'));
            $ids = [];
            foreach ($keywords as $keyword) {
                array_push($ids, $keyword->article_id);
            }

            $query = new Query();
            $query->select('article.*, topic.topic_title, sourcelogo.imagename, GROUP_CONCAT(keyword.keyword) as keywords')
                ->from('article')
                ->leftJoin('topic', 'topic.id = article.topic_id')
                ->leftJoin('sourcelogo', 'sourcelogo.id = article.sourcelogo_id')
                ->leftJoin('keyword', 'keyword.article_id = article.id')
                ->where(['in', 'article.id', $ids])
                ->groupBy('article.id')
                ->orderBy('article.created DESC');
            return $query->all();
        }
        return false;
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
