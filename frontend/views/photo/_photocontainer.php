<?php
/**
 * @link https://github.com/Outstyle/website
 * @copyright Copyright (c) 2018 Outstyle Network
 * @license Beerware
 */
use yii\helpers\Html;
use frontend\models\Photo;

/**
 * Photo container
 *
 * @var $this               yii\web\View
 * @var $photo
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 * @since 1.0
*/

echo Html::img($photo['img'], [
    'class' => 'o-image'
]);
