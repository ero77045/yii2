<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Heading */

$this->title = 'Create Heading';
$this->params['breadcrumbs'][] = ['label' => 'Headings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="heading-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
