<?php

namespace Modules\Auth\Models\Model;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Sqits\UserStamps\Concerns\HasUserStamps;

/**
 * 
 *
 * @property string $id
 * @property string $model_type
 * @property string $model_id
 * @property string $number
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property int|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Phone newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Phone newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Phone query()
 * @method static \Illuminate\Database\Eloquent\Builder|Phone whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Phone whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Phone whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Phone whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Phone whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Phone whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Phone whereModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Phone whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Phone whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Phone whereUpdatedBy($value)
 * @property-read \Modules\Auth\Models\User|null $author
 * @property-read \Modules\Auth\Models\User|null $destroyer
 * @property-read \Modules\Auth\Models\User|null $editor
 * @mixin \Eloquent
 */
class Phone extends Model
{
    use HasUlids;
    use HasUserStamps;

    protected $table = 'model_phones';

    public $dateFormat = 'U';
}
