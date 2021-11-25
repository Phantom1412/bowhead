<?php
 
namespace Bowhead\Console\Commands\My;

use Bowhead\Models;
use Bowhead\Traits;
use Bowhead\Util\Console;
use Carbon\Carbon;
use ccxt;
use ccxt\BaseError;
use Illuminate\Console\Command;
use ccxt\AuthenticationError;
use Bowhead\Traits\Config;

class DataRunnerCcxtCommand extends Command
{
    use Config, Traits\DataCcxt;

    /**
     * @var array
     */
    protected $pairs = [];

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'bowhead:datarunner_ccxt';

    /**
     * @var string
     */
    protected $signature = 'bowhead:datarunner_ccxt {--update} {--v} {--vv}';

    /**
     * @var int
     */
    protected $memusage = 0;

    /**
     * @var int
     */
    protected $lastmemusage = 0;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'CCXT runner, this gets data for exchanges we select.';

    /**
     * @param $size
     *
     * @return string
     */
    function convert($size)
    {
        if ($size == 0) {
            return "0b";
        }
        $unit=array('b','kb','mb','gb','tb','pb');
        if ($size < 0) {
            return "-" . @round(abs($size)/pow(1024,($i=floor(log(abs($size),1024)))),2).' '.$unit[$i];
        }
        return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
    }

    /**
     * @param $line
     */
    public function profile($line)
    {
        gc_enable();
        $mem = memory_get_usage();
        $pri = $this->lastmemusage;
        $memchange = $mem-$pri;

        echo "$line mem: ". $this->convert($mem) ." used, ". $this->convert($memchange)." from ". $this->convert($pri) ."\n";
        $this->lastmemusage = $mem;

        return;
    }

