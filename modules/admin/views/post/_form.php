<?php

use app\common\helpers\TreeRenderer;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Post */
/* @var $form yii\widgets\ActiveForm */
use leandrogehlen\treegrid\TreeGrid;

?>

<div class="post-form">

    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model,'heading_id[]',['template' => "{label}\n{hint}\n{error}"])->textInput();
    echo TreeRenderer::returnTree([
        'dataArray' => \app\models\Heading::find()->asArray()->all(),
        'checkbox' => true,
        'single' =>false,
        'title_field' => 'name',
        'parent_field' => 'parent_id',
        'model'=>$model,
        'attribute'=>'heading_id',
        'selectedAttr' => \yii\helpers\Json::encode($model->postHeadingsIds)
    ]);
    ?>
    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>



    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
