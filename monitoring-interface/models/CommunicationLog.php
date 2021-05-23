<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "communication_log".
 *
 * @property int $id
 * @property int $requester_id
 * @property string $http_method
 * @property string|null $provider_address
 * @property int|null $provider_port
 * @property string|null $service_uri
 * @property string|null $interface_name
 * @property string $created_at
 * @property string|null $uri_components
 *
 * @property System $requester
 */
class CommunicationLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'communication_log';
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
            [['requester_id', 'http_method'], 'required'],
            [['requester_id', 'provider_port'], 'integer'],
            [['http_method'], 'string'],
            [['created_at'], 'safe'],
            [['provider_address'], 'string', 'max' => 100],
            [['service_uri', 'interface_name', 'uri_components'], 'string', 'max' => 255],
            [['requester_id'], 'exist', 'skipOnError' => true, 'targetClass' => System::className(), 'targetAttribute' => ['requester_id' => 'id']],
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
            'http_method' => 'Http Method',
            'provider_address' => 'Provider Address',
            'provider_port' => 'Provider Port',
            'service_uri' => 'Service Uri',
            'interface_name' => 'Interface Name',
            'created_at' => 'Created At',
            'uri_components' => 'Uri Components',
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
}
