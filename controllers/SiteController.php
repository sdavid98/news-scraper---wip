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

    public function getTopicByUrl($url) {
        $response = file_get_contents('https://sandbox.aylien.com/textapi/classify/iab-qag?taxonomy=iab-qag&language=en&url=' . $url);
        $topic = array_values(json_decode($response)->categories)[0]->label;
        $myfile = fopen("../topic.txt", "w");
        fwrite($myfile, $topic);
        fclose($myfile);

        return $topic;
    }

    public function getSummaryByUrl($url) {
        $response = file_get_contents('https://sandbox.aylien.com/textapi/summarize?language=en&sentences_number=8&url=' . $url);
        $summary = join(' ', json_decode($response)->sentences);
        $myfile = fopen("../summary.txt", "w");
        fwrite($myfile, $summary);
        fclose($myfile);

        return $summary;
    }

    public function getKeywordsByUrl($url) {
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
        //got the title too
        $text = json_decode($response)->text;

        $context = stream_context_create(array(
            'http' => array(
                'method' => 'POST',
                'header' => "Content-Type: text/plain\r\n",
                'content' => $text
            )
        ));
        $response = file_get_contents('https://languages.cortical.io/rest/text/keywords?retina_name=en_general', FALSE, $context);

        $myfile = fopen("../kexwords.txt", "w");
        fwrite($myfile, join(', ', json_decode($response)));
        fclose($myfile);
    }

    function getDomain($url) {
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

    public function getSourceLogoByUrl($url) {
        $saveto = '../src/images/source-logos/logo.jpg';

        $ch = curl_init ('https://logo.clearbit.com/'.parse_url($url)['host']);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $raw=curl_exec($ch);
        curl_close ($ch);

        /*if(file_exists($saveto)){
            unlink($saveto);
        }*/
        $fp = fopen($saveto,'w+');
        fwrite($fp, $raw);
        fclose($fp);

        return true;
    }

    /**
     * @return string
     */
    public function actionGenerate()
    {

        if (Yii::$app->request->post('link')) {
            $url = Yii::$app->request->post('link');
            $createdArticleData = Article::createArticleDataFromUrl($url);

            return $this->render('generate', ['model' => $createdArticleData]);
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
