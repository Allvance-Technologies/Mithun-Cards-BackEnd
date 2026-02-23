<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Subcategory extends Model
{
    protected $fillable = ['card_type_id', 'name', 'slug', 'description'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($sub) {
            $sub->slug = Str::slug($sub->name);
        });
        static::updating(function ($sub) {
            $sub->slug = Str::slug($sub->name);
        });
    }

    public function cardType()
    {
        return $this->belongsTo(CardType::class);
    }

    public function inventoryItems()
    {
        return $this->hasMany(InventoryItem::class);
    }
}
