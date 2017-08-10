<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\components\helpers\ElementsHelper;

/**
* Single board post
*
* @author [SC]Smash3r <scsmash3r@gmail.com>
*
* @version 1.0
*
* @link https://github.com/Outstyle/website
* @license Beerware
**/

/* @see @frontend/widgets/UserBoardPost for vars */
/* @var $posts Post object with predefined variables */

foreach ($posts as $id => $post) {

  # POST WRAP
  echo Html::tag('div',

    # ------------ POST HEADER
    Html::tag('div',

      # POST USER AVATAR WRAP
      Html::tag('div',

        # AVATAR
        ElementsHelper::linkElement('friend',
          Html::img(
          $post['userAvatar'],
            [
              'alt' => $post['userNickname'],
              'class' => 'roundborder u-pull-left color-'.$post['userCulture'].'--border avatar avatar--big',
            ]
          ),
        Url::to(['/id'.$post['userId']], true), false, $post['userNickname']).

        # USER NAME + POST DATE
        Html::tag('div',
          ElementsHelper::linkElement('friend', $post['userNickname'], Url::to(['/id'.$post['userId']], true)).
          '<br>'.
          '<span class="post__date">'.$post['created'].'</span>',
          [
            'class' => 'post__author'
          ]
        ).

        # POST OPTIONS
        ElementsHelper::postSettingsButton().

        Html::tag('div', '', ['class' => 'clearfix']),

        [
          'class' => 'user-name--withavatar'
        ]
      ),

      [
        'class' => 'o-grid__cell o-grid__cell--width-100 u-window-box--xlarge post__header'
      ]
    ).


    # ------------ POST BODY
    Html::tag('div',
      $post['text'],
      [
        'class' => 'o-grid__cell o-grid__cell--width-100 u-pillar-box--xlarge post__body'
      ]
    ).


    # ------------ POST FOOTER
    Html::tag('div',

      # ACTIONS BOX (like, view count, comment buttons)
      Html::tag('div',
        Html::tag('div',
          ElementsHelper::likeButton(Yii::$app->controller->id, $id, $post['likesCount'], $post['myLike']).

          # COMMENT FORM SHOW LINK
          Html::a('<i class="zmdi zmdi-comment-text-alt"></i><span>'.Yii::t('app', 'Post comment').'</span></a>',
            'javascript:void(0)',
            [
              'class' => 'i-icon show-comment-form-link',
              'ic-include' => '{"elem_type":"'.Yii::$app->controller->id.'","elem_id":'.(int)$id.'}',
              'ic-target' => '#comments-'.$id,
              'ic-indicator' => "#outstyle_loader",
              'ic-get-from' => Url::toRoute(['comments/show']),
            ]
          ),

          [
            'class' => 'post__actions u-pull-left',
          ]
        ).

        # VIEWS COUNT
        Html::tag('div',
          ElementsHelper::linkElement('views', 0, false, 'zmdi-eye'),
          [
            'class' => 'u-pull-right',
          ]
        ).

        Html::tag('div', '', ['class' => 'clearfix']),
        [
          'class' => 'post__actionbox u-letter-box--medium'
        ]
      ),

      [
        'class' => 'o-grid__cell o-grid__cell--width-100 u-pillar-box--xlarge post__actions'
      ]
    ).

    # ------------ POST BOX FOR COMMENTS
    Html::tag('div',
      '',
      [
        'id' => 'comments-'.$id,
        'class' => 'o-grid__cell o-grid__cell--width-100 u-pillar-box--xlarge post__comments'
      ]
    ),

    [
      'class' => 'o-grid__cell o-grid__cell--width-100 o-grid__cell--no-gutter post'
    ]
  );
}
