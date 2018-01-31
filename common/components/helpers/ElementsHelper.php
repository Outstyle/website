<?php

namespace common\components\helpers;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Provides all the needed HTML tag elements for better work with Outstyle layout
 * Using blazecss classes: http://blazecss.com/.
 *
 * TODO: Split into pieces, like with attachment helper (this one has too much methods already)
 */
class ElementsHelper extends Html
{
    const DEFAULT_AJAX_ID = 'ajax';
    const DEFAULT_TARGET_ID = 'content';
    const DEFAULT_ID_PREFIX = 'outstyle_';
    const DEFAULT_AJAX_LOADER = '#outstyle_loader';

    /*
     * !!! DO NOT CHANGE THE ORDER OF ELEMENTS! ONLY ADD NEW ONES BELOW!
     * This list is needed for comparing allowed elements, that are about to be written to DB
     * Since DB stores numeric values and not the element names, this list highly relies on array keys
     * Use 'getElementIdByControllerId' method to get it's numeric value for DB storing
     */
    public static $allowedElements = [
      'NONE',
      'news',
      'article',
      'school',
      'events',
      'board',
      'comments',
      'photo',
      'video',
    ];

    /**
     * Transforms controller ID (string) into its numeric value (int)
     * Since keys are counting from 0, we will add 1, so the 'news' could be first
     * @param  string $controllerId     (see self::$allowedElements)
     * @return int
     */
    public static function getElementIdByControllerId($controllerId = '')
    {
        # If element is not in our list
        if (!in_array($controllerId, self::$allowedElements)) {
            return;
        }

        return (array_keys(self::$allowedElements, $controllerId)[0]);
    }

    /**
     * Generates CSRF toekn for AJAX requests, where needed
     * @return string
     */
    public static function getCSRFToken()
    {
        $csrf_token = Yii::$app->request->get('_csrf');
        if (!$csrf_token) {
            $csrf_token = Yii::$app->request->csrfToken;
        }
        return $csrf_token;
    }

    /**
     * Wraps content into div containers, needed for receiving ajax requests
     * First container goes as an active wrap for ajax
     * Second one indicates elemnt type (controller ID i.e.) and can have custom options
     * Third is a o-grid wrapper.
     *
     * @param string $el_type     element's type (will be used as a class for DIV block)
     * @param string $add_classes additional classes for grid wrap element
     * @param string $content     the content to be enclosed in the grid
     * @param array  $options     the HTML tag attributes (HTML options) in terms of name-value pairs
     *
     * @return html the generated HTML output
     */
    public static function ajaxGridWrap($el_type = '', $add_classes = '', $content = '', $options = [])
    {
        if ($el_type === null || $el_type === false) {
            return $content;
        }

        if (!isset($options['id'])) {
            $options['id'] = self::DEFAULT_ID_PREFIX.$el_type;
        }

        if (!isset($options['class'])) {
            $options['class'] = 'color-content--bg';
        }

        $class = preg_replace('!\s+!', ' ', trim("o-grid o-grid--wrap {$add_classes} {$el_type}"));

        return
        Html::tag('div',
          Html::tag('div',
            Html::tag('div',
              $content,
            [
              'class' => $class,
              'ic-target' => '#'.self::DEFAULT_TARGET_ID,
              'ic-push-url' => 'true',
              'ic-select-from-response' => '#'.self::DEFAULT_AJAX_ID,
            ]),
          $options),
        ['id' => self::DEFAULT_AJAX_ID]);
    }

