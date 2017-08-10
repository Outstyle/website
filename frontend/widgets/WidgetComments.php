<?php

namespace frontend\widgets;

use Yii;
use yii\base\Widget;
use app\models\Comments;

/**
 * Handles comment form
 * Part of Outstyle network
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 *
 * @version 1.0
 *
 * @link https://github.com/Outstyle/website
 * @license Beerware
 */
class WidgetComments extends Widget
{

    /**
     * Element ID for comments to be shown
     * @var int
     */
    public $elem_id = 0;

    /**
     * Comments array
     * @var array
     */
    public $comments = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $comments = Comments::getComments([
          'elem_type' => Yii::$app->controller->id,
          'elem_id' => $this->elem_id
        ]);

        $this->comments = $comments;
    }


    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render('widgetComments', [
            'comments' => (Yii::$app->controller->id != 'board') ? $this->comments : '',
            'elem_id' => $this->elem_id
        ]);
    }
}
