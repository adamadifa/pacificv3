@if ($message = Session::get('success'))
<div class="alert alert-success" role="alert">
    <h4 class="alert-heading">Success</h4>
    <p class="mb-0">
        {{$message}}
    </p>
</div>

@endif
@if ($message = Session::get('failed'))

@endif

@if ($message = Session::get('warning'))
<div class="alert alert-warning" role="alert">
    <h4 class="alert-heading">Warning</h4>
    <p class="mb-0">
        {{$message}}
    </p>
</div>
@endif
