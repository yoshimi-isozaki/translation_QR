<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            // シートセッション
            $table->biginteger('seat_session_id')->unsigned()->index();
            // 税込み金額
            $table->integer('tax_included_price');

            // ステータス（=リスト登録中（注文前）, preparation=準備中, printing=プリント中, afterpaying=清算後）
            $table->enum('payment_state', ['preparation', 'printing','afterpaying'])->default('preparation');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
