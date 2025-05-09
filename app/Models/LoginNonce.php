<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginNonce extends Model
{
    protected $guarded = [];

    public function databaseInfo()
    {
        return $this->belongsTo(DatabaseInfo::class);
    }
}
