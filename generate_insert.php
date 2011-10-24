<?php
$lines = file("input_file_path.txt");
$sql_insert = "";
$indentation_changed = false;
$relationship = 0;
$index_of_last_parent = array();
$tree_nodes = array();
$prev_indentation_level = -1;

// $tree_nodes 0 = parent, 1 = node, 2 = left, 3 = right, 4 = indentation

for ($i = 0; $i <= count($lines); $i++) {
    $indentation_level = strspn($lines[$i],"\t");
	$indentation_changed = false;
	
	if ($indentation_level > $prev_indentation_level) {
		// Indentation increased
		$indentation_changed = true;
		$relationship++;
		$index_of_last_parent[$indentation_level] = $i-1;
	}
	if ($indentation_level < $prev_indentation_level) {
		// Indentation decreased
		$indentation_changed = true;
		$tree_nodes[$i-1][2] = $relationship;
		$relationship++;
		$tree_nodes[$i-1][3] = $relationship;
		$relationship++;
		
		$indentation_diff = $prev_indentation_level - $indentation_level;
		for ($j = $indentation_diff; $j > 0; $j--) {
			$tree_nodes[$index_of_last_parent[$indentation_level+$j]][3] = $relationship;
			$relationship++;
		}
	}
	
	if ($i != count($lines))
		$tree_nodes[] = array($indentation_level == 0 ? "" : $lines[$index_of_last_parent[$indentation_level]], $lines[$i], $relationship, 0, $indentation_level);
	
	if (!$indentation_changed) {
		// No change in indentation since the last node
		$tree_nodes[$i-1][2] = $relationship;
		$relationship++;
		$tree_nodes[$i-1][3] = $relationship;
		$relationship++;
	}

	$prev_indentation_level = $indentation_level;
}

$sql_insert .= "INSERT INTO `table_name` (parent, title, lft, rgt) VALUES (";

foreach ($tree_nodes as $value) {
	$sql_insert .= "('" . mysql_real_escape_string(trim($value[0])) . "','" . mysql_real_escape_string(trim($value[1])) . "'," . $value[2] . "," . $value[3] . "),";
}

echo substr($sql_insert,0,-1);
?>