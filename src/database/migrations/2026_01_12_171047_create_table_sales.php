<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();

            $table->foreignId('collaborator_id')
                ->constrained('collaborators')
                ->cascadeOnDelete();

            $table->string('client_name');
            $table->decimal('amount', 10, 2); // valor da venda (R$)
            $table->date('client_first_payment_date');

            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
