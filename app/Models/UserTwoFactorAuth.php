<?php

namespace Modules\Auth\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Modules\SocialMedia\Casts\EncryptArrayObject;

/**
 * @property string $id
 * @property mixed $secret_key
 * @property string $user_id
 * @property \Illuminate\Database\Eloquent\Casts\ArrayObject $recovery_codes
 * @property int|null $confirmed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|UserTwoFactorAuth newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserTwoFactorAuth newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserTwoFactorAuth query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserTwoFactorAuth whereConfirmedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTwoFactorAuth whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTwoFactorAuth whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTwoFactorAuth whereRecoveryCodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTwoFactorAuth whereSecretKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTwoFactorAuth whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTwoFactorAuth whereUserId($value)
 *
 * @mixin \Eloquent
 */
class UserTwoFactorAuth extends Model
{
    use HasUlids;

    public $table = 'user_two_factor_auth';

    protected $dateFormat = 'U';

    protected $fillable = [
        'secret_key',
        'recovery_codes',
        'confirmed_at',
    ];

    protected $casts = [
        'secret_key' => 'encrypted',
        'recovery_codes' => EncryptArrayObject::class,
    ];

    protected $hidden = [
        'secret_key',
        'recovery_codes',
    ];
}
