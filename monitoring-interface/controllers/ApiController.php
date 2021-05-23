<?php

namespace app\controllers;

use app\models\AccessToken;
use app\models\CommunicationLog;
use app\models\OrchestrationConnection;
use app\models\OrchestrationLog;
use app\models\ValidationRule;
use app\models\VisualizationGraphState;
use Yii;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;
use app\models\System;
use app\models\ServiceInterface;
use app\models\OrchestrationLogItem;
use yii\web\ConflictHttpException;
use yii\web\HttpException;

class ApiController extends Controller
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
                        'actions' => ['logout', 'send-data'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                    'send-data' => ['post'],
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
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if ($action->id == 'send-data' ||
            $action->id == 'get-graph-data' ||
            $action->id == 'monitor-connection' ||
            $action->id == 'terminate-connection' ||
            $action->id == 'add-orchestration-log' ||
            $action->id == 'add-communication-log' ) {
            $this->enableCsrfValidation = false;
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        }

        return parent::beforeAction($action);
    }

    /**
     * This endpoint returns a graph view to display
     * @param int $id the id of the graph view to render
     */
    public function actionGetGraphData($id = 1) {
        $graphState = VisualizationGraphState::findOne($id);
        $updated_at = "-";
        $json = "{}";
        if($graphState != false) {
            $json = $graphState->data;
            $updated_at = $graphState->updated_at;
        }
        $jsonArray = json_decode($json,true);
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->data = ["updated_at"=> $updated_at,"graph"=>$jsonArray];
    }

    /**
     * This endpoint returns the active validation errors in the system
     */
    public function actionGetValidationErrors() {
        $rules = ValidationRule::find()
            ->select(['id', 'name', 'last_validation'])
            ->where(['active'=>1, 'fulfilled'=>0])
            ->all();
        $result = [];
        foreach ($rules as $rule) {
            $result[]=["id"=>$rule->id,"name"=>$rule->name];
        }
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->data = ['validation_errors'=>$result];
    }

    /**
     * This endpoint can be called to notify the framework of new connection
     * Required input: requester_name: Name of the consumer system
     *                 requester_address: Address of the consumer system
     *                 interface_name: Name of the interface used for the connection
     *                 provider_id: ID of the provider system
     *                 service_id: ID of the consumed service
     * @return bool
     * @throws ConflictHttpException
     * @throws Exception
     * @throws ForbiddenHttpException
     * @throws HttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionMonitorConnection()
    {
        $this->checkAuthentication();
        $log = Yii::$app->request->getBodyParams();
        if(empty($log)) throw new HttpException("Bad request",400);
        foreach($log as $logItem) {
            $requester_id = System::getId($logItem['requester_name'], $logItem['requester_address'], 
                $logItem['requester_port']);
            $interface_id = ServiceInterface::getId($logItem['interface_name']);
            if($requester_id ===-1 || $interface_id === -1) {
                throw new Exception("Cannot find requester or service");
            }
            $model = OrchestrationConnection::find()->where([
                'interface_id' => $interface_id,
                'requester_id' => $requester_id,
                'provider_id' => $logItem['provider_id'],
                'service_id' => $logItem['service_id'],
            ])->one();
            if(!$model) {
                $model = new OrchestrationConnection();
                $model->interface_id = $interface_id;
                $model->requester_id = $requester_id;
                $model->provider_id = $logItem['provider_id'];
                $model->service_id = $logItem['service_id'];
            }
            else {
                $model->updated_at = date("Y-m-d H:i:s");;
            }
            $model->terminated_at = null;
            if(!$model->save()) {
                $errors = !empty($model->getErrors())? implode(', ',$model->getErrorSummary(true)) : 'Error occurred';
                throw new ConflictHttpException($errors);
            }
        }
        return true;
    }

    /**
     * This endpoint can be called to notify the framework of a terminated connection
     * Required input: requester_name: Name of the consumer system
     *                 requester_address: Address of the consumer system
     *                 interface_name: Name of the interface used for the connection
     *                 provider_id: ID of the provider system
     *                 service_id: ID of the consumed service
     * @return bool
     * @throws ConflictHttpException
     * @throws Exception
     * @throws ForbiddenHttpException
     * @throws HttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionTerminateConnection()
    {
        $this->checkAuthentication();
        $log = Yii::$app->request->getBodyParams();
        if(empty($log)) throw new HttpException("Bad request",400);
        foreach($log as $logItem) {
            $requester_id = System::getId($logItem['requester_name'], $logItem['requester_address'],
                $logItem['requester_port']);
            $interface_id = ServiceInterface::getId($logItem['interface_name']);
            if($requester_id ===-1 || $interface_id === -1) {
                throw new Exception("Cannot find requester or service");
            }
            $model = OrchestrationConnection::find()->where([
                'interface_id' => $interface_id,
                'requester_id' => $requester_id,
                'provider_id' => $logItem['provider_id'],
                'service_id' => $logItem['service_id'],
            ])->one();
            if(!$model) {
                throw new yii\web\NotFoundHttpException("Cannot find the specified system", 404);
            }
            else {
                $model->terminated_at = date("Y-m-d H:i:s");
            }
            if(!$model->save()) {
                $errors = !empty($model->getErrors())? implode(', ',$model->getErrorSummary(true)) : 'Error occurred';
                throw new ConflictHttpException($errors);
            }
        }
        return true;
    }

    /**
     * This endpoint is used to create a HTTP communication log
     * Required input: requester_name: Name of the consumer system
     *                 requester_address: Address of the consumer system
     *                 http_method: HTTP verb of the query
     * Optional input:
     *                 uri_components: URI of the query
     *                 provider_address: Address of the producer system
     *                 provider_port: Port of the producer system
     *                 service_uri: URI of the service provided by producer
     *                 interface_name: Name of the interface used for the communication
     * @return bool
     * @throws Exception
     * @throws ForbiddenHttpException
     * @throws HttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionAddCommunicationLog()
    {
        $this->checkAuthentication();
        $log = Yii::$app->request->getBodyParams();
        if(empty($log)) throw new HttpException("Bad request",400);

        foreach($log as $logItem) {
            $requester_id = System::getId($logItem['requester_name'], $logItem['requester_address'],
                $logItem['requester_port']);
            if($requester_id ===-1) {
                throw new Exception("Cannot find requester");
            }
            $communication_log = new CommunicationLog();
            $communication_log->created_at =  date("Y-m-d H:i:s");
            $communication_log->requester_id = $requester_id;
            $communication_log->http_method = $logItem["http_method"];
            if(isset($logItem["uri_components"])) $communication_log->uri_components = $logItem["uri_components"];
            if(isset($logItem["provider_address"])) $communication_log->provider_address = $logItem["provider_address"];
            if(isset($logItem["provider_port"])) $communication_log->provider_port = $logItem["provider_port"];
            if(isset($logItem["service_uri"])) $communication_log->service_uri = $logItem["service_uri"];
            if(isset($logItem["interface_name"])) $communication_log->interface_name = $logItem["interface_name"];

            if(!$communication_log->save()) {
                $errors = !empty($communication_log->getErrors())? implode(', ',
                    $communication_log->getErrorSummary(true)) : 'Error occurred';
                throw new HttpException($errors);
            }
        }
        return true;
    }

    /**
     * This endpoint is used to create an orchestration log
     * Required input: requester_name: Name of the consumer system
     *                 requester_address: Address of the consumer system
     *                 interface_id: ID of the interface used for the connection
     *                 provider_id: ID of the provider system
     *                 service_id: ID of the consumed service
     * @return bool
     * @throws Exception
     * @throws ForbiddenHttpException
     * @throws HttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionAddOrchestrationLog() {
        $this->checkAuthentication();
        $log = Yii::$app->request->getBodyParams();
        if(empty($log)) throw new HttpException("Bad request",400);
        $orchestration_log = new OrchestrationLog();
        $orchestration_log->created_at =  date("Y-m-d H:i:s");
        if(!$orchestration_log->save()) throw new Exception("Cannot save orchestration log");

        foreach($log as $logItem) {
            $requester_id = System::getId($logItem['requester_name'], $logItem['requester_address'],
                $logItem['requester_port']);
            if($requester_id ===-1 ) {
                throw new Exception("Cannot find requester or service");
            }
            $model = new OrchestrationLogItem();
            $model->interface_id = $logItem['interface_id'];
            $model->requester_id = $requester_id;
            $model->provider_id = $logItem['provider_id'];
            $model->service_id = $logItem['service_id'];
            $model->orchestration_log_id = $orchestration_log->id;

            if(!$model->save()) {
                $errors = !empty($model->getErrors())? implode(', ',$model->getErrorSummary(true)) : 'Error occurred';
                throw new HttpException($errors);
            }
        }
        return true;
    }

    /**
     * This function checks the token inside the Authorization Header of the request
     * And returns true if the token is valid
     * @return bool True is the token valid
     * @throws ForbiddenHttpException Exception if the token is invalid
     */
    private function checkAuthentication() {
        $headers = Yii::$app->request->headers;
        $authorization  = $headers->get('Authorization');
        if(empty($authorization) || ! AccessToken::find()->where(['value'=>$authorization, 'active'=>1])->one()) {
            throw new ForbiddenHttpException("Invalid access token");
        }
        return true;
    }
}
