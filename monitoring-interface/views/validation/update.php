<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ValidationRule */

$this->title = 'Update Validation Rule: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Validation Rules', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="validation-rule-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
