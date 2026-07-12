<?php

namespace App\Filament\Widgets;

use App\Models\Assessment;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Question;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Video;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PlatformOverview extends StatsOverviewWidget
{
    protected ?string $heading = 'Platform overview';

    protected ?string $description = 'High-level counts for your accessible tenant data.';

    protected function getStats(): array
    {
        return [
            Stat::make('Tenants', Tenant::query()->count()),
            Stat::make('Users', User::query()->count()),
            Stat::make('Courses', Course::query()->count()),
            Stat::make('Lessons', Lesson::query()->count()),
            Stat::make('Videos', Video::query()->count()),
            Stat::make('Assessments', Assessment::query()->count()),
            Stat::make('Questions', Question::query()->count()),
            Stat::make('Plans', Plan::query()->count()),
            Stat::make('Subscriptions', Subscription::query()->count()),
            Stat::make('Enrollments', Enrollment::query()->count()),
            Stat::make('Orders', Order::query()->count()),
            Stat::make('Payments', Payment::query()->count()),
        ];
    }
}
