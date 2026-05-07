<?php

use App\AssetCondition;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->datetime('loaned_at');
            $table->datetime('returned_at')->nullable();
            $table->string('condition_on_delivery')->default(AssetCondition::Excelent->value)->nullable();
            $table->string('condition_on_return')->nullable();
            $table->boolean('is_active');
            $table->text('notes')->nullable();
            $table->foreignId('asset_id')->constrained()->restrictOnDelete();
            $table->foreignId('employee_id')->constrained()->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
