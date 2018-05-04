<?php
/**
 * Created by PhpStorm.
 * User: joeldg
 * Date: 6/26/17
 * Time: 4:03 PM
 */
namespace Bowhead\Traits;

use Bowhead\Models\bh_tickers;
use Illuminate\Support\Facades\DB;

trait OHLC {

//	TODO remove this function as the data is populated in DB no need for this, leaving now for testing only!
	/**
	 * @param $ticker
	 *
	 * @return bool
	 */
	public function markOHLC($ticker, $bf_pair = 'BTC/USD',$excahnge_ID = null) {

		if (empty($excahnge_ID)) {
			$excahnge_ID = env('DEFAULT_EXCHANGE_ID');
		} // if

		$symbol = str_replace('-','/',$bf_pair);
		$high = isset($ticker['high']) ? $ticker['high'] : null;
		$low = isset($ticker['low']) ? $ticker['low'] : null;
		$bid = isset($ticker['bid']) ? $ticker['bid'] : null;
		$ask = isset($ticker['ask']) ? $ticker['ask'] : null;
		$open = isset($ticker['open']) ? $ticker['open'] : null;
		$close = isset($ticker['close']) ? $ticker['close'] : null;
		$last = isset($ticker['price']) ? $ticker['price'] : null;
		$average = ($bid+$ask)/2;

		$time = isset($ticker['timestamp']) ? strtotime($ticker['timestamp']) : time();
		$date = isset($ticker['date']) ? $ticker['date'] : date('Y-m-d H:i:s');

		$fill = array (
			'bh_exchanges_id' => $excahnge_ID,
			'symbol' => $symbol,
			'timestamp' => $time,
			'datetime' => $date,
			'high' => floatval($high),
			'low' => floatval($low),
			'bid' => floatval($bid),
			'ask' => floatval ($ask),
			'vwap' => null,
			'open' => $open,
			'close' => $close,
			'first' => null,
			'last' => $last,
			'change' => null,
			'percentage' => null,
			'average' => floatval($average),
			'baseVolume' => null,
			'quoteVolume' => null,
			'updated_at' => date('Y-m-d H:i:s'),
			'created_at' => date('Y-m-d H:i:s'),
			'deleted_at' => null
		);
		$this->updateOrCreate($fill);
//		$getDate = \DB::table('bh_tickers')->where('datetime', $date)->exists();
//		if ($getDate) {
//			\DB::table('bh_tickers')
//			  ->where('datetime', $date)
//			  ->update($fill);
//		} else {
//			\DB::table('bh_tickers')->insert($fill);
//		} // if

		return true;
	} // markOHLC

    /**
     * @param $datas
     *
     * @return array
     */
    public function organizePairData($datas, $limit=999) {
    	$exchID = env('DEFAULT_EXCHANGE_ID');
        $ret = array();
        foreach ($datas as $data) {
	        if (isset($exchID) && $data->bh_exchanges_id == $exchID) {
		        $ret['timestamp'][]   = $data->buckettime;
		        $ret['date'][]   = gmdate("j-M-y", $data->buckettime);
//		        $ret['date'][]   = gmdate("Y-m-d H:i:s", $data->buckettime);
		        $ret['low'][]    = $data->low;
		        $ret['high'][]   = $data->high;
		        $ret['open'][]   = $data->open;
		        $ret['close'][]  = $data->close;
		        $ret['volume'][] = $data->volume;
	        } else {
		        $ret[$data->bh_exchanges_id]['timestamp'][]   = $data->buckettime;
		        $ret[$data->bh_exchanges_id]['date'][]   = gmdate("j-M-y", $data->buckettime);
//		        $ret[$data->bh_exchanges_id]['date'][]   = gmdate("Y-m-d H:i:s", $data->buckettime);
		        $ret[$data->bh_exchanges_id]['low'][]    = $data->low;
		        $ret[$data->bh_exchanges_id]['high'][]   = $data->high;
		        $ret[$data->bh_exchanges_id]['open'][]   = $data->open;
		        $ret[$data->bh_exchanges_id]['close'][]  = $data->close;
		        $ret[$data->bh_exchanges_id]['volume'][] = $data->volume;
	        } // if
        } // foreach

	    if (isset($exchID)) {
		    $ret = array_reverse($ret);
	    } else {
		    foreach($ret as $ex => $opt) {
			    foreach ($opt as $key => $rettemmp) {
				    $ret[$ex][$key] = array_reverse($rettemmp);
				    $ret[$ex][$key] = array_slice($ret[$ex][$key], 0, $limit, true);
			    } // foreach
		    } // foreach
	    } // if
        return $ret;
    }

