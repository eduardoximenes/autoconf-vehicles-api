<?php

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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('license_plate', 8)->unique()->comment('Brazil Format: ABC1D23');
            $table->string('chassis', 17)->unique();
            $table->string('brand');
            $table->string('model');
            $table->string('version');
            $table->decimal('sale_price', 15, 2);
            $table->string('color');
            $table->integer('km')->unsigned()->default(0);
            $table->enum('transmission', ['manual', 'automatic']);
            $table->enum('fuel_type', ['gasoline', 'ethanol', 'flex', 'diesel', 'hybrid', 'electric']);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
