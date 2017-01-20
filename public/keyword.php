<!doctype html>
<html lang="en">
<head>
<?php require_once("../_includes/header_addon.php"); ?>
<title>Keyword Search</title>
<!-- LESS/SASS SCRIPTS GO HERE IF NECCESSARY -->

<?php
	$page->enable_ajax();

	if (isset($_POST["submit"])) {
	    $keyword = $db->clean_input($_POST["keyword"]);
	    $result = food::search_food($db,$keyword,"any",false);
	    if (empty($result)) {$no_result = true;}
	}
?>
</head>
<body>
<?php include_once("../_includes/top_bar.php"); ?>
<div id="wrapper">

	<div class="container-fluid">
		<div class="row centered">
			<div class="form-group">
		        <form class="form-inline" action="keyword.php" method="post" target="_self">
		            <label>I'm looking for : 
		            <input class="form-control" name="keyword" type="text" value="" maxlength="30" />
		            </label>
		            <button class="btn btn-default" name="submit" type="submit" value="Search">Search</button>
		        </form>
	        </div>
	        <?php
	        	if (!isset($result)) {echo '<span class="glyphicon glyphicon-arrow-up"></span>';}
	        	elseif (isset($no_result)) {echo "<div class='alert alert-danger'>No result. Please try again.</div>";}
	        	else {
	        		foreach ($result as $current_food) {
	        			echo food::generate_gallery_thumbnail_with_caption($current_food->food_id,$current_food->food_name_en);
	        		}
	        	}
	        ?>
	    </div>
	</div>


	<!-- Modal -->
	<div id="food_preview" class="modal fade" role="dialog">
		<div class="modal-dialog">
		    <!-- Modal content-->
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal">&times;</button>
		        <h4 class="modal-title">Food Name</h4>
		      </div>
		      <div class="modal-body">
		      	<p class="food_description">Food Description</p>
		      	<img src="_images/loading.gif">
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		      </div>
		    </div>
		</div>
	</div>

	
</div> <!-- END WRAPPER HERE -->

<script type="text/javascript">
	$(document).ready(function(){

		$(".img-thumbnail").click(function(){
			reset_modal();
			var $food_id = $(this).attr("id");
			$food_id = $food_id.substr(8);

			// Do Ajax call for food info
			$.post("_ajax/food_info.php",{ "request_type" : "basic_info", "request[]" : ["food_name_en","description","available","category"], "food_id" : $food_id }, function(data){
				var $food = JSON.parse(data);
				$(".modal-title").text($food.info.food_name_en);
				$(".modal-body p").text($food.info.description);
			});

			var $food_url = "_images/menu/large/" + $food_id + ".jpg";
			// See if food picture exists, if not, use default picture
			$.ajax({
			    url: $food_url,
			    type:'HEAD',
			    error: function() {
			        $(".modal-body").removeAttr("style");
			        $(".modal-body img").attr("src", "_images/menu/large/default.jpg");
					$(".modal-body img").show();
			    },
			    success: function() {
			    	$(".modal-body").removeAttr("style");
			        $(".modal-body img").attr("src", $food_url);
			        $(".modal-body img").show();
			    }
			});
		});
	});
</script>

<?php include_once("../_includes/bottom_bar.php"); ?>
</body>
</html>