<?php

namespace frontend\widgets;

use Yii;
use yii\base\Widget;
use common\components\helpers\CryptoHelper;
use frontend\models\Video;
use frontend\models\VideoServices;

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
     * Widget options
     * @var array
     */
    public $options = [];

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
                $videos[$k]['hash'] = $video['id'];
                $videos[$k]['service_id'] = VideoServices::getVideoServiceNameByServiceId($video['service_id']);
                $videos[$k]['service_link'] = VideoServices::generateServiceLink($video['video_id'], $video['service_id']);
            }
            $this->videos = $videos;
        }

        # Working with default options
        if (!isset($this->options['titleTag'])) {
            $this->options['titleTag'] = 'h4';
        }

        if (!isset($this->options['cell_class'])) {
            $this->options['cell_class'] = '';
        }

        if (!isset($this->options['view'])) {
            $this->options['view'] = 'userVideosBlock';
        }

        # Widget button settings
        if (!isset($this->options['widgetButton']['action'])) {
            $this->options['widgetButton']['action'] = 'edit';
        }

        if (!isset($this->options['widgetButton']['position'])) {
            $this->options['widgetButton']['position'] = 'topright';
        }

        if (!isset($this->options['widgetButton']['size'])) {
            $this->options['widgetButton']['size'] = 'lg';
        }

        if (!isset($this->options['widgetButton']['indicator'])) {
            $this->options['widgetButton']['indicator'] = false;
        }

        # Attachments
        if (!isset($this->options['attachment']['elem_type'])) {
            $this->options['attachment']['elem_type'] = false;
        }

        if (!isset($this->options['attachment']['elem_key'])) {
            $this->options['attachment']['elem_key'] = false;
        }
    }


    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render($this->options['view'], [
          'videos' => $this->videos,
          'options' => $this->options,
        ]);
    }
}
