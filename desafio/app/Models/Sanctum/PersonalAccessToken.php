<?php

namespace App\Models\Sanctum;

use MongoDB\Laravel\Eloquent\DocumentModel;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    use DocumentModel;

    protected $connection = 'mongodb';
    protected $keyType = 'string';
}
