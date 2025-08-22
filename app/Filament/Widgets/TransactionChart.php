<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;

class TransactionChart extends ChartWidget
{
    protected int | string | array $columnSpan = [
        // 'default' => 1,
        'xs' => 2,
        'sm' => 2,
        'md' => 2,
        'lg' => 2,
        'xl' => 1,
    ];

    protected static ?string $heading = 'Transaction Categories';

    protected static string $color = 'gray';

    protected function getData(): array
    {
        $userId = auth()->id();

        $data =
            Transaction::query()
            ->selectRaw('category_id, SUM(amount) as total_amount')
            ->where('user_id', $userId)
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->groupBy('category_id')
            ->with('category')
            ->get();

        $labels = $data->map(fn ($t) => $t->category->name ?? 'Unknown')->toArray();
        $values = $data->pluck('total_amount')->toArray();

        $colors = collect($labels)->map(fn ($label, $i) => $this->getColorForIndex($i))->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Expenses',
                    'data' => $values,
                    'backgroundColor' => $colors,
                    'borderColor' => $colors,
                ],
            ],
            'labels' => $labels,
        ];
    }

    /**
     * Generate a consistent color for each index
     */
    private function getColorForIndex(int $index): string
    {
        $palette = [
            '#3b82f6', // blue
            '#ef4444', // red
            '#10b981', // green
            '#f59e0b', // amber
            '#8b5cf6', // violet
            '#ec4899', // pink
            '#14b8a6', // teal
            '#f97316', // orange
        ];

        return $palette[$index % count($palette)];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
