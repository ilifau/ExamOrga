# How to run plugin unit tests # 

To start the unit tests do following in plugin directory:

1. composer require --dev phpunit/phpunit ^9

2. edit composer.json to contain:
{
    "autoload": {
        "classmap": [
            "classes/"
        ]
    },
    "require-dev": {
        "phpunit/phpunit": "^9"
    }
}

3. composer update

4. Run tests:
 ./vendor/bin/phpunit --testdox --debug tests


See https://phpunit.de/getting-started/phpunit-9.html