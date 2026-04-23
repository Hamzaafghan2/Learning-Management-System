<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title','To Do APP')</title>
    <link href="{{asset("assets\css\bootstrap-grid.css")}}">
  </head>
  @yield('style')
  <body>
    
      @yield('content')
    
    <script src="{{asset("assets\js\bootstrap.min.js")}}" ></script>
  </body>
</html>

{{-- integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous" --}}