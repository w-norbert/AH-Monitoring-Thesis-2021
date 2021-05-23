<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "orchestration_log_items".
 *
 * @property int $id
 * @property int $requester_id
 * @property int $provider_id
 * @property int $service_id
 * @property int $interface_id
 * @property int $orchestration_log_id
 *
 * @property ServiceInterface $interface
 * @property OrchestrationLog $orchestrationLog
 * @property System $provider
 * @property System $requester
 * @property ServiceDefinition $service
 */
class OrchestrationLogItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'orchestration_log_items';
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
            [['requester_id', 'provider_id', 'service_id', 'interface_id', 'orchestration_log_id'], 'required'],
            [['requester_id', 'provider_id', 'service_id', 'interface_id', 'orchestration_log_id'], 'integer'],
            [['interface_id'], 'exist', 'skipOnError' => true, 'targetClass' => ServiceInterface::className(), 'targetAttribute' => ['interface_id' => 'id']],
            [['orchestration_log_id'], 'exist', 'skipOnError' => true, 'targetClass' => OrchestrationLog::className(), 'targetAttribute' => ['orchestration_log_id' => 'id']],
            [['provider_id'], 'exist', 'skipOnError' => true, 'targetClass' => System::className(), 'targetAttribute' => ['provider_id' => 'id']],
            [['requester_id'], 'exist', 'skipOnError' => true, 'targetClass' => System::className(), 'targetAttribute' => ['requester_id' => 'id']],
            [['service_id'], 'exist', 'skipOnError' => true, 'targetClass' => ServiceDefinition::className(), 'targetAttribute' => ['service_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'requester_id' => 'Requester ID',
            'provider_id' => 'Provider ID',
            'service_id' => 'Service ID',
            'interface_id' => 'Interface ID',
            'orchestration_log_id' => 'Orchestration Log ID',
        ];
    }

    /**
     * Gets query for [[Interface]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInterface()
    {
        return $this->hasOne(ServiceInterface::className(), ['id' => 'interface_id']);
    }

    /**
     * Gets query for [[OrchestrationLog]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrchestrationLog()
    {
        return $this->hasOne(OrchestrationLog::className(), ['id' => 'orchestration_log_id']);
    }

    /**
     * Gets query for [[Provider]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProvider()
    {
        return $this->hasOne(System::className(), ['id' => 'provider_id']);
    }

    /**
     * Gets query for [[Requester]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRequester()
    {
        return $this->hasOne(System::className(), ['id' => 'requester_id']);
    }

    /**
     * Gets query for [[Service]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getService()
    {
        return $this->hasOne(ServiceDefinition::className(), ['id' => 'service_id']);
    }
}
