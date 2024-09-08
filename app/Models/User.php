<?php

namespace Modules\Auth\Models;

use App\Concerns\HasOrganization;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Scout\Searchable;
use Modules\Address\Concerns\HasAddresses;
use Modules\Auth\Concerns\HasEmail;
use Modules\Auth\Concerns\HasPhones;
use Modules\SocialMedia\Models\Workspace;
use Modules\Subscription\Traits\HasPlanSubscriptions;
use Modules\System\Concerns\HasSettings;
use Spatie\Permission\Traits\HasRoles;
use Turahe\Media\HasMedia;

/**
 * @property string $id
 * @property string $username
 * @property string|null $phone
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property \Illuminate\Support\Carbon|null $phone_verified_at
 * @property mixed $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Address\Models\Address> $addresses
 * @property-read int|null $addresses_count
 * @property-read mixed $alias
 * @property-read mixed $avatar
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Auth\Models\Model\Email> $emails
 * @property-read int|null $emails_count
 * @property-read mixed $full_name
 * @property-read \Kalnoy\Nestedset\Collection<int, \App\Models\Organization> $managedOrganization
 * @property-read int|null $managed_organization_count
 * @property-read \Kalnoy\Nestedset\Collection<int, \Modules\System\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Kalnoy\Nestedset\Collection<int, \App\Models\Organization> $organizations
 * @property-read int|null $organizations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Auth\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Auth\Models\Model\Phone> $phones
 * @property-read int|null $phones_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravelcm\Subscriptions\Models\Subscription> $planSubscriptions
 * @property-read int|null $plan_subscriptions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Auth\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\System\Models\Setting> $settings
 * @property-read int|null $settings_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 *
 * @method static \Modules\Auth\Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User ofManager(\App\Models\User $user, $withCurrentUser = true)
 * @method static \Illuminate\Database\Eloquent\Builder|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePhoneVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|User withoutRole($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|User withoutTrashed()
 *
 * @mixin \Eloquent
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasAddresses;
    use HasApiTokens;
    use HasEmail;
    use HasFactory, Notifiable;
    use HasMedia;
    use HasOrganization;
    use HasPhones;
    use HasPlanSubscriptions;
    use HasRoles;
    use HasSettings;
    use HasUlids;
    use Searchable;
    use SoftDeletes;

    protected $dateFormat = 'U';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'phone',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * @var string[]
     */
    protected $defaultSettings = [
        'language',
        'timezone',
        'datetime',
    ];

    // settings rules
    /**
     * @var array|string[]
     */
    public array $settingsRules = [
        'datetime' => 'date',
        'language' => 'string|exists:tm_languages,code',
        'timezone' => 'timezone:all',
    ];

    public function toSearchableArray()
    {
        return [
            'username' => $this->username,
            'email' => $this->email,
            'phone' => $this->phone,
            'created_at' => $this->created_at,

        ];
    }

    /**
     * Get the URL to the user's profile photo.
     */
    public function avatar(): Attribute
    {
        return Attribute::make(get: fn () => $this->hasMedia('photo')
            ? $this->getMediaUrl('photo')
            : $this->defaultProfilePhotoUrl())->shouldCache();
    }

    /**
     * Get the default profile photo URL if no profile photo has been uploaded.
     */
    protected function defaultProfilePhotoUrl(): string
    {
        return 'https://ui-avatars.com/api/?name='.urlencode($this->alias).'&color=7F9CF5&background=EBF4FF';
    }

    protected function alias(): Attribute
    {
        return Attribute::get(fn () => name_alias($this->fullName));

    }

    /**
     * Set the full name of user
     */
    protected function fullName(): Attribute
    {
        return Attribute::make(get: fn () => $this->people ? "{$this->people->full_name}" : $this->username)->shouldCache();
    }

    public function setActiveWorkspace(Workspace $workspace): void
    {
        $this->settings()->updateOrCreate(
            [
                'name' => 'active_workspace',
                'user_id' => $this->id,
            ],
            ['payload' => $workspace->id]
        );
    }

    public function getActiveWorkspace()
    {
        $workspaceId = $this->settings()
            ->where('name', 'active_workspace')
            ->value('payload');

        if (! $workspaceId) {
            return null;
        }

        return $this->workspaces()->where('workspace_id', $workspaceId)->first();
    }

    protected static function newFactory()
    {
        return \Modules\Auth\Database\Factories\UserFactory::new();
    }
}
