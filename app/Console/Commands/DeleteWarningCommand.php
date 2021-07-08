<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Mail;
use Carbon\Carbon;
use App\Models\User;

class DeleteWarningCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:warning';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command sends warning to deactivated users';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $emails = User::where("is_active", 0)->whereDate("updated_at","=", Carbon::now()->subDays(25)->format('Y-m-d'))->pluck('email')->toArray();
        if(sizeof($emails) > 0){
        Mail::send('warningMail', [], function($message) use ($emails)
        {    
              $message->to($emails)->subject('Upozorenje na brisanje profila')->from('no-reply@vebcentar.me', 'Cv priča');    
         });

        }
         return response()->json(['success' => true, 'messages' => "Mail sent"], 200);   
    }
}
