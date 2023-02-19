@extends('layouts.master')
@section('main-content')
<div class="container home">
    <h1>Dashboard</h1>
    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
  @endif
</div>
@endsection()
