<?php

namespace App\Services;

use App\Models\CustomerProfile;
use App\Models\DailyAdherence;
use App\Models\Day30Decision;
use App\Models\MilestoneCheckin;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ResetProfile;
use App\Models\ResetReentryRequest;
use App\Models\SafetyFlag;
use App\Models\Testimonial;
use App\Models\Shipment;
use App\Models\WhatsappContact;
use App\Models\WhatsappMessage;
use Illuminate\Support\Facades\DB;

class Phase2DAnalyticsService
{
    public function paymentDashboard(array $filters=[]): array
    {
        $query=Payment::with('order.user')->latest('id');

        if (!empty($filters['status'])) $query->where('status',$filters['status']);
        if (!empty($filters['gateway'])) $query->where('gateway',$filters['gateway']);
        if (!empty($filters['from'])) $query->whereDate('created_at','>=',$filters['from']);
        if (!empty($filters['to'])) $query->whereDate('created_at','<=',$filters['to']);

        $rows=(clone $query)->paginate(30)->withQueryString();

        return [
            'payments'=>$rows,
            'totals'=>[
                'all'=>Payment::count(),
                'paid'=>Payment::where('status','paid')->count(),
                'pending'=>Payment::whereIn('status',['pending','authorized'])->count(),
                'failed'=>Payment::where('status','failed')->count(),
                'paid_amount'=>(float)Payment::where('status','paid')->sum('amount'),
                'pending_amount'=>(float)Payment::whereIn('status',['pending','authorized'])->sum('amount'),
            ],
        ];
    }

    public function fulfilmentDashboard(array $filters=[]): array
    {
        $shipments=Shipment::with('order.user')->latest('id');
        if (!empty($filters['status'])) $shipments->where('status',$filters['status']);
        if (!empty($filters['courier'])) $shipments->where('courier',$filters['courier']);

        return [
            'shipments'=>$shipments->paginate(30)->withQueryString(),
            'totals'=>[
                'pending'=>Shipment::whereIn('status',['pending','created'])->count(),
                'packed'=>Shipment::where('status','packed')->count(),
                'in_transit'=>Shipment::whereIn('status',['dispatched','in_transit'])->count(),
                'out_for_delivery'=>Shipment::where('status','out_for_delivery')->count(),
                'delivered'=>Shipment::where('status','delivered')->count(),
            ],
        ];
    }

    public function whatsappDashboard(): array
    {
        $contacts=WhatsappContact::count();
        $optedIn=WhatsappContact::where('opt_in',true)->count();

        return [
            'contacts'=>$contacts,
            'opted_in'=>$optedIn,
            'opt_in_rate'=>$contacts ? round(($optedIn/$contacts)*100,1) : 0,
            'queued'=>WhatsappMessage::where('status','queued')->count(),
            'sent'=>WhatsappMessage::where('status','sent')->count(),
            'delivered'=>WhatsappMessage::where('status','delivered')->count(),
            'read'=>WhatsappMessage::where('status','read')->count(),
            'failed'=>WhatsappMessage::where('status','failed')->count(),
            'inbound'=>WhatsappMessage::where('direction','inbound')->count(),
            'outbound'=>WhatsappMessage::where('direction','outbound')->count(),
            'templates'=>\App\Models\WhatsappTemplate::where('is_active',true)->count(),
            'recent'=>WhatsappMessage::with('whatsappContact.user')->latest('id')->limit(40)->get(),
            'template_stats'=>WhatsappMessage::select('template_name','status',DB::raw('COUNT(*) as total'))
                ->whereNotNull('template_name')
                ->groupBy('template_name','status')
                ->orderByDesc('total')->get(),
        ];
    }

    public function resetDashboard(): array
    {
        $active=ResetProfile::whereIn('status',['active','started','in_progress'])->count();

        $dayRows=ResetProfile::select('current_reset_day',DB::raw('COUNT(*) as total'))
            ->whereIn('status',['active','started','in_progress'])
            ->groupBy('current_reset_day')
            ->orderBy('current_reset_day')->get();

        $today=DailyAdherence::whereDate('calendar_date',today());
        $todayTotal=(clone $today)->count();
        $todayResponded=(clone $today)->whereNotNull('responded_at')->count();
        $todayCompleted=(clone $today)->where('response_status','completed')->count();

        $milestones=[
            'pending'=>MilestoneCheckin::where('status','pending')->count(),
            'started'=>MilestoneCheckin::where('status','started')->count(),
            'completed'=>MilestoneCheckin::where('status','completed')->count(),
        ];

        return [
            'active'=>$active,
            'completed'=>ResetProfile::where('status','completed')->count(),
            'safety_flags'=>SafetyFlag::where('manual_review_required',true)->count(),
            'manual_review'=>ResetProfile::where('manual_review_required',true)->count(),
            'dropoffs'=>ResetProfile::where('status','dropoff')->count(),
            'paused'=>ResetProfile::where('status','paused')->count(),
            'reentry_pending'=>ResetReentryRequest::where('status','pending')->count(),
            'testimonials_pending'=>Testimonial::where('moderation_status','pending')->count(),
            'day0'=>ResetProfile::where('current_reset_day',0)->whereIn('status',['created','active','started','in_progress'])->count(),
            'day30'=>ResetProfile::where('current_reset_day',30)->whereIn('status',['active','started','in_progress'])->count(),
            'day_distribution'=>$dayRows,
            'today_total'=>$todayTotal,
            'today_responded'=>$todayResponded,
            'today_completed'=>$todayCompleted,
            'today_response_rate'=>$todayTotal ? round($todayResponded/$todayTotal*100,1) : 0,
            'today_completion_rate'=>$todayTotal ? round($todayCompleted/$todayTotal*100,1) : 0,
            'milestones'=>$milestones,
            'recent'=>ResetProfile::with('user.customerProfile')->latest('id')->limit(40)->get(),
            'day30_decisions'=>Day30Decision::with('resetProfile.user')->latest('id')->limit(20)->get(),
        ];
    }
}
