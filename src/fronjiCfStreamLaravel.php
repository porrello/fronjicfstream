<?php

namespace fronji\fronjicfstream;

use GuzzleHttp\Client;

class fronjicfstreamLaravel extends fronjicfstream
{
    public function __construct()
    {
        parent::__construct(config('cfstream.key'), config('cfstream.zone'), config('cfstream.email'));
    }
}
