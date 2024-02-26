# Тестовое задание Drom

## Docker build
```shell
docker build -t drom_test:latest ./
```

## Задание 1

```shell
#Usage
docker run --rm -t drom_test ./../task_1/index.php ./../task_1/fixtures
```

## Задание 2
```shell
#Test
docker run --rm -t drom_test composer test
```