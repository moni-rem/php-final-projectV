<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('role_name', 50)->unique();
            $table->timestamp('created_at')->useCurrent();
        });

        DB::table('roles')->insert([
            ['role_name' => 'user'],
            ['role_name' => 'event_owner'],
            ['role_name' => 'admin'],
        ]);

        Schema::table('users', function (Blueprint $table) {
            $table->string('full_name', 150)->nullable()->after('id');
            $table->string('phone', 20)->nullable()->after('password');
            $table->foreignId('role_id')->default(1)->after('phone')->constrained('roles');
        });

        Schema::create('event_categories', function (Blueprint $table) {
            $table->id();
            $table->string('category_name', 100)->unique();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('event_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('status_name', 50)->unique();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('booking_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('status_name', 50)->unique();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('method_name', 50)->unique();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('payment_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('status_name', 50)->unique();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizer_id')->constrained('users');
            $table->foreignId('category_id')->constrained('event_categories');
            $table->foreignId('status_id')->constrained('event_statuses');
            $table->string('slug', 120)->unique();
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->text('about')->nullable();
            $table->json('what_to_expect')->nullable();
            $table->json('important_information')->nullable();
            $table->string('location')->nullable();
            $table->date('event_date');
            $table->time('start_time');
            $table->time('end_time')->nullable();
            $table->unsignedInteger('total_seats');
            $table->decimal('ticket_price', 10, 2);
            $table->string('image')->nullable();
            $table->string('image2')->nullable();
            $table->string('image3')->nullable();
            $table->text('map_url')->nullable();
            $table->timestamps();
        });

        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code', 100)->unique();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('event_id')->constrained('events');
            $table->foreignId('booking_status_id')->constrained('booking_statuses');
            $table->timestamp('booking_date')->useCurrent();
            $table->string('ticket_type', 50)->default('Standard');
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->unique()->constrained('bookings');
            $table->foreignId('payment_method_id')->constrained('payment_methods');
            $table->foreignId('payment_status_id')->constrained('payment_statuses');
            $table->decimal('paid_amount', 10, 2);
            $table->string('currency', 10)->default('USD');
            $table->string('transaction_reference', 150)->nullable();
            $table->string('payment_proof')->nullable();
            $table->timestamp('payment_date')->useCurrent();
            $table->timestamps();
        });

        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings');
            $table->string('ticket_code', 100)->unique();
            $table->text('qr_code')->nullable();
            $table->string('ticket_status', 50)->default('valid');
            $table->timestamp('issued_date')->useCurrent();
            $table->timestamp('checked_in_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('events');
        Schema::dropIfExists('payment_statuses');
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('booking_statuses');
        Schema::dropIfExists('event_statuses');
        Schema::dropIfExists('event_categories');

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('role_id');
            $table->dropColumn(['full_name', 'phone']);
        });

        Schema::dropIfExists('roles');
    }
};
