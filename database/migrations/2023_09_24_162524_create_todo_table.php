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
        Schema::create('todo', function (Blueprint $table) {
            $table->id();
            $table->integer('pid')->default(0)->comment('Parent ID');
            $table->unsignedBigInteger('uid')->comment('User( Author ) ID');
            $table->string('title', 150);
            $table->text('description');
            $table->boolean('status')->default(false);
            $table->tinyInteger('priority');
            $table->boolean('deleted')->default(false);
            $table->timestamp('createdAt')->useCurrent();
            $table->timestamp('completedAt')->nullable();

            $table->foreign('uid')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('todo', function (Blueprint $table) {
            $table->dropForeign(['uid']);
        });

        Schema::dropIfExists('todo');
    }
};
