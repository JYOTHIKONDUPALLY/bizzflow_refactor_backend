<?php

namespace App\Domains\Auth\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domains\Auth\Models\Franchise;
use App\Domains\Auth\Traits\HasFranchise;
use App\Domains\Auth\Traits\HasLocation;

class BusinessUnits extends Model
{
    use SoftDeletes, HasFranchise, HasLocation;

    protected $table = 'business_units';

    protected $fillable = [
        'franchise_id',
        'name',
        'is_active',
        'is_deleted',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_deleted' => 'boolean',
    ];

    public function franchise(): BelongsTo
    {
        return $this->belongsTo(Franchise::class);
    }
}
