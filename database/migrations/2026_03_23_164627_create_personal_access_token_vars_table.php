<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('personal_access_token_vars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personal_access_token_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('key', 32)->comment('The key (name) of the variable.');
            $table->string('type', 32)->default('string');
            $table->text('value')->comment('The text representation of the value.');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['personal_access_token_id', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_access_token_vars');
    }
};
