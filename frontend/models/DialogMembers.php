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
     * @param int    $userId      User ID
     * @param int    $dialogId    Dialogue ID
     */
    public static function addMemberToDialog(int $userId = 0, int $dialogId = 0) : bool
    {
        $model = new DialogMembers();
        $model->user = $userId;
        $model->dialog = $dialogId;
        if ($model->validate()) {
            return $model->save();
        }
        return false;
    }

    /**
     * Check if user belongs to certain dialog
     * @param int    $userId      User ID
     * @param int    $dialogId    Dialogue ID
     */
    public static function isDialogMember(int $userId = 0, int $dialogId = 0) : bool
    {
        $dialogMember = self::find()
            ->select('id')
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
     * Check if user is an owner of certain dialog
     * @param int    $userId      User ID
     * @param int    $dialogId    Dialogue ID
     */
    public static function isDialogOwner(int $userId = 0, int $dialogId = 0) : bool
    {
        $dialogOwner = self::find()
            ->where('dialog = :dialog AND user = :user AND is_owner = :is_owner', [
                ':dialog' => $dialogId,
                ':user' => $userId,
                ':is_owner' => 1])
            ->one();

        if ($dialogOwner) {
            return true;
        }

        return false;
    }

    /**
     * Check if user is an owner of certain dialog
     * @param int    $userId      User ID
     * @param int    $dialogId    Dialogue ID
     */
    public static function checkMemberForDialogOwner(int $userId = 0, array $dialogMembers = []) : bool
    {
        if ($dialogMembers[$userId]['is_owner'] == 1) {
            return true;
        }

        return false;
    }

    /**
     * Makes user an owner of dialog
     * @param int    $userId      User ID
     * @param int    $dialogId    Dialogue ID
     */
    public static function setDialogOwner(int $userId = 0, int $dialogId = 0) : bool
    {
        $dialog = self::find()
            ->where('dialog = :dialog AND user = :user', [
                ':dialog' => $dialogId,
                ':user' => $userId])
            ->one();

        if ($dialog) {
            $dialog->is_owner = 1;
            $dialog->update();
            return true;
        }

        return false;
    }

    /**
     * Checks if certain users already belonging to dialogue
     * @param  array        $usersIdArray
     * @return int          Dialogue ID if existing or 0 if not
     */
    public static function isAlreadyInDialog(array $usersIdArray = []) : int
    {
        /* Also including current user */
        $usersIdArray[] = Yii::$app->user->id;

        $existingDialogs = DialogMembers::find()
            ->where(['in', 'user', $usersIdArray])
            ->with(['dialog'])
            ->asArray()
            ->all();

        $existingDialogs = ArrayHelper::map($existingDialogs, 'id', 'user', 'dialog.id');
        foreach ($existingDialogs as $dialogId => $dialogMembers) {
            $result = count(array_diff($usersIdArray, $dialogMembers));

            /* If match found in current user's existing dialogs, we also need to refresh modified date to bring up this dialogue to the very top of user's dialogs list */
            if ($result === 0) {
                $existingDialog = Dialog::findOne($dialogId);
                $existingDialog->modified = date('Y-m-d h:i:s', strtotime('+3 hours')); /* FIXME: Needs rewrite to unixtimestamp */;
                if ($existingDialog->save()) {
                    return $dialogId;
                }
            }
        }

        return 0;
    }

    /**
     * Organize dialog members data in array to be ready for use in views
     * @param  array          $dialogMembers    self::getDialogMembersById()
     * @return array
     */
    public static function setupData(array $dialogMembers = []) : array
    {
        foreach ($dialogMembers as $id => $member) {
            $dialogMembers[$id]['fullname'] = UserNickname::composeFullName($member['userDescription']);
            $dialogMembers[$id]['userDescription']['userAvatar']['path'] =
            (isset($member['userDescription']['userAvatar']['img'])) ? UserAvatar::getAvatarPath($member['userDescription']['userAvatar']['img'], '150x150_', $member['userDescription']['userAvatar']['service_id']) :
            UserAvatar::getAvatarPath();
        }

        /* Reindexing for direct data query by 'userId' key */
        $dialogMembers = ArrayHelper::index($dialogMembers, 'user');

        return $dialogMembers;
    }
}
