<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Translatable;

class Genre extends Model
{
    use Translatable;

    public static $rules = [
        'genre_key' => 'required|max:60',
        'genre_name' => 'required|max:60',
        'lang' => 'required',
    ];

    protected $fillable = [
        'genre_key',
        'genre_name',
        'lang',
        'genre_order',
        'parent_id',
    ];

    public function items()
    {
        return $this->hasMany('App\Item');
    }
    public function parent()
    {
        return $this->belongsTo('App\Genre', 'parent_id');
    }
    public function children()
    {
        return $this->hasMany('App\Genre', 'parent_id')->orderBy('genre_order', 'ASC');
    }

    public static function optionsForSelect()
    {
        $ret = [];
        self::orderBy('genre_order', 'ASC')->each(function($genre) use(&$ret){
            $ret[$genre->id] = $genre->genre_name. "(". $genre->genre_key .")". "[". $genre->lang_jp ."]";
        });
        return $ret;
    }

    public static function optionsForSelectParents()
    {
        $ret = [] ;
        $ret[''] = '階層構造にする場合は選択してください';
        self::whereNull('parent_id')->orderBy('genre_order', 'ASC')->each(function($genre) use(&$ret){
            $ret[$genre->id] = $genre->genre_name. "(". $genre->genre_key .")". "[". $genre->lang_jp ."]";
        });
        return $ret;
    }

    public static function optionsForSelectParentsByLang($lang)
    {
        $ret = [] ;
        self::where('lang', $lang)->whereNull('parent_id')->orderBy('genre_order', 'ASC')->each(function($genre) use(&$ret){
            $ret[$genre->id] = $genre->genre_name. "(". $genre->genre_key .")". "[". $genre->lang_jp ."]";
        });
        return $ret;
    }
}
