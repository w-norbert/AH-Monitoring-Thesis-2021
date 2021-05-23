<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "visualization_graph_state".
 *
 * @property int $view_id
 * @property string $name
 * @property string $updated_at
 * @property string|null $data
 */
class VisualizationGraphState extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'visualization_graph_state';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('monitoring_db');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['data','name', 'updated_at'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'view_id' => 'View ID',
            'data' => 'Data',
        ];
    }

    public static function getViews()
    {
        return self::find()->select(['view_id','name'])->all();
    }
}
