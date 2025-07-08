<!DOCTYPE html>
<html lang="pt-PT" data-theme="emerald">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Teste DaisyUI</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="min-h-screen bg-base-200">
        <div class="container mx-auto p-8">
            <h1 class="text-4xl font-bold text-primary mb-6">Teste DaisyUI - Tema Emerald</h1>
            
            <!-- Botões -->
            <div class="space-x-2 mb-6">
                <button class="btn btn-primary">Primário</button>
                <button class="btn btn-secondary">Secundário</button>
                <button class="btn btn-accent">Accent</button>
                <button class="btn btn-ghost">Ghost</button>
            </div>

            <!-- Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div class="card w-full bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h2 class="card-title">Card com DaisyUI!</h2>
                        <p>Se você está vendo este card estilizado, o DaisyUI está funcionando.</p>
                        <div class="card-actions justify-end">
                            <button class="btn btn-primary">Comprar</button>
                        </div>
                    </div>
                </div>

                <div class="card w-full bg-primary text-primary-content">
                    <div class="card-body">
                        <h2 class="card-title">Card Primário</h2>
                        <p>Este card usa as cores primárias do tema.</p>
                    </div>
                </div>
            </div>

            <!-- Badge e Alert -->
            <div class="mb-6">
                <div class="badge badge-primary">Badge</div>
                <div class="badge badge-secondary">Secondary</div>
                <div class="badge badge-accent">Accent</div>
            </div>

            <div class="alert alert-success mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>DaisyUI está funcionando perfeitamente!</span>
            </div>

            <!-- Teste de troca de tema -->
            <div class="form-control">
                <label class="label cursor-pointer">
                    <span class="label-text">Trocar para tema escuro</span>
                    <input type="checkbox" class="toggle" onclick="toggleTheme()">
                </label>
            </div>
        </div>
    </div>

    <script>
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-theme');
            html.setAttribute('data-theme', currentTheme === 'emerald' ? 'dark' : 'emerald');
        }
    </script>
</body>
</html>