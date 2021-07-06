# Laravel API ⭐

## Summary
[Description](#Description)\
[Installation](#Installation)\
[Dependencies](#Dependencies)\
[Environment](#Environment)\
[Entities](#Entities)\
[Endpoints](#Endpoints)

## Description
+ Read [dependenies](#Dependencies) section.
+ Clone repository.
+ Run the following commands:

    ```
        composer require laravel/sail --dev 
    ```
    ```
        php artisan sail:install
    ```
+ Create an *.env* file with the variables specified in [environment section](#Environment)
+ To test in development mode run `sail up -d`

## Installation

## Dependencies
There are listed on [package.json](https://github.com/irenehl/LaravelAPI/blob/master/package.json)

## Environment
| Variable         |
|------------------|
| MAIL             |
| DB_CONNECTION    |
| DB_HOST          |
| DB_DATABASE      |
| DB_USERNAME      |
| DB_PASSWORD      |
| APP_KEY          |

## Entities
The base of the API are ***products and users*** so, these are the models that were used

### User Model

```
class UserModel extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'phone',
        'dob'
    ];
}
```

### Product Model
```
class ProductModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'name',
        'stock',
        'price',
        'description'
    ];
}
```

## Endpoints
Endpoints are documented and handled in [Insomnia](https://support.insomnia.rest/) client, refer to [Insomnia JSON file]() and import it to client.

-----------------------

If you have any question, you can send me a email to <26irenelopez@gmail.com> 😎