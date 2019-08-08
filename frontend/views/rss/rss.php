<?php
use yii\helpers\Url;
use yii\helpers\StringHelper;


$response = Yii::$app->response;
\Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
$response->getHeaders()->set('Content-Type', 'application/xml; charset=utf-8');

$feed->addChannel(Url::toRoute('/', true));
$feed
    ->addChannelTitle('Музыкальные и хип-хоп новости')
    ->addChannelLink(Url::toRoute('/', true))
    ->addChannelDescription('Подробно о мире хип-хопа');

$feed
    ->addChannelLanguage(Yii::$app->language);

foreach ($model as $item){
    if ($item['date_redact'] == 0) {
        $date = new DateTime($item['created']);
        $pubDate = $date->format(DateTime::RFC822);
    } else {
        $dateRedact = $item['date_redact'];
        $date = new DateTime("@$dateRedact");
        $pubDate = $date->format(DateTime::RFC822);
    }
    $fullText = strip_tags($item['text']);
    $srcImg = Url::toRoute('/',true).'/frontend/web/images/news/'.$item['img'];

    $typeImg = '';

    $jpg = strripos($item['img'], '.jpg');
    $png = strripos($item['img'], '.png');

    if($jpg){$typeImg = 'image/jpeg';}
    if($png){$typeImg = 'image/png';}

    $feed->addItem();
    $feed
        ->addItemTitle(StringHelper::truncateWords($item['name'], 200))
        ->addItemDescription(strip_tags($item['small']));
    $feed
        ->addItemLink(Url::toRoute(['news/' . $item['url']], true))
        ->addItemAuthor($item['username'])
        ->addItemCategory($item['catName'])
        ->addItemPubDate($pubDate);
    $feed->addItemElement('yandex:full-text', $fullText);
    if(!empty($item['img']))
    $feed->addItemElement('enclosure','',['url'=>$srcImg, 'type'=> $typeImg]);

}


echo $feed;
