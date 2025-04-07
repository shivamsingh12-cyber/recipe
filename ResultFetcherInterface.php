<?php

interface ResultFetcherInterface
{
    public function fetch(\mysqli_stmt $stmt): ?array;
    public function fetchAll(\mysqli_stmt $stmt): array;

    
}


