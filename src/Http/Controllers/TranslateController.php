<?php

namespace Vis\Translations;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class TranslateController extends Controller
{
    /**
     * get index page in admin.
     *
     * @return Illuminate\Support\Facades\View
     */
    public function fetchIndex()
    {
        if (request('search_q') && mb_strlen(request('search_q')) > 1) {
            return $this->doSearch();
        }

        $countShow = request('count_show') ?: config('translations.config.show_count')[0];
        $allpage = Trans::orderBy('id', 'desc');
        $allpage = $allpage->paginate($countShow);

        $view = Request::ajax() ? 'translations::part.table_center' : 'translations::trans';

        $langs = config('translations.config.alt_langs');

        return view($view)
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
        $querySearch = trim(request('search_q'));
        $langs = config('translations.config.alt_langs');

        $allPage = Trans::leftJoin('translations', 'translations.id_translations_phrase', '=', 'translations_phrases.id')
            ->select('translations_phrases.*')
            ->where(function ($query) use ($querySearch) {
                $query->where('phrase', 'like', '%'.$querySearch.'%')
                    ->orWhere('translations.translate', 'like', '%'.$querySearch.'%');
            })

            ->groupBy('translations_phrases.id')
            ->orderBy('translations_phrases.id', 'desc')->paginate(20);

        return view('translations::part.result_search', compact('allPage', 'langs'));
    }

    /**
     * get popup create new phrase.
     *
     * @return Illuminate\Support\Facades\View
     */
    public function fetchCreate()
    {
        $langs = config('translations.config.alt_langs');

        return view('translations::part.form_trans', compact('langs'));
    }

    /**
     * do create new translation.
     *
     * @return json Response
     */
    public function doSaveTranslate()
    {
        parse_str(request('data'), $data);

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

        $langs = config('translations.config.alt_langs');

        foreach ($data as $k => $el) {
            if (in_array($k, $langs) && $el && $model->id) {
                Translate::create([
                    'id_translations_phrase' => $model->id,
                    'lang' => $k,
                    'translate' => trim($el),
                ]);
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
        Trans::find(request('id'))->delete();

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
        $lang = request('name');
        $phrase = request('value');
        $id = request('pk');

        if ($id && $phrase && $lang) {
            $phraseChange = Translate::where('id_translations_phrase', $id)->where('lang', $lang)->first();
            if (isset($phraseChange->id)) {
                $phraseChange->translate = $phrase;
                $phraseChange->save();
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

    public function getJs($lang, $withoutHeader = false)
    {
        $data = Trans::fillCacheTrans();

        $translates = [];
        foreach ($data as $phrase => $translate) {
            $key = trim(str_replace(["\r\n", "\r", "\n"], '', $phrase));
            $value = trim(isset($translate[$lang]) ? str_replace(["\r\n", "\r", "\n"], '', $translate[$lang]) : '');
            $translates[$key] = $value;
        }

        if ($withoutHeader) {
            return view('translations::js', compact('data', 'lang', 'translates'))->render();
        }

        return response()
            ->view('translations::js', compact('data', 'lang', 'translates'), 200)
            ->header('Content-Type', 'text/javascript');
    }

    public function doTranslatePhraseInJs()
    {
        return __t(request('phrase'));
    }

    public function createdJsFile()
    {
        if (!is_dir(public_path('/js'))) {
            mkdir(public_path('/js'), 0755, true);
        }

        foreach (config('translations.config.languages') as $lang) {

            $content = $this->getJs($lang['caption'], true);

            echo route( 'auto_translate').'<br>';

            file_put_contents(public_path('/js/translation_'.$lang['caption'].'.js'), $content);
        }
    }
}
