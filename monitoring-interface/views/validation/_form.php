<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ValidationRule */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="validation-rule-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'rule')/*->textarea(['rows' => 6])*/ ->widget(
        'trntv\aceeditor\AceEditor',
        [
            'mode'=>'html', // programing language mode. Default "html"
            'theme'=>'github', // editor theme. Default "github"
            'readOnly'=>'false' // Read-only mode on/off = true/false. Default "false"
        ]
    ) ?>

    <?= $form->field($model, 'active')->checkBox() ?>

    <?= $form->field($model, 'positive_evaluation')->checkBox() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
