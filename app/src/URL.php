<?php

namespace Src;

class URL
{
    public $id;
    public $url;
    public $hash;

    public function __construct(int $id, string $url = '', string $hash = '')
    {
        $this->url = $url;
        $this->hash = $hash;
        $this->id = $id;
    }
}
