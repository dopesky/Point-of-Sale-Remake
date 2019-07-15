<?php !defined(BASEPATH) or die('No direct script access allowed');

class Purchases_model extends CI_Model {

    //The constructor function
  	public function __construct(){
  		parent::__construct();
  	}

  	public function get_purchase_by_id($purchase_id, $check_active, $check_suspended){
  		$this->db->where('purchases.purchase_id',$purchase_id);
  		if($check_active){
  			$this->db->where('purchases.active', 1);
        $this->db->where('payments.active', 1);
  		}
  		if($check_suspended){
  			$this->db->where('purchases.suspended', 0);
        $this->db->where('payments.suspended', 0);
  		}
      $this->db->join('payments', 'payments.payment_id = purchases.payment_id');
  		return $this->db->get('purchases')->row();
  	}

  	public function add_purchase($data){
  		return $this->db->insert('purchases',$data) ? $this->db->insert_id() : false;
  	}

    public function add_purchases_batch($batch){
      return $this->db->insert_batch('purchases',$batch);
    }

  	public function update_purchase($purchase_id, $data, $payment_data){
      $payment_id = $payment_data['payment_id'];
      unset($payment_data['payment_id']);
      $this->db->trans_start();
      $this->db->where('payment_id',$payment_id)->update('payments', $payment_data);
      $this->db->where('purchase_id', $purchase_id)->update('purchases',$data);
      $this->db->trans_complete();
      return $this->db->trans_status();
  	}

    public function disable_enable_purchase($purchase_id, $data){
      return $this->db->where('purchase_id', $purchase_id)->update('purchases',$data);
    }

    public function get_purchases_by_owner_id($owner_id, $check_active, $check_suspended){
      $this->db->select('purchase_details.*');
      $this->db->where('purchase_details.owner_id', $owner_id);
      if($check_suspended){
        $this->db->where('purchase_details.suspended', 0);
        $this->db->where('purchase_details.payment_suspended', 0);
      }
      if($check_active){
        $this->db->where('purchase_details.active', 1);
      }
      $this->db->join('products', 'products.product_id = purchase_details.product_id');
      $this->db->where('purchase_details.owner_suspended', 0);
      $this->db->where('purchase_details.owner_active', 1);
      $this->db->where('products.suspended', 0);
      $this->db->order_by("purchase_details.purchase_id", "ASC");
      return $this->db->get('purchase_details')->result();
    }

    public function get_products_for_purchase($owner_id, $check_active, $check_suspended){
      $this->db->where('owner_id', $owner_id);
      if($check_suspended){
        $this->db->where('suspended', 0);
      }
      if($check_active){
        $this->db->where('active', 1);
      }
      $this->db->where('owner_suspended', 0);
      $this->db->where('owner_active', 1);
      $this->db->order_by('product', "ASC");
      return $this->db->get('inventory_summary')->result();
    }
}