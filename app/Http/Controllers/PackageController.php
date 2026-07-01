<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StorePackageRequest;
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
}