    /**
     * Generates an active button element to send API requests for likes.
     *
     * @param string     $elem_type    elem_type from DB (see self::$allowedElements)
     * @param int        $elem_id      elem_id from DB (taxonomy/relations)
     * @param int|string $likesCount
     * @param bool       $isLikeActive
     *
     * @return html the generated HTML button tag
     */
    public static function likeButton($elem_type = '', $elem_id = 0, $likesCount = '', $isLikeActive = 0)
    {
        if ($isLikeActive) {
            $isLikeActive = 'active';
        } else {
            $isLikeActive = '';
        }

        $class = preg_replace('!\s+!', ' ', trim("zmdi-icon--hoverable i-like i-icon {$isLikeActive}"));

        return
        Html::button(
          Html::tag('i', '', [
            'class' => 'icon-heart',
          ])
          .Html::tag('span', $likesCount),
        [
          'class' => $class,
          'title' => Yii::t('app', 'Like'),
          'ic-indicator' => self::DEFAULT_AJAX_LOADER,
          'ic-include' => '{"elem_type":"'.$elem_type.'","id":'.(int) $elem_id.'}',
          'ic-action' => 'toggleClass:active',
          'ic-target' => 'find span',
          'ic-get-from' => Url::toRoute('likes/like'),
          'ic-push-url' => 'false',
        ]);
    }

    /**
     * Generates an active button element to send API requests for comments.
     *
     * @param string     $elem_type    elem_type from DB (see self::$allowedElements)
     * @param int        $elem_id      elem_id from DB (taxonomy/relations)
     *
     * @return html the generated HTML button tag
     */
    public static function commentAddButton($elem_type = '', $elem_id = 0)
    {
        $class = preg_replace('!\s+!', ' ', trim("c-button u-small i-send u-pull-right"));

        return
        Html::button(
          Yii::t('app', 'Send'),
        [
          'class' => $class,
          'title' => Yii::t('app', 'Send'),
          'ic-indicator' => self::DEFAULT_AJAX_LOADER,
          'ic-include' => '{"elem_type":"'.$elem_type.'","elem_id":'.(int) $elem_id.'}',
          'ic-target' => '#'.$elem_type.'_comments .comments_body',
          'ic-get-from' => Url::toRoute(['comments/add']),
          'ic-append-from' => Url::toRoute(['comments/add']),
          'ic-push-url' => 'false',
          'ic-select-from-response' => '#new_comment'
        ]);
    }

    /**
     * Generates an active video link
     * @param  string $video_hash
     * @param  string $video_title
     * @param  string $video_image
     * @return HTML <a> tag
     */
    public static function videoLink($video_hash = '', $video_title = '')
    {
        return
        Html::a($video_title, Url::toRoute('/video-'.$video_hash, true),
          [
            'class' => 'user__videotitle',
            'ic-action' => 'userShowVideoModal',
            'ic-get-from' => Url::toRoute('/video-'.$video_hash),
            'ic-select-from-response' => '#'.self::DEFAULT_TARGET_ID,
            'ic-target' => '#uservideo .modal__iframe',
            'ic-indicator' => self::DEFAULT_AJAX_LOADER,
            'ic-push-url' => 'true',
          ]
        );
    }

