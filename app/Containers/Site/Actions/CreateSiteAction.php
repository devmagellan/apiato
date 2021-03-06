<?php

namespace App\Containers\Site\Actions;

use App\Ship\Parents\Actions\Action;
use App\Ship\Parents\Requests\Request;
use Apiato\Core\Foundation\Facades\Apiato;

class CreateSiteAction extends Action
{
    public function run(Request $request)
    {
        $data = $request->sanitizeInput([
            // add your request data here
        ]);

        $site = Apiato::call('Site@CreateSiteTask', [$data]);

        return $site;
    }
}
