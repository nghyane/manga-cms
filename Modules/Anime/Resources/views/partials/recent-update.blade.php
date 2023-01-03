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
        $limit = 12;
        $offset = request()->cookie('home_page_num', 1) * $limit;

        $anime_update = \Modules\Anime\Entities\Anime::orderBy('updated_at', 'desc')
            ->with('meta')
            ->offset($offset)
            ->limit($limit)
            ->get();
    @endphp

    <x-anime::itemlist :animes="$anime_update" />
</section>
