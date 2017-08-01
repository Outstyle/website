<?php

namespace frontend\widgets;

use Yii;
use yii\base\Widget;

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
     * Friends additional info, formed in this widget out of raw DB data
     * @var string
     */
    public $friendAvatarPath;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $friends = [];

        # Working with each friend data, setting additional info
        if (isset($this->friends)) {
            foreach ($this->friends as $k => $friend) {
                # Friend ID
                $id = $this->friends[$k]['id'];

                # Friend avatar path
                $friends[$id]['friendAvatarPath'] = \app\models\UserAvatar::getAvatarPath($id);

                # Friend name
                $friends[$id]['name'] = \app\models\UserNickname::getNickname($id);

                # Friend culture
                # $friends[$id]['culture'] = \app\models\UserDescription::getUserCultureByUserId($id);
            }
            $this->friends = $friends;
        }
    }


    /**
     * @inheritdoc
     */
    public function run()
    {
        # If user has no friends
        if (!$this->friends) {
            return;
        }

        return $this->render('userFriendsBlock', [
          'friends' => $this->friends,
        ]);
    }
}
