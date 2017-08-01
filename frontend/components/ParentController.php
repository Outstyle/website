<?php
namespace frontend\components;

use Yii;
use yii\web\Controller;
use common\models\User;

class ParentController extends Controller
{
    public function init()
    {
        parent::init();

        /* Запись времени последнего посещения в БД при триггере любого контроллера */
        /* TODO: Make local 5 minutes checktime to prevent DB trigerring every time on page refresh */
        if (Yii::$app->user->id) {
            $userOnline = User::findOne(Yii::$app->user->id);
            $userOnline->lastvisit = time();
            $userOnline->save();
        }
    }
}
