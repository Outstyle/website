<?php

namespace frontend\widgets;

use Yii;
use yii\base\Widget;
use common\components\helpers\CryptoHelper;
use app\models\Video;
use app\models\VideoServices;

/**
 * Handles User -> Videos block, showing videos of user
 * Part of Outstyle network
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 *
 * @version 1.0
 *
 * @link https://github.com/Outstyle/website
 * @license Beerware
 */
class UserVideosBlock extends Widget
{

  /**
   * User videos array
   * @var array
   */
    public $videos = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $videos = [];

        # Hashing video URLs for later use
        if (isset($this->videos)) {
            foreach ($this->videos as $k => $video) {
                $videos[$k] = $video;
                $videos[$k]['hash'] = CryptoHelper::hash($video['id']);
                $videos[$k]['service_id'] = VideoServices::getVideoServiceNameByServiceId($videos[$k]['service_id']);
            }
            $this->videos = $videos;
        }
    }


    /**
     * @inheritdoc
     */
    public function run()
    {
        # Checking if we have any videos. If not - returning null
        if (!$this->videos) {
            return;
        }

        return $this->render('userVideosBlock', [
          'videos' => $this->videos,
        ]);
    }
}
