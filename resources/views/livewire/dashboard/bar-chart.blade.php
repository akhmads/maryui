<?php

use Livewire\Volt\Component;

new class extends Component {

    public array $barChart = [
        'type' => 'bar',
        'options' => [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => false,
                    'position' => 'right',
                    'labels' => [
                        'usePointStyle' => true,
                    ]
                ],
            ],
        ],
        'data' => [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May','Jun'],
            'datasets' => [
                [
                    'label' => ' # Order',
                    'data' => [12, 19, 3, 17, 32, 6],
                ],
            ],
        ],
    ];

    public function with(): array
    {
        return [];
    }

    public function placeholder()
    {
        return <<<'HTML'
        <div class="h-full flex gap-3 justify-center bg-indigo-50 p-20 rounded-lg">
            <x-loading class="text-primary loading-dots" /> Loading...
        </div>
        HTML;
    }

}; ?>

<div>

    <x-chart wire:model="barChart" class="w-full h-[250px]" />

</div>
