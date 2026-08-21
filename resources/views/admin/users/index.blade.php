@extends('layouts.admin')

@section('title', 'إدارة المستخدمين')

@section('content')

<div class="min-h-screen bg-[#0f172a] text-slate-100 p-6">

    <div class="max-w-7xl mx-auto">


        {{-- Header --}}
        <div class="mb-8">

            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                <div>

                    <p class="text-cyan-400 text-sm font-semibold tracking-wide">
                        TOURIFY ADMIN
                    </p>

                    <h1 class="text-3xl font-bold mt-2">
                        إدارة المستخدمين
                    </h1>

                    <p class="text-slate-400 mt-2">
                        عرض وإدارة جميع المستخدمين والحسابات المرتبطة بالنظام
                    </p>

                </div>

                <a
                    href="{{ route('admin.dashboard') }}"
                    class="
                        inline-flex
                        items-center
                        justify-center
                        gap-2
                        bg-white/5
                        border
                        border-slate-700
                        hover:border-cyan-500/40
                        hover:bg-cyan-500/10
                        text-slate-300
                        hover:text-cyan-400
                        px-5
                        py-3
                        rounded-xl
                        transition
                    "
                >

                    <i class="fa-solid fa-arrow-right"></i>

                    العودة للوحة التحكم

                </a>

            </div>

        </div>


        {{-- Flash Message --}}
        @if(session('success'))

            <div
                class="
                    mb-6
                    rounded-xl
                    border
                    border-emerald-500/20
                    bg-emerald-500/10
                    px-5
                    py-4
                    text-emerald-400
                "
            >

                <div class="flex items-center gap-3">

                    <i class="fa-solid fa-circle-check"></i>

                    <span>
                        {{ session('success') }}
                    </span>

                </div>

            </div>

        @endif


        @if(session('error'))

            <div
                class="
                    mb-6
                    rounded-xl
                    border
                    border-red-500/20
                    bg-red-500/10
                    px-5
                    py-4
                    text-red-400
                "
            >

                <div class="flex items-center gap-3">

                    <i class="fa-solid fa-circle-exclamation"></i>

                    <span>
                        {{ session('error') }}
                    </span>

                </div>

            </div>

        @endif


        {{-- Statistics --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">


            {{-- Total --}}
            <div
                class="
                    bg-slate-800/80
                    border border-slate-700
                    rounded-2xl
                    p-5
                    shadow-xl
                    hover:border-cyan-500/30
                    transition
                "
            >

                <div class="flex items-center justify-between">

                    <div>

                        <p class="text-slate-400 text-sm">
                            إجمالي المستخدمين
                        </p>

                        <p class="text-3xl font-bold mt-2">
                            {{ number_format($totalUsers) }}
                        </p>

                    </div>

                    <div
                        class="
                            w-12
                            h-12
                            rounded-xl
                            bg-cyan-500/10
                            border border-cyan-500/10
                            text-cyan-400
                            flex items-center justify-center
                        "
                    >

                        <i class="fa-solid fa-users text-xl"></i>

                    </div>

                </div>

            </div>


            {{-- Normal Users --}}
            <div
                class="
                    bg-slate-800/80
                    border border-slate-700
                    rounded-2xl
                    p-5
                    shadow-xl
                    hover:border-cyan-500/30
                    transition
                "
            >

                <div class="flex items-center justify-between">

                    <div>

                        <p class="text-slate-400 text-sm">
                            المستخدمون العاديون
                        </p>

                        <p class="text-3xl font-bold text-cyan-400 mt-2">
                            {{ number_format($usersCount) }}
                        </p>

                    </div>

                    <div
                        class="
                            w-12
                            h-12
                            rounded-xl
                            bg-cyan-500/10
                            text-cyan-400
                            flex items-center justify-center
                        "
                    >

                        <i class="fa-solid fa-user"></i>

                    </div>

                </div>

            </div>


            {{-- Agencies --}}
            <div
                class="
                    bg-slate-800/80
                    border border-slate-700
                    rounded-2xl
                    p-5
                    shadow-xl
                    hover:border-cyan-500/30
                    transition
                "
            >

                <div class="flex items-center justify-between">

                    <div>

                        <p class="text-slate-400 text-sm">
                            المكاتب السياحية
                        </p>

                        <p class="text-3xl font-bold text-cyan-400 mt-2">
                            {{ number_format($agenciesCount) }}
                        </p>

                    </div>

                    <div
                        class="
                            w-12
                            h-12
                            rounded-xl
                            bg-cyan-500/10
                            text-cyan-400
                            flex items-center justify-center
                        "
                    >

                        <i class="fa-solid fa-building"></i>

                    </div>

                </div>

            </div>


            {{-- Airlines --}}
            <div
                class="
                    bg-slate-800/80
                    border border-slate-700
                    rounded-2xl
                    p-5
                    shadow-xl
                    hover:border-cyan-500/30
                    transition
                "
            >

                <div class="flex items-center justify-between">

                    <div>

                        <p class="text-slate-400 text-sm">
                            شركات الطيران
                        </p>

                        <p class="text-3xl font-bold text-cyan-400 mt-2">
                            {{ number_format($airlinesCount) }}
                        </p>

                    </div>

                    <div
                        class="
                            w-12
                            h-12
                            rounded-xl
                            bg-cyan-500/10
                            text-cyan-400
                            flex items-center justify-center
                        "
                    >

                        <i class="fa-solid fa-plane"></i>

                    </div>

                </div>

            </div>

        </div>


        {{-- Secondary Statistics --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">


            <div
                class="
                    bg-slate-800/80
                    border border-yellow-500/10
                    rounded-2xl
                    p-5
                "
            >

                <div class="flex items-center justify-between">

                    <div>

                        <p class="text-slate-400 text-sm">
                            الحسابات بانتظار الموافقة
                        </p>

                        <p class="text-3xl font-bold text-yellow-400 mt-2">
                            {{ number_format($pendingCount) }}
                        </p>

                    </div>

                    <div
                        class="
                            w-12
                            h-12
                            rounded-xl
                            bg-yellow-500/10
                            text-yellow-400
                            flex items-center justify-center
                        "
                    >

                        <i class="fa-solid fa-clock"></i>

                    </div>

                </div>

            </div>


            <div
                class="
                    bg-slate-800/80
                    border border-emerald-500/10
                    rounded-2xl
                    p-5
                "
            >

                <div class="flex items-center justify-between">

                    <div>

                        <p class="text-slate-400 text-sm">
                            الحسابات الموافق عليها
                        </p>

                        <p class="text-3xl font-bold text-emerald-400 mt-2">
                            {{ number_format($approvedCount) }}
                        </p>

                    </div>

                    <div
                        class="
                            w-12
                            h-12
                            rounded-xl
                            bg-emerald-500/10
                            text-emerald-400
                            flex items-center justify-center
                        "
                    >

                        <i class="fa-solid fa-circle-check"></i>

                    </div>

                </div>

            </div>

        </div>


        {{-- Search / Filters --}}
        <div
            class="
                bg-slate-800/80
                border border-slate-700
                rounded-2xl
                p-5
                mb-6
            "
        >

            <form
                method="GET"
                action="{{ route('admin.users.index') }}"
            >

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">


                    {{-- Search --}}
                    <div class="md:col-span-2 relative">

                        <label class="block text-sm text-slate-400 mb-2">
                            بحث
                        </label>

                        <div class="relative">

                            <i
                                class="
                                    fa-solid
                                    fa-magnifying-glass
                                    absolute
                                    right-4
                                    top-1/2
                                    -translate-y-1/2
                                    text-slate-500
                                "
                            ></i>

                            <input
                                type="text"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="الاسم أو البريد أو اسم المستخدم أو الهاتف"
                                class="
                                    w-full
                                    bg-slate-900
                                    border border-slate-700
                                    rounded-xl
                                    pr-11
                                    pl-4
                                    py-3
                                    text-slate-100
                                    placeholder-slate-600
                                    focus:border-cyan-400
                                    focus:ring-2
                                    focus:ring-cyan-400/20
                                    outline-none
                                "
                            >

                        </div>

                    </div>


                    {{-- Role --}}
                    <div>

                        <label class="block text-sm text-slate-400 mb-2">
                            الدور
                        </label>

                        <select
                            name="role"
                            class="
                                w-full
                                bg-slate-900
                                border border-slate-700
                                rounded-xl
                                px-4
                                py-3
                                text-slate-100
                                focus:border-cyan-400
                                focus:ring-2
                                focus:ring-cyan-400/20
                                outline-none
                            "
                        >

                            <option value="">
                                جميع الأدوار
                            </option>

                            <option
                                value="user"
                                {{ request('role') === 'user' ? 'selected' : '' }}
                            >
                                مستخدم
                            </option>

                            <option
                                value="agency"
                                {{ request('role') === 'agency' ? 'selected' : '' }}
                            >
                                مكتب سياحي
                            </option>

                            <option
                                value="airline"
                                {{ request('role') === 'airline' ? 'selected' : '' }}
                            >
                                شركة طيران
                            </option>

                        </select>

                    </div>


                    {{-- Status --}}
                    <div>

                        <label class="block text-sm text-slate-400 mb-2">
                            الحالة
                        </label>

                        <select
                            name="status"
                            class="
                                w-full
                                bg-slate-900
                                border border-slate-700
                                rounded-xl
                                px-4
                                py-3
                                text-slate-100
                                focus:border-cyan-400
                                focus:ring-2
                                focus:ring-cyan-400/20
                                outline-none
                            "
                        >

                            <option value="">
                                جميع الحالات
                            </option>

                            <option
                                value="pending"
                                {{ request('status') === 'pending' ? 'selected' : '' }}
                            >
                                بانتظار الموافقة
                            </option>

                            <option
                                value="approved"
                                {{ request('status') === 'approved' ? 'selected' : '' }}
                            >
                                موافق عليه
                            </option>

                            <option
                                value="rejected"
                                {{ request('status') === 'rejected' ? 'selected' : '' }}
                            >
                                مرفوض
                            </option>

                            <option
                                value="suspended"
                                {{ request('status') === 'suspended' ? 'selected' : '' }}
                            >
                                موقوف
                            </option>

                        </select>

                    </div>

                </div>


                <div class="flex items-center gap-3 mt-5">

                    <button
                        type="submit"
                        class="
                            bg-cyan-500
                            hover:bg-cyan-400
                            text-slate-950
                            font-bold
                            px-6
                            py-3
                            rounded-xl
                            transition
                        "
                    >

                        <i class="fa-solid fa-filter ml-2"></i>

                        تطبيق الفلتر

                    </button>


                    <a
                        href="{{ route('admin.users.index') }}"
                        class="
                            bg-white/5
                            border border-slate-700
                            hover:bg-white/10
                            text-slate-300
                            px-6
                            py-3
                            rounded-xl
                            transition
                        "
                    >

                        مسح

                    </a>

                </div>

            </form>

        </div>


        {{-- Users Table --}}
        <div
            class="
                bg-slate-800/80
                border border-slate-700
                rounded-2xl
                overflow-hidden
                shadow-xl
            "
        >

            <div class="overflow-x-auto">

                <table class="min-w-full">

                    <thead class="bg-slate-900/70">

                    <tr class="border-b border-slate-700">

                        <th class="px-6 py-4 text-right text-xs font-semibold text-slate-400">
                            المستخدم
                        </th>

                        <th class="px-6 py-4 text-right text-xs font-semibold text-slate-400">
                            الهاتف
                        </th>

                        <th class="px-6 py-4 text-right text-xs font-semibold text-slate-400">
                            الدور
                        </th>

                        <th class="px-6 py-4 text-right text-xs font-semibold text-slate-400">
                            الحالة
                        </th>

                        <th class="px-6 py-4 text-right text-xs font-semibold text-slate-400">
                            التسجيل
                        </th>

                        <th class="px-6 py-4 text-right text-xs font-semibold text-slate-400">
                            الإجراءات
                        </th>

                    </tr>

                    </thead>


                    <tbody class="divide-y divide-slate-700/70">

                    @forelse($users as $user)

                        @php
                            $role = $user->getRoleNames()->first();
                        @endphp

                        <tr class="hover:bg-cyan-500/5 transition">


                            {{-- User --}}
                            <td class="px-6 py-5">

                                <div class="flex items-center gap-4">

                                    <div class="shrink-0">

                                        @if($user->profile_image)

                                            <img
                                                src="{{ asset('storage/users/' . $user->profile_image) }}"
                                                class="
                                                    w-12
                                                    h-12
                                                    rounded-xl
                                                    object-cover
                                                    border
                                                    border-cyan-500/20
                                                "
                                                alt="{{ $user->first_name }}"
                                            >

                                        @else

                                            <div
                                                class="
                                                    w-12
                                                    h-12
                                                    rounded-xl
                                                    bg-cyan-500/10
                                                    border border-cyan-500/20
                                                    flex items-center justify-center
                                                    text-cyan-400
                                                    font-bold
                                                "
                                            >
                                                {{ strtoupper(substr($user->first_name, 0, 1)) }}
                                            </div>

                                        @endif

                                    </div>


                                    <div>

                                        <p class="font-semibold text-white">

                                            {{ $user->first_name }}
                                            {{ $user->last_name }}

                                        </p>

                                        <p class="text-sm text-slate-500 mt-1">

                                            {{ $user->username }}

                                        </p>

                                        @if($user->email)

                                            <p class="text-sm text-slate-400 mt-1">

                                                {{ $user->email }}

                                            </p>

                                        @endif

                                    </div>

                                </div>

                            </td>


                            {{-- Phone --}}
                            <td class="px-6 py-5 text-sm text-slate-300">

                                {{ $user->phone_number ?? 'لا يوجد' }}

                            </td>


                            {{-- Role --}}
                            <td class="px-6 py-5">

                                @if($role === 'user')

                                    <span
                                        class="
                                            inline-flex
                                            items-center
                                            gap-2
                                            px-3 py-1.5
                                            rounded-full
                                            text-xs
                                            font-semibold
                                            bg-cyan-500/10
                                            text-cyan-400
                                            border border-cyan-500/20
                                        "
                                    >

                                        <i class="fa-solid fa-user"></i>

                                        مستخدم

                                    </span>

                                @elseif($role === 'agency')

                                    <span
                                        class="
                                            inline-flex
                                            items-center
                                            gap-2
                                            px-3 py-1.5
                                            rounded-full
                                            text-xs
                                            font-semibold
                                            bg-slate-700
                                            text-slate-200
                                            border border-slate-600
                                        "
                                    >

                                        <i class="fa-solid fa-building"></i>

                                        مكتب سياحي

                                    </span>

                                @elseif($role === 'airline')

                                    <span
                                        class="
                                            inline-flex
                                            items-center
                                            gap-2
                                            px-3 py-1.5
                                            rounded-full
                                            text-xs
                                            font-semibold
                                            bg-cyan-500/10
                                            text-cyan-300
                                            border border-cyan-500/20
                                        "
                                    >

                                        <i class="fa-solid fa-plane"></i>

                                        شركة طيران

                                    </span>

                                @else

                                    <span
                                        class="
                                            inline-flex
                                            items-center
                                            gap-2
                                            px-3 py-1.5
                                            rounded-full
                                            text-xs
                                            font-semibold
                                            bg-slate-700
                                            text-slate-400
                                            border border-slate-600
                                        "
                                    >

                                        بدون دور

                                    </span>

                                @endif

                            </td>


                            {{-- Status --}}
                            <td class="px-6 py-5">

                                @switch($user->status->value)

                                    @case('pending')

                                        <span
                                            class="
                                                inline-flex
                                                items-center
                                                gap-2
                                                px-3 py-1.5
                                                rounded-full
                                                text-xs
                                                font-semibold
                                                bg-yellow-500/10
                                                text-yellow-400
                                                border border-yellow-500/20
                                            "
                                        >

                                            <i class="fa-solid fa-clock"></i>

                                            بانتظار الموافقة

                                        </span>

                                    @break


                                    @case('approved')

                                        <span
                                            class="
                                                inline-flex
                                                items-center
                                                gap-2
                                                px-3 py-1.5
                                                rounded-full
                                                text-xs
                                                font-semibold
                                                bg-emerald-500/10
                                                text-emerald-400
                                                border border-emerald-500/20
                                            "
                                        >

                                            <i class="fa-solid fa-circle-check"></i>

                                            موافق عليه

                                        </span>

                                    @break


                                    @case('rejected')

                                        <span
                                            class="
                                                inline-flex
                                                items-center
                                                gap-2
                                                px-3 py-1.5
                                                rounded-full
                                                text-xs
                                                font-semibold
                                                bg-red-500/10
                                                text-red-400
                                                border border-red-500/20
                                            "
                                        >

                                            <i class="fa-solid fa-circle-xmark"></i>

                                            مرفوض

                                        </span>

                                    @break


                                    @case('suspended')

                                        <span
                                            class="
                                                inline-flex
                                                items-center
                                                gap-2
                                                px-3 py-1.5
                                                rounded-full
                                                text-xs
                                                font-semibold
                                                bg-slate-700
                                                text-slate-300
                                                border border-slate-600
                                            "
                                        >

                                            <i class="fa-solid fa-ban"></i>

                                            موقوف

                                        </span>

                                    @break


                                    @default

                                        <span class="text-slate-500">
                                            {{ $user->status->value }}
                                        </span>

                                @endswitch

                            </td>


                            {{-- Created --}}
                            <td class="px-6 py-5 text-sm">

                                <p class="text-slate-300">

                                    {{ $user->created_at->format('Y/m/d') }}

                                </p>

                                <p class="text-xs text-slate-500 mt-1">

                                    {{ $user->created_at->diffForHumans() }}

                                </p>

                            </td>


                            {{-- Actions --}}
                            <td class="px-6 py-5">

                                <div class="flex items-center gap-2">


                                    {{-- View --}}
                                    <button
                                        type="button"
                                        onclick="viewUser(
                                            {{ $user->id }},
                                            @js($user->first_name . ' ' . $user->last_name),
                                            @js($user->email),
                                            @js($user->phone_number)
                                        )"
                                        class="
                                            w-9
                                            h-9
                                            rounded-lg
                                            bg-cyan-500/10
                                            text-cyan-400
                                            hover:bg-cyan-500/20
                                            transition
                                        "
                                        title="عرض"
                                    >

                                        <i class="fa-solid fa-eye"></i>

                                    </button>


                                    {{-- Delete --}}
                                    <button
                                        type="button"
                                        onclick="showDeleteModal(
                                            {{ $user->id }},
                                            @js($user->first_name . ' ' . $user->last_name)
                                        )"
                                        class="
                                            w-9
                                            h-9
                                            rounded-lg
                                            bg-red-500/10
                                            text-red-400
                                            hover:bg-red-500/20
                                            transition
                                        "
                                        title="حذف"
                                    >

                                        <i class="fa-solid fa-trash"></i>

                                    </button>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="px-6 py-16 text-center"
                            >

                                <div class="flex flex-col items-center">

                                    <div
                                        class="
                                            w-16
                                            h-16
                                            rounded-2xl
                                            bg-slate-700/50
                                            flex items-center justify-center
                                            text-slate-500
                                            mb-4
                                        "
                                    >

                                        <i class="fa-solid fa-users text-2xl"></i>

                                    </div>

                                    <p class="text-slate-300 font-semibold">
                                        لا يوجد مستخدمون
                                    </p>

                                    <p class="text-slate-500 text-sm mt-1">
                                        جرّب تغيير الفلاتر أو البحث.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>


            {{-- Pagination --}}
            @if($users->hasPages())

                <div
                    class="
                        px-6
                        py-5
                        border-t
                        border-slate-700
                        flex
                        flex-col
                        md:flex-row
                        gap-4
                        items-center
                        justify-between
                    "
                >

                    <p class="text-sm text-slate-500">

                        عرض

                        <span class="text-slate-300 font-semibold">
                            {{ $users->firstItem() }}
                        </span>

                        إلى

                        <span class="text-slate-300 font-semibold">
                            {{ $users->lastItem() }}
                        </span>

                        من

                        <span class="text-slate-300 font-semibold">
                            {{ $users->total() }}
                        </span>

                        مستخدم

                    </p>

                    <div>
                        {{ $users->links() }}
                    </div>

                </div>

            @endif

        </div>

    </div>

</div>


{{-- View User Modal --}}
<div
    id="viewUserModal"
    class="
        fixed
        inset-0
        z-50
        hidden
        bg-black/70
        backdrop-blur-sm
        items-center
        justify-center
        p-4
    "
>

    <div
        class="
            w-full
            max-w-md
            bg-slate-800
            border border-slate-700
            rounded-2xl
            shadow-2xl
        "
    >

        <div class="p-6">

            <div class="flex items-center justify-between mb-6">

                <div>

                    <p class="text-cyan-400 text-xs font-semibold">
                        USER DETAILS
                    </p>

                    <h3 class="text-xl font-bold mt-1">
                        معلومات المستخدم
                    </h3>

                </div>

                <button
                    type="button"
                    onclick="closeViewModal()"
                    class="
                        w-9
                        h-9
                        rounded-lg
                        bg-white/5
                        text-slate-400
                        hover:text-white
                        transition
                    "
                >

                    <i class="fa-solid fa-xmark"></i>

                </button>

            </div>


            <div class="space-y-4">

                <div
                    class="
                        bg-slate-900/60
                        rounded-xl
                        p-4
                    "
                >

                    <p class="text-xs text-slate-500">
                        الاسم
                    </p>

                    <p
                        id="viewUserName"
                        class="font-semibold mt-1"
                    ></p>

                </div>


                <div
                    class="
                        bg-slate-900/60
                        rounded-xl
                        p-4
                    "
                >

                    <p class="text-xs text-slate-500">
                        البريد الإلكتروني
                    </p>

                    <p
                        id="viewUserEmail"
                        class="font-semibold mt-1"
                    ></p>

                </div>


                <div
                    class="
                        bg-slate-900/60
                        rounded-xl
                        p-4
                    "
                >

                    <p class="text-xs text-slate-500">
                        رقم الهاتف
                    </p>

                    <p
                        id="viewUserPhone"
                        class="font-semibold mt-1"
                    ></p>

                </div>

            </div>


            <div class="mt-6 flex justify-end">

                <button
                    type="button"
                    onclick="closeViewModal()"
                    class="
                        bg-cyan-500
                        hover:bg-cyan-400
                        text-slate-950
                        font-bold
                        px-6
                        py-3
                        rounded-xl
                        transition
                    "
                >

                    إغلاق

                </button>

            </div>

        </div>

    </div>

</div>


{{-- Delete Modal --}}
<div
    id="deleteModal"
    class="
        fixed
        inset-0
        z-50
        hidden
        bg-black/70
        backdrop-blur-sm
        items-center
        justify-center
        p-4
    "
>

    <div
        class="
            w-full
            max-w-md
            bg-slate-800
            border border-slate-700
            rounded-2xl
            shadow-2xl
        "
    >

        <div class="p-6">

            <div class="flex items-center gap-4 mb-5">

                <div
                    class="
                        w-12
                        h-12
                        rounded-xl
                        bg-red-500/10
                        text-red-400
                        flex items-center justify-center
                    "
                >

                    <i class="fa-solid fa-triangle-exclamation"></i>

                </div>

                <div>

                    <h3 class="text-xl font-bold">
                        تأكيد الحذف
                    </h3>

                    <p class="text-slate-400 text-sm mt-1">
                        هذا الإجراء لا يمكن التراجع عنه.
                    </p>

                </div>

            </div>


            <div
                class="
                    bg-slate-900/60
                    rounded-xl
                    p-4
                    mb-6
                "
            >

                <p class="text-sm text-slate-400">

                    المستخدم:

                    <span
                        id="deleteUserName"
                        class="text-white font-semibold"
                    ></span>

                </p>

            </div>


            <form
                id="deleteForm"
                method="POST"
                class="flex gap-3"
            >

                @csrf

                @method('DELETE')


                <button
                    type="button"
                    onclick="closeDeleteModal()"
                    class="
                        flex-1
                        bg-white/5
                        border border-slate-700
                        text-slate-300
                        hover:bg-white/10
                        px-5
                        py-3
                        rounded-xl
                        font-semibold
                        transition
                    "
                >

                    إلغاء

                </button>


                <button
                    type="submit"
                    class="
                        flex-1
                        bg-red-500
                        hover:bg-red-400
                        text-white
                        px-5
                        py-3
                        rounded-xl
                        font-semibold
                        transition
                    "
                >

                    <i class="fa-solid fa-trash ml-2"></i>

                    حذف

                </button>

            </form>

        </div>

    </div>

</div>


@push('scripts')

<script>

    /*
    |--------------------------------------------------------------------------
    | View Modal
    |--------------------------------------------------------------------------
    */

    function viewUser(
        id,
        name,
        email,
        phone
    ) {

        document.getElementById('viewUserName').textContent =
            name || 'لا يوجد';

        document.getElementById('viewUserEmail').textContent =
            email || 'لا يوجد';

        document.getElementById('viewUserPhone').textContent =
            phone || 'لا يوجد';

        const modal =
            document.getElementById('viewUserModal');

        modal.classList.remove('hidden');

        modal.classList.add('flex');
    }


    function closeViewModal() {

        const modal =
            document.getElementById('viewUserModal');

        modal.classList.add('hidden');

        modal.classList.remove('flex');
    }


    /*
    |--------------------------------------------------------------------------
    | Delete Modal
    |--------------------------------------------------------------------------
    */

    function showDeleteModal(
        userId,
        userName
    ) {

        const modal =
            document.getElementById('deleteModal');

        const form =
            document.getElementById('deleteForm');

        const name =
            document.getElementById('deleteUserName');


        name.textContent =
            userName;


        form.action =
            `/admin/users/${userId}`;


        modal.classList.remove('hidden');

        modal.classList.add('flex');
    }


    function closeDeleteModal() {

        const modal =
            document.getElementById('deleteModal');

        modal.classList.add('hidden');

        modal.classList.remove('flex');
    }


    /*
    |--------------------------------------------------------------------------
    | Close Modal By Clicking Outside
    |--------------------------------------------------------------------------
    */

    document
        .getElementById('viewUserModal')
        .addEventListener('click', function (event) {

            if (event.target === this) {
                closeViewModal();
            }

        });


    document
        .getElementById('deleteModal')
        .addEventListener('click', function (event) {

            if (event.target === this) {
                closeDeleteModal();
            }

        });

</script>

@endpush

@endsection