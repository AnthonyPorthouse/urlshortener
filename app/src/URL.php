<?php

namespace Src;

class URL
{
    public function shorten(string $url): string
    {
        return md5($url);
    }
}