<?php
/**
 * @link https://github.com/Outstyle/website
 * @copyright Copyright (c) 2018 Outstyle Network
 * @license Beerware
 */

namespace common\models;

use Yii;
use common\components\helpers\StringHelper;

/**
 * This is the model class for table "{{%dialog}}".
 * This model serves as a common one, both for backend and frontend.
 *
 * Only general yii2 built-in methods should be used here!
 * If you want to add some modifications or new methods, you should extend from this model.
 * @see: frontend/models/Dialog.php
 * @see: backend/models/Dialog.php
 *
 * Also, all the relations with other models should be declared in this common model.
 *
 * @property int      $id
 * @property string   $created      TIMESTAMP
 * @property string   $modified     TIMESTAMP
 * @property string   $name
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 * @since 1.0
 */
class Dialog extends \yii\db\ActiveRecord
{

    /**
     * @var $dialogPageSize  How much dialogs per request to get
     */
    public static $dialogPageSize = 25;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%dialog}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                ['name'],
                'default',
                'value' => ''
            ],
            [
                ['name'],
                'string',
                'max' => 40
            ],
            [
                ['name'],
                'filter',
                'filter' => function ($name) {
                    return StringHelper::clearString($name);
                },
            ],
            [
                ['created', 'modified'],
                'safe'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'created' => Yii::t('app', 'Created'),
            'name' => Yii::t('app', 'Name'),
        ];
    }


    /* RELATIONS */
    public function getDialogMembers()
    {
        return $this->hasMany(DialogMembers::className(), ['dialog' => 'id']);
    }

    public function getMessage()
    {
        return $this->hasMany(Message::className(), ['dialog' => 'id']);
    }
}
