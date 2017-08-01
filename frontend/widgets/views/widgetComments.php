<?php
/**
 * Comments form (used at particular single pages)
 * Part of Outstyle network
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 *
 * @version 1.0
 *
 * @link https://github.com/Outstyle/website
 * @license Beerware
 */

use yii\helpers\Html;
use yii\helpers\Url;

/* @see @frontend/widgets/WidgetComments for vars */
/* @var $comments */
/* @var $elem_id */

echo Html::beginTag('div', [
      'id' => 'comments_section',
      'class' => 'u-full-width c-comments '.Yii::$app->controller->id.'__comments'
    ]
);

  # COMMENTS FORM
  if ($elem_id) {
      echo $this->render('../../views/comments/_form',
        ['modelElemId' => $elem_id]
      );
  }


  # COMMENTS LIST
  if ($comments) {
      echo $this->render('../../views/comments/_commentblock',
        ['modelComments' => $comments]
      );
  }

echo Html::endTag('div');

/* JS: @see js/outstyle.comments.js */
?>
<script>jQuery(document).ready(function(){commentsInit();});</script>
