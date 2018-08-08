<?php
/**
 * @link https://github.com/Outstyle/website
 * @copyright Copyright (c) 2018 Outstyle Network
 * @license Beerware
 */
use yii\helpers\Html;
use yii\helpers\Url;

use common\components\helpers\ElementsHelper;
use common\components\helpers\SEOHelper;

/**
 * Main friends page
 *
 * @var $this                 yii\web\View
 * @var $friends              @frontend/models/Friend
 *
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 * @since 1.0
 */

SEOHelper::setMetaInfo($this);

echo Html::beginTag('div', ['id' => 'ajax']);

    /* --- LEFT BLOCK SECTION --- */
    echo Html::beginTag('section', ['id' => 'leftBlock']),
        $this->render('search/_form'),
    Html::endTag('section');


    /* --- RIGHT BLOCK SECTION --- */
    echo Html::beginTag('section', ['id' => 'rightBlock']),
        $this->render('view', [
          'friends' => $friends
        ]),
    Html::endTag('section');

echo Html::endTag('div');
