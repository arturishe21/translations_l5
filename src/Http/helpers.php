<?php
use Vis\Translations\Trans;

//get translate
function __($phrase, array $replacePhrase = []) {
    $this_lang = Lang::locale();

    $array_translate =  Trans::fillCacheTrans();

    if (isset($array_translate[$phrase][$this_lang])) {
        $phrase = $array_translate[$phrase][$this_lang];
    }

    if (count($replacePhrase)) {
        $phrase = str_replace(array_keys($replacePhrase), array_values($replacePhrase), $phrase);
    }

    return $phrase;
}

  function cmp($a, $b) {
        if ($a == $b) {
            return 0;
        }
        return ( strlen($a) < strlen($b)) ? -1 : 1;
    }
