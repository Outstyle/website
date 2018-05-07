<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\db\ActiveRecord;
use yii\data\Pagination;
use backend\models\Category;
use app\models\Comments;
use app\models\Likes;
use app\models\UserDescription;
use app\models\UserNickname;
use app\models\UserAvatar;
use common\components\helpers\StringHelper;
use common\components\helpers\BlocksHelper;
use himiklab\sitemap\behaviors\SitemapBehavior;
use yii\helpers\Url;

/**
 * This is the model class for table "{{%news}}".
 *
 * @property int    $id
 * @property string $name
 * @property string $url
 * @property int    $category
 * @property int    $user
 * @property string $title
 * @property string $description
 * @property string $small
 * @property string $text
 * @property string $img
 * @property string $created
 * @property int    $article
 * @property int    $date_redact
 * @property int    $redactor_id
 * @property int    $status
 * @property int    $img_block_size
 */


class News extends ActiveRecord
{
    const STATUS_MODERATED = 0;
    const STATUS_PUBLISHED = 1;
    const STATUS_DRAFT = 2;
    const STATUS_DELETED = 3;

    const NEWS_CATEGORIES = [1, 2, 3, 4]; /* Categories to select from category table */

    /**
     * @var int    How much news do we show per page by default?
     * @var int    $similarPageSize        How much similar news do we show per page?
     * @var int    $recommendedPageSize    How much recommended news do we show per page?
     * @var string $addtionalNewsOrderBy   Default order of additional news
     */
    public static $defaultPageSize = 15;
    public static $similarPageSize = 6;
    public static $recommendedPageSize = 6;
    public static $addtionalNewsOrderBy = 'RAND()';

    /**
     * imageUploaderBehavior - https://github.com/demisang/yii2-image-uploader
     * Needed for 'News' image uploading and cropping in admin area.
     */
    public function behaviors()
    {
        return [
        'imageUploaderBehavior' => [
          'class' => 'demi\image\ImageUploaderBehavior',
          'imageConfig' => [
            'imageAttribute' => 'img',
            'savePathAlias' => Yii::$app->params['imagesPathDir'].Yii::$app->controller->id,
            'rootPathAlias' => Yii::$app->params['imagesPathDir'],
            'noImageBaseName' => 'noimage.jpg',
            'imageSizes' => [
              '' => 1000, /* Also serves as a maxWidth limit for uploaded file and only for this set of image sizes */
              '500x500_' => 500, /* 1/1 aspect ratio */
              '500x250_' => 500, /* 2/1 aspect ratio */
              '250x250_' => 250, /* 1/1 aspect ratio */
              '250x125_' => 250, /* 2/1 aspect ratio */
              '150x450_' => 150, /* 1/3 aspect ratio - won't be generated until 1/3 is not set up in aspectRatio param */
            ],
            'imageValidatorParams' => [
              'minWidth' => 500,
              'minHeight' => 500,
              'maxWidth' => 2000,
              'maxHeight' => 2000,
            ],
            'aspectRatio' => [
              1 / 1,
              2 / 1,
            ],
            'imageRequire' => false,
            'fileTypes' => 'jpg,jpeg,gif,png',
            'maxFileSize' => 3145728,
            'backendSubdomain' => 'admin.',
          ],
        ],
            'sitemap' => [
                'class' => SitemapBehavior::className(),
                'scope' => function ($model) {
                    $model->select(['url', 'created', 'article']);
                    $model->andWhere(['status' => 1]);
                },
                'dataClosure' => function ($model) {
                    if($model->article == 1){
                        $model_url = Url::to('/article/'.$model->url, 'https');
                    }
                    else{
                        $model_url = Url::to('/news/'.$model->url, 'https');
                    }
                    return [
                        'loc' => $model_url,
                        'lastmod' => strtotime($model->created),
                        'changefreq' => SitemapBehavior::CHANGEFREQ_DAILY,
                        'priority' => 0.8
                    ];
                },

            ]
      ];
    }

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
      return '{{%news}}';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
      return [
        [['date_redact'], 'default', 'value' => 0],
        [['redactor_id'], 'default', 'value' => 0],

        [['name', 'category', 'small', 'text'], 'required'],
        [['user', 'category', 'article', 'redactor_id', 'status'], 'integer'],
        [['small'], 'string', 'max' => 520],
        [['created', 'date_redact'], 'safe'],
        [['url', 'description', 'img'], 'string', 'max' => 155],
        [['name', 'title'], 'string', 'max' => 60],
        [['text'], 'string', 'min' => 300],
        [['url'], 'unique'],
        [['text'], 'string'],
        ['img_block_size', 'integer', 'min' => 0, 'max' => 9],
        [['img'], 'file', 'extensions' => ['png', 'jpg', 'jpeg', 'gif']],
      ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
      return [
        'id' => Yii::t('app', 'ID'),
        'name' => Yii::t('app', 'Название'),
        'url' => Yii::t('app', 'Url'),
        'category' => Yii::t('app', 'Категория'),
        'user' => Yii::t('app', 'Пользователь'),
        'title' => Yii::t('app', 'SEO Title'),
        'description' => Yii::t('app', 'SEO Description'),
        'small' => Yii::t('app', 'Короткий текст'),
        'text' => Yii::t('app', 'Полный текст'),
        'img' => Yii::t('app', 'Главное большое фото'),
        'created' => Yii::t('app', 'Создан'),
        'article' => Yii::t('app', 'Статья'),
        'date_redact' => Yii::t('app', 'Дата редактирования'),
        'redactor_id' => Yii::t('app', 'ID редактора'),
        'status' => Yii::t('app', 'Статус'),
        'img_block_size' => Yii::t('app', 'Размер блока'),
      ];
  }

