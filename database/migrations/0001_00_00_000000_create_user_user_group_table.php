<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * CreateUserRolesTable
 */
class CreateUserUserGroupTable extends Migration
{
    private $table_name = 'user_user_group';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();
        if (! Schema::hasTable($this->table_name)) {
            Schema::create($this->table_name, function (Blueprint $table) {
                $table->foreignId('user_id');
                $table->foreignId('user_group_id');
                $table->primary(['user_id', 'user_group_id']);
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->cascadeOnDelete()
                    ->cascadeOnUpdate();
                $table->foreign('user_group_id')
                    ->references('id')
                    ->on('user_groups')
                    ->cascadeOnDelete()
                    ->cascadeOnUpdate();
            });
        }
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists($this->table_name);
        Schema::enableForeignKeyConstraints();
    }
}
