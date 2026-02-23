<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CardType extends Model
{
    protected $fillable = ['name', 'slug', 'description'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($cardType) {
            $cardType->slug = Str::slug($cardType->name);
        });
        static::updating(function ($cardType) {
            $cardType->slug = Str::slug($cardType->name);
        });
    }

    public function subcategories()
    {
        return $this->hasMany(Subcategory::class);
    }

    public function inventoryItems()
    {
        return $this->hasManyThrough(InventoryItem::class, Subcategory::class);
    }
}
