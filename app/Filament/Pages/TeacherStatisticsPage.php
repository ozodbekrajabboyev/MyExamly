<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class TeacherStatisticsPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';
    
    protected static ?string $navigationLabel = 'Malaka Statistikasi';
    
    protected static ?string $title = 'Malaka Statistikasi';
    
    protected static ?string $navigationGroup = 'Foydalanuvchilar boshqaruvi';
    
    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.teacher-statistics-page';
    
    public function getHeading(): string
    {
        return "O'qituvchilar Malaka Statistikasi";
    }
    
    public function getSubheading(): ?string
    {
        return "Viloyat, tuman, maktab va malaka darajasi bo'yicha o'qituvchilar statistikasi";
    }
}
