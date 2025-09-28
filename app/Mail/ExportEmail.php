<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ExportEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $filePath;
    public $fileName;

    public function __construct($filePath, $fileName)
    {
        $this->filePath = $filePath;
        $this->fileName = $fileName;
    }

    public function build()
    {
        return $this->subject('Export Data Tiket Pendapatan Kalimas')
                    ->view('emails.export')
                    ->attach($this->filePath, [
                        'as' => $this->fileName,
                    ]);
    }
}
