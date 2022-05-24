<div class="form-group  @error($field) error @enderror"">
    <div class=" form-label-group position-relative has-icon-left">
    <div class="controls">
        <input type="text" autocomplete="off" id="{{$field}}" @isset($value) value="{{old($field) ? old($field) : $value}}" @else value="{{old($field)}}" @endisset class="form-control @isset($datepicker) pickadate-months-year picker__input @endisset @isset($tanggal) tanggal @endisset @isset($right) text-right @endisset @isset($money) money @endisset" name="{{$field}}" @isset($readonly) readonly @endisset @isset($disabled) @if($disabled=="disabled" ) disabled @endif @endisset @isset($read) @if($read=="true" ) readonly @endif @endisset" placeholder="{{$label}}">
        <div class="form-control-position">
            <i class="{{$icon}}"></i>
        </div>
        @error($field)
        <div class="help-block">
            <ul role="alert">
                <li>{{$message}}</li>
            </ul>
        </div>
        @enderror
    </div>
</div>
</div>
