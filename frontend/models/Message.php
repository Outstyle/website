<?php
/**
 * @link https://github.com/Outstyle/website
 * @copyright Copyright (c) 2018 Outstyle Network
 * @license Beerware
 */

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "{{%message}".
 * This model serves as a frontend one and should always extend common `Message` model.
 *
 * Only custom methods are stored here.
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 * @since 1.0
 */
class Message extends \common\models\Message
{
    /**
     * @inheritdoc
     * @see https://www.yiiframework.com/doc/api/2.0/yii-db-baseactiverecord#afterSave()-detail
     *
     * If more related DB tables for insertion are about to be added, use transactions
     * @see: https://stackoverflow.com/a/35225902
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $setUnread = MessageStatus::setUnread($this->dialog, $this->id);
        if ($setUnread) {
            Yii::$app->response->headers->add('X-IC-Trigger', '{"newMessageAdded":[]}');
        }
    }

    /**
     * Retrieve all user dialogues by user ID
     * @param  int          $dialogId
     * @return instanceof   yii\db\ActiveQuery
     */
    public static function getByDialogId(int $dialogId = 0) : yii\db\ActiveQuery
    {
        return self::find()->where(['dialog' => $dialogId]);
    }
}
