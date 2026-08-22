@extends('layouts.admin')

@section('title', 'إضافة فندق')

@section('content')

<div class="min-h-screen bg-[#0f172a] text-slate-100 p-6">

    <div class="max-w-6xl mx-auto">

        {{-- Header --}}
        <div class="mb-8">

            <p class="text-cyan-400 text-sm font-semibold">
                HOTELS
            </p>

            <h1 class="text-3xl font-bold mt-2">
                إضافة فندق
            </h1>

            <p class="text-slate-400 mt-2">
                أضف الفندق وحدد عدد الغرف والسعر لكل نوع.
            </p>

        </div>


        {{-- Success --}}
        @if(session('success'))

            <div class="mb-6 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-xl px-5 py-4">

                <i class="fa-solid fa-circle-check ml-2"></i>

                {{ session('success') }}

            </div>

        @endif


        {{-- Errors --}}
        @if($errors->any())

            <div class="mb-6 bg-red-500/10 border border-red-500/20 text-red-400 rounded-xl px-5 py-4">

                <ul class="space-y-1">

                    @foreach($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        @endif


        <form
            method="POST"
            action="{{ route('admin.hotels.store') }}"
            enctype="multipart/form-data"
        >

            @csrf


            {{-- Basic Information --}}
            <div
                class="
                    bg-slate-800/80
                    border border-slate-700
                    rounded-2xl
                    p-6
                    mb-6
                "
            >

                <h2 class="text-xl font-bold mb-6">
                    معلومات الفندق
                </h2>


                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">


                    {{-- Country --}}
                    <div>

                        <label class="block text-sm text-slate-300 mb-2">
                            الدولة
                        </label>

                        <select
                            id="country_id"
                            name="country_id"
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
                                اختر الدولة
                            </option>

                            @foreach($countries as $country)

                                <option
                                    value="{{ $country->id }}"
                                >
                                    {{ $country->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- City --}}
                    <div>

                        <label class="block text-sm text-slate-300 mb-2">
                            المدينة
                        </label>

                        <select
                            id="city_id"
                            name="city_id"
                            required
                            disabled
                            class="
                                w-full
                                bg-slate-900
                                border border-slate-700
                                rounded-xl
                                px-4
                                py-3
                                outline-none
                                focus:border-cyan-400
                                disabled:opacity-40
                            "
                        >

                            <option value="">
                                اختر الدولة أولاً
                            </option>

                        </select>

                    </div>


                    {{-- Name --}}
                    <div>

                        <label class="block text-sm text-slate-300 mb-2">
                            اسم الفندق
                        </label>

                        <input
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
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
                            placeholder="مثال: Sheraton Damascus"
                        >

                    </div>


                    {{-- Phone --}}
                    <div>

                        <label class="block text-sm text-slate-300 mb-2">
                            رقم الهاتف
                        </label>

                        <input
                            type="text"
                            name="phone"
                            value="{{ old('phone') }}"
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

                    </div>


                    {{-- Description --}}
                    <div class="md:col-span-2">

                        <label class="block text-sm text-slate-300 mb-2">
                            الوصف
                        </label>

                        <textarea
                            name="description"
                            rows="5"
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
                        >{{ old('description') }}</textarea>

                    </div>


                    {{-- Images --}}
                    <div class="md:col-span-2">

                        <label class="block text-sm text-slate-300 mb-2">
                            صور الفندق
                        </label>

                        <input
                            type="file"
                            name="images[]"
                            multiple
                            required
                            accept="image/jpeg,image/jpg,image/png,image/webp"
                            class="
                                block
                                w-full
                                text-sm
                                text-slate-400
                                file:bg-cyan-500
                                file:text-slate-950
                                file:border-0
                                file:rounded-xl
                                file:px-5
                                file:py-3
                                file:font-bold
                            "
                        >

                    </div>

                </div>

            </div>


            {{-- Rooms --}}
            <div
                class="
                    bg-slate-800/80
                    border border-slate-700
                    rounded-2xl
                    p-6
                    mb-6
                "
            >

                <div class="mb-6">

                    <h2 class="text-xl font-bold">
                        أنواع الغرف
                    </h2>

                    <p class="text-slate-400 text-sm mt-1">
                        حدد عدد الغرف والسعر لكل نوع.
                    </p>

                </div>


                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">


                    {{-- A --}}
                    <div
                        class="
                            bg-slate-900/70
                            border border-cyan-500/10
                            rounded-2xl
                            p-5
                        "
                    >

                        <div class="flex items-center justify-between mb-5">

                            <div>

                                <h3 class="font-bold text-lg">
                                    غرفة A
                                </h3>

                                <p class="text-slate-500 text-sm">
                                    تتسع لـ 4 أشخاص
                                </p>

                            </div>

                            <span
                                class="
                                    w-10
                                    h-10
                                    rounded-xl
                                    bg-cyan-500/10
                                    text-cyan-400
                                    flex
                                    items-center
                                    justify-center
                                    font-bold
                                "
                            >
                                A
                            </span>

                        </div>


                        <div class="grid grid-cols-2 gap-4">

                            <div>

                                <label class="block text-xs text-slate-400 mb-2">
                                    عدد الغرف
                                </label>

                                <input
                                    type="number"
                                    name="rooms[A][quantity]"
                                    min="0"
                                    value="{{ old('rooms.A.quantity', 50) }}"
                                    required
                                    class="
                                        w-full
                                        bg-slate-800
                                        border border-slate-700
                                        rounded-xl
                                        px-4
                                        py-3
                                        outline-none
                                        focus:border-cyan-400
                                    "
                                >

                            </div>


                            <div>

                                <label class="block text-xs text-slate-400 mb-2">
                                    السعر
                                </label>

                                <input
                                    type="number"
                                    name="rooms[A][price]"
                                    min="0"
                                    step="0.01"
                                    value="{{ old('rooms.A.price', 100) }}"
                                    required
                                    class="
                                        w-full
                                        bg-slate-800
                                        border border-slate-700
                                        rounded-xl
                                        px-4
                                        py-3
                                        outline-none
                                        focus:border-cyan-400
                                    "
                                >

                            </div>

                        </div>

                    </div>


                    {{-- B --}}
                    <div
                        class="
                            bg-slate-900/70
                            border border-cyan-500/10
                            rounded-2xl
                            p-5
                        "
                    >

                        <div class="flex items-center justify-between mb-5">

                            <div>

                                <h3 class="font-bold text-lg">
                                    غرفة B
                                </h3>

                                <p class="text-slate-500 text-sm">
                                    تتسع لـ 3 أشخاص
                                </p>

                            </div>

                            <span
                                class="
                                    w-10
                                    h-10
                                    rounded-xl
                                    bg-cyan-500/10
                                    text-cyan-400
                                    flex
                                    items-center
                                    justify-center
                                    font-bold
                                "
                            >
                                B
                            </span>

                        </div>


                        <div class="grid grid-cols-2 gap-4">

                            <div>

                                <label class="block text-xs text-slate-400 mb-2">
                                    عدد الغرف
                                </label>

                                <input
                                    type="number"
                                    name="rooms[B][quantity]"
                                    min="0"
                                    value="{{ old('rooms.B.quantity', 50) }}"
                                    required
                                    class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 outline-none focus:border-cyan-400"
                                >

                            </div>


                            <div>

                                <label class="block text-xs text-slate-400 mb-2">
                                    السعر
                                </label>

                                <input
                                    type="number"
                                    name="rooms[B][price]"
                                    min="0"
                                    step="0.01"
                                    value="{{ old('rooms.B.price', 75) }}"
                                    required
                                    class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 outline-none focus:border-cyan-400"
                                >

                            </div>

                        </div>

                    </div>


                    {{-- C --}}
                    <div
                        class="
                            bg-slate-900/70
                            border border-cyan-500/10
                            rounded-2xl
                            p-5
                        "
                    >

                        <div class="flex items-center justify-between mb-5">

                            <div>

                                <h3 class="font-bold text-lg">
                                    غرفة C
                                </h3>

                                <p class="text-slate-500 text-sm">
                                    تتسع لشخصين
                                </p>

                            </div>

                            <span class="w-10 h-10 rounded-xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center font-bold">
                                C
                            </span>

                        </div>


                        <div class="grid grid-cols-2 gap-4">

                            <div>

                                <label class="block text-xs text-slate-400 mb-2">
                                    عدد الغرف
                                </label>

                                <input
                                    type="number"
                                    name="rooms[C][quantity]"
                                    min="0"
                                    value="{{ old('rooms.C.quantity', 50) }}"
                                    required
                                    class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 outline-none focus:border-cyan-400"
                                >

                            </div>


                            <div>

                                <label class="block text-xs text-slate-400 mb-2">
                                    السعر
                                </label>

                                <input
                                    type="number"
                                    name="rooms[C][price]"
                                    min="0"
                                    step="0.01"
                                    value="{{ old('rooms.C.price', 50) }}"
                                    required
                                    class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 outline-none focus:border-cyan-400"
                                >

                            </div>

                        </div>

                    </div>


                    {{-- D --}}
                    <div
                        class="
                            bg-slate-900/70
                            border border-cyan-500/10
                            rounded-2xl
                            p-5
                        "
                    >

                        <div class="flex items-center justify-between mb-5">

                            <div>

                                <h3 class="font-bold text-lg">
                                    غرفة D
                                </h3>

                                <p class="text-slate-500 text-sm">
                                    تتسع لشخص واحد
                                </p>

                            </div>

                            <span class="w-10 h-10 rounded-xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center font-bold">
                                D
                            </span>

                        </div>


                        <div class="grid grid-cols-2 gap-4">

                            <div>

                                <label class="block text-xs text-slate-400 mb-2">
                                    عدد الغرف
                                </label>

                                <input
                                    type="number"
                                    name="rooms[D][quantity]"
                                    min="0"
                                    value="{{ old('rooms.D.quantity', 50) }}"
                                    required
                                    class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 outline-none focus:border-cyan-400"
                                >

                            </div>


                            <div>

                                <label class="block text-xs text-slate-400 mb-2">
                                    السعر
                                </label>

                                <input
                                    type="number"
                                    name="rooms[D][price]"
                                    min="0"
                                    step="0.01"
                                    value="{{ old('rooms.D.price', 25) }}"
                                    required
                                    class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 outline-none focus:border-cyan-400"
                                >

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- Submit --}}
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
                        shadow-lg
                        shadow-cyan-500/20
                        transition
                    "
                >

                    <i class="fa-solid fa-hotel ml-2"></i>

                    إضافة الفندق

                </button>

            </div>

        </form>

    </div>

</div>


@push('scripts')

<script>

    const countries = @json($countries);

    const countrySelect =
        document.getElementById('country_id');

    const citySelect =
        document.getElementById('city_id');


    countrySelect.addEventListener(
        'change',
        function () {

            const countryId = this.value;

            citySelect.innerHTML =
                '<option value="">اختر المدينة</option>';

            if (!countryId) {

                citySelect.disabled = true;

                return;

            }

            const country =
                countries.find(
                    country => country.id == countryId
                );

            if (!country) {

                citySelect.disabled = true;

                return;

            }

            citySelect.disabled = false;

            country.cities.forEach(city => {

                const option =
                    document.createElement('option');

                option.value = city.id;

                option.textContent = city.name;

                citySelect.appendChild(option);

            });

        }
    );

</script>

@endpush

@endsection