<header>
    <div class="container">
        <div class="wrapper">
            <div id="menu-toggler">
                <i class="fa fa-bars"></i>
            </div><a href="/home" id="logo" title="Watch Anime Online Free">
                <h2>Watch Anime Online Free</h2>
            </a>
            <ul id="menu">
                <li>
                    <a>Genre</a>
                    <ul class="genre">
                        @php
                            // cache the genres for 60 minutes
                            $genres = Cache::remember('genres', 60, function () {
                                return \Modules\Anime\Entities\Genres::all();
                            });
                        @endphp

                        @foreach ($genres as $genre)
                            <li>
                                <a href="/genre/{{ $genre->slug }}" title="{{ $genre->name }} Anime">{{ $genre->name }}</a>
                            </li>
                        @endforeach
                    </ul>
                </li>
                <li>
                    <a>Types</a>
                    <ul>
                        <li>
                            <a href="/tv">TV Series</a>
                        </li>
                        <li>
                            <a href="/movie">Movies</a>
                        </li>
                        <li>
                            <a href="/ova">OVAs</a>
                        </li>
                        <li>
                            <a href="/ona">ONAs</a>
                        </li>
                        <li>
                            <a href="/special">Specials</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="/newest" title="Newest Anime">Newest</a>
                </li>
                <li>
                    <a href="/updated" title="Recently Updated Anime">Updated</a>
                </li>
                <li>
                    <a href="/ongoing" title="Ongoing Anime">Ongoing</a>
                </li>
                <li>
                    <a href="/added" title="Recently Added Anime">Added</a>
                </li>
                <li>
                    <a class="tip" href="/random" title="Watch random anime"><i class="fas fa-random"></i></a>
                </li>
            </ul>
            <div id="search-toggler">
                <i class="fa fa-search"></i>
            </div>
            <div id="search">
                <form class="search autocomplete" action="filter"> <input type="text" name="keyword"
                        placeholder="Enter anime name..." autocomplete="off"> <button type="button"><i
                            class="fa fa-search"></i></button>
                    <div class="suggestions"></div> <input type="hidden" name="vrf">
                </form>
            </div>
            <div id="user">
                <div class="guest" data-toggle="modal" data-target="#md-sign">
                    <i class="fa fa-user-circle"></i>
                </div>
            </div>
        </div>
    </div>
</header>

