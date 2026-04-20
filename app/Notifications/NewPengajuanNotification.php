<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewPengajuanNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $pengajuan;
    protected $sender;

    /**
     * Create a new notification instance.
     */
    public function __construct($pengajuan, $sender)
    {
        $this->pengajuan = $pengajuan;
        $this->sender = $sender;
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
        
        // Memastikan email bersih dari spasi atau karakter newline
        $email = trim($notifiable->email);

        return (new MailMessage)
                    ->subject('Pengajuan Peminjaman Ruangan Baru - ' . $this->pengajuan->no_pengajuan)
                    ->greeting('Halo, ' . $notifiable->name)
                    ->line('Ada pengajuan peminjaman ruangan baru yang memerlukan verifikasi Anda.')
                    ->line('**Detail Pengajuan:**')
                    ->line('No. Pengajuan: ' . $this->pengajuan->no_pengajuan)
                    ->line('Pengaju: ' . $this->sender->name)
                    ->line('Tipe: ' . $this->pengajuan->tipe_pengajuan)
                    ->line('Alasan: ' . $this->pengajuan->alasan)
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
            'title' => 'Pengajuan Baru: ' . $this->pengajuan->no_pengajuan,
            'message' => 'User ' . $this->sender->name . ' membuat pengajuan peminjaman ruangan tipe ' . $this->pengajuan->tipe_pengajuan,
            'type' => 'pengajuan',
            'pengajuan_id' => $this->pengajuan->id,
            'link' => '/app/detail-peminjaman-ruangan/' . $this->pengajuan->id,
            'action_label' => 'Verifikasi Sekarang'
        ];
    }
}
