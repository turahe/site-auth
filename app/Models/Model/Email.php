<?php

namespace Modules\Auth\Models\Model;

use App\Models\Model\Client;
use App\Models\Model\ClientManager;
use App\Models\Model\EmailAccountFolderCollection;
use App\Models\Model\EmailAccountsForUserCriteria;
use App\Models\Model\Media;
use App\Models\Model\Message;
use App\Models\Model\MessageForward;
use App\Models\Model\MessageReply;
use App\Models\Model\MetableContract;
use App\Models\Model\ScheduledEmail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Modules\Auth\Models\OAuthAccount;
use Modules\Auth\Models\User;
use Modules\MailClient\Enums\ConnectionType;
use Modules\MailClient\Enums\EmailAccountType;
use Modules\MailClient\Enums\SyncState;
use Modules\MailClient\Models\EmailAccountFolder;
use Modules\MailClient\Models\EmailAccountMessage;
use Modules\System\Concerns\HasSettings;
use Sqits\UserStamps\Concerns\HasUserStamps;
use Turahe\Media\HasMedia;

/**
 * 
 *
 * @property string $id
 * @property string $model_type
 * @property string $model_id
 * @property string $address
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property int|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Email newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Email newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Email query()
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereUpdatedBy($value)
 * @property-read \Modules\Auth\Models\User|null $author
 * @property-read \Modules\Auth\Models\User|null $destroyer
 * @property-read \Modules\Auth\Models\User|null $editor
 * @property-read mixed $display_email
 * @property-read \Illuminate\Database\Eloquent\Collection<int, EmailAccountFolder> $folders
 * @property-read int|null $folders_count
 * @property-read mixed $formatted_from_name_header
 * @property-read mixed $from_name_header
 * @property-read \Kalnoy\Nestedset\Collection<int, \Modules\System\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, EmailAccountMessage> $messages
 * @property-read int|null $messages_count
 * @property-read OAuthAccount|null $oAuthAccount
 * @property bool $requires_auth
 * @property-read EmailAccountFolder|null $sentFolder
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\System\Models\Setting> $settings
 * @property-read int|null $settings_count
 * @property-read EmailAccountFolder|null $trashFolder
 * @property-read mixed $type
 * @property-read Model|\Eloquent $user
 * @method static Builder|Email personal(int $userId)
 * @method static Builder|Email shared()
 * @method static Builder|Email syncable()
 * @method static Builder|Email withCommon()
 * @method static Builder|Email withFolders()
 * @mixin \Eloquent
 */
class Email extends Model
{
    use HasMedia;
    use HasSettings;
    use HasUlids;
    use HasUserStamps;

    protected $table = 'model_emails';

    public $dateFormat = 'U';

    /**
     * Indicates the primary meta key for user.
     */
    const PRIMARY_META_KEY = 'primary-email-account';

    /**
     * The default shared account from header type
     */
    const DEFAULT_FROM_NAME_HEADER = '{agent} from {company}';

    protected $defaultSettings = [
        'connection_type',
        'access_token_id',
        'sent_folder_id',
        'trash_folder_id',
        'create_contact',
        'initial_sync_from',
        'last_sync_at',
        'sync_state',
        'sync_state_comment',
        'requires_auth',
        'password',
        'validate_cert',
        'username',
        'imap_server',
        'imap_port',
        'imap_encryption',
        'smtp_server',
        'smtp_port',
        'smtp_encryption',
    ];

    // settings rules
    /**
     * @var array|string[]
     */
    public array $settingsRules = [
        'connection_type' => 'required',
        'access_token_id' => 'integer',
        'sent_folder_id' => 'integer',
        'trash_folder_id' => 'integer',
        'create_contact' => 'boolean',
        'initial_sync_from' => 'int',
        'last_sync_at' => 'datetime',
        'sync_state' => 'boolean',
        'sync_state_comment' => 'boolean',
        'requires_auth' => 'boolean',
        'password' => 'required|confirmed',
        'validate_cert' => 'boolean',
        'username' => 'required|string',
        'imap_server' => 'required|int',
        'imap_port' => 'required|int',
        'imap_encryption' => 'required|in:tls,ssl',
        'smtp_server' => 'required|int',
        'smtp_port' => 'required|int',
        'smtp_encryption' => 'required|in:tls,ssl',
    ];

