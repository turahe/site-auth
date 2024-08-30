<?php

namespace Modules\Auth\Models;

use App\Models\Google;
use app\Services\AccessTokenProvider;
use Illuminate\Database\Eloquent\Model;
use Modules\MailClient\Events\OAuthAccountDeleting;

class OAuthAccount extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'oauth_accounts';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'requires_auth' => 'boolean',
        'access_token' => 'encrypted',
        'user_id' => 'int',
    ];

    /**
     * Perform any actions required after the model boots.
     */
    protected static function booted(): void
    {
        static::deleting(function (OAuthAccount $account) {
            OAuthAccountDeleting::dispatch($account);
        });

        static::deleted(function (OAuthAccount $account) {
            if ($account->type === 'google') {
                try {
                    Google::revokeToken($account->access_token);
                } catch (\Exception) {
                }
            }
        });
    }

    /**
     * Set that this account requires authentication.
     */
    public function setAuthRequired(bool $value = true)
    {
        $this->requires_auth = $value;
        $this->save();

        return $this;
    }

    /**
     * Get the user the OAuth account belongs to.
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Create new token provider.
     */
    public function tokenProvider(): AccessTokenProvider
    {
        return new AccessTokenProvider($this->access_token, $this->email);
    }

}
