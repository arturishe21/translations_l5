<?php

namespace Vis\Translations;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class TranslateController extends Controller
{
    /**
     * get index page in admin.
     *
     * @return Illuminate\Support\Facades\View
     */
    public function fetchIndex()
    {
        if (Input::get('search_q') && mb_strlen(Input::get('search_q')) > 1) {
            return $this->doSearch();
        }

        $countShow = Input::get('count_show') ? Input::get('count_show') : Config::get('translations.config.show_count')[0];
        $allpage = Trans::orderBy('id', 'desc');
        $allpage = $allpage->paginate($countShow);
        $breadcrumb[Config::get('translations.config.title_page')] = '';

        if (Request::ajax()) {
            $view = 'translations::part.table_center';
        } else {
            $view = 'translations::trans';
        }

        $langs = Config::get('translations.config.alt_langs');

        return View::make($view)
            ->with('title', Config::get('translations.config.title_page'))
            ->with('breadcrumb', $breadcrumb)
            ->with('allPage', $allpage)
            ->with('langs', $langs)
            ->with('count_show', $countShow);
    }

    /**
     * do search in list phrase.
     *
     * @return Illuminate\Support\Facades\View
     */
    public function doSearch()
    {
        $querySearch = trim(Input::get('search_q'));
        $langs = Config::get('translations.config.alt_langs');
        $countShow = Input::get('count_show') ? Input::get('count_show') : Config::get('translations.config.show_count')[0];

        $allPage = Trans::where('phrase', 'like', '%'.$querySearch.'%')
            ->orderBy('id', 'desc')->paginate($countShow);

        return View::make('translations::part.result_search', compact('allPage', 'langs'));
    }

    /**
     * get popup create new phrase.
     *
     * @return Illuminate\Support\Facades\View
     */
    public function fetchCreate()
    {
        $langs = Config::get('translations.config.alt_langs');

        return View::make('translations::part.form_trans', compact('langs'));
    }

    /**
     * do create new translation.
     *
     * @return json Response
     */
    public function doSaveTranslate()
    {
        parse_str(Input::get('data'), $data);

        $validator = Validator::make($data, Trans::$rules);
        if ($validator->fails()) {
            return Response::json(
                [
                    'status'          => 'error',
                    'errors_messages' => $validator->messages(),
                ]
            );
        }

        $model = new Trans();
        $model->phrase = strip_tags(str_replace('"', '', trim($data['phrase'])));
        $model->save();

        $langs = Config::get('translations.config.alt_langs');

        foreach ($data as $k => $el) {
            if (in_array($k, $langs) && $el && $model->id) {
                $model_trans = new  Translate();
                $model_trans->translate = trim($el);
                $model_trans->lang = $k;
                $model_trans->id_translations_phrase = $model->id;
                $model_trans->save();
            }
        }

        Trans::reCacheTrans();

        return Response::json(
            [
                'status'      => 'ok',
                'ok_messages' => 'Фраза успешно добавлена',
            ]
        );
    }

    /**
     * delete phrase.
     *
     * @return json Response
     */
    public function doDelelePhrase()
    {
        $id_record = Input::get('id');
        Trans::find($id_record)->delete();

        Trans::reCacheTrans();

        return Response::json(['status' => 'ok']);
    }

    /**
     * save phrase.
     *
     * @return void
     */
    public function doSavePhrase()
    {
        $lang = Input::get('name');
        $phrase = Input::get('value');
        $id = Input::get('pk');

        if ($id && $phrase && $lang) {
            $phrase_change = Translate::where('id_translations_phrase', $id)->where('lang', $lang)->first();
            if (isset($phrase_change->id)) {
                $phrase_change->translate = $phrase;
                $phrase_change->save();
            } else {
                Translate::create(
                    [
                        'id_translations_phrase' => $id,
                        'lang'                   => $lang,
                        'translate'              => $phrase,
                    ]
                );
            }
        }

        Trans::reCacheTrans();
    }

    public function getJs($lang)
    {
        \Debugbar::disable();

        $data = Trans::fillCacheTrans();

        return response()
            ->view('translations::js', compact('data', 'lang'), 200)
            ->header('Content-Type', 'text/javascript');
    }
}
