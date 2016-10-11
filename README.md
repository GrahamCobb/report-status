# report-status
Record status reports from various sources

## Sending reports
To send a report use:
````
wget https://.../logapi.php/statuslog --post-data="source=sss&target=tttt&status=0&log_text=mm" --user=user --password=pass
````

## REST API
The API is a simple CRUD API using the https://github.com/mevdschee/php-crud-api library.

## Display log
To see the log visit https://.../log.php with a web browser.

