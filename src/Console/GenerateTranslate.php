<?php

namespace Vis\Translations;

use Illuminate\Console\Command;
use Yandex\Translate\Translator;

class GenerateTranslate extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'translate:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate translate for phrases __() if empty ';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function fire()
    {
        $allPhrase = Trans::get();
        $languages = config('translations.config.alt_langs');
        $defaultLanguage = config('translations.config.def_locale');
        $this->info('Start');
        $transNoExit = true;
        $translator = new Translator(config('builder.translate_cms.api_yandex_key'));

        foreach ($allPhrase as $phrase) {
            foreach ($languages as $lang) {
                if (! $this->ifExistTranslate($phrase->id, $lang)) {
                    $this->info(Trans::generateTranslation($phrase->phrase, $lang));

                    $lang = str_replace('ua', 'uk', $lang);
                    $defaultLanguage = str_replace('ua', 'uk', $defaultLanguage);

                    $translation = $translator->translate($phrase->phrase, $defaultLanguage.'-'.$lang);
                    $lang = str_replace('uk', 'ua', $lang);

                    if (isset($translation->getResult()[0])) {
                        $this->info($phrase->phrase.' -- '.$lang.' -> '.$translation->getResult()[0]);

                        Translate::create(
                            [
                                'id_translations_phrase' => $phrase->id,
                                'lang'                   => $lang,
                                'translate'              => $translation->getResult()[0],
                            ]
                        );
                        $transNoExit = false;
                    }
                }
            }
        }

        Trans::reCacheTrans();

        if ($transNoExit) {
            $this->info('Нет фраз для перевода');
        }

        $this->info('Finish');
    }

    private function ifExistTranslate($id, $lang)
    {
        return Translate::where('id_translations_phrase', $id)->where('lang', $lang)->count();
    }
}
