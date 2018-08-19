<?php
namespace frontend\models;

use Yii;
use common\models\User;
use yii\helpers\Url;
use yii\base\Model;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $repeatPassword;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username' => Yii::t('app', 'Login'),
            'password' => Yii::t('app', 'Password'),
            'repeatPassword' => Yii::t('app', 'Repeat password'),
            'email' => Yii::t('app', 'Email'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required'],
            ['username', 'string', 'min' => 2, 'max' => 64],
            ['username', 'match', 'pattern' => '/^[a-zA-Z0-9_\-]*$/i',
             'message' => Yii::t('app', 'You can only use alpanumeric symbols along with - and _')],

            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => '\common\models\User',
             'message' => Yii::t('app', 'This email address is already taken')],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],

            ['repeatPassword', 'required'],
            ['repeatPassword', 'string', 'min' => 6],
            ['repeatPassword', 'compare', 'compareAttribute'=>'password', 'skipOnEmpty' => false,
             'message'=>Yii::t('app', 'Password mismatch')],
        ];
    }

    /**
     * Signs user up, if validation is passed
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if ($this->validate()) {
            $user = new User();
            $user->username = $this->username;
            $user->email = $this->email;
            $user->setPassword($this->password);
            $user->generateAuthKey();

            // If signup process is successfull, returning user id.
            if ($user->save()) {
                return $user->id;
            }
        }

        return null;
    }
}
