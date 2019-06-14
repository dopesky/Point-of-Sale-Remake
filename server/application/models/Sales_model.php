<?php !defined(BASEPATH) or die('No direct script access allowed');

class Sales_model extends CI_Model {

    //The constructor function
  	public function __construct(){
  		parent::__construct();
  	}

  	public function get_sale_by_id($sale_id, $check_active, $check_suspended){
  		$this->db->where('sales.sale_id',$sale_id);
  		if($check_active){
  			$this->db->where('sales.active', 1);
        $this->db->where('payments.active', 1);
  		}
  		if($check_suspended){
  			$this->db->where('sales.suspended', 0);
        $this->db->where('payments.suspended', 0);
  		}
      $this->db->join('payments', 'payments.payment_id = sales.payment_id');
  		return $this->db->get('sales')->row();
  	}

  	public function add_sale($data){
  		return $this->db->insert('sales',$data) ? $this->db->insert_id() : false;
  	}

    public function add_sales_batch($batch){
      return $this->db->insert_batch('sales',$batch);
    }

  	public function update_sale($sale_id, $data, $payment_data){
      $payment_id = $payment_data['payment_id'];
      unset($payment_data['payment_id']);
      $this->db->trans_start();
      $this->db->where('payment_id',$payment_id)->update('payments', $payment_data);
      $this->db->where('sale_id', $sale_id)->update('sales',$data);
      $this->db->trans_complete();
      return $this->db->trans_status();
  	}

    public function disable_enable_sale($sale_id, $data){
      return $this->db->where('sale_id', $sale_id)->update('sales',$data);
    }

    public function get_sales_by_owner_id($owner_id, $check_active, $check_suspended){
      $this->db->select('sale_details.*');
      $this->db->where('sale_details.owner_id', $owner_id);
      if($check_suspended){
        $this->db->where('sale_details.suspended', 0);
        $this->db->where('sale_details.payment_suspended', 0);
      }
      if($check_active){
        $this->db->where('sale_details.active', 1);
      }
      $this->db->join('products', 'products.product_id = sale_details.product_id');
      $this->db->where('sale_details.owner_suspended', 0);
      $this->db->where('sale_details.owner_active', 1);
      $this->db->where('products.suspended', 0);
      return $this->db->get('sale_details')->result();
    }

    public function get_products_for_sale($owner_id, $check_active, $check_suspended){
      $this->db->where('owner_id', $owner_id);
      if($check_suspended){
        $this->db->where('suspended', 0);
      }
      if($check_active){
        $this->db->where('active', 1);
      }
      $this->db->where('owner_suspended', 0);
      $this->db->where('owner_active', 1);
      $this->db->where('inventory_level > ', 0);
      return $this->db->get('inventory_summary')->result();
    }
}