<?php
/**
 * User profile block view
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
use common\components\helpers\ElementsHelper;

/* @see @frontend/widgets/UserProfileBlock for vars */
/* @var $user */

echo Html::tag('div',

    # Widget settings button
    ElementsHelper::widgetButton().

    # User timestamp
    Html::tag('div',
      $user->userLastVisitTimestamp,
      [
        'class' => 'user__onlinestatus',
      ]
    ).

    # User avatar
    Html::tag('div',
      Html::img($user->userAvatarPath, ['class' => 'roundborder']),
      [
        'class' => 'u-letter-box--medium user__avatar',
      ]
    ).

    # User nickname
    Html::tag('div',
      Html::tag('span', $user->userNickname),
      [
        'class' => 'user__nickname',
      ]
    ).

    # User name
    Html::tag('div',
      $user->userName.' '.$user->userLastName,
      [
        'class' => 'user__name',
      ]
    ).

    # User birthday
    Html::tag('div',
      $user->userBirthdayDate ?
        Html::tag('span', $user->labels['birthday'].':', ['class' => 'o-grid__cell']).
        Html::tag('span', $user->userBirthdayDate, ['class' => 'o-grid__cell'])
        : '',
      [
        'class' => 'o-grid o-grid--no-gutter ta-l user__birthday',
      ]
    ).

    # User sex
    Html::tag('div',
      $user->userSex ?
        Html::tag('span', $user->labels['sex'].':', ['class' => 'o-grid__cell']).
        Html::tag('span', $user->userSex, ['class' => 'o-grid__cell'])
        : '',
      [
        'class' => 'o-grid o-grid--no-gutter ta-l user__sex',
      ]
    ).

    # User culture
    Html::tag('div',
      $user->userCulture ?
        Html::tag('span', $user->labels['culture'].':', ['class' => 'o-grid__cell']).
        Html::tag('span', $user->userCulture, ['class' => 'o-grid__cell'])
        : '',
      [
        'class' => 'o-grid o-grid--no-gutter ta-l user__culture',
      ]
    ).

    # User country
    Html::tag('div',
      $user->userCountry ?
        Html::tag('span', $user->labels['country'].':', ['class' => 'o-grid__cell']).
        Html::tag('span', $user->userCountry, ['class' => 'o-grid__cell'])
        : '',
      [
        'class' => 'o-grid o-grid--no-gutter ta-l user__country',
      ]
    ).

    # User city
    Html::tag('div',
      $user->userCity ?
        Html::tag('span', $user->labels['city'].':', ['class' => 'o-grid__cell']).
        Html::tag('span', $user->userCity, ['class' => 'o-grid__cell'])
        : '',
      [
        'class' => 'o-grid o-grid--no-gutter ta-l user__city',
      ]
    ).

    # User team
    Html::tag('div',
      $user->userTeam ?
        Html::tag('span', $user->labels['team'].':', ['class' => 'o-grid__cell']).
        Html::tag('span', $user->userTeam, ['class' => 'o-grid__cell'])
        : '',
      [
        'class' => 'o-grid o-grid--no-gutter ta-l user__team',
      ]
    ).

    # User phone
    Html::tag('div',
      $user->userPhone ?
        Html::tag('span', $user->labels['phone'].':', ['class' => 'o-grid__cell']).
        Html::tag('span', $user->userPhone, ['class' => 'o-grid__cell'])
        : '',
      [
        'class' => 'o-grid o-grid--no-gutter ta-l user__phone',
      ]
    ).

    # User skype
    Html::tag('div',
      $user->userSkype ?
        Html::tag('span', $user->labels['skype'].':', ['class' => 'o-grid__cell']).
        Html::tag('span', $user->userSkype, ['class' => 'o-grid__cell'])
        : '',
      [
        'class' => 'o-grid o-grid--no-gutter ta-l user__skype',
      ]
    ),

[
  'class' => 'user__info u-window-box--medium'
]);

# SEPARATOR
echo ElementsHelper::separatorWidget(2, 'bottomborder');
