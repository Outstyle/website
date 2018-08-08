<?php
/**
 * User friends search block list representation
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
use yii\helpers\Json;
use common\components\helpers\ElementsHelper;

/**
 * @var array   $friends    @see: @frontend/widgets/UserFriendsBlock.php
 */

/* If no users are found at all */
if (!$friends['active']) {
    echo Html::tag('div',
      Html::tag('div',
        '<i class="zmdi zmdi-alert-triangle c-blue"></i> '.
        Yii::t('app', 'No users found matching your criteria...')),
      ['class' => 'search__friends search__friends--notfound']);
    return;
}

/* Working with friends model (grid), if atleast one user is found */
echo Html::beginTag('div', ['class' => 'search__friends']);
  echo Html::beginTag('div', ['class' => 'o-grid o-grid--wrap o-grid--no-gutter']);

      foreach ($friends['active'] as $friend) {
          echo Html::beginTag('div', [
            'class' => 'o-grid__cell--width-100 u-window-box--small friend__box friend__'.$friend['friendship_status']
          ]);
          echo Html::beginTag('div', ['class' => 'o-grid o-grid--wrap o-grid--no-gutter']);

          echo Html::tag('div',
            /* Friend image */
            ElementsHelper::linkElement('friend',
              Html::img($friend['avatar'], ['class' => 'o-image roundborder friend__avatar friend__avatar--small']),
            Url::to(['/id'.$friend['id']], true), false, $friend['fullname']).

            /* Friend info block */
            Html::tag('div',
              ElementsHelper::linkElement('friend', $friend['fullname'], Url::to(['/id'.$friend['id']], true)).
              '<br>'.$friend['location'].
              '<br>'.$friend['birthday_date'],
              ['class' => 'friend__info']),
            ['class' => 'o-grid__cell--width-50']
          );

          if ($friend['friendship_status'] == 'pending') {
              /* Friend actions block */
              echo
              Html::tag('div',
                Html::tag('div',

                /* Friendship disapprove */
                Html::button(
                  Yii::t('app', 'Hide'),
                  [
                    'id' =>'friend__action-keep',
                    'class' => 'c-button c-button--xsmall i-keepfriend u-pull-right',
                    'title' => Yii::t('app', 'Keep as subscriber'),
                    'ic-include' => '{"id":'.$friend['id'].'}',
                    'ic-post-to' => Url::toRoute(['api/friends/accept']),
                    'ic-indicator' => ElementsHelper::DEFAULT_AJAX_LOADER,
                    'ic-on-beforeSend2' => 'friendBeforeKeepFriend()',
                    'ic-on-complete2' => 'friendAfterKeepFriend()',
                    'ic-push-url' => 'false'
                  ]
                ).

                /* Friendship accept */
                Html::button(
                  Yii::t('app', 'Add to friends'),
                  [
                    'id' =>'friend__action-add',
                    'class' => 'c-button c-button--success c-button--xsmall i-addfriend u-pull-right',
                    'title' => Yii::t('app', 'Add to friends'),
                    'ic-include' => '{"friendId":'.$friend['id'].'}',
                    'ic-post-to' => Url::toRoute(['api/friends/accept']),
                    'ic-indicator' => ElementsHelper::DEFAULT_AJAX_LOADER,
                    'ic-on-beforeSend2' => 'friendBeforeAddFriend()',
                    'ic-on-complete2' => 'friendAfterAddFriend()',
                    'ic-push-url' => 'false',
                    'ic-target' => 'false'
                  ]
                ),

                ['class' => 'friend__actions']),
              ['class' => 'o-grid__cell--width-50 o-grid__cell--center']);
          }

          echo Html::endTag('div');
          echo Html::endTag('div');
      }

  echo Html::endTag('div');
echo Html::endTag('div');