    public function handle()
    {
        if ($this->bowhead_config('COINIGY') == 1) {
            exit(1);
        }

        ini_set('memory_limit', '256M');
        stream_set_blocking(STDIN, 0);

        $update_all = $this->option('update');
        $verbose = $this->option('v');
        $very_verbose = $this->option('vv');

        if ($verbose) { $this->profile(__LINE__); }

        $console = new Console();
        $trading_pairs = $this->bowhead_config('PAIRS');
        if (empty($trading_pairs)) {
            echo $console->colorize("
                You need to add in some trading pairs in the bh_configs table.
                This can be done by running the migrations and seeding the database.\n", 'bg_red', 'bold');
            return;
        }
        $trading_pairs = explode(',', $trading_pairs);
        $trading_exchanges = explode(',', $this->bowhead_config('EXCHANGES'));

        if ($update_all) {
            $exchanges = ccxt\Exchange::$exchanges;
            foreach ($exchanges as $exchange) {
                $classname = 'ccxt\\' . $exchange;
                $class = new $classname;

                $ins = [];
                $ins['exchange'] = $exchange;
                $ins['hasFetchTickers'] = $class->hasFetchTickers ?? 1;
                $ins['hasFetchOHLCV'] = $class->hasFetchOHLCV ?? 1;
                $ins['data'] = json_encode($class->markets_by_id, 1);

                Models\BhExchanges::updateOrCreate(['exchange' => $exchange], $ins);
            }

            $ex_loop = Models\BhExchanges::where('ccxt', 1)->whereIn('id', $trading_exchanges)->get();
            foreach ($ex_loop as $ex) {
                $exchange = $ex->exchange;

                $classname = 'ccxt\\' . $exchange;
                $class = new $classname;
                try {
                    echo "updating $exchange data\n";
                    $markets = $class->load_markets();
                    foreach (array_keys($markets) as $pair) {
                        $pair_model = new Models\BhExchangePairs();
                        $pair_model::updateOrCreate(['exchange_id' => $ex->id, 'exchange_pair' => $pair]);
                    }
                    $markets = [];
                } catch (AuthenticationError $e) {
                    echo "\n\t$exchange needs auth (set this exchange to -1 in the database to disable it):\n $e..\n\n";
                } catch (BaseError $e) {
                    echo "\n\t$exchange error (set this exchange to -1 in the database to disable it):\n $e\n\n";
                }
            }
            echo "\n\nUPDATED .. run normally now\n\n";
            return;
        }

        /**
         *  Do class instantiation here to avoid doing it in the loop which
         *  adds to memory on each loop for the system try/catch
         */
        $ex_loop = Models\BhExchanges::where('ccxt', 1)->whereIn('id', $trading_exchanges)->get();
        foreach ($ex_loop as $ex) {
            $exchange = $ex->exchange;
            $classname = '\ccxt\\' . $exchange;
            ${'bh_'.$exchange} = new $classname(array (
                'enableRateLimit' => true,
                'apiKey'   => Config::bowhead_config(strtoupper($exchange) .'_APIKEY'),
                'secret'   => Config::bowhead_config(strtoupper($exchange) .'_SECRET'),
                'uid'      => Config::bowhead_config(strtoupper($exchange) .'_UID'),
                'password' => Config::bowhead_config(strtoupper($exchange) .'_PASSWORD')
            ));
            if ($verbose){ echo "$exchange mem: ". $this->profile(__LINE__); }
        }
        $carbon = new Carbon();

        while (1) {
            if ($verbose) { $this->profile(__LINE__); }

            if (ord(fgetc(STDIN)) == 113) {
                echo "QUIT detected...";
                return null;
            }

            $ex_loop = Models\BhExchanges::where('ccxt', 1)->whereIn('id', $trading_exchanges)->get();
            foreach ($ex_loop as $ex) {
                $exchange = $ex->exchange;
                if (isset(${'bh_'.$exchange})) {
                    $class = ${'bh_' . $exchange};
                } else {
                    $classname = '\ccxt\\' . $exchange;
                    ${'bh_'.$exchange} = new $classname(array (
                        'enableRateLimit' => true,
                        'apiKey'   => Config::bowhead_config(strtoupper($exchange) .'_APIKEY'),
                        'secret'   => Config::bowhead_config(strtoupper($exchange) .'_SECRET'),
                        'uid'      => Config::bowhead_config(strtoupper($exchange) .'_UID'),
                        'password' => Config::bowhead_config(strtoupper($exchange) .'_PASSWORD')
                    ));
                    $class = ${'bh_' . $exchange};
                }

                try {
                    // $markets = $class->load_markets();
                    foreach ($trading_pairs as $pair) {
                        if ($ex->hasFetchTickers) {
                            $tick = $class->fetchTicker($pair);
                            unset($tick['info']);
                            $dt = explode('.', $tick['datetime']);
                            $tick['timestamp'] = (intval($tick['timestamp']) / 1000);
                            $tick['bh_exchanges_id'] = $ex->id;
                            $tick['datetime'] = $dt[0];

                            $tick['basevolume'] = $tick['baseVolume'];
                            unset($tick['baseVolume']);
                            $tick['quotevolume'] = $tick['quoteVolume'];
                            unset($tick['quoteVolume']);

                            Models\BhTickers::updateOrCreate(
                                ['bh_exchanges_id' => $ex->id, 'symbol' => $pair, 'timestamp' => $tick['timestamp']]
                                , $tick);
                        }
                        if ($ex->hasFetchOHLCV) {
                            $ohlcc = $class->fetchOHLCV($pair, '1m', ($carbon->now()->subMinutes(5)->timestamp*1000), 3);
                            foreach($ohlcc as $oh){
                                $ins = [];
                                $ins['bh_exchanges_id'] = $ex->id;
                                $ins['symbol'] = $pair;
                                $ins['timestamp'] = (intval($oh[0]) / 1000);
                                $ins['datetime'] = $carbon->createFromTimestamp(($oh[0]/1000))->toDateTimeString();
                                $ins['open']   = $oh[1];
                                $ins['high']   = $oh[2];
                                $ins['low']    = $oh[3];
                                $ins['close']  = $oh[4];
                                $ins['volume'] = $oh[5];

                                Models\BhOhclvs::updateOrCreate(
                                    ['bh_exchanges_id' => $ex->id, 'symbol' => $pair, 'timestamp' => $ins['timestamp']]
                                    , $ins);
                            }
                        }
                    }
                } catch (\Exception $e) {
                    if ($verbose) { $this->profile(__LINE__); }
                    if ($verbose) {
                        echo "temp issue with $exchange\n";
                        if ($very_verbose) { echo $e; }
                    }

                    unset($e); // or will cause mem leak
                } catch (AuthenticationError $e) {
                    echo "\n\t$exchange needs auth (set this exchange to -1 in the database to disable it):\n $e..\n\n";
                } catch (BaseError $e) {
                    echo "\n\t$exchange error (set this exchange to -1 in the database to disable it):\n $e\n\n";
                }
            }
            if ($verbose) {
                echo "line: " . __LINE__ . " mem: ". $this->convert(memory_get_usage()) ." used\n";
                echo "Sleeping\n";
            }
            sleep(5);
        }
    }
}
