<ul class="itemlist">
    @foreach ($animes as $anime)
        <li>
            <div class="inner ">
                <a class="poster tooltipstered" href="{{ get_anime_url($anime) }}" data-tip="{{ $anime->id }}">
                    <img src="https://static.bunnycdn.ru/i/cache/images/e/e3/e33e1884bfb546ce1752b7442c6e175a.jpg">
                    <div class="type">MUSIC</div>
                    <div class="meta">
                        <div class="ep-status-wrap">
                            <span class="ep-status sub"><span></span></span>
                            <span class="ep-status total"><span>1</span></span>
                        </div>
                    </div>
                </a>
                <div class="info">
                    <div class="name">
                        <h3>
                            <a href="{{ get_anime_url($anime) }}" class="d-title"
                                data-jp="{{ $anime->name }}">{{ $anime->name }}</a>
                        </h3>
                    </div>
                    <div class="meta">
                        <span class="dot">MUSIC</span>
                        <span class="dot">
                            <span class="ep-status-wrap">
                                <span class="ep-status sub">
                                    <span></span>
                                </span>
                                <span class="ep-status total">
                                    <span>1</span>
                                </span>
                            </span>
                        </span>
                    </div>
                </div>
            </div>
        </li>
    @endforeach
</ul>
