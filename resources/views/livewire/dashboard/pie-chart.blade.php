<?php

use Livewire\Volt\Component;

new class extends Component {

    public array $barChart = [
        'type' => 'doughnut',
        'options' => [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'position' => 'left',
                    'labels' => [
                        'usePointStyle' => true,
                    ],
                ],
            ],
        ],
        'data' => [
            'labels' => ['Laravel', 'Codeigniter', 'Symfony', 'Yii'],
            'datasets' => [
                [
                    'label' => ' # Users',
                    'data' => [1234, 890, 454, 98],
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
        <div class="h-full flex gap-3 justify-center bg-indigo-50 dark:bg-gray-800 dark:text-white p-20 rounded-lg">
            <x-loading class="text-primary loading-dots" /> Loading...
        </div>
        HTML;
    }

}; ?>

<div>

    <x-chart wire:model="barChart" class="w-full h-[250px]" />

</div>
