<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        // First add the temporary column
        Schema::table('service_user', function (Blueprint $table) {
            $table->string('old_role')->nullable();
        });

        // Now we can safely backup the existing roles
        DB::statement('UPDATE service_user SET old_role = role');

        // Then modify the table structure
        Schema::table('service_user', function (Blueprint $table) {
            $table->dropColumn('role');
            $table->unsignedBigInteger('role_id')->nullable();
            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->onDelete('set null');
        });

        // Migrate existing roles to the new structure
        $this->migrateExistingRoles();
    }

    public function down()
    {
        // First add back the role column
        Schema::table('service_user', function (Blueprint $table) {
            $table->string('role')->nullable();
        });

        // Restore the old roles
        DB::statement('UPDATE service_user SET role = old_role');

        // Then clean up the new structure
        Schema::table('service_user', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
            $table->dropColumn('old_role');
        });
    }
    private function migrateExistingRoles()
    {
        $roleMapping = [
            'owner' => 'service-owner',
            'manager' => 'service-manager',
            'member' => 'service-member'
        ];

        foreach ($roleMapping as $oldRole => $newRole) {
            DB::statement("
            UPDATE service_user su
            INNER JOIN roles r ON r.name = ?
            SET su.role_id = r.id
            WHERE su.old_role = ?
        ", [$newRole, $oldRole]);
        }

        // Handle any remaining null role_ids with default member role
        DB::statement("
        UPDATE service_user su
        INNER JOIN roles r ON r.name = 'service-member'
        SET su.role_id = r.id
        WHERE su.role_id IS NULL AND su.old_role IS NOT NULL
    ");
    }
};