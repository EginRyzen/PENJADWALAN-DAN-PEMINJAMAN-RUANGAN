<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PengajuanRevisionNotification extends Notification implements ShouldQueue
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
                    ->subject('Perbaikan Pengajuan Peminjaman - ' . $this->pengajuan->no_pengajuan)
                    ->greeting('Halo, ' . $notifiable->name)
                    ->line('Pengajuan peminjaman ruangan Anda memerlukan perbaikan (Koreksi).')
                    ->line('**Detail Pengajuan:**')
                    ->line('No. Pengajuan: ' . $this->pengajuan->no_pengajuan)
                    ->line('Tipe: ' . $this->pengajuan->tipe_pengajuan)
                    ->line('**Catatan Koreksi dari ' . ($this->approver->name ?? 'Approver') . ':**')
                    ->line($this->catatan ?? '-')
                    ->action('Lihat & Edit Pengajuan', $url)
                    ->line('Silakan lakukan perbaikan dan ajukan kembali.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Perbaikan Diperlukan: ' . $this->pengajuan->no_pengajuan,
            'message' => 'Pengajuan Anda memerlukan koreksi: ' . ($this->catatan ?? '-'),
            'type' => 'pengajuan',
            'pengajuan_id' => $this->pengajuan->id,
            'link' => '/app/detail-peminjaman-ruangan/' . $this->pengajuan->id,
            'action_label' => 'Edit Pengajuan'
        ];
    }
}
