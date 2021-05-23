<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "validation_rule".
 *
 * @property int $id
 * @property string $name
 * @property string $rule
 * @property int $positive_evaluation true = query must return result to be valid
 * @property int $active
 * @property int|null $fulfilled
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $last_validation
 */
class ValidationRule extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'validation_rule';
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
            [['name', 'rule'], 'required'],
            [['rule'], 'string'],
            [['positive_evaluation', 'active', 'fulfilled'], 'integer'],
            [['created_at', 'updated_at', 'last_validation'], 'safe'],
            [['name'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'rule' => 'Rule',
            'positive_evaluation' => 'Positive Evaluation',
            'active' => 'Active',
            'fulfilled' => 'Fulfilled',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'last_validation' => 'Last Validation',
        ];
    }
}
