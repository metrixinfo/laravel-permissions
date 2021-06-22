<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Add user_group_id column to users.
 */
class AddUsersUserGroupIdColumn extends Migration
{
    private $table_name = 'users';
    private $group_column_name = 'user_group_id';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (!Schema::hasColumn($this->table_name, $this->group_column_name)) {
            Schema::disableForeignKeyConstraints();
            Schema::table($this->table_name, function (Blueprint $table) {
                $table->foreignId($this->group_column_name)->nullable();
                $table->foreign('user_group_id')
                    ->references('id')
                    ->on('user_groups')
                    ->nullOnDelete()
                    ->cascadeOnUpdate();
            });
            Schema::enableForeignKeyConstraints();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        if (Schema::hasColumn($this->table_name, $this->group_column_name)) {
            Schema::disableForeignKeyConstraints();
            Schema::table($this->table_name, function (Blueprint $table) {
                $table->dropColumn($this->group_column_name);
            });
            Schema::enableForeignKeyConstraints();
        }
    }
}
