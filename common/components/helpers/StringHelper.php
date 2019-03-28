<?php

namespace common\components\helpers;

use Yii;

/**
 * StringHelper provides a set of static methods for working with strings, i.e. to modify or to validate them
 * @author [SC]Smash3r <scsmash3r@gmail.com>
 * @since 1.0
 */
class StringHelper
{
    /**
     * Reduce Multiples
     * Reduces multiple instances of a particular character. Example:
     * Fred, Bill,, Joe, Jimmy
     * becomes:
     * Fred, Bill, Joe, Jimmy.
     *
     * @param	string
     * @param	string	the character you wish to reduce
     * @param	bool	TRUE/FALSE - whether to trim the character from the beginning/end
     *
     * @return string
     */
    public static function reduceMultiples($str, $character = ',', $trim = false)
    {
        $str = preg_replace('#'.preg_quote($character, '#').'{2,}#', $character, $str);
        if ($trim === true) {
            $str = trim($str, $character);
        }

        return $str;
    }

    /**
     * Creates a slug out of string. Example:
     * slugify('название для статьи') will return:
     * 'nazvanie-dlja-statii'.
     *
     * Currently supports only CYR strings
     *
     * @param $str
     *
     * @return string
     */
    public static function slugify($str)
    {
        $search = '_';
        $replace = '-';
        $go = "а б в г д е ё ж з и й к л м н о п р с т у ф х ц ч ш щ ъ ы ь э ю я А Б В Г Д Е Ё Ж З И Й К Л М Н О П Р С Т У Ф Х Ц Ч Ш Щ Ъ Ы Ь Э Ю Я ' \" ( ) [ ] { } . ,";
        $to = 'a b v g d e yo zh z i j k l m n o p r s t u f h c ch sh shh i y i ye yu ya A B V G D E Yo Zh Z I J K L M N O P R S T U F H C Ch Sh Shh II Y II YE YU YA _ _ _ _ _ _ _ _ - -';
        $go = explode(' ', $go);
        $to = explode(' ', $to);
        $str = str_replace($go, $to, trim($str));
        $trans = array(
            '&\#\d+?;' => '',
            '&\S+?;' => '',
            '\s+' => $replace,
            '[^a-z0-9\-\._]' => '',
            $search.'+' => $replace,
            $search.'$' => $replace,
            '^'.$search => $replace,
            '\.+$' => '',
        );
        $str = strip_tags($str);
        foreach ($trans as $key => $val) {
            $str = preg_replace('#'.$key.'#i', $val, $str);
        }
        $str = mb_strtolower($str, 'UTF-8');
        $str = self::reduceMultiples($str, '-', true);

        return trim(stripslashes($str));
    }

    /**
     * Cuts a string to a given amount of characters, leaving last whole words uncut.
     *
     * @param string
     * @param number
     * @param bool
     *
     * @return string
     */
    public static function cutString($string, $maxlen, $precise = false)
    {
        $cutLen = $len = (mb_strlen($string) > $maxlen)
            ? mb_strripos(mb_substr($string, 0, $maxlen), ' ')
            : $maxlen
        ;

        if ($precise) {
            $cutLen = $maxlen;
        }

        $cutStr = strip_tags(mb_substr($string, 0, $cutLen));
        return (mb_strlen($string) > $maxlen)
            ? $cutStr.'...'
            : $cutStr
        ;
    }

    /**
     * Checks url for validity (used mainly by 'away' checks for external links)
     * @param  string $url Url to check
     * @return bool
     */
    public static function checkExternalUrlValidity($url)
    {
        // Remove all illegal characters from a url
        $url = filter_var($url, FILTER_SANITIZE_URL);

        // Validate url
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            return true;
        }

        return false;
    }

    /**
     * Cuts all unwanted chars from a string
     * @param  string $string String to replace
     * @return string
     */
    public static function clearString($string)
    {
        return mb_ereg_replace('/[^a-zа-яА-Я0-9\-\. _]/i', '', trim(strip_tags($string)));
    }

    /**
     * Converts timestamp to human readable string
     * Using 'datetime.php' messages translations.
     * TODO: Break this up into separate funcs
     *
     * @param int     $time           UNIX timestamp
     * @param string  $format         date() format (http://php.net/manual/en/function.date.php)
     * @param string  $controllerId   Controller ID to return date with a certain rules for that controller
     *
     * @return string
     */
    public static function convertTimestampToHuman($time = 0, $format = '', $controllerId = '')
    {
        $todayPrefix = 'Today';
        $yesterdayPrefix = 'Yesterday';
        $longAgoPrefix = '';

        /* Working with different date formats for converting to suit our needs */
        if ($format) {
            switch ($format) {
                case 'F':
                return Yii::t('datetime', 'of '.date($format, $time));

                case 'd M':
                # Match d m Y date for today's date, returning H:i string
                if (date('d m Y', time()) === date('d m Y', $time)) {
                    return date('H:i', $time);
                }
                return date('d', $time).' '.Yii::t('datetime', date('M', $time));

                default:
                return Yii::t('datetime', date($format, $time));
            }
        }

        /* Working with controllers */
        if ($controllerId) {
            switch ($controllerId) {

                case 'events':
                    if ($time < time()) {
                        return Yii::t('app', 'Event passed');
                    }

                case 'users':
                    $todayPrefix = 'Was today';
                    $yesterdayPrefix = 'Was yesterday';
                    $longAgoPrefix = 'Last seen';

                    if ((time() - $time) < 900) {
                        return Yii::t('app', 'online');
                    }
            }
        }

        # Match d m Y date for today's date, returning H:i string
        if (date('d m Y', time()) === date('d m Y', $time)) {
            return Yii::t('datetime', $todayPrefix.' @ {time}', ['time' => date('H:i', $time)]);
        }

        # Match yesterday day, returning H:i string
        if ((date('d', time()) - date('d', $time)) == 1 && date('m Y', time()) == date('m Y', $time)) {
            return Yii::t('datetime', $yesterdayPrefix.' @ {time}', ['time' => date('H:i', $time)]);
        }

        # If our year is already passed one */
        if (date('Y', $time) != date('F', time())) {
            return Yii::t('datetime', $longAgoPrefix).' '.date('d', $time).' '.Yii::t('datetime', 'of '.date('F', $time)).' '.date('Y', $time);
        }

        # Default
        return date('d', $time).' '.Yii::t('datetime', 'of '.date('F', $time)).' <span>'.date('H:i', $time).'</span>';
    }
}
