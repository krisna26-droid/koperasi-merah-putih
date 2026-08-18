<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Services\AccountingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AkuntansiLanjutanController extends Controller
{
    protected AccountingService $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    public function neracaSaldo(Request $request): View
    {
        $endDate = $request->get('end_date', Carbon::now()->toDateString());
        $data = $this->accountingService->getTrialBalance($endDate);

        return view('pengurus.akuntansi.neraca-saldo', compact('data', 'endDate'));
    }

    public function labaRugi(Request $request): View
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfYear()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());
        $data = $this->accountingService->getIncomeStatement($startDate, $endDate);

        return view('pengurus.akuntansi.laba-rugi', compact('data', 'startDate', 'endDate'));
    }
}