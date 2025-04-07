<?php

require_once 'ResultFetcherInterface.php';

class MysqliResultFetcher implements ResultFetcherInterface
{
    public function fetch(\mysqli_stmt $stmt): ?array
    {
        $result = $stmt->get_result();
        return $result ? $result->fetch_assoc() : null;
    }

    public function fetchAll(\mysqli_stmt $stmt): array
    {
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}

