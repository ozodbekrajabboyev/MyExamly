@props(['clm' => 5])
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    
    <title>Student Exam Results Dashboard</title>
</head>
<body>
<div class="container">
    <header>
        <h1>Student Exam Results</h1>
    </header>

    <main class="dashboard">
        <livewire:dashboard />
    </main>

    <footer>
        <p>© 2025 Exam Results Dashboard. All rights reserved.</p>
    </footer>
</div>
</body>
</html>
