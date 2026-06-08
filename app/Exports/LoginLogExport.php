<?php

namespace App\Exports;

use App\Models\LoginLog;
use App\Services\ModalIndexQuey;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Excel;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class LoginLogExport implements FromQuery, Responsable, WithColumnFormatting, WithHeadings, WithMapping
{
    use Exportable;

    /**
     * It's required to define the fileName within
     * the export class when making use of Responsable.
     */
    private $fileName = 'login-history.xlsx';

    /**
     * Optional Writer Type
     */
    private $writerType = Excel::XLSX;

    /**
     * Optional headers
     */
    private $headers = [
        'Content-Type' => 'text/csv',
    ];

    public function __construct(
        public ?array $filters
    ) {}

    public function query()
    {
        // Define the query to fetch the users
        $query = LoginLog::query()->with(['user']);

        return ModalIndexQuey::globalQuery($query);
    }

    /**
     * @param  LoginLog  $LoginLog
     */
    public function map($log): array
    {
        return [
            ''.$log->user->full_name.'<br>'.$log->user->username,
            Carbon::parse($log->created_at)->format('Y-m-d H:i:s').' <br> '.Carbon::parse($log->created_at)->diffForHumans(),
            $log->ip_address,
            $log->location,
            $log->browser.'<br>'.$log->os,
        ];
    }

    public function headings(): array
    {
        return [
            'User',
            'Login At',
            'Ip Address',
            'Location',
            'Browser|OS',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }
}
