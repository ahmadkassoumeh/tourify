@extends('layouts.admin')

@section('title', 'إدارة المحافظ')

@section('content')

<div class="min-h-screen bg-[#0f172a] text-slate-100 p-6">

    <div class="max-w-4xl mx-auto">

        <div class="mb-8">

            <p class="text-cyan-400 text-sm font-semibold">
                WALLET MANAGEMENT
            </p>

            <h1 class="text-3xl font-bold mt-2">
                إدارة المحافظ
            </h1>

            <p class="text-slate-400 mt-2">
                إضافة رصيد للمستخدمين والمكاتب وشركات الطيران داخل النظام.
            </p>

        </div>


        @if(session('success'))

            <div class="mb-6 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-xl px-5 py-4">

                <i class="fa-solid fa-circle-check ml-2"></i>

                {{ session('success') }}

            </div>

        @endif


        @if($errors->any())

            <div class="mb-6 bg-red-500/10 border border-red-500/20 text-red-400 rounded-xl px-5 py-4">

                @foreach($errors->all() as $error)

                    <p>{{ $error }}</p>

                @endforeach

            </div>

        @endif


        <div
            class="
                bg-slate-800/80
                border border-slate-700
                rounded-2xl
                p-6
                shadow-xl
            "
        >

            <div class="flex items-center gap-4 mb-8">

                <div
                    class="
                        w-14
                        h-14
                        rounded-2xl
                        bg-cyan-500/10
                        border border-cyan-500/20
                        text-cyan-400
                        flex
                        items-center
                        justify-center
                    "
                >

                    <i class="fa-solid fa-wallet text-2xl"></i>

                </div>

                <div>

                    <h2 class="text-xl font-bold">
                        إضافة رصيد
                    </h2>

                    <p class="text-slate-500 text-sm mt-1">
                        اختر الحساب الذي تريد تعبئة محفظته.
                    </p>

                </div>

            </div>


            <form
                method="POST"
                action="{{ route('admin.wallet.add') }}"
            >

                @csrf


                {{-- User --}}
                <div class="mb-6">

                    <label class="block text-sm text-slate-300 mb-2">
                        المستخدم
                    </label>

                    <select
                        id="user_id"
                        name="user_id"
                        required
                        class="
                            w-full
                            bg-slate-900
                            border border-slate-700
                            rounded-xl
                            px-4
                            py-3
                            outline-none
                            focus:border-cyan-400
                        "
                    >

                        <option value="">
                            اختر المستخدم
                        </option>

                        @foreach($users as $user)

                            @php
                                $role = $user->getRoleNames()->first();
                            @endphp

                            <option
                                value="{{ $user->id }}"
                                data-credit="{{ $user->credit }}"
                            >

                                {{ $user->first_name }}
                                {{ $user->last_name }}

                                —
                                {{ $user->username }}

                                —
                                {{ ucfirst($role ?? 'user') }}

                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- Current Credit --}}
                <div
                    class="
                        bg-slate-900/60
                        border border-slate-700
                        rounded-2xl
                        p-5
                        mb-6
                    "
                >

                    <p class="text-sm text-slate-500">
                        الرصيد الحالي
                    </p>

                    <p
                        id="currentCredit"
                        class="
                            text-4xl
                            font-bold
                            text-cyan-400
                            mt-2
                        "
                    >
                        $0.00
                    </p>

                </div>


                {{-- Amount --}}
                <div class="mb-6">

                    <label class="block text-sm text-slate-300 mb-2">
                        المبلغ المراد إضافته
                    </label>

                    <div class="relative">

                        <span
                            class="
                                absolute
                                right-4
                                top-1/2
                                -translate-y-1/2
                                text-cyan-400
                                font-bold
                            "
                        >
                            $
                        </span>

                        <input
                            id="amount"
                            type="number"
                            name="amount"
                            min="1"
                            step="0.01"
                            required
                            placeholder="500"
                            class="
                                w-full
                                bg-slate-900
                                border border-slate-700
                                rounded-xl
                                pr-10
                                pl-4
                                py-4
                                text-xl
                                outline-none
                                focus:border-cyan-400
                            "
                        >

                    </div>

                </div>


                {{-- Preview --}}
                <div
                    class="
                        bg-cyan-500/5
                        border border-cyan-500/20
                        rounded-2xl
                        p-5
                        mb-8
                    "
                >

                    <div class="flex items-center justify-between">

                        <span class="text-slate-400">
                            الرصيد بعد الإضافة
                        </span>

                        <span
                            id="newCredit"
                            class="text-2xl font-bold text-cyan-400"
                        >
                            $0.00
                        </span>

                    </div>

                </div>


                <div class="flex justify-end">

                    <button
                        type="submit"
                        class="
                            bg-cyan-500
                            hover:bg-cyan-400
                            text-slate-950
                            font-bold
                            px-8
                            py-3
                            rounded-xl
                            transition
                            shadow-lg
                            shadow-cyan-500/20
                        "
                    >

                        <i class="fa-solid fa-plus ml-2"></i>

                        إضافة الرصيد

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


@push('scripts')

<script>

    const userSelect =
        document.getElementById('user_id');

    const amountInput =
        document.getElementById('amount');

    const currentCredit =
        document.getElementById('currentCredit');

    const newCredit =
        document.getElementById('newCredit');


    let selectedCredit = 0;


    function updateCreditPreview() {

        const option =
            userSelect.options[userSelect.selectedIndex];

        selectedCredit =
            parseFloat(
                option?.dataset?.credit || 0
            );

        const amount =
            parseFloat(
                amountInput.value || 0
            );

        currentCredit.textContent =
            '$' + selectedCredit.toFixed(2);

        newCredit.textContent =
            '$' + (selectedCredit + amount).toFixed(2);
    }


    userSelect.addEventListener(
        'change',
        updateCreditPreview
    );


    amountInput.addEventListener(
        'input',
        updateCreditPreview
    );

</script>

@endpush

@endsection