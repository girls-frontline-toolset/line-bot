<?php

class GoogleAnalytics
{

    private $tid;
    private $cid;
    private $url;

    public function __construct(){
        $this->_init();
    }

    public function _init(){
        $this->tid  = "UA-108349832-1";
        $this->cid = "ad65d9f2-5d6d-47df-bc3d-1da8be386bf8";
        $this->url = "https://www.google-analytics.com/collect";
    }

    public function sendEvent($eventCategory, $eventAction){

        $query = array('v' =>"1", "t"=>"event","tid"=>$this->tid,"cid"=>$this->cid,"ec"=>$eventCategory,"ea"=>$eventAction);
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($query)
            ),
            'ssl' => array(
                'verify_peer' => false,
            )
        );

        $context  = stream_context_create($options);
        file_get_contents($this->url, false, $context);

    }


}
