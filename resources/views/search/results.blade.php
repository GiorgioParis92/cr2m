@foreach($results as $type => $items)
@dd($items)
    @foreach($items as $item)
        <div>{{ $item->beneficiaire->nom.' '.$item->beneficiaire->prenom }}</div>
    @endforeach
@endforeach
