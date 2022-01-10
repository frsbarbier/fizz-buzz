# fizz-buzz

A fizz-buzz REST API written in php with Phalcon Framework

## Requirements
- [git](https://git-scm.com/book/en/v2/Getting-Started-Installing-Git)
- [docker](https://docs.docker.com/get-docker)
- [docker-compose](https://docs.docker.com/compose/install)

## Installation

### Download project

Clone the project:
```bash
git clone https://github.com/frsbarbier/fizz-buzz.git
cd fizz-buzz
git checkout main
```

### Build image
```bash
docker-compose build
```
Building this image can take some time because of downloading and compile some libraries.

### Create and start containers
```bash
docker-compose up -d
```

## Usage

### Endpoints
The server have two endpoints returning json response :

- fizzbuzz :  
**http://127.0.0.1:8080/api/fizzbuzz/{int1}/{int2}/{limit}/{str1}/{str2}**  
This endpoint start a fizzbuzz with five query parameters.  
int1 : first integer  
int2 : second integer  
limit: limit integer  
str1 : first string  
str2 : second string  
  
This endpoint return string with numbers from 1 to limit, where: all multiples of int1 are replaced by str1, all multiples of int2 are replaced by str2, all multiples of int1 and int2 are replaced by str1str2, like this : "1,2,fizz,4,buzz,fizz,7,8,fizz,buzz,11,fizz,13,14,fizzbuzz,16,..." 

The operationnal code is defined in the [src/Controllers/Api.php](https://github.com/frsbarbier/fizz-buzz/blob/5b283fd3681e1c2e11e99adde3d04dd085580a6d/src/Controllers/Api.php#L123-L159) file, in the doFizzBuzz method


- stats:  
**http://127.0.0.1:8080/api/stats**  
This endpoint returns the most used fizzbuzz request, with query parameters and the number of hits, like {"hit": 5, "int1": 1, "int2": 2, "limit": 100, "str1": "fizz", "str2": "buzz"}

A middleware is triggered for fizzbuzz request to increase the number of hits, and save paramaters in the redis server, in order to get stats.
The operationnal code is defined in the [src/Middlewares/FizzBuzz.php](https://github.com/frsbarbier/fizz-buzz/blob/5b283fd3681e1c2e11e99adde3d04dd085580a6d/src/Middlewares/FizzBuzz.php#L18-L28) file.  
To increase hits number, [ZINCRBY](https://redis.io/commands/ZINCRBY) redis function is used in the [src/Models/Stat.php](https://github.com/frsbarbier/fizz-buzz/blob/5b283fd3681e1c2e11e99adde3d04dd085580a6d/src/Models/Stat.php#L116-L123) file

In order to retrieve the top request, [ZREVRANGE](https://redis.io/commands/zrevrange) redis function is used, to get the greater hit and the request parameters.
The operationnal code is defined in the [src/Models/Stat.php](https://github.com/frsbarbier/fizz-buzz/blob/5b283fd3681e1c2e11e99adde3d04dd085580a6d/src/Models/Stat.php#L144-L160) file, in the getTopRequest method

There are different ways to test application :
- use [curl](https://curl.se) in CLI, 
- use your web browser
- use client like [Postman](https://www.postman.com)
- use swagger UI : http://127.0.0.1:8080/swagger

[Visit API doc](https://rawcdn.githack.com/frsbarbier/fizz-buzz/main/docs/api/index.html)

### Run unit tests
```bash
docker-compose exec web vendor/bin/phpunit
```

### Generating API Documentation with [phpDocumentor](https://www.phpdoc.org)
```bash
docker run --rm -v $(pwd):/data phpdoc/phpdoc:3 -d . -t ./docs/api --ignore vendor/ --ignore tests/
```

### Generating swagger.json for [Swagger UI](https://swagger.io/tools/swagger-ui)
```bash
docker-compose exec web vendor/bin/openapi --format json --output public/swagger/swagger.json src/
````

### Server requirements
- [php](https://www.php.net) 7.4.27
- [apache](https://httpd.apache.org) 2.4.51
- [redis](https://redis.io) 6.2.6
- [php-psr](https://github.com/jbboehr/php-psr) 1.1.0
- [phalcon](https://phalcon.io) 4.1.2
- [php-redis](https://github.com/phpredis/phpredis) 5.3.5
- [composer](https://getcomposer.org) 2.2.3
- [swagger-php](https://github.com/zircote/swagger-php) 4.2.0
- [phalcon/incubator-test](https://github.com/phalcon/incubator-test) v1.0.0-alpha.1

### Stop and delete containers
```bash
docker-compose down
```
