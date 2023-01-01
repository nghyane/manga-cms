let watchConfig = {
    'autoPlay': false,
    'autoNext': true,
    'autoSkip': false,
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
        });

    },

    initAnime: function () {


    },

    initWatchPage: function () {


        const updateWatchConfig = function (config, $element) {
            watchConfig[config] = !watchConfig[config];
            turnOrOff(watchConfig[config], $element);
            localStorage.setItem('watchConfig', JSON.stringify(watchConfig));
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

        $overlay.on('click', function () {
            // add z-index to
            $playerWrapper. css('z-index', 23);
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

    },

};


Anime.init();

