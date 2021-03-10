<?php

declare(strict_types=1);

namespace Tipoff\Addresses\Models;

use Assert\Assert;
use Carbon\Carbon;
use Tipoff\Addresses\Transformers\DomesticAddressTransformer;
use Tipoff\Support\Models\BaseModel;
use Tipoff\Support\Traits\HasPackageFactory;

/**
 * @property int id
 * @property string address_line_1
 * @property string address_line_2
 * @property City city
 * @property Zip zip
 * @property Carbon created_at
 * @property Carbon updated_at
 * // Raw relations
 * @property int city_id
 * @property string zip_code
 */
class DomesticAddress extends BaseModel
{
    use HasPackageFactory;

    protected $casts = [
        'id' => 'integer',
        'city_id' => 'integer',
    ];

    protected $fillable = [
        'address_line_1',
        'address_line_2',
        'city_id',
        'zip_code',
    ];

    /**
     * @param string $line1
     * @param string|null $line2
     * @param string|City $city
     * @param string|Zip $zip
     * @return static
     */
    public static function createDomesticAddress(string $line1, ?string $line2, $city, $zip): self
    {
        $zip = ($zip instanceof Zip) ? $zip : Zip::query()->findOrFail(trim($zip));
        $city = ($city instanceof City) ? $city : City::query()->byTitle($city)->firstOrCreate(['title' => $city]);

        /** @var DomesticAddress $domesticAddress */
        $domesticAddress = static::query()->firstOrCreate([
            'address_line_1' => trim($line1),
            'address_line_2' => empty($line2) ? null : trim($line2),
            'city_id' => $city->id,
            'zip_code' => $zip->code,
        ]);

        return $domesticAddress;
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function (DomesticAddress $address) {
            Assert::lazy()
                ->that($address->address_line_1)->notEmpty('US domestic addresses must have a street.')
                ->that($address->city_id)->notEmpty('US domestic addresses must have a city.')
                ->that($address->zip_code)->notEmpty('US domestic addresses must have zip code.')
                ->verifyNow();
        });
    }

    public function getTransformer($context = null)
    {
        return new DomesticAddressTransformer();
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function zip()
    {
        return $this->belongsTo(Zip::class);
    }
}
