<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Tourify Admin')</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    >

    <style>

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Scrollbar */

        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #0f172a;
        }

        ::-webkit-scrollbar-thumb {
            background: #0891b2;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #06b6d4;
        }

        /* Animations */

        @keyframes slideIn {

            from {
                opacity: 0;
                transform: translateY(15px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }

        }

        .animate-slide-in {
            animation: slideIn 0.35s ease-out;
        }

        /* Glass effect */

        .glass {
            background: rgba(15, 23, 42, 0.78);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }

    </style>

    @stack('styles')

</head>


<body class="bg-[#0f172a] text-slate-100 min-h-screen">


    <!--
    |--------------------------------------------------------------------------
    | Top Navigation
    |--------------------------------------------------------------------------
    -->

    <nav
        class="glass border-b border-cyan-500/10 shadow-2xl sticky top-0 z-50"
    >

        <div class="max-w-7xl mx-auto px-4 lg:px-6">

            <div class="flex justify-between items-center py-4">


                <!-- Logo -->

                <div class="flex items-center gap-4">

                    <div
                        class="w-12 h-12 rounded-2xl
                        bg-cyan-500/10
                        border border-cyan-400/20
                        flex items-center justify-center
                        shadow-lg shadow-cyan-500/5"
                    >

                        <i
                            class="fa-solid fa-earth-americas
                            text-cyan-400 text-xl"
                        ></i>

                    </div>


                    <div>

                        <h1 class="text-white font-bold text-xl">
                            Tourify
                        </h1>

                        <p class="text-slate-400 text-xs">
                            لوحة تحكم النظام
                        </p>

                    </div>

                </div>


                <!-- Navigation -->

                <div class="hidden lg:flex items-center gap-7">


                    <!-- Dashboard -->

                    <a
                        href="{{ route('admin.dashboard') }}"
                        class="
                            flex items-center gap-2
                            text-slate-300
                            hover:text-cyan-400
                            transition duration-200
                            {{ request()->routeIs('admin.dashboard')
                                ? 'text-cyan-400'
                                : '' }}
                        "
                    >

                        <i class="fa-solid fa-chart-line"></i>

                        لوحة التحكم

                    </a>


                    <!-- Places -->

                    <a
                        href="{{ route('admin.places.create') }}"
                        class="
                            flex items-center gap-2
                            text-slate-300
                            hover:text-cyan-400
                            transition duration-200
                            {{ request()->routeIs('admin.places.*')
                                ? 'text-cyan-400'
                                : '' }}
                        "
                    >

                        <i class="fa-solid fa-location-dot"></i>

                        الأماكن

                    </a>


                    <!-- Airlines -->

                    <a
                        href="{{ route('admin.airlines.create') }}"
                        class="
                            flex items-center gap-2
                            text-slate-300
                            hover:text-cyan-400
                            transition duration-200
                            {{ request()->routeIs('admin.airlines.*')
                                ? 'text-cyan-400'
                                : '' }}
                        "
                    >

                        <i class="fa-solid fa-plane"></i>

                        الطيران

                    </a>


                    <!-- Pending Agencies -->

                    <a
                        href="{{ route('admin.users.pending') }}"
                        class="
                            flex items-center gap-2
                            text-slate-300
                            hover:text-cyan-400
                            transition duration-200
                        "
                    >

                        <i class="fa-solid fa-building"></i>

                        طلبات المكاتب

                        @php
                            $pendingAgenciesCount =
                                \App\Models\User::role('agency')
                                    ->where(
                                        'status',
                                        \App\Enums\UserStatusEnum::PENDING
                                    )
                                    ->count();
                        @endphp

                        @if($pendingAgenciesCount > 0)

                            <span
                                class="
                                    min-w-5 h-5 px-1.5
                                    rounded-full
                                    bg-cyan-500
                                    text-slate-950
                                    text-[10px]
                                    font-bold
                                    flex items-center justify-center
                                "
                            >
                                {{ $pendingAgenciesCount }}
                            </span>

                        @endif

                    </a>

                </div>


                <!-- Right Section -->

                <div class="flex items-center gap-3">


                    <!-- Notification -->

                    <button
                        type="button"
                        class="
                            relative
                            w-10 h-10
                            rounded-xl
                            flex items-center justify-center
                            text-slate-400
                            hover:text-cyan-400
                            hover:bg-cyan-500/10
                            transition
                        "
                    >

                        <i class="fa-regular fa-bell text-lg"></i>


                        @if(isset($pendingAgenciesCount) && $pendingAgenciesCount > 0)

                            <span
                                class="
                                    absolute
                                    top-1
                                    right-1
                                    w-2
                                    h-2
                                    rounded-full
                                    bg-cyan-400
                                    shadow-[0_0_8px_rgba(34,211,238,0.8)]
                                "
                            ></span>

                        @endif

                    </button>


                    <!-- User -->

                    <div class="relative group">


                        <button
                            type="button"
                            class="
                                flex items-center gap-3
                                px-3 py-2
                                rounded-xl
                                bg-white/5
                                border border-white/5
                                hover:bg-cyan-500/10
                                hover:border-cyan-500/20
                                transition
                            "
                        >

                            <div
                                class="
                                    w-10 h-10
                                    rounded-xl
                                    bg-cyan-500/10
                                    border border-cyan-400/20
                                    flex items-center justify-center
                                    text-cyan-400
                                    font-bold
                                "
                            >

                                {{ strtoupper(
                                    substr(
                                        auth()->user()->first_name ?? 'A',
                                        0,
                                        1
                                    )
                                ) }}

                            </div>


                            <div class="hidden sm:block text-right">

                                <p class="text-white text-sm font-semibold">

                                    {{ auth()->user()->first_name }}
                                    {{ auth()->user()->last_name }}

                                </p>

                                <p class="text-cyan-400 text-xs">

                                    مدير النظام

                                </p>

                            </div>


                            <i
                                class="
                                    fa-solid
                                    fa-chevron-down
                                    text-xs
                                    text-slate-500
                                "
                            ></i>

                        </button>


                        <!-- Dropdown -->

                        <div
                            class="
                                absolute
                                left-0
                                top-full
                                mt-2
                                w-52
                                bg-slate-800/95
                                backdrop-blur-xl
                                border border-slate-700
                                rounded-2xl
                                shadow-2xl
                                opacity-0
                                invisible
                                group-hover:opacity-100
                                group-hover:visible
                                transition-all
                                duration-200
                                overflow-hidden
                            "
                        >

                            <div class="p-2">


                                <div
                                    class="
                                        px-4 py-3
                                        mb-1
                                        rounded-xl
                                        bg-cyan-500/5
                                        border border-cyan-500/10
                                    "
                                >

                                    <p class="text-white text-sm font-semibold">

                                        {{ auth()->user()->username }}

                                    </p>

                                    <p class="text-slate-500 text-xs mt-1">

                                        حساب المدير

                                    </p>

                                </div>


                                <!-- Logout -->

                                <form
                                    method="POST"
                                    action="{{ route('logout') }}"
                                >

                                    @csrf

                                    <button
                                        type="submit"
                                        class="
                                            w-full
                                            text-right
                                            px-4 py-3
                                            rounded-xl
                                            text-slate-300
                                            hover:bg-red-500/10
                                            hover:text-red-400
                                            transition
                                            flex items-center gap-3
                                        "
                                    >

                                        <i class="fa-solid fa-right-from-bracket"></i>

                                        تسجيل الخروج

                                    </button>

                                </form>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </nav>


    <!--
    |--------------------------------------------------------------------------
    | Mobile Navigation
    |--------------------------------------------------------------------------
    -->

    <div class="lg:hidden glass border-b border-cyan-500/10 px-4 py-3">

        <div class="flex gap-2 overflow-x-auto pb-1">


            <a
                href="{{ route('admin.dashboard') }}"
                class="
                    whitespace-nowrap
                    px-4 py-2
                    rounded-xl
                    text-sm
                    border
                    transition
                    {{ request()->routeIs('admin.dashboard')
                        ? 'bg-cyan-500 text-slate-950 border-cyan-500'
                        : 'bg-white/5 text-slate-300 border-white/5 hover:text-cyan-400' }}
                "
            >

                <i class="fa-solid fa-chart-line ml-1"></i>

                الرئيسية

            </a>


            <a
                href="{{ route('admin.places.create') }}"
                class="
                    whitespace-nowrap
                    px-4 py-2
                    rounded-xl
                    text-sm
                    border
                    transition
                    {{ request()->routeIs('admin.places.*')
                        ? 'bg-cyan-500 text-slate-950 border-cyan-500'
                        : 'bg-white/5 text-slate-300 border-white/5 hover:text-cyan-400' }}
                "
            >

                <i class="fa-solid fa-location-dot ml-1"></i>

                إضافة مكان

            </a>


            <a
                href="{{ route('admin.airlines.create') }}"
                class="
                    whitespace-nowrap
                    px-4 py-2
                    rounded-xl
                    text-sm
                    border
                    transition
                    {{ request()->routeIs('admin.airlines.*')
                        ? 'bg-cyan-500 text-slate-950 border-cyan-500'
                        : 'bg-white/5 text-slate-300 border-white/5 hover:text-cyan-400' }}
                "
            >

                <i class="fa-solid fa-plane ml-1"></i>

                إضافة طيران

            </a>


            <a
                href="{{ route('admin.users.pending') }}"
                class="
                    whitespace-nowrap
                    px-4 py-2
                    rounded-xl
                    text-sm
                    border
                    transition
                "
            >

                <i class="fa-solid fa-building ml-1"></i>

                طلبات المكاتب

            </a>

        </div>

    </div>


    <!--
    |--------------------------------------------------------------------------
    | Main Content
    |--------------------------------------------------------------------------
    -->

    <main class="min-h-screen animate-slide-in">

        @yield('content')

    </main>


    <!--
    |--------------------------------------------------------------------------
    | Footer
    |--------------------------------------------------------------------------
    -->

    <footer
        class="
            border-t
            border-cyan-500/10
            bg-slate-950/70
            mt-12
        "
    >

        <div
            class="
                max-w-7xl
                mx-auto
                px-6
                py-7
                flex
                flex-col
                md:flex-row
                justify-between
                items-center
                gap-4
            "
        >


            <!-- Brand -->

            <div class="flex items-center gap-3">

                <div
                    class="
                        w-9 h-9
                        rounded-xl
                        bg-cyan-500/10
                        border border-cyan-500/20
                        flex items-center justify-center
                    "
                >

                    <i
                        class="
                            fa-solid
                            fa-earth-americas
                            text-cyan-400
                        "
                    ></i>

                </div>

                <div>

                    <p class="text-white font-semibold text-sm">

                        Tourify

                    </p>

                    <p class="text-slate-500 text-xs">

                        نظام الإدارة السياحية

                    </p>

                </div>

            </div>


            <!-- Copyright -->

            <p class="text-slate-500 text-xs text-center">

                © {{ date('Y') }} Tourify
                —
                جميع الحقوق محفوظة

            </p>


            <!-- Status -->

            <div
                class="
                    flex
                    items-center
                    gap-2
                    text-xs
                    text-slate-400
                "
            >

                <span
                    class="
                        w-2
                        h-2
                        rounded-full
                        bg-emerald-400
                        shadow-[0_0_8px_rgba(52,211,153,0.8)]
                    "
                ></span>

                النظام يعمل

            </div>

        </div>

    </footer>


    @stack('scripts')

</body>

</html>