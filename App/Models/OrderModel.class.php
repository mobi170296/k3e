<?php
    namespace App\Models;
    use Core\Model;
    
    class OrderModel extends Model{
        #Trang thai don hang
        const NGUOI_MUA_DANG_THANH_TOAN = 1, NGUOI_MUA_THANH_TOAN_THAT_BAI = 2, CHO_NGUOI_BAN_XAC_NHAN = 3, HUY_DON_HANG = 4, KHONG_CON_HANG = 5, HUY_DO_HE_THONG = 6, DANG_GIAO = 7, GIAO_THAT_BAI = 8, DA_GIAO =9, HOAN_TAT = 10;
        
        
        public $id, $shop_id, $client_id, $status, $note, $created_time, $total_price, $ship_fee, $paid, $paycomplete, $clientname, $clientphone, $clientaddress, $clientwardname, $clientdistrictname, $clientprovincename, $shopname, $shopphone, $shopaddress, $shopwardname, $shopdistrictname, $shopprovincename, $paymenttype_id, $transporter_id;
        
        #objects
        public $shop, $client, $paymenttype, $transporter;
        
        #contains objects
        public $orderitems, $orderimages, $orderlogs, $assessments, $onepayorder, $ghntransporter;
        
        public function loadData(){
            $rows = $this->database->selectall()->from(DB_TABLE_ORDER)->where('id=' . (int)$this->id)->execute();
            if(count($rows)){
                $row = $rows[0];
                #lazy load data via key and value map
                foreach($row as $col => $value){
                    $this->$col = $value;
                }
                return true;
            }else{
                return false;
            }
        }
        
        public function loadShop(){
            $this->shop = new ShopModel($this->database);
            $this->shop->id = $this->shop_id;
            return $this->shop->loadData();
        }
        
        public function loadClient(){
            $this->client = new UserModel($this->database);
            $this->client->id = $this->client_id;
            return $this->client->loadData();
        }
        
        public function loadPaymentType(){
            $this->paymenttype = new PaymentTypeModel($this->database);
            $this->paymenttype->id = $this->paymenttype_id;
            
            return $this->paymenttype->loadData();
        }
        
        public function getStatusString(){
            switch($this->status){
                case self::NGUOI_MUA_DANG_THANH_TOAN:
                    return "Người mua đang thanh toán";
                case self::NGUOI_MUA_THANH_TOAN_THAT_BAI:
                    return "Người mua chưa thanh toán";
                case self::CHO_NGUOI_BAN_XAC_NHAN:
                    return "Chờ người bán xác nhận";
                case self::HUY_DON_HANG:
                    return "Hủy đơn hàng";
                case self::KHONG_CON_HANG:
                    return "Không còn hàng";
                case self::HUY_DO_HE_THONG:
                    return "Hủy do hệ thống";
                case self::DANG_GIAO:
                    return "Đang giao";
                case self::GIAO_THAT_BAI:
                    return "Giao hàng thất bại";
                case self::DA_GIAO:
                    return "Đã giao";
                case self::HOAN_TAT:
                    return "Hoàn tất";
                default:
                    return "Trạng thái không xác định";
            }
        }
        
        public function getOrderLink(){
            return "";
        }
        
        
    }