    /**
     * @param string $pair
     * @param int    $limit
     * @param bool   $day_data
     * @param int    $hour
     * @param string $periodSize
     * @param bool   $returnRS
     *
     * @return array
     */
    public function getRecentData($pair='BTC/USD', $limit=168, $day_data=false, $hour=12, $periodSize='1m', $returnRS=false) {
        /**
         *  we need to cache this as many strategies will be
         *  doing identical pulls for signals.
         */
        $connection_name = config('database.default');
        $key = 'recent::'.$pair.'::'.$limit."::$day_data::$hour::$periodSize::$connection_name";
        if(\Cache::has($key)) {
            return \Cache::get($key);
        } // if

        $timeslice = 60;
        switch($periodSize) {
            case '1m':
                $timescale = '1 minute';
                $timeslice = 60;
                break;
            case '5m':
                $timescale = '5 minutes';
                $timeslice = 300;
                break;
            case '10m':
                $timescale = '10 minutes';
                $timeslice = 600;
                break;
            case '15m':
                $timescale = '15 minutes';
                $timeslice = 900;
                break;
            case '30m':
                $timescale = '30 minutes';
                $timeslice = 1800;
                break;
            case '1h':
                $timescale = '1 hour';
                $timeslice = 3600;
                break;
            case '4h':
                $timescale = '4 hours';
                $timeslice = 14400;
                break;
            case '1d':
                $timescale = '1 day';
                $timeslice = 86400;
                break;
            case '1w':
                $timescale = '1 week';
                $timeslice = 604800;
                break;
        }
        $current_time = time();
        $offset = ($current_time - ($timeslice * $limit)) -1;

        /**
         *  The time slicing queries in various databases are done differently.
         *  Postgres supports series() mysql does not, timescale has buckets, the others don't etc.
         *  having support for timescaledb is important for the scale of the project.
         *
         *  none of these queries can be done through our eloquent models unfortunately.
         */
        if ($connection_name == 'pgsql') {
            if (Config::bowhead_config('TIMESCALEDB')) {
                // timescale query
                $results = DB::select(DB::raw("
                    SELECT time_bucket('$timescale', created_at) buckettime,
                        bh_exchanges_id,
                        first(bid, created_at) as open,
                        last(bid,created_at) as close,
                        first(bid, bid) as low,
                        last(bid,bid) as high,
                        SUM(basevolume) AS volume,
                        AVG(bid) AS avgbid,
                        AVG(ask) AS avgask,
                        AVG(basevolume) AS avgvolume
                    FROM bh_tickers
                    WHERE symbol = '$pair'
                    AND extract(epoch from created_at) > ($offset)
                    GROUP BY bh_exchanges_id, buckettime 
                    ORDER BY buckettime DESC   
                "));
                echo "test:" . $offset;
            } else {
                // regular psql query
                // TODO
                die("TimescaleDB extension required for Postgres. see timescale.com\n");
            }
        } else {
            // mysql query
            $results = DB::select(DB::raw("
              SELECT 
                bh_exchanges_id,
                SUBSTRING_INDEX(GROUP_CONCAT(CAST(bid AS CHAR) ORDER BY created_at), ',', 1 ) AS `open`,
                SUBSTRING_INDEX(GROUP_CONCAT(CAST(bid AS CHAR) ORDER BY bid DESC), ',', 1 ) AS `high`,
                SUBSTRING_INDEX(GROUP_CONCAT(CAST(bid AS CHAR) ORDER BY bid), ',', 1 ) AS `low`,
                SUBSTRING_INDEX(GROUP_CONCAT(CAST(bid AS CHAR) ORDER BY created_at DESC), ',', 1 ) AS `close`,
                SUM(basevolume) AS volume,
                ROUND((CEILING(UNIX_TIMESTAMP(`created_at`) / $timeslice) * $timeslice)) AS buckettime,
                round(AVG(bid),11) AS avgbid,
                round(AVG(ask),11) AS avgask,
                AVG(baseVolume) AS avgvolume
              FROM bh_tickers
              WHERE symbol = '$pair'
              AND UNIX_TIMESTAMP(`created_at`) > ($offset)
              GROUP BY bh_exchanges_id, buckettime 
              ORDER BY buckettime DESC
          "));
        }

        if ($returnRS) {
            $ret = $results;
        } else {
            $ret = $this->organizePairData($results, $limit);
        }

        \Cache::put($key, $ret, 2);

        return $ret;
    }

	/**
	 * We check here how old our data is in the database
	 *
	 * @return array
	 */
    public function checkRecentData() {
	    $last_record = DB::table('bh_tickers')->orderBy('id', 'desc')->first();
	    $latest_insert = strtotime($last_record->created_at);
	    $difference = time()-$latest_insert;

	    $hours = floor($difference / 3600);
	    $minutes = floor($difference / 60);
	    $seconds = $difference;

	    $data = array();
	    switch (true) {
		    case $seconds <= 60 :
			    $data = array('seconds' => 'Nice we are up to date, the difference is '.$seconds.' seconds');
			    break;
		    case $minutes <= 60 :
		    	$data = array('minutes' => 'This is just a warning we are away '.$minutes.' minutes!');
			    break;
		    case $hours <= 1 :
		    	$data = array('hours' => 'Database is out of sync '.$hours.' minutes!');
			    break;
	    } // switch

	    return $data;
    } // checkRecentData
}
