<?php if (!isset($current_page)) {$current_page = "undefined";}

///////////////////// TESTING AREA /////////////////////

//dump($_SESSION);


?>
<div class="container-fluid main_nav">
    <div class="navbar-header">
        <h1>nayada<span>THAI CUISINE</span></h1>
        <button type="button" class="navbar-toggle">
            <span class="glyphicon glyphicon-menu-hamburger"></span>
        </button>
    </div>
    <nav class="collapse navbar-collapse">
    	<h1 class="sr-only">Main Navigation</h1>
       
        <ul class="nav navbar-nav navbar-left">
            <li>
                <a href="<?php echo $server->status->url_prefix; ?>index.php">Home</a>
            </li>
            <li>
                <a href="#" class="dropdown dropdown-toggle" data-toggle="dropdown">Menu <span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="<?php echo $server->status->url_prefix; ?>menu.php?category=1">Lunch Special</a></li>
                    <li><a href="<?php echo $server->status->url_prefix; ?>menu.php?category=2">Appetizer</a></li>
                    <li><a href="<?php echo $server->status->url_prefix; ?>menu.php?category=3">Soup</a></li>
                    <li><a href="<?php echo $server->status->url_prefix; ?>menu.php?category=4">Salad</a></li>
                    <li><a href="<?php echo $server->status->url_prefix; ?>menu.php?category=5">Curry</a></li>
                    <li><a href="<?php echo $server->status->url_prefix; ?>menu.php?category=6">Noodle</a></li>
                    <li><a href="<?php echo $server->status->url_prefix; ?>menu.php?category=7">Rice</a></li>
                    <li><a href="<?php echo $server->status->url_prefix; ?>menu.php?category=8">Entree</a></li>
                    <li><a href="<?php echo $server->status->url_prefix; ?>menu.php?category=9">Chef's Special</a></li>
                    <li><a href="<?php echo $server->status->url_prefix; ?>menu.php?category=10">Dessert</a></li>
                    <li><a href="<?php echo $server->status->url_prefix; ?>menu.php?category=11">Beverage</a></li>
                    <li><a href="<?php echo $server->status->url_prefix; ?>menu.php?category=12">Side</a></li>
                </ul>
            </li>
            <li>
                <a href="#" class="dropdown dropdown-toggle" data-toggle="dropdown">Food <span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="<?php echo $server->status->url_prefix; ?>gallery.php">Photo Gallery</a></li>
                    <li><a href="<?php echo $server->status->url_prefix; ?>keyword.php">Keyword Search</a></li>
                    <li><a href="<?php echo $server->status->url_prefix; ?>faq.php">FAQ</a></li>
                </ul>
            </li>
            <li><a class="scroll_contact" href="<?php echo $server->status->url_prefix; ?>index.php#contact_area">Contact</a></li>
        </ul>

        <?php 
            if ($cust_logged_in === true) { 
        ?>
            <ul class="nav navbar-nav navbar-right <?php if ($cust_logged_in === true) {echo "logged_in";} ?>">
                <!-- <li>Clock Icon</li> -->
                <li title="Cart"><a href="<?php echo $server->status->url_prefix; ?>cart.php"><span class="glyphicon glyphicon-shopping-cart"></span><span class="label label-pill label-default cart_item_count"><?php echo cart::count_item()->total_item; ?></span></a></li>                
                <li title="Wait time for pick-up orders">
                    <a href="#" class="dropdown dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-time"></span><span class="label<?php if ($server->info->wait_time >= 60) {echo " label-danger";} elseif (($server->info->wait_time > 20) && ($server->info->wait_time < 60)) {echo " label-warning";} else {echo " label-default";}?>"><?php echo $server->info->wait_time; ?></span></a>
                    <ul class="dropdown-menu">
                        <li class="compensate_padding<?php if ($server->info->wait_time >= 60) {echo " danger";} elseif (($server->info->wait_time > 20) && ($server->info->wait_time < 60)) {echo " warning";} ?>"><?php echo $server->info->wait_time; ?> minutes wait time for pick-up orders</li>
                    </ul>
                </li>


                <li title="Settings">
                    <a href="#" class="dropdown dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-cog"></span><span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li title="Account Settings"><a href="<?php echo $server->status->url_prefix; ?>account.php">Account</a></li>
                        <li title="Order History"><a href="<?php echo $server->status->url_prefix; ?>history.php">Order History</a></li>
                        <li class="compensate_padding">Logged in as <?php echo $_SESSION["cust"]->info->name_first; ?></li>
                    </ul>
                </li>



                <li title="Logout"><a href="<?php echo $server->status->url_prefix; ?>login.php?action=logout"><span class="glyphicon glyphicon-log-out"></span></a></li>
            </ul>
        <?php } else { ?>
            <ul class="nav navbar-nav navbar-right">
                <li title="Login"><a href="<?php echo $server->status->url_prefix; ?>login.php"><span class="glyphicon glyphicon-log-in"></span> Login</a></li>
                <li title="Register"><a href="<?php echo $server->status->url_prefix; ?>register.php"><span class="glyphicon glyphicon-user"></span> Register</a></li>
            </ul>
        <?php } // end if cust is not logged in ?>

    </nav>
</div>

<!-- Displaying Alerts -->
<?php echo page::generate_alert($alert); ?>

<div class="script_output"></div>