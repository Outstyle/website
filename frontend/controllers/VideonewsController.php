<?php

namespace frontend\controllers;

use common\models\News;

class VideonewsController extends NewsController
{
    /* Since for article view we must use another representation, let's redefine this value to needed file for rendering */
    public $partialViewFile = '//article/_articleblock';
    public $newsType = News::NEWS_TYPE_VIDEONEWS;
}
