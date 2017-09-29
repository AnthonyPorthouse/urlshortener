# UrlShortener

## Installation instructions
+ Install Docker and docker-compose
+ Run `docker-compose up -d`
+ Run `docker run --rm --interactive --tty --volume $PWD/app:/app composer install`
+ Run `docker-compose exec php ./vendor/bin/phinx migrate`
+ Map `urlshortener.dev` to `localhost` using your preferred method

## Running Unit Tests
Run the following: `docker-compose exec php vendor/bin/phpunit`

## Usage Instructions

### Shortening a URL

Simply go to the `/shorten` route and pass in the url via a parameter called `url`.

E.g. `http://urlshortener.dev/shorten?url=http://google.com`

The response will contain the shortened url.

### Expanding a URL

Simply go to the shortened URL and you will be redirected to the original URL specified.