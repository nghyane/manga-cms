let watchConfig = {
    'autoPlay': false,
    'autoNext': true,
    'autoSkip': false,
}

const stringCipher = function (string, shift) {
    string = window.atob(string);

    var encoded_string = "";
    for (var i = 0; i < string.length; i++) {
        var char = string[i];
        if (char.match(/[a-z]/i)) {
            var code = char.charCodeAt(0);
            if (char.match(/[A-Z]/)) {
                code += shift;
                if (code > 90) {
                    code = 65 + (code - 91);
                } else if (code < 65) {
                    code = 91 - (65 - code);
                }
            } else {
                code += shift;
                if (code > 122) {
                    code = 97 + (code - 123);
                } else if (code < 97) {
                    code = 123 - (97 - code);
                }
            }
            encoded_string += String.fromCharCode(code);
        } else {
            encoded_string += char;
        }
    }
    return encoded_string;
}

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
        let $autoSkip = $controls.find('.auto-skip');
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
            autoSkip: function () {
                turnOrOff(watchConfig.autoSkip, $autoSkip);
            },
            bookmark: function () {
                // bookmark
                let $bookmark = $controls.find('.bookmark [data-toggle="dropdown"]');

                $bookmark.html($bookmark.data('add'));

                $bookmark.on('click', function (e) {
                    e.preventDefault();
                });
            },

            init: function () {
                for (let key in this) {
                    if (key !== 'init') {
                        this[key]();
                    }
                }
            }
        }

        initToggle.init();

        $autoPlay.on('click', function () {
            updateWatchConfig('autoPlay', $autoPlay);
        });

        $autoNext.on('click', function () {
            updateWatchConfig('autoNext', $autoNext);
        });

        $autoSkip.on('click', function () {
            updateWatchConfig('autoSkip', $autoSkip);
        });

        // get episodes CHECK IF ANIME NOT NULL
        if (typeof (anime) !== 'undefined' && anime !== null) {
            if ($('[data-dub="1"]').length < 1 || $('[data-sub="1"]').length < 1) {
                $(".filter.type").hide();
            }

            let currentEp = episode ? $(`[data-episode-id="${episode.id}"] .ep-link`) : $("#episodes").find('.ep-link').first();
            currentEp.click();
            currentEp.addClass('active');

            episode = episode ? episode : currentEp.data('episode');

            let currentRange = $(currentEp).parents('.ep-range').data('range');

            displayRange(currentRange);
        }

        // filter input
        $(document).on('keyup', '.filter input', function () {
            let filter = $(this).val().toLowerCase();
            $('.ep-link').removeClass('highlight');

            $("[data-episode-num='" + filter + "']").find('.ep-link').addClass('highlight');
        });


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

