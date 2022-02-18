<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\Guardian;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use App\Notifications\SendPasswordNotification;
use Illuminate\Support\Facades\App;

class GuardiansImport implements ToModel, WithStartRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Check for required fields
        if(empty($row[0]) || empty($row[2]) || empty($row[3])) return null;

        try {
            
            /** @var Student */
            $student = Student::findOrFail($row[0]);

            DB::transaction(function() use($student, $row){

                /** @var Guardian */
                $guardian = Guardian::create([
                    'location' => $row[5],
                    'profession' => $row[6]
                ]);
    
                /** @var User */
                $user = $guardian->auth()->updateOrCreate([
                        'phone' => $row[3]
                    ],[
                        'name' => $row[2],
                        'email' => $row[4],
                        'password' => Hash::make($password = Str::random(6))
                    ]
                );

                // Associate student a guardian
                $student->guardians()->attach($guardian, [
                    'primary' => true
                ]);
    
                // Sending email verification link to the user
                if(!empty($user->email)) $user->sendEmailVerificationNotification();
                
                if (App::environment('local')) {

                    Log::debug(['phone' => $user->phone, 'password' => $password]);

                }else{
                    $user->notifyNow(new SendPasswordNotification($password));
                }

            });

            return null;

        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), ['action' => __METHOD__]);
            
            return null;
        }
    }

    /**
     * The guardians import start from the defined row
     * 
     * {@inheritdoc}
     */
    public function startRow(): int
    {
        return 2;
    }
}
