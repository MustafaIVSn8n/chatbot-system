<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class Role extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'display_name',
        'description',
    ];
    
    /**
     * Get the users that belong to this role.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
    
    /**
     * Check if the role is super admin.
     *
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        return $this->name === 'super_admin';
    }
    
    /**
     * Check if the role is admin.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->name === 'admin';
    }
    
    /**
     * Check if the role is agent.
     *
     * @return bool
     */
    public function isAgent(): bool
    {
        return $this->name === 'agent';
    }
}
