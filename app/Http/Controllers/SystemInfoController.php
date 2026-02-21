<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class SystemInfoController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewSystemInformation', User::class);

        return view('system.system-info', [
            'databaseSize' => $this->getDatabaseSizeInBytes(),
        ]);
    }

    private function getDatabaseSizeInBytes(): int
    {
        $databaseName = DB::getDatabaseName();
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            $size = file_exists($databaseName) ? filesize($databaseName) : 0;
            return $size === false ? 0 : $size;
        }

        /** @var object{size: numeric-string} $result */
        $result = match ($driver) {
            'pgsql' => DB::selectOne('SELECT pg_database_size(?) AS size', [$databaseName]),
            'sqlsrv' => DB::selectOne('SELECT SUM(size) * 8 * 1024 AS size FROM sys.database_files'), // uses database of current connection
            default => DB::selectOne('SELECT SUM(data_length + index_length) AS size FROM information_schema.TABLES WHERE table_schema = ?', [$databaseName]),
        };
        return (int) $result->size;
    }
}