    /**
     * Generates an active 'a' tag element with given type
     * ZMDI icons: http://zavoloklom.github.io/material-design-iconic-font/icons.html#comment.
     *
     * @param string       $elem_name  Name of the 'a' element
     * @param string|false $url        Url (full or relative) or wrapped into Url::toRoute()
     * @param string|false $icon       icon name (see names @ZMDI icons)
     * @param string|false $elem_title tag's 'title' attr
     *
     * @return html the generated HTML a tag
     */
    public static function linkElement($elem_name = '', $elem_text = '', $url = false, $icon = '', $elem_title = '')
    {
        /* INITIAL for ic-select-from-response: http://intercoolerjs.org/attributes/ic-select-from-response.html */
        $selected_element = '#'.self::DEFAULT_AJAX_ID;

        /*
         * Setting title for the link, referring to element's name
         * For more names, please add manually here
         */
        switch ($elem_name) {
          case 'comment':
            $elem_title = Yii::t('app', 'Comment');
            break;

          case 'views':
            $elem_title = Yii::t('app', 'Views');
            $elem_name .= ' innactive';
            break;

          case 'repost':
            $elem_title = Yii::t('app', 'Repost');
            break;

          case 'readmore':
            $elem_title = Yii::t('app', 'Read more...');
            break;

          case 'category':
            $elem_title = Yii::t('app', 'More from '.$elem_text);
            break;

          case 'friend':
            $selected_element = '#content';
            break;

          case 'title':
            $elem_title = strip_tags($elem_text);
            $elem_name .= ' c-text--shadow block__'.$elem_name.' '.Yii::$app->controller->id.'__'.$elem_name;
            break;

          default:
            $elem_title = ($elem_title) ? $elem_title : 'UNTITLED';
        }

        /* Adding controller to class */
        $elem_name .= ' '.Yii::$app->controller->id.'__'.$elem_name;

        /*
         * Wrapping up our element in case if it has no tags
         */

         if ($elem_text != strip_tags($elem_text)) {
             $elem_text = trim($elem_text);
         } else {
             $elem_text = "<span>{$elem_text}</span>";
         }

        /*
         * Checking for an icon bundles.
         * I.e. if our icon has 'zmdi' in it, we will know what class to assign
         */
        if ($icon) {
            if (strpos($icon, 'zmdi') !== false) {
                $icon = 'zmdi '.$icon;
            }

            $elem_text = Html::tag('i', '', ['class' => "{$icon}"]).$elem_text;
            $elem_name .= ' i-icon';
        }

        /**
         * Adding some initial tag attributes.
         */
        $attr = [];

        /*
         * If our link has actual URL (href) or else
         */
        if ($url && strpos($url, "#") === false) {
            $attr['ic-get-from'] = $url;
            $attr['ic-indicator'] = self::DEFAULT_AJAX_LOADER;
            $attr['ic-target'] = '#'.self::DEFAULT_TARGET_ID;
            $attr['ic-push-url'] = 'true';
            $attr['ic-select-from-response'] = $selected_element;
        }

        if (!$url) {
            $url = 'javascript:void(0)';
        }

        $attr['class'] = $elem_name;
        $attr['title'] = Html::encode(Yii::t('app', $elem_title));

        /*
          Separate actions handling
         */

        return Html::a($elem_text, $url, $attr);
    }

    /**
     * Load more element
     * See http://intercoolerjs.org/attributes/ic-include.html on $include param.
     *
     * @param string $append_from url to append an info from
     * @param string $target_el   element selector to append data to
     * @param string $include     what data to include (can be jquery selectors or valid JSON obj)
     *
     * @return html the generated HTML a tag
     */
    public static function loadMore($append_from = '', $target_el = '', $include = '')
    {
        return Html::tag('span', '', [
          'id' => 'loadmore',
          'ic-append-from' => $append_from,
          'ic-trigger-on' => 'scrolled-into-view',
          'ic-target' => $target_el,
          'ic-indicator' => self::DEFAULT_AJAX_LOADER,
          'ic-push-url' => 'false',
          'ic-include' => $include,
          'ic-on-beforeSend' => 'document.getElementById(\'loadmore\').remove()',
        ]);
    }

