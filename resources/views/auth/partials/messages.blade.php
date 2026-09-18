@if (session('status'))
    <p role="status" class="as-alert as-alert--success mt-5">{{ session('status') }}</p>
@endif

@if ($errors->any())
    <div role="alert" class="as-alert as-alert--error mt-5">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif
