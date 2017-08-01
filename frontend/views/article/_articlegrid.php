<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use common\components\helpers\ElementsHelper;

if (empty($category)) {

    /* Filter box form wrap for articles */
    echo
      Html::beginTag('div', ['class' => 'o-grid o-grid--wrap o-grid--no-gutter', 'id' => 'articles-filter']),

          Html::beginForm('', '',
            [
              'id' => 'article-filter-form',
              'way-data' => 'article.filter',
              'way-persistent' => 'true',
            ]
          );

          /*
           * Getting all the categories for filtering
           * @var $modelNews    common/models/News  -> getNews()
           */
          foreach ($newsCategories as $c) {
              echo ElementsHelper::ajaxedCheckbox('categories[]', $c->id, Yii::t('app', $c->name), Url::toRoute('article/show'), '#outstyle_articles .articles__body', '#article-filter-form');
          }

    echo Html::endForm(),

    Html::endTag('div');

    echo ElementsHelper::separatorDiamond('<h1>'.Yii::t('seo', Yii::$app->controller->id.'.h1').'</h1>');
} else {
    echo
    Html::tag('div',

      /* BREADCRUMBS */
      Breadcrumbs::widget(
        [
          'tag' => 'ol',
          'homeLink' => false,
          'options' => ['class' => 'c-breadcrumbs u-cf'],
          'itemTemplate' => '<li class="c-breadcrumbs__crumb">{link}<i class="icon-right-open-big zmdi-hc-small"></i></li>',
          'links' => [
              [
                'label' => Yii::t('app', ucfirst(Yii::$app->controller->id)),
                'url' => ['/'.Yii::$app->controller->id],
                'ic-get-from' => Url::to('/'.Yii::$app->controller->id),
                'ic-indicator' => ElementsHelper::DEFAULT_AJAX_LOADER,
              ],
              [
                'label' => $modelNews[0]['category'],
              ],
          ],
        ]
      ),

      [
        'class' => 'o-grid__cell o-grid__cell--width-100',
      ]
    );

    foreach ($newsCategories as $c) {
        if ($c->id == $category) {
            echo ElementsHelper::separatorDiamond('<h1>'.Yii::t('seo', Yii::$app->controller->id.'.'.$c->url.'.h1').'</h1>');
        }
    }
}

echo Html::a('<i class="zmdi zmdi-plus-circle-o zmdi-hc-3x"></i>', '#googleforms_add_article',
  [
    'class' => 'btn btn__addnew roundcorners modal-open',
    'title' => 'Предложить статью'
  ]);
echo $this->render('@modals/google/GoogleFormsAddArticle');

/*
* --- Main article blocks ---
* Notice, that we are passing $modelNews from 'NewsController', using News model.
* That's because 'article' is only a representation of 'news' and uses the same array of values.
*/

echo Html::tag('div',
  $this->render('_articleblock', [
      'modelNews' => $modelNews,
      'page' => $page,
      'category' => $category,
  ]),
[
  'class' => 'o-grid__cell o-grid__cell--width-100 u-pillar-box--super news-single articles__body',
]);

/* This input is for sending pages */
echo Html::hiddenInput('page', $page, ['id' => 'page']);


/* JS: @see js/outstyle.portal.articles.grid.js */
?>
<script>jQuery(document).ready(function(){articlesGridInit();});</script>
