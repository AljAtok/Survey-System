<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main_model extends CI_Model {

	public function get_data($tbl, $where=null, $row=FALSE, $select=null, $order=FALSE){

		if($where != null){
			$this->db->where($where);
		}

		if($select != null){
			$this->db->select($select);
		}

		if($order != FALSE){
			$this->db->order_by($order);
		}

		$query = $this->db->get($tbl);


		if($row == TRUE){
			$result_data = $query->row();	
		}else{
			$result_data= $query->result();
		}
		return $result_data;
	}

	public function insert_data($tbl, $set, $id=FALSE){
		$this->db->trans_start();

		$this->db->set($set);
		$this->db->insert($tbl);
		$insert_id = $this->db->insert_id();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			return FALSE;
		}else{
			$this->db->trans_commit();
			if ($id == TRUE) {
				$result['result'] = TRUE;
				$result['id'] = $insert_id;
				return $result;
			} else {
				return TRUE;
			}
		}	
	}

	public function update_data($tbl, $set, $where){
		$this->db->trans_start();

		$this->db->set($set);
		$this->db->where($where);
		$this->db->update($tbl);

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			return FALSE;
		}else{
			$this->db->trans_commit();
			return TRUE;
		}	
	}

	public function check_data($tbl, $where, $row=FALSE, $select=FALSE){

		if($select != FALSE){
			$this->db->select($select);
		}

		$query = $this->db->get_where($tbl, $where);

		$result = $query->num_rows();

		if($result > 0){
			if($row == TRUE){
				$data['info'] = $query->row();
				$data['result'] = TRUE;
			}else{
				$data = TRUE;
			}
			
		}else{
			if($row == TRUE){
				$data['result'] = FALSE;
			}else{

				$data = FALSE;
			}
			
		}
		return $data;
	}

	public function get_join($tbl, $join, $row_type=FALSE, $order=FALSE, $group=FALSE, $select=FALSE, $where=FALSE, $string = FALSE){

		// foreach($join as $row=>$value){
		// 	$this->db->join($row, $value);	
		// }

		foreach($join as $row=>$value){
			if(strpos($row, ', ')){
				$split_row = explode(', ', $row);
				$join_tbl = $split_row[0];
				$join_type = $split_row[1];

				$this->db->join($join_tbl, $value, $join_type);
			}else{
				$this->db->join($row, $value);
			}
		}

		if($select != FALSE){
			$this->db->select($select);
		}
		
		if($group != FALSE){
			$this->db->group_by($group);
		}

		if($order != FALSE){
			$this->db->order_by($order);
		}

		if($where != FALSE){
			$this->db->where($where);
		}

		$query = $this->db->get($tbl);
		
		if($string){
			return $this->db->last_query();
		}

		if($row_type === FALSE){
			$result = $query->result();
		}else{
			$result = $query->row();
		}
		return $result;
	}

	public function get_query($sql_query, $row_type=FALSE){

		$query = $this->db->query($sql_query);
		if($row_type === FALSE){
			$result = $query->result();
		}else{
			$result = $query->row();
		}
		return $result;
	}

	public function check_join($tbl, $join, $row_type=FALSE, $order=FALSE, $group=FALSE, $select=FALSE){

		foreach($join as $row=>$value){
			$this->db->join($row, $value);	
		}
		
		if($select != FALSE){
			$this->db->select($select);
		}
		
		if($group != FALSE){
			$this->db->group_by($group);
		}

		if($order != FALSE){
			$this->db->order_by($order);
		}

		$query = $this->db->get($tbl);
		$num_rows = $query->num_rows();
		if($row_type == FALSE){

			if($num_rows > 0){
				return TRUE;
			}else{
				return FALSE;
			}
		}else{

			if($num_rows > 0){
				$result['result'] = TRUE;

				$result['info'] = $query->row();
				return $result;
			}else{
				$result['result'] = FALSE;
				return $result;
			}
		}
		
	}

	public function check_query($query, $row_data=FALSE){
		$query = $this->db->query($query);

		$num = $query->num_rows();
		if($row_data == FALSE){
			
			if($num > 0){
				return TRUE;
			}else{
				return FALSE;
			}
		}else{
			if($num > 0){
				$data['result'] = TRUE;	
				$data['info'] = $query->row();
			}else{
				$data['result'] = FALSE;	
			}
			
		}

		return $data;
	}

	public function get_count($tbl, $where=null){
		
		if($where != null){
			$this->db->where($where);	
		}
		
		$query = $this->db->get($tbl);

		$num = $query->num_rows();
		return $num;
	}

	public function get_join_datatables($tbl, $join, $row_type=FALSE, $order=FALSE, $group=FALSE, $select=FALSE, $where=FALSE, $string = false){

		foreach($join as $row=>$value){
			$this->db->join($row, $value);	
		}

		if($select != FALSE){
			$this->db->select($select);
		}
		
		if($group != FALSE){
			$this->db->group_by($group);
		}

		if($order != FALSE){
			$this->db->order_by($order);
		}

		if($where != FALSE){
			$this->db->where($where);
		}

		$query = $this->db->get($tbl);
		if($row_type === FALSE){
			$result = $query;
		}else{
			$result = $query->row();
		}
		if($string){
			return $this->db->last_query();
		} else {
			return $result;
		}
	}

	public function delete_data($tbl, $where)
	{
		$this->db->where($where);
		$this->db->delete($tbl);
		
		if ($this->db->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}
}