<?php
/**
 * @link https://github.com/Outstyle/website
 * @copyright Copyright (c) 2018 Outstyle Network
 * @license Beerware
 */

namespace common\models;

use Yii;
use common\models\user\UserDescription;
use frontend\models\user\UserAvatar;

/**
 * This is the model class for table "{{%dialog_members}}".
 * This model serves as a common one, both for backend and frontend.
 *
 * Only general yii2 built-in methods should be used here!
 * If you want to add some modifications or new methods, you should extend from this model.
 * @see frontend/models/DialogMembers.php
 * @see backend/models/DialogMembers.php
 *
 * Also, all the relations with other models should be declared in this common model.
 *
 * @property int $id
 * @property int $user
 * @property int $dialog
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 * @since 1.0
 */
class DialogMembers extends \yii\db\ActiveRecord
{
    /**
     * @var $friendsPageSize  How much friends per request to get
     */
    public static $dialogMembersLimit = 100;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%dialog_members}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user', 'dialog'], 'required'],
            [['user', 'dialog'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user' => Yii::t('app', 'User'),
            'dialog' => Yii::t('app', 'Dialog'),
        ];
    }


    /* RELATIONS */
    public function getDialog()
    {
        return $this->hasOne(Dialog::className(), ['id' => 'dialog']);
    }
    public function getMessage()
    {
        return $this->hasMany(Message::className(), ['dialog' => 'dialog'])
            ->limit(1)
            ->orderBy(['created' => SORT_DESC]);
    }
    public function getUserDescription()
    {
        return $this->hasOne(UserDescription::className(), ['id' => 'user']);
    }
}
