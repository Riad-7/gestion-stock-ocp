<?php

namespace App\Console\Commands;

use App\Support\ActionLogger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use RuntimeException;

class BackupDatabase extends Command
{
    protected $signature = 'backup:database {--initiator=}';

    protected $description = 'Create a database backup in storage/app/backups';

    public function handle(): int
    {
        $driver = config('database.default');
        $backupDirectory = storage_path('app/backups');

        File::ensureDirectoryExists($backupDirectory);

        $timestamp = now()->format('Y-m-d_H-i-s');

        try {
            $backupPath = match ($driver) {
                'sqlite' => $this->backupSqlite($backupDirectory, $timestamp),
                'mysql' => $this->backupMysql($backupDirectory, $timestamp),
                default => throw new RuntimeException("Backup driver non supporte: {$driver}"),
            };

            ActionLogger::log(
                'backup.database',
                'Backup de la base de donnees genere.',
                null,
                ['path' => $backupPath, 'driver' => $driver],
                null,
                $this->option('initiator') ? (int) $this->option('initiator') : null,
            );

            $this->info("Backup termine: {$backupPath}");

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Backup failed: '.$e->getMessage());

            return self::FAILURE;
        }
    }

    private function backupSqlite(string $backupDirectory, string $timestamp): string
    {
        $databasePath = config('database.connections.sqlite.database');

        if (! $databasePath || ! File::exists($databasePath)) {
            throw new RuntimeException('Fichier SQLite introuvable.');
        }

        $backupPath = $backupDirectory.DIRECTORY_SEPARATOR."backup_{$timestamp}.sqlite";

        File::copy($databasePath, $backupPath);

        return $backupPath;
    }

    private function backupMysql(string $backupDirectory, string $timestamp): string
    {
        $connection = config('database.connections.mysql');
        $database = $connection['database'] ?? null;

        if (! $database) {
            throw new RuntimeException('Base MySQL non configuree.');
        }

        $dumpBinary = $this->resolveMysqlDumpBinary();
        $backupPath = $backupDirectory.DIRECTORY_SEPARATOR."backup_{$timestamp}.sql";

        $command = array_values(array_filter([
            $dumpBinary,
            '--host='.$connection['host'],
            '--port='.(string) $connection['port'],
            '--user='.$connection['username'],
            filled($connection['password'] ?? null) ? '--password='.$connection['password'] : null,
            '--result-file='.$backupPath,
            '--skip-comments',
            '--single-transaction',
            $database,
        ]));

        $result = Process::timeout(120)->run($command);

        if ($result->failed()) {
            throw new RuntimeException(trim($result->errorOutput()) ?: 'mysqldump a echoue.');
        }

        return $backupPath;
    }

    private function resolveMysqlDumpBinary(): string
    {
        $candidates = array_filter([
            env('DB_DUMP_BINARY'),
            'C:\\xampp\\mysql\\bin\\mysqldump.exe',
            'C:\\laragon\\bin\\mysql\\mysql-8.0.30-winx64\\bin\\mysqldump.exe',
            'mysqldump',
        ]);

        foreach ($candidates as $candidate) {
            if ($candidate === 'mysqldump' || File::exists($candidate)) {
                return $candidate;
            }
        }

        throw new RuntimeException('mysqldump introuvable. Configure DB_DUMP_BINARY dans .env.');
    }
}
