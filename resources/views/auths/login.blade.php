<!doctype html>
<html lang="en">

<!-- Mirrored from geeksui.codescandy.com/geeks/pages/sign-in.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 14 Jan 2025 13:53:57 GMT -->

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="author" content="Codescandy" />

    <!-- Favicon icon-->
    <link rel="shortcut icon" type="image/x-icon" href="../assets/images/favicon/favicon.ico" />

    <!-- darkmode js -->
    <script src="../assets/js/vendors/darkMode.js"></script>

    <!-- Libs CSS -->
    <link href="../assets/fonts/feather/feather.css" rel="stylesheet" />
    <link href="../assets/libs/bootstrap-icons/font/bootstrap-icons.min.css" rel="stylesheet" />
    <link href="../assets/libs/simplebar/dist/simplebar.min.css" rel="stylesheet" />

    <!-- Theme CSS -->
    <link rel="stylesheet" href="../assets/css/theme.min.css">
    <link rel="canonical" href="sign-in.html" />
    <title>Login</title>
</head>

<body>
    <!-- Page content -->
    <main>
        <section class="container d-flex flex-column vh-100">
            <div class="row align-items-center justify-content-center g-0 h-lg-100 py-8">
                <div class="col-lg-5 col-md-8 py-8 py-xl-0">
                    <!-- Card -->
                    <div class="card shadow">
                        <!-- Card body -->
                        <div class="card-body p-6 d-flex flex-column gap-4">
                            <div>
                                <a href="../index.html"><img src="/logo.png" class="mb-4 w-50" alt="logo-icon" /></a>
                                <div class="d-flex flex-column gap-1">
                                    <h1 class="mb-0 fw-bold">Đăng nhập</h1>
                                </div>
                            </div>
                            <!-- Form -->
                            <form class="needs-validation" method="POST" action="{{ route('login') }}">
                                <!-- Username -->
                                @csrf
                                <div class="mb-3">
                                    <label for="signInEmail" class="form-label">Email</label>
                                    <input type="email" id="signInEmail" class="form-control" name="email"
                                        placeholder="Email address here" required />
                                    <div class="invalid-feedback">Please enter valid username.</div>
                                </div>
                                <!-- Password -->
                                <div class="mb-3">
                                    <label for="signInPassword" class="form-label">Mật khẩu</label>
                                    <input type="password" id="signInPassword" class="form-control" name="password"
                                        placeholder="Nhập mật khẩu" required />
                                    <div class="invalid-feedback">Please enter valid password.</div>
                                    <div class="my-3">
                                        <!-- Button -->
                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-primary">Đăng nhập</button>
                                        </div>
                                    </div>

                                    <hr class="my-4" />
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <!-- Scripts -->
    <!-- Libs JS -->
    <script src="../assets/libs/%40popperjs/core/dist/umd/popper.min.js"></script>
    <script src="../assets/libs/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="../assets/libs/simplebar/dist/simplebar.min.js"></script>

    <!-- Theme JS -->
    <script src="../assets/js/theme.min.js"></script>

    <script src="../assets/js/vendors/validation.js"></script>
    <script>
        document.documentElement.setAttribute("data-bs-theme", "light");
    </script>
</body>

</html>
