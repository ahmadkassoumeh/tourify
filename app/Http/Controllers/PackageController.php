<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StorePackageRequest;
use App\Models\Country;
use App\Utilities\ApiResponseService;
use App\Http\Resources\CountryResource;
use App\Models\Package;
use App\Http\Resources\PackageResource;
use App\Services\PackageService;

class PackageController extends Controller
{
    public function __construct(
        private PackageService $packageService
    ) {}

    public function store(StorePackageRequest $request)
    {
        return $this->packageService->store($request);
    }

    public function hint(Request $request)
    {
        $data = $request->all();
        return $this->packageService->hint($data);
    }

    public function country()
    {
        $countries = Country::with('cities')
            ->orderBy('name')
            ->get();

        return ApiResponseService::successResponse(
            data: CountryResource::collection($countries)
        );
    }

    public function allPackages()
    {
        $packages = Package::with([
            'agency',
            'country',
            'days'
        ])->get();

        return ApiResponseService::successResponse(
            data: PackageResource::collection($packages)
        );
    }
}
