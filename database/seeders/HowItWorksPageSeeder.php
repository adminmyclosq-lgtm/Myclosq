<?php

namespace Database\Seeders;

use App\Models\CmsPage;
use Illuminate\Database\Seeder;

class HowItWorksPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $page = CmsPage::firstOrCreate(
            ['slug' => 'how-it-works'],
            [
                'title' => 'How It Works',
                'status' => 'published',
            ]
        );

        $page->sections()->delete(); // Clear old ones

        $sections = [
            [
                'title' => 'Hero',
                'html' => '
<section class="border-b border-border/60">
    <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8 grid gap-10 py-20 lg:py-32 lg:grid-cols-[1.05fr_1fr] lg:gap-14 lg:items-center">
        <div>
            <h1 class="font-serif text-5xl leading-[1.05] text-foreground md:text-6xl">One daily capsule.<br/>A few guided moments.<br/>A clearer decision at Day 30.</h1>
            <p class="mt-6 max-w-md text-[15px] leading-relaxed text-foreground/70">The 30-day guided course records enough about your capsule exposure and reported response to make the end read honest.</p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="/shop" class="btn-primary">View Product</a>
                <a href="/#standards" class="btn-secondary">Review Standards</a>
            </div>
        </div>
        <div class="overflow-hidden rounded-2xl bg-cream min-h-[400px] lg:min-h-[600px] w-full">
            <img src="/assets/hero-product-CftTmpl1.jpg" alt="Morning capsule routine" class="h-full w-full object-cover"/>
        </div>
    </div>
</section>'
            ],
            [
                'title' => 'Product Layer',
                'html' => '
<section class="bg-primary text-primary-foreground">
    <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8 py-20 lg:py-32 text-center">
        <h2 class="mx-auto max-w-3xl font-serif text-3xl leading-[1.15] md:text-4xl">The capsule creates the response.<br/>The guided experience helps make it readable.</h2>
        <div class="mx-auto mt-8 grid max-w-3xl gap-4 md:grid-cols-2 text-left">
            <div class="rounded-xl border border-primary-foreground/15 bg-primary-foreground/[0.05] p-5">
                <div class="text-[11px] uppercase tracking-[0.16em] text-primary-foreground/70">The Product</div>
                <ul class="mt-3 space-y-1 text-[13px] text-primary-foreground/85">
                    <li>· Targeted capsule</li>
                    <li>· 30 capsules</li>
                    <li>· One capsule daily</li>
                </ul>
            </div>
            <div class="rounded-xl border border-primary-foreground/15 bg-primary-foreground/[0.05] p-5">
                <div class="text-[11px] uppercase tracking-[0.16em] text-primary-foreground/70">The Guided Layer</div>
                <ul class="mt-3 space-y-1 text-[13px] text-primary-foreground/85">
                    <li>· Starting Point</li>
                    <li>· Course Moments</li>
                    <li>· Gut Response Brief</li>
                </ul>
            </div>
        </div>
    </div>
</section>'
            ],
            [
                'title' => 'Timeline',
                'html' => '
<section class="py-24 lg:py-32">
    <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8">
        <h2 class="text-center font-serif text-3xl leading-tight md:text-4xl">A 30-Day Guided Journey</h2>
        <div class="mt-12 grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-6">
            <div class="rounded-xl border border-border/60 bg-card p-4 text-center">
                <div class="mx-auto grid h-8 w-8 place-items-center rounded-full bg-primary/10 text-[11px] text-primary">•</div>
                <div class="mt-3 text-[11px] uppercase tracking-[0.14em] text-muted-foreground">Before Day 1</div>
                <div class="mt-1 font-serif text-[13px] leading-snug">Starting Point</div>
            </div>
            <div class="rounded-xl border border-border/60 bg-card p-4 text-center">
                <div class="mx-auto grid h-8 w-8 place-items-center rounded-full bg-primary/10 text-[11px] text-primary">•</div>
                <div class="mt-3 text-[11px] uppercase tracking-[0.14em] text-muted-foreground">Day 3</div>
                <div class="mt-1 font-serif text-[13px] leading-snug">Product Fit</div>
            </div>
            <div class="rounded-xl border border-border/60 bg-card p-4 text-center">
                <div class="mx-auto grid h-8 w-8 place-items-center rounded-full bg-primary/10 text-[11px] text-primary">•</div>
                <div class="mt-3 text-[11px] uppercase tracking-[0.14em] text-muted-foreground">Day 7</div>
                <div class="mt-1 font-serif text-[13px] leading-snug">First-Week Signal</div>
            </div>
            <div class="rounded-xl border border-border/60 bg-card p-4 text-center">
                <div class="mx-auto grid h-8 w-8 place-items-center rounded-full bg-primary/10 text-[11px] text-primary">•</div>
                <div class="mt-3 text-[11px] uppercase tracking-[0.14em] text-muted-foreground">Day 14</div>
                <div class="mt-1 font-serif text-[13px] leading-snug">Midpoint Pattern</div>
            </div>
            <div class="rounded-xl border border-border/60 bg-card p-4 text-center">
                <div class="mx-auto grid h-8 w-8 place-items-center rounded-full bg-primary/10 text-[11px] text-primary">•</div>
                <div class="mt-3 text-[11px] uppercase tracking-[0.14em] text-muted-foreground">Day 21</div>
                <div class="mt-1 font-serif text-[13px] leading-snug">Final Stretch</div>
            </div>
            <div class="rounded-xl border border-border/60 bg-card p-4 text-center">
                <div class="mx-auto grid h-8 w-8 place-items-center rounded-full bg-primary/10 text-[11px] text-primary">•</div>
                <div class="mt-3 text-[11px] uppercase tracking-[0.14em] text-muted-foreground">Day 30</div>
                <div class="mt-1 font-serif text-[13px] leading-snug">Gut Response Brief</div>
            </div>
        </div>
        <div class="mx-auto mt-10 max-w-md rounded-xl border border-border/60 bg-cream p-4 text-center text-[13px] text-foreground/70">15 minutes total · 6 milestones</div>
    </div>
</section>'
            ],
            [
                'title' => 'Designed to Fit',
                'html' => '
<section class="bg-cream/50 py-24 lg:py-32">
    <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8">
        <h2 class="font-serif text-3xl leading-tight md:text-4xl">Designed to fit real life.</h2>
        <p class="mt-4 max-w-2xl text-[14px] leading-relaxed text-foreground/70">A gentle guided layer, sensitive to the ways real life interrupts.</p>
        <div class="mt-8 grid gap-4 md:grid-cols-4">
            <div class="rounded-xl bg-background p-5"><div class="font-serif text-base">Life Load</div><div class="mt-2 text-[12px] leading-relaxed text-foreground/60">Real-life demands are recorded as context, not failure.</div></div>
            <div class="rounded-xl bg-background p-5"><div class="font-serif text-base">Credibility</div><div class="mt-2 text-[12px] leading-relaxed text-foreground/60">Every observation is stored honestly.</div></div>
            <div class="rounded-xl bg-background p-5"><div class="font-serif text-base">Add Context</div><div class="mt-2 text-[12px] leading-relaxed text-foreground/60">A few short moments capture what matters.</div></div>
            <div class="rounded-xl bg-background p-5"><div class="font-serif text-base">Return Ready</div><div class="mt-2 text-[12px] leading-relaxed text-foreground/60">Return to your course at any time via bottle QR.</div></div>
        </div>
    </div>
</section>'
            ],
            [
                'title' => 'Missed Day',
                'html' => '
<section class="py-24 lg:py-32">
    <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8">
        <h2 class="text-center font-serif text-3xl leading-tight md:text-4xl">A missed day does not erase the journey.</h2>
        <div class="mx-auto mt-10 grid max-w-4xl gap-4 md:grid-cols-2">
            <div class="rounded-xl border border-border/60 bg-card p-5"><div class="font-serif text-base">Missed Capsule</div><div class="mt-2 text-[13px] text-foreground/60">Recorded, then continued.</div></div>
            <div class="rounded-xl border border-border/60 bg-card p-5"><div class="font-serif text-base">Travel or Diet Change</div><div class="mt-2 text-[13px] text-foreground/60">Captured as context.</div></div>
            <div class="rounded-xl border border-border/60 bg-card p-5"><div class="font-serif text-base">Missed Milestone</div><div class="mt-2 text-[13px] text-foreground/60">Return to it — the milestone waits.</div></div>
            <div class="rounded-xl border border-border/60 bg-card p-5"><div class="font-serif text-base">Uneven Feeling</div><div class="mt-2 text-[13px] text-foreground/60">Add a short note if it helps.</div></div>
        </div>
    </div>
</section>'
            ],
            [
                'title' => 'Honest Read',
                'html' => '
<section class="bg-primary text-primary-foreground py-24 lg:py-32">
    <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8">
        <h2 class="font-serif text-3xl leading-tight md:text-4xl">Built for an honest read.</h2>
        <ul class="mt-8 max-w-2xl space-y-4 text-[14px] text-primary-foreground/85">
            <li class="flex gap-3"><span class="mt-2 h-1 w-1 shrink-0 rounded-full bg-primary-foreground/70"></span>Anchoring on your starting intention.</li>
            <li class="flex gap-3"><span class="mt-2 h-1 w-1 shrink-0 rounded-full bg-primary-foreground/70"></span>Separating routine disruptions from response signal.</li>
            <li class="flex gap-3"><span class="mt-2 h-1 w-1 shrink-0 rounded-full bg-primary-foreground/70"></span>Weighing capsule exposure against reported response.</li>
            <li class="flex gap-3"><span class="mt-2 h-1 w-1 shrink-0 rounded-full bg-primary-foreground/70"></span>Preserving observations, not conclusions.</li>
            <li class="flex gap-3"><span class="mt-2 h-1 w-1 shrink-0 rounded-full bg-primary-foreground/70"></span>Offering the most relevant next step, not a score.</li>
        </ul>
    </div>
</section>'
            ],
            [
                'title' => 'Personal Brief',
                'html' => '
<section class="py-24 lg:py-32">
    <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8 grid items-center gap-14 lg:grid-cols-[1fr_1fr] lg:gap-20">
        <div class="overflow-hidden rounded-2xl bg-cream min-h-[400px] lg:min-h-[500px] w-full"><img src="/assets/bottle-capsules-COsDFlLa.jpg" alt="Sample Gut Response Brief" class="h-full w-full object-cover"/></div>
        <div>
            <h2 class="font-serif text-3xl leading-tight md:text-4xl">Your personal Gut Response Brief.</h2>
            <p class="mt-4 max-w-md text-[14px] leading-relaxed text-foreground/70">A single honest read of the 30 days — what changed, what did not, the context around it, and the most relevant next step.</p>
            <div class="mt-6 flex flex-wrap gap-2">
                <span class="rounded-full border border-border/70 px-3 py-1 text-[11px]">Repeat</span>
                <span class="rounded-full border border-border/70 px-3 py-1 text-[11px]">Maintenance</span>
                <span class="rounded-full border border-border/70 px-3 py-1 text-[11px]">Try another</span>
                <span class="rounded-full border border-border/70 px-3 py-1 text-[11px]">Stop</span>
                <span class="rounded-full border border-border/70 px-3 py-1 text-[11px]">Doctor first</span>
            </div>
        </div>
    </div>
</section>'
            ],
            [
                'title' => 'Physical Pack',
                'html' => '
<section class="bg-cream/60 py-24 lg:py-32">
    <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8 grid items-center gap-14 lg:grid-cols-[1fr_1fr] lg:gap-20">
        <div class="overflow-hidden rounded-2xl bg-background min-h-[400px] lg:min-h-[500px] w-full"><img src="/assets/course-kit-DtVPc2tT.jpg" alt="Open kit" class="h-full w-full object-cover"/></div>
        <div>
            <h2 class="font-serif text-3xl leading-tight md:text-4xl">The physical pack helps you start correctly.</h2>
            <ul class="mt-6 space-y-2 text-[13px] text-foreground/70">
                <li>· Bottle-attached start cue</li>
                <li>· Course Companion card</li>
                <li>· Welcome Guide</li>
            </ul>
        </div>
    </div>
</section>'
            ],
            [
                'title' => 'Help Is Available',
                'html' => '
<section class="py-24 lg:py-32">
    <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8 grid gap-12 lg:grid-cols-[1fr_1.5fr] lg:gap-16 lg:items-center">
        <div>
            <h2 class="font-serif text-3xl leading-tight md:text-4xl">Help is available when you need it.</h2>
            <div class="mt-6"><a href="/#faq" class="btn-secondary">Contact Us</a></div>
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="rounded-xl border border-border/60 bg-card p-5"><div class="font-serif text-base">WhatsApp</div><div class="mt-2 text-[12px] text-foreground/60">Ask a question or share a concern.</div></div>
            <div class="rounded-xl border border-border/60 bg-card p-5"><div class="font-serif text-base">Email</div><div class="mt-2 text-[12px] text-foreground/60">Reach us for account or product help.</div></div>
            <div class="rounded-xl border border-border/60 bg-card p-5"><div class="font-serif text-base">Guidance</div><div class="mt-2 text-[12px] text-foreground/60">Speak to a doctor first if unsure.</div></div>
            <div class="rounded-xl border border-border/60 bg-card p-5"><div class="font-serif text-base">Support</div><div class="mt-2 text-[12px] text-foreground/60">See our support page for common questions.</div></div>
        </div>
    </div>
</section>'
            ],
            [
                'title' => 'Ready If Relevant',
                'html' => '
<section class="border-t border-border/60 py-20 lg:py-24">
    <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8 grid gap-12 md:grid-cols-2">
        <div>
            <div class="text-[11px] font-medium uppercase tracking-[0.18em] text-muted-foreground">Ready if relevant</div>
            <ul class="mt-4 space-y-1 text-[13px] text-foreground/70">
                <li>· Recurring gut discomfort</li>
                <li>· Looking for structure</li>
                <li>· Want to observe response</li>
            </ul>
        </div>
        <div>
            <div class="text-[11px] font-medium uppercase tracking-[0.18em] text-muted-foreground">Speak to a doctor first</div>
            <ul class="mt-4 space-y-1 text-[13px] text-foreground/70">
                <li>· Pregnant or breast-feeding</li>
                <li>· Diagnosed condition</li>
                <li>· On prescription medication</li>
            </ul>
        </div>
    </div>
</section>'
            ],
            [
                'title' => 'Give Fair Trial',
                'html' => '
<section class="bg-primary text-primary-foreground">
    <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8 py-20 lg:py-32 text-center">
        <h2 class="font-serif text-3xl md:text-4xl">Give the product a fair 30-day trial.</h2>
        <div class="mt-6 flex flex-wrap justify-center gap-3">
            <a href="/shop" class="btn-primary !bg-background !text-foreground hover:!bg-background/90">Shop</a>
            <a href="#fit-check" class="btn-secondary !border-primary-foreground/30 !text-primary-foreground hover:!bg-primary-foreground/10">Check Fit</a>
        </div>
    </div>
</section>'
            ]
        ];

        foreach ($sections as $index => $section) {
            $page->sections()->create([
                'section_type' => 'custom_html',
                'title' => $section['title'],
                'subtitle' => 'How It Works static section',
                'sort_order' => ($index + 1) * 10,
                'content' => json_encode([
                    'html' => $section['html'],
                    'settings' => ['top_spacing' => '', 'bottom_spacing' => '']
                ])
            ]);
        }
    }
}
