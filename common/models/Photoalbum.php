<?php
/**
 * @link https://github.com/Outstyle/website
 * @copyright Copyright (c) 2018 Outstyle Network
 * @license Beerware
 */
namespace common\models;

use Yii;

use common\components\helpers\ModelHelper;
use common\components\helpers\StringHelper;
use common\components\helpers\PrivacyHelper;

/**
 * This is the model class for table "{{%photoalbum}}".
 * This model serves as a common one, both for backend and frontend.
 *
 * Only general yii2 built-in methods should be used here!
 * If you want to add some modifications or new methods, you should extend from this model.
 * @see: frontend/models/Photoalbum.php
 * @see: backend/models/Photoalbum.php
 *
 * Also, all the relations with other models should be declared in this common model.
 *
 * @property int $id
 * @property int $user
 * @property string $name
 * @property string $url
 * @property string $text
 * @property string $created
 * @property int $privacy
 * @property int $privacy_comments
 * @property int $cover
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 * @since 1.0
 */
class Photoalbum extends \yii\db\ActiveRecord
{

    /**
     * Photoalbums limit per user
     * @var int
     */
    public $photoalbumsLimit = 100;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%photoalbum}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
              ['text'],
              'default', 'value' => ''
            ],
            [
              ['name'],
              'default', 'value' => date("Y-m-d H:i:s"),
            ],
            [
              ['name', 'text'],
              'match', 'pattern' => '/^[а-яА-ЯЁё\w\-.,!\?:;\s]+$/ui',
              'message' => Yii::t('app', 'This field can only contain letters and numbers'),
            ],
            [
              ['url'],
              'default', 'value' => function ($model, $attribute) {
                  return StringHelper::slugify($model->name);
              }
            ],
            [
              ['user'],
              'default', 'value' => Yii::$app->user->id
            ],
            [
              ['user'],
              'required'
            ],
            [
              ['user', 'privacy', 'privacy_comments', 'cover'],
              'integer'
            ],
            [
              ['privacy', 'privacy_comments'],
              'in', 'range' => PrivacyHelper::getPrivacyArrayKeys()
            ],
            [
              ['name'],
              'string',
              'message' => '',
              'tooLong' => Yii::t('app', 'Album name can not be longer than 32 symbols'),
              'tooShort' => Yii::t('app', 'Album name can not be shorter than 1 symbol'),
              'min' => 1,
              'max' => 32
            ],
            [
              ['photoalbumsLimit'],
              'default', 'value' => $this->photoalbumsLimit
            ],
            [
              ['photoalbumsLimit'], 'checkPhotoalbumsLimitForUser',
              'skipOnEmpty' => false,
              'skipOnError' => false
            ],
            [
              ['created'],
              'default', 'value' => date("Y-m-d H:i:s")
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user' => Yii::t('app', 'User'),
            'name' => Yii::t('app', 'Album title'),
            'url' => Yii::t('app', 'Url'),
            'text' => Yii::t('app', 'Description'),
            'created' => Yii::t('app', 'Created'),
            'privacy' => Yii::t('app', 'Who can view this album?'),
            'privacy_comments' => Yii::t('app', 'Who can comment photos in album?'),
            'cover' => Yii::t('app', 'Cover'),
        ];
    }

    /**
     * Creates new photoalbum for another model.
     * TODO: Move this function to backend? This also should be in controller?
     *
     * @param string $controllerId Another related controller ID (required)
     * @param int    $modelId      Another related model object (required)
     *
     * @return int $photoalbumId    Created album ID on success, false (0) on failure
     */
    public function createFor($controllerId = '', $modelId = 0)
    {
        /* Getting model obj to populate photoalbum */
        $model = ModelHelper::findBy($controllerId, $modelId);

        $this->name = isset($model->title) ? $model->title : Yii::t('app', 'Photoalbum description');
        $this->url = isset($model->url) ? $model->url : StringHelper::slugify($this->name);

        if ($this->validate() && $model->validate()) {
            $this->save();

            /* Setting photoalbum ID for related model */
            $model->album = $this->id;
            $model->save();
        }
    }

    /**
     * Custom validation rule: checks photoalbums limit for active user
     * NOTE: If there will be more validators, make it as a separate component
     * @return array
     */
    public function checkPhotoalbumsLimitForUser()
    {
        $photoalbumsCount = self::find()->where(['user' => $this->user])->count();
        if ($photoalbumsCount >= $this->photoalbumsLimit) {
            $this->addError('photoalbumsLimit',
              Yii::t('app', 'Photoalbum limit reached {photoalbum_current} out of {photoalbum_limit}', [
                'photoalbum_current' => $photoalbumsCount,
                'photoalbum_limit' => $this->photoalbumsLimit,
              ])
            );
        }
    }

    /**
     * Relations
     */
    public function getPhoto()
    {
        return $this->hasMany(Photo::className(), ['album' => 'id']);
    }

    public function getSchool()
    {
        return $this->hasMany(School::className(), ['album' => 'id']);
    }
}
