<?php

namespace app\controllers;

use app\models\SignupForm;

use app\models\SortForm;
use app\models\Statistics;
use mysqli;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
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
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */



    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Форма регистрации.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }
        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
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

    public function actionAdd_statistics()
    {
        $action = "add_statistics";

        //if (!Yii::$app->user->isGuest) {
        // $get_params = Yii::$app->request->queryParams;
        $cid = Yii::$app->request->get('cid');

        $campaign_id = Yii::$app->request->get('campaign_id');
        $event_name = Yii::$app->request->get('event');
        $time = Yii::$app->request->get('time');
        $sub1 = Yii::$app->request->get('sub1');

        $message_error = null;

        $model_save = new Statistics();
        $model_save->cid = $cid;
        $model_save->campaign_id = $campaign_id;
        $model_save->event = $event_name;
        $model_save->time = time();
        $model_save->sub1 = $sub1;

        switch ($event_name) {
            case 'click':
                // проверка уникального cid для click
                $model = Statistics::find()->where([
                    'cid' => $cid,
                    'event' => $event_name
                ])
                    ->one();
                if ($model) {
                    $message_error = "Уникальный cid для события 'click' уже существует.";
                }
                else {

                    $model_save->save();
                }
                break;
            case 'trial_started':
            case 'install':
                $model_save->save();
                break;
            default:
                $message_error = "Неверный запрос";
        }
        //  }
        // else{
        //   $message_error = "Добавление в БД под учетной записью гостя не разрешено!";
        //}

        return $this->render('index',
            [
                'message_error'=> $message_error,
                'action' => $action
            ]
        );
    }

    public function actionGet_statistics(){
        $action = "get_statistics";
        $model_camp_id = Statistics::find()->all();

        if (!Yii::$app->user->isGuest) {
            $mysqli = new mysqli("localhost", "root", "", "test_postback");
            $sort_form_model = new SortForm();
            $sort_form_model->group_at_campaign = 1;
            if($sort_form_model->load(Yii::$app->request->get())){
                if ($sort_form_model->group_at_campaign){
                    //  var_dump($sort_form_model->group_at_campaign);
                    if($sort_form_model->campaign_id && $sort_form_model->date1 && $sort_form_model->date2 ){
                        $res = $mysqli->query("SELECT campaign_id, SUM(IF(event = 'trial_started', 1, 0)) as trials, SUM(IF(event = 'click', 1, 0)) as clicks, SUM(IF(event = 'install', 1, 0)) as installs FROM statistics
                        WHERE time >=" . strtotime($sort_form_model->date1) . " AND time <=" . strtotime($sort_form_model->date2) . " AND campaign_id =" . $sort_form_model->campaign_id . " GROUP BY campaign_id");
                    }
                    else
                        if ($sort_form_model->campaign_id && (!$sort_form_model->date1 || !$sort_form_model->date2)){
                            $res = $mysqli->query("SELECT campaign_id, SUM(IF(event = 'trial_started', 1, 0)) as trials, SUM(IF(event = 'click', 1, 0)) as clicks, SUM(IF(event = 'install', 1, 0)) as installs FROM statistics WHERE campaign_id =   " . $sort_form_model->campaign_id );
                        }
                        else
                            if (!$sort_form_model->campaign_id && (!$sort_form_model->date1 || !$sort_form_model->date2)){
                                $res = $mysqli->query("SELECT campaign_id, SUM(IF(event = 'trial_started', 1, 0)) as trials, SUM(IF(event = 'click', 1, 0)) as clicks, SUM(IF(event = 'install', 1, 0)) as installs FROM statistics GROUP BY campaign_id");
                            }
                            else
                                if (!$sort_form_model->campaign_id && ($sort_form_model->date1 && $sort_form_model->date2)){
                                    $res = $mysqli->query("SELECT campaign_id, SUM(IF(event = 'trial_started', 1, 0)) as trials, SUM(IF(event = 'click', 1, 0)) as clicks, SUM(IF(event = 'install', 1, 0)) as installs FROM statistics
                                    WHERE time >=" . strtotime($sort_form_model->date1) . " AND time <=" . strtotime($sort_form_model->date2) . " GROUP BY campaign_id");
                                }
                }
                else{
                    if($sort_form_model->campaign_id && $sort_form_model->date1 && $sort_form_model->date2 ){
                        $res = $mysqli->query("SELECT campaign_id, SUM(IF(event = 'trial_started', 1, 0)) as trials, SUM(IF(event = 'click', 1, 0)) as clicks, SUM(IF(event = 'install', 1, 0)) as installs FROM statistics
                        WHERE time >=" . strtotime($sort_form_model->date1) . " AND time <=" . strtotime($sort_form_model->date2) . " AND campaign_id =" . $sort_form_model->campaign_id );
                    }
                    else
                        if ($sort_form_model->campaign_id && (!$sort_form_model->date1 || !$sort_form_model->date2)){
                            $res = $mysqli->query("SELECT * FROM statistics WHERE campaign_id =   " . $sort_form_model->campaign_id );
                        }
                        else
                            if (!$sort_form_model->campaign_id && (!$sort_form_model->date1 || !$sort_form_model->date2)){
                                $res = $mysqli->query("SELECT * FROM statistics ");
                            }
                            else
                                if (!$sort_form_model->campaign_id && ($sort_form_model->date1 && $sort_form_model->date2)){
                                    $res = $mysqli->query("SELECT * FROM statistics
                                    WHERE time >=" . strtotime($sort_form_model->date1) . " AND time <=" . strtotime($sort_form_model->date2) );
                                }
                }
                //echo (date('d.m.y H:i:s',1604743746));
                //echo strtotime($sort_form_model->date2);

            }
            else{
                $res = $mysqli->query("SELECT campaign_id, SUM(IF(event = 'trial_started', 1, 0)) as trials, SUM(IF(event = 'click', 1, 0)) as clicks, SUM(IF(event = 'install', 1, 0)) as installs FROM statistics  GROUP BY campaign_id");
            }
            // $sql = "SELECT campaign_id, SUM(IF(event = 'trial', 1, 0)) as trials, SUM(IF(event = 'click', 1, 0)) as clicks, SUM(IF(event = 'install', 1, 0)) as installs FROM statistics GROUP BY campaign_id";
            // $model = Statistics::findBySql($sql)->all();
        }
        else{
            $message_error = "Просмотр под учетной записью гостя не разрешен!";
        }

        return $this->render('index', compact('message_error', 'action', 'res', 'sort_form_model', 'model_camp_id'));
    }
}
