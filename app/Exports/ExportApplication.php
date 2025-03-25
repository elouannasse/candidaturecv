<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ExportApplication implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize, WithCustomStartCell, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Join user_offre with users and offres tables to get meaningful data
        return DB::table('user_offre')
            ->join('users', 'user_offre.user_id', '=', 'users.id')
            ->join('offres', 'user_offre.offre_id', '=', 'offres.id')
            ->select(
                'user_offre.id',
                'users.name as user_name',
                'users.email as user_email',
                'offres.title as offre_title',
                'user_offre.cv',
                'user_offre.created_at',
                'user_offre.updated_at'
            )
            ->get();
    }

    public function map($application): array
    {
        return [
            $application->id,
            $application->user_name ?? 'N/A',
            $application->user_email ?? 'N/A',
            $application->offre_title ?? 'N/A',
            $application->cv,
            is_string($application->created_at) ? $application->created_at :
                (is_object($application->created_at) ? $application->created_at->format('Y-m-d H:i:s') : 'N/A'),
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Applicant Name',
            'Applicant Email',
            'Job Title',
            'CV Details',
            'Application Date'
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
                $event->sheet->getColumnDimension('A')->setWidth(15);
                $event->sheet->getColumnDimension('B')->setWidth(30);
                $event->sheet->getColumnDimension('C')->setWidth(35);
                $event->sheet->getColumnDimension('D')->setWidth(40);
                $event->sheet->getColumnDimension('E')->setWidth(50);
                $event->sheet->getColumnDimension('F')->setWidth(25);
            },
        ];
    }
}
