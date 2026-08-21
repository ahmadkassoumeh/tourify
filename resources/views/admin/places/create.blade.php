@extends('layouts.admin')

@section('content')

<div class="min-h-screen bg-[#0f172a] text-slate-100 p-6">

    <div class="max-w-5xl mx-auto">

        <div class="mb-8">

            <p class="text-cyan-400 text-sm font-semibold">
                TOURISM PLACES
            </p>

            <h1 class="text-3xl font-bold mt-2">
                إضافة مكان سياحي أو أثري
            </h1>

            <p class="text-slate-400 mt-2">
                أضف مكاناً جديداً ليظهر ضمن الأماكن السياحية في التطبيق.
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

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        @endif


        <form
            action="{{ route('admin.places.store') }}"
            method="POST"
            enctype="multipart/form-data"
            class="bg-slate-800/80 border border-slate-700 rounded-2xl p-6 shadow-xl"
        >

            @csrf


            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Country --}}
                <div>

                    <label class="block text-sm font-semibold text-slate-300 mb-2">
                        الدولة
                    </label>

                    <select
                        id="country_id"
                        name="country_id"
                        required
                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/20 outline-none"
                    >

                        <option value="">
                            اختر الدولة
                        </option>

                        @foreach($countries as $country)

                            <option
                                value="{{ $country->id }}"
                                {{ old('country_id') == $country->id ? 'selected' : '' }}
                            >
                                {{ $country->name }}
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- City --}}
                <div>

                    <label class="block text-sm font-semibold text-slate-300 mb-2">
                        المدينة
                    </label>

                    <select
                        id="city_id"
                        name="city_id"
                        required
                        disabled
                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/20 outline-none disabled:opacity-50"
                    >

                        <option value="">
                            اختر الدولة أولاً
                        </option>

                    </select>

                </div>


                {{-- Name --}}
                <div class="md:col-span-2">

                    <label class="block text-sm font-semibold text-slate-300 mb-2">
                        اسم المكان
                    </label>

                    <input
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        placeholder="مثال: تدمر الأثرية"
                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 focus:border-cyan-400 outline-none"
                    >

                </div>


                {{-- URL --}}
                <div class="md:col-span-2">

                    <label class="block text-sm font-semibold text-slate-300 mb-2">
                        رابط المكان
                    </label>

                    <input
                        type="url"
                        name="url"
                        value="{{ old('url') }}"
                        placeholder="https://..."
                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 focus:border-cyan-400 outline-none"
                    >

                </div>


                {{-- Description --}}
                <div class="md:col-span-2">

                    <label class="block text-sm font-semibold text-slate-300 mb-2">
                        الوصف
                    </label>

                    <textarea
                        name="description"
                        rows="5"
                        required
                        placeholder="وصف المكان السياحي..."
                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 focus:border-cyan-400 outline-none"
                    >{{ old('description') }}</textarea>

                </div>


                {{-- History --}}
                <div class="md:col-span-2">

                    <label class="block text-sm font-semibold text-slate-300 mb-2">
                        التاريخ
                    </label>

                    <textarea
                        name="history"
                        rows="5"
                        placeholder="التاريخ الأثري أو التاريخ المرتبط بالمكان..."
                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 focus:border-cyan-400 outline-none"
                    >{{ old('history') }}</textarea>

                </div>


                {{-- Images --}}
                <div class="md:col-span-2">

                    <label class="block text-sm font-semibold text-slate-300 mb-2">
                        صور المكان
                    </label>

                    <input
                        type="file"
                        name="images[]"
                        multiple
                        accept="image/jpeg,image/png,image/webp"
                        required
                        class="block w-full text-sm text-slate-400 file:mr-4 file:py-3 file:px-5 file:rounded-xl file:border-0 file:bg-cyan-500 file:text-slate-950 file:font-bold hover:file:bg-cyan-400"
                    >

                    <p class="text-xs text-slate-500 mt-2">
                        يمكن رفع حتى 10 صور. أول صورة سيتم اعتبارها الصورة الرئيسية.
                    </p>

                </div>

            </div>


            <div class="flex justify-end mt-8">

                <button
                    type="submit"
                    class="bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold px-8 py-3 rounded-xl shadow-lg shadow-cyan-500/20 transition"
                >

                    <i class="fa-solid fa-plus ml-2"></i>

                    إضافة المكان

                </button>

            </div>

        </form>

    </div>

</div>


@push('scripts')

<script>

    const countries = @json($countries);

    const countrySelect = document.getElementById('country_id');
    const citySelect = document.getElementById('city_id');

    const oldCityId = @json(old('city_id'));

    function loadCities(countryId) {

        citySelect.innerHTML = '';

        if (!countryId) {

            citySelect.disabled = true;

            citySelect.innerHTML = `
                <option value="">
                    اختر الدولة أولاً
                </option>
            `;

            return;
        }

        const country = countries.find(
            country => country.id == countryId
        );

        citySelect.disabled = false;

        citySelect.innerHTML = `
            <option value="">
                اختر المدينة
            </option>
        `;

        if (!country) {
            return;
        }

        country.cities.forEach(city => {

            const option = document.createElement('option');

            option.value = city.id;

            option.textContent = city.name;

            if (oldCityId == city.id) {
                option.selected = true;
            }

            citySelect.appendChild(option);

        });

    }

    countrySelect.addEventListener('change', function () {

        loadCities(this.value);

    });

    if (countrySelect.value) {

        loadCities(countrySelect.value);

    }

</script>

@endpush

@endsection