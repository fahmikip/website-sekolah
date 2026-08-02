@extends('layouts.admin', ['title'=>'Tulis Berita', 'heading'=>'Tulis Berita'])
@section('content')<form method="POST" action="{{ route('admin.news.store') }}" enctype="multipart/form-data">@csrf @include('admin.news._form')</form>@endsection
