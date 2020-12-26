## Select with raw SQL

```php
$secondaryExams = $db->select("select examination_name as Id, examination_name from examinations where level='secondary' order by serial_number")->fromSQL()->fetchArray()->toList();
```



