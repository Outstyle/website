<?php
/**
 * @link https://github.com/Outstyle/website
 * @license Beerware
 */
namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * Removes all seeded data from DB
 * Must extend seed controller in order to have all the vars available
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 * @version 1.0
 */
class UndoseedController extends SeedController
{
    public function init()
    {
        parent::init();
    }

    /**
     * Displays available commands
     * @return bool|message
     */
    public function actionIndex()
    {
        $this->stdout("Removes all seeded data from DB. Examples:\n");
        $this->stdout("> undoseed/video\n");
        $this->stdout("> undoseed/users\n");
    }

    /**
     * Delete all bot users data from all related tables
     * @return bool|message
     */
    public function actionUsers()
    {
        $this->table->setHeaders(['Username', 'ID']);

        $users = new \frontend\models\User();
        $query = $users->find()
          ->select('id, username')
          ->filterWhere(['LIKE', 'username', 'outstylebot_%', false])
          ->asArray()
          ->all();

        if (!$query) {
            return ExitCode::NOUSER;
        }

        foreach ($query as $userInfo) {
            if ($users->deleteUserByUserId($userInfo['id'])) {
                /* Console output */
                $this->tableData[] = [
                  $userInfo['username'],
                  '/id'.$userInfo['id']
                ];
            }
        }

        echo $this->table->setrows($this->tableData)->run();
        return ExitCode::OK;
    }

    /**
     * Delete all user videos by -u=[int] ID
     * @return bool|message
     */
    public function actionVideo()
    {
        return 0;
    }
}
