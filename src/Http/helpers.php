<?php
use Vis\Translations\Trans;

//get translate
if (!function_exists('__')) {
    function __($phrase, array $replacePhrase = [])
    {
        return __t($phrase, $replacePhrase);
    }
}

//for laravel 5.4
if (!function_exists('__t')) {
    function __t ($phrase, array $replacePhrase = [])
    {
        $thisLang = Lang::locale ();
        $array_translate = Trans::fillCacheTrans ();

        if (isset($array_translate[$phrase][$thisLang])) {
            $phrase = $array_translate[$phrase][$thisLang];
        } else {
            $phrase = Trans::generateTranslation ($phrase, $thisLang);
        }

        if (count ($replacePhrase)) {
            $phrase = str_replace (array_keys ($replacePhrase),
                array_values ($replacePhrase), $phrase);
        }

        return $phrase;
    }
}

if (!function_exists('cmp')) {
    function cmp ($a, $b)
    {
        if ($a == $b) {
            return 0;
        }
        return (strlen ($a) < strlen ($b)) ? -1 : 1;
    }
}
