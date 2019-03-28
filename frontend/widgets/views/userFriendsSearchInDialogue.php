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
use common\components\helpers\StringHelper;

/**
 * @var array   $friends    @see: @frontend/widgets/UserFriendsBlock.php
 */

/* Working with friends model (grid), if atleast one user is found */
/* ! --- FRIENDS GRID --- */
echo Html::beginTag('div', ['id' => 'friendsList']);
    echo Html::beginTag('div', ['class' => 'o-grid o-grid--wrap o-grid--no-gutter']);

        foreach ($friends['active'] as $friend) {

            /* ! FRIEND SINGLE ROW */
            echo Html::beginTag('div', [
              'id' => 'friendbox-'.$friend['id'],
              'class' => 'o-grid__cell--width-100 u-window-box--small friend__box friend__'.$friend['friendship_status']
            ]);
            echo Html::beginTag('div', ['class' => 'o-grid o-grid--wrap o-grid--no-gutter']);

            echo Html::tag('div',
                  /* ! FRIEND IMAGE */
                  ElementsHelper::linkElement('friend',
                    Html::img($friend['avatar'], ['class' => 'o-image u-pull-left roundborder friend__avatar friend__avatar--mini']),
                  Url::to(['/id'.$friend['id']], true), false, $friend['fullname']).

                  /* ! FRIEND INFO BLOCK */
                  Html::tag('div',
                    StringHelper::cutString(html_entity_decode($friend['fullname']), $stringLimit = 24, $preciseCut = true).
                    '<br><span>'.$friend['location'].'</span>',
                    ['class' => 'friend__info u-letter-box--small']),
                  ['class' => 'o-grid__cell--width-80']
                );

                /* ! --- ADD FRIEND INTO DIALOGUE AREA --- */
                echo Html::tag('div',
                  Html::tag('div',

                  /* ! FRIEND SELECT CHECKBOX */
                  Html::tag('label',
                    Html::input('checkbox', 'selected', $friend['id'], [
                        'class' => 'friends-form-trigger',
                        'ic-trigger-on' => 'click',
                    ]).
                    '<span class="circle circle--bright big"></span>'),

                  ['class' => 'u-window-box--small']),
                ['class' => 'o-grid__cell--width-20']);

            echo Html::endTag('div');
            echo Html::endTag('div');
        }

    echo Html::endTag('div');
echo Html::endTag('div');
