<?php

require_once('SaleBasketRepository.php');

$redis = new Redis();
$redis->connect('localhost', 6379);
$timeStart = microtime();

$useCache = (int)( $_GET['cache'] ?? 0 )  === 1;
$selectedId = $_GET['id'] ?? null;

$responseData = [];

if ( $useCache ) {
$key = SaleBasketRepository::tableName();
    if ( $selectedId ) {
        $cache = findInCacheOrSetToCache( $redis, "$key.{$selectedId}", function() use ( $selectedId ) {
            return (new SaleBasketRepository())->find( $selectedId );
        });
    } else {
        $cache = findInCacheOrSetToCache( $redis, $key, function() {
            return (new SaleBasketRepository())->findAll();
        });
        echo 'items count: ' . count($cache). '<br/>';
    }
    $responseData = $cache;
} else {
    echo 'not cache!!! <br/>'; 
    $responseData = $selectedId ? (new SaleBasketRepository())->find( $selectedId ) : (new SaleBasketRepository())->findAll();
    echo 'items count: ' . count($responseData). '<br/>';
}

function findInCacheOrSetToCache( $redis, $key, $callback ) {
    $cache = $redis->get( $key );
    if ( !$cache ) {
        echo 'not cache!!! <br/>'; 
        $cache = $callback();
        $redis->set( $key, json_encode( $cache ) );
    } else {
        echo 'data from cache <br/>';  
        $cache = json_decode( $cache );
    }
    return $cache;
}

$timeEnd = microtime() - $timeStart;

echo "Server is running: ".$redis->ping() . '<br/>';
echo "Page loading in: $timeEnd milliseconds";

