@extends('layouts.admin')

@section('header')
    @include('admin.chunks.header')
@endsection

@section('sidebar')
    @include('admin.chunks.sidebar')
@endsection

@section('content')
    @include('admin.content.channels')
@endsection

@section('footer')
    @include('admin.chunks.footer')
@endsection
