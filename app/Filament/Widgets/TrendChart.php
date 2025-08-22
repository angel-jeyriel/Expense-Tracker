<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class TrendChart extends ChartWidget
{
    protected int | string | array $columnSpan = [
        // 'default' => 1,
        'xs' => 2,
        'sm' => 2,
        'md' => 2,
        'lg' => 2,
        'xl' => 1,
    ];

    protected static ?string $heading = 'Recent Transactions';

    protected function getData(): array
    {
        $userId = auth()->id();

        // Sum of recent transactions
        $sumData = Trend::query(
            Transaction::query()->where('user_id', $userId)
        )
            ->between(
                start: now()->subDays(6)->startOfDay(),
                end: now()->endOfDay(),
            )
            ->perDay()
            ->sum('amount');

        return [
            'datasets' => [
                [
                    'label' => 'Total Expenses',
                    'data' => $sumData->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => '#f97316',
                    'backgroundColor' => 'rgba(79,70,229,0.2)',
                ],
            ],
            'labels' => $sumData->map(
                fn (TrendValue $value) =>
                Carbon::parse($value->date)->format('D')
            ),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
