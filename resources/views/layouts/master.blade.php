<?php header("Content-Security-Policy: frame-ancestors https://".Auth::user()->name); ?>
  
  @include('layouts.particals.header-css')
  
  @include('layouts.particals.sidebar')

  @include('layouts.particals.header')

  @yield('content')

  @include('layouts.particals.footer')

  @include('layouts.particals.footer-js')