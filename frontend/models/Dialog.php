<?php
/**
 * @link https://github.com/Outstyle/website
 * @copyright Copyright (c) 2018 Outstyle Network
 * @license Beerware
 */

namespace frontend\models;

use Yii;

use frontend\models\DialogMembers;
use frontend\models\UserAvatar;
use frontend\models\UserNickname;
use frontend\models\Message;

/**
 * This is the model class for table "{{%dialog}}".
 * This model serves as a frontend one and should always extend common `Dialog` model.
 *
 * Only custom methods are stored here.
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 * @since 1.0
 */
class Dialog extends \common\models\Dialog
{
    /**
     * Retrieve all user dialogues by user ID
     * @param  int          $userId
     * @return instanceof   yii\db\ActiveQuery
     */
    public static function getByUserId(int $userId = 0) : yii\db\ActiveQuery
    {
        return DialogMembers::find()
            ->where(['user' => $userId])
            ->with(['dialog']);
    }

    /**
     * Prepare dialogs and organize data in array to be ready for use in views
     * @param  array          $dialogs  Dialogs array
     * @return array
     */
    public static function setupData(array $dialogs = []) : array
    {
        foreach ($dialogs as $dialogKey => $dialog) {
            $dialogId = $dialog['dialog']['id'];
            $dialogName = $dialogs[$dialogKey]['dialog']['name'];
            $dialogMembers = $dialogs[$dialogKey]['dialog']['members'] = DialogMembers::getDialogMembersById($dialogId);
            $dialogLastMessage = Message::getByDialogId($dialogId)->orderBy('id DESC')->one();

            foreach ($dialogMembers as $memberKey => $member) {
                $dialogs[$dialogKey]['dialog']['members'][$memberKey]['userDescription']['userAvatar']['path'] = UserAvatar::getAvatarPath($member['userDescription']['userAvatar']['img'], '150x150_', $member['userDescription']['userAvatar']['service_id']);
            }

            /* If dialog has only one member (straight 1x1 chat), dialog name will be username */
            if (!$dialogName && count($dialogMembers) == 2) {
                $dialogs[$dialogKey]['dialog']['name'] = UserNickname::composeFullName($dialogMembers[1]['userDescription']);
            }

            /* Get last message from any dialog to be shown in dialogs list */


            /* Predefine array keys for last message and time of last message
               If no last message is found, time must be dialog creation date */
            $dialogs[$dialogKey]['message']['last'] = '';
            $dialogs[$dialogKey]['message']['time'] = strtotime($dialogs[$dialogKey]['dialog']['created']);

            /* If dialog has any messages */
            if (isset($dialogLastMessage['message'])) {
                $dialogs[$dialogKey]['message']['last'] = $dialogLastMessage['message'];
                $dialogs[$dialogKey]['message']['time'] = strtotime($dialogLastMessage['created']);
            }
        }

        return $dialogs;
    }
}
