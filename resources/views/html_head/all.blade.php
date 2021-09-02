@php 
  $cur_uri    = request()->path();
  $page_title = ucwords( str_replace( '/' , ' ' , $cur_uri ) );
  $page_id    = strtolower( str_replace( '/' , '_' , $cur_uri ) );
@endphp
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $page_title }}</title>
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body id="page_{{ $page_id }}" class="app sidebar-mini">