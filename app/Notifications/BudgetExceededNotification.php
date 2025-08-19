<?php

namespace App\Notifications;

use App\Models\Budget;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BudgetExceededNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Budget $budget, public float $spent)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
        // return ['mail', 'database'];
    }

    /*
     * Get the mail representation of the notification.
     
        public function toMail(object $notifiable): MailMessage
        {
            return (new MailMessage)
                ->subject('Budget Exceeded')
                ->line("Your budget for {$this->budget->category->name} has been exceeded.")
                ->line("Budget: {$this->budget->amount}")
                ->line("Spent: {$this->spent}")
                ->action('View Transactions', url('/admin/transactions'));
        }
    */

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'category' => $this->budget->category->name,
            'budget_amount' => $this->budget->amount,
            'spent' => $this->spent,
        ];
    }
}
