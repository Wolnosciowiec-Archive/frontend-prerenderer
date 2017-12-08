<?php declare(strict_types=1);

namespace App\Manager;

/**
 * @package App\Manager
 */
class VisitedUrlsManager
{
    /**
     * @var \PDO $db
     */
    private $db;

    public function __construct()
    {
        $this->db = new \PDO('sqlite:' . __DIR__ . '/../../data/storage.sqlite3');
    }

    /**
     * @param string $url
     */
    public function addUrl(string $url)
    {
        if (trim($url) === '' || $this->hasUrl($url)) {
            return;
        }

        $statement = $this->db->query('INSERT INTO urls_visited_by_crawlers (url) VALUES (:url)');
        $statement->bindValue(':url', $url);
        $statement->execute();
    }

    /**
     * @param string $url
     *
     * @return bool
     */
    private function hasUrl(string $url): bool
    {
        $statement = $this->db->query('SELECT COUNT(*) FROM urls_visited_by_crawlers WHERE url = :url');
        $statement->bindValue(':url', $url);
        $statement->execute();

        return (int) $statement->fetch()['COUNT(*)'] >= 1;
    }
}
