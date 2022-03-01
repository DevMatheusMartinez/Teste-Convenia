<?php

namespace App\Jobs;


use App\Imports\EmployeeImport;
use App\Mail\SendMailUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Excel;

class EmployeerImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $file;

    public function __construct($file)
    {
        $this->file = $file;
    }

    public function handle(): void
    {
        $import = new EmployeeImport();
        $import->import($this->file, null, Excel::CSV);
        $importReport = $import->getImportReport();
        $errorsReport = $import->getErrorsReport();
        $userLogged = auth()->user();

        $post = new SendMailUser($userLogged, $importReport, $errorsReport);
        Mail::to($userLogged->email)->send($post);
    }
}
