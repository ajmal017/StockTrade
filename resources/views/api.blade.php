@extends('templates/master-bulma')

@section('title', 'Watchlist')

@section('content')
    @verbatim
        <section class="hero">
            <div class="hero-body">
                <div class="container">
                    <h1 class="title">
                        Documentation
                    </h1>
                    <h2 class="subtitle">
                        Stock Trade API
                    </h2>
                </div>
            </div>
        </section>
        <div id="docs" class="container">

        </div>
    @endverbatim
@endsection

@push('styles')
    <link href="{{ asset('css/stocktrade-main.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css" media="all" />
@endpush

@push('scripts')
    <script> var hostname = "{{ url('/') }}"; </script>
    <script src="{{ asset('js/manifest.js') }}" charset="utf-8"></script>
    <script src="{{ asset('js/vendor.js')   }}" charset="utf-8"></script>
    <script src="{{ asset('js/app.js')      }}" charset="utf-8"></script>
    <script type="text/javascript" src="{{ asset('api/v1/js/docs.js') }}"></script>
@endpush
