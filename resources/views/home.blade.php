@extends('layouts.app')

@section('content')
    <x-cms-page-renderer :page="$page" :products="$products ?? null" />
@endsection
