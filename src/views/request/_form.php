<?php

use app\models\Manager;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Request */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="request-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

    <?php if ($model->id): ?>
    <fieldset disabled="disabled">
        <?php endif; ?>

        <?= $form->field($model, 'manager_id')->dropDownList(Manager::getList(), ['prompt' => '']) ?>

        <?php if ($model->id): ?>
    </fieldset>
    <?php endif; ?>

    <?= $form->field($model, 'text')->textarea(['rows' => 10]) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
