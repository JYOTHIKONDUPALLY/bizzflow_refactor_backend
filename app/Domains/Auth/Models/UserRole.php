<?php
namespace Domain\Auth\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'role_id',
    ];
}