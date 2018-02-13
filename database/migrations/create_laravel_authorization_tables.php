<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaravelAuthorizationTables extends Migration
{
    public function up(): void
    {
        $tables = config('authorization.tables');

        Schema::create($tables['authorizations'], function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('authorized_id');
            $table->string('authorized_type', 100);
            $table->index(['authorized_id', 'authorized_type']);

            $table->unsignedInteger('grantable_id');
            $table->string('grantable_type', 25);
            $table->index(['grantable_id', 'authorizable_type']);

            $table->timestamps();
        });

        Schema::create($tables['role'], $this->getAuthorizableStructure());

        Schema::create($tables['permission'], $this->getAuthorizableStructure());

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

    private function getAuthorizableStructure()
    {
        return function (Blueprint $table) {
            $table->increments('id');
            $table->string('secret_name', 60)->unique();
            $table->string('display_name', 60);
            $table->string('description')->nullable();
            $table->timestamps();
        };
    }
}
