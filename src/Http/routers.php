<?php

Route::group (['middleware' => ['web']], function () {
    
    Route::group (
        ['prefix' => 'admin', 'middleware' => 'auth.admin'],
        function () {
                Route::any('translations/phrases', array(
                        'as' => 'phrases_all',
                        'uses' => 'Vis\Translations\TranslateController@fetchIndex')
                );
            
                if (Request::ajax()) {
                    Route::post('translations/create_pop', array(
                            'as' => 'create_pop',
                            'uses' => 'Vis\Translations\TranslateController@fetchCreate')
                    );
                    Route::post('translations/translate', array(
                            'as' => 'translate',
                            'uses' => 'Vis\Translations\TranslateController@doTranslate')
                    );
                    Route::post('translations/add_record', array(
                            'as' => 'add_record',
                            'uses' => 'Vis\Translations\TranslateController@doSaveTranslate')
                    );
                    Route::post('translations/change_text_lang', array(
                            'as' => 'change_text_lang',
                            'uses' => 'Vis\Translations\TranslateController@doSavePhrase')
                    );
                    Route::post('translations/del_record', array(
                            'as' => 'del_record',
                            'uses' => 'Vis\Translations\TranslateController@doDelelePhrase')
                    );
                }

            });
});
