<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | <?php echo e(config('app.name')); ?></title>
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            background: #f6f7fb;
            color: #1f2937;
            display: grid;
            place-items: center;
        }
        .panel {
            width: min(420px, calc(100% - 32px));
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 28px;
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.08);
        }
        h1 {
            margin: 0 0 8px;
            font-size: 26px;
        }
        p {
            margin: 0 0 24px;
            color: #6b7280;
        }
        label {
            display: block;
            margin: 16px 0 6px;
            font-weight: 700;
            font-size: 14px;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            box-sizing: border-box;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 11px 12px;
            font-size: 15px;
        }
        .check {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 16px 0;
            color: #4b5563;
            font-size: 14px;
        }
        button {
            width: 100%;
            border: 0;
            border-radius: 6px;
            background: #2563eb;
            color: #ffffff;
            padding: 12px 16px;
            font-weight: 700;
            font-size: 15px;
        }
        a {
            color: #2563eb;
            text-decoration: none;
            font-weight: 700;
        }
        .foot {
            margin-top: 18px;
            text-align: center;
            font-size: 14px;
            color: #6b7280;
        }
        .error {
            margin-top: 6px;
            color: #b91c1c;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <main class="panel">
        <h1>Login</h1>
        <p>Masuk ke sistem absensi PPSU lokal.</p>

        <form method="POST" action="<?php echo e(route('login', [], false)); ?>">
            <?php echo csrf_field(); ?>

            <label for="login">Email, Username, atau NIK</label>
            <input id="login" type="text" name="login" value="<?php echo e(old('login')); ?>" required autofocus autocomplete="username">
            <?php $__errorArgs = ['login'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="error"><?php echo e($message); ?></div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

            <label for="password">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password">
            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="error"><?php echo e($message); ?></div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

            <button type="submit">Masuk</button>
        </form>

        <!-- <div class="foot">
            Belum punya akun? <a href="<?php echo e(route('register')); ?>">Daftar</a>
        </div> -->
    </main>
</body>
</html>
<?php /**PATH D:\kerjaan\Proyek_absensi\absensi-app\resources\views/auth/login.blade.php ENDPATH**/ ?>