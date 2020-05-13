<?php
/**
 * @link https://github.com/Outstyle/website
 * @license Beerware
 */
namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;


use console\components\Faker\Nickname as FakerNicknameProvider;
use console\components\Faker\Person as FakerPersonProvider;

use common\components\helpers\StringHelper;
use common\components\helpers\CURLHelper;

/**
 * Database seeder
 * @link https://stackoverflow.com/questions/34996056/how-to-seed-database-in-yii2
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 * @version 1.0
 */
class SeedController extends Controller
{
    /**
     * Parameters
     * @var int
     */
    public $count;
    public $user;
    public $user_sex;


    /**
     * Faker object
     * @link https://github.com/fzaninotto/Faker
     * @var obj
     */
    private $faker;

    /**
     * Table for console
     * @link https://www.yiiframework.com/doc/api/2.0/yii-console-widgets-table
     * @var obj
     */
    protected $table;
    protected $tableData;


    /*
     * @inheritdoc
     */
    public function options($actionID)
    {
        return [
          'count',
          'user',
          'user_sex'
        ];
    }

    /*
     * @inheritdoc
     */
    public function optionAliases()
    {
        return [
          'c' => 'count',
          'u' => 'user',
          'sex' => 'user_sex'
        ];
    }

    public function init()
    {
        /**
         * Faker instance
         * @var obj
         */
        $this->faker = \Faker\Factory::create('ru_RU');

        /**
         * Person provider
         *
         * Usage:
         * $provider->firstUserName('female')
         * $provider->lastUserName('male')
         *
         * @link https://github.com/fzaninotto/Faker#fakerprovideren_usperson
         * @var obj
         */
        $this->faker->addProvider(new FakerPersonProvider($this->faker));

        /**
         * Nickname provider [CUSTOM]
         *
         * Usage:
         * $provider->maleNickname
         * $provider->femaleNickname
         *
         * @var obj
         */
        $this->faker->addProvider(new FakerNicknameProvider($this->faker));

        /**
         * Console table instance
         * @var obj
         */
        $this->table = new \yii\console\widgets\Table();
        $this->tableData = [];

        parent::init();
    }

    /**
     * Displays available commands
     * @return bool|message
     */
    public function actionIndex()
    {
        $this->stdout("Seeds a model with some test data. Examples:\n");
        $this->stdout("> seed/video -c=20\n");
        $this->stdout("> seed/users -c=20 -sex=[male|female]\n");
    }

    /**
     * Populate users table with -c=[int] amount of random users
     * @return bool|message
     */
    public function actionUsers()
    {
        $avatarsArray = $this->_prepareUserAvatarsArray($this->user_sex ?? 'male');

        $this->table->setHeaders(['Name', 'Nickname', 'Lastname', 'ID', 'Avatar']);

        for ($i=0;$i<$this->count;$i++) {

            /* Generating the data, using Faker */
            $data['firstname'] = $this->faker->firstUserName($this->user_sex);
            $data['nickname'] = $this->faker->nickname($this->user_sex);
            $data['lastname'] = $this->faker->lastUserName($this->user_sex);

            /* Generating some unique ID for later use */
            $uniqueId = StringHelper::slugify($data['firstname'].$data['nickname'].$data['lastname']);

            /* Filling up signup data */
            $signUp = new \frontend\models\SignupForm();
            $signUp->username = 'outstylebot_'.$uniqueId;
            $signUp->password = 'a1234567890z';
            $signUp->repeatPassword = 'a1234567890z';
            $signUp->email = $uniqueId.'@outstyle.org';

            /* If basic user signup process completed successfully, filling up user description */
            if ($userId = $signUp->signup()) {
                $userDescription = new \frontend\models\UserDescription();
                $userDescription->scenario = $userDescription::SCENARIO_DEFAULT;

                /**
                 * User ID
                 * @var int
                 */
                $userDescription->id = $userId;

                /**
                 * Basic user info: Name, Nickname, Lastname
                 * @var string
                 */
                $userDescription->name = $data['firstname'];
                $userDescription->nickname = $data['nickname'];
                $userDescription->last_name = $data['lastname'];

                /**
                 * User country
                 * 1 - Россия
                 * 2 - Украина
                 * @var int
                 */
                $userDescription->country = $this->faker->numberBetween(1, 2);

                /**
                 * User city
                 * 1 - Москва
                 * 2 - Санкт-Петербург
                 * 628 - Запорожье
                 * 223 - Донецк
                 * 280 - Харьков
                 * 427 - Херсон
                 * 2642 - Черкассы
                 * 1057 - Львов
                 * 314 - Киев
                 * 3170 - Ровно
                 * 292 - Одесса
                 * @var int
                 */
                if ($userDescription->country == 1) {
                    $userDescription->city = $this->faker->randomElement([1, 2]);
                }
                if ($userDescription->country == 2) {
                    $userDescription->city = $this->faker->randomElement([628,223,280,427,2642,1057,314,3170,292]);
                }

                /**
                 * @see UserDescription::familyList()
                 * @var int
                 */
                $userDescription->family = $this->faker->numberBetween(0, 8);

                /**
                 * Birthday date? Between? Not accurate.
                 * Example: 1982-12-26
                 * @var string
                 */
                $userDescription->birthday = $this->faker->date('Y-m-d', '-15 years');

                /**
                 * @see UserDescription::cultureList()
                 * @var int
                 */
                $userDescription->culture = $this->faker->numberBetween(0, 4);
                $userDescription->sex = $this->user_sex ?? 'male';


                /* Filling up user privacy */
                $userPrivacy = new \frontend\models\UserPrivacy();
                $userPrivacy->id = $userId;

                if ($userDescription->validate() && $userDescription->save() &&
                    $userPrivacy->validate() && $userPrivacy->save()) {
                      
                    /* Now when user is saved and added to DB, we can assign an avatar */
                    $currentUserAvatar = '';
                    if (isset($avatarsArray[$i])) {
                        $currentUserAvatar = $this->_uploadUserAvatar($userId, $avatarsArray[$i]);
                    }

                    /* Console output */
                    $this->tableData[] = [
                      $data['firstname'],
                      $data['nickname'],
                      $data['lastname'],
                      '/id'.$userId,
                      $currentUserAvatar
                    ];
                } else {
                    /* [ERROR][DEV] Debug UserDescription data in console */
                    var_dump($userDescription->save());
                    var_dump($userDescription->errors);
                    var_dump($userPrivacy->save());
                    var_dump($userPrivacy->errors);
                }
            } else {
                /* [ERROR][DEV] Debug basic user signup data in console */
                echo $uniqueId;
                var_dump($signUp->errors);
            }
        }

        echo $this->table->setrows($this->tableData)->run();
        return ExitCode::OK;
    }

    /**
     * [MISC] Prepares an array of images to work with
     * @param  string $sex    male|female
     * @return array
     */
    private function _prepareUserAvatarsArray($sex)
    {
        $directory = "D:\\_TMP\\img\\{$sex}\\";
        return glob($directory . "*.{jpg,jpeg,png}", GLOB_BRACE);
    }

    /**
     * [MISC] Upload an avatar for user
     * @param  int $userId      User ID
     * @param  string $image    Path to image
     * @return bool
     */
    private function _uploadUserAvatar($userId, $image)
    {
        $url = 'http://t2.local/api/avatar/upload';
        $file_name_with_full_path = curl_file_create($image);

        $postData = [
          'token' => Yii::$app->params['AllowedTokens']['outstyle_console'],
          'UserAvatar[img]' => $file_name_with_full_path,
          'user' => $userId
        ];

        return CURLHelper::postToURL($url, $postData);
    }
}
