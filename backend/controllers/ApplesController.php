<?php

namespace backend\controllers;

use common\models\Apple;
use common\models\AppleSearch;
use Exception;
use yii\helpers\Json;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

class ApplesController extends Controller
{

    private const INDEX = ['apples/index'];

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex(): string
    {
        $searchModel = new AppleSearch();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $searchModel->search(Yii::$app->request->queryParams),
            'model' => new Apple(),
        ]);
    }

    public function actionGenerate()
    {
        Apple::generate();

        $this->redirect(self::INDEX);
    }

    public function actionFailToGround(int $id)
    {
        try {
            if (!$model = Apple::findOne(['id' => $id])) {
                throw new Exception('Not found id');
            }
            $model->failToGround();
            $this->redirect(self::INDEX);
        } catch (Exception $e) {
            return Json::encode($e->getMessage());
        }
    }

    public function actionEat(int $id, int $percent)
    {
        try {
            if (!$model = Apple::findOne(['id' => $id])) {
                throw new Exception('Not found id');
            }
            $model->eat($percent);
            $this->redirect(self::INDEX);
        } catch (Exception $e) {
            return Json::encode($e->getMessage());
        }
    }

    public function actionDelete(int $id)
    {
        try {
            if (!$model = Apple::findOne(['id' => $id])) {
                throw new Exception('Not found id');
            }
            $model->del();
            $this->redirect(self::INDEX);
        } catch (Exception $e) {
            return Json::encode($e->getMessage());
        }
    }
}
