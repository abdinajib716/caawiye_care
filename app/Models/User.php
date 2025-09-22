<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\QueryBuilderTrait;
use App\Notifications\AdminResetPasswordNotification;
use App\Concerns\AuthorizationChecker;
use App\Observers\UserObserver;
use Illuminate\Auth\Notifications\ResetPassword as DefaultResetPassword;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

#[ObservedBy([UserObserver::class])]
class User extends Authenticatable
{
    use AuthorizationChecker;
    use HasApiTokens;
    use HasFactory;
    use HasRoles;
    use Notifiable;
    use QueryBuilderTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'username',
        // 'avatar_id', // Removed - Media Library removed for healthcare application
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The attributes that should be appended to the model.
     */
    protected $appends = [
        'avatar_url',
        'full_name',
    ];

    /**
     * The relationships that should be eager loaded.
     * Removed: avatar relationship - Media Library removed for healthcare application
     */
    protected $with = [
        // 'avatar', // Removed with Media Library
    ];

    public function actionLogs()
    {
        return $this->hasMany(ActionLog::class, 'action_by');
    }

    /**
     * Get the user's metadata.
     */
    public function userMeta()
    {
        return $this->hasMany(UserMeta::class);
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     */
    public function sendPasswordResetNotification($token): void
    {
        // Check if the request is for the admin panel
        if (request()->is('admin/*')) {
            $this->notify(new AdminResetPasswordNotification($token));
        } else {
            $this->notify(new DefaultResetPassword($token));
        }
    }

    /**
     * Check if the user has any of the given permissions.
     *
     * @param  array|string  $permissions
     */
    public function hasAnyPermission($permissions): bool
    {
        if (empty($permissions)) {
            return true;
        }

        $permissions = is_array($permissions) ? $permissions : [$permissions];

        foreach ($permissions as $permission) {
            if ($this->can($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the user's avatar URL.
     * Simplified version without Media Library dependency.
     */
    public function getAvatarUrlAttribute(): string
    {
        // For healthcare application, we'll use Gravatar only
        // Avatar uploads can be implemented later with a simpler approach
        return $this->getGravatarUrl();
    }

    /**
     * Get the Gravatar URL for the model's email.
     */
    public function getGravatarUrl(int $size = 80): string
    {
        return "https://ui-avatars.com/api/{$this->full_name}/{$size}/635bff/fff/2";
    }

    /**
     * Get the user's full name.
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }
}
