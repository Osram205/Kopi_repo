<?php
require __DIR__ . "/vendor/autoload.php";
use Illuminate\Http\Client\PendingRequest;
$factory = new \Illuminate\Http\Client\Factory();
$request = $factory->asMultipart();
$options = $request->mergeOptions(["multipart" => []])->getOptions();
print_r($options);

