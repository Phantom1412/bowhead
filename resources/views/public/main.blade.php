@extends('base')

@section('content')
<div class="c w-100">
    <p class="err">{{ $notice }}</p>
    <div class="row card">
        <div class="7 col">B O W H E A D</div>
        <div class="1 col"><a href="/exchanges">api keys</a></div>
        <div class="1 col"><a href="#">arbitrage</a></div>
        <div class="1 col"><a href="#">builder</a></div>
        <div class="1 col"><a href="#">docs</a></div>
        <div class="1 col"><a href="/settings">settings</a></div>
    </div>
    <div class="row">
        <div class="2 col" style="vertical-align:top;">
        <div class="scrolly">
            @foreach ($exchanges as $ekey => $exchange)
                @if ($e == $ekey)
                    <a href="?enc={{encrypt(['p'=>$p, 'e' => $ekey])}}" style="background: #EEE;"><strong>{{ $exchange }}</strong></a><br>
                @else
                    <a href="?enc={{encrypt(['p'=>$p, 'e' => $ekey])}}">{{ $exchange }}</a><br>
                @endif
            @endforeach
        </div>
        </div>
        <div class="8 col">
            <div>
            <img src="http://via.placeholder.com/900x350">
            </div>
        </div>
        <div class="2 col" style="vertical-align: top;">
            <div class="scrolly">
            @foreach ($pairs as $pair)
                    @if ($p == $pair)
                        <a href="?enc={{encrypt(['p'=>$pair, 'e' => $e])}}" style="background: #EEE;"><strong>{{ $pair }}</strong></a><br>
                    @else
                        <a href="?enc={{encrypt(['p'=>$pair, 'e' => $e])}}">{{ $pair }}</a><br>
                    @endif
            @endforeach
            </div>
        </div>
    </div>

    <div class="row">
        <div class="6 col card">
        </div>
        <div class="6 col card">
        </div>
    </div>
</div>
@stop
