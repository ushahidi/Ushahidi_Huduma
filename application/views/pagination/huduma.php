<?php 
/**
 * pagination view.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Huduma - https://github.com/ushahidi/Ushahidi_Huduma
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

/**
 * Ushahidi_Huduma pagination style
 * 
 * @preview  <<Prev 1 … 4 5 6 7 8 … 15 Next>>
 */
?>

	<p class="pagination">
	
		<?php $prev_page_label = ucfirst(strtolower(substr(Kohana::lang('pagination.previous'), 0, 4))); ?>
		
		<?php if ($previous_page): ?>
			<a href="<?php echo str_replace('{page}', $previous_page, $url) ?>">&lt;&nbsp;<?php echo $prev_page_label ?></a>
		<?php else: ?>
			&lt;&nbsp;<?php echo $prev_page_label; ?>
		<?php endif ?>


		<?php if ($total_pages < 13): /* « Previous  1 2 3 4 5 6 7 8 9 10 11 12  Next » */ ?>

			<?php for ($i = 1; $i <= $total_pages; $i++): ?>
				<?php if ($i == $current_page): ?>
					<span class="current"><?php echo $i ?></span>
				<?php else: ?>
					<span><a href="<?php echo str_replace('{page}', $i, $url) ?>"><?php echo $i ?></a></span>
				<?php endif ?>
			<?php endfor ?>

		<?php elseif ($current_page < 9): /* « Previous  1 2 3 4 5 6 7 8 9 10 … 25 26  Next » */ ?>

			<?php for ($i = 1; $i <= 10; $i++): ?>
				<?php if ($i == $current_page): ?>
					<span class="current"><?php echo $i ?></span>
				<?php else: ?>
					<span><a href="<?php echo str_replace('{page}', $i, $url) ?>"><?php echo $i ?></a></span>
				<?php endif ?>
			<?php endfor ?>

			&hellip;
			<span><a href="<?php echo str_replace('{page}', $total_pages - 1, $url) ?>"><?php echo $total_pages - 1 ?></a></span>
			<span><a href="<?php echo str_replace('{page}', $total_pages, $url) ?>"><?php echo $total_pages ?></a></span>

		<?php elseif ($current_page > $total_pages - 8): /* « Previous  1 2 … 17 18 19 20 21 22 23 24 25 26  Next » */ ?>

			<span><a href="<?php echo str_replace('{page}', 1, $url) ?>">1</a></span>
			<span><a href="<?php echo str_replace('{page}', 2, $url) ?>">2</a></span>
			&hellip;

			<?php for ($i = $total_pages - 9; $i <= $total_pages; $i++): ?>
				<?php if ($i == $current_page): ?>
					<span class="current"><?php echo $i ?></span>
				<?php else: ?>
					<span><a href="<?php echo str_replace('{page}', $i, $url) ?>"><?php echo $i ?></a></span>
				<?php endif ?>
			<?php endfor ?>

		<?php else: /* « Previous  1 2 … 5 6 7 8 9 10 11 12 13 14 … 25 26  Next » */ ?>

			<span><a href="<?php echo str_replace('{page}', 1, $url) ?>">1</a></span>
			<span><a href="<?php echo str_replace('{page}', 2, $url) ?>">2</a></span>
			&hellip;

			<?php for ($i = $current_page - 5; $i <= $current_page + 5; $i++): ?>
				<?php if ($i == $current_page): ?>
					<span class="current"><?php echo $i ?></span>
				<?php else: ?>
					<span><a href="<?php echo str_replace('{page}', $i, $url) ?>"><?php echo $i ?></a></span>
				<?php endif ?>
			<?php endfor ?>

			&hellip;
			<span><a href="<?php echo str_replace('{page}', $total_pages - 1, $url) ?>"><?php echo $total_pages - 1 ?></a></span>
			<span><a href="<?php echo str_replace('{page}', $total_pages, $url) ?>"><?php echo $total_pages ?></a></span>

		<?php endif ?>


		<?php if ($next_page): ?>
			<a href="<?php echo str_replace('{page}', $next_page, $url) ?>"><?php echo ucfirst(strtolower(Kohana::lang('pagination.next'))) ?>&nbsp;&gt;</a>
		<?php else: ?>
			<?php echo ucfirst(strtolower(Kohana::lang('pagination.next'))) ?>&nbsp;&gt;
		<?php endif ?>

	</p>