<?php

namespace App\Models;

use App\Scopes\FilterDeleteScope;
use Illuminate\Database\Eloquent\Model;

class ProfileEmail extends Model
{
    protected $table = 'profileemails';
    const CREATED_AT = 'CreateDate';
    const UPDATED_AT = 'LastModified';
    protected $guarded = ['_token'];

    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class, 'ProfileId', 'ProfileId');
    }

    public function setEmailAttribute($value)
    {
        $this->attributes['Email'] = strtolower($value);
    }
}
