    <nav class="navbar navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <span class="navbar-brand">Imaginary Environmental</span>
            </div>
            <div id="navbar" class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li class="active"><a href="index.php">Home</a></li>
                    <li><a href="page1.php">Page 1</a></li>
                    <li><a href="page2.php">Page 2</a></li>
                    <li><a href="page3.php">Page 3</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <!-- if we are logged in we will make these two not show and show my content -->
                    <?php
                        if (logged_in()) {
                            echo "<li><a href='mycontent.php'>{$_SESSION['username']}'s content</a></li>";
                            echo "<li><a href='logout.php'>Logout</a></li>";
                        } else {
                            echo "<li><a href='login.php'>Login</a></li>";
                            echo "<li><a href='register.php'>Register</a></li>";
                        }
                    ?>
                </ul>
            </div><!--/.nav-collapse -->
        </div>
    </nav>
