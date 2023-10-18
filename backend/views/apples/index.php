<?php

use common\models\Apple;
use common\models\AppleSearch;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = Yii::t('app', 'Apples');
$this->params['breadcrumbs'][] = $this->title;

/* @var Apple $model */
$form = ActiveForm::begin([
    'action' => ['apples/generate'],
]);
echo Html::submitButton('Сгенерировать', [
    'class' => 'btn btn-primary',
]);
$form->end();
/** @var ActiveDataProvider $dataProvider */
/** @var AppleSearch $searchModel */
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        'color' => [
            'attribute' => 'color',
            'value' => static fn($data) => '<div style="width:30px;height:30px;background-color:' . $data->color . ';"></div>',
            'format' => 'raw',
        ],
        'created_at',
        'dropped_at',
        [
            'attribute' => 'status',
            'value' => static fn($data) => \common\domain\Apple::$statuses[$data->status] ?? '',
        ],
        'size',
        [
            'class' => yii\grid\ActionColumn::class,
            'template' => '{fail-to-ground} {eat} {delete}',
            'buttons' => [
                'fail-to-ground' => static function ($url, $model) {
                    return ($model->status == \common\domain\Apple::STATUS_ON_TREE)
                        ? Html::a(
                            'Упасть',
                            $url,
                            [
                                'title' => 'Упасть',
                                'class' => 'btn btn-warning btn-sm',
                            ])
                        : '';
                },
                'eat' => static function ($url, $model) {
                    return ($model->status == \common\domain\Apple::STATUS_ON_GROUND && $model->size != 0)
                        ? Html::a(
                            'Съёсть 10%',
                            $url . '&percent=10',
                            [
                                'title' => 'Съёсть 10%',
                                'class' => 'btn btn-success btn-sm',
                            ])
                        : '';
                },
                'delete' => static function ($url, $model) {
                    return ($model->size == 0 || $model->status == \common\domain\Apple::STATUS_ON_GROUND)
                        ? Html::a(
                            'Удалить',
                            $url,
                            [
                                'title' => 'Удалить',
                                'class' => 'btn btn-danger btn-sm',
                            ])
                        : '';
                }
            ],
        ],
    ],
]); ?>