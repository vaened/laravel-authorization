<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Vaened\Authorization\Configuration\Tables;

return new class extends Migration {
    public function up(): void
    {
        Schema::create(Tables::roles(), self::authorization());
        Schema::create(Tables::permissions(), self::authorization());

        Schema::create(Tables::rolePermissions(), function (Blueprint $table): void {
            $table->foreignId('role_id')
                  ->constrained(Tables::roles())
                  ->cascadeOnDelete();

            $table->foreignId('permission_id')
                  ->constrained(Tables::permissions())
                  ->cascadeOnDelete();

            $table->primary(['role_id', 'permission_id']);
        });

        Schema::create(Tables::subjectRoles(), function (Blueprint $table): void {
            $table->foreignId('role_id')
                  ->constrained(Tables::roles())
                  ->cascadeOnDelete();

            $table->morphs('authorizable');

            $table->primary(['role_id', 'authorizable_type', 'authorizable_id']);
        });

        Schema::create(Tables::subjectPermissions(), function (Blueprint $table): void {
            $table->foreignId('permission_id')
                  ->constrained(Tables::permissions())
                  ->cascadeOnDelete();

            $table->morphs('authorizable');
            $table->boolean('denied')->default(false);

            $table->primary(['permission_id', 'authorizable_type', 'authorizable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(Tables::subjectPermissions());
        Schema::dropIfExists(Tables::subjectRoles());
        Schema::dropIfExists(Tables::rolePermissions());
        Schema::dropIfExists(Tables::permissions());
        Schema::dropIfExists(Tables::roles());
    }

    protected static function authorization(): callable
    {
        return static function (Blueprint $table): void {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
        };
    }
};
