<?php
/**
 * Service providers js file.
 *
 * Handles javascript stuff related  to api log function
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     API Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
?>

<?php require SYSPATH.'../application/views/admin/form_utils_js.php' ?>

	// Ajax Submission
	function dashboardRoleAction( action, confirmAction, dashboard_role_id )
	{
		var statusMessage;

		if ( !isChecked( "dashboard_role" ) && dashboard_role_id =='' && action !='n' ) {
			alert('Please select at least one role.');
		} else {
			var answer = confirm('<?php echo Kohana::lang('ui_admin.are_you_sure_you_want_to'); ?> ' + confirmAction + '?')
			if (answer){
				// Set Submit Type
				$("#action").attr("value", action);

				if (dashboard_role_id != '')
				{
					// Submit Form For Single Item
					$("#dashboard_role_single").attr("value", dashboard_role_id);
					$("#dashboardRolesMain").submit();
				}
				else
				{
					// Set Hidden form item to 000 so that it doesn't return server side error for blank value
					$("#dashboard_role_single").attr("value", "000");

					// Submit Form For Multiple Items
					$("#dashboardRolesMain").submit();
				}

			} else {
				return false;
			}
		}
	}
	
	// Displays the dialog for adding/editing a dashboard role
	function showDashboardRoleDialog(dashboard_role_id) {
	
		// Display the dialog
		$("#facebox .content").attr("class", "content body");
		$("#facebox").css("display", "block");
		
		$.facebox(function(){
			jQuery.get('<?php echo url::site().'admin/dashboard/roles/get/'?>'+unescape(dashboard_role_id),
				function(data){
					// Set the dialog data
					jQuery.facebox(data);

					// Attach events to the save and close buttons
					$('.btn_save_close').live('click', function() {
						// Get the input data
						var data = {
							id: $("#role_id").val(),
							name: $("#role_name").val(),
							description: $("#role_description").val(),
							agency_id: $("#role_agency_id").val(),
							action: $("#role_action").val(),
							static_entity_id: $("#role_static_entity_id").val(),
							boundary_id: $("#role_administrative_boundary_id").val(),
							category_id: $("#role_category_id").val()
						}
						
						// Post the data
						$.post('<?php echo url::site().'admin/dashboard/roles/save'?>',
							data,
							function(response){
								if (response.success) {
									// Show the message
									$("#message_green_box").css("display", "block");
									$("#message_green_box").css("class", "green-box");

									$("#message_green_box").html("<h3>"+response.message+"</h3>");

									// Close the dialog
									setTimeout(function(){
											$.facebox.close;

											// Redirect to landing page
											window.location.replace('<?php echo url::site().'admin/dashboard/roles'; ?>');
										},
										300
									);

								} else {
									$("#message_red_box").css("display", "block");
									$("#message_green_box").css("class", "red-box");
									
									$("#message_redbox_box").html("<h3>"+response.message+"</h3>");
								}
							}
						);
					});

					// onChange event handler for the agency dropdown
					$("#role_agency_id").change(function() {
						// Get the currently selected value
						var value = $(this).val();

						// Validation
						if (value != 0) {
							// Hide the category selector
							$("#role-category-selector").hide('slow');
						} else if (value == 0) {
							// Show the category selector
							$("#role-category-selector").show('slow');
						}

						// Fetch the entities
						fetchEntities({ agency_id: value, category_id: 0});


					});

					// onChange event handler for the category dropdown
					$("#role_category_id").change(function(){

						// Fetch the entities
						fetchEntities({ agency_id: 0, category_id: $(this).val() });
					});

				});
			});
	}

	// Fetches entities via JSON and populates the entities dropdown
	function fetchEntities(data) {
		// Clear the items from the static entity dropdown
		$("#role_static_entity_id").html("");
		
		// Fetch the new items
		$.post(
			'<?php echo url::site().'admin/dashboard/roles/entities'?>',
			data,
			function(response) {
				if (response.success) {
					var htmlStr = "<option value=\"0\">---<?php echo Kohana::lang('ui_huduma.select_entity'); ?>---</option>";

					$.each(response.data, function(id, value){
						htmlStr += "<option value=\""+id+"\">"+value+"</option>";
					});

					// Populate the dropdown
					$("#role_static_entity_id").html(htmlStr);
				}
			},
			'json'
		);
	}
	