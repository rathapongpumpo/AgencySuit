@if (session('status'))
    <p role="status" class="mt-5 rounded-xl bg-green-50 px-4 py-3 text-sm text-green-900">{{ session('status') }}</p>
@endif

@if ($errors->any())
    <div role="alert" class="mt-5 rounded-xl bg-red-50 px-4 py-3 text-sm text-red-800">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif
