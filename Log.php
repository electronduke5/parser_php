<?php
class Log
{
    private $ip;
    private $identity;
    private $user;
    private $date;
    private $time;
    private $timezone;
    private $method;
    private $path;
    private $protocol;
    private $status;
    private $bytes;
    private $url;
    private $agent;

    public function __construct($lineArray){
        $this->ip = $lineArray['ip'];
        $this->identity = $lineArray['identity'];
        $this->user = $lineArray['user'];
        $this->date = $lineArray['date'];
        $this->time = $lineArray['time'];
        $this->timezone = $lineArray['timezone'];
        $this->method = $lineArray['method'];
        $this->path = $lineArray['path'];
        $this->protocol = $lineArray['protocol'];
        $this->status = $lineArray['status'];
        $this->bytes = $lineArray['bytes'];
        $this->url = $lineArray['url'];
        $this->agent = $lineArray['agent'];
    }

    public function bytes() : int{
        return $this->bytes;
    }
    public function agent() : string{
        return $this->agent;
    }
}