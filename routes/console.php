<?php

use Illuminate\Support\Facades\Schedule;

Schedule::call(fn()=>app(\App\Services\GutResetService::class)->processDailyWorkflows())
    ->name('gutreset-daily-workflow')
    ->dailyAt(env('GUTRESET_DAILY_WORKFLOW_TIME','09:00'))
    ->withoutOverlapping();

Schedule::command('payments:reconcile')
    ->name('payments-reconcile')
    ->hourly()
    ->withoutOverlapping();
