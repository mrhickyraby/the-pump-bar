<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#a71925">
    <title>The Pump Bar | Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="pump-app pump-dashboard">
    @include('layouts.pump-navigation')

    <main class="pump-main">
        <section class="pump-intro" aria-labelledby="dashboard-title">
            <div>
                <p class="pump-eyebrow"><span></span> Operations overview</p>
                <h1 id="dashboard-title">Business dashboard</h1>
                <p class="pump-intro__date">{{ now()->format('l, F j, Y') }}</p>
            </div>
            @if(! Auth::user()->isStaff())
                <a class="pump-primary-action" href="{{ route('pos.index') }}">Open point of sale <span aria-hidden="true">&rarr;</span></a>
            @endif
        </section>

        @if(Auth::user()->isOwner())
            <section class="pump-analytics" aria-label="Financial analytics">
                <form method="GET" action="{{ route('dashboard') }}" class="pump-analytics__filters">
                    <div class="pump-analytics__filter">
                        <label for="period">Period</label>
                        <select id="period" name="period">
                            <option value="day" @selected($selectedPeriod === 'day')>Daily</option>
                            <option value="week" @selected($selectedPeriod === 'week')>Weekly</option>
                            <option value="month" @selected($selectedPeriod === 'month')>Monthly</option>
                        </select>
                    </div>
                    <div class="pump-analytics__filter">
                        <label for="date">Anchor date</label>
                        <input id="date" type="date" name="date" value="{{ $selectedDate->toDateString() }}">
                    </div>
                    <button type="submit">Apply</button>
                </form>

                <p class="pump-analytics__caption">Showing owner financial analytics for {{ $rangeLabel }}</p>

                <section class="pump-metrics pump-metrics--owner" aria-label="Owner financial summary">
                    <article class="pump-metric pump-metric--sales">
                        <div class="pump-metric__top"><span class="pump-metric__label">Gross sales</span><span class="pump-metric__mark" aria-hidden="true">₱</span></div>
                        <p class="pump-metric__value">₱{{ number_format($financialSummary['totals']['gross_sales'] ?? 0, 2) }}</p>
                        <p class="pump-metric__note">{{ number_format($financialSummary['totals']['cups_sold'] ?? 0) }} cups sold</p>
                    </article>
                    <article class="pump-metric">
                        <div class="pump-metric__top"><span class="pump-metric__label">COGS</span><span class="pump-metric__mark pump-metric__mark--yellow" aria-hidden="true">C</span></div>
                        <p class="pump-metric__value">₱{{ number_format($financialSummary['totals']['cogs'] ?? 0, 2) }}</p>
                        <p class="pump-metric__note">Product costs</p>
                    </article>
                    <article class="pump-metric">
                        <div class="pump-metric__top"><span class="pump-metric__label">Gross profit</span><span class="pump-metric__mark" aria-hidden="true">G</span></div>
                        <p class="pump-metric__value">₱{{ number_format($financialSummary['totals']['gross_profit'] ?? 0, 2) }}</p>
                        <p class="pump-metric__note">Before operating expenses</p>
                    </article>
                    <article class="pump-metric">
                        <div class="pump-metric__top"><span class="pump-metric__label">OPEX</span><span class="pump-metric__mark pump-metric__mark--yellow" aria-hidden="true">O</span></div>
                        <p class="pump-metric__value">₱{{ number_format($financialSummary['totals']['opex'] ?? 0, 2) }}</p>
                        <p class="pump-metric__note">Manual daily expenses</p>
                    </article>
                    <article class="pump-metric">
                        <div class="pump-metric__top"><span class="pump-metric__label">Commission</span><span class="pump-metric__mark" aria-hidden="true">%</span></div>
                        <p class="pump-metric__value">₱{{ number_format($financialSummary['totals']['commission'] ?? 0, 2) }}</p>
                        <p class="pump-metric__note">Based on cups sold</p>
                    </article>
                    <article class="pump-metric">
                        <div class="pump-metric__top"><span class="pump-metric__label">Net profit</span><span class="pump-metric__mark pump-metric__mark--yellow" aria-hidden="true">N</span></div>
                        <p class="pump-metric__value">₱{{ number_format($financialSummary['totals']['net_profit'] ?? 0, 2) }}</p>
                        <p class="pump-metric__note">Gross profit - OPEX - commission</p>
                    </article>
                </section>
            </section>
        @else
            <section class="pump-metrics" aria-label="Business summary">
                <article class="pump-metric pump-metric--sales">
                    <div class="pump-metric__top"><span class="pump-metric__label">Total sales</span><span class="pump-metric__mark" aria-hidden="true">₱</span></div>
                    <p class="pump-metric__value">₱{{ number_format($salesTotal ?? 0, 2) }}</p>
                    <p class="pump-metric__note">Recorded revenue</p>
                </article>
                <article class="pump-metric">
                    <div class="pump-metric__top"><span class="pump-metric__label">Products</span><span class="pump-metric__mark" aria-hidden="true">P</span></div>
                    <p class="pump-metric__value">{{ $products ?? 0 }}</p>
                    <p class="pump-metric__note">Available in catalog</p>
                </article>
                <article class="pump-metric">
                    <div class="pump-metric__top"><span class="pump-metric__label">Transactions</span><span class="pump-metric__mark pump-metric__mark--yellow" aria-hidden="true">#</span></div>
                    <p class="pump-metric__value">{{ $transactions ?? 0 }}</p>
                    <p class="pump-metric__note">Completed sales</p>
                </article>
                <article class="pump-metric">
                    <div class="pump-metric__top"><span class="pump-metric__label">Low stock alerts</span><span class="pump-metric__mark" aria-hidden="true">!</span></div>
                    <p class="pump-metric__value">{{ $lowStock ?? 0 }}</p>
                    <p class="pump-metric__note">Items below reorder level</p>
                </article>
            </section>
        @endif

        <div class="pump-sections">
            <section class="pump-section" aria-labelledby="branches-title">
                <div class="pump-section__heading">
                    <div>
                        <p class="pump-section__kicker">Locations</p>
                        <h2 id="branches-title">Branch performance</h2>
                    </div>
                    @if(Auth::user()->isOwner() || Auth::user()->isManager())
                        <a class="pump-text-link" href="{{ route('branches.index') }}">All branches <span aria-hidden="true">&rarr;</span></a>
                    @endif
                </div>
                <div class="pump-branch-list">
                    @forelse($branches as $branch)
                        <div class="pump-branch">
                            <span class="pump-branch__initial" aria-hidden="true">{{ strtoupper(substr($branch->name, 0, 1)) }}</span>
                            <div class="pump-branch__details">
                                <span class="pump-branch__name">{{ $branch->name }}</span>
                                <span class="pump-branch__code">Branch {{ $branch->code }}</span>
                            </div>
                            <span class="pump-status"><span></span>{{ ucfirst($branch->status) }}</span>
                        </div>
                    @empty
                        <p class="pump-empty">No branches created yet.</p>
                    @endforelse
                </div>
            </section>

            <section class="pump-section" aria-labelledby="actions-title">
                <div class="pump-section__heading">
                    <div>
                        <p class="pump-section__kicker">Shortcuts</p>
                        <h2 id="actions-title">Daily operations</h2>
                    </div>
                </div>
                <div class="pump-actions">
                    @if(Auth::user()->isOwner())
                        <a class="pump-action" href="{{ route('branches.index') }}"><span>Manage branches</span><span aria-hidden="true">&rarr;</span></a>
                        <a class="pump-action" href="{{ route('users.index') }}"><span>Manage users</span><span aria-hidden="true">&rarr;</span></a>
                    @endif
                    <a class="pump-action" href="{{ route('products.index') }}"><span>{{ Auth::user()->isOwner() ? 'Manage products' : 'View products' }}</span><span aria-hidden="true">&rarr;</span></a>
                    @if(! Auth::user()->isStaff())
                        <a class="pump-action" href="{{ route('inventory.index') }}"><span>Track inventory</span><span aria-hidden="true">&rarr;</span></a>
                        @if(Auth::user()->isOwner())
                            <a class="pump-action" href="{{ route('expenses.index') }}"><span>Record expenses</span><span aria-hidden="true">&rarr;</span></a>
                            <a class="pump-action" href="{{ route('reports.index') }}"><span>View reports</span><span aria-hidden="true">&rarr;</span></a>
                        @endif
                    @endif
                    <a class="pump-action" href="{{ route('pos.index') }}"><span>Open point of sale</span><span aria-hidden="true">&rarr;</span></a>
                </div>
            </section>
        </div>
        <footer class="pump-footer">The Pump Bar <span aria-hidden="true">·</span> Operations</footer>
    </main>
</body>
</html>
