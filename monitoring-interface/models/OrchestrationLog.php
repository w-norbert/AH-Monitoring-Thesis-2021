<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "orchestration_log".
 *
 * @property int $id
 * @property string $created_at
 *
 * @property OrchestrationLogItem[] $orchestrationLogItems
 */
class OrchestrationLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'orchestration_log';
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
            [['created_at'], 'required'],
            [['created_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[OrchestrationLogItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrchestrationLogItems()
    {
        return $this->hasMany(OrchestrationLogItem::className(), ['orchestration_log_id' => 'id']);
    }
}
