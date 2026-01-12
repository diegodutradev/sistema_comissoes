<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Installment  extends Model
{
    protected $table = 'installments';

    public $timestamps = false;

    protected $fillable = [
        'sale_id',
        'index',
        'client_due_date',
        'client_paid',
        'client_paid_date',
        'collaborator_receipt_date',
        'collaborator_paid',
        'collaborator_paid_date',
        'amount',
    ];

    protected $casts = [
        'index' => 'integer',
        'client_paid' => 'boolean',
        'collaborator_paid' => 'boolean',
        'client_due_date' => 'date',
        'client_paid_date' => 'date',
        'collaborator_receipt_date' => 'date',
        'collaborator_paid_date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Installment belongs to a sale
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }
}
