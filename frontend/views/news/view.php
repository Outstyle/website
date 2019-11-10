<?php

use common\components\helpers\ElementsHelper;

/* @var $this yii\web\View */
$this->title = $modelNews[0]['title'];
$this->registerMetaTag([
  'name' => 'description',
  'content' => (($modelNews[0]['description']) ? $modelNews[0]['description'] : $modelNews[0]['name']),
]);

/**
 * Single news view, that must be wrapped by #ajax for Intercooler
 * @var $modelNews      common/models/News -> getNews()
 */

echo
ElementsHelper::ajaxGridWrap('news-single', 'o-grid--no-gutter color-content--bg',
  $this->render('_newssingle', [
      'modelNews' => $modelNews,
      'visibleScript' => $visibleScript,
      'bodyScript' => $bodyScript,
    ]
  )
);
