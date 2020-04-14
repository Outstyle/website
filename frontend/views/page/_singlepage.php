
<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
    $this->title = Yii::t('app', 'О нас');
    //$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Главная'), 'url' => ['/main/index']];
    $this->registerMetaTag(['name' => 'description', 'content' => $this->title]);
    //$this->params['breadcrumbs'][] = $this->title;

?>
<div class="about-us" style="text-align: center;  margin: 0 auto;">
	<h1>О нас</h1>
<p style="text-align: center;">
OutStyle — это портал и социальная сеть для удобного
</p>
<p style="text-align: center;">
<span></span>общения между представителями хип-хоп сообщества.
</p>
<p style="text-align: center;">
<br>
<br>
Задача OutStyle — объединить в одном месте всех представителей культуры:
</p>
<p style="text-align: center;">
MCs, DJs, Writers, Bboys, а также тех, кому эта культура интересна.
</p>
<p style="text-align: center;">
<br>
</p>
<div style="background:#cccccc;padding-top:30px;padding-bottom:30px;">
<h3 style="text-align: center;">
Для прессы и партнёров
</h3>
<p>
<img src="/images/news/590a374b062c7.png" style="height: 100px; width: 100px; display: block; margin: auto;" alt="">
</p>
<p class="blog_about_link_title" style="text-align: center;">
<a href="mailto:press@outstyle.org">press@outstyle.org</a>
</p>
<p class="blog_about_link_descr" style="text-align: center;">
В данный момент мы ищем людей, которые смогут писать интересные новости и статьи для портала :)
</p>
</div>
</div>
