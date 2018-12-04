<?php
/**
 * @link https://github.com/Outstyle/website
 * @copyright Copyright (c) 2018 Outstyle Network
 * @license Beerware
 */

namespace frontend\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%dialog_members}}".
 * This model serves as a frontend one and should always extend common `DialogMembers` model.
 *
 * Only custom methods are stored here.
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 * @since 1.0
 */
class DialogMembers extends \common\models\DialogMembers
{
    /**
     * Retrieves all members by dialogue ID
     * @param  int      $dialogId
     * @return array
     */
    public static function getDialogMembersById(int $dialogId = 0) : array
    {
        return self::find()
            ->where('dialog = :dialog', [':dialog' => $dialogId])
            ->with(['userDescription' => function ($query) {
                $query->select('id, name, last_name, nickname, avatar')->with('userAvatar');
            }])
            ->limit(self::$dialogMembersLimit)
            ->asArray()
            ->all();
    }

    /**
     * Adds new user into dialogue
     * @param int    $dialogId    Dialogue ID
     * @param int    $userId      User ID
     */
    public static function addMemberToDialog(int $dialogId = 0, int $userId = 0) : bool
    {
        $model = new DialogMembers();
        $model->user = $userId;
        $model->dialog = $dialogId;
        if ($model->validate()) {
            $model->save();
            return true;
        }
        return false;
    }

    /**
     * Check if user belongsto certain dialog
     * @param int    $userId      User ID
     * @param int    $dialogId    Dialogue ID
     */
    public static function isDialogMember(int $userId = 0, int $dialogId = 0) : bool
    {
        $dialogMember = self::find()
            ->where('dialog = :dialog AND user = :user', [
                ':dialog' => $dialogId,
                ':user' => $userId])
            ->one();

        if ($dialogMember) {
            return true;
        }

        return false;
    }

    /**
     * Organize dialog members data in array to be ready for use in views
     * @param  array          $dialogMembers    self::getDialogMembersById()
     * @return array
     */
    public static function setupData(array $dialogMembers = []) : array
    {
        foreach ($dialogMembers as $id => $member) {
            $dialogMembers[$id]['userDescription']['userAvatar']['path'] =
            (isset($member['userDescription']['userAvatar']['img'])) ? UserAvatar::getAvatarPath($member['userDescription']['userAvatar']['img'], '150x150_', $member['userDescription']['userAvatar']['service_id']) :
            UserAvatar::getAvatarPath();
        }

        /* Reindexing for direct data query by 'userId' key */
        $dialogMembers = ArrayHelper::index($dialogMembers, 'user');

        return $dialogMembers;
    }
}
