<?php

function dd($value) 
{
    echo "<pre>";
    var_dump($value);
    echo "</pre>";
    die();
}

function sendJson(array $param): void
{
    header('content-type: application/json');
    echo json_encode($param);
}

function errorJson(bool $status, string $message = ''): void 
{
    header('content-type: application/json');
    echo json_encode(['status' => $status, 'message' => $message]);
}

function sendResult(bool $status, string $mesej, $data = null): array 
{
    return ["status" => $status,"message" => $mesej, "data" => $data];
}