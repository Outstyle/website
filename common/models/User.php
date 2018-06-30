<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use common\models\user\UserDescription;

/* https://github.com/developeruz/yii2-db-rbac */
use developeruz\db_rbac\interfaces\UserRbacInterface;

/**
 * This is the model class for table "{{%user}}".
 * TODO: Optimize this model by splitting it into separate models
 *
 * @property string $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $lastvisit
 */

class User extends ActiveRecord implements IdentityInterface, UserRbacInterface
{

    /**
     * User model entity statuses
     * @var integer
     */
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    /**
     * User social statuses (indicators)
     * @var integer
     */
    const USER_SOCIAL_OFFLINE = 0;
    const USER_SOCIAL_ONLINE = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public $newPass;
    public $newPass_repeat;
    public $newEmail;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            /* http://www.yiiframework.com/doc-2.0/yii-behaviors-timestampbehavior.html */
            TimestampBehavior::className(),
        ];
    }

    public function rules()
    {
        return [
            ['lastvisit', 'default', 'value' => 0],

            [['username', 'auth_key', 'password_hash','password_reset_token', 'newPass', 'newPass_repeat', 'newEmail'], 'filter', 'filter' => 'trim'],
            [['username', 'auth_key', 'password_hash', 'email'], 'required'],
            [['status'], 'integer'],
            [['username', 'password_reset_token', 'email'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['email', 'newEmail'], 'email'],

            [['newPass'], 'string', 'length' => [6, 255]],
            [['newPass_repeat'], 'string', 'length' => [6, 255]],

            [['password_reset_token'], 'safe'],
            [['password_hash'], 'string', 'length' => [6, 255]],

            [['username'], 'string', 'length' => [2, 30]],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
            ['email', 'unique', 'message' => Yii::t('app', 'Этот адрес электронной почты уже занят.')],
            ['username', 'match', 'pattern' => '/^[a-zA-Z0-9_\-]*$/i', 'message' => Yii::t('app', 'Допустимы только латинский буквы, цифры и знаки "-", "_"')],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'username' => Yii::t('app', 'Логин'),
            'auth_key' => Yii::t('app', 'Auth Key'),
            'password_hash' => Yii::t('app', 'Пароль'),
            'password_reset_token' => Yii::t('app', 'Password Reset Token'),

            'oldPass' => Yii::t('app', 'Старый пароль:'),
            'newPass' => Yii::t('app', 'Новый пароль:'),
            'newPass_repeat' => Yii::t('app', 'Повторите пароль:'),
            'newEmail' => Yii::t('app', 'Новый адрес:'),


            'email' => Yii::t('app', 'Email'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by email
     *
     * @param string $email
     * @return static|null
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /* RELATIONS */
    public function getUserdescription()
    {
        return $this->hasOne(UserDescription::className(), ['id' => 'id']);
    }

    public function getUserprivacy()
    {
        return $this->hasOne(UserPrivacy::className(), ['id' => 'id']);
    }

    public function getUserName()
    {
        return $this->username;
    }

    public static function usersSelect()
    {
        $model = self::find()->orderBy('username')->all();
        $r = [];
        foreach ($model as $v) {
            $r[$v->id] = $v->username;
        }
        return $r;
    }
}
