<?php namespace Phpjit\SymfonysFacade\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Phpjit\SymfonysFacade\Facades\Commands\SymfonyCommandsFacade;

class ManagerController extends Controller
{
    public function interpreter()
    {
        return view('SymfonysFacade::global', ['response' => '']);
    }

    public function run(Request $request, SymfonyCommandsFacade $sc)
    {
        $response = '';
        $input = $request->all();
        if (isset($input['command'])) {
            $response = $sc->runCommand($input['command']);
        }

        return view('SymfonysFacade::global', ['response' => $response]);
    }
}
