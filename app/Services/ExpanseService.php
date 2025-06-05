<?php

namespace App\Services;

use App\Models\Expanse_Operasional;
use App\Models\Expanse_Mendadak;
use App\Models\TotalIncome;
use App\Models\TotalExpanse;

class ExpanseService
{
    public static function syncIncomeAndExpanse($userId, $mendadak, $operasional)
    {
        
        
        $income_id = TotalIncome::whereDate('created_at', now()->toDateString())->first();
        $total = $mendadak + $operasional;

        if(!$income_id)
        {
           $income_id =  TotalIncome::create([
                'user_id' => $userId,
                'total_parking_details' => 0,
                'total_ticket_details' => 0,
                'total_bantuan_details' => 0,
                'total_resto_details' => 0,
                'total_toilet_details' => 0,
                'total_wahana_details' => 0,
                'total_amount' => 0,
            ]);
        };
        
        
        TotalExpanse::updateOrCreate(
            ['created_at' => now()->toDateString()
        ],
            

            [  'total_income_id' => $income_id->id,
                'user_id' => $userId,
                'total_expanse_mendadak' => $mendadak,
                'total_expanse_operasional' => $operasional,
                'total_amount' => $total,
            ]
        );
    }
    }

