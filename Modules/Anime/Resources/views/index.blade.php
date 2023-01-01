@extends('anime::layouts.master')

@section('content')
    @include('anime::partials.hsearch')

    <section class="text-center">
        <a class="btn btn-lg btn-outline-primary" href="home">{{ __('go_to_homepage') }}</a>
    </section>

    <section style="max-width: 900px; margin:0 auto; ">
        <div class="heading sline">
            <h1 class="title mb-2">Watch anime online</h1>
            <div class="content"> AnimeSuge is a free streaming anime website that allows you to <strong>watch anime online
                    in English subbed and dubbed</strong>. Join us and <strong>watch anime online for free</strong> with
                ease. Easy access and no registration is required. Our content is updated daily with fast streaming servers
                and great features that help you easily track and watch your favorite anime. We are confident AnimeSuge is
                the <strong>best free anime streaming site</strong> in the space that you can't simply miss!<div
                    class="text-center mt-2">
                    <div>Please help us by sharing this site with your friends. Thanks!</div>
                    <div class="addthis_inline_share_toolbox" data-url="{{ url('/') }}"></div>
                </div>
            </div>
        </div>
    </section>




@endsection
