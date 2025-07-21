<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ExportEmail extends Mailable
{
    use Queueable, SerializesModels;

    public string $filePath;
    public string $fileName;

    public function __construct(string $filePath, string $fileName)
    {
        $this->filePath = $filePath;
        $this->fileName = $fileName;
    }

    public function build()
    {
        return $this->subject('Laporan Export')
                    ->view('emails.export') // buat view sederhana
                    ->attach($this->filePath, [
                        'as' => $this->fileName,
                    ]);
    }
}
