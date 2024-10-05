<?php
namespace App\Services;

use App\Repositories\Contracts\CityRepositoryInterface;
use App\Repositories\Contracts\GymRepositoryInterface;
use App\Repositories\Contracts\SubscribePackageRepositoryInterface;

class FrontService 
{
    protected $gymRepository;
    protected $cityRepository;
    protected $subscribePackageRepository;

    public function __construct(GymRepositoryInterface $gymRepository, 
                                CityRepositoryInterface $cityRepository, 
                                SubscribePackageRepositoryInterface $subscribePackageRepository) 
    {
        $this->gymRepository = $gymRepository;
        $this->cityRepository = $cityRepository;
        $this->subscribePackageRepository = $subscribePackageRepository;
    }

    public function getFrontPageData()
    {
        $popularGyms = $this->gymRepository->getPopularGyms(4);
        $newGyms = $this->gymRepository->getAllNewGyms();
        $cities = $this->cityRepository->getAllCities();

        return compact('popularGyms', 'newGyms', 'cities');
    }

    public function getSubscriptionData()
    {
        $subscribePackage = $this->subscribePackageRepository->getAllSubscribePackages();

        return compact('subscribePackage');
    }
}
