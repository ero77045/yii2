<?php

use leandrogehlen\treegrid\TreeGrid;
use yii\helpers\Html;
use yii\grid\GridView;
/* * *ext** */


/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\search\HeadingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Heading';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tree-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php
            echo Html::a('Создать корневой элемент', ['add'], ['class' => 'btn btn-success']);
        ?>
    </p>
    <?=
    TreeGrid::widget([
        'dataProvider' => $dataProvider,
        'keyColumnName' => 'id',
        'showOnEmpty' => FALSE,
        'parentColumnName' => 'parent_id',
        'columns' => [

            'name',
            ['class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete} {add}',
                'buttons' => [
                    'add' => function ($url, $model, $key)
                    {
                        return Html::a('<span class="glyphicon glyphicon-plus"></span>', $url);
                    },
                ]
            ]
        ]
    ]);
    ?>

</div>