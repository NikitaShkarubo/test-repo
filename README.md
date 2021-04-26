To run all tests use command:
```bash
./vendor/bin/phpunit tests
```
To run application use this command:
```bash
php application.php input.csv
```
There is **input.csv** file int the root of the project which contain test data.

To run command via **docker-compose**:
```bash
docker-compose run php ./application.php input.csv
```
To run tests via **docker-compose**:
```bash
docker-compose run php vendor/bin/phpunit tests
```

Thanks for the interesting task!
