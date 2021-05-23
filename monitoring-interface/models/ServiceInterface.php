<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "service_interface".
 *
 * @property int $id
 * @property string|null $interface_name
 * @property string $created_at
 * @property string $updated_at
 *
 * @property AuthorizationInterCloudInterfaceConnection[] $authorizationInterCloudInterfaceConnections
 * @property AuthorizationInterCloud[] $authorizationInterClouds
 * @property AuthorizationIntraCloudInterfaceConnection[] $authorizationIntraCloudInterfaceConnections
 * @property AuthorizationIntraCloud[] $authorizationIntraClouds
 * @property OrchestrationConnection[] $orchestrationConnections
 * @property OrchestrationLog[] $orchestrationLogs
 * @property OrchestratorStore[] $orchestratorStores
 * @property ServiceRegistryInterfaceConnection[] $serviceRegistryInterfaceConnections
 * @property ServiceRegistry[] $serviceRegistries
 */
class ServiceInterface extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'service_interface';
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
            [['created_at', 'updated_at'], 'safe'],
            [['interface_name'], 'string', 'max' => 255],
            [['interface_name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'interface_name' => 'Interface Name',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[AuthorizationInterCloudInterfaceConnections]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthorizationInterCloudInterfaceConnections()
    {
        return $this->hasMany(AuthorizationInterCloudInterfaceConnection::className(), ['interface_id' => 'id']);
    }

    /**
     * Gets query for [[AuthorizationInterClouds]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthorizationInterClouds()
    {
        return $this->hasMany(AuthorizationInterCloud::className(), ['id' => 'authorization_inter_cloud_id'])->viaTable('authorization_inter_cloud_interface_connection', ['interface_id' => 'id']);
    }

    /**
     * Gets query for [[AuthorizationIntraCloudInterfaceConnections]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthorizationIntraCloudInterfaceConnections()
    {
        return $this->hasMany(AuthorizationIntraCloudInterfaceConnection::className(), ['interface_id' => 'id']);
    }

    /**
     * Gets query for [[AuthorizationIntraClouds]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthorizationIntraClouds()
    {
        return $this->hasMany(AuthorizationIntraCloud::className(), ['id' => 'authorization_intra_cloud_id'])->viaTable('authorization_intra_cloud_interface_connection', ['interface_id' => 'id']);
    }

    /**
     * Gets query for [[OrchestrationConnections]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrchestrationConnections()
    {
        return $this->hasMany(OrchestrationConnection::className(), ['interface_id' => 'id']);
    }

    /**
     * Gets query for [[OrchestrationLogs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrchestrationLogs()
    {
        return $this->hasMany(OrchestrationLog::className(), ['interface_id' => 'id']);
    }

    /**
     * Gets query for [[OrchestratorStores]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrchestratorStores()
    {
        return $this->hasMany(OrchestratorStore::className(), ['service_interface_id' => 'id']);
    }

    /**
     * Gets query for [[ServiceRegistryInterfaceConnections]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getServiceRegistryInterfaceConnections()
    {
        return $this->hasMany(ServiceRegistryInterfaceConnection::className(), ['interface_id' => 'id']);
    }

    /**
     * Gets query for [[ServiceRegistries]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getServiceRegistries()
    {
        return $this->hasMany(ServiceRegistry::className(), ['id' => 'service_registry_id'])->viaTable('service_registry_interface_connection', ['interface_id' => 'id']);
    }

    public static function getId($name) {
        $interface = ServiceInterface::find()->where(['interface_name'=>$name])->one();
        if(!$interface) return -1;
        return $interface->id;
    }
}
