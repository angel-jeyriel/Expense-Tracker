<?php

use Filament\Notifications\Notification as FilamentNotification;

FilamentNotification::make()
    ->title('Budget Exceeded')
    ->body("Your {$budget->category->name} budget was exceeded: spent {$spent}, limit {$budget->amount}.")
    ->danger()
    ->send();