    /**
     * Ajaxed checkbox with custom styling
     * For $method see: http://intercoolerjs.org/reference.html (post-to, get-from, append-to, prepend-from).
     *
     * @param string $name      input[type=checkbox][name=$name]
     * @param string $value     input[type=checkbox].val()
     * @param string $title     checkbox title, wrapped into <span>
     * @param string $url       Url (full or relative) or wrapped into Url::toRoute()
     * @param string $target_el element selector to {$method} data to
     * @param string $include   what data to include (can be jquery selectors or valid JSON obj)
     * @param string $method    method that will be applied while getting the data (append or get or post)
     *
     * @return html the generated HTML span tag
     */
    public static function ajaxedCheckbox($name = '', $value = '', $title = '', $url = '', $target_el = '', $include = '', $method = 'get-from')
    {
        return
        Html::tag('label',

          //checkbox
          Html::checkbox($name, false,
            [
              'value' => $value,
              'ic-indicator' => self::DEFAULT_AJAX_LOADER,
              'ic-target' => $target_el,
              'ic-push-url' => 'false',
              'ic-trigger-delay' => '200ms',
              'ic-include' => $include,
              'ic-'.$method => $url,
            ]
          ).

          //checkbox square for styling
          Html::tag('div',
            '<i class="zmdi zmdi-check"></i>',
            [
              'class' => 'checkbox__square u-pull-left c-field--ordinary',
            ]
          )
          .'<span>'.trim($title).'</span>',

          [
            'class' => 'u-pillar-box--medium u-pull-left noselect checkbox__wrap',
          ]
        ).

        //fake checkbox label for swapping while element is active and AJAX event is going
        Html::tag('label',

          //fake checkbox square for styling
          Html::tag('div',
            '<i class="zmdi zmdi-check"></i>',
            [
              'class' => 'checkbox__square u-pull-left c-field--ordinary',
              'data-fake-id' => $value,
            ]
          )
          .'<span>'.trim($title).'</span>',
          [
            'class' => 'u-pillar-box--medium u-pull-left noselect checkbox__wrap--disabled',
          ]
        );
    }

    /**
     * Separator with diamond-shaped title.
     *
     * @param string $text        Text to show inside the diamond box
     * @param string $size        Padding size (see 'boxing': http://blazecss.com/utilities/boxing/ )
     * @param bool   $linethrough Should the line go through the padding?
     * @param string $labelAlign  Diamond with text positioning
     *
     * @return html the generated HTML output
     */
    public static function separatorDiamond($text = '', $size = 'super', $linethrough = true, $labelAlign = 'left')
    {
        $divOneAttrs = $divTwoAttrs = [];

        //should our diamond box border be full width with linethrough?
        if (!$linethrough) {
            $divOneAttrs['class'] = 'u-pillar-box--'.$size;
            $divTwoAttrs['class'] = 'c-diamond c-diamond__border c-diamond__border--small';
        } else {
            $divTwoAttrs['class'] = 'c-diamond c-diamond__border c-diamond__border--small u-pillar-box--'.$size;
        }

        //positioning of label and additional classes and decorations
        if (strpos($labelAlign, 'toggleable')) {
            $text = '<i class="zmdi zmdi-chevron-down"></i>'.$text.'<i class="zmdi zmdi-chevron-down"></i>';
        }

        return
        Html::tag('div',
          Html::tag('div',
            Html::tag('div',
              Html::tag('div',
                '<span class="u-pillar-box--xlarge">'.trim($text).'</span>',
                [
                  'class' => "c-diamond__label c-diamond--small c-diamond__label--{$labelAlign}",
                ]
              ),
              $divTwoAttrs
            ),
            $divOneAttrs
          ),
          [
            'class' => 'o-grid__cell o-grid__cell--no-gutter o-grid__cell--width-100',
          ]
        );
    }

    /**
     * Separator with corner shadows
     * @param  integer $effect Shadow effect number (CSS style)
     * @param  string  $style  Custom CSS style (bottomborder, etc)
     * @return html            Div block with styles
     */
    public static function separatorWidget($effect = 0, $style = 'none')
    {
        return Html::tag('div',
          '',
          [
            'class' => "box box__shadow box__shadow--effect{$effect} box--{$style}"
          ]);
    }

