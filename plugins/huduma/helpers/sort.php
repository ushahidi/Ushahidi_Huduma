<?php
/**
 * Sort helper for sorting data using a variety of sorting algorithms
 */
class sort_Core {
	
	/**
	 * Implements the merge sort algorithm. Time complexity is O(n log n)
	 *
	 * @param array $data Data array to be sorted
	 * @return mixed
	 */
	public static function merge_sort(array & $data)
	{
		if ( ! is_array($data) or count($data) <= 1)
			return FALSE;
		
		$left = array();
		$right = array();
		$result = array();
		
		// Get the mid point
		$middle = (count($data) % 2 == 0) ? count($data)/2 : (count($data)/2) + 1;
		$left = array_slice($data, 0, $middle);
		$right = array_slice($data, $middle-1, count($data)-$middle);
		
		self::merge_sort($left);
		self::merge_sort($right);
		
		return self::merge($left, $right);
	}
	
	/**
	 * Helper function for the merge sort algorithm
	 */
	private static function merge($left, $right)
	{
		$result = array();
		while (count($left) > 0 OR count($right) > 0)
		{
			$left_keys = array_keys($left);
			$right_keys = array_keys($right);
			
			if (count($left) > 0 AND count($right) > 0)
			{
				if
			}
			elseif (count($left) > 0)
			{
				
			}
			elseif (count($right) > 0)
			{
				
			}
		}
	}
}
?>