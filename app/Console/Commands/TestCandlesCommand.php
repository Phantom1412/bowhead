<?php

namespace Bowhead\Console\Commands;

use Bowhead\Traits\CandleMap;
use Bowhead\Traits\Pivots;
use Bowhead\Traits\OHLC;
use Bowhead\Util\Candles;
use Bowhead\Util\Console;
use Bowhead\Util\Indicators;
use Illuminate\Console\Command;

class TestCandlesCommand extends Command
{
    use OHLC, Pivots, CandleMap;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bowhead:test_candles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the candles.';

    /**
     * @var
     */
    protected $candles;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->candles = new Candles();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $console = new Console();
//        $ind = new Indicators();
//        $data = $this->getRecentData('AUD_USD',150);
//        $instruments = ['USD_JPY','NZD_USD','EUR_GBP','USD_CAD','USD_CNH','USD_MXN','USD_TRY','AUD_USD','EUR_USD','USD_CHF'];
	    $instruments = ['BTC/USD','ETH/BTC','LTC/BTC'];

        while(1) {
            $all = [];
            foreach($instruments as $instrument) {
                $data = $this->getRecentData($instrument,200);
                $cand = $this->candles->allCandles($instrument, $data);
                $candles[$instrument] = $cand['current'] ?? [];
                $all = array_merge($all, $cand['current'] ?? []);
            } // foreach
            foreach($instruments as $instrument) {
                foreach ($all as $allof => $val) {
                    $candles[$instrument][$allof] = $candles[$instrument][$allof] ?? 0;
                } // foreach
            } // foreach
            #print_r($all);

            $lines = [];
            $lines['top'] = '';
            $output = '';
            foreach ($instruments as $instrument) {
                $lines['top'] .= str_pad($instrument, 17);
                foreach ($all as $candle => $val) {
                    if (!isset($lines[$candle])) {
                        $lines[$candle] = '';
                    } // if
                    $color = ($candles[$instrument][$candle] > 0 ? 'bg_green' : ($candles[$instrument][$candle] < 0 ? 'bg_red' : 'bg_black'));
                    $lines[$candle] .= $console->colorize(str_pad($candle, 17), $color);
                } // foreach
            } // foreach
            echo "\n\n" . $console->colorize(@$lines['top']);
            foreach ($all as $candle => $val) {
                echo "\n" . $lines[$candle];
            } // foreach
            echo "\n\n";
            sleep(5);
        } // while
    }
}
