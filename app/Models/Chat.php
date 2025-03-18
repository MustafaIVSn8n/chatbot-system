<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chat extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'website_id',
        'assigned_agent_id',
        'thread_id',
        'status',
        'customer_name',
        'customer_email',
        'customer_phone',
        'student_grade',
        'student_age',
        'class_days',
        'class_time',
        'start_date',
        'subjects',
        'session',
        'human_transfer_requested',
        'last_activity_at',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'human_transfer_requested' => 'boolean',
        'last_activity_at' => 'datetime',
        'start_date' => 'date',
    ];
    
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['unread_count', 'needs_human']; // Added needs_human
    
    /**
     * Get the unread message count for this chat.
     *
     * @return int
     */
    public function getUnreadCountAttribute()
    {
        return $this->messages()
            ->where('is_read', false)
            ->where('sender_type', 'customer')
            ->count();
    }
    
    /**
     * Alias needs_human to human_transfer_requested for compatibility.
     *
     * @return bool
     */
    public function getNeedsHumanAttribute()
    {
        return $this->human_transfer_requested;
    }
    
    /**
     * Get the website that owns the chat.
     */
    public function website(): BelongsTo
    {
        return $this->belongsTo(Website::class);
    }
    
    /**
     * Get the agent assigned to the chat.
     */
    public function assignedAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_agent_id');
    }
    
    /**
     * Get the messages for the chat.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
    
    /**
     * Get the notifications for the chat.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }
    
    /**
     * Get the email logs for the chat.
     */
    public function emailLogs(): HasMany
    {
        return $this->hasMany(EmailLog::class);
    }
    
    /**
     * Check if the chat is open.
     *
     * @return bool
     */
    public function isOpen(): bool
    {
        return $this->status === 'open';
    }
    
    /**
     * Check if the chat is closed.
     *
     * @return bool
     */
    public function isClosed(): bool
    {
        return in_array($this->status, ['ai_closed', 'closed']);
    }
    
    /**
     * Check if the chat has a trial scheduled.
     *
     * @return bool
     */
    public function hasTrialScheduled(): bool
    {
        return in_array($this->status, ['ai_trial_scheduled', 'trial_scheduled']);
    }
    
    /**
     * Check if the chat needs human attention.
     *
     * @return bool
     */
    public function needsHumanAttention(): bool
    {
        return $this->status === 'open' && $this->human_transfer_requested;
    }
}