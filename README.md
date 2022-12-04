# GSG Test

The solution was built using Laravel Sail. It requires Docker and Docker compose to be installed on a target system.

## How to run it?

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

3. To run tests execute this command:

```
./vendor/bin/sail test
```
