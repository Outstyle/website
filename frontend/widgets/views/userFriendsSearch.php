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

/* Working with friends model (grid), if atleast one user is found */
echo Html::beginTag('div', ['id' => 'friendsList']);
  echo Html::beginTag('div', ['class' => 'search__friends']);
    echo Html::beginTag('div', ['class' => 'o-grid o-grid--wrap o-grid--no-gutter']);

        /* Counter is needed for loadmore triggers */
        $friendsTotal = count($friends['active']);
        $friendsCounter = 0;

        foreach ($friends['active'] as $friend) {

            /* Load more trigger */
            $friendsCounter++;
            if ($friendsCounter === $friendsTotal && empty($_POST['search'])) {
                echo Html::tag('div', '', [
                'id' => 'friends_loadmore',
                'ic-action' => 'trigger:loadMoreFriends',
                'ic-trigger-on' => 'scrolled-into-view'
              ]);
            }

            /* Friend single row */
            echo Html::beginTag('div', [
              'id' => 'friendbox-'.$friend['id'],
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
              ['class' => 'o-grid__cell--width-70']
            );

            if ($friend['friendship_status'] === 'pending') {
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
                      'ic-include' => '{"friendId":'.$friend['id'].'}',
                      'ic-post-to' => Url::toRoute(['api/friends/refuse']),
                      'ic-indicator' => ElementsHelper::DEFAULT_AJAX_LOADER,
                      'ic-push-url' => 'false'
                    ]
                  ).

                  /* Friendship accept */
                  Html::button(
                    Yii::t('app', 'To friends'),
                    [
                      'id' =>'friend__action-accept',
                      'class' => 'c-button c-button--success c-button--xsmall i-acceptfriend u-pull-right',
                      'title' => Yii::t('app', 'To friends'),
                      'ic-include' => '{"friendId":'.$friend['id'].'}',
                      'ic-post-to' => Url::toRoute(['api/friends/accept']),
                      'ic-indicator' => ElementsHelper::DEFAULT_AJAX_LOADER,
                      'ic-push-url' => 'false'
                    ]
                  ),

                  ['class' => 'friend__actions']),
                ['class' => 'o-grid__cell--width-30 o-grid__cell--center']);
            }

            /* For friends actual search mode only */
            if (Yii::$app->request->pathInfo === 'friends/search' ||
                Yii::$app->request->pathInfo === 'api/friends/find') {
                echo Html::tag('div',
                  Html::tag('div',

                    /* Friend search options button ... */
                    Html::button(
                      Html::tag('i', '', [
                        'class' => "zmdi zmdi-more zmdi-hc-lg",
                      ]),
                    [
                      'class' => 'zmdi-icon--hoverable i-postbutton i-postbutton--right friend__options',
                      'title' => Yii::t('app', 'Options'),
                      'ic-action' => 'friendShowOptionsTooltip:'.$friend['id']
                    ]),

                  ['class' => 'u-window-box--small']),
                ['class' => 'o-grid__cell--width-30']);
            }

            echo Html::endTag('div');
            echo Html::endTag('div');
        }

    echo Html::endTag('div');
  echo Html::endTag('div');
echo Html::endTag('div');
