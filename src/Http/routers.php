<?php

Route::get('/js/translate_phrases_{lang}.js', 'Vis\Translations\TranslateController@getJs')->name('translate_js');
Route::group(['middleware' => ['web']], function () {
    Route::group(
        ['prefix' => 'admin', 'middleware' => 'auth.admin'],
        function () {
            Route::any('translations/phrases', [
                'as'   => 'phrases_all',
                'uses' => 'Vis\Translations\TranslateController@fetchIndex', ]
                );

            if (Request::ajax()) {
                Route::post('translations/create_pop', [
                    'as'   => 'create_pop',
                    'uses' => 'Vis\Translations\TranslateController@fetchCreate', ]
                    );
                Route::post('translations/translate', [
                    'as'   => 'translate',
                    'uses' => 'Vis\Translations\TranslateController@doTranslate', ]
                    );
                Route::post('translations/add_record', [
                    'as'   => 'add_record',
                    'uses' => 'Vis\Translations\TranslateController@doSaveTranslate', ]
                    );
                Route::post('translations/change_text_lang', [
                    'as'   => 'change_text_lang',
                    'uses' => 'Vis\Translations\TranslateController@doSavePhrase', ]
                    );
                Route::post('translations/del_record', [
                    'as'   => 'del_record',
                    'uses' => 'Vis\Translations\TranslateController@doDelelePhrase', ]
                    );
                Route::post('translations/create_js_file', [
                        'as'   => 'create_js_file',
                        'uses' => 'Vis\Translations\TranslateController@createdJsFile', ]
                );
            }
        });
});

Route::group(
    ['prefix' => LaravelLocalization::setLocale(), 'middleware' => 'web'],
    function () {
        Route::post('auto_translate', 'Vis\Translations\TranslateController@doTranslatePhraseInJs')
            ->name('auto_translate');
    });
