<?php

namespace App\Exports;

use App\Models\Ticket;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TicketsExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithStyles,
    WithTitle
{
    public function __construct(private readonly Builder $query) {}

    public function query(): Builder
    {
        return $this->query->with(['store', 'handler'])->clone();
    }

    public function headings(): array
    {
        return [
            'No. Ticket',
            'Toko',
            'Diajukan Oleh',
            'Jenis',
            'Status',
            'Handler',
            'Dibuat',
            'Diselesaikan',
        ];
    }

    public function map($ticket): array
    {
        return [
            $ticket->ticket_number,
            $ticket->store->name ?? '-',
            $ticket->submitted_by,
            config('ajuin.ticket_types')[$ticket->type] ?? $ticket->type,
            config('ajuin.statuses')[$ticket->status] ?? $ticket->status,
            $ticket->handler?->name ?? '-',
            $ticket->created_at?->format('d/m/Y H:i'),
            $ticket->resolved_at?->format('d/m/Y H:i') ?? '-',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF6366F1']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function title(): string
    {
        return 'Tickets';
    }
}
