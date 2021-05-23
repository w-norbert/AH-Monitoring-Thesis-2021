<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "orchestration_connection".
 *
 * @property int $id
 * @property int $requester_id
 * @property int $provider_id
 * @property int $service_id
 * @property int $interface_id
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $terminated_at
 *
 * @property System $requester
 * @property ServiceDefinition $service
 * @property ServiceInterface $interface
 * @property System $provider
 */
class OrchestrationConnection extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'orchestration_connection';
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
            [['requester_id', 'provider_id', 'service_id', 'interface_id'], 'required'],
            [['requester_id', 'provider_id', 'service_id', 'interface_id'], 'integer'],
            [['created_at', 'updated_at', 'terminated_at'], 'safe'],
            [['requester_id', 'provider_id', 'service_id', 'interface_id'], 'unique', 'targetAttribute' => ['requester_id', 'provider_id', 'service_id', 'interface_id']],
            //[['requester_id'], 'exist', 'skipOnError' => true, 'targetClass' => System::className(), 'targetAttribute' => ['requester_id' => 'id']],
            //[['service_id'], 'exist', 'skipOnError' => true, 'targetClass' => ServiceDefinition::className(), 'targetAttribute' => ['service_id' => 'id']],
            //[['interface_id'], 'exist', 'skipOnError' => true, 'targetClass' => ServiceInterface::className(), 'targetAttribute' => ['interface_id' => 'id']],
            //[['provider_id'], 'exist', 'skipOnError' => true, 'targetClass' => System::className(), 'targetAttribute' => ['provider_id' => 'id']],
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
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'terminated_at' => 'Terminated At',
        ];
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
     * Gets query for [[Provider]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProvider()
    {
        return $this->hasOne(System::className(), ['id' => 'provider_id']);
    }
}
