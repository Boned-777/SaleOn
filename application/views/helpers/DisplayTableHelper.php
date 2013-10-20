<?php
class Zend_View_Helper_DisplayTableHelper extends Zend_View_Helper_Abstract {

    public function displayTableHelper(Zend_Db_Table_Rowset_Abstract $rowset, $columnsList=NULL, $links=NULL) {
        $table = "";
        if(count($rowset)>0) {
            $table .= '<table class="table-striped table-bordered table-condensed"><thead><tr>';
			
			$fieldsList = array();
            foreach(array_keys($rowset->current()->toArray()) as $column) {
            	if (is_null($columnsList))
                	$table .= '<th>'.$column.'</th>';
				else
					if (isset($columnsList[$column])) {
						$table .= '<th>'.$columnsList[$column].'</th>';
						$fieldsList[] = $column;
					}
            }
			$table .= "</tr></thead><tbody>";
			
			$tr = "";
            foreach($rowset as $row) {
                if (is_null($links))
					$table .= $tr.'<tr>';
				else
                	$table .= $tr.'<tr onclick="show(' . "'" . $links["template"].$row[$links["source"]] . "'" . ')" style="cursor: pointer;">';
				if (is_null($columnsList))
                	foreach($row->toArray() as $content)
	                	$table .= '<td>'.$content.'</td>';
				else {
					foreach ($fieldsList as $value) {
						$table .= '<td>'.$row->$value.'</td>';
					}
				}
				$tr = "</tr>";
            }
            $table .='</tr></tbody></table>';
       }
       return $table;
    }
}
?>