<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaravelAuthorizationTables extends Migration
{
    public function up(): void
    {
        $tables = config('authorization.tables');

        Schema::create($tables['role'], $this->getGrantableStructure());
        Schema::create($tables['permission'], $this->getGrantableStructure());
        Schema::create($tables['user_roles'], $this->getAuthorizationsStructure(str_singular($tables['role'])));
        Schema::create($tables['user_permissions'], $this->getAuthorizationsStructure(str_singular($tables['permission'])));

        Schema::create($tables['role_has_many_permissions'], function (Blueprint $table) use ($tables) {
            $table->unsignedInteger('role_id');
            $table->foreign('role_id')->references('id')->on($tables['role']);

            $table->unsignedInteger('permission_id');
            $table->foreign('permission_id')->references('id')->on($tables['permission']);

            $table->primary(['role_id', 'permission_id']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        $tables = config('authorization.tables');

        Schema::dropIfExists($tables['role_has_many_permissions']);
        Schema::dropIfExists($tables['permission']);
        Schema::dropIfExists($tables['role']);
        Schema::dropIfExists($tables['authorizable_group']);
    }

    private function getGrantableStructure()
    {
        return function (Blueprint $table) {
            $table->increments('id');
            $table->string('secret_name', 60)->unique();
            $table->string('display_name', 60);
            $table->string('description')->nullable();
            $table->timestamps();
        };
    }

    private function getAuthorizationsStructure($tableName)
    {
        return function (Blueprint $table) use ($tableName) {
            $table->increments('id');

            $table->unsignedInteger('authorizable_id');
            $table->string('authorizable_type', 100);
            $table->index(['authorizable_id', 'authorizable_type']);

            $table->unsignedInteger("{$tableName}_id");
            $table->foreign("{$tableName}_id")->references('id')->on(str_plural($tableName));

            $table->timestamps();
        };
    }
}
