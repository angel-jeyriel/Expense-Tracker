<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;

use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class TrendChart extends ChartWidget
{
    protected static ?string $heading = 'Recent Transactions';

    protected function getData(): array
    {

        $userId = auth()->id();

        $data = Trend::model(Transaction::class)
            ->between(
                start: now()->startOfWeek(),
                end: now()->endOfWeek(),
            )
            ->perDay()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Expenses',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }



    // protected static ?string $heading = 'Transactions';

    // protected static string $color = 'gray';

    // protected function getData(): array
    // {
    //     $userId = auth()->id();

    //     $data =
    //         Transaction::query()
    //         ->selectRaw('category_id, SUM(amount) as total_amount')
    //         ->where('user_id', $userId)
    //         ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
    //         ->groupBy('category_id')
    //         ->with('category')  //relationship
    //         ->get();

    //     $labels = $data->map(fn ($t) => $t->category->name ?? 'Unknown')->toArray();
    //     $values = $data->pluck('total_amount')->toArray();

    //     $colors = collect($labels)->map(fn ($label, $i) => $this->getColorForIndex($i))->toArray();

    //     return [
    //         'datasets' => [
    //             [
    //                 'label' => 'Expenses',
    //                 'data' => $values,
    //                 'backgroundColor' => $colors,
    //                 'borderColor' => $colors,
    //             ],
    //         ],
    //         'labels' => $labels,
    //     ];
    // }

    protected function getType(): string
    {
        return 'line';
    }
}
