<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Menu extends Model
{
    use HasFactory;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['name', 'parent_id', 'depth'];

    protected static function boot()
    {
        parent::boot();

        // Automatically generate a UUID for the 'id' field before creating a new record
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = Str::uuid();
            }
        });
    }

    /**
     * Root Menus
     *
     * @param Builder $query
     * @return void
     */
    public function scopeRootMenus(Builder $query): void
    {
        $query->whereNull('parent_id');
    }


    public function children(): HasMany
    {
        return $this->hasMany(Menu::class, 'parent_id')->chaperone();
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Menu::class)->with('parent');
    }
}
