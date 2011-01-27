<?php
/**
 * Boundary type view page.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Huduma Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
?>
<div
	class="bg">
<h2>
	<?php huduma_tool::huduma_subtabs("boundary_types"); ?>
</h2>
<?php
if ($form_error) {
	?> <!-- red-box -->
<div class="red-box">
<h3>Error</h3>
<ul>
<?php
foreach ($errors as $error_item => $error_description)
{
	print (!$error_description) ? '' : "<li>" . $error_description . "</li>";
}
?>
</ul>
</div>
<?php
}

if ($form_saved) {
	?> <!-- green-box -->
<div class="green-box">
<h3><?php echo Kohana::lang('huduma.boundary_type');?> <?php echo $form_action; ?></h3>
</div>
	<?php
}
?> <!-- report-table -->
<div class="report-form"><?php print form::open(NULL,array('id' =>'boundarytypeListing','name' => 'boundarytypeListing')); ?> 
<input type="hidden" name="action" id="action" value=""> 
<input type="hidden" name="boundary_type_id" id="boundary_type_id_action" value="">
<div class="table-holder">
<table class="table">
	<thead>
		<tr>
			<th>&nbsp;</th>
			<th><?php echo Kohana::lang('huduma.boundary_type');?></th>
			<th class="col-4"><?php echo Kohana::lang('huduma.actions');?></th>
		</tr>
	</thead>
	<tfoot>
		<tr class="foot">
			<td colspan="4"><?php echo $pagination; ?></td>
		</tr>
	</tfoot>
	<tbody>
	<?php
	if ($total_items == 0)
	{
		?>
		<tr>
			<td colspan="4" class="col">
			<h3><?php echo Kohana::lang('huduma.no_results');?></h3>
			</td>
		</tr>
		<?php
	}
	foreach ($boundary_types as $boundary_type)
	{
		$boundary_type_id = $boundary_type->id;
		$boundary_type_name = $boundary_type->boundary_type_name;
		?>
		<tr>
			<td class="col-1">&nbsp;</td>
			<td class="col-2">
			<div class="post">
			<h4><?php echo $boundary_type_name; ?></h4>
			</div>
			</td>
			<td class="col-4">
			<ul>
				<li class="none-separator"><a href="#add"
					onClick="fillFields('<?php echo(rawurlencode($boundary_type_id)); ?>','<?php echo(rawurlencode($boundary_type_name)); ?>')">Edit</a>
				<li><a href="javascript:catAction('d','DELETE','<?php echo(rawurlencode($boundary_type_id)); ?>')" class="del">Delete</a></li>
			
			</ul>
			</td>
		</tr>
		<?php

	}
	?>
	</tbody>
</table>
</div>
	<?php print form::close(); ?></div>

<!-- tabs -->
<div class="tabs"><!-- tabset --> <a name="add"></a>
<ul class="tabset">
	<li><a href="#" class="active">ADD/EDIT</a></li>
</ul>
<!-- tab -->
<div class="tab"><?php print form::open(NULL,array('enctype' => 'multipart/form-data', 'id' => 'boundarytypeMain', 'name' => 'boundarytypeMain')); ?> 
<input type="hidden" id="boundary_type_id" name="boundary_type_id" value="" /> 
<input type="hidden" name="action" id="action" value="a" />
<div class="tab_form_item"><?php echo Kohana::lang('huduma.boundary_type');?><br />
<?php print form::input('boundary_type_name', '', ' class="text"'); ?></div>
 <div class="tab_form_item">
<strong><?php echo Kohana::lang('huduma.parent_category');?>:</strong><br />
<?php print form::dropdown('parent_id', $parents_array, '0'); ?></div>
<div style="clear: both"></div>
<div class="tab_form_item">&nbsp;<br />
<input type="image" src="<?php echo url::base() ?>media/img/admin/btn-save.gif"
	class="save-rep-btn" value="SAVE" /></div>
<?php print form::close(); ?></div>
</div>
</div>
