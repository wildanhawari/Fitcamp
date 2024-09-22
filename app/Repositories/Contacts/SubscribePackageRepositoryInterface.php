<?php

namespace app\Repositories\Contracts;

interface SubscribePackageRepositoryInterface
{
    public function getAllSubscribePackages();
    public function find($id);
    public function getPrice($subscribePackageId);
}