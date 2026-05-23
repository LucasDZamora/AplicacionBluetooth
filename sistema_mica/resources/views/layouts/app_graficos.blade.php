<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link href="{{ asset('assets/css/bootstrap.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-table@1.22.6/dist/bootstrap-table.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="{{asset('lou-multi-select/css/multi-select.css')}}" media="screen" rel="stylesheet" type="text/css">

</head>
<body>
    <div id="app">
        <main class="py-4">
            @yield('content')
        </main>
    </div>
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdn.amcharts.com/lib/5/index.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/plugins/exporting.js"></script>
    <script src="//cdn.amcharts.com/lib/5/locales/es_ES.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-table@1.22.6/dist/bootstrap-table.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-table@1.22.6/dist/locale/bootstrap-table-es-ES.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tableexport.jquery.plugin@1.29.0/tableExport.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-table@1.22.6/dist/extensions/export/bootstrap-table-export.min.js"></script>
    <script type="text/javascript" src="{{ asset('assets/libs/js-xlsx/xlsx.core.min.js') }}"></script>
    <script type="text/javascript" src="{{asset('lou-multi-select/js/jquery.multi-select.js')}}"></script>
    <script src="{{asset('lou-multi-select/js/jquery.quicksearch.js') }}"></script>
    <script src="{{asset('assets/js/jquery.rut.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('js')
</body>
</html>
