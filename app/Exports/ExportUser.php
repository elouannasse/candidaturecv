<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\DB;

class ExportUser implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize, WithCustomStartCell, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $users = User::all();

        if ($users->isEmpty()) {

            return collect([
                (object)[
                    'id' => 'No data',
                    'name' => 'No users found',
                    'email' => 'Please check database',
                    'created_at' => now()
                ]
            ]);
        }

        return $users;
    }

    public function map($user): array
    {
        return [
            $user->id,
            $user->name,
            $user->email,
            is_string($user->created_at) ? $user->created_at :
                (is_object($user->created_at) ? $user->created_at->format('Y-m-d H:i:s') : 'N/A')
        ];
    }

    public function headings(): array
    {
        return [
            '#',
            'Name',
            'Email',
            'Created Date'
        ];
    }

    public function startCell(): string
    {
        return 'A4'; // Start data from row 4
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Add the current date/time in the format you specified
                $currentDateTime = now()->format('Y-m-d H:i:s');
                $event->sheet->setCellValue('A1', 'Current Date and Time (UTC - YYYY-MM-DD HH:MM:SS formatted): ' . $currentDateTime);
                $event->sheet->setCellValue('A2', 'Current User\'s Login: LJAMINE');

                // Make these cells bold
                $event->sheet->getStyle('A1:A2')->getFont()->setBold(true);

                // Set column width for the header (making it wider to fit the text)
                $event->sheet->getColumnDimension('A')->setWidth(60);
                $event->sheet->getColumnDimension('B')->setWidth(20);
                $event->sheet->getColumnDimension('C')->setWidth(30);
                $event->sheet->getColumnDimension('D')->setWidth(25);
            },
        ];
    }
}
