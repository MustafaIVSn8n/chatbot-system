<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'chat_id',
        'user_id',
        'sender_type',
        'content',
        'is_read',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_read' => 'boolean',
    ];
    
    /**
     * Get the chat that owns the message.
     */
    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }
    
    /**
     * Get the user that sent the message (if any).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Check if the message was sent by an agent.
     *
     * @return bool
     */
    public function isSentByAgent(): bool
    {
        return $this->sender_type === 'user';
    }
    
    /**
     * Get the sent_by_agent attribute.
     *
     * @return bool
     */
    public function getSentByAgentAttribute(): bool
    {
        return $this->isSentByAgent();
    }
    
    /**
     * Check if the message was sent by the customer.
     *
     * @return bool
     */
    public function isSentByCustomer(): bool
    {
        return $this->sender_type === 'customer';
    }
    
    /**
     * Check if the message was sent by AI.
     *
     * @return bool
     */
    public function isSentByAI(): bool
    {
        return $this->sender_type === 'ai';
    }
}
