<?php

use common\models\Apple;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = Yii::t('app', 'Apples');
$this->params['breadcrumbs'][] = $this->title;

/* @var Apple $model */

?>

<?php

$form = ActiveForm::begin([
    'action' => ['apples/generate'],
]);
echo Html::submitButton('Сгенерировать', [
    'class' => 'btn btn-primary',
]);
$form->end();

?>
<?php
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        'color',
        'created_at',
        'dropped_at',
        [
            'attribute' => 'status',
            'value' => static fn($data) => Apple::$statuses[$data->status] ?? '',
        ],
        'size',
        [
            'class' => yii\grid\ActionColumn::class,
            'template' => '{fail-to-ground} {eat} {delete}',
            'buttons' => [
                'fail-to-ground' => static function ($url, $model) {
                    return ($model->status == Apple::STATUS_ON_TREE)
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
                    return ($model->status == Apple::STATUS_ON_GROUND && $model->size != 0)
                        ? Html::a(
                            'Съёсть 10%',
                            $url.'&percent=10',
                            [
                                'title' => 'Съёсть 10%',
                                'class' => 'btn btn-success btn-sm',
                            ])
                        : '';
                },
                'delete' => static function ($url, $model) {
                    if ($model->size == 0) {
                        return Html::a(
                            'Удалить',
                            $url,
                            [
                                'title' => 'Удалить',
                                'class' => 'btn btn-danger btn-sm',
                            ]);
                    }
                }
            ],
        ],
    ],
]); ?>