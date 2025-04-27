<?php
	/**
	 * Created by PhpStorm.
	 * User: oc_dev
	 */
	class ModelCatalogWork extends Model {

		public function getCat($parent){
			$q = $this->db->query("SELECT * FROM j_cat WHERE parent_id = '".$parent."' ORDER by name");


			return $q->rows;
		}

		public function getCat2($parent){
			$q = $this->db->query("SELECT * FROM j_cat_vladex WHERE parent_id = '".$parent."' ORDER by name");


			return $q->rows;
		}

	}
