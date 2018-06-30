<?php

namespace frontend\widgets;

use Yii;
use yii\base\Widget;

use app\models\UserAvatar;

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
     * User friends array
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

        # Working with friends and setting all the data for using in view
        if (isset($this->friends) && !empty($this->friends)) {
            foreach ($this->friends as $k => $friend) {
                $friends[$k] = $friend;

                $friends[$k]['fullname'] = $friend['name'].' &quot;'.$friend['nickname'].'&quot; '.$friend['last_name'];
                $friends[$k]['location'] = '';
                $friends[$k]['birthday_date'] = $friend['birthday'] ?? '';
                $friends[$k]['avatar'] = UserAvatar::getAvatarPath($friend['id']);

                if (isset($friend['geolocationCountries']) && isset($friend['geolocationCities'])) {
                    $friends[$k]['location'] = $friend['geolocationCountries']['name_ru'].', '.$friend['geolocationCities']['name'];
                }

                if (isset($friend['birthday'])) {
                    $friends[$k]['birthday_date'] = Yii::$app->formatter->asDate($friend['birthday'], Yii::$app->params['dateMini']);
                }
            }
            $this->friends = $friends;
        }

        # Default view for a widget
        if (!isset($this->options['view'])) {
            $this->options['view'] = 'userFriendsBlock';
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render($this->options['view'], [
          'friends' => $this->friends,
        ]);
    }
}
