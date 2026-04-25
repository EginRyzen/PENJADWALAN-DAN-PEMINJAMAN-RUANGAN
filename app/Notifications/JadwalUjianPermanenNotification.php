<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class JadwalUjianPermanenNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $dosen;
    protected $jadwalList;
    protected $tipe;

    public function __construct($dosen, $jadwalList, string $tipe)
    {
        $this->dosen      = $dosen;
        $this->jadwalList = $jadwalList;
        $this->tipe       = strtoupper($tipe);
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject("Jadwal {$this->tipe} Telah Dikonfirmasi — {$this->dosen->nama}")
            ->greeting("Halo, {$notifiable->name}")
            ->line("Jadwal ujian **{$this->tipe}** telah ditetapkan secara permanen. Berikut jadwal pengawasan Anda:");

        foreach ($this->jadwalList as $index => $j) {
            $no      = $index + 1;
            $tanggal = $j->tanggal
                ? \Carbon\Carbon::parse($j->tanggal)->locale('id')->isoFormat('dddd, D MMMM YYYY')
                : '-';
            $mail->line("**{$no}. {$j->mataKuliah->nama}** (Kelas {$j->kelas->nama_kelas})");
            $mail->line("📅 {$tanggal} | ⏰ {$j->jam_mulai} – {$j->jam_selesai} | 🏫 {$j->ruangan->room_name}");
        }

        $mail->line('---')
             ->line('Jadwal ini bersifat **FINAL** dan tidak dapat diubah.')
             ->line('Terima kasih atas perhatian Anda.');

        return $mail;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title'   => "Jadwal {$this->tipe} Sudah Dikonfirmasi",
            'message' => "Jadwal ujian {$this->tipe} untuk {$this->jadwalList->count()} mata kuliah telah ditetapkan permanen.",
            'type'    => 'jadwal_ujian',
            'link'    => '/app/penjadwalan',
            'action_label' => 'Lihat Jadwal',
        ];
    }
}
