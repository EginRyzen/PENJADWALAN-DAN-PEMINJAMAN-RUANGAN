<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PengajuanCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $pengajuan;

    /**
     * Create a new notification instance.
     */
    public function __construct($pengajuan)
    {
        $this->pengajuan = $pengajuan;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = url('/app/list-pengajuan/detail/' . $this->pengajuan->id);

        return (new MailMessage)
                    ->subject('Pengajuan Peminjaman Selesai - ' . $this->pengajuan->no_pengajuan)
                    ->greeting('Halo, ' . $notifiable->name)
                    ->line('Peminjaman ruangan Anda sudah berstatus COMPLETED dan siap digunakan.')
                    ->line('**Detail Pengajuan:**')
                    ->line('No. Pengajuan: ' . $this->pengajuan->no_pengajuan)
                    ->line('Tipe: ' . $this->pengajuan->tipe_pengajuan)
                    ->action('Lihat Detail Pengajuan', $url)
                    ->line('Terima kasih telah menggunakan aplikasi kami!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Peminjaman Disetujui: ' . $this->pengajuan->no_pengajuan,
            'message' => 'Peminjaman ruangan Anda sudah berstatus COMPLETED dan siap digunakan.',
            'type' => 'pengajuan',
            'pengajuan_id' => $this->pengajuan->id,
            'link' => '/app/list-pengajuan/detail/' . $this->pengajuan->id,
            'action_label' => 'Lihat Detail'
        ];
    }
}
