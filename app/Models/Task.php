<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];
    // protected $fillable = [
    //     'user_id','category_id','title','description','status','priority',
    //     'ai_priority_score','ai_reasoning','due_date','tags','completed_at',
    // ];


    protected $casts = [
        'due_date'     => 'date',
        'completed_at' => 'datetime',
        'tags'         => 'array',
    ];


    public function scopePending($q)   { return $q->where('status', 'pending'); }
    public function scopeCompleted($q) { return $q->where('status', 'completed'); }
    public function scopeOverdue($q) {
        return $q->pending()->whereNotNull('due_date')->where('due_date', '<', now());
    }
    public function isOverdue(): bool {
        return $this->due_date && $this->due_date->isPast()
            && !in_array($this->status, ['completed', 'cancelled']);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

}