    /**
     * Widget button
     * @param   string $action    Widget button action
     * @param   string $position  Widget button position
     * @param   string $size      Widget button size
     * @see:    http://zavoloklom.github.io/material-design-iconic-font/icons.html for icon name
     * @return  html
     */
    public static function widgetButton($action = 'edit', $position = 'topleft', $size = 'lg')
    {
        /*
         * Setting params for button, based on action
         * For more names, please add manually here
         */
        switch ($action) {
          case 'edit':
            $title = Yii::t('app', 'Edit');
            $icon = 'edit';
            break;

          case 'delete':
            $title = Yii::t('app', 'Delete');
            $icon = 'close-circle';
            break;

          default:
            $title = 'UNTITLED';
            $icon = 'http';
        }

        $class = preg_replace('!\s+!', ' ', trim("zmdi-icon--hoverable i-widgetbutton i-widgetbutton--{$position} i-{$icon}"));

        return
        Html::button(
          Html::tag('i', '', [
            'class' => "zmdi zmdi-{$icon} zmdi-hc-{$size}",
          ]),
        [
          'class' => $class,
          'title' => $title,
          'ic-indicator' => self::DEFAULT_AJAX_LOADER,
          'ic-get-from' => Url::toRoute([Yii::$app->controller->id.'/'.$action]),
          'ic-push-url' => 'false'
        ]);
    }

    /**
     * Post settings button
     * @param   string $style Icon name
     * @see:    http://zavoloklom.github.io/material-design-iconic-font/icons.html for icon name
     * @return  html
     */
    public static function postSettingsButton($style = 'more')
    {
        $class = preg_replace('!\s+!', ' ', trim("zmdi-icon--hoverable i-postbutton i-postbutton--right i-{$style}"));

        return
        Html::button(
          Html::tag('i', '', [
            'class' => "zmdi zmdi-{$style} zmdi-hc-lg",
          ]),
        [
          'class' => $class,
          'title' => Yii::t('app', ucfirst($style)),
          'ic-indicator' => self::DEFAULT_AJAX_LOADER,
          'ic-target' => '#outstyle_comments .comments_body',
          'ic-get-from' => Url::toRoute(['comments/add']),
          'ic-prepend-from' => Url::toRoute(['comments/add']),
          'ic-push-url' => 'false',
          'ic-select-from-response' => '#new_comment'
        ]);
    }

    /**
     * Filter box tooltip
     * For $method see: http://intercoolerjs.org/reference.html (post-to, get-from, append-to, prepend-from).
     *
     * @param array $categories Array of categories from model
     * @paran  string $name       input checkbox group name
     *
     * @param string $url       Url (full or relative) or wrapped into Url::toRoute()
     * @param string $target_el element selector to {$method} data to
     * @param string $include   what data to include (can be jquery selectors or valid JSON obj)
     * @param string $method    method that will be applied while getting the data (append or get or post)
     *
     * @return html Generated HTML output
     */
    public static function filterBox($categories = [], $name = '', $url = '', $target_el = '', $include = '', $method = 'get-from')
    {
        /**
         * Working with categories array.
         */
        $categories_list = '';

        foreach ($categories as $category) {
            $categories_list .=

            //category label
            Html::label(
              Html::input('checkbox', $name, $category->id,
                []
              ).
              Html::tag('i',
                '',
                [
                  'class' => "zmdi zmdi-circle-o color-{$category->url}",
                ]
              ).
              '<span>'.Yii::t('app', $category->name).'</span>',
              null,
              []
            );
        }

        return
        Html::tag('div',

          //filter box form with persistent data
          Html::beginForm('', 'post',
            [
              'id' => Yii::$app->controller->id.'-filter-form',
              'class' => 'u-window-box--small tooltip-box__body',
              'way-data' => Yii::$app->controller->id.'.filter',
              'way-persistent' => 'true',
            ]
          ).

          //filter box label
          Html::tag('p',
            Yii::t('app', 'Filter'),
            [
              'class' => 'c-text--shadow tooltip-box__header',
            ]
          ).

          //filter box items (categories array)
          Html::tag('div',
            $categories_list,
            [
              'class' => 'u-window-box--small',
            ]
          ).

          //filter box submit button
          Html::tag('p',
            Html::button(
              Html::tag('i',
                '',
                [
                  'class' => 'zmdi zmdi-check color-breaking',
                ]
              ),
              [
                'id' => Yii::$app->controller->id.'-filter__submit',
                'class' => 'zmdi-icon--hoverable',
                'ic-indicator' => self::DEFAULT_AJAX_LOADER,
                'ic-target' => $target_el,
                'ic-push-url' => 'false',
                'ic-include' => $include,
                'ic-'.$method => $url,
              ]
            ),
            [
              'class' => 'tooltip-box__footer',
            ]
          ).

          Html::endForm(),
          [
            'id' => 'filter-box',
            'class' => 'tooltip-box noselect',
          ]
        );
    }

