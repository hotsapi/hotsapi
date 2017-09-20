@extends('template')
@section('title', 'HotsApi')


@section('head')
    <style>
        iframe {
            position: fixed;
            margin: 52px 0 120px 0;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
            padding: 0;
            border: none;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }
    </style>
@endsection

@section('content')
<iframe src="/swagger"></iframe>
@endsection
