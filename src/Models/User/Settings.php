<?php

namespace Handtuchsystem\Models\User;

use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * @property string $language
 * @property int    $theme
 * @property bool   $email_human
 * @property bool   $email_shiftinfo
 *
 * @method static QueryBuilder|Settings[] whereLanguage($value)
 * @method static QueryBuilder|Settings[] whereTheme($value)
 * @method static QueryBuilder|Settings[] whereEmailHuman($value)
 * @method static QueryBuilder|Settings[] whereEmailShiftinfo($value)
 */
class Settings extends HasUserModel
{
    /** @var string The table associated with the model */
    protected $table = 'users_settings';

    /** The attributes that are mass assignable */
    protected $fillable = [
        'user_id',
        'language',
        'theme',
        'email_human',
        'email_shiftinfo',
    ];
}
