@extends('layouts.admin')

@section('content')

<div class="min-h-screen bg-[#0f172a] text-slate-100 p-6">

    <div class="max-w-4xl mx-auto">

        <div class="mb-8">

            <p class="text-cyan-400 text-sm font-semibold">
                AIRLINES
            </p>

            <h1 class="text-3xl font-bold mt-2">
                إضافة شركة طيران
            </h1>

            <p class="text-slate-400 mt-2">
                إنشاء حساب لشركة الطيران وربطه بكيان شركة الطيران داخل النظام.
            </p>

        </div>


        @if(session('success'))

            <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400">
                {{ session('success') }}
            </div>

        @endif


        @if($errors->any())

            <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/30">

                <ul class="space-y-1 text-red-400">

                    @foreach($errors->all() as $error)

                        <li>{{ $error }}</li>

                    @endforeach

                </ul>

            </div>

        @endif


        <form
            method="POST"
            action="{{ route('admin.airlines.store') }}"
            class="bg-slate-800/80 border border-slate-700 rounded-2xl p-6 shadow-xl"
        >

            @csrf


            <div class="mb-8">

                <h2 class="text-lg font-bold mb-1">
                    معلومات شركة الطيران
                </h2>

                <p class="text-slate-500 text-sm">
                    هذه البيانات تخص كيان شركة الطيران نفسه.
                </p>

            </div>


            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div class="md:col-span-2">

                    <label class="block text-sm font-semibold text-slate-300 mb-2">
                        اسم شركة الطيران
                    </label>

                    <input
                        type="text"
                        name="airline_name"
                        value="{{ old('airline_name') }}"
                        required
                        placeholder="مثال: Syrian Air"
                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 focus:border-cyan-400 outline-none"
                    >

                </div>


                <div>

                    <label class="block text-sm font-semibold text-slate-300 mb-2">
                        اسم المستخدم
                    </label>

                    <input
                        type="text"
                        name="username"
                        value="{{ old('username') }}"
                        required
                        placeholder="syrian_air"
                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 focus:border-cyan-400 outline-none"
                    >

                </div>


                <div>

                    <label class="block text-sm font-semibold text-slate-300 mb-2">
                        البريد الإلكتروني
                    </label>

                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="airline@example.com"
                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 focus:border-cyan-400 outline-none"
                    >

                </div>


                <div>

                    <label class="block text-sm font-semibold text-slate-300 mb-2">
                        الاسم الأول
                    </label>

                    <input
                        type="text"
                        name="first_name"
                        value="{{ old('first_name') }}"
                        required
                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 focus:border-cyan-400 outline-none"
                    >

                </div>


                <div>

                    <label class="block text-sm font-semibold text-slate-300 mb-2">
                        الاسم الأخير
                    </label>

                    <input
                        type="text"
                        name="last_name"
                        value="{{ old('last_name') }}"
                        required
                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 focus:border-cyan-400 outline-none"
                    >

                </div>


                <div class="md:col-span-2">

                    <label class="block text-sm font-semibold text-slate-300 mb-2">
                        رقم الهاتف
                    </label>

                    <input
                        type="text"
                        name="phone_number"
                        value="{{ old('phone_number') }}"
                        required
                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 focus:border-cyan-400 outline-none"
                    >

                </div>


                <div>

                    <label class="block text-sm font-semibold text-slate-300 mb-2">
                        كلمة المرور
                    </label>

                    <input
                        type="password"
                        name="password"
                        required
                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 focus:border-cyan-400 outline-none"
                    >

                </div>


                <div>

                    <label class="block text-sm font-semibold text-slate-300 mb-2">
                        تأكيد كلمة المرور
                    </label>

                    <input
                        type="password"
                        name="password_confirmation"
                        required
                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 focus:border-cyan-400 outline-none"
                    >

                </div>

            </div>


            <div class="mt-6 bg-cyan-500/5 border border-cyan-500/20 rounded-xl p-4">

                <div class="flex gap-3">

                    <i class="fa-solid fa-circle-info text-cyan-400 mt-1"></i>

                    <div>

                        <p class="font-semibold text-cyan-300">
                            ملاحظة
                        </p>

                        <p class="text-sm text-slate-400 mt-1">
                            سيتم إنشاء حساب للشركة بدور Airline وحالته Approved مباشرة، وبعدها تستطيع إدارة الرحلات والجداول من التطبيق.
                        </p>

                    </div>

                </div>

            </div>


            <div class="flex justify-end mt-8">

                <button
                    type="submit"
                    class="bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold px-8 py-3 rounded-xl shadow-lg shadow-cyan-500/20 transition"
                >

                    <i class="fa-solid fa-plane ml-2"></i>

                    إضافة شركة الطيران

                </button>

            </div>

        </form>

    </div>

</div>

@endsection