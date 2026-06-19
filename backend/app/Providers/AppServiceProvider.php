<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\Contracts\SettingRepositoryInterface::class,
            \App\Repositories\Eloquent\SettingRepository::class
        );

        $this->app->singleton(\App\Services\SettingService::class);

        // Test Management Bindings
        $this->app->bind(
            \App\Repositories\Contracts\TestRepositoryInterface::class,
            \App\Repositories\Eloquent\TestRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\TestSectionRepositoryInterface::class,
            \App\Repositories\Eloquent\TestSectionRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\QuestionRepositoryInterface::class,
            \App\Repositories\Eloquent\QuestionRepository::class
        );

        // Audio Recording Bindings
        $this->app->bind(
            \App\Repositories\Contracts\AudioRecordingRepositoryInterface::class,
            \App\Repositories\Eloquent\AudioRecordingRepository::class
        );

        // Read Aloud Result Bindings
        $this->app->bind(
            \App\Repositories\Contracts\ReadAloudResultRepositoryInterface::class,
            \App\Repositories\Eloquent\ReadAloudResultRepository::class
        );

        // Interview Result Bindings
        $this->app->bind(
            \App\Repositories\Contracts\InterviewResultRepositoryInterface::class,
            \App\Repositories\Eloquent\InterviewResultRepository::class
        );

        // Analysis Report Bindings
        $this->app->bind(
            \App\Repositories\Contracts\AnalysisReportRepositoryInterface::class,
            \App\Repositories\Eloquent\AnalysisReportRepository::class
        );

        $this->app->singleton(\App\Services\TestService::class);
        $this->app->singleton(\App\Services\TestSectionService::class);
        $this->app->singleton(\App\Services\QuestionService::class);
        $this->app->singleton(\App\Services\AudioRecordingService::class);
        $this->app->singleton(\App\Services\AiEngineService::class);
        $this->app->singleton(\App\Services\ReadAloudResultService::class);
        $this->app->singleton(\App\Services\InterviewResultService::class);
        $this->app->singleton(\App\Services\AnalysisReportService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
