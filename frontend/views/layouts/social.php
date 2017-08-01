<?php
/**
 * Main SOCIAL layout
 * Questions? Feel free to ask: <scsmash3r@gmail.com> / skype: smash3rs / Telegram: @scsmash3r
 * Github: https://github.com/scsmash3r
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Menu;
use yii\widgets\Spaceless;

use frontend\assets\SocialAsset;

SocialAsset::register($this);

$this->beginPage();
Spaceless::begin();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo Html::encode($this->title); ?></title>
<?php $this->head(); ?>
<?php echo Html::csrfMetaTags(); ?>
<link rel="shortcut icon" type="image/png" href="<?=Url::toRoute('/css/favicon.png');?>">
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
</head>
<body class="social user<?php echo (Yii::$app->user->id) ? '-registered' : '-guest'; ?>">
<?php
$this->beginBody();

# Fixed width wrapper
echo Html::beginTag('div', ['class' => 'wrap']);
echo Html::beginTag('div', ['class' => 'wrap-fix']);

  # Music player area + logo
  echo Html::tag('header',

    Html::a('', Url::to(['/id'.Yii::$app->user->id]),
      [
          'id' => 'logo',
          'class' => 'logo',
          'ic-get-from' => Url::to(['/id'.Yii::$app->user->id]),
          'ic-trigger-delay' => "200ms",
          'ic-select-from-response' => '#content',
          'ic-indicator' => '#outstyle_loader',
          'ic-target' => '#content'
      ]
    ).

    '',//$this->render('@forms/headerPlayerForm'),
    ['class' => 'social__header']
  );

  # Sidebar with navigation
  $checkController = function ($route) {
      return $route === $this->context->getUniqueId(); /* Checks only top-level */
  };
  $checkRoute = function ($route) {
      return $route === $this->context->getRoute(); /* Checks all levels, including child pages */
  };
  echo Html::tag('aside',
      Html::tag('nav',
        Menu::widget(
          [
            'itemOptions' => ['tag' => 'span'],
            'options' => ['tag' => false],
            'encodeLabels' => false,
            'activeCssClass' =>'c-nav--active',
            'linkTemplate' => '<a href="{url}" ic-get-from="{url}" ic-select-from-response="#content" ic-target="#content" ic-trigger-delay="200ms" ic-indicator="#outstyle_loader" class="c-nav__item">{label}</a>',
            'items' => [
                [
                  'label' => '<i class="icons icons--wall"></i>'.Yii::t('app', 'Wall'),
                  'url' => ['/id'.Yii::$app->user->id],
                  'active' => $checkController('users')
                ],
                [
                  'label' => '<i class="icons icons--messages"></i>'.Yii::t('app', 'Messages'),
                  'url' => ['/id'.Yii::$app->user->id],
                  'active' => $checkController('messages')
                ],
                [
                  'label' => '<i class="icons icons--friends"></i>'.Yii::t('app', 'Friends'),
                  'url' => ['/'],
                  'active' => $checkController('messages')
                ],
                [
                  'label' => '<i class="icons icons--photos"></i>'.Yii::t('app', 'Photos'),
                  'url' => ['/'],
                  'active' => $checkController('messages')
                ],
                [
                  'label' => '<i class="icons icons--videos"></i>'.Yii::t('app', 'Videos'),
                  'url' => ['/videos'],
                  'active' => $checkController('video')
                ],
                [
                  'label' => '<i class="icons icons--settings"></i>'.Yii::t('app', 'Settings'),
                  'url' => ['/'],
                  'active' => $checkController('messages')
                ],
            ],
        ]
      ),
      [
          'class' => 'c-nav',
          'ic-target' => '#content',
          'ic-select-from-response' => '#ajax',
          'ic-push-url' => 'true',
      ]
    ),
    ['class' => 'social__sidebar']
  );

  # Main content
  echo Html::tag('main',
    $content,
    [
      'id' => 'content',
      'ic-history-elt' => '',
      'class' => 'content u-window-box--small'
    ]
  );

  /*
      ==================================
      Actions & stuff for all users
      ==================================
  */
  echo $this->render('@modals/userVideo');

echo Html::endTag('div');
echo Html::endTag('div');
?>
<div id="scrollup"><i class="icon-up-open-big icon-huge"></i></div>
<div id="outstyle_loader"><div class="loader"></div></div>
<div id="ohsnap"></div>
<script type="text/javascript" src="/js/jquery.mousewheel.js"></script>
<script type="text/javascript" src="/js/jquery.jscrollpane.js"></script>
<?php $this->endBody() ?>
<?php echo Html::endTag('body'); ?>
</html>
<?php
Spaceless::end();
$this->endPage() ?>
