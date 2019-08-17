# Laravel eloquent JSON API

## Introduction
   JSON:API is a specification for how a client should request that resources be fetched or modified, and how a server should respond to those requests.
   
   JSON:API is designed to minimize both the number of requests and the amount of data transmitted between clients and servers. This efficiency is achieved without compromising readability, flexibility, or discoverability.
   
   JSON:API requires use of the JSON:API media type (application/vnd.api+json) for exchanging data.

## Install

- `composer require krasnikov/eloquent-json`
- add `\Krasnikov\EloquentJSON\EloquentJsonServiceProvider::class,` to Application Service Providers
- `php artisan vendor:publish --provider="Krasnikov\EloquentJSON\EloquentJsonServiceProvider" --tag=config`
- `php artisan vendor:publish --provider="Krasnikov\EloquentJSON\EloquentJsonServiceProvider" --tag=translations`
- change date format and route prefix in `config/jsonSpec.php`,
- if you need object id in attributes set `'show_id' => true` in `config/jsonSpec.php`,
- use `ModelJson` trait in your models
```php
<?php
namespace App\Domain\User;

use Krasnikov\EloquentJSON\Traits\ModelJson;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use ModelJson;
    
}
```
or extended it `JsonModel` class

```php
<?php
namespace App\Domain\User;

use Krasnikov\EloquentJSON\JsonModel;

class User extends JsonModel
{
    
}
```
- use parameter `$allowedReferences` in your model for define allowed reference list
```php
<?php
namespace App\Domain\User;

use Krasnikov\EloquentJSON\Traits\ModelJson;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use ModelJson;
    
    protected $allowedReferences = [
        'account',
        'roles',
        'roles.permission'
    ];  
}
```
- use ```Krasnikov\EloquentJSON\Pagination::fromRequest($request)``` in your controller for set pagination and send object to repository;
- use ```Krasnikov\EloquentJSON\Sorting::fromRequest($request)``` in your controller for set sorting and send object to repository.
In repository use method `setBuilder` for set sorting to query builder `$sorting->setBuilder($builder)`;
- use Model method `toJsonSpec()` in your controller for get response structure ```return \response()->json($users->toJsonSpec(), Response::HTTP_OK);```

## Load relationships
```
GET /articles/1?include=author,comments.author HTTP/1.1
```
### Sparse Fieldsets
```
GET /articles?include=author&fields[articles]=title,body&fields[people]=name HTTP/1.1
```
### Pagination
```
GET /articles?page[number]=2&page[size]=10 HTTP/1.1
```

#### Response example
```json
{
    "data": [
        {
            "type": "user",
            "id": 1,
            "attributes": {
                "id": 1,
                "name": "Admin",
                "email": "admin@admin.com",
                "emailVerifiedAt": "2019-07-30 10:46:20"
            },
            "relationships": {
                "roles": {
                    "data": [
                        {
                            "id": 1,
                            "type": "role",
                            "relations": {
                                "permissions": {
                                    "data": [
                                        {
                                            "id": 1,
                                            "type": "permission"
                                        },
                                        {
                                            "id": 2,
                                            "type": "permission"
                                        },
                                        {
                                            "id": 3,
                                            "type": "permission"
                                        },
                                        {
                                            "id": 4,
                                            "type": "permission"
                                        },
                                        {
                                            "id": 5,
                                            "type": "permission"
                                        }
                                    ]
                                }
                            }
                        }
                    ]
                },
                "account": {
                    "data": {
                        "id": 1,
                        "type": "account"
                    }
                },
                "permissions": {
                    "data": []
                }
            }
        },
        {
            "type": "user",
            "id": 2,
            "attributes": {
                "id": 2,
                "name": "Admin2",
                "email": "admin2@admin.com",
                "emailVerifiedAt": "2019-07-30 10:46:20"
            },
            "relationships": {
                "roles": {
                    "data": [
                        {
                            "id": 2,
                            "type": "role",
                            "relations": {
                                "permissions": {
                                    "data": [
                                        {
                                            "id": 1,
                                            "type": "permission"
                                        }
                                    ]
                                }
                            }
                        }
                    ]
                },
                "account": {
                    "data": {
                        "id": 2,
                        "type": "account"
                    }
                },
                "permissions": {
                    "data": []
                }
            }
        }
    ],
    "meta": {
        "total": 2,
        "perPage": 20
    },
    "links": {
        "self": "http://0.0.0.0:81/api/v1.0/users?page%5Bnumber%5D=1&page%5Bsize%5D=20",
        "first": "http://0.0.0.0:81/api/v1.0/users?page%5Bnumber%5D=1&page%5Bsize%5D=20",
        "last": "http://0.0.0.0:81/api/v1.0/users?page%5Bnumber%5D=1&page%5Bsize%5D=20"
    },
    "included": [
        {
            "id": 1,
            "type": "permission",
            "attributes": {
                "id": 1,
                "name": "List user",
                "guardName": "api",
                "createdAt": "2019-07-30 10:46:20",
                "updatedAt": "2019-07-30 10:46:20"
            }
        },
        {
            "id": 2,
            "type": "permission",
            "attributes": {
                "id": 2,
                "name": "Create user",
                "guardName": "api",
                "createdAt": "2019-07-30 10:46:20",
                "updatedAt": "2019-07-30 10:46:20"
            }
        },
        {
            "id": 3,
            "type": "permission",
            "attributes": {
                "id": 3,
                "name": "Update user",
                "guardName": "api",
                "createdAt": "2019-07-30 10:46:20",
                "updatedAt": "2019-07-30 10:46:20"
            }
        },
        {
            "id": 4,
            "type": "permission",
            "attributes": {
                "id": 4,
                "name": "Delete user",
                "guardName": "api",
                "createdAt": "2019-07-30 10:46:20",
                "updatedAt": "2019-07-30 10:46:20"
            }
        },
        {
            "id": 5,
            "type": "permission",
            "attributes": {
                "id": 5,
                "name": "Get user",
                "guardName": "api",
                "createdAt": "2019-07-30 10:46:21",
                "updatedAt": "2019-07-30 10:46:21"
            }
        },
        {
            "id": 1,
            "type": "role",
            "attributes": {
                "id": 1,
                "name": "Admin",
                "guardName": "api",
                "createdAt": "2019-07-30 10:46:21",
                "updatedAt": "2019-07-30 10:46:21"
            }
        },
        {
            "id": 1,
            "type": "account",
            "attributes": {
                "id": 1,
                "userId": 1,
                "firstName": "Alex",
                "lastName": "Krasnikov",
                "phone": "88888888888"
            }
        },
        {
            "id": 2,
            "type": "role",
            "attributes": {
                "id": 2,
                "name": "User",
                "guardName": "api",
                "createdAt": "2019-07-30 10:46:21",
                "updatedAt": "2019-07-30 10:46:21"
            }
        },
        {
            "id": 2,
            "type": "account",
            "attributes": {
                "id": 2,
                "userId": 2,
                "firstName": "Alex",
                "lastName": "Krasnikov",
                "phone": "88888888888"
            }
        }
    ]
}
```
## Sample output
[CHANGELOG.md](./CHANGELOG.md)
