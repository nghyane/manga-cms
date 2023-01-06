<div class="server-tip">
    <div>You're watching <b class="current-episode">Episode {{ $episode->name }}</b>.</div>
    <div>If current servers doesn't work, please try other servers beside.</div>
</div>
<div class="servers">
    <div class="type" data-type="sub">
        <label class="label">
            <i class="fas fa-closed-captioning"></i> Sub</label>
        <div class="list">
            @foreach ($episode->video as $server)
                <a data-url="{{ stringCipher($server->url, 5) }}">
                    {{ $server->server ?? 'Unknown' }} </a>
            @endforeach
        </div>
    </div>
</div>
