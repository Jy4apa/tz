<?php

namespace app\controllers;

use app\models\Manager;
use Yii;
use app\models\Request;
use app\models\RequestSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use Exception;

class RequestController extends Controller
{
    public function actionIndex()
    {
        $searchModel = new RequestSearch();
        try {
            foreach (Yii::$app->request->queryParams as $key => $field) {
                $searchModel[$key] = $field;
            }
        }
        catch (Exception $e){
            Yii::$app->getSession()->setFlash('error', 'Ошибка! Не удалось заполнить поля модели из запроса!');
        }
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate()
    {
        $model = new Request();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            # Поиск дубля
            $duplicates = Request::find()->where(['email' => $model->email])
                ->orWhere(['phone' => $model->phone]);
            $duplicates = $duplicates->andFilterWhere(['not', ['id' => $model->id]])
                ->andFilterWhere(['<', 'created_at', $model->created_at])
                ->orderBy(['id' => SORT_DESC])->one();

            if ($duplicates)
            {
                if (Manager::findOne(['id' => $duplicates->manager_id])->is_works)
                    $model->manager_id = $duplicates->manager_id;
                else $model->manager_id = $this->getFreeManager();
            }
            else $model->manager_id = $this->getFreeManager();
            $model->save();

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    protected function findModel($id)
    {
        if (($model = Request::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function getFreeManager()
    {
        $managers = Manager::find()->where(['is_works' => true])->all();
        $workload = [];
        foreach ($managers as $manager)
            $workload[$manager->id] = $manager->getRequests()->count();
        asort($workload);
        return array_key_first($workload);
    }
}