    /**
     * A model has OAuth connection.
     */
    public function oAuthAccount(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(OAuthAccount::class, 'id', 'access_token_id');
    }

    /**
     * Check whether the user or system has sofly disabled the sync.
     */
    public function isSyncDisabled(): bool
    {
        return $this->sync_state === SyncState::Disabled;
    }

    /**
     * Check whether the system has stopped the account sync.
     */
    public function isSyncStopped(): bool
    {
        return $this->sync_state === SyncState::Stopped;
    }

    /**
     * Checks whether an initial sync is performed for the account.
     */
    public function isInitialSyncPerformed(): bool
    {
        return ! empty($this->last_sync_at);
    }

    /**
     * Check whether account synchronization is on hold.
     */
    public function isSyncOnHold(): bool
    {
        if (! $resumeDate = $this->getSyncResumeDate()) {
            return false;
        }

        return $resumeDate->isFuture();
    }

    /**
     * Continue sync for the account that sync is on hold.
     */
    public function continueSync(): static
    {
        $this->removeMeta('_continue_sync_after');

        return $this;
    }

    /**
     * Set the date the synchronization should be postponed.
     */
    public function holdSyncUntil(string $date): static
    {
        $this->setMeta('_continue_sync_after', $date);

        return $this;
    }

    /**
     * Get the sync that is on hold resume date.
     */
    public function getSyncResumeDate(int $safeMinutes = 15): ?Carbon
    {
        $continueAfter = $this->getMeta('_continue_sync_after');

        // We will add 15 minutes to allow Google to properly clear all quota limits,
        // while testing identified that if sync is retried 15 minutes after the
        // retry after timestamp, most likely won't hit the rate limit.
        return $continueAfter ? Carbon::parse($continueAfter)->addMinutes($safeMinutes) : null;
    }

    /**
     * We will set custom accessor for the requires_auth attribute.
     *
     * If it's OAuthAccount we will return the value from the actual oauth account
     * instead of the syncable value, because OAuth account can be used in other features
     * in the application and these features may update the requires_auth on the oauth_accounts table directly
     * In this case, we this will ensure that the requires_auth attribute value is up to date.
     *
     * If it's regular account without OAuth e.q. IMAP, we will return the value from the actual syncable model.
     */
    protected function requiresAuth(): Attribute
    {
        return Attribute::make(
            get: fn (bool $value) => is_null($this->oAuthAccount) ?
                $value :
                $this->oAuthAccount->requires_auth,
            set: fn (bool $value) => $value,
        );
    }

    /**
     * An email account can belongs to a user (personal).
     */
    public function user()
    {
        return $this->morphTo('model');
    }

