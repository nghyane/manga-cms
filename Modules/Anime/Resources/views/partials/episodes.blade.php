@php
    $range = [];
    $max_items = 100;

    $episodes = $episodes->chunk($max_items);

    foreach ($episodes as $key => $item) {
        if ($key == $episodes->count() - 1) {
            $range[] = [
                'range' => sprintf('%03d-%03d', $key * $max_items + 1, $key * $max_items + $item->count()),
                'episodes' => $item,
            ];
            break;
        }

        $range[] = [
            'range' => sprintf('%03d-%03d', $key * $max_items + 1, $key * $max_items + $max_items),
            'episodes' => $item,
        ];
    }

@endphp

<div class="ep-filters">
    <div class="dropdown filter type"> <button data-toggle="dropdown" class="btn btn-sm btn-secondary dropdown-toggle">Sub
            &amp; Dub</button>
        <div class="dropdown-menu">
            <div class="dropdown-item active" data-value="">Sub &amp; Dub</div>
            <div class="dropdown-item" data-value="sub">Only Sub</div>
            <div class="dropdown-item" data-value="dub">Only Dub</div>
        </div>
    </div>
    <div class="dropdown filter range">
        <button data-toggle="dropdown" class="btn btn-sm btn-secondary dropdown-toggle"></button>
        <div class="dropdown-menu">
            @foreach ($range as $item)
                <div class="dropdown-item" data-value="{{ $item['range'] }}">{{ $item['range'] }}</div>
            @endforeach
        </div>
    </div>
    <div class="filter name">
        <input type="text" class="form-control form-control-sm" placeholder="Episode number">
    </div>
</div>

<div class="episodes">
    @foreach ($range as $item)
        <ul class="ep-range" data-range="{{ $item['range'] }}" style="display: none;">
            @foreach ($item['episodes'] as $episode)
                @php
                    $epJson = json_encode([
                        'id' => $episode->id,
                        'name' => $episode->name,
                    ]);
                @endphp

                <li data-sub="{{ $episode->subbed }}" data-dub="{{ $episode->dubbed }}"
                    data-episode-id="{{ $episode->id }}" data-episode='{{ $epJson }}'>
                    <a href="{{ $episode->url($anime) }}" class="ep-link">
                        {{ $episode->name }}
                    </a>
                </li>
            @endforeach
        </ul>
    @endforeach

</div>
