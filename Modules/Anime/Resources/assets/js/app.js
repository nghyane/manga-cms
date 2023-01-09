let watchConfig = {
    'autoPlay': false,
    'autoNext': true,
    'autoSkip': false,
}


const stringCipher = function (string, shift) {
    string = decodeURIComponent(window.atob(string));

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

const loadScript = (FILE_URL, async = true, type = "text/javascript") => {
    return new Promise((resolve, reject) => {
        try {
            const scriptEle = document.createElement("script");
            scriptEle.type = type;
            scriptEle.async = async;
            scriptEle.src = FILE_URL;

            scriptEle.addEventListener("load", (ev) => {
                resolve({ status: true });
            });

            scriptEle.addEventListener("error", (ev) => {
                reject({
                    status: false,
                    message: `Failed to load the script ＄{FILE_URL}`
                });
            });

            document.body.appendChild(scriptEle);
        } catch (error) {
            reject(error);
        }
    });
};

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

            $(document).on('click', ".gotop", function () {
                $("html, body").animate({ scrollTop: 0 }, "slow");
                return false;
            });

            $(document).on('click', "#menu-toggler", function () {
                // style hide or show
                $("#menu").slideToggle({
                    duration: 200,
                });
            })

        });

        $("#search").click(function () {
            $(this).toggleClass("show");

            // the click not inside the from close the form
            $(document).mouseup(function (e) {
                console.log(e.target);
                if (!$("form").is(e.target) || $("form").has(e.target).length === 0) {
                    $("#search").removeClass("show");
                }
            });
        });

    },

    initAnime: function () {
        let Section = {
            element: $('#recent-update'),
            name: $('.links .tab.active').data('name'),
            page: $('.links .tab.active').data('page') || 1,
        }


        const getAnime = function (name, page = 1) {
            $.ajax({
                url: `/api/home/${name}?page=${page}`,
                success: function (data) {
                    $(".itemlist").html(data.data);
                }
            });
        }

        $(Section.element).on('click', '.links .tab', function () {
            Section.name = $(this).data('name');
            Section.page = $(this).data('page') || 1;

            $('.links .tab').removeClass('active');

            $(this).addClass('active');

            getAnime(Section.name, Section.page);

            if (Section.page <= 1) {
                $('.paging .prev').addClass('disabled');
            }
        });

        $(Section.element).on('click', '.paging .next', function () {
            $('.paging .prev').removeClass('disabled');

            getAnime(Section.name, ++Section.page);
        });

        $(Section.element).on('click', '.paging .prev', function () {
            getAnime(Section.name, --Section.page);

            if (Section.page <= 1) {
                $('.paging .prev').addClass('disabled');
            }
        });

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

        $(document).ready(function () {

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

            $(document).on('click', ".toggler", function () {
                // toggle text content
                $(this).parents('.shorting').toggleClass('expand');
                // [more] to [less] or [less] to [more] content
                $(this).html($(this).html() == '[more]' ? '[less]' : '[more]');
            });


            $(document).on('click', ".switch", function () {
                // data-switch="title_lang"
                $(this).find('.active').removeClass('active');


                let $switch = $(this).data('switch');
                let $switchElm = $(`[data-switch="${$switch}"]`);

                $switchElm.html($switchElm.data($lang));
            });


            $(document).on('click', '.servers a', function (e) {
                e.preventDefault();

                let $this = $(this);
                let url = $this.data('url');
                let type = $this.data('type');

                $('.servers a').removeClass('active');
                $this.addClass('active');

                Anime.initPlayer(url, type);
            });

            $(document).on('click', '.play', function (e) {
                $('.servers a').first().trigger('click');
            });

            if (watchConfig.autoPlay) {
                $(".play").click();
            }
        });


    },

    initPlayer: async function (url, type = 'mp4') {
        url = stringCipher(url, 21);
        const playerEle = document.getElementById('player');
        var bg_img = $("backdrop").css("background-image");

        //clear Player
        playerEle.innerHTML = '';

        // if type embed
        if (type === 'embed') {
            let $iframe = $('<iframe src="' + url + '" frameborder="0" allowfullscreen></iframe>');
            $iframe.css({
                width: '100%',
                height: '100%',
                position: 'absolute',
                top: 0,
                left: 0,
            });

            $(playerEle).html($iframe);
            return;
        }

        const config = {
            "file": url,
            "image": bg_img,
            "width": "100%",
            "height": "100%",
            "autostart": true,
            "mute": true,
            "controls": true,
        };


        // check script IndigoPlayer
        if (typeof (jwplayer) == 'undefined') {
            // add script
            await loadScript('https://ssl.p.jwpcdn.com/player/v/8.1.3/jwplayer.js');

            jwplayer.key = "W7zSm81+mmIsg7F+fyHRKhF3ggLkTqtGMhvI92kbqf/ysE99";
        }

        var player = jwplayer(playerEle).setup(config);

        player.on('ready', function () {
            if (!watchConfig.autoPlay) {
                player.play();
            }
        });

        player.onComplete(function () {
            if (watchConfig.autoNext) {
                let $nextEp = $('.ep-link.active').parents('.ep-range').next().find('.ep-link').first();

                if ($nextEp.length > 0) {
                    $nextEp.click();
                }
            }
        });
    }

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

