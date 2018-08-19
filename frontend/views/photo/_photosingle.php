<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\components\helpers\ElementsHelper;
use frontend\widgets\WidgetComments;
use frontend\models\VideoServices;

echo Html::tag('div',

  $this->render('_photocontainer', [
    'photo' => $photo ?? [],
  ]),

[
  'class' => 'o-grid__cell o-grid__cell--width-100'
]);

echo '1';
# Comments
echo WidgetComments::widget([
  'elem_id' => $photo['id']
]);
