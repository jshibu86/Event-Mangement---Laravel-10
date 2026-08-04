<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Support\Facades\DB;
return new class extends Migration { public function up(): void { DB::table('event_contributions')->orderBy('id')->get()->each(function($contribution){$amount=DB::table('events')->where('id',$contribution->event_id)->value('per_head_amount');if((float)$contribution->amount_due===0.0 && $amount !== null){DB::table('event_contributions')->where('id',$contribution->id)->update(['amount_due'=>$amount,'updated_at'=>now()]);}}); } public function down(): void {} };
