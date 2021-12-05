<?php

namespace Bowhead\Util;

class Binance
{
    public function binance_load_markets($reload = false, $params = array())
    {
        // extends
    }
    
    public function binance_fetch_markets($params = array())
    {
    }

    public function binance_create_order($symbol, $type, $side, $amount, $price = null, $params = array())
    {
    }

    public function binance_fetch_open_orders($symbol = null, $since = null, $limit = null, $params = array())
    {
    }

    public function binance_fetch_closed_orders($symbol = null, $since = null, $limit = null, $params = array())
    {
        // filter binance_create_order
    }


    public function getTransactions($type='deposits')
    {
        $ret = $this->callWC("transactions/$type");
        return $ret;
    }

    public function positionsList($state='active')
    {
        $ret = $this->callWC("positions/$state");
        return $ret;
    }

    public function positionGet($id)
    {
        if (empty($id)) {
            return false;
        }
        $ret = $this->callWC("position/$id");
        return $ret;
    }
}
