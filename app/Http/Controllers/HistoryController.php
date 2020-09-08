<?php

namespace App\Http\Controllers;

use App\Service\Histories;

class HistoryController extends Controller {
    private $Historieservice;

    public function __construct(Histories $Historieservice)
    {
        $this->Historieservice = $Historieservice;
    }

    public function history()
    {
        return $this->Historieservice->get();
    }
}