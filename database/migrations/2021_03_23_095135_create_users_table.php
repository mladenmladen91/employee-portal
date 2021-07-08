<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->enum('role', ['admin', 'employee','company']);
                $table->string('full_name');
                $table->string('email')->unique();
                $table->string('password')->nullable();
                $table->string('profile_image')->nullable();
                // for employee
                $table->string('phone')->nullable();
                $table->date('birth_year')->nullable();
                $table->text('aditional_info')->nullable();
                $table->bigInteger('gender_id')->unsigned()->nullable();
                $table->boolean('notifications')->default(true);
                $table->boolean('is_active')->default(true);
                // for company
                $table->string('company_activity')->nullable();
                $table->text('company_description')->nullable();
                $table->string('company_video')->nullable();
                $table->integer('employees_number')->nullable();
                $table->string('pib')->nullable();
                $table->string('pdv')->nullable();
                $table->bigInteger('package_id')->unsigned()->nullable();
                // for both
                $table->bigInteger('country_id')->unsigned()->nullable();
                $table->bigInteger('city_id')->unsigned()->nullable();
                $table->string('address')->nullable();
                $table->integer('zip_code')->nullable();
                $table->rememberToken();
                $table->timestamp('email_verified_at')->nullable();
                $table->timestamps();
                $table->bigInteger('created_by')->unsigned()->nullable();
                $table->bigInteger('modified_by')->unsigned()->nullable();

                // foreign keys
                $table->foreign('gender_id')
                    ->references('id')
                    ->on('genders')
                    ->onDelete('cascade');
                $table->foreign('package_id')
                    ->references('id')
                    ->on('packages')
                    ->onDelete('cascade');
                $table->foreign('country_id')
                    ->references('id')
                    ->on('countries')
                    ->onDelete('cascade');
                $table->foreign('city_id')
                    ->references('id')
                    ->on('cities')
                    ->onDelete('cascade');
                $table->foreign('created_by')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
                $table->foreign('modified_by')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
            });

            Schema::table('genders', function($table){
                $table->bigInteger('created_by')->unsigned()->nullable();
                $table->bigInteger('modified_by')->unsigned()->nullable();

                $table->foreign('created_by')
                        ->references('id')
                        ->on('users')
                        ->onDelete('cascade');
                $table->foreign('modified_by')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
            });
            Schema::table('packages', function($table){
                $table->bigInteger('created_by')->unsigned()->nullable();
                $table->bigInteger('modified_by')->unsigned()->nullable();

                $table->foreign('created_by')
                        ->references('id')
                        ->on('users')
                        ->onDelete('cascade');
                $table->foreign('modified_by')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
            });
            Schema::table('cities', function($table){
                $table->bigInteger('created_by')->unsigned()->nullable();
                $table->bigInteger('modified_by')->unsigned()->nullable();

                $table->foreign('created_by')
                        ->references('id')
                        ->on('users')
                        ->onDelete('cascade');
                $table->foreign('modified_by')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
            });
            Schema::table('countries', function($table){
                $table->bigInteger('created_by')->unsigned()->nullable();
                $table->bigInteger('modified_by')->unsigned()->nullable();

                $table->foreign('created_by')
                        ->references('id')
                        ->on('users')
                        ->onDelete('cascade');
                $table->foreign('modified_by')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