  /**
   * Gets the news from DB and returns an array of data. TODO: Split up 'recommended' and 'similar' news.
   *
   * @param array $where WHERE clause to add for more precise selection. Defaults to select everything with published status
   * @param int $page page number. Must be >0 for pagination to appear
   *
   * @return array|null news data
   */
  public static function getNews($where = [], $page = null)
  {

      /* Default arguments for 'where' clause */
      $where['status'] = self::STATUS_PUBLISHED;

      /**
       * Getting the news: let's start by partially adding parameters in case we have pagination
       * Notice that we are adding query parameters one by one (chaining), checking additional conditions
       * Read more about QB syntax here: http://www.yiiframework.com/doc-2.0/guide-db-query-builder.html
       * Also we are using 'with()' for eager loading from user description table.
       *
       * TODO: Add to userdescription only needed params select
       */
      $newsQuery = self::find()->with(['userDescription'])->where($where)->orderBy('id desc');
      $newsQuery = $newsQuery->limit(self::$defaultPageSize);

      /* If we have pagination */
      if ($page) {
          $pagination = new Pagination([
            'defaultPageSize' => self::$defaultPageSize,
            'totalCount' => $newsQuery->count(),
            'page' => $page - 1,
          ]);

          $newsQuery = $newsQuery->offset($pagination->offset)->limit($pagination->limit);
      }

      /* And finally let's make our request to DB */
      $news = $newsQuery->all();

      /* If we don't have any news queried, we won't populate news model */
      if (!$news) {
          return;
      }

      /* Now to populate our News model with data */
      for ($i = 0; $i < count($news); ++$i) {

          /* Other models data */
          $likes = Likes::findLikesCount(Yii::$app->controller->id, $news[$i]->id);
          $comments = Comments::countComments(Yii::$app->controller->id, $news[$i]->id);
          $imagesize = BlocksHelper::getBlockSizeStatus($news[$i]->img_block_size);

          $modelNews[$i]['id'] = $news[$i]->id;
          $modelNews[$i]['url'] = $news[$i]->url;
          $modelNews[$i]['text'] = trim($news[$i]->small);
          $modelNews[$i]['name'] = $news[$i]->name;
          $modelNews[$i]['title'] = (!empty($news[$i]->title)) ? trim($news[$i]->title) : trim($news[$i]->name);
          $modelNews[$i]['img'] = $news[$i]->getImageSrc($imagesize.'_');
          $modelNews[$i]['img_block_size'] = $imagesize;
          $modelNews[$i]['created'] = StringHelper::convertTimestampToHuman(strtotime($news[$i]->created));
          $modelNews[$i]['userId'] = $news[$i]->user;
          $modelNews[$i]['myLike'] = Likes::findMyLike(Yii::$app->controller->id, $news[$i]->id);
          $modelNews[$i]['likesCount'] = ($likes) ? (int) $likes : 0;
          $modelNews[$i]['countComments'] = (!empty($comments)) ? (int) $comments : 0;
          $modelNews[$i]['category'] = $news[$i]->categories->name;
          $modelNews[$i]['categoryUrl'] = $news[$i]->categories->url;

          /*
           * Additional stuff for single news page (or when news represented as an article)
           * If we have URL parameter, lets also populate additional array with similar news from same category
           * Description key is needed for single news page (SEO purposes). You can add additional stuff for singles here
           */
          if (isset($where['url']) || $where['article'] == 1) {
              $modelNews[$i]['description'] = trim($news[$i]->description);
              $modelNews[$i]['text'] = trim($news[$i]->text);
              $modelNews[$i]['img'] = $news[$i]->getImageSrc('250x250_');
              $modelNews[$i]['userName'] = UserNickname::getNickname($news[$i]->user);
              $modelNews[$i]['userAvatar'] = UserAvatar::getAvatarPath($news[$i]->user);
              $modelNews[$i]['userCulture'] = ArrayHelper::getValue(UserDescription::cultureList(true), $news[$i]->userDescription->culture);
              $modelNews[$i]['comments'] = Comments::getComments(['elem_type' => Yii::$app->controller->id, 'elem_id' => $news[$i]->id]);
              $modelNews[$i]['categories'] = Category::getCategories(['id' => self::NEWS_CATEGORIES]);

              /* TODO: make it as a separate methods (getSimilarNews, getRecommendedNews)
               * Adding a category parameter to WHERE clause and making another request
               * We also need to unset URL parameter to query all the news + exclude it from query pool
               */
              if (isset($where['url'])) {
                  $where['category'] = $news[$i]->category;
                  $andWhere = ['!=', 'id', $news[$i]->id];
                  unset($where['url']);

                  /* Getting similar news */
                  /* TODO: How to count similarity? Probably must be based on hashtags? https://trello.com/c/tgqgMiFJ */
                  $similarNews = self::find()
                  ->where($where)
                  ->andWhere($andWhere)
                  ->orderBy(self::$addtionalNewsOrderBy)
                  ->limit(self::$similarPageSize)
                  ->all();
                  if (!empty($similarNews)) {
                      $count = (count($similarNews) >= self::$similarPageSize) ? self::$similarPageSize : count($similarNews);
                      for ($s = 0; $s < $count; ++$s) {
                          $modelNews[$i]['similar'][$s]['name'] = $similarNews[$s]->name;
                          $modelNews[$i]['similar'][$s]['url'] = $similarNews[$s]->url;
                          $modelNews[$i]['similar'][$s]['img'] = $similarNews[$s]->getImageSrc('250x125_');
                      }
                  }

                  /* Getting recommended news */
                  /* TODO: How to count recommendations? Probably by likes count? https://trello.com/c/tgqgMiFJ */
                  if (!isset($where['article'])) {
                      $recommendedNews = self::find()
                      ->where($where)
                      ->andWhere($andWhere)
                      ->orderBy(self::$addtionalNewsOrderBy)
                      ->limit(self::$recommendedPageSize)
                      ->all();
                      if (!empty($recommendedNews)) {
                          $count = (count($recommendedNews) >= self::$recommendedPageSize) ? self::$recommendedPageSize : count($recommendedNews);
                          for ($s = 0; $s < $count; ++$s) {
                              $modelNews[$i]['recommended'][$s]['name'] = $recommendedNews[$s]->name;
                              $modelNews[$i]['recommended'][$s]['url'] = $recommendedNews[$s]->url;
                              $modelNews[$i]['recommended'][$s]['img'] = $recommendedNews[$s]->getImageSrc('250x125_');
                          }
                      }
                  }
              }
          }
      }

      return $modelNews;
  }

    /**
     * Relations: http://www.yiiframework.com/doc-2.0/guide-db-active-record.html#relational-data.
     */
    public function getUserDescription()
    {
        return $this->hasOne(UserDescription::className(), ['id' => 'user']);
    }

    public function getLikes()
    {
        return $this->hasMany(Likes::className(), ['elem_id' => 'id']);
    }

    public function getRedactor()
    {
        if (!empty($this->hasOne(User::className(), ['id' => 'redactor_id'])->one()->username)) {
            return $this->hasOne(User::className(), ['id' => 'redactor_id'])->one()->username;
        }

        return  '';
    }

    public function getCategories()
    {
        return $this->hasOne(Category::className(), ['id' => 'category']);
    }

    public function getComments()
    {
        return $this->hasMany(Comments::className(), ['user_id' => 'user']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user']);
    }
}
