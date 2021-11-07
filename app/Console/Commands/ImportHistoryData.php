<?php

namespace Bowhead\Console\Commands;

use function GuzzleHttp\Psr7\str;
use Illuminate\Console\Command;
use Bowhead\Util\Console;
use Bowhead\Traits\OHLC;

class ImportHistoryData extends Command {

	use OHLC;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bowhead:import_history_data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports history data from Bitfinex';

	/**
	 * @var currency pairs
	 */
	protected $instruments = array('BTC-USD','ETH-BTC','LTC-BTC');

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {

	    $console = new Console();

	    echo $console->colorize("------------------------------------------------------------------\n");
	    echo $console->colorize("This script by default loads 7 day data from Bitfinex\n");
	    echo $console->colorize("There are 3 pairs : BTC-USD, ETH-BTC, LTC-BTC\n");
	    echo $console->colorize("PRESS ENTER TO CONTINUE\n");
	    echo $console->colorize("------------------------------------------------------------------\n\n");

	    $handle = fopen ("php://stdin","r");
	    $line = fgets($handle);

	    foreach ($this->instruments as $instrument) {
		    echo $console->colorize("Updating $instrument data...\n", 'reverse');

//		    TODO insert date range by day and another foreach for below
		    $start = date('Y-m-d H:i:s',strtotime('-2 days'));
		    $start_timestamp = strtotime($start)*1000;

		    $end = date('Y-m-d H:i:s',strtotime('-1 days'));
		    $end_timestamp = strtotime($end)*1000;

		    $symbol = str_replace('-','',$instrument);
		    $url = "https://api.bitfinex.com/v2/candles/trade:1m:t$symbol/hist?start=$start_timestamp&end=$end_timestamp&sort=1&limit=1000";
		    $json = file_get_contents($url);
		    $data = json_decode($json);
	        var_dump($data);
	        die('asd');
		    $progress = count($data);
		    $i = 1;
		    foreach ($data as $val) {
			    $ticker = array(
			    	'bh_exchanges_id'   => '',
				    'timestamp'         => ($val[0]/1000),
				    'date'              => date('Y-m-d H:i:s',($val[0]/1000)),
				    'high'              => $val[3],
				    'low'               => $val[4],
				    'bid'               => null,
				    'ask'               => null,
				    'open'              => $val[1],
				    'close'             => $val[2],
				    'price'             => null,
				    'basevolume'        => $val[5]
			    );
			    $this->markOHLC($ticker, $instrument);
			    usleep(10000);
			    echo $console->progressBar($i, $progress);
			    $i++;
		    } // foreach

//		    for ($i = 1; $i <= 1000; $i++) {
//			    usleep(10000);
//			    echo $console->progressBar($i, 1000);
//		    } // for

		    echo $console->colorize("\nUPDATED $instrument for date: \n\n");
		    if ($instrument !== end($this->instruments)){
			    echo $console->colorize("Stand by ... ",'reverse');
			    sleep(1);
		    } // if

	    } // foreach

	    $return = $console->colorize("------------------------------------------------------------------\n",'green');
	    $return .= $console->colorize("Now that all the data is populated follow the instructions in README\n",'green');
	    $return .= $console->colorize("You can check in the 'bh_ticker' table are the data populated\n",'green');
	    $return .= $console->colorize("------------------------------------------------------------------\n",'green');
	    $return .= $console->colorize("Exit: all data imported. Have a great day.\n",'green');
	    $return .= $console->colorize("Bye.\n",'green');
	    echo $return;

    }
}
