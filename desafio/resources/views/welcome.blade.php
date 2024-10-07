@extends('layouts.main')

@section('content')
<div class="container">
    {!! \Str::markdown(file_get_contents(base_path('README.md'))) !!}
</div>
@endsection
