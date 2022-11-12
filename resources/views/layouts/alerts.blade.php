@if(\Illuminate\Support\Facades\Session::has('success'))
    <div class="alert alert-success">
        <i class="fa fa-check-circle"></i>
        {{ \Illuminate\Support\Facades\Session::get('success') }}
    </div>
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
    <div class="alert alert-danger">
        <i class="fa fa-exclamation-triangle"></i>
        {{ $firstErrorMessage }}
        @foreach($errorMessages as $errorMessage)
            <div>{{ $errorMessage }}</div>
        @endforeach
    </div>
@endif
