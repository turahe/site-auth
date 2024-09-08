<?php

namespace Modules\Auth\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $user_id
 * @property string $name
 * @property string $token
 * @property \Illuminate\Support\Carbon|null $last_used_at
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Modules\Auth\Models\User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|UserToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserToken query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserToken whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserToken whereLastUsedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserToken whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserToken whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserToken whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserToken whereUserId($value)
 *
 * @mixin \Eloquent
 */
class UserToken extends Model
{
    use HasUlids;

    public $table = 'user_tokens';

    protected $dateFormat = 'U';

    protected $fillable = [
        'name',
        'token',
        'expires_at',
    ];

    protected $casts = [
        'last_used_at' => 'datetime',
        'expires_at' => 'date',
    ];

    protected $hidden = [
        'token',
    ];

    public static function findToken($token)
    {
        return static::where('token', hash('sha256', $token))->first();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'user_id');
    }
}
