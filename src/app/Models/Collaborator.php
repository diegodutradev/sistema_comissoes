<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Collaborator extends Model
{
    protected $table = 'collaborators';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Collaborator has many sales
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }
}
