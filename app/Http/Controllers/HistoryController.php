<?php

namespace App\Http\Controllers;

use App\Service\Historys;

class HistoryController extends Controller {
    private $historyService;

    public function __construct(Historys $historyService)
    {
        $this->historyService = $historyService;
    }

    public function history()
    {
        return $this->historyService->get();
    }
}