<?php

namespace Vis\Translations;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Yandex\Translate\Translator;

class GenerateTranslateTable extends Command
{
    protected $name = 'translate:table {tables} {fields}';
    protected $description = 'Generate translate for fields tables if they empty, example: translate:table tb_tree,news title,description';

    protected $signature = 'translate:table {tables} {fields}';

    public function __construct()
    {
        parent::__construct();
    }

    public function fire()
    {
        $languages = array_values(config('translations.config.alt_langs'));
        $defaultLanguage = config('translations.config.def_locale');
        $key = array_search($defaultLanguage, $languages);
        unset($languages[$key]);

        $this->info(print_arr($languages));

        $tables = explode(',', $this->argument('tables'));
        $fields = explode(',', $this->argument('fields'));
        $translator = new Translator(config('builder.translate_cms.api_yandex_key'));
        $this->info('Start');
        foreach ($tables as $table) {
            foreach ($fields as $field) {
                if (Schema::hasColumn($table, $field)) {
                    $phrases = DB::table($table)->get();

                    $this->createFieldIfNotExist($table, $field, $languages);

                    foreach ($phrases as $phrase) {
                        $phrase = (array) $phrase;
                        $valueField = $phrase[$field];

                        if ($valueField) {
                            foreach ($languages as $lang) {
                                $newField = $field.'_'.$lang;
                                if (! $phrase[$newField]) {
                                    $lang = str_replace('ua', 'uk', $lang);
                                    $defaultLanguage = str_replace('ua', 'uk', $defaultLanguage);

                                    $translation = $translator->translate($valueField, $defaultLanguage.'-'.$lang);

                                    if (isset($translation->getResult()[0])) {
                                        DB::table($table)
                                            ->where('id', $phrase['id'])
                                            ->update([$newField => $translation->getResult()[0]]);
                                    }
                                    $this->info($lang.' -> '.$translation->getResult()[0]);
                                }
                            }
                        }
                    }
                } else {
                    $this->info('Не существует поля '.$field.' в таблице '.$table);
                }
            }
        }

        $this->info('finish');
    }

    private function createFieldIfNotExist($table, $field, $languages)
    {
        foreach ($languages as $lang) {
            $newField = $field.'_'.$lang;

            if (! Schema::hasColumn($table, $newField)) {
                $this->info($field.'_'.$lang);

                $typeField = $this->getTypeField($table, $field);

                Schema::table($table, function ($table) use ($field, $newField, $typeField) {
                    if ($typeField == 'text') {
                        $table->text($newField)->after($field);
                    } else {
                        $table->string($newField)->after($field);
                    }

                    $this->info('Created new field '.$newField);
                });
            }
        }
    }

    private function getTypeField($table, $field)
    {
        $table_info_columns = DB::select(DB::raw('SHOW COLUMNS FROM '.$table));

        foreach ($table_info_columns as $fields) {
            if ($fields['Field'] == $field) {
                return $fields['Type'];
            }
        }
    }
}
