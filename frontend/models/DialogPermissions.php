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
 * This model extends frontend "{{%dialog_members}}" model.
 * This model serves as a permission handler for dialogue members.
 * It should always extend frontend `DialogMembers` model.
 *
 * Only custom methods are stored here.
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 * @since 1.0
 */
class DialogPermissions extends DialogMembers
{
    /**
     * Checks if user is allowed to perform a certain action
     * @param  string  $actionId    Controller ID
     * @param  integer $userId      User ID
     * @return bool
     */
    public static function checkActionAccessByUserID(string $actionId = '', int $userId = 0) : bool
    {
        $dialogId = Yii::$app->request->post('dialog');

        if ($actionId === 'update' && $dialogId) {
            return self::isDialogOwner($userId, $dialogId);
        }

        return true;
    }
}
