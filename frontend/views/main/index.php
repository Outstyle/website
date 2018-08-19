<?php
    use yii\helpers\Html;
    use yii\helpers\Url;
    use frontend\widgets\WidgetProfileUserMenu;
    use yii\widgets\ActiveForm;
    use frontend\models\UserAvatar;
    use frontend\models\UserDescription;
    use frontend\models\User;
    use frontend\models\Photo; 
    use frontend\models\Country;
    use frontend\models\City;
    /* @var $this yii\web\View */
    $this->title = Yii::t('app', 'Главная').' - '.Yii::$app->name;
    $this->registerMetaTag(['name' => 'description', 'content' => $this->title]);
    //$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Главная'), 'url' => ['/main/index']];
?>
<div class="main-page">
    <div class="poster0">
        <div class="type-news">
        
            <img src="<?php echo Yii::$app->homeUrl; ?>css/img/msing.png">
            <div class="filter">
                <div class="filter-item">
                    <p>фильтр</p>
                    <form>
                        <input type="checkbox" id="all" name="cc" />
                        <label class="all-filter" for="all"><span></span>все</label>
                        <input type="checkbox" id="brayk" name="cc" />
                        <label class="brayk-filter" for="brayk"><span></span>брейкданс</label>
                        <input type="checkbox" id="mc" name="cc" />
                        <label class="mc-filter" for="mc"><span></span>мс’инг</label>
                        <input type="checkbox" id="graffiti" name="cc" />
                        <label class="graffiti-filter" for="graffiti"><span></span>графити</label>
                        <input type="checkbox" id="dj" name="cc" />
                        <label class="dj-filter" for="dj"><span></span>диджеинг</label>
                    </form>
                </div> 
                <div class="check-filter"></div> 
            </div>
        </div>
        <span class="name-type-news">MC'инг</span>
        <span class="name-group">Black Eyed Peas</span>
        <hr style="width:150px">
        <div class="group-description">
            Группа ставшая лауреатом Grammy Awards<br>
            выпустила новый клип трибьют хитам прошлого.
        </div>
        <div class="footer-group">
            <div class="icons">
                <div class="add-coment">
                    <img src="<?php echo Yii::$app->homeUrl; ?>css/img/AddComment_ICO.png"> 154
                </div>
                <div class="like">
                    <img src="<?php echo Yii::$app->homeUrl; ?>css/img/Like_ICO.png"> 587
                </div>
                <div class="share">
                    <img src="<?php echo Yii::$app->homeUrl; ?>css/img/Share_ICO.png">
                </div>
            </div>
            <div class="read-more">
                Подробнее
            </div>
        </div>
    </div>
    <div class="poster1">
        <div class="type-news">
            <img src="<?php echo Yii::$app->homeUrl; ?>css/img/msing.png">
        </div>
        <span class="name-type-news">День из истории</span>
        <span class="name-group">28.08.1958 <br> Родился Майк</span>
        <hr style="width:150px">
        <div class="group-description">
            28 августа 1958 года родился легендарный<br>
            Майкл Джексон.
        </div>
        <div class="footer-group">
            <div class="icons">
                <div class="add-coment">
                    <img src="<?php echo Yii::$app->homeUrl; ?>css/img/AddComment_ICO.png"> 121
                </div>
                <div class="like">
                    <img src="<?php echo Yii::$app->homeUrl; ?>css/img/Like_ICO.png"> 587
                </div>
                <div class="share">
                    <img src="<?php echo Yii::$app->homeUrl; ?>css/img/Share_ICO.png">
                </div>
            </div>
            <div class="read-more">
                Подробнее
            </div>
        </div>
    </div>
    <div class="poster2">
        <div class="type-news">
            <img src="<?php echo Yii::$app->homeUrl; ?>css/img/breik.png">
        </div>
        <span class="name-type-news">Брейкданс</span>
        <span class="name-group">Battlescholl</span>
        <hr style="width:150px">
        <div class="group-description">
            Battlescholl
        </div>
        <div class="footer-group">
            <div class="icons">
                <div class="add-coment">
                    <img src="<?php echo Yii::$app->homeUrl; ?>css/img/AddComment_ICO.png"> 121
                </div>
                <div class="like">
                    <img src="<?php echo Yii::$app->homeUrl; ?>css/img/Like_ICO.png"> 587
                </div>
                <div class="share">
                    <img src="<?php echo Yii::$app->homeUrl; ?>css/img/Share_ICO.png">
                </div>
            </div>
            <div class="read-more">
                Подробнее
            </div>
        </div>
    </div>
    <div class="poster3">
        <div class="type-news">
            <img src="<?php echo Yii::$app->homeUrl; ?>css/img/breik.png">
        </div>
        <span class="name-type-news">Брейкданс</span>
        <span class="name-group">ЛИЛУ - 31</span>
        <hr style="width:150px">
        <div class="group-description">
            ЛИЛУ - 31
        </div>
        <div class="footer-group">
            <div class="icons">
                <div class="add-coment">
                    <img src="<?php echo Yii::$app->homeUrl; ?>css/img/AddComment_ICO.png"> 121
                </div>
                <div class="like">
                    <img src="<?php echo Yii::$app->homeUrl; ?>css/img/Like_ICO.png"> 587
                </div>
                <div class="share">
                    <img src="<?php echo Yii::$app->homeUrl; ?>css/img/Share_ICO.png">
                </div>
            </div>
            <div class="read-more">
                Подробнее
            </div>
        </div>
    </div>
    <div class="poster12">
    </div>
    <div class="poster13">
    </div>
    <div class="poster14">
    </div>
    <div class="poster15">
    </div>
    <div class="poster16">
    </div>
    <div class="poster17">
    </div>
    <div class="poster18">
    </div>
    <div class="promo-social">
        <div class="logo-promo">
        </div>
        <div class="reg-text"> 
            Регистрируйся в первой хип-хоп<br>
            социальной сети рунета!
        </div>
        <div class="users-ava">
            <div class="user">
                <img src="<?php echo Yii::$app->homeUrl; ?>css/img/ava-user-reg.png">
                kolobok
            </div>
            <div class="user">
                <img src="<?php echo Yii::$app->homeUrl; ?>css/img/ava-user-reg.png">
                kolobok
            </div>
            <div class="user">
                <img src="<?php echo Yii::$app->homeUrl; ?>css/img/ava-user-reg.png">
                kolobok
            </div>
            <div class="user">
                <img src="<?php echo Yii::$app->homeUrl; ?>css/img/ava-user-reg.png">
                kolobok
            </div>
        </div>
        <div class="register">
            Регистрироваться<img src="<?php echo Yii::$app->homeUrl; ?>css/img/arrow-reg.png">
        </div>
        <img class="close-reg" src="<?php echo Yii::$app->homeUrl; ?>css/img/Close.png">
    </div>
</div>
