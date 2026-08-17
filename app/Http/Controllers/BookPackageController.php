<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\BookPackageService;
use Illuminate\Http\Request;
use App\Models\Package;
use App\Models\Agency;
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

    public function activePackages()
    {
        $packages = $this->bookPackageService->activePackages(
            auth()->user()
        );

        return ApiResponseService::successResponse(
            data: $packages
        );
    }

    public function pendingBookings(Package $package)
    {
        return $this->bookPackageService->pendingBookings($package);
    }

    public function approve(
        Package $package,
        Booking $booking
    ) {

        $agency = Agency::where('user_id', auth()->user()->id)->first();

        if (!$agency || $package->agency_id !== $agency->id) {
            return ApiResponseService::unauthorizedResponse(
                msg: 'You are not authorized to manage this package.'
            );
        }

        $this->bookPackageService->approveBooking(
            $package,
            $booking
        );

        return ApiResponseService::successResponse(
            msg: 'Booking approved successfully.'
        );
    }

    public function reject(
        Package $package,
        Booking $booking
    ) {

        $agency = Agency::where('user_id', auth()->user()->id)->first();

        if (!$agency || $package->agency_id !== $agency->id) {
            return ApiResponseService::unauthorizedResponse(
                msg: 'You are not authorized to manage this package.'
            );
        }

        $this->bookPackageService->rejectBooking(
            $package,
            $booking
        );

        return ApiResponseService::successResponse(
            msg: 'Booking rejected successfully.'
        );
    }

    public function cancel(  // by user 
        Package $package,
        Booking $booking
    ) {
        $this->bookPackageService->cancelPendingBooking(
            $package,
            $booking,
            auth()->user()
        );

        return ApiResponseService::successResponse(
            msg: 'Booking cancelled successfully.'
        );
    }
    
}
