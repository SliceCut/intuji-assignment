<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="<?php echo baseUrl() ?>">Intuji</a>
        <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navb">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navb">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item d-flex align-items-center">
                    <div class="dropdown">
                        <a class="dropdown-toggle" type="button" id="dropdownMenuButton" href="#" data-toggle="dropdown">
                            <?php echo auth()->user()['email'] ?>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <form id="logoutform" method="post" action="<?php echo baseUrl('logout') ?>">
                            </form>
                            <a class="dropdown-item" href="javascript:void(0);" onclick="$('#logoutform').submit();">Logout</a>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>