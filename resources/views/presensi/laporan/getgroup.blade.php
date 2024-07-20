<option value="">Semua Group</option>
@foreach ($group as $d)
    <option value="{{ $d->grup }}">{{ $d->nama_group }}</option>
@endforeach
