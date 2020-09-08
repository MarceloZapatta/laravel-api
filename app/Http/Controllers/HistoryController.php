<?php

namespace App\Http\Controllers;

use App\Service\Histories;

class HistoryController extends Controller {
    private $historieService;

    public function __construct(Histories $historieService)
    {
        $this->historieService = $historieService;
    }

    /**
     * Get the history of prices
     */
    public function history()
    {
        return $this->historieService->get();
    }
}