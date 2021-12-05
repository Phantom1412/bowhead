<?php

namespace Bowhead\Http\Controllers;

use Bowhead\Models\BhExchangePairs;
use Bowhead\Models\BhExchanges;
use Bowhead\Traits;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function create(Request $request)
    {
        $exchanges = explode(',', Traits\Config::bowhead_config('EXCHANGES'));
        $pairs = explode(',', Traits\Config::bowhead_config('PAIRS'));

        $bhExchanges = BhExchanges::with('pairs')->whereIn('id', $exchanges)->first();
        // if ($exname == 'Global Digital Asset Exchange') {
        //     $exname = 'GDAX';
        // }

        return view('private.setting', compact('exchanges', 'pairs', 'bhExchanges'));
    }

    public function store(Request $request)
    {
        
    }
}
