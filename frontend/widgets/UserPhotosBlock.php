<?php

namespace frontend\widgets;

use Yii;
use yii\base\Widget;
use common\models\Photo;

/**
 * Handles User -> Photos block, showing photos of user
 * Part of Outstyle network
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 *
 * @version 1.0
 *
 * @link https://github.com/Outstyle/website
 * @license Beerware
 */
class UserPhotosBlock extends Widget
{

    /**
     * User photos array
     * @var array
     */
    public $photos = [];

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

        # Working with default options
        if (!isset($this->options['class'])) {
            $this->options['class'] = '';
        }

        if (!isset($this->options['cell_class'])) {
            $this->options['cell_class'] = '';
        }

        if (!isset($this->options['view'])) {
            $this->options['view'] = 'userPhotosBlock';
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
          'photos' => $this->photos,
          'options' => $this->options,
        ]);
    }
}
