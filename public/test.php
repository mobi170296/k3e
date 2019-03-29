<?php
	if(isset($_POST['content'])){
		

		$m = new mysqli('localhost', 'root', 'trinhvanlinh', 'test');
		$data = $m->real_escape_string($_POST['content']);
		if($m->query('insert into posts values(\'' . $data  . '\')')){
			echo 'Success';
		}else{
			echo 'Failed';
		}
	}else{
		$m = new mysqli('localhost', 'root', 'trinhvanlinh', 'test');
		$result = $m->query('select * from posts');
		$row = $result->fetch_assoc();
		if($row){
			echo $row['content'];
		}else{
			echo 'Không có nội dung ở bảng posts';
		}
	}
?>