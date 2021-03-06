<?
$_TITLE = "Administrasi Data BHP";
Class BHP {

	function list_data($hal = 0) {
		$_SESSION[setting_bhp][hal] = $hal;
		$objResponse = new xajaxResponse();
		$paging = new MyPagina;
		$paging->rows_on_page = 20;
		$paging->sql = "SELECT id,  nama, biaya, bhp_p, bhp_rs, bhp_rs_adm, bhp_rs_op FROM bhp ORDER BY nama";
		$paging->hal = $hal;
		$paging->get_page_result();

		$data = $paging->data;
		$no = $paging->start_number();
		$navi = $paging->navi();

		$tabel = new Table;
		$tabel->tbody_height = 430;
		$tabel->addTh("No", "BHP", "Biaya", "bhp p", "bhp rs", "bhp rs adm", "bhp rs op", "Hapus");
		//$tabel->addExtraTh("style=\"width: 50px;\"", "", " style=\"width: 70px;\"");

		for($i=0;$i<sizeof($data);$i++) {
			$tabel->addRow(
				($no+$i), 
				$data[$i][nama], 
				$data[$i][biaya], 
				$data[$i][bhp_p], 
				$data[$i][bhp_rs], 
				$data[$i][bhp_rs_adm], 
				$data[$i][bhp_rs_op], 
				"<a href=\"javascript:void(0)\" title=\"Hapus\" onclick=\"hapus_bhp('".$data[$i][id]."', this)\" class=\"tombol_hapus\"><img src=\"".IMAGES_URL."remove.png\" alt=\"Hapus\" border=\"0\" /></a>");
			$tabel->addOnclickTd(
				"xajax_get_bhp('".$data[$i][id]."')", 
				"xajax_get_bhp('".$data[$i][id]."')", 
				"xajax_get_bhp('".$data[$i][id]."')", 
				"xajax_get_bhp('".$data[$i][id]."')", 
				"xajax_get_bhp('".$data[$i][id]."')", 
				"xajax_get_bhp('".$data[$i][id]."')", 
				"xajax_get_bhp('".$data[$i][id]."')"
			);
		}

		$buka = $tabel->build();
		$objResponse->addAssign("list_data", "innerHTML", $buka);
		$objResponse->addAssign("navi", "innerHTML", $navi);
		return $objResponse;
	}

	function get_bhp($id) {
		$kon = new Konek;
		$kon->sql = "SELECT id, nama, biaya, bhp_p, bhp_rs, bhp_rs_adm, bhp_rs_op FROM bhp WHERE id = '".$id."'";
		$kon->execute();
		$data = $kon->getOne();
		$objResponse = new xajaxResponse();
		$objResponse->addAssign("id_bhp", "value", $data[id]);
		$objResponse->addAssign("nama", "value", $data[nama]);
		$objResponse->addAssign("biaya", "value", $data[biaya]);
		$objResponse->addAssign("bhp_p", "value", $data[bhp_p]);
		$objResponse->addAssign("bhp_rs", "value", $data[bhp_rs]);
		$objResponse->addAssign("bhp_rs_adm", "value", $data[bhp_rs_adm]);
		$objResponse->addAssign("bhp_rs_op", "value", $data[bhp_rs_op]);
		$objResponse->addScriptCall("fokus", "nama");
		return $objResponse;
	}

	function hapus_bhp($id) {
		$kon = new Konek;
		$kon->sql = "DELETE FROM bhp WHERE id = '".$id."'";
		$kon->execute();
		$ret = $kon->affected_rows;
		$objResponse = new xajaxResponse();
		if($ret<0) {
			$objResponse->addAlert("Data Tidak Dapat Dihapus.");
		} else {
			$objResponse->addScriptCall("xajax_list_data", $_SESSION[setting_bhp][hal]);
			$objResponse->addScriptCall("fokus", "nama");
		}
		return $objResponse;
	}

	function simpan_bhp($value) {
		$kon = new Konek;
		if(!$value['id_bhp'])
			$kon->sql = "INSERT INTO bhp(nama, biaya, bhp_p, bhp_rs, bhp_rs_adm, bhp_rs_op) VALUES ('".$value[nama]."', '".$value[biaya]."', '".$value[bhp_p]."', '".$value[bhp_rs]."', '".$value[bhp_rs_adm]."', '".$value[bhp_rs_op]."')";
		else 
			$kon->sql = "UPDATE bhp SET nama = '".$value[nama]."', biaya = '".$value[biaya]."', bhp_p = '".$value[bhp_p]."', bhp_rs = '".$value[bhp_rs]."', bhp_rs_adm = '".$value[bhp_rs_adm]."', bhp_rs_op = '".$value[bhp_rs_op]."' WHERE id = '".$value[id_bhp]."'";
		$kon->execute();
		$objResponse = new xajaxResponse();
		$objResponse->addScriptCall("show_status_simpan");
		$objResponse->addScriptCall("xajax_list_data", $_SESSION[setting_bhp][hal]);
		$objResponse->addScriptCall("xajax_reset_bhp");
		return $objResponse;
	}

	function simpan_bhp_check($value) {
		$objResponse = new xajaxResponse();
		$value[nama] = addslashes(trim($value[nama]));
		if(!$value[nama])
			$objResponse->addAlert("Silakan Isi Nama BHP.");
		else 
			$objResponse->addScriptCall("xajax_simpan_bhp", $value);
		$objResponse->addScriptCall("fokus", "nama");
		return $objResponse;
	}

	function reset_bhp () {
		$objResponse = new xajaxResponse();
		$objResponse->addClear("id_bhp", "value");
		$objResponse->addClear("nama", "value");
		$objResponse->addClear("biaya", "value");
		$objResponse->addClear("bhp_p", "value");
		$objResponse->addClear("bhp_rs", "value");
		$objResponse->addClear("bhp_rs_adm", "value");
		$objResponse->addClear("bhp_rs_op", "value");
		$objResponse->addScriptCall("fokus", "nama");
		return $objResponse;
	}
}


//$_xajax->debugOn();
$_xajax->registerFunction(array("list_data", "BHP", "list_data"));
$_xajax->registerFunction(array("get_bhp", "BHP", "get_bhp"));
$_xajax->registerFunction(array("hapus_bhp", "BHP", "hapus_bhp"));
$_xajax->registerFunction(array("simpan_bhp", "BHP", "simpan_bhp"));
$_xajax->registerFunction(array("simpan_bhp_check", "BHP", "simpan_bhp_check"));
$_xajax->registerFunction(array("reset_bhp", "BHP", "reset_bhp"));


?>