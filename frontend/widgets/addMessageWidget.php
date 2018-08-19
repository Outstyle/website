<?php

namespace frontend\widgets;

use frontend\models\MessageAnswer;


class addMessageWidget extends \yii\bootstrap\Widget {

    public $user = null;
    public $idDOM = null;

    public function init(){
        parent::init();
    }

    public function run(){
        $model = new MessageAnswer();
        return $this->render('addMessageWidget', [
            'model' => $model,
            'user' => $this->user,
            'idDOM' => $this->idDOM,
        ]);
    }
}