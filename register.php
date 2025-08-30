<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listify - Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('https://source.unsplash.com/1600x900/?nature,productive') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #333;
        }
        .header {
            background: #007bff;
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            width: 100%;
            text-align: center;
            position: fixed;
            top: 0;
            z-index: 1000;
        }
        .header h1 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 600;
        }
        .login-card {
            border: none;
            border-radius: 15px;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            padding: 2.5rem;
            width: 100%;
            max-width: 450px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .login-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        .form-control {
            border-radius: 8px;
            border-color: #ced4da;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.4);
            outline: none;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            border-radius: 8px;
            padding: 0.6rem 1.5rem;
            font-weight: 500;
            transition: background-color 0.3s, transform 0.2s;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
            transform: translateY(-2px);
        }
        .btn-primary:active {
            transform: translateY(0);
        }
        .alert {
            border-radius: 8px;
            margin-bottom: 1.5rem;
            padding: 0.75rem;
        }
        .form-check-label {
            font-size: 0.9rem;
            color: #6c757d;
        }
        .text-muted a {
            color: #007bff;
            text-decoration: none;
            transition: color 0.3s;
        }
        .text-muted a:hover {
            color: #0056b3;
            text-decoration: underline;
        }
        .register-link {
            text-align: center;
            margin-top: 1rem;
            font-size: 0.9rem;
        }
        @media (max-width: 768px) {
            .login-card { padding: 1.5rem; }
            .header h1 { font-size: 1.5rem; }
        }
    </style>
</head>
<body>
    <header class="header">
        <h1>Listify</h1>
    </header>
    <div class="container mt-5 pt-5">
        <div class="login-card">
            <h2 class="text-center mb-4 text-primary">Create Account</h2>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form action="index.php?action=register" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" name="username" id="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Register</button>
            </form>
            <div class="register-link">
                Already have an account? <a href="index.php?action=login">Login here</a>
            </div>
        </div>
    </div>
</body>
</html>