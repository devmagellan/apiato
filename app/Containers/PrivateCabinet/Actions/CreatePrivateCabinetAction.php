<?php

namespace App\Containers\PrivateCabinet\Actions;

use App\Ship\Parents\Actions\Action;
use App\Ship\Parents\Requests\Request;
use Apiato\Core\Foundation\Facades\Apiato;

class CreatePrivateCabinetAction extends Action
{
    public function run(Request $request)
    {
        $data = $request->sanitizeInput([
            // add your request data here
        ]);

        $privatecabinet = Apiato::call('PrivateCabinet@CreatePrivateCabinetTask', [$data]);

        return $privatecabinet;
    }
}
