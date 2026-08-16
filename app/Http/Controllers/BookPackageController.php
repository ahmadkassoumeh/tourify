<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\BookPackageService;
use Illuminate\Http\Request;
use App\Models\Package;
use App\Utilities\ApiResponseService;

class BookPackageController extends Controller
{
    public function __construct(
        private BookPackageService $bookPackageService
    ) {}

    public function store(Request $request)
    {
        $request->validate([
            'package_id' => [
                'required',
                'exists:packages,id'
            ],
        ]);

        $package = Package::with([
            'days.items.itemable',
            'agency.user',
        ])->findOrFail($request->package_id);

        $this->bookPackageService->bookPackage(
            $package,
            auth()->user()
        );

        return ApiResponseService::successResponse(
            msg: 'Package booked successfully.'
        );
    }

}
