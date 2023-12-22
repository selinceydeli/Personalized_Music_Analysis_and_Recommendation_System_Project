<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('users', function (Blueprint $table) {
            $table->string('theme')->change(); // Change 'theme' to string type
        });
    }

    public function down() {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('theme', ['dark', 'light'])->change(); // Revert back to enum in the down method
        });
    }
};
