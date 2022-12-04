# GSG Test

The solution was built using Laravel Sail. It requires Docker and Docker compose to be installed on a target system.

## How to run it?

0. If you're using Windows it'll be better to run these commands in WSL.

1. Install dependencies by running:

```
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/var/www/html \
    -w /var/www/html \
    laravelsail/php81-composer:latest \
    composer install --ignore-platform-reqs
```

2. Run it:

```
./vendor/bin/sail up -d
```

3. Create tables by running:

```
./vendor/bin/sail artisan migrate
```

4. To run tests execute this command:

```
./vendor/bin/sail test
```

## How to use it?

Solution provides two set of endpoints.

Don't forget to add these headers to your requests:

```
Content-Type: application/json 
Accept: application/json
```

1. ``http://localhost/api/vouchers``
   1. ``GET http://localhost/api/vouchers`` get a list of active vouchers. To get a list of expired vouchers just add ``?expired`` to the URL.
   Method returns JSON
   ```
    {
        "data": [],
        "links": {
            "first": null,
            "last": null,
            "prev": null,
            "next": null
        },
        "meta": {
            "path": "http://localhost/api/vouchers",
            "per_page": 10,
            "next_cursor": null,
            "prev_cursor": null
        }
    }
   ```
   Method returns 10 orders per page. To navigate to a next or to a prev page use links provided in ``links`` section. 
   3. ``POST http://localhost/api/vouchers`` create a new voucher.
   Required params:
    ```
    {
        "unique_code": "TEST10",
        "amount": "1000",
        "expired_dt": "2022-12-10 00:00:00"
    }
    ```
   Method returns a newly created voucher:
    ```
    {
        "data": {
            "unique_code": "TEST10",
            "amount": "1000",
            "expired_dt": "2022-12-05T00:00:00.000000Z",
            "updated_at": "2022-12-04T12:05:44.000000Z",
            "created_at": "2022-12-04T12:05:44.000000Z",
            "id": 1
        }
    }
    ```
   3. ``GET http://localhost/api/vouchers/[0-9]+`` get voucher by id.
   Method returns JSON
   ```
    {
        "data": {
            "id": 1,
            "created_at": "2022-12-04T12:05:44.000000Z",
            "updated_at": "2022-12-04T12:05:44.000000Z",
            "deleted_at": null,
            "unique_code": "TEST10",
            "amount": 1000,
            "expired_dt": "2022-12-05T00:00:00.000000Z",
            "used_dt": null
        }
    }
   ```
   4. ``PUT http://localhost/api/vouchers/[0-9]+`` update voucher by id.
   Available params:
    ```
    {
        "unique_code": "TEST10",
        "amount": "1000",
        "expired_dt": "2022-12-10 00:00:00"
    }
    ```
   Method returns JSON
    ```
    {
        "data": {
            "id": 1,
            "created_at": "2022-12-04T12:05:44.000000Z",
            "updated_at": "2022-12-04T12:05:44.000000Z",
            "deleted_at": null,
            "unique_code": "TEST10",
            "amount": 1000,
            "expired_dt": "2022-12-05T00:00:00.000000Z",
            "used_dt": null
        }
    }
    ```
   5. ``DELETE http://localhost/api/vouchers/[0-9]+`` remove voucher by id.
   Method returns empty JSON.
   ```
   {}
   ```
2. ``http://localhost/api/orders``
   1. ``GET http://localhost/api/vouchers`` get a paginated list of orders.
   Method returns JSON
   ```
    {
        "data": [
            {
                "id": 1,
                "created_at": "2022-12-04T12:11:11.000000Z",
                "updated_at": "2022-12-04T12:11:11.000000Z",
                "deleted_at": null,
                "total_amount": 10000,
                "amount_to_pay": 9000,
                "voucher_code": "TEST10"
            }
        ],
        "links": {
            "first": null,
            "last": null,
            "prev": null,
            "next": null
        },
        "meta": {
            "path": "http://localhost/api/orders",
            "per_page": 10,
            "next_cursor": null,
            "prev_cursor": null
        }
    }
   ```
   Method returns 10 orders per page. To navigate to a next or to a prev page use links provided in ``links`` section.
   3. ``POST http://localhost/api/vouchers`` create new order.
   Available params
   ```
    {
        "total_amount": "10000",
        "voucher_code": "TEST10"
    }
   ```
   ``total_amount`` is required.
    Method return JSON
    ```
    {
        "data": {
            "total_amount": "10000",
            "updated_at": "2022-12-04T12:11:11.000000Z",
            "created_at": "2022-12-04T12:11:11.000000Z",
            "id": 1,
            "amount_to_pay": 9000
        }
    }
    ```
