<option value="">Semua Kantor</option>
@foreach ($kantor as $d)
<option value="{{ $d->id_kantor }}">{{ $d->id_kantor }}</option>
@endforeach
