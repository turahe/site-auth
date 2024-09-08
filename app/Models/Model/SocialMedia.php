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
 * @property string $name
 * @property string|null $link
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property int|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Modules\Auth\Models\User|null $author
 * @property-read \Modules\Auth\Models\User|null $destroyer
 * @property-read \Modules\Auth\Models\User|null $editor
 * @method static \Illuminate\Database\Eloquent\Builder|SocialMedia newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SocialMedia newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SocialMedia query()
 * @method static \Illuminate\Database\Eloquent\Builder|SocialMedia whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialMedia whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialMedia whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialMedia whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialMedia whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialMedia whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialMedia whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialMedia whereModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialMedia whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialMedia whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialMedia whereUpdatedBy($value)
 * @mixin \Eloquent
 */
class SocialMedia extends Model
{
    use HasUlids;
    use HasUserStamps;

    protected $table = 'model_social_media';

    public $dateFormat = 'U';
}
