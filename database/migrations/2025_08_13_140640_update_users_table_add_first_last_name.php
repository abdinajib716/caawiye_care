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
            // Check and drop the old name field if it exists
            if (Schema::hasColumn('users', 'name')) {
                $table->dropColumn('name');
            }

            // Add first_name and last_name if they don't exist
            if (!Schema::hasColumn('users', 'first_name')) {
                $table->string('first_name')->after('id');
            }
            if (!Schema::hasColumn('users', 'last_name')) {
                $table->string('last_name')->after('first_name');
            }

            // Add avatar_id if it doesn't exist
            if (!Schema::hasColumn('users', 'avatar_id')) {
                $table->unsignedBigInteger('avatar_id')->nullable()->after('username');
                // Note: media table might not exist yet, so skip foreign key for now
                // $table->foreign('avatar_id')->references('id')->on('media')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Revert changes - add back the name field
            $table->dropForeign(['avatar_id']);

            $table->string('name')->after('id');

            $table->dropColumn(['first_name', 'last_name', 'avatar_id']);
        });
    }
};
