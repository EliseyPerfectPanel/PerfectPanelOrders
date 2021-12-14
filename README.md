# Test Project

```bash
1. git clone https://github.com/EliseyPerfectPanel/PerfectPanelOrders
```
Конфиг для окружения:
```bash
2. cp .env-example .env
```
```bash
3. cd docker
```
```bash
4. docker-compose up -d  --build
```
Конфиг для докера
```bash
5. cp .env-dist .env
```
```bash
6. Open CLI image name local/yiisoftware/yii2-php:7.4-apache
```
Установка Yii и пары дополнительных пакетов
```bash
7. composer install
```
Запуск миграции.
```bash
8. php yii migrate
```

## Links

- [Phpmyadmin http://localhost:8080](http://localhost:8080)
- [Project http://127.0.0.1:8101/orders](http://127.0.0.1:8101/orders)