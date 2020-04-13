<?php

namespace Vis\Translations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Yandex\Translate\Translator;

class Trans extends Model
{
    protected $table = 'translations_phrases';

    public static $rules = [
        'phrase' => 'required|unique:translations_phrases',
    ];

    protected $fillable = ['phrase'];

    public $timestamps = false;

    public function getTrans()
    {
        $res = $this->hasMany('Vis\Translations\Translate', 'id_translations_phrase')->get()->toArray();

        if ($res) {
            $trans = [];
            foreach ($res as $k=>$el) {
                $trans[$el['lang']] = $el['translate'];
            }

            return $trans;
        }
    }

    /**
     * auto generate translation for function __() if empty.
     *
     * @param string $phrase
     * @param strign $thisLang
     *
     * @return string
     */
    public static function generateTranslation($phrase, $thisLang)
    {
        if ($phrase && $thisLang) {
            $checkPresentPhrase = self::where('phrase', 'like', $phrase)->first();
            if (! $checkPresentPhrase) {
                $newPhrase = self::create(['phrase' => $phrase]);

                $langsDef = config('app.locale');
                $langsAll = array_keys(config('builder.translations.config.languages'));

                foreach ($langsAll as $lang) {
                    $lang = str_replace('ua', 'uk', $lang);
                    $langsDef = str_replace('ua', 'uk', $langsDef);
                    $translate = $phrase;

                    if (config('builder.translations.cms.api_yandex_key')) {
                        try {
                            $translator = new Translator(config('builder.translations.cms.api_yandex_key'));
                            $translation = $translator->translate($phrase, $langsDef.'-'.$lang);
                            $translate = $translation->getResult()[0] ?? $phrase;
                        } catch (\Exception $e) {
                            $translate = $phrase;
                        }
                    }

                    Translate::create(
                        [
                            'id_translations_phrase' => $newPhrase->id,
                            'lang'                   => str_replace('uk', 'ua', $lang),
                            'translate'              => $translate,
                        ]
                    );
                }

                self::reCacheTrans();
                $arrayTranslate = self::fillCacheTrans();

                return $arrayTranslate[$phrase][$thisLang] ?? 'error translation';
            }

            $translatePhrase = Translate::where('id_translations_phrase', $checkPresentPhrase->id)
                ->where('lang', 'like', $thisLang)->first();

            if ($translatePhrase) {
                return $translatePhrase->translate;
            }
        }
    }

    /**
     * filling cache translate.
     *
     * @return array
     */
    public static function fillCacheTrans()
    {
        if (Cache::get('translations')) {
            $arrayTranslate = Cache::get('translations');
        } else {
            $arrayTranslate = self::getArrayTranslation();
            Cache::forever('translations', $arrayTranslate);
        }

        return $arrayTranslate;
    }

    /** recache translate.
     *
     * @return void
     */
    public static function reCacheTrans()
    {
        Cache::forget('translations');
        self::fillCacheTrans();
    }

    /**
     * get array all phrase translation.
     *
     * @return array
     */
    private static function getArrayTranslation()
    {
        $translationsGet = DB::table('translations_phrases')
            ->leftJoin('translations', 'translations.id_translations_phrase', '=', 'translations_phrases.id')
            ->get(['translate', 'lang', 'phrase']);

        $arrayTranslate = [];
        foreach ($translationsGet as $el) {
            $el = (array) $el;
            $arrayTranslate[$el['phrase']][$el['lang']] = $el['translate'];
        }

        return $arrayTranslate;
    }
}
