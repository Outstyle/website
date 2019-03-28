<?php

namespace frontend\widgets;

use Yii;
use yii\base\Widget;

use frontend\models\UserAvatar;
use frontend\models\UserNickname;

/**
 * Handles User -> Friends block, showing friends of user
 * Part of Outstyle network
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 *
 * @version 1.0
 *
 * @link https://github.com/Outstyle/website
 * @license Beerware
 */
class UserFriendsBlock extends Widget
{
    /**
     * All friends array
     * @var array
     */
    public $friends = [];

    /**
     * Widget options
     * @var array
     */
    public $options = [];


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $friends = [];

        /* Working with active friends and setting all the data for using in view */
        if (isset($this->friends) && !empty($this->friends)) {
            foreach ($this->friends as $friendship_status => $friend_info) {
                foreach ($friend_info as $k => $friend) {
                    $friends[$k] = $friend;

                    $friends[$k]['fullname'] = UserNickname::composeFullName($friend);
                    $friends[$k]['location'] = '';
                    $friends[$k]['birthday_date'] = $friend['birthday'] ?? '';
                    $friends[$k]['avatar'] = UserAvatar::getAvatarPath($friend['userAvatar']['img'], '150x150_', $friend['userAvatar']['service_id']);

                    if (isset($friend['geolocationCountries']) && isset($friend['geolocationCities'])) {
                        $friends[$k]['location'] = $friend['geolocationCountries']['name_ru'].', '.$friend['geolocationCities']['name'];
                    }

                    if (isset($friend['birthday'])) {
                        $friends[$k]['birthday_date'] = Yii::$app->formatter->asDate($friend['birthday'], Yii::$app->params['dateMini']);
                    }

                    $friends[$k]['friendship_status'] = $friendship_status;
                }
            }
            $this->friends = $friends;
        }

        /* Default view for a widget */
        if (!isset($this->options['view'])) {
            $this->options['view'] = 'userFriendsBlock';
        }

        /* If friends are requested in dialogs area from loader */
        if (Yii::$app->request->post('ic-target-id') == 'friends_in_dialogs_content') {
            $this->options['view'] = 'userFriendsSearchInDialogue';
        }

        /* If friends are requested from searchbox input */
        if (Yii::$app->request->post('ic-element-id') == 'friends_in_dialogs_search') {
            $this->options['view'] = 'userFriendsSearchInDialogue';
        }

        /* If no friends found at all - setting another view */
        if (!$this->friends) {
            $this->options['view'] = 'userFriendsNotFound';
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render($this->options['view'], [
          'friends' => [
            'active' => $this->friends
          ],
        ]);
    }
}
