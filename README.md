# Readme: Db

Some helpers for database task and query construction.

**Contents:**

 - Parser\ObjectParser
 - ArrayUtil
 - Highlight
 - Icons
 - Options

## Install

`composer require inanepain/db`

## Usage

```php
$qb = new SQLQueryBuilder();
$query = $qb
    ->select('users', ['name', 'email', 'password'])
    ->where('age', 18, '>')
    ->where('age', 30, '<')
    ->limit(10, 20);


echo "-- Testing MySQL query builder:\n";
echo $qb->getSQLFor(new MysqlQueryBuilder());

echo "\n\n";

echo "-- Testing PostgresSQL query builder:\n";
echo $qb->getSQLFor(new PostgresQueryBuilder());
```

Which should give you:

```sql
-- Testing MySQL query builder:
SELECT name, email, password FROM users WHERE age > 18 AND age < 30 LIMIT 10, 20;

-- Testing PostgresSQL query builder:
SELECT name, email, password FROM users WHERE age > 18 AND age < 30 LIMIT 10 OFFSET 20;
```
