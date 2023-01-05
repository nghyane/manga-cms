let watchConfig = {
    'autoPlay': false,
    'autoNext': true,
    'autoSkip': false,
}

// Event callback when click change episode

const Anime = {
    init: function () {
        // if has .watchpage
        Anime.initAnime();

        if ($('.watchpage').length) {
            Anime.initWatchPage();
        }

        $(document).ready(function () {
            //tooltip fade bs-tooltip-top show
            $('.tip').hover(function () {
                $(this).tooltip('show');
            });

            $(".gotop").click(function () {
                $("html, body").animate({ scrollTop: 0 }, "slow");
                return false;
            });

            $(".toggler").on('click', function () {
                // toggle text content
                $(this).parents('.shorting').toggleClass('expand');
                // [more] to [less] or [less] to [more] content
                $(this).html($(this).html() == '[more]' ? '[less]' : '[more]');
            });

            $(".switch").on('click', function () {
                // data-switch="title_lang"
                $(this).find('.active').removeClass('active');


                let $switch = $(this).data('switch');
                let $switchElm = $(`[data-switch="${$switch}"]`);

                $switchElm.html($switchElm.data($lang));
            });
        });

    },

    initAnime: function () {


    },

    initWatchPage: function () {
        let anime = $('.watchpage').data('anime');
        let episode = $('.watchpage').data('episode')

        // get episodes
        const getEpisodes = function (anime_id, callback) {
            $.ajax({
                url: `/api/anime/${anime_id}/episodes`,
                async: false,
                success: callback || function (data) {
                    console.log(data);
                }
            });
        }

        const displayRange = function (range) {
            $('.ep-range').hide();

            $(".filter.range button").html(range);
            $(`.ep-range[data-range="${range}"]`).show();
        }

        const updateWatchConfig = function (config, $element) {
            watchConfig[config] = !watchConfig[config];
            turnOrOff(watchConfig[config], $element);
            localStorage.setItem('watchConfig', JSON.stringify(watchConfig));
        }

        const turnOrOff = function (config, $element) {
            if (config) {
                $element.html(
                    $element.data('on')
                );
            } else {
                $element.html(
                    $element.data('off')
                );
            }
        }

        // get watchConfig from localstorage
        if (typeof (localStorage) !== 'undefined') {
            if (localStorage.getItem('watchConfig') !== null) {
                watchConfig = localStorage.getItem('watchConfig');
                watchConfig = JSON.parse(watchConfig);
            }
        }

        // check autoPlay
        let $controls = $(".controls");
        let $playerWrapper = $(".player-wrapper");
        let $autoPlay = $controls.find('.auto-play');
        let $autoNext = $controls.find('.auto-next');
        let $overlay = $controls.find('.light');

        let $overlayElm = $('<div style="width: 100%; height: 100%; position: fixed; left: 0px; top: 0px; z-index: 22; background: rgb(0, 0, 0); opacity: 0.95; display: none;"></div>');


        $('body').append($overlayElm);


        $overlay.on('click', function () {
            // add z-index to
            $playerWrapper.css('z-index', 23);
            $overlayElm.fadeToggle();
        });

        $overlayElm.on('click', function () {
            $overlayElm.fadeToggle();
        });

        if (watchConfig.autoPlay) {
            $(".play").click();
        }

        const initToggle = {
            autoPlay: function () {
                turnOrOff(watchConfig.autoPlay, $autoPlay);
            },
            autoNext: function () {
                turnOrOff(watchConfig.autoNext, $autoNext);
            },
            init: function () {
                this.autoPlay();
                this.autoNext();
            }
        }

        initToggle.init();

        $autoPlay.on('click', function () {
            updateWatchConfig('autoPlay', $autoPlay);
        });

        $autoNext.on('click', function () {
            updateWatchConfig('autoNext', $autoNext);
        });

        // get episodes CHECK IF ANIME NOT NULL
        if (typeof (anime) !== 'undefined' && anime !== null) {
            // getEpisodes(anime.id, function (res) {
            //     if (res.status == 'success') {
            //         $("#episodes").html(res.data);


            //     }
            // });

            if ($('[data-dub="1"]').length < 1 || $('[data-sub="1"]').length < 1) {
                $(".filter.type").hide();
            }

            let currentEp = episode ? $(`[data-episode-id="${episode.id}"] .ep-link`) : $("#episodes").find('.ep-link').first();
            currentEp.click();
            currentEp.addClass('active');

            let currentRange = $(currentEp).parents('.ep-range').data('range');

            displayRange(currentRange);
        }


        // add event change range
        $(document).on('click', '.filter.range .dropdown-item', function () {
            let range = $(this).data('value');

            console.log(range);

            displayRange(range);
        });


    },

};


class SPA {
    constructor(root) {
        this.root = root;
        this.init();
    }

    goTo(url) {
        // send ajax request to url
        $.ajax({
            url: url,
            success: function (res) {
                if (res.status == 'success') {
                    // push state
                    this.pushState(url);
                    // replace content
                    $(this.root).html(res.data);
                    Anime.init();
                }
            }
        });
    }

    pushState(url) {
        history.pushState({}, '', url);
    }

    init() {
        // listen state change
        window.addEventListener('popstate', function (e) {
            e.preventDefault();
            // go to current url
            this.goTo(window.location.href);
        });
    }
}



Anime.init();

