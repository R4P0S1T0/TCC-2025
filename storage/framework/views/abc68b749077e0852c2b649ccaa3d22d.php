<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Gestão</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        body {
            background: radial-gradient(circle at 20% 20%, #4E2A8C, #3B1F72, #251547);
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center px-4">
    <div class="bg-white/10 backdrop-blur-lg border border-white/20 rounded-3xl shadow-2xl p-10 w-full max-w-md text-white">
        
        <div class="text-center mb-8">
            <img src="<?php echo e(asset('img/logo/logo.svg')); ?>" alt="Logo Lart Digital"
                 class="w-24 mx-auto mb-4 drop-shadow-lg">
            <h1 class="text-3xl font-extrabold tracking-tight">Sistema de Gestão</h1>
            <p class="text-[#CBBFF9] mt-2 text-sm">Acesse sua conta e gerencie suas operações</p>
        </div>

        
        <form method="POST" action="<?php echo e(route('login')); ?>">
            <?php echo csrf_field(); ?>

            
            <?php if($errors->any()): ?>
                <div class="bg-red-100/20 border border-red-400 text-red-300 px-4 py-3 rounded-lg mb-4">
                    <ul class="list-disc list-inside text-sm">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="space-y-5">
                
                <div>
                    <label for="email" class="block text-sm font-medium text-[#E0D6FF] mb-1">E-mail</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-3 top-3 text-[#B9A6F7]"></i>
                        <input type="email" id="email" name="email" value="<?php echo e(old('email')); ?>"
                               class="pl-10 w-full px-4 py-3 bg-white/10 border border-white/30 rounded-lg 
                                      focus:ring-2 focus:ring-[#6A38C1] focus:border-transparent 
                                      placeholder-[#D3C8FA] text-white outline-none transition"
                               placeholder="seu@email.com" required autofocus>
                    </div>
                </div>

                
                <div>
                    <label for="password" class="block text-sm font-medium text-[#E0D6FF] mb-1">Senha</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-3 top-3 text-[#B9A6F7]"></i>
                        <input type="password" id="password" name="password"
                               class="pl-10 w-full px-4 py-3 bg-white/10 border border-white/30 rounded-lg 
                                      focus:ring-2 focus:ring-[#6A38C1] focus:border-transparent 
                                      placeholder-[#D3C8FA] text-white outline-none transition"
                               placeholder="••••••••" required>
                    </div>
                </div>

                
                <button type="submit"
                        class="w-full bg-gradient-to-r from-[#6A38C1] via-[#5A2DA0] to-[#4E2A8C] 
                               text-white py-3 px-4 rounded-lg font-semibold tracking-wide shadow-lg 
                               hover:brightness-110 active:scale-[0.98] transition-all duration-200">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Entrar no Sistema
                </button>
            </div>
        </form>

        
        <div class="mt-8 text-center">
            <p class="text-sm text-[#C7B9F9]">
                Lart Digital &copy; 2025<br>
                <span class="text-xs">powered by 
                    <a href="https://castorise.com.br" target="_blank" rel="noopener noreferrer"
                       class="text-[#A78BFA] hover:text-white transition">
                        Castorise
                    </a>
                </span>
            </p>
        </div>
    </div>
</body>
</html>
<?php /**PATH /opt/lampp/htdocs/tcc-real-test/lart-digital/resources/views/auth/login.blade.php ENDPATH**/ ?>