<div>
    @if(!empty(session('alert')))
    <div class="row">
        <div class="col-12">
            <div class="alert alert-{{ session('alert') }}">
                {{ session('msg') }}
            </div>
        </div>
    </div>
    @endif
    <div class="row">
        <div class="col-3">

            <div class="form-group @error('kontrak_ke') error @enderror">
                <select name="kontrak_ke" id="kontrak_ke" wire:model="kontrak_ke" class="form-control">
                    <option value="">Kontrak Ke</option>
                    @for ($i=1; $i<=10; $i++) <option value="{{ $i }}">{{ $i }}</option>@endfor
                </select>
                @error('kontrak_ke')
                <div class="help-block">
                    <ul role="alert">
                        <li>{{$message}}</li>
                    </ul>
                </div>
                @enderror
            </div>
        </div>
        <div class="col-3">
            <div class="form-group @error('dari') error @enderror">
                <div class="form-label-group position-relative has-icon-left">
                    <div class="controls">
                        <input type="text" class="form-control pickadate-months-year picker__input" name="dari" placeholder="Dari" wire:model="dari" onchange="this.dispatchEvent(new InputEvent('input'))" autocomplete="off">
                        <div class="form-control-position">
                            <i class="feather icon-calendar"></i>
                        </div>
                        @error('dari')
                        <div class="help-block">
                            <ul role="alert">
                                <li>{{$message}}</li>
                            </ul>
                        </div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        <div class="col-3">
            <div class="form-group @error('sampai') error @enderror">
                <div class="form-label-group position-relative has-icon-left">
                    <div class="controls">
                        <input type="text" class="form-control pickadate-months-year picker__input" name="sampai" placeholder="Sampai" wire:model="sampai" onchange="this.dispatchEvent(new InputEvent('input'))" autocomplete="off">
                        <div class="form-control-position">
                            <i class="feather icon-calendar"></i>
                        </div>
                        @error('sampai')
                        <div class="help-block">
                            <ul role="alert">
                                <li>{{$message}}</li>
                            </ul>
                        </div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        <div class="col-3">
            <div class="form-group">
                <button class="btn btn-primary" wire:click="save"><i class="feather icon-plus"></i></button>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Kontrak Ke</th>
                        <th>Periode Kontrak</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($kontrak as $d)
                    <tr>
                        <td>{{ $d->kontrak_ke }}</td>
                        <td>{{ DateToIndo2($d->dari) }} s/d {{ DateToIndo2($d->sampai) }}</td>
                        <td><a href="#" wire:click="delete({{ $d->id }})"><i class="feather icon-trash danger"></i></a></td>
                    </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
    </div>
</div>
<script>
    window.addEventListener('contentChanged', (e) => {
        $('.pickadate-months-year').pickadate({
            selectYears: true
            , selectMonths: true
            , format: 'yyyy-mm-dd'
        });

    });

</script>
