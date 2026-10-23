<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PDO;
use PDOException;

class CheckDatabaseConnection extends Command
{
    protected $signature = 'db:check {--database= : Specify database connection}';

    protected $description = 'Safely verify database connection and SSL status without exposing secrets';

    public function handle(): int
    {
        $connectionName = $this->option('database') ?: config('database.default');
        $config = config("database.connections.{$connectionName}");

        if (! $config) {
            $this->error("Connection [{$connectionName}] is not configured.");

            return self::FAILURE;
        }

        $this->info("Checking connection: [{$connectionName}]");
        $this->line('Driver: '.($config['driver'] ?? 'unknown'));
        $this->line('Host: '.($config['host'] ?? 'N/A'));
        $this->line('Port: '.($config['port'] ?? 'N/A'));
        $this->line('Database: '.($config['database'] ?? 'N/A'));
        $this->line('Username: '.($config['username'] ?? 'N/A'));
        $this->line('Password: '.(! empty($config['password']) ? '[SET (length: '.strlen($config['password']).')]' : '[EMPTY]'));
        $sslCa = $config['options'][PDO::MYSQL_ATTR_SSL_CA ?? 1007] ?? null;
        $this->line('SSL CA: '.(! empty($sslCa) ? $sslCa : '[NOT SPECIFIED]'));

        if (empty($config['password'])) {
            $this->warn('Warning: Database password is empty.');
        }

        try {
            $this->line('Connecting...');
            $pdo = DB::connection($connectionName)->getPdo();
            $this->info('✓ Connection established successfully!');

            // 1. Basic query check
            $version = $pdo->query('SELECT VERSION()')->fetchColumn();
            $currentUser = $pdo->query('SELECT CURRENT_USER()')->fetchColumn();
            $this->line("MySQL Version: {$version}");
            $this->line("Current User: {$currentUser}");

            // 2. SSL verification query
            $sslStmt = $pdo->query("SHOW STATUS LIKE 'Ssl_cipher'");
            $sslRow = $sslStmt ? $sslStmt->fetch(PDO::FETCH_ASSOC) : null;
            $sslCipher = $sslRow['Value'] ?? 'None';
            if (! empty($sslCipher) && $sslCipher !== 'None') {
                $this->info("✓ SSL/TLS Encryption Active (Cipher: {$sslCipher})");
            } else {
                $this->warn('! Connection does not use SSL encryption');
            }

            // 3. Inspect existing tables in database
            $tables = DB::connection($connectionName)->select('SHOW TABLES');
            $tableCount = count($tables);
            $this->line("Total existing tables in [{$config['database']}]: {$tableCount}");

            if ($tableCount > 0) {
                $tableNames = [];
                foreach ($tables as $table) {
                    $prop = 'Tables_in_'.$config['database'];
                    $tableNames[] = $table->$prop ?? array_values((array) $table)[0];
                }
                $this->line('Tables: '.implode(', ', array_slice($tableNames, 0, 15)).($tableCount > 15 ? '...' : ''));

                if (in_array('migrations', $tableNames)) {
                    $migCount = DB::connection($connectionName)->table('migrations')->count();
                    $this->info("✓ Migration table found with {$migCount} applied migrations.");
                } else {
                    $this->line('No migrations table found.');
                }
            } else {
                $this->line('Database is empty (no tables found).');
            }

            return self::SUCCESS;
        } catch (PDOException $e) {
            $this->error('Connection failed (PDOException):');
            $this->error($e->getMessage());

            return self::FAILURE;
        } catch (\Throwable $e) {
            $this->error('Connection failed (Exception):');
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }
}
