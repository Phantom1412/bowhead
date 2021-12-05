<?php

namespace Bowhead\Http\Controllers;

use Bowhead\Models\BhExchangePairs;
use Bowhead\Models\BhExchanges;
use Bowhead\Traits;
use Illuminate\Http\Request;

class MainController extends Controller
{
    use Traits\DataCcxt;

    public function test(Request $request)
    {
        $this->init_exchange('binance');
        dd($this->exchange->symbols, 'lol');
        
        $orderbook = $exchange->fetch_order_book ($exchange->symbols[0]);
        $bid = count ($orderbook['bids']) ? $orderbook['bids'][0][0] : null;
        $ask = count ($orderbook['asks']) ? $orderbook['asks'][0][0] : null;
        $spread = ($bid && $ask) ? $ask - $bid : null;
        $result = array ('bid' => $bid, 'ask' => $ask, 'spread' => $spread);
        var_dump ($exchange->id, 'market price', $result);
        dd($this->exchange, 'lol');
        // ${'bh_'.$exchange}->createOrder('BTC/BUSD', 'market', 'sell', amount, ...)

        \Artisan::call('my:datarunner_ccxt');
        dd(\Artisan::output(), 'lol');

        // $exchanges = \ccxt\Exchange::$exchanges;
        // dd($exchanges);
    }

    public function main(Request $request)
    {
        $_input = $request->all();
        $input = (!empty($_input['enc']) ? decrypt($_input['enc']) : []);
        print_r($input);
        echo "<br>\n";
        $vars['e'] = $input['e'] ?? null;
        $vars['p'] = $input['p'] ?? null;

        $vars['notice'] = '';
        $exs = [];
        $exchanges = explode(',', Traits\Config::bowhead_config('EXCHANGES'));
        /*if (in_array(105, $exchanges)) {
            foreach($exchanges as $key => $val) {
                $exchanges[$key] = $val == 105 ? 52 : $val;
            }
        }*/
        $pairs = explode(',', Traits\Config::bowhead_config('PAIRS'));
        $vars['p'] = $vars['p'] ?? head($pairs);
        sort($pairs);

        /**
         *  Load up the pairs data by exchanges as we cannot try to graph data that does not exist.
         */
        $exchange_pairs = BhExchangePairs::whereIn('exchange_id', $exchanges)->get();
        foreach($exchange_pairs as $exchange_pair){
            $vars['exchange_pairs'][$exchange_pair->exchange_id][] = $exchange_pair->exchange_pair;
        }

        foreach ($exchanges as $ex_id) {
            $exch = BhExchanges::where('id','=', $ex_id)->first();
            $exname = $exch->exchange;
            $vars['e'] = $vars['e'] ?? $ex_id;
            if ($exname == 'Global Digital Asset Exchange') {
                $exname = 'GDAX';
            }
            $exs[$ex_id] = $exname;
        }
        asort($exs);
        $vars['exchanges'] = $exs;
        $vars['pairs'] = [];
        $vars_pairs = $vars['exchange_pairs'][$vars['e']] ?? [];
        sort($vars_pairs);
        foreach($vars_pairs as $pair) {
            if (in_array($pair, $pairs)) {
                $vars['pairs'][] = $pair;
            }
        }
        print_r($pairs);



        $vars['data'] = '';

        return view('main', $vars);
    }
}
