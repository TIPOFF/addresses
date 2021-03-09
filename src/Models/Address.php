<?php

declare(strict_types=1);

namespace Tipoff\Addresses\Models;

use Tipoff\Support\Models\BaseModel;
use Tipoff\Support\Traits\HasPackageFactory;

class Address extends BaseModel
{
    use HasPackageFactory;

    public function domesticAddress()
    {
        return $this->belongsTo(DomesticAddress::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(app('user'), 'creator_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(app('user'), 'updater_id');
    }
}
