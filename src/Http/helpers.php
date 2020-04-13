<?php

use Vis\Translations\Trans;

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
