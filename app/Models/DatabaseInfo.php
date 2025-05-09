<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DatabaseInfo extends Model
{
    protected $guarded = [];

    public function loginNonces()
    {
        return $this->hasMany(LoginNonce::class, 'database_info_id');
    }
}
