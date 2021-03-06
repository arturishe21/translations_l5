<?php

use Vis\Translations\Trans;

//get translate
if (! function_exists('__')) {
    function __($phrase, array $replacePhrase = [])
    {
        return __t($phrase, $replacePhrase);
    }
}

//for laravel 5.4
if (! function_exists('__t')) {
    function __t($phrase, array $replacePhrase = [])
    {
        if (env('APP_ENV') == 'testing') {
            return $phrase;
        }

        $thisLang = Lang::locale();
        $arrayTranslate = app('arrayTranslate');

        if (is_array($arrayTranslate) && array_key_exists($phrase, $arrayTranslate) && isset($arrayTranslate[$phrase][$thisLang])) {
            $phrase = $arrayTranslate[$phrase][$thisLang];
        } else {
            $phrase = Trans::generateTranslation($phrase, $thisLang);
        }

        if (count($replacePhrase)) {
            $phrase = str_replace(array_keys($replacePhrase), array_values($replacePhrase), $phrase);
        }

        return $phrase;
    }
}

if (! function_exists('cmp')) {
    function cmp($a, $b)
    {
        if ($a == $b) {
            return 0;
        }

        return (strlen($a) < strlen($b)) ? -1 : 1;
    }
}
