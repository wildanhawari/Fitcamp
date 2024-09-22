<?php

namespace app\Repositories;

use App\Models\City;
use app\Repositories\Contracts\CityRepositoryInterface;

class CityRepository implements CityRepositoryInterface
{
    public function getAllCities()
    {
        return City::latest()->get();
    }
}