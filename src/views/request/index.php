<?php

use app\models\Manager;
use app\models\Request;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\RequestSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Заявки';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="request-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить заявку', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'created_at:datetime',
            //'updated_at:datetime',
            'email:email',
            'phone',
            [
                'attribute' => 'manager_id',
                'filter' => Manager::getList(),
                'value' => function (Request $request) {
                    return $request->manager ? $request->manager->name : null;
                }
            ],
            [
                'attribute' => 'previousRequest',
                'format' => 'raw',
                'value' => function ($model) {
                    $duplicates = Request::find()->where(['email' => $model->email])
                        ->orWhere(['phone' => $model->phone]);
                    $duplicates = $duplicates->andFilterWhere(['not', ['id' => $model->id]])
                        ->andFilterWhere(['<', 'created_at', $model->created_at])
                        ->orderBy(['id' => SORT_DESC])->one();
                    if ($duplicates && (strtotime($model->created_at) - strtotime($duplicates->created_at)) < 2592000) {
                        return Html::a('№ ' . $duplicates->id,
                            'view?id=' . $duplicates->id);
                    }
                    else return '---';
                }
            ],
            [
                'class' => yii\grid\ActionColumn::class,
                'template' => '{view}',
                'buttons' => [
                    'view' => function ($url) {
                        return Html::a('Просмотр', $url, [
                            'class' => 'btn btn-primary',
                        ]);
                    },
                ],
                'contentOptions' => ['style' => 'width:1px'],
            ],
            [
                'class' => yii\grid\ActionColumn::class,
                'template' => '{update}',
                'buttons' => [
                    'update' => function ($url) {
                        return Html::a('Редактировать', $url, [
                            'class' => 'btn btn-primary',
                        ]);
                    },
                ],
                'contentOptions' => ['style' => 'width:1px'],
            ],
        ],
    ]); ?>

</div>
