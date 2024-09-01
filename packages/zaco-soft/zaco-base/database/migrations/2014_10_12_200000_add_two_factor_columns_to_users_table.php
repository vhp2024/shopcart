<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTwoFactorColumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')
                    ->after('email_verified_at')
                    ->nullable();

            $table->string('username')
                    ->after('name')
                    ->unique();

            $table->string('password_updated_at')
                    ->after('password')
                    ->nullable();
                    
            $table->text('two_factor_secret')
                    ->after('password_updated_at')
                    ->nullable();

            $table->text('user_token')
                ->after('two_factor_secret')
                ->nullable()
                ->unique();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('two_factor_secret', 'email_verified_at', 'name', 'user_token');
        });
    }
}
