@extends('layouts.admin')

@section('header')
    @include('admin.chunks.header')
@endsection

@section('sidebar')
    @include('admin.chunks.sidebar')
@endsection

@section('content')
    @include('admin.content.index')
@endsection

@section('footer')
    @include('admin.chunks.footer')
@endsection
