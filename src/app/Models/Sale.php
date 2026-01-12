<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale  extends Model
{
    protected $table = 'sales';

    public $timestamps = false;

    protected $fillable = [
        'collaborator_id',
        'client_name',
        'amount',
        'client_first_payment_date',
        'created_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'client_first_payment_date' => 'date',
        'created_at' => 'datetime',
    ];

    public function collaborator(): BelongsTo
    {
        return $this->belongsTo(Collaborator::class);
    }

    public function installments(): HasMany
    {
        return $this->hasMany(CommissionInstallment::class);
    }
}
