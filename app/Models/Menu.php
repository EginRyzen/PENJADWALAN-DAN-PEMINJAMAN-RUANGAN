<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'menus';

    protected $fillable = [
        'menu_code',
        'menu_name',
        'menu_id_alias',
        'menu_desc',
        'sequence',
        'parent_id',
        'is_desktop',
        'is_mobile',
    ];

    /**
     * Relasi ke Induk Menu (Parent)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    /**
     * Relasi ke Anak Menu (Children)
     * Digunakan untuk menyusun nested JSON
     */
    public function children(): HasMany
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('sequence', 'asc');
    }

    /**
     * Relasi ke Roles melalui tabel pivot role_menus
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_menus', 'menu_id', 'role_id')
                    ->withTimestamps();
    }
}