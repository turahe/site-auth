<?php

namespace Modules\Auth\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Sqits\UserStamps\Concerns\HasUserStamps;

/**
 * @property string $id
 * @property string $name
 * @property string $guard_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Modules\Auth\Models\User|null $author
 * @property-read \Modules\Auth\Models\User|null $destroyer
 * @property-read \Modules\Auth\Models\User|null $editor
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Auth\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Auth\Models\User> $users
 * @property-read int|null $users_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereGuardName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role withoutPermission($permissions)
 *
 * @mixin \Eloquent
 */
class Role extends \Spatie\Permission\Models\Role
{
    use HasUlids;
    use HasUserStamps;

    protected $dateFormat = 'U';
}
