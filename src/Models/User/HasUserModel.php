<?php

namespace Handtuchsystem\Models\User;

use Handtuchsystem\Models\BaseModel;

abstract class HasUserModel extends BaseModel
{
    use UsesUserModel;

    /** @var string The primary key for the model */
    protected $primaryKey = 'user_id';

    /** The attributes that are mass assignable */
    protected $fillable = [
        'user_id',
    ];

    /** The relationships that should be touched on save */
    protected $touches = ['user'];
}