    /**
     * 500x250 filter block for various purposes, i.e. geolocation filtering.
     *
     * @param string $type         block type (can declare own)
     * @param string $controllerId controllerId, that is allowed in self::$allowedElements (just in case :D)
     * @param array  $categories   categories for filtering dropdown (optional)
     *
     * @return html Generated HTML output
     */
    public static function filterBlock($type = '', $controllerId = '', $categories = [])
    {
        if (!in_array($controllerId, self::$allowedElements)) {
            return;
        }

        // map our categories to proper structure for Select2 usage (see proper formatting: https://select2.github.io/options.html#how-should-nested-results-be-formatted)
        if (isset($categories)) {
            $categories = array_map(function ($category) {
                return array(
                  'id' => $category['id'],
                  'text' => $category['name'],
              );
            }, $categories);
        }

        switch ($type) {
          case 'geolocation':
            return
            Html::tag('div',

              //filter box form with persistent data
              Html::beginForm('', 'post',
                [
                  'id' => $controllerId.'-filter-block',
                  'class' => 'u-window-box--large',
                  'way-data' => $controllerId.'.filter.'.$type,
                  'way-persistent' => 'true',
                ]
              ).

              //filter box label
              Html::tag('h4',
                Yii::t('app', 'Find schools'),
                [
                  'class' => 'block__title',
                ]
              ).

              Html::a('<i class="zmdi zmdi-plus-circle-o zmdi-hc-3x"></i>', '#googleforms_add_school',
                [
                  'class' => 'btn btn__addnew roundcorners modal-open',
                  'title' => 'Добавить школу'
                ]).

              \Yii::$app->view->render('@modals/google/GoogleFormsAddSchool').

              \Yii::$app->view->render('@common/views/geolocation/_filterblock', [
                'categories' => $categories,
                ]).

              Html::endForm(),
              [
                'id' => $controllerId.'-filter-block--'.$type,
                'class' => 'color-content--bg',
              ]
            );

            break;

        }
    }

    /**
     * Generates blocks for gallery.
     *
     * @param array $photosArray  Array of items, generated from Photo model query
     * @see: Photo::getByAlbumId()
     *
     * @return html Generated HTML output
     */
    public static function galleryBlock($photos_array = [])
    {
        $response = '';

        /* Gallery loop */
        foreach ($photos_array as $k => $photo) {
            $response .=
            Html::tag('div',

              /* Gallery image with lightbox */
              Html::tag('a',
                Html::img(
                  '/frontend/web/images/photoalbum/'.$photo['img_thumbnail'],
                  [
                   'class' => 'gallery__image o-image',
                  ]
                ),
                [
                  'data-fancybox' => 'gallery',
                  'href' => '/frontend/web/images/photoalbum/'.$photo['img'],
                ]
              ),

              [
                'class' => 'gallery__item',
              ]
            );
        }

        return $response;
    }

    /**
     * Generates a block for errors displaying from $model->errors array.
     *
     * @param array $errors '$model->errors' array
     *
     * @return html Generated HTML output
     */
    public static function errorsContainer($errors = [])
    {
        echo Html::beginTag('div', ['id' => 'errors_container']);
        foreach ($errors as $field_name => $error) {
            echo Html::ul([strtoupper($field_name).': '.$error[0]], ['class' => 'alert alert-red']);
        }
        echo Html::endTag('div');
    }
}
