@extends('anime::layouts.master')

@section('content')

@include('anime::partials.hsearch')

    <section>
        <div class="heading sline">
            <h1 class="title">Watch anime online</h1>
            <div class="content">
                <div class="shorting">
                    <div class="content"> AnimeSuge is a free streaming anime website that allows you to <strong>watch anime
                            online in English subbed and dubbed</strong>. Join us and <strong>watch anime online for
                            free</strong> with ease. Easy access and no registration is required. Our content is updated
                        daily with fast streaming servers and great features that help you easily track and watch your
                        favorite anime. We are confident AnimeSuge is the <strong>best free anime streaming site</strong> in
                        the space that you can't simply miss! </div>
                    <div class="toggler">[more]</div>
                </div>
                <div>Please help us by sharing this site with your friends. Thanks!</div>
                <div class="addthis_inline_share_toolbox" data-url="https://animesuge.to"></div>
            </div>
        </div>
    </section>

    @include('anime::partials.recent-update')

@stop
