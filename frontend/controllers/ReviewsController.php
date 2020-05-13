<?php

namespace frontend\controllers;

use common\models\News;

class ReviewsController extends NewsController
{
    /* Since for article view we must use another representation, let's redefine this value to needed file for rendering */
    public $partialViewFile = '_articleblock';
    public $newsType = News::NEWS_TYPE_REVIEW;
}
