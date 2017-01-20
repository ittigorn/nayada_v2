<!doctype html>
<html lang="en">
<head>
<?php require_once("../_includes/header_addon.php"); ?>
<title>Frequently Asked Questions</title>
<!-- LESS/SASS SCRIPTS GO HERE IF NECCESSARY -->
</head>
<body>
<?php include_once("../_includes/top_bar.php"); ?>
<div id="wrapper">
    <div class="container-fluid">
        <div class="row faq_container">
            <div class="col-lg-4">
                <section class="thumbnail thumbnail_equal">
                    <h3>Online ordering</h3>
                        <blockquote>
                            <h4>What is the preparation time for take-out orders?</h4>
                                <blockquote>
                                    <p>Normally, about 20 minutes unless stated otherwise. Preparation time may increase  depending on the size of the order and the predefined delay set by the manager.</p>
                                </blockquote>
                        </blockquote>
                        <blockquote>
                            <h4>How can I cancel my order?</h4>
                                <blockquote>
                                    <p>Please call the restaurant directly at 1 ( 562 ) 860 - 6108 to cancel your order. The manager will assist you through cancelling process. An order that has been placed for more than 15 minutes cannot be cancelled or refunded as stated in our <a href="terms.php#refund" target="_blank">Terms of Use</a>.</p>
                                </blockquote>
                        </blockquote>
                        <blockquote>
                            <h4>There is something wrong with my food!</h4>
                                <blockquote>
                                    <p>Please call the restaurant directly at 1 ( 562 ) 860 - 6108. The manager will assist you through the process.</p>
                                </blockquote>
                        </blockquote>
                        <blockquote>
                            <h4>Do you take delivery order online?</h4>
                                <blockquote>
                                    <p>We do not. We only take delivery orders over the phone.<br />Delivery time could vary greatly due to other factors at the moment the order is placed. <br />Since not all factors can be observed by our web server therefore we cannot give you a precise estimate of the delivery time online.</p>
                                </blockquote>
                        </blockquote>
                </section>
            </div>
            <div class="col-lg-4">
                <section class="thumbnail thumbnail_equal">
                    <h3>Facility &amp; Service</h3>
                        <blockquote>
                            <h4>Do you deliver?</h4>
                                <blockquote>
                                    <p>Yes, we do!<br />Place your order over the phone at <a href="contact.php">1 (562) 860-6108</a></p>
                                </blockquote>
                        </blockquote>
                        <blockquote>
                            <h4>Do you take a reservation?</h4>
                                <blockquote>
                                    <p>Yes, we do!<br />Call us at <a href="contact.php">1 (562) 860 - 6108</a></p>
                                </blockquote>
                        </blockquote>
                        <blockquote>
                            <h4>Is your facility good for a big party?</h4>
                                <blockquote>
                                    <p>Yes, it is.<br />We can accommodate a group of up to 30 people.<br />Availability might vary so we suggest you give us a call at <a href="contact.php">1 (562) 860 - 6108</a></p>
                                </blockquote>
                        </blockquote>
                </section>
            </div>
            <div class="col-lg-4">
                <section class="thumbnail thumbnail_equal">
                    <h3>Food &amp; drinks</h3>
                    <blockquote>
                        <h4>Do you have any vegetarian dish?</h4>
                        <blockquote>
                            <p>We can make most of our dishes strictly vegetarian without using any chicken broth, fish sauce or other animal product.<br />Ask your server for more information.</p>
                        </blockquote>
                    </blockquote>
                    <blockquote>
                        <h4>What kind of wine do you have?</h4>
                        <blockquote>
                            <p>Sweet Wine</p>
                            <ul>
                                <li>Plum wine</li>
                                <li>Moscato</li>
                            </ul>
                            <p>Semi Sweet</p>
                            <ul>
                                <li>White Zinfandel</li>
                            </ul>
                            <p>Dry Wine</p>
                            <ul>
                                <li>Merlot</li>
                                <li>Cabernet</li>
                                <li>Chardonnay</li>
                                <li>Malbec</li>
                            </ul>
                            <p>Sparkling Wine</p>
                            <ul>
                                <li>Stella Rosa</li>
                            </ul>
                        </blockquote>
                    </blockquote>
                </section>
            </div>
        </div>
        <div class="row faq_container">
            <div class="col-lg-12">
                <section class="thumbnail terms">
                    <blockquote>
                        <p><a href="privacy_policy.php" target="_blank">Privacy Policy</a></p> 
                    </blockquote>
                    <blockquote>
                        <p><a href="terms.php" target="_blank">Terms of Use</a></p>
                    </blockquote>
                </section>
            </div>
        </div>
    </div> <!-- END CONTAINER-FLUID -->
</div> <!-- END WRAPPER HERE -->

<script type="text/javascript">
    //Hide answers on ready
    $(document).ready(function() {
        
        $("#wrapper h4").next().hide();
        $(".thumbnail_equal>blockquote").css('cursor','pointer');
        $(".thumbnail_equal>blockquote").css('border-top','rgba(255,255,255,0) thin solid');
        $(".thumbnail_equal>blockquote").css('border-right','rgba(255,255,255,0) thin solid');
        $(".thumbnail_equal>blockquote").css('border-bottom','rgba(255,255,255,0) thin solid');
        
        $(".thumbnail_equal>blockquote").hover(
            function(){
                $(this).css('border-top','#b30000 thin solid');
                $(this).css('border-right','#b30000 thin solid');
                $(this).css('border-bottom','#b30000 thin solid');
        },
            function(){
                $(this).css('border-top','rgba(255,255,255,0) thin solid');
                $(this).css('border-right','rgba(255,255,255,0) thin solid');
                $(this).css('border-bottom','rgba(255,255,255,0) thin solid');
        });
        
        $(".thumbnail_equal>blockquote").click(function(){
            $("#wrapper h4").next().hide();
            $(this).children("blockquote").toggle(400,recalculate());
        });

        equalHeight($(".thumbnail_equal"));
    });// end document ready


    // Thumbnail equal height function
    function equalHeight(group) {    
        var tallest = 0;    
        group.each(function() {       
            var thisHeight = $(this).height();       
            if(thisHeight > tallest) {          
                tallest = thisHeight;       
            }    
        });    
        group.each(function() { $(this).height(tallest); });
    }

    function recalculate(){
        $(".thumbnail_equal").removeAttr("style");
    }// end function

    $(window).resize(function() {
        $(".thumbnail_equal").removeAttr("style");
        equalHeight($(".thumbnail_equal"));
    });






</script>

<?php include_once("../_includes/bottom_bar.php"); ?>
</body>
</html>