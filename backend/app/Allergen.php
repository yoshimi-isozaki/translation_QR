<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Translatable;

class Allergen extends Model
{
    use Translatable;

    public static $rules = [
        'allergen_key' => 'required|alpha_dash|max:60',
        'allergen_name' => 'required|max:60',
        'lang' => 'required',
    ];

    protected $fillable = [
        'allergen_key',
        'allergen_name',
        'lang',
    ];

    public static $cacheNames = [
    ];

    public function items(): object
    {
        return $this->belongsToMany('App\Item', 'allergen_item');
    }
    public function getHashAttribute(): int
    {
        return crc32($this->allergen_key);
    }


    public static function nameJpHash(string $key): string
    {
        if (array_key_exists($key , self::$cacheNames)) {
            return self::$cacheNames[$key];
        } else {
            \App\Allergen::where('lang', "ja_JP")->get()->each(function($allergen){
                self::$cacheNames[$allergen->allergen_key] = $allergen->allergen_name;
            });
            return self::$cacheNames[$key];
        }
    }

    public function getNameJpAttribute(): string
    {
        if ($this->lang === "ja_JP") {
            return $this->allergen_name;
        } else {
            return self::nameJpHash($this->allergen_key);
        }
    }

    public function saveOtherLangByKey(): void
    {
        try {
            collect(['en_US', 'zh_CN', 'ko_KR'])->each(function(string $langVal) {
                $new_allergen = new \App\Allergen();
                $new_allergen->fill([
                    'lang' => $langVal,
                    'allergen_name' => '【'. $this->allergen_name. '】の翻訳を入れてください',
                    'allergen_key' => $this->allergen_key,
                ])->save();
            });
        } catch(\Illuminate\Database\QueryException $e) {
            // SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry
        }
    }
}
