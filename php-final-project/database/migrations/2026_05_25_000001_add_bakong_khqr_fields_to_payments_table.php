<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('khqr_md5', 100)->nullable()->after('transaction_reference');
            $table->string('khqr_transaction_id', 150)->nullable()->after('khqr_md5');
            $table->string('khqr_external_reference', 150)->nullable()->after('khqr_transaction_id');
            $table->text('khqr_qr_string')->nullable()->after('khqr_external_reference');
            $table->string('khqr_qr_image_url')->nullable()->after('khqr_qr_string');
            $table->timestamp('khqr_checked_at')->nullable()->after('khqr_qr_image_url');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'khqr_md5',
                'khqr_transaction_id',
                'khqr_external_reference',
                'khqr_qr_string',
                'khqr_qr_image_url',
                'khqr_checked_at',
            ]);
        });
    }
};
