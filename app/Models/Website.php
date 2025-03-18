<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use App\Models\Chat;
use App\Models\WidgetButton;

class Website extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'url',
        'api_key',
        'assistant_id',
        'model_name',
        'description',
        'welcome_message',
        'is_active',
        'widget_color',
        'widget_position',
        'website_type',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];
    
    /**
     * Get the admins for the website.
     */
    public function admins(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'website_admins')
            ->withTimestamps();
    }
    
    /**
     * Get the agents for the website.
     */
    public function agents(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'website_agents')
            ->withTimestamps();
    }
    
    /**
     * Get the chats for the website.
     */
    public function chats(): HasMany
    {
        return $this->hasMany(Chat::class);
    }
    
    /**
     * Get the active chats for the website.
     */
    public function activeChats()
    {
        return $this->chats()->whereIn('status', ['open', 'human_transfer_requested']);
    }
    
    /**
     * Get the closed chats for the website.
     */
    public function closedChats()
    {
        return $this->chats()->whereIn('status', ['ai_closed', 'ai_trial_scheduled', 'closed', 'trial_scheduled']);
    }
    
    /**
     * Get the buttons for the website.
     */
    public function buttons()
    {
        return $this->hasMany(WidgetButton::class)->orderBy('display_order');
    }
}
