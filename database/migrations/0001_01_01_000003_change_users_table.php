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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedTinyInteger('required2fa')
                ->default(0)
                ->after('password');
            $table->string('google2fa_secret',40)->nullable()
                ->after('required2fa');
            $table->unsignedTinyInteger('google2fa_enabled')->default('0')
                ->after('google2fa_secret');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropColumns('users', ['required2fa', 'google2fa_secret', 'google2fa_enabled']);
    }
};
