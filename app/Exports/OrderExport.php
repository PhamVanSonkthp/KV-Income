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
        $query = Order::select('id', 'code', 'created_at', 'orders.user_id', 'orders.service_charge', 'orders.tips', 'orders.payment_type_id', 'orders.deposit', 'note');
        if(request('begin')){
            $query = $query->whereDate('created_at', '>=', request('begin'));
        }

        if(request('end')){
            $query = $query->whereDate('created_at', '<=', request('end'));
        }
        $order = $query->latest()->get();

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
            optional($row->payment)->name,
            number_format($row->deposit),
            $row->note
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
            'Payment',
            'Deposit',
            'Note'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('A1:J1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ]
                ]);

                $event->sheet->setAutoFilter('A1:J1');
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
