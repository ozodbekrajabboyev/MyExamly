<?php

namespace App\Providers;

use App\Models\Sinf;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use App\Policies\SinfPolicy;
use App\Policies\StudentPolicy;
use App\Policies\SubjectPolicy;
use App\Policies\TeacherPolicy;
use App\Policies\UserPolicy;
use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentView;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Facades\Vite;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */

    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Sinf::class, SinfPolicy::class);
        Gate::policy(Student::class, StudentPolicy::class);
        Gate::policy(Subject::class, SubjectPolicy::class);
        Gate::policy(Teacher::class, TeacherPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        Model::unguard();

        FilamentView::registerRenderHook(
            'panels::body.end',
            fn (): string => Blade::render('
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        // Fix notification panel scroll issue
                        document.addEventListener("click", function(e) {
                            setTimeout(() => {
                                if (!document.querySelector("[data-headlessui-state=\'open\']")) {
                                    document.body.style.overflow = "";
                                    document.documentElement.style.overflow = "";
                                    document.body.classList.remove("overflow-hidden");
                                }
                            }, 200);
                        });
                    });
                </script>
            ')
        );
    }
}
