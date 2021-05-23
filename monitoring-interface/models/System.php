<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "system_".
 *
 * @property int $id
 * @property string $system_name
 * @property string $address
 * @property int $port
 * @property string|null $authentication_info
 * @property string $created_at
 * @property string $updated_at
 *
 * @property AuthorizationInterCloud[] $authorizationInterClouds
 * @property AuthorizationIntraCloud[] $authorizationIntraClouds
 * @property AuthorizationIntraCloud[] $authorizationIntraClouds0
 * @property CommunicationLog[] $communicationLogs
 * @property OrchestrationConnection[] $orchestrationConnections
 * @property OrchestrationConnection[] $orchestrationConnections0
 * @property OrchestrationLog[] $orchestrationLogs
 * @property OrchestrationLog[] $orchestrationLogs0
 * @property OrchestrationLogOld[] $orchestrationLogOlds
 * @property OrchestratorStore[] $orchestratorStores
 * @property QosIntraMeasurement[] $qosIntraMeasurements
 * @property QosReservation[] $qosReservations
 * @property ServiceDefinition[] $reservedServices
 * @property ServiceRegistry[] $serviceRegistries
 * @property ServiceDefinition[] $services
 * @property Subscription[] $subscriptions
 * @property EventType[] $eventTypes
 * @property SubscriptionPublisherConnection[] $subscriptionPublisherConnections
 * @property Subscription[] $subscriptions0
 * @property SystemRegistry[] $systemRegistries
 * @property Device[] $devices
 */
class System extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'system_';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('arrowhead_db');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['system_name', 'address', 'port'], 'required'],
            [['port'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['system_name', 'address'], 'string', 'max' => 255],
            [['authentication_info'], 'string', 'max' => 2047],
            [['system_name', 'address', 'port'], 'unique', 'targetAttribute' => ['system_name', 'address', 'port']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'system_name' => 'System Name',
            'address' => 'Address',
            'port' => 'Port',
            'authentication_info' => 'Authentication Info',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[AuthorizationInterClouds]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthorizationInterClouds()
    {
        return $this->hasMany(AuthorizationInterCloud::className(), ['provider_system_id' => 'id']);
    }

    /**
     * Gets query for [[AuthorizationIntraClouds]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthorizationIntraClouds()
    {
        return $this->hasMany(AuthorizationIntraCloud::className(), ['consumer_system_id' => 'id']);
    }

    /**
     * Gets query for [[AuthorizationIntraClouds0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthorizationIntraClouds0()
    {
        return $this->hasMany(AuthorizationIntraCloud::className(), ['provider_system_id' => 'id']);
    }

    /**
     * Gets query for [[CommunicationLogs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCommunicationLogs()
    {
        return $this->hasMany(CommunicationLog::className(), ['requester_id' => 'id']);
    }

    /**
     * Gets query for [[OrchestrationConnections]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrchestrationConnections()
    {
        return $this->hasMany(OrchestrationConnection::className(), ['requester_id' => 'id']);
    }

    /**
     * Gets query for [[OrchestrationConnections0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrchestrationConnections0()
    {
        return $this->hasMany(OrchestrationConnection::className(), ['provider_id' => 'id']);
    }

    /**
     * Gets query for [[OrchestrationLogs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrchestrationLogs()
    {
        return $this->hasMany(OrchestrationLog::className(), ['requester_id' => 'id']);
    }

    /**
     * Gets query for [[OrchestrationLogs0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrchestrationLogs0()
    {
        return $this->hasMany(OrchestrationLog::className(), ['provider_id' => 'id']);
    }

    /**
     * Gets query for [[OrchestrationLogOlds]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrchestrationLogOlds()
    {
        return $this->hasMany(OrchestrationLogOld::className(), ['provider_id' => 'id']);
    }

    /**
     * Gets query for [[OrchestratorStores]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrchestratorStores()
    {
        return $this->hasMany(OrchestratorStore::className(), ['consumer_system_id' => 'id']);
    }

    /**
     * Gets query for [[QosIntraMeasurements]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQosIntraMeasurements()
    {
        return $this->hasMany(QosIntraMeasurement::className(), ['system_id' => 'id']);
    }

    /**
     * Gets query for [[QosReservations]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQosReservations()
    {
        return $this->hasMany(QosReservation::className(), ['reserved_provider_id' => 'id']);
    }

    /**
     * Gets query for [[ReservedServices]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReservedServices()
    {
        return $this->hasMany(ServiceDefinition::className(), ['id' => 'reserved_service_id'])->viaTable('qos_reservation', ['reserved_provider_id' => 'id']);
    }

    /**
     * Gets query for [[ServiceRegistries]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getServiceRegistries()
    {
        return $this->hasMany(ServiceRegistry::className(), ['system_id' => 'id']);
    }

    /**
     * Gets query for [[Services]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getServices()
    {
        return $this->hasMany(ServiceDefinition::className(), ['id' => 'service_id'])->viaTable('service_registry', ['system_id' => 'id']);
    }

    /**
     * Gets query for [[Subscriptions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubscriptions()
    {
        return $this->hasMany(Subscription::className(), ['system_id' => 'id']);
    }

    /**
     * Gets query for [[EventTypes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEventTypes()
    {
        return $this->hasMany(EventType::className(), ['id' => 'event_type_id'])->viaTable('subscription', ['system_id' => 'id']);
    }

    /**
     * Gets query for [[SubscriptionPublisherConnections]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubscriptionPublisherConnections()
    {
        return $this->hasMany(SubscriptionPublisherConnection::className(), ['system_id' => 'id']);
    }

    /**
     * Gets query for [[Subscriptions0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubscriptions0()
    {
        return $this->hasMany(Subscription::className(), ['id' => 'subscription_id'])->viaTable('subscription_publisher_connection', ['system_id' => 'id']);
    }

    /**
     * Gets query for [[SystemRegistries]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSystemRegistries()
    {
        return $this->hasMany(SystemRegistry::className(), ['system_id' => 'id']);
    }

    /**
     * Gets query for [[Devices]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDevices()
    {
        return $this->hasMany(Device::className(), ['id' => 'device_id'])->viaTable('system_registry', ['system_id' => 'id']);
    }

    public static function getId($name, $address, $port) {
        $system = System::find()->where(['system_name'=>$name, 'address'=>$address, 'port'=>$port])->one();
        if(!$system) return -1;
        return $system->id;
    }
}
