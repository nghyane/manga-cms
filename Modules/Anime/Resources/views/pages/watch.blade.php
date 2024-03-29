@if (!request()->ajax())
    @extends('anime::layouts.master')
@endif

@php
    $anime_public = [
        'id' => $anime->id,
        'slug' => $anime->slug,
        'url' => $anime->url(),
    ];

    $episode_public = isset($episode)
        ? [
            'id' => $episode->id,
            'slug' => $episode->slug,
            'url' => $episode->url($anime),
            'name' => $episode->name,
        ]
        : null;
@endphp

@section('content')
    <div class="watchpage" data-anime='@json($anime_public)' data-episode='@json($episode_public)'>
        <div class="main">
            <div class="inner">
                @if (!empty($episode))
                    <section class="my-0">
                        <div class="heading sline">
                            <h1 class="title d-title" data-jp="{{ $metas->get('alternative') }}">{{ $anime->name }}</h1>
                        </div>

                        @if (config('anime.wacth_notif'))
                            <div class="alert alert-warning">
                                {{ config('anime.wacth_notif') }}
                            </div>
                        @endif

                        <div class="content">
                            <div class="player-wrapper">
                                <div id="player">
                                    <div class="backdrop" style="background-image: url('{{ $episode->thumbnail }}')">
                                    </div>
                                    <div class="play"></div>
                                </div>
                            </div>
                            <div class="controls">
                                <div class="ctrl onoff tip auto-play" data-default="1" data-name="auto_play"
                                    data-off='<i class="fas fa-square" style="font-weight: 400"></i></i> Auto Play'
                                    data-on='<i class="fas fa-check-square" style="font-weight: 400"></i> Auto Play'
                                    data-persist="true" title="Toggle auto play"></div>
                                <div class="ctrl onoff tip auto-next" data-default="1" data-name="auto_next"
                                    data-off='<i class="fas fa-square" style="font-weight: 400"></i> Auto Next'
                                    data-on='<i class="fas fa-check-square" style="font-weight: 400"></i> Auto Next'
                                    data-persist="true" title="Toggle auto next"></div>
                                <div class="ctrl onoff tip auto-skip text-warning" data-default="0"
                                    data-name="auto_skip_intro"
                                    data-off='<i class="fas fa-square" style="font-weight: 400"></i> Auto Skip'
                                    data-on='<i class="fas fa-check-square" style="font-weight: 400"></i> Auto Skip'
                                    data-persist="true" title="Toggle auto skip intro"></div>
                                <div class="ctrl light tip" data-default="1" title="Toggle light">
                                    <i class="fa fa-adjust"></i> Light
                                </div>
                                <div class="ctrl dropdown bookmark" data-fetch="true" data-id="3835">
                                    <div data-add='<i class="fas fa-bookmark"></i> Add to list'
                                        data-edit='<i class="fas fa-pen"></i> Edit watch list' data-placeholder="false"
                                        data-toggle="dropdown"></div>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <div class="folders"></div>
                                    </div>
                                </div>
                                <div class="ctrl onoff shortcuts tip" data-target="#shortcuts" data-toggle="collapse"
                                    title="Player shortcuts">
                                    <i class="fa fa-keyboard"></i> {{ __('shortcuts') }}
                                </div>
                                <div class="ctrl share" data-target="#md-share" data-toggle="modal">
                                    <i class="fa fa-share"></i> {{ __('share') }}
                                </div>
                            </div>
                        </div>
                    </section>

                    @include('anime::partials.shortcuts')

                    <div id="servers">
                        @include('anime::partials.servers')
                    </div>

                    <section id="episodes">
                        @include('anime::partials.episodes')
                    </section>
                @endif

                <div class="addthis_inline_share_toolbox text-center"></div>
                <section class="collapse" id="info">
                    <div class="poster">
                        <div><img src="{{ $anime->cover() }}">
                        </div>
                    </div>
                    <div class="info">
                        <h2 class="title d-title" data-jp="{{ $metas->get('alternative', $anime->name) }}">
                            {{ $anime->name }}</h2>
                        <div class="alias">
                            @if ($metas->get('alternative'))
                                {{ $metas->get('alternative') }};
                            @endif {{ $anime->name }}
                        </div>
                        <div class="desc shorting">
                            <div class="content">
                                {{ $anime->description }}
                            </div>
                            <div class="toggler">[more]</div>
                        </div>
                        <div class="meta">
                            <div class="col1">
                                <div>
                                    {{ __('type') }}: <span>{{ $anime->type() }}</span>
                                </div>
                                <div>
                                    {{ __('studio') }}: <span>
                                        @foreach ($studios as $studio)
                                            <a href="/studio/{{ $studio->slug }}">{{ $studio->name }}</a>
                                            @if (!$loop->last)
                                                ,
                                            @endif
                                        @endforeach
                                    </span>
                                </div>
                                <div>
                                    {{ __('date_aired') }}: <span>{{ $metas->get('date_aired', __('unknown')) }}</span>
                                </div>
                                @if ($metas->get('broadcast'))
                                    <div>

                                        {{ __('broadcast') }}: <span>{{ $metas->get('broadcast') }}</span>
                                    </div>
                                @endif

                                <div>
                                    {{ __('status') }}: <span>{{ $anime->status() }}</span>
                                </div>
                            </div>
                            <div class="col2">
                                <div>
                                    {{ __('genre') }}: <span>
                                        {{-- Add , each genners --}}
                                        @foreach ($anime->genres as $genre)
                                            <a href="/genre/{{ $genre->slug }}">{{ $genre->name }}</a>
                                            @if (!$loop->last)
                                                ,
                                            @endif
                                        @endforeach
                                    </span>
                                </div>
                                <div>
                                    {{ __('country') }}: <span>
                                        <a href="/country/japan">{{ $metas->get('country') }}</a>
                                    </span>
                                </div>
                                <div>
                                    {{ __('premiered') }}: <span><a href="#">{{ $metas->get('season') }}
                                            {{ $metas->get('year', 'unknown') }}</a></span>
                                </div>
                                <div>
                                    {{ _('duration') }}: <span>{{ $metas->get('duration', 'N/a') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section data-id="xxq3" data-link="https://animesuge.to/anime/someones-gaze-xxq3" data-load="true"
                    data-src="//animesuge.disqus.com/embed.js" id="comments">
                    <div class="heading">
                        <div class="title">
                            {{ __('comments') }}
                        </div>
                        <div class="links tabs">
                            <span class="tab active" data-name="anime">Anime</span> <span class="tab"
                                data-name="episode">Episode <span class="current-episode-name"></span> <span
                                    class="current-episode-type"></span></span>
                        </div>
                        <div class="content mt-4">
                            <div id="disqus_thread"></div>
                            <div class="text-center">
                                <div class="btn btn-secondary load-comments">
                                    open discussion
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
        <div class="sidebar">
            <section>
                <div class="head">
                    <div class="heading">
                        <div class="title">
                            Trending
                        </div>
                    </div>
                </div>
                <div class="body">
                    <ul class="itemlist-sm">
                        <li>
                            <a class="poster" data-tip="177?/cachebc6a" href="/anime/one-piece-ov8">
                                <div><img class="lazyload"
                                        data-src="https://static.bunnycdn.ru/i/cache/images/4/42/422670a855efae8d8d8fafb59d43c197.jpg-w100">
                                </div>
                            </a>
                            <div class="info">
                                <a class="d-title" data-jp="ONE PIECE" href="/anime/one-piece-ov8">ONE PIECE</a>
                                <div>
                                    <span class="dot">TV</span> <span class="dot">? Eps</span> <span
                                        class="dot"><i class="fas fa-bookmark"></i> 257,518</span>
                                </div>
                            </div>
                        </li>
                        <li>
                            <a class="poster" data-tip="13094?/cache4168" href="/anime/chainsaw-man-8o9q">
                                <div><img class="lazyload"
                                        data-src="https://static.bunnycdn.ru/i/cache/images/9/9e/9e12905bf8156dba240756891e906f68.jpg-w100">
                                </div>
                            </a>
                            <div class="info">
                                <a class="d-title" data-jp="Chainsaw Man" href="/anime/chainsaw-man-8o9q">Chainsaw
                                    Man</a>
                                <div>
                                    <span class="dot">TV</span> <span class="dot">12 Eps</span> <span
                                        class="dot"><i class="fas fa-bookmark"></i> 155,639</span>
                                </div>
                            </div>
                        </li>
                        <li>
                            <a class="poster" data-tip="13518?/cache00cc" href="/anime/the-eminence-in-shadow-4ylx">
                                <div><img class="lazyload"
                                        data-src="https://static.bunnycdn.ru/i/cache/images/b/b4/b4b8f02a8ca80bac7f7806cde7703e12.jpg-w100">
                                </div>
                            </a>
                            <div class="info">
                                <a class="d-title" data-jp="Kage no Jitsuryokusha ni Naritakute!"
                                    href="/anime/the-eminence-in-shadow-4ylx">The Eminence in Shadow</a>
                                <div>
                                    <span class="dot">TV</span> <span class="dot">20 Eps</span> <span
                                        class="dot"><i class="fas fa-bookmark"></i> 103,586</span>
                                </div>
                            </div>
                        </li>
                        <li>
                            <a class="poster" data-tip="14264?/cache6b07" href="/anime/blue-lock-2o2mq">
                                <div><img class="lazyload"
                                        data-src="https://static.bunnycdn.ru/i/cache/images/8/84/840c96762c4e47405ef7ae67151ccd49.jpg-w100">
                                </div>
                            </a>
                            <div class="info">
                                <a class="d-title" data-jp="Blue Lock" href="/anime/blue-lock-2o2mq">BLUELOCK</a>
                                <div>
                                    <span class="dot">TV</span> <span class="dot">24 Eps</span> <span
                                        class="dot"><i class="fas fa-bookmark"></i> 72,181</span>
                                </div>
                            </div>
                        </li>
                        <li>
                            <a class="poster" data-tip="14483?/cache20bf" href="/anime/boku-no-hero-academia-6-xrrvz">
                                <div><img class="lazyload"
                                        data-src="https://static.bunnycdn.ru/i/cache/images/3/3b/3ba8e07776041325e2a67856465546e5.jpg-w100">
                                </div>
                            </a>
                            <div class="info">
                                <a class="d-title" data-jp="Boku no Hero Academia 6"
                                    href="/anime/boku-no-hero-academia-6-xrrvz">My Hero Academia Season 6</a>
                                <div>
                                    <span class="dot">TV</span> <span class="dot">25 Eps</span> <span
                                        class="dot"><i class="fas fa-bookmark"></i> 76,919</span>
                                </div>
                            </div>
                        </li>
                        <li>
                            <a class="poster" data-tip="14134?/cachea642" href="/anime/bleach-sennen-kessen-hen-w190l">
                                <div><img class="lazyload"
                                        data-src="https://static.bunnycdn.ru/i/cache/images/8/80/8061c2dc9b36972bac492974e5c79308.jpg-w100">
                                </div>
                            </a>
                            <div class="info">
                                <a class="d-title" data-jp="BLEACH: Sennen Kessen-hen"
                                    href="/anime/bleach-sennen-kessen-hen-w190l">BLEACH: Thousand-Year Blood War</a>
                                <div>
                                    <span class="dot">TV</span> <span class="dot">13 Eps</span> <span
                                        class="dot"><i class="fas fa-bookmark"></i> 62,781</span>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </section>

            <section>
                <div class="head">
                    <div class="heading">
                        <div class="title">
                            Recommended
                        </div>
                    </div>
                </div>
                <div class="body">
                    <ul class="itemlist-sm">
                        <li>
                            <a class="poster" data-tip="148?/cache1a6f"
                                href="/anime/she-and-her-cat-everything-flows-jl2">
                                <div><img class="lazyload"
                                        data-src="https://static.bunnycdn.ru/i/cache/images/2018/04/8780dc74dd74a9de275bd3283713f8b4.jpg-w100">
                                </div>
                            </a>
                            <div class="info">
                                <a class="d-title" data-jp="Kanojo to Kanojo no Neko: Everything Flows"
                                    href="/anime/she-and-her-cat-everything-flows-jl2">She and Her Cat -Everything
                                    Flows-</a>
                                <div>
                                    <span class="dot">TV</span> <span class="dot">4 Eps</span> <span
                                        class="dot"><i class="fas fa-bookmark"></i> 5,994</span>
                                </div>
                            </div>
                        </li>
                        <li>
                            <a class="poster" data-tip="4903?/cachefa1f" href="/anime/shelter-mylz">
                                <div><img class="lazyload"
                                        data-src="https://static.bunnycdn.ru/i/cache/images/2020/05/937064768936fd27d14d53ae03332f95.jpg-w100">
                                </div>
                            </a>
                            <div class="info">
                                <a class="d-title" data-jp="Shelter" href="/anime/shelter-mylz">Shelter</a>
                                <div>
                                    <span class="dot">TV</span> <span class="dot">1 Eps</span> <span
                                        class="dot"><i class="fas fa-bookmark"></i> 7,406</span>
                                </div>
                            </div>
                        </li>
                        <li>
                            <a class="poster" data-tip="6782?/cache9c56"
                                href="/anime/she-and-her-cat-their-standing-points-wz74">
                                <div><img class="lazyload"
                                        data-src="https://static.bunnycdn.ru/i/cache/images/2018/04/cc277553009eae533044ddd95657ad79.jpg-w100">
                                </div>
                            </a>
                            <div class="info">
                                <a class="d-title" data-jp="Kanojo to Kanojo no Neko"
                                    href="/anime/she-and-her-cat-their-standing-points-wz74">She and Her Cat: Their
                                    Standing Points</a>
                                <div>
                                    <span class="dot">OVA</span> <span class="dot">1 Eps</span> <span
                                        class="dot"><i class="fas fa-bookmark"></i> 2,422</span>
                                </div>
                            </div>
                        </li>
                        <li>
                            <a class="poster" data-tip="5629?/cachea9e6" href="/anime/otona-joshi-no-anime-time-o8w5">
                                <div><img class="lazyload"
                                        data-src="https://static.bunnycdn.ru/i/cache/images/2018/04/2c09584d063414c1db569f5ba3909005.jpg-w100">
                                </div>
                            </a>
                            <div class="info">
                                <a class="d-title" data-jp="Otona Joshi no Anime Time"
                                    href="/anime/otona-joshi-no-anime-time-o8w5">Otona Joshi no Anime Time</a>
                                <div>
                                    <span class="dot">SPECIAL</span> <span class="dot">4 Eps</span> <span
                                        class="dot"><i class="fas fa-bookmark"></i> 596</span>
                                </div>
                            </div>
                        </li>
                        <li>
                            <a class="poster" data-tip="6824?/cache3667" href="/anime/cross-road-j043">
                                <div><img class="lazyload"
                                        data-src="https://static.bunnycdn.ru/i/cache/images/2020/08/b48b95076acacbdfc427830f6cb1e7cc.jpg-w100">
                                </div>
                            </a>
                            <div class="info">
                                <a class="d-title" data-jp="Cross Road" href="/anime/cross-road-j043">Cross Road</a>
                                <div>
                                    <span class="dot">SPECIAL</span> <span class="dot">1 Eps</span> <span
                                        class="dot"><i class="fas fa-bookmark"></i> 3,431</span>
                                </div>
                            </div>
                        </li>
                        <li>
                            <a class="poster" data-tip="1433?/cachebd2b" href="/anime/5-centimeters-per-second-v50l">
                                <div><img class="lazyload"
                                        data-src="https://static.bunnycdn.ru/i/cache/images/2018/04/79317c885bbe9c467e0ecb271d80a51d.jpg-w100">
                                </div>
                            </a>
                            <div class="info">
                                <a class="d-title" data-jp="Byousoku 5 Centimeter"
                                    href="/anime/5-centimeters-per-second-v50l">5 Centimeters per Second</a>
                                <div>
                                    <span class="dot">MOVIE</span> <span class="dot">3 Eps</span> <span
                                        class="dot"><i class="fas fa-bookmark"></i> 38,083</span>
                                </div>
                            </div>
                        </li>
                        <li>
                            <a class="poster" data-tip="12102?/cacheed6e" href="/anime/egao-lwpn">
                                <div><img class="lazyload"
                                        data-src="https://static.bunnycdn.ru/i/cache/images/2020/01/b29c16d8eab781435c4c9f334d4e57aa.jpg-w100">
                                </div>
                            </a>
                            <div class="info">
                                <a class="d-title" data-jp="Egao" href="/anime/egao-lwpn">Egao</a>
                                <div>
                                    <span class="dot">ONA</span> <span class="dot">1 Eps</span> <span
                                        class="dot"><i class="fas fa-bookmark"></i> 207</span>
                                </div>
                            </div>
                        </li>
                        <li>
                            <a class="poster" data-tip="13726?/cache51c1" href="/anime/night-world-lvoz">
                                <div><img class="lazyload"
                                        data-src="https://static.bunnycdn.ru/i/cache/images/a/a5/a5f0cf3e6fa4f0eff3826afee60e4124.jpg-w100">
                                </div>
                            </a>
                            <div class="info">
                                <a class="d-title" data-jp="Yoru no Kuni" href="/anime/night-world-lvoz">night world</a>
                                <div>
                                    <span class="dot">ONA</span> <span class="dot">3 Eps</span> <span
                                        class="dot"><i class="fas fa-bookmark"></i> 1,189</span>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </section>
        </div>
    </div>
@endsection
