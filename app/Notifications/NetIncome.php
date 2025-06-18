<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NetIncome extends Notification
{
    use Queueable;

    protected $total;

    /**
     * Create a new notification instance.
     */
    public function __construct($total)
    {
        $this->total = $total;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Laporan Total Income (Per Jam)')
            ->greeting('Halo Admin,')
            ->line('Berikut adalah total income terbaru:')
            ->line('💰 Rp ' . number_format($this->total, 0, ',', '.'))
            ->line('Laporan ini dikirim otomatis setiap 1 jam.')
            ->salutation('Terima kasih 🙏');
    }
    

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
