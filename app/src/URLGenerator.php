<?php

namespace Src;

use Doctrine\DBAL\Connection;
use Tuupola\Base62;

class URLGenerator
{
    /** @var Connection $db */
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function shortenURL(string $url): URL
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            throw new \InvalidArgumentException('Must be a valid URL to shorten');
        }

        // Claim our ID
        $this->db->insert('urls', ['url' => $url]);
        $id = $this->db->lastInsertId();

        $b62 = new Base62();
        $hash = $b62->encode($id);

        $url = new URL($id, $url, $hash);

        $this->db->update('urls', ['hash' => $hash], ['id' => $url->id]);

        return $url;
    }

    public function getURLFromHash(string $hash): URL
    {
        $result = $this->db->fetchAssoc('SELECT * FROM urls WHERE hash = ?', [$hash]);

        if ($result === false) {
            throw new \InvalidArgumentException('Hash does not exist');
        }

        return new URL($result['id'], $result['url'], $result['hash']);
    }
}
