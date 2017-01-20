<!doctype html>
<html lang="en">
<head>
<?php require_once("../_includes/header_addon.php"); ?>
<title>Photo Gallery</title>
<!-- LESS/SASS SCRIPTS GO HERE IF NECCESSARY -->

<?php
	$page->enable_ajax();
	$gallery_file_list = scandir("_images/menu/gallery");
?>

</head>
<body>
<?php include_once("../_includes/top_bar.php"); ?>
<div id="wrapper">
	<div class="container-fluid">

	<?php 
	foreach ($gallery_file_list as $file_name) {
		if (($file_name !== ".") && ($file_name !== "..")) {
			echo food::generate_gallery_thumbnail($file_name);
		}// end if
	}// end foreach
	?>
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

		function reset_modal(){
			$("h4.modal-title").text("");
			$(".modal-body p.food_description").text("");
			$("h4.modal-title").removeAttr("style");
			$(".modal-body img").hide();
			$(".modal-body").css({"background-image" : "url(_images/loading.gif)", "background-size" : "auto", "height" : "200px"});
		}// end function

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
			$.get($food_url,function(){
				$(".modal-body").removeAttr("style");
				$(".modal-body img").attr("src", $food_url);
				$(".modal-body img").show();
			});
		});
	});
</script>

<?php include_once("../_includes/bottom_bar.php"); ?>
</body>
</html>