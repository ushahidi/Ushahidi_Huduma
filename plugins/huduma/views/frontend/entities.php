<div id="content">
	<div class="content-bg">
		<div class="page-title"><h1><?php echo Kohana::lang('ui_huduma.entities').": ";?> </h1></div>
		<div style="clear:both;"></div>
		
		<!-- start reports block -->
		<div class="big-block">
			<div class="r_cat_tooltip"> <a href="#" class="r-3"></a> </div>

			<script type="text/javascript">
				$(document).ready(function(){
					$(".r_details:odd").css('background-color', '#E5F7FC');
				});
			</script>
			
			<div id="pageColRight">
			<div class="reports-box">
				<?php
				foreach ($entities as $entity)
				{
                    $entity_id = $entity->id;
                    $entity_name = preg_replace('/\b(\w)/e', 'ucfirst("$1")', strtolower($entity->entity_name));
                    $entity_type_color = $entity->static_entity_type->entity_type_color;
                    $entity_type_image = $entity->static_entity_type->entity_type_image;
                    $category_id = $entity->static_entity_type->category_id;
                    $category_title = $entity->static_entity_type->category->category_title;
                    $category_color = $entity->static_entity_type->category->category_color;
                    $category_image_thumb = $entity->static_entity_type->category->category_image_thumb;
					
					// TODO: Fetch the metadata and only display a portion of it
                    
                    $entity_thumb = url::base().((! empty($entity_type_image))
                        ? Kohana::config('upload.relative_directory')."/".$entity_type_image
                        : "swatch/?c=".$entity_type_color."&w=25&h=40");

                    $category_thumb = url::base().((! empty($category_image_thumb))
                        ? Kohana::config('upload.relative_directory')."/".$category_image_thumb
                        : "swatch/?c=".$category_color."&w=16&h=416");

					?>
					<div class="rb_report">

						<div class="r_media">
							<p class="r_photo">
                                <a href="<?php echo url::site(); ?>entities/view/<?php echo $entity_id; ?>"><img src="<?php echo $entity_thumb; ?>" /></a>
							</p>

							<div class="r_categories">
								<h4><?php echo Kohana::lang('ui_main.categories'); ?></h4>
                                <a class="r_category" href="<?php echo url::site(); ?>entities/?c=<?php echo $category_id; ?>">
                                    <span class="r_cat-box"> <img src="<?php echo $category_thumb; ?>" /></span><span class="r_cat-desc"><?php echo $category_title; ?></span>
                                </a>
							</div>

						</div>

						<div class="r_details">
							<h3>
                                <a class="r_title" href="<?php echo url::site(); ?>entities/view/<?php echo $entity_id; ?>"><?php echo $entity_name; ?></a>
                                <a href="<?php echo url::site(); ?>entities/view/<?php echo $entity_id; ?>#discussion" class="r_comments"><?php //echo $comment_count; ?></a>
                            </h3>
                            <!-- Metadata -->
                            <?php //if ( ! empty($metadata)): ?>
							<p style="padding: 3px;">
							</p>
							<?php //endif; ?>
                            <!-- /Metadata -->
                            <p class="r_location">
                            </p>
							<p class="r_date r-3 bottom-cap"><?php //echo $incident_date; ?></p>
							<div class="r_description"> <?php //echo $incident_description; ?> </div>
							<p class="r_location"><a href="<?php echo url::site(); ?>entities/?l=<?php //echo $location_id; ?>"><?php //echo $location_name; ?></a></p>
						</div>
					</div>
				<?php } ?>
			</div>
			<?php echo $pagination; ?>
			</div>
		</div>
		<!-- end reports block -->
	</div>
</div>