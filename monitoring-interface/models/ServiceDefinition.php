<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "service_definition".
 *
 * @property int $id
 * @property string $service_definition
 * @property string $created_at
 * @property string $updated_at
 *
 * @property AuthorizationInterCloud[] $authorizationInterClouds
 * @property AuthorizationIntraCloud[] $authorizationIntraClouds
 * @property OrchestrationConnection[] $orchestrationConnections
 * @property OrchestrationLogItems[] $orchestrationLogItems
 * @property OrchestrationLogOld[] $orchestrationLogOlds
 * @property OrchestratorStore[] $orchestratorStores
 * @property QosReservation[] $qosReservations
 * @property System[] $reservedProviders
 * @property ServiceRegistry[] $serviceRegistries
 * @property System[] $systems
 */
class ServiceDefinition extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'service_definition';
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
            [['service_definition'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['service_definition'], 'string', 'max' => 255],
            [['service_definition'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'service_definition' => 'Service Definition',
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
        return $this->hasMany(AuthorizationInterCloud::className(), ['service_id' => 'id']);
    }

    /**
     * Gets query for [[AuthorizationIntraClouds]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthorizationIntraClouds()
    {
        return $this->hasMany(AuthorizationIntraCloud::className(), ['service_id' => 'id']);
    }

    /**
     * Gets query for [[OrchestrationConnections]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrchestrationConnections()
    {
        return $this->hasMany(OrchestrationConnection::className(), ['service_id' => 'id']);
    }

    /**
     * Gets query for [[OrchestrationLogItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrchestrationLogItems()
    {
        return $this->hasMany(OrchestrationLogItems::className(), ['service_id' => 'id']);
    }

    /**
     * Gets query for [[OrchestrationLogOlds]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrchestrationLogOlds()
    {
        return $this->hasMany(OrchestrationLogOld::className(), ['service_id' => 'id']);
    }

    /**
     * Gets query for [[OrchestratorStores]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrchestratorStores()
    {
        return $this->hasMany(OrchestratorStore::className(), ['service_id' => 'id']);
    }

    /**
     * Gets query for [[QosReservations]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQosReservations()
    {
        return $this->hasMany(QosReservation::className(), ['reserved_service_id' => 'id']);
    }

    /**
     * Gets query for [[ReservedProviders]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReservedProviders()
    {
        return $this->hasMany(System::className(), ['id' => 'reserved_provider_id'])->viaTable('qos_reservation', ['reserved_service_id' => 'id']);
    }

    /**
     * Gets query for [[ServiceRegistries]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getServiceRegistries()
    {
        return $this->hasMany(ServiceRegistry::className(), ['service_id' => 'id']);
    }

    /**
     * Gets query for [[Systems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSystems()
    {
        return $this->hasMany(System::className(), ['id' => 'system_id'])->viaTable('service_registry', ['service_id' => 'id']);
    }
}
