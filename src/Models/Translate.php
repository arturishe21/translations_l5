<?php

namespace Vis\Translations;

use Illuminate\Database\Eloquent\Model;

class Translate extends Model
{
    protected $table = 'translations';

    public $timestamps = false;

    protected $fillable = ['id_translations_phrase', 'lang', 'translate'];
}
