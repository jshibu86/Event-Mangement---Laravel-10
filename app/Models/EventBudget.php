<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model; class EventBudget extends Model { protected $guarded=[]; protected $casts=['total_budget'=>'decimal:2']; }
