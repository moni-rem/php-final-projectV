<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (! Schema::hasColumn('bookings', 'ticket_type')) {
                $table->string('ticket_type', 50)->default('Standard')->after('booking_date');
            }

            if (! Schema::hasColumn('bookings', 'unit_price')) {
                $table->decimal('unit_price', 10, 2)->default(0)->after('quantity');
            }

            if (! Schema::hasColumn('bookings', 'total_price')) {
                $table->decimal('total_price', 10, 2)->default(0)->after('unit_price');
            }
        });

        Schema::table('tickets', function (Blueprint $table) {
            if (! Schema::hasColumn('tickets', 'ticket_status')) {
                $table->string('ticket_status', 50)->default('valid')->after('qr_code');
            }

            if (! Schema::hasColumn('tickets', 'checked_in_at')) {
                $table->timestamp('checked_in_at')->nullable()->after('issued_date');
            }
        });

        Schema::table('payments', function (Blueprint $table) {
            if (! Schema::hasColumn('payments', 'currency')) {
                $table->string('currency', 10)->default('USD')->after('paid_amount');
            }

            if (! Schema::hasColumn('payments', 'transaction_reference')) {
                $table->string('transaction_reference', 150)->nullable()->after('currency');
            }

            if (! Schema::hasColumn('payments', 'payment_proof')) {
                $table->string('payment_proof')->nullable()->after('transaction_reference');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            foreach (['ticket_type', 'unit_price', 'total_price'] as $column) {
                if (Schema::hasColumn('bookings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('tickets', function (Blueprint $table) {
            foreach (['ticket_status', 'checked_in_at'] as $column) {
                if (Schema::hasColumn('tickets', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('payments', function (Blueprint $table) {
            foreach (['currency', 'transaction_reference', 'payment_proof'] as $column) {
                if (Schema::hasColumn('payments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
