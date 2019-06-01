/*
* preciseTextResize.js v1.0
* Resizes text, based on char count and width/height of parent element.
* Text sizes must be set manually as each UI set will require it's own array of sizes.
*
* RU: На самом портале, на странице новостей, блочные элементы могут быть разной ширины и высоты (500px, 250px),
* и заголовок новости тоже может быть разный по количеству символов (максимальная оптимальная длина = 59 символов).
* Почему именно такая оптимальная длина - читаем тут: https://moz.com/blog/title-tag-length-guidelines-2016-edition
*
* Учитывая, что блок может быть маленьким, но с длинным заголовком, или, к примеру, большим, но с коротким заголовком,
* нам необходимо просчитать оптимальный размер шрифта для каждого случая, под каждый размер, чтобы не нарушить UI.
* Стандартные фиттеры типа FitText.js не подходят по ряду причин, среди которых - невозможность точно указывать dimensions.
*
* В дальнейшем плагин может быть дописан или модифицирован, чтобы передавать параметры размеров через options (array sizes).
* Сетка имеет массив вида: ширина блока -> высота блока -> количество символов : размер шрифта
*
* Параметры offset нужны для того, чтобы учитывать полную высоту/ширину элемента, т.к. если у элемента есть border,
* то он не считается за продолжение самого элемента, а значит мы получаем dimension элемента как (dimension - border[px]).
*
* Copyright 2016, <scsmash3r@gmail.com>
*/

(function($) {
   $.fn.preciseTextResize = function(options) {

// Establish default settings/variables
// ====================================
      var settings = $.extend({
         grid : [{
             500 : {500 : {1:62,8:54,16:48,24:42,36:38,45:36,49:32,55:32},
                    250 : {1:46,8:40,16:36,24:32,36:28,45:24}},

             250 : {250 : {1:32,10:30,15:28,22:24,26:21,38:20,45:18,50:16}},
             //max-width 540px
             350 : {350 : {1:32,10:30,15:28,22:24,26:21,38:20,45:18,50:16},
                    125 : {1:32,10:30,15:28,22:24,26:21,38:20,45:18,50:16}},
             175 : {175 : {1:32,10:30,15:28,22:24,26:21,38:20,45:18,50:16}},
             //360*640
             347 : {250 : {1:32,10:30,15:28,22:26,26:24,38:22,45:20,50:18},
                    125 : {1:28,10:24,15:21,22:20,26:16,38:15,45:14,50:13}},
             //357*640
             344 : {250 : {1:32,10:30,15:28,22:26,26:24,38:22,45:20,50:18},
                    125 : {1:28,10:24,15:21,22:20,26:16,38:15,45:14,50:13}},
             //320*568
             307 : {250 : {1:30,10:28,15:26,22:24,26:22,38:20,45:18,50:16},
                    125 : {1:26,10:22,15:20,22:18,26:16,38:15,45:14,50:13}},
             //max-width:280
             267 : {200 : {1:28,10:26,15:24,22:20,26:16,38:15,45:14,50:13},
                    100 : {1:28,10:22,15:18,22:18,26:15,38:14,45:13,50:13}},
         }],
         parent: null,
         widthOffset: 0,
         heightOffset: 0,
         lineHeightFix: false,
         lineHeightRatio: 1.2
      }, options),

// Do the magic
// ============
      changes = function(el) {
         var elem = $(el),
             elw = elem.width(),
             elh = elem.height(),
             parentw = elw,
             parenth = elh,
             textLength = $.trim(elem.text()).length;

         if (settings.parent !== null) {
          var parent = elem.parent(settings.parent);
              parentw = parent.width()+settings.widthOffset;
              parenth = parent.height()+settings.heightOffset;
          }


         $.each(settings.grid, function(key, width) {
           if (width[0]) {
             parentw = 0;
           }

            if (width[parentw] && width[parentw][parenth]) {
              $.each(width[parentw][parenth], function(strsize, fontsize) {
                if (textLength >= strsize) {
                  elem.css({'font-size':fontsize+'px'});
                  if (settings.lineHeightFix) { elem.css({'line-height':(fontsize/lineHeightRatio)+'px'}); }
                }
              });
            }
         });

      };

// Make the magic visible
// ======================
      return this.each(function() {
      // Context for resize callback
         var that = this;
         // If we already applied styles - return
         if ($(that).attr('style')) {
           return;
         }
      // Set changes on load
         changes(this);
      });
   };
}(jQuery));
