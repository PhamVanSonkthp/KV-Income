@if(count($items) > 0)
    @foreach($items as $item)
        <a class="show_list--item" href="{{ route('administrator.'.$prefixView.'.detail', ['id' => $item->id]) }}">
            <div class="id_item">{{ $item->id }}</div>
            @if(strpos($_SERVER['REQUEST_URI'], 'orders') !== false)
                <div class="name_item">{{ $item->code }}</div>
            @else
                <div class="name_item">{{ $item->name }}</div>
            @endif
            <div class="time_item">{{ \App\Models\Helper::convert_date_from_db($item->created_at) }}</div>
        </a>
    @endforeach
@endif
