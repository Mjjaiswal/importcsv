@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }} </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if (session('success'))
                        <p style="color: green;">{!! session('success') !!}</p>
                    @endif

                    @if ($errors->any())
                        <p style="color: red;">{{ $errors->first() }}</p>
                    @endif


                    @if(Auth::user()->role == 'exporter')
                        Export CSV File : <a href="{{ route('data.export') }}" class="btn">Export CSV</a>
                    @else
                        <form action="{{ route('data.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <label for="file">Import File:</label>
                            <input type="file" name="file" required>
                            <button type="submit" class="btn">Import</button>
                        </form>
                    @endif


                </div>
            </div>
        </div>
    </div>
</div>
@endsection
