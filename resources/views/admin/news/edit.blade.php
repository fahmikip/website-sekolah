@extends('layouts.admin', ['title'=>'Edit Berita', 'heading'=>'Edit Berita'])
@section('content')<form method="POST" action="{{ route('admin.news.update',$news) }}" enctype="multipart/form-data">@csrf @method('PUT') @include('admin.news._form')</form>@endsection
