# Laravel eloquent JSON API

## Introduction
   JSON:API is a specification for how a client should request that resources be fetched or modified, and how a server should respond to those requests.
   
   JSON:API is designed to minimize both the number of requests and the amount of data transmitted between clients and servers. This efficiency is achieved without compromising readability, flexibility, or discoverability.
   
   JSON:API requires use of the JSON:API media type (application/vnd.api+json) for exchanging data.

## Install

- `composer require ....`
- add `\Krasnikov\EloquentJSON\EloquentJsonServiceProvider::class,` to Application Service Providers
- php artisan vendor:publish --provider="Krasnikov\EloquentJSON\EloquentJsonServiceProvider" --tag=config
- php artisan vendor:publish --provider="Krasnikov\EloquentJSON\EloquentJsonServiceProvider" --tag=translations
- change date format and route prefix in `config/jsonSpec.php`
- use `ModelJson` trait in your models on extended it `JsonModel` class
