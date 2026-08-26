<?php

declare(strict_types=1);

namespace Inane\Db\Tests\Query;

use Inane\Db\Query\Clause\OrderDirection;
use Inane\Db\Query\DatabaseDriver;
use Inane\Db\Query\Grammar\ANSIGrammar;
use Inane\Db\Query\Grammar\MySQLGrammar;
use Inane\Db\Query\Grammar\PostgreSQLGrammar;
use Inane\Db\Query\Grammar\SQLiteGrammar;
use Inane\Db\Query\QueryBuilder;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class QueryBuilderTest extends TestCase {
    public function testBuildsComplexSqliteSelectQueryWithBindings(): void {
        $builder = new QueryBuilder(DatabaseDriver::SQLITE);

        $sql = $builder
            ->table('users')
            ->select('users.id', 'users.name')
            ->join('profiles', 'users.id', 'profiles.user_id')
            ->where('active', true)
            ->orWhere('role', 'admin')
            ->whereIn('id', [1, 2])
            ->whereNull('deleted_at')
            ->whereNotNull('verified_at')
            ->whereBetween('age', [18, 65])
            ->groupBy('users.id')
            ->having('total', '>', 10)
            ->orderBy('users.name', OrderDirection::DESC)
            ->limit(10)
            ->offset(5)
            ->toSql();

        self::assertSame(
            'SELECT "users"."id", "users"."name" FROM "users" INNER JOIN "profiles" ON "users"."id" = "profiles"."user_id" WHERE "active" = ? OR "role" = ? AND "id" IN (?, ?) AND "deleted_at" IS NULL AND "verified_at" IS NOT NULL AND "age" BETWEEN ? AND ? GROUP BY "users"."id" HAVING "total" > ? ORDER BY "users"."name" DESC LIMIT 10 OFFSET 5',
            $sql,
        );
        self::assertSame([1, 'admin', 1, 2, 18, 65, 10], $builder->getBindings());
    }

    public function testBuildsInsertUpdateAndDeleteQueries(): void {
        $insert = new QueryBuilder(DatabaseDriver::MYSQL);
        $update = new QueryBuilder(DatabaseDriver::MYSQL);
        $delete = new QueryBuilder(DatabaseDriver::MYSQL);

        self::assertSame(
            'INSERT INTO `users` (`name`, `active`) VALUES (?, ?)',
            $insert->table('users')->insert(['name' => 'Ada', 'active' => true])->toSql(),
        );
        self::assertSame(['Ada', true], $insert->getBindings());

        self::assertSame(
            'UPDATE `users` SET `name` = ?, `active` = ? WHERE `id` = ?',
            $update->table('users')->update(['name' => 'Grace', 'active' => false])->where('id', 7)->toSql(),
        );
        self::assertSame(['Grace', false, 7], $update->getBindings());

        self::assertSame(
            'DELETE FROM `users` WHERE `id` = ?',
            $delete->table('users')->delete()->where('id', 7)->toSql(),
        );
        self::assertSame([7], $delete->getBindings());
    }

    #[DataProvider('driverGrammarProvider')]
    public function testSelectUsesTheConfiguredDriverGrammar(
        DatabaseDriver $driver,
        string $expectedSql,
        string $expectedGrammar,
    ): void {
        $builder = new QueryBuilder($driver);

        self::assertInstanceOf($expectedGrammar, $builder->getGrammar());
        self::assertSame($expectedSql, $builder->table('users')->select('users.id')->limit(3)->toSql());
    }

    public static function driverGrammarProvider(): array {
        return [
            'ansi' => [DatabaseDriver::ANSI, 'SELECT "users"."id" FROM "users" FETCH FIRST 3 ROWS ONLY', ANSIGrammar::class],
            'mysql' => [DatabaseDriver::MYSQL, 'SELECT `users`.`id` FROM `users` LIMIT 3', MySQLGrammar::class],
            'postgresql' => [DatabaseDriver::POSTGRESQL, 'SELECT "users"."id" FROM "users" LIMIT 3', PostgreSQLGrammar::class],
            'sqlite' => [DatabaseDriver::SQLITE, 'SELECT "users"."id" FROM "users" LIMIT 3', SQLiteGrammar::class],
        ];
    }

    public function testAcceptsTypedAndLegacyWhereDefinitions(): void {
        $builder = new QueryBuilder(DatabaseDriver::ANSI);

        $sql = $builder
            ->table('users')
            ->select()
            ->wheres([
                ['status', 'active'],
                ['type' => 'like', 'column' => 'email', 'value' => '%@example.test'],
                ['type' => 'between', 'column' => 'created_at', 'values' => ['2026-01-01', '2026-12-31']],
            ])
            ->toSql();

        self::assertSame(
            'SELECT * FROM "users" WHERE "status" = ? AND "email" LIKE ? AND "created_at" BETWEEN ? AND ?',
            $sql,
        );
        self::assertSame(['active', '%@example.test', '2026-01-01', '2026-12-31'], $builder->getBindings());
    }

    public function testRequiresTableAndQueryTypeBeforeBuildingSql(): void {
        $builder = new QueryBuilder();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Table not specified');

        $builder->toSql();
    }

    public function testRequiresQueryTypeAfterTableIsSet(): void {
        $builder = new QueryBuilder();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Query type not specified');

        $builder->table('users')->toSql();
    }
}