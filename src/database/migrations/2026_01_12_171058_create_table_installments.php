<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('installments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sale_id')
                ->constrained('sales')
                ->cascadeOnDelete();

            $table->unsignedInteger('index'); // 1, 2 ou 3

            $table->date('client_due_date');
            $table->boolean('client_paid')->default(false);
            $table->date('client_paid_date')->nullable();

            $table->date('collaborator_receipt_date')->nullable();
            $table->boolean('collaborator_paid')->default(false);
            $table->date('collaborator_paid_date')->nullable();

            $table->decimal('amount', 10, 2); // valor da comissão
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('installments');
    }
};
