<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PengajuanRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $pengajuan;
    public $approver;
    public $catatan;

    /**
     * Create a new notification instance.
     */
    public function __construct($pengajuan, $approver = null, $catatan = null)
    {
        $this->pengajuan = $pengajuan;
        $this->approver = $approver;
        $this->catatan = $catatan;
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
        $url = url('/app/detail-peminjaman-ruangan/' . $this->pengajuan->id);

        return (new MailMessage)
                    ->subject('Pengajuan Peminjaman Ditolak - ' . $this->pengajuan->no_pengajuan)
                    ->greeting('Halo, ' . $notifiable->name)
                    ->line('Mohon maaf, pengajuan peminjaman ruangan Anda telah ditolak.')
                    ->line('**Detail Pengajuan:**')
                    ->line('No. Pengajuan: ' . $this->pengajuan->no_pengajuan)
                    ->line('Tipe: ' . $this->pengajuan->tipe_pengajuan)
                    ->line('**Alasan Penolakan:**')
                    ->line($this->catatan ?? '-')
                    ->action('Lihat Detail Pengajuan', $url)
                    ->line('Silakan ajukan kembali dengan jadwal atau ruangan yang berbeda.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Peminjaman Ditolak: ' . $this->pengajuan->no_pengajuan,
            'message' => 'Pengajuan peminjaman ruangan Anda telah ditolak dengan alasan: ' . ($this->catatan ?? '-'),
            'type' => 'pengajuan',
            'pengajuan_id' => $this->pengajuan->id,
            'link' => '/app/detail-peminjaman-ruangan/' . $this->pengajuan->id,
            'action_label' => 'Lihat Detail'
        ];
    }
}
