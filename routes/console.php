<?php

use Illuminate\Support\Facades\Schedule;

Schedule::call(fn()=>app(\App\Services\GutResetWhatsAppService::class)->processDailyWorkflows())
    ->name('gutreset-daily-workflow')
    ->dailyAt(env('GUTRESET_DAILY_WORKFLOW_TIME','09:00'))
    ->withoutOverlapping();

Schedule::call(fn()=>app(\App\Services\Day30WorkflowService::class)->process())
    ->name('day30-workflow')
    ->dailyAt('10:00')
    ->withoutOverlapping();

Schedule::command('payments:reconcile')
    ->name('payments-reconcile')
    ->hourly()
    ->withoutOverlapping();
