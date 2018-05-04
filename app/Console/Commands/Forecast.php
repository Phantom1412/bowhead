<?php

namespace Bowhead\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class Forecast extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bowhead:forecast';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Base on history data the AI try to forecast the price';

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

	    $which='close';
	    $pair='BTC-USD';
	    $a = \DB::table('historical')
	            ->select('*')
	            ->where('pair', $pair)
	            ->orderby('buckettime', 'DESC')
	            ->limit(24*7)
	            ->get();

	    $csv = "seq,id,curr,close,open,volume,zero\n";
	    $_csv = array();
	    foreach($a as $stuff) {
		    $_csv[] = "'".$stuff->buckettime ."',". $stuff->id .",'". $stuff->pair . "'," . (float)$stuff->close .','.(float)$stuff->open.','.(float)$stuff->volume.",0\n";
	    }
	    $__csv = array_reverse($_csv);
	    $ccsv = join ("", $__csv);
	    $csv .= trim($ccsv);

	    \Cache::put('tempbook',$csv,5); // we use redis to pass this to python

	    echo array_pop($__csv) . "\n";
	    $doing = base_path() . "/app/Scripts/$which"."_prediction.py";
	    $process = new Process("python -W ignore $doing");
	    $process->run();
	    // executes after the command finishes
	    if (!$process->isSuccessful()) {
		    throw new ProcessFailedException($process);
	    }
	    $out = explode(',', $process->getOutput());
	    return round(array_pop($out),2);
    }
}
