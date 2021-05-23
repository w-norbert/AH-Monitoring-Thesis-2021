<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ValidationRule */

$this->title = 'Create Validation Rule';
$this->params['breadcrumbs'][] = ['label' => 'Validation Rules', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="validation-rule-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
