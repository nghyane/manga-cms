<section id="recent-update">
    <div class="heading">
        <h2 class="title">{{ __('recently updated') }}</h2>
        <div class="links tabs">
            <span data-name="updated-all" class="tab active">All</span>
            <span data-name="updated-sub" class="tab">Sub</span>
            <span data-name="updated-dub" class="tab">Dub</span>
            <span data-name="updated-china" class="tab">Chinese</span>
            <span data-name="trending" class="tab">Trending</span>
            <span data-name="random" class="tab">Random</span>
        </div>

        <div class="paging">
            <span class="prev tip disabled" data-original-title="Page 1">
                <i class="fa fa-angle-left"></i>
            </span>
            <span class="next tip" data-original-title="Page 2">
                <i class="fa fa-angle-right"></i>
            </span>
        </div>
    </div>

    @php
        $animes = \Modules\Anime\Entities\Anime::orderBy('updated_at', 'desc')->limit(10)->get();
    @endphp

    <x-anime::itemlist :animes="$animes" />

</section>
