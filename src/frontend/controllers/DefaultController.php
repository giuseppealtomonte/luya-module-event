<?php
namespace johnnymcweed\event\frontend\controllers;

use johnnymcweed\event\models\Cat;
use johnnymcweed\event\models\Event;
use luya\web\Controller;
use yii\data\ActiveDataProvider;

/**
 * Class DefaultController
 *
 * The default event controller handles the frontend actions
 *
 * @package app\modules\event\frontend\controllers
 * @author Alexander Schmid <alex.schmid@stud.unibas.ch>
 * @since 1.0.0
 */
class DefaultController extends Controller
{
    /**
     * Single Event
     *
     * @param $id
     * @param $title
     * @return string|\yii\web\Response
     */
    public function actionEvent($id, $title)
    {
        $model = Event::findOne(['id' => $id, 'is_deleted' => false]);

        if (!$model) {
            return $this->goHome();
        }
        return $this->render('event', [
            'model' => $model,
        ]);
    }

    /**
     * Multiple events
     *
     * Define through $type what kind of events you'd like to have
     *
     * @param string $listType
     * @return string
     */
    public function actionEvents($type = 'coming', $cat = '', $view = 'events' )
    {
        switch ($type) {
            case 'coming':
                $query = Event::find()
                    ->andWhere(['event_event.is_deleted' => false])
                    ->andWhere(['>=', 'event_event.event_start', time()]);
                $order = ['defaultOrder' => $this->module->comingEventDefaultOrder];
                $pagination = ['defaultPageSize' => $this->module->comingEventDefaultPageSize];
                break;
            case 'past':
                $query = Event::find()->andWhere(['event_event.is_deleted' => false])
                    ->andWhere(['<=', 'event_end', time()]);
                $order = ['defaultOrder' => $this->module->pastEventDefaultOrder];
                $pagination = ['defaultPageSize' => $this->module->pastEventDefaultPageSize];
                break;
            case 'current':
                $query = Event::find()->andWhere(['event_event.is_deleted' => false])
                    ->andWhere(['<=', 'event_event.event_start', time()])
                    ->andWhere(['>=', 'event_event.event_end', time()]);
                $order = ['defaultOrder' => $this->module->currentEventDefaultOrder];
                $pagination = ['defaultPageSize' => $this->module->currentEventDefaultPageSize];
                break;
            default:
                $query = Event::find();
                $order = ['defaultOrder' => $this->module->allEventDefaultOrder];
                $pagination = ['defaultPageSize' => $this->module->allEventDefaultPageSize];
                break;
        }

        if(!empty($cat)){
            $query->leftJoin('event_cat', 'event_event.cat_id = event_cat.id')
                ->andWhere(['LIKE', 'event_cat.title', $cat]);
        }
        $provider = new ActiveDataProvider([
            'query' => $query,
            'sort' => $order,
            'pagination' => $pagination
        ]);

        return $this->render($view, [
            'model' => Event::className(),
            'provider' => $provider
        ]);
    }

    /**
     * Single category
     *
     * @param $id
     * @param $title
     * @return string|\yii\web\Response
     */
    public function actionCategory($id, $title)
    {
        $model = Cat::findOne(['id' => $id, 'is_deleted' => false]);
        if (!$model) {
            return $this->goHome();
        }
        return $this->render('category', [
            'model' => $model,
        ]);
    }

    /**
     * Multiple categories
     */
    public function actionCategories()
    {
        $provider = new ActiveDataProvider([
            'query' => Cat::find()->andWhere(['is_deleted' => false]),
            'sort' => [
                'defaultOrder' => $this->module->categoriesDefaultOrder,
            ],
            'pagination' => [
                'defaultPageSize' => $this->module->categoriesDefaultPageSize,
            ],
        ]);
        return $this->render('categories', [
            'model' => Cat::className(),
            'provider' => $provider
        ]);
    }

}