<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NetIncome extends Notification
{
    use Queueable;

    protected $data;

    /**
     * Create a new notification instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
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
            ->subject('Laporan Total Income dan Detail Pemasukan (Per Jam)')
            ->greeting('Halo Admin,')
            ->line('Berikut adalah laporan Rincian Pendapatan ditiap tiap loket dan total Net Income:')
            ->line('Total Pendapatan di Loket Resto Rp ' . number_format($this->data['resto'], 0, ',', '.'))
            ->line('Total Pendapatan di Loket Parkir Rp ' . number_format($this->data['parking'], 0, ',', '.'))
            ->line('Total Pendapatan di Loket Tiket Rp ' . number_format($this->data['ticket'], 0, ',', '.'))
            ->line('Total Pendapatan di Loket Wahana Rp ' . number_format($this->data['wahana'], 0, ',', '.'))
            ->line('Total Pendapatan di Loket Toilet Rp ' . number_format($this->data['toilet'], 0, ',', '.'))
            ->line('Total Pendapatan di Loket Bantuan Rp ' . number_format($this->data['bantuan'], 0, ',', '.'))
            ->line('Total Pengeluaran Rp ' . number_format($this->data['expanse'], 0, ',', '.'))

            ->line('Total Net Income Rp ' . number_format($this->data['total'], 0, ',', '.'))

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
