@foreach ($animes as $anime)
    <li>
        <div class="inner ">
            <a class="poster tooltipstered" href="{{ $anime->url() }}" data-tip="{{ $anime->id }}">
                <img src="{{ $anime->cover() }}">
                <div class="type">{{ $anime->type() }}</div>
                <div class="meta">
                    <div class="ep-status-wrap">
                        <span class="ep-status sub"><span>{{ $anime->getMeta('ep-status-sub') }}</span></span>
                        <span class="ep-status total"><span>{{ $anime->getMeta('ep-status-total') }}</span></span>
                    </div>
                </div>
            </a>
            <div class="info">
                <div class="name">
                    <h3>
                        <a href="{{ $anime->url() }}" class="d-title"
                            data-jp="{{ $anime->name }}">{{ $anime->name }}</a>
                    </h3>
                </div>
                <div class="meta">
                    <span class="dot">{{ $anime->type() }}</span>
                    <span class="dot">
                        <span class="ep-status-wrap">
                            <span class="ep-status sub">
                                <span>{{ $anime->getMeta('ep-status-sub') }}</span>
                            </span>
                            <span class="ep-status total">
                                <span>{{ $anime->getMeta('ep-status-total') }}</span>
                            </span>
                        </span>
                    </span>
                </div>
            </div>
        </div>
    </li>
@endforeach