    /**
     * An email account has many mail messages.
     */
    public function messages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(EmailAccountMessage::class);
    }

    /**
     * An email account has many mail folders.
     */
    public function folders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(EmailAccountFolder::class);
    }

    /**
     * An email account has sent folder indicator.
     */
    public function sentFolder(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(EmailAccountFolder::class);
    }

    /**
     * An email account has trash folder indicator.
     */
    public function trashFolder(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(EmailAccountFolder::class);
    }

    /**
     * Get the account active folders.
     */
    public function activeFolders(): EmailAccountFolderCollection
    {
        return $this->folders->active();
    }

    /**
     * Check whether the account is shared.
     */
    public function isShared(): bool
    {
        return is_null($this->model_id);
    }

    /**
     * Check whether the account is personal.
     */
    public function isPersonal(): bool
    {
        return ! $this->isShared();
    }

    /**
     * Get the user type of the account.
     *
     * 'shared' or 'personal'
     */
    protected function type(): Attribute
    {
        return Attribute::get(
            fn () => $this->isShared() ? EmailAccountType::Shared : EmailAccountType::Personal
        );
    }

    /**
     * Check whether the account is primary account for the given or current user.
     */
    public function isPrimary((MetableContract&User)|null $user = null): bool
    {
        $user = $user ?? auth()->user();

        return (int) $user->getMeta(self::PRIMARY_META_KEY) === (int) $this->id;
    }

    /**
     * Mark the account as primary for the given user.
     */
    public function markAsPrimary(MetableContract&User $user): static
    {
        $user->setMeta(self::PRIMARY_META_KEY, $this->id);

        return $this;
    }

    /**
     * Unmark the account as primary for the given user.
     */
    public static function unmarkAsPrimary(MetableContract&User $user): void
    {
        $user->removeMeta(self::PRIMARY_META_KEY);
    }

    /**
     * Get the email address that should be used to the account owner.
     */
    protected function displayEmail(): Attribute
    {
        return Attribute::get(
            fn () => $this->alias_email ?? $this->email
        );
    }

    /**
     * Get the account form name header option.
     */
    protected function fromNameHeader(): Attribute
    {
        return Attribute::get(
            fn () => $this->getMeta('from_name_header') ?: static::DEFAULT_FROM_NAME_HEADER
        );
    }

    /**
     * Get the formatted from name header for the account.
     */
    protected function formattedFromNameHeader(): Attribute
    {
        return Attribute::get(function () {
            // When running the synchronization command via the console
            // there is no logged in user
            // In this case, we will just set as an empty name
            // as the SMTP client is not used in the synchronization command
            if ($this->isPersonal()) {
                $agent = $this->user->name;
            } else {
                $agent = auth()->check() ? auth()->user()->name : '';
            }

            // Does not work in Microsoft, cannot set custom from name header ATM
            if ($this->connection_type == ConnectionType::Outlook) {
                return $agent;
            }

            return $this->formatFromNameHeader($this->fromNameHeader, $agent);
        });
    }

    /**
     * Format the given from name header.
     */
    public function formatFromNameHeader(string $value, string $agent): string
    {
        return str_replace(
            ['{agent}', '{company}'],
            [$agent, config('app.name')],
            $value
        );
    }

    /**
     * Check whether the account can send mails.
     */
    public function canSendEmail(): bool
    {
        return ! ($this->requires_auth || $this->isSyncStopped());
    }

    /**
     * Create email account mail client.
     */
    public function createClient(): Client
    {
        return $this->newClient()
            ->setFromAddress($this->display_email)
            ->setFromName($this->formatted_from_name_header);
    }

    /**
     * Create new client instance.
     */
    protected function newClient(): Client
    {
        if ($this->oAuthAccount) {
            return ClientManager::createClient(
                $this->connection_type,
                $this->oAuthAccount->tokenProvider()
            );
        }

        return ClientManager::createClient(
            $this->connection_type,
            $this->getImapConfig(),
            $this->getSmtpConfig()
        );
    }

    /**
     * Get the account client.
     */
    public function getClient(): Client
    {
        return $this->clientInstance ??= $this->createClient();
    }

    /**
     * Scope a query to include only syncable accounts.
     */
    public function scopeSyncable(Builder $query): void
    {
        $query->where('sync_state', SyncState::ENABLED);
    }

    /**
     * Scope a query to include only shared accounts.
     */
    public function scopeShared(Builder $query): void
    {
        $query->doesntHave('user');
    }

    /**
     * Scope a query to include only personal accounts.
     */
    public function scopePersonal(Builder $query, int $userId): void
    {
        $query->where('user_id', $userId);
    }

    /**
     * Create a scope to eager load the account folders data.
     */
    public function scopeWithFolders(Builder $query): void
    {
        $query->with(['folders' => fn ($query) => $query->withCount([
            'messages as unread_count' => fn ($query) => $query->unread(),
        ])]);
    }

    /**
     * Eager load the relations that are required for the front end response.
     */
    public function scopeWithCommon(Builder $query): void
    {
        $query->withCount([
            'messages as unread_count' => fn ($query) => $query->unread(),
        ])->withFolders()->with([
            'user',
            'sentFolder',
            'trashFolder',
            'oAuthAccount',
        ]);
    }

    /**
     * Set that this account requires authentication.
     */
    public function setAuthRequired(bool $value = true): static
    {
        if (! is_null($this->oAuthAccount)) {
            $this->oAuthAccount->setAuthRequired($value);
        }

        $this->fill(['requires_auth' => $value])->save();

        return $this;
    }

    /**
     * Set the account synchronization state.
     */
    public function setSyncState(SyncState $state, ?string $comment = null): static
    {
        $this->forceFill([
            'sync_state' => $state,
            'sync_state_comment' => $comment,
        ])->save();

        return $this;
    }

    /**
     * Enable account synchronization.
     */
    public function enableSync(): static
    {
        return $this->setSyncState(SyncState::ENABLED);
    }

    /**
     * Count the unread messages for all accounts the given user can access.
     */
    public static function countUnreadMessagesForUser(User|int $user): int
    {
        $userAccounts = static::select('id')
            ->criteria(new EmailAccountsForUserCriteria($user))
            ->get();

        if ($userAccounts->isEmpty()) {
            return 0;
        }

        return EmailAccountMessage::unread()
            ->whereHas('folders', fn ($query) => $query->where('syncable', 1))
            ->whereIn('email_account_id', $userAccounts->pluck('id')->all())
            ->count();
    }

    /**
     * Count the total unread messages for the given account.
     */
    public static function countUnreadMessages(int $id): int
    {
        return static::countReadOrUnreadMessages($id, 'unread');
    }

    /**
     * Count the total read messages for the given account.
     */
    public static function countReadMessages(int $id): int
    {
        return static::countReadOrUnreadMessages($id, 'read');
    }

    /**
     * Count read or unread messages for the given account.
     */
    protected static function countReadOrUnreadMessages(int $id, string $scope): int
    {
        return EmailAccountMessage::where('email_account_id', $id)
            ->{$scope}()
            ->count();
    }

    /**
     * Create the message composer.
     */
    public function createMessageComposer(string $type, ?int $relatedMessageId): Message|MessageReply|MessageForward
    {
        $relatedMessage = $relatedMessageId ? EmailAccountMessage::find($relatedMessageId) : null;

        return match ($type) {
            Message::SEND => new Message(
                $this->createClient(),
                $this->sentFolder->identifier()
            ),
            Message::REPLY => new MessageReply(
                $this->createClient(),
                $relatedMessage->remote_id,
                $relatedMessage->folders->first()->identifier(),
                $this->sentFolder->identifier()
            ),
            Message::FORWARD => new MessageForward(
                $this->createClient(),
                $relatedMessage->remote_id,
                $relatedMessage->folders->first()->identifier(),
                $this->sentFolder->identifier()
            )
        };
    }

    /**
     * Purge the model data.
     */
    public function purge(): void
    {
        // Detach from only messages with associations
        // This helps to not loop over all messages and delete them
        foreach (['contacts', 'companies', 'deals'] as $relation) {
            $this->messages()
                ->whereHas($relation, function ($query) {
                    $query->withTrashed();
                })
                ->cursor()
                ->each(function (EmailAccountMessage $message) use ($relation) {
                    $message->{$relation}()->withTrashed()->detach();
                });
        }

        ScheduledEmail::where('email_account_id', $this->id)
            ->get()
            ->each(function (ScheduledEmail $message) {
                $message->delete();
            });

        // To prevent looping through all messages and loading them into
        // memory, we will get their id's only and purge the media for the messages where media is available
        $messagesIds = $this->messages()->cursor()->map(fn ($message) => $message->id);

        (new Media)->purgeByMediableIds(EmailAccountMessage::class, $messagesIds);

        $this->messages()->delete();

        $this->folders->each->delete();

        $systemEmailAccountId = $this->getSetting('system_email_account_id');

        if ((int) $systemEmailAccountId === (int) $this->id) {
            $this->deleteSetting('system_email_account_id');
        }
    }
}
