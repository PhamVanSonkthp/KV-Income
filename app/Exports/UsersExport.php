<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;


class UsersExport implements FromCollection, WithMapping, WithHeadings, WithEvents, WithColumnFormatting, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */

    public function collection()
    {
        $user = User::select('id', 'name', 'code', 'created_at', 'branch_id')->where('is_admin', 0)->latest()->get();
        return $user;
    }

    function map($row): array
    {
        return [
            $row->id,
            $row->name,
            $row->code,
            date('m/d/Y H:i:s', strtotime($row->created_at)),
            optional($row->branch)->name,
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Code',
            'Created time',
            'Branch',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('A1:E1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ]
                ]);

                $event->sheet->setAutoFilter('A1:E1');
            }
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }
}
