<?php

namespace frontend\widgets;

use Yii;
use yii\base\Widget;
use frontend\models\UserDescription;
use frontend\models\UserPrivacy;
use yii\helpers\ArrayHelper;
use common\components\helpers\StringHelper;

/**
 * Handles User -> Profile block, showing all the info about particular user
 * Part of Outstyle network
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 *
 * @version 1.0
 *
 * @link https://github.com/Outstyle/website
 * @license Beerware
 */
class UserProfileBlock extends Widget
{
    /**
     * Labels for user description, taken from UserDescription model
     * @see: common\models\user\UserDescription -> attributeLabels()
     * @var array
     */
    public $labels = [];

    /**
     * User data
     * @var array
     */
    public $user = [];

    /**
     * User batch info
     * @var string
     */
    public $userName;
    public $userLastName;
    public $userNickname;
    public $userCountry;
    public $userCity;
    public $userTeam;
    public $userSkype;
    public $userPhone;
    public $userAvatarPath;
    public $userSex;
    public $userFamily;
    public $userCulture;

    /**
     * User birthday date timestamp
     * @var date YYYY-MM-DD
     */
    public $userBirthdayDate;

    /**
     * Last user visit timestamp
     * @var timestamp
     */
    public $userLastVisitTimestamp;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        # Populating labels array from one of the models
        $userDescription = new UserDescription();
        $this->labels = $userDescription->attributeLabels();

        # User nickname
        if (isset($this->user->userDescription->nickname)) {
            $this->userNickname = $this->user->userDescription->nickname;
        }

        # User name
        if (isset($this->user->userDescription->name)) {
            $this->userName = $this->user->userDescription->name;
        }

        # User lastname
        if (isset($this->user->userDescription->last_name)) {
            $this->userLastName = $this->user->userDescription->last_name;
        }

        # Converting user last visit timestamp to a human readable string
        if (isset($this->user->lastvisit)) {
            $this->userLastVisitTimestamp = StringHelper::convertTimestampToHuman($this->user->lastvisit, '', Yii::$app->controller->id);
        }

        # User avatar
        if (isset($this->user->userDescription->avatar)) {
            $this->userAvatarPath = \frontend\models\UserAvatar::getAvatarPath($this->user->id);
        }

        # User birthday + Privacy check
        if (isset($this->user->userDescription->birthday)) {
            if (UserPrivacy::getPrivacy($this->user->userPrivacy->birthday, $this->user->id)) {
                $this->userBirthdayDate = Yii::$app->formatter->asDate($this->user->userDescription->birthday, Yii::$app->params['dateMini']);
            }
        }

        # User country
        if (isset($this->user->userDescription->country)) {
            $this->userCountry = $this->user->userDescription->country;
        }

        # User city
        if (isset($this->user->userDescription->city)) {
            $this->userCity = $this->user->userDescription->city;
        }

        # User team
        if (isset($this->user->userDescription->team)) {
            $this->userTeam = $this->user->userDescription->team;
        }

        # User skype
        if (isset($this->user->userDescription->skype)) {
            $this->userSkype = $this->user->userDescription->skype;
        }

        # User phone
        if (isset($this->user->userDescription->phone)) {
            $this->userPhone = $this->user->userDescription->phone;
        }

        # User sex
        if (isset($this->user->userDescription->sex)) {
            $this->userSex = $this->user->userDescription->sex;
        }

        # User culture
        if (isset($this->user->userDescription->culture)) {
            $this->userCulture = ArrayHelper::getValue(UserDescription::cultureList(), $this->user->userDescription->culture);
        }

        # User family
        if (isset($this->user->userDescription->family)) {
            $this->userFamily = $this->user->userDescription->family;
        }
    }


    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render('userProfileBlock', [
          'user' => $this,
        ]);
    }
}
