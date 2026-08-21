@extends('layouts.admin')

@section('content')

<div class="min-h-screen bg-[#0f172a] text-slate-100 p-6">

    <div class="max-w-7xl mx-auto">

        {{-- Header --}}
        <div class="mb-8">
            <p class="text-cyan-400 text-sm font-semibold mb-2">
                TOURIFY ADMIN
            </p>

            <h1 class="text-3xl font-bold">
                لوحة التحكم
            </h1>

            <p class="text-slate-400 mt-2">
                نظرة شاملة على بيانات النظام والحجوزات والخدمات السياحية
            </p>
        </div>


        {{-- Statistics --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

            @php
                $cards = [
                    [
                        'title' => 'المستخدمون',
                        'value' => $stats['users'],
                        'icon' => 'fa-users',
                    ],
                    [
                        'title' => 'المكاتب السياحية',
                        'value' => $stats['agencies'],
                        'icon' => 'fa-building',
                    ],
                    [
                        'title' => 'طلبات المكاتب',
                        'value' => $stats['pending_agencies'],
                        'icon' => 'fa-clock',
                    ],
                    [
                        'title' => 'شركات الطيران',
                        'value' => $stats['airlines'],
                        'icon' => 'fa-plane',
                    ],
                    [
                        'title' => 'الفنادق',
                        'value' => $stats['hotels'],
                        'icon' => 'fa-hotel',
                    ],
                    [
                        'title' => 'المطاعم',
                        'value' => $stats['restaurants'],
                        'icon' => 'fa-utensils',
                    ],
                    [
                        'title' => 'الأماكن السياحية',
                        'value' => $stats['places'],
                        'icon' => 'fa-landmark',
                    ],
                    [
                        'title' => 'البكيجات',
                        'value' => $stats['packages'],
                        'icon' => 'fa-suitcase',
                    ],
                ];
            @endphp

            @foreach($cards as $card)

                <div class="bg-slate-800/80 border border-slate-700/70 rounded-2xl p-5 shadow-xl hover:border-cyan-500/40 transition-all duration-300">

                    <div class="flex items-center justify-between">

                        <div>
                            <p class="text-slate-400 text-sm">
                                {{ $card['title'] }}
                            </p>

                            <p class="text-3xl font-bold mt-2">
                                {{ number_format($card['value']) }}
                            </p>
                        </div>

                        <div class="w-12 h-12 rounded-xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center">
                            <i class="fa-solid {{ $card['icon'] }} text-xl"></i>
                        </div>

                    </div>

                </div>

            @endforeach

        </div>


        {{-- Booking Statistics --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

            <div class="lg:col-span-2 bg-slate-800/80 border border-slate-700/70 rounded-2xl p-6">

                <div class="flex items-center justify-between mb-6">

                    <div>
                        <h2 class="text-xl font-bold">
                            حالة الحجوزات
                        </h2>

                        <p class="text-slate-400 text-sm mt-1">
                            آخر إحصائيات الحجوزات
                        </p>
                    </div>

                    <i class="fa-solid fa-chart-simple text-cyan-400 text-xl"></i>

                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

                    <div class="bg-yellow-500/10 border border-yellow-500/20 rounded-xl p-4">
                        <p class="text-yellow-400 text-sm">
                            معلقة
                        </p>

                        <p class="text-2xl font-bold mt-2">
                            {{ $bookingStats['pending'] }}
                        </p>
                    </div>

                    <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-xl p-4">
                        <p class="text-emerald-400 text-sm">
                            مؤكدة
                        </p>

                        <p class="text-2xl font-bold mt-2">
                            {{ $bookingStats['confirmed'] }}
                        </p>
                    </div>

                    <div class="bg-red-500/10 border border-red-500/20 rounded-xl p-4">
                        <p class="text-red-400 text-sm">
                            مرفوضة
                        </p>

                        <p class="text-2xl font-bold mt-2">
                            {{ $bookingStats['rejected'] }}
                        </p>
                    </div>

                    <div class="bg-slate-700/50 border border-slate-600/50 rounded-xl p-4">
                        <p class="text-slate-300 text-sm">
                            ملغاة
                        </p>

                        <p class="text-2xl font-bold mt-2">
                            {{ $bookingStats['cancelled'] }}
                        </p>
                    </div>

                </div>

            </div>


            <div class="bg-slate-800/80 border border-slate-700/70 rounded-2xl p-6">

                <h2 class="text-xl font-bold mb-5">
                    الحجوزات
                </h2>

                <div class="text-center py-5">

                    <p class="text-5xl font-bold text-cyan-400">
                        {{ number_format($stats['bookings']) }}
                    </p>

                    <p class="text-slate-400 mt-2">
                        إجمالي الحجوزات
                    </p>

                </div>

            </div>

        </div>


        {{-- Countries --}}
        <div class="bg-slate-800/80 border border-slate-700/70 rounded-2xl p-6 mb-8">

            <div class="flex items-center justify-between mb-6">

                <div>
                    <h2 class="text-xl font-bold">
                        توزيع النظام حسب الدول
                    </h2>

                    <p class="text-slate-400 text-sm mt-1">
                        أعداد الخدمات والمحتوى السياحي
                    </p>
                </div>

                <i class="fa-solid fa-globe text-cyan-400 text-xl"></i>

            </div>


            <div class="overflow-x-auto">

                <table class="w-full text-right">

                    <thead>

                    <tr class="border-b border-slate-700 text-slate-400 text-sm">

                        <th class="py-4 px-3">
                            الدولة
                        </th>

                        <th class="py-4 px-3">
                            المدن
                        </th>

                        <th class="py-4 px-3">
                            الأماكن
                        </th>

                        <th class="py-4 px-3">
                            الفنادق
                        </th>

                        <th class="py-4 px-3">
                            المطاعم
                        </th>

                        <th class="py-4 px-3">
                            البكيجات
                        </th>

                    </tr>

                    </thead>

                    <tbody>

                    @foreach($countries as $country)

                        <tr class="border-b border-slate-700/50 hover:bg-slate-700/20">

                            <td class="py-4 px-3 font-semibold">
                                {{ $country->name }}
                            </td>

                            <td class="py-4 px-3">
                                {{ $country->cities->count() }}
                            </td>

                            <td class="py-4 px-3 text-cyan-400">
                                {{ $country->places_count }}
                            </td>

                            <td class="py-4 px-3">
                                {{ $country->hotels_count }}
                            </td>

                            <td class="py-4 px-3">
                                {{ $country->restaurants_count }}
                            </td>

                            <td class="py-4 px-3">
                                {{ $country->packages_count }}
                            </td>

                        </tr>

                    @endforeach

                    </tbody>

                </table>

            </div>

        </div>


        {{-- Latest Packages --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <div class="bg-slate-800/80 border border-slate-700/70 rounded-2xl p-6">

                <div class="flex items-center justify-between mb-5">

                    <h2 class="text-xl font-bold">
                        آخر البكيجات
                    </h2>

                    <i class="fa-solid fa-suitcase text-cyan-400"></i>

                </div>

                <div class="space-y-3">

                    @forelse($latestPackages as $package)

                        <div class="bg-slate-700/30 rounded-xl p-4 flex items-center justify-between">

                            <div>

                                <p class="font-semibold">
                                    {{ $package->name }}
                                </p>

                                <p class="text-slate-400 text-sm mt-1">
                                    {{ $package->country->name ?? '—' }}
                                    ·
                                    {{ $package->agency->name ?? '—' }}
                                </p>

                            </div>

                            <span class="text-cyan-400 font-bold">
                                ${{ number_format($package->price, 2) }}
                            </span>

                        </div>

                    @empty

                        <p class="text-slate-500">
                            لا يوجد بكيجات حالياً.
                        </p>

                    @endforelse

                </div>

            </div>


            <div class="bg-slate-800/80 border border-slate-700/70 rounded-2xl p-6">

                <div class="flex items-center justify-between mb-5">

                    <h2 class="text-xl font-bold">
                        آخر طلبات المكاتب
                    </h2>

                    <i class="fa-solid fa-building text-cyan-400"></i>

                </div>

                <div class="space-y-3">

                    @forelse($latestAgencies as $user)

                        <div class="bg-slate-700/30 rounded-xl p-4 flex items-center justify-between">

                            <div>

                                <p class="font-semibold">
                                    {{ $user->agency->name ?? $user->username }}
                                </p>

                                <p class="text-slate-400 text-sm mt-1">
                                    {{ $user->email ?? 'لا يوجد بريد' }}
                                </p>

                            </div>

                            <span class="text-yellow-400 text-sm">
                                {{ $user->status->value }}
                            </span>

                        </div>

                    @empty

                        <p class="text-slate-500">
                            لا يوجد بيانات.
                        </p>

                    @endforelse

                </div>

            </div>

        </div>

    </div>

</div>

@endsection