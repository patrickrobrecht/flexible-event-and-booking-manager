@if(\Illuminate\Support\Facades\Session::has('success'))
    <x-bs::alert variant="success">
        <i class="fa fa-check-circle"></i>
        {{ \Illuminate\Support\Facades\Session::get('success') }}
    </x-bs::alert>
@endif

@if(\Illuminate\Support\Facades\Session::has('error'))
    @php
        $error = \Illuminate\Support\Facades\Session::get('error');
        if (is_array($error)) {
            $firstErrorMessage = $error[0] ?? '';
            $errorMessages = array_slice($error, 1);
        } else {
            $firstErrorMessage = $error;
            $errorMessages = [];
        }
    @endphp
    <x-bs::alert variant="danger">
        <i class="fa fa-exclamation-triangle"></i>
        {{ $firstErrorMessage }}
        @foreach($errorMessages as $errorMessage)
            <div>{{ $errorMessage }}</div>
        @endforeach
    </x-bs::alert>
@endif
