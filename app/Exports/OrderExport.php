<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class OrderExport implements FromCollection, WithMapping, WithHeadings, WithEvents, WithColumnFormatting, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $order = Order::select('id', 'code', 'created_at', 'orders.user_id', 'orders.service_charge', 'orders.tips')->latest()->get();
        return $order;
    }

    function map($row): array
    {
        return [
            $row->id,
            $row->code,
            date('m/d/Y H:i:s', strtotime($row->created_at)),
            optional(optional($row->user)->branch)->name,
            optional($row->user)->name,
            number_format($row->service_charge),
            number_format($row->tips),
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Bill code',
            'Created time',
            'Branch',
            'Staff',
            'Service chage',
            'Tips',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('A1:G1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ]
                ]);

                $event->sheet->setAutoFilter('A1:G1');
            }
        ];
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }
}
