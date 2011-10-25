<?php
$lines = file("input_file_path.txt");
$sql_insert = "";
$relationship = 0;
$index_of_last_parent_at_level = array();
$tree_nodes = array();
$prev_indentation_level = -1;

for ($i = 0; $i <= count($lines); $i++) {
    $indentation_level = strspn($lines[$i],"\t");
	
	if ($indentation_level > $prev_indentation_level) {
		// Indentation increased
		$relationship++;
		$index_of_last_parent_at_level[$indentation_level] = $i-1;
	} else if ($indentation_level < $prev_indentation_level) {
		// Indentation decreased
		$tree_nodes[$i-1][2] = $relationship;
		$relationship++;
		$tree_nodes[$i-1][3] = $relationship;
		$relationship++;
		
		$indentation_diff = $prev_indentation_level - $indentation_level;
		for ($j = $indentation_diff; $j > 0; $j--) {
			$tree_nodes[$index_of_last_parent_at_level[$indentation_level+$j]][3] = $relationship;
			$relationship++;
		}
	} else {
		// No change in indentation since the last node
		$tree_nodes[$i-1][2] = $relationship;
		$relationship++;
		$tree_nodes[$i-1][3] = $relationship;
		$relationship++;
	}
	
	if ($i != count($lines))
		$tree_nodes[] = array($indentation_level == 0 ? "" : $lines[$index_of_last_parent_at_level[$indentation_level]], $lines[$i], $relationship, 0, $indentation_level);

	$prev_indentation_level = $indentation_level;
}

$sql_insert .= "INSERT INTO `table_name` (parent, title, lft, rgt) VALUES (";

foreach ($tree_nodes as $value) {
	$sql_insert .= "('" . mysql_real_escape_string(trim($value[0])) . "','" . mysql_real_escape_string(trim($value[1])) . "'," . $value[2] . "," . $value[3] . "),";
}

echo substr($sql_insert,0,-1);
?>