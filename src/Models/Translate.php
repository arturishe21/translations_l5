<?php namespace Vis\Translations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;

class Translate extends Model {

    protected $table = 'translations';

    public $timestamps = false;

    protected $fillable = array('id_translations_phrase', 'lang', 'translate');

}