<?php
    namespace App\Models;
    use Core\Model;
    
    use Library\Database\DBNumber;
    use Library\Database\DBString;
    use Library\Database\DBDateTime;
    
    class OrderModel extends Model{
        const PAID = 1, UNPAID = 0;
        const CODPAY = 1, ONEPAYPAY = 2;
        const GHNTRANSPORTER = 1, GHTKTRANSPORTER = 2;
        
        const PAYCOMPLETE = 1, PAYINCOMPLETE = 0;
        
        #Trang thai don hang
        const NGUOI_MUA_DANG_THANH_TOAN = 1, NGUOI_MUA_THANH_TOAN_THAT_BAI = 2, CHO_NGUOI_BAN_XAC_NHAN = 3, HUY_DON_HANG = 4, KHONG_CON_HANG = 5, HUY_DO_HE_THONG = 6, DANG_GIAO = 7, GIAO_THAT_BAI = 8, DA_GIAO =9, HOAN_TAT = 10, CHO_LAY_HANG = 11, HUY_DO_KHONG_LAY_DUOC_HANG = 12, DON_HANG_DUOC_TAO = 13;
        
        public $id, $ordercode, $shop_id, $client_id, $status, $note, $created_time, $total_price, $ship_fee, $paid, $paycomplete, $clientname, $clientphone, $clientaddress, $clientwardname, $clientdistrictname, $clientprovincename, $shopname, $shopphone, $shopaddress, $shopwardname, $shopdistrictname, $shopprovincename, $paymenttype_id, $transporter_id, $weight, $length, $width, $height;
        
        #objects
        public $shop, $client, $paymenttype, $transporter;
        
        #contains objects
        public $orderitems, $orderimages, $orderlogs, $assessments, $onepayorder, $ghntransporter;
        
        
        public function checkNote(){
            if(!is_string($this->note) || mb_strlen($this->note) > 1024){
                $this->addErrorMessage('note', 'Ghi chú không hợp lệ, phải có chiều dài tối đa 1024 ký tự');
            }
            return $this;
        }
        
        public function loadData(){
            $rows = $this->database->selectall()->from(DB_TABLE_ORDER)->where('id=' . (int)$this->id)->execute();
            if(count($rows)){
                $row = $rows[0];
                #lazy load data via key and value map
                foreach($row as $col => $value){
                    $this->$col = $value;
                }
                $this->created_time = DBDateTime::parse($this->created_time);
                return true;
            }else{
                return false;
            }
        }
        
        public function loadFromOrderCode(){
            $rows = $this->database->selectall()->from(DB_TABLE_ORDER)->where('ordercode=' . (new DBString($this->ordercode))->SqlValue())->execute();
            if(count($rows)){
                $row = $rows[0];
                #lazy load data via key and value map
                foreach($row as $col => $value){
                    $this->$col = $value;
                }
                $this->created_time = DBDateTime::parse($this->created_time);
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
        
        public function loadTransporterUnit(){
            if($this->transporter_id == TransporterModel::GHN){
                return $this->loadGHNTransporter();
            }
            
            return false;
        }
        
        public function loadTransporter(){
            $this->transporter = new TransporterModel($this->database);
            $this->transporter->id = $this->transporter_id;
            
            return $this->transporter->loadData();
        }
        
        public function loadGHNTransporter(){
            $this->ghntransporter = new GHNTransporterModel($this->database);
            $this->ghntransporter->order_id = $this->id;
            
            return $this->ghntransporter->loadFromOrderId();
        }
        
        public function loadOnePayOrder(){
            $this->onepayorder = new OnePayOrderModel($this->database);
            $this->onepayorder->order_id = $this->id;
            
            return $this->onepayorder->loadFromOrderId();
        }
        
        public function loadOrderLogs(){
            $this->orderlogs = [];
            $rows = $this->database->select('id')->from(DB_TABLE_ORDERLOG)->where('order_id=' . (int)$this->id)->orderby('created_time asc, id asc')->execute();
            
            foreach($rows as $row){
                $orderlog = new OrderLogModel($this->database);
                $orderlog->id = $row->id;
                
                if(!$orderlog->loadData()){
                    return false;
                }
                
                $this->orderlogs[] = $orderlog;
            }
            
            return true;
        }
        
        public function loadOrderItems(){
            $this->orderitems = [];
            $rows = $this->database->select('order_id, product_id')->from(DB_TABLE_ORDERITEM)->where('order_id=' . (int)$this->id)->execute();
            foreach($rows as $row){
                $orderitem = new OrderItemModel($this->database);
                $orderitem->product_id = $row->product_id;
                $orderitem->order_id = $row->order_id;
                if(!$orderitem->loadData()){
                    return false;
                }
                $this->orderitems[] = $orderitem;
            }
            return true;
        }
        
        public function clientCanCancel(){
            $ac = [self::CHO_NGUOI_BAN_XAC_NHAN];
            return in_array($this->status, $ac);
        }
        
        public function clientCancel(){
            
        }
        
        public function shopCancel(){
            
        }
        
        public function getPaidString(){
            $name = [
                self::PAID => 'Đã thanh toán',
                self::UNPAID => 'Chưa thanh toán'
            ];
            
            return $name[$this->paid];
        }
        
        public function getClientFullAddress(){
            return $this->clientaddress . ', ' . $this->clientwardname . ', ' . $this->clientdistrictname . ', ' . $this->clientprovincename;
        }
        
        public function getTransporterOrderCode(){
            $ad = [self::HUY_DO_KHONG_LAY_DUOC_HANG, self::CHO_LAY_HANG, self::DANG_GIAO, self::GIAO_THAT_BAI, self::DA_GIAO, self::HOAN_TAT];
            if(in_array($this->status, $ad)){
                if($this->transporter->id == TransporterModel::GHN && $this->ghntransporter != null){
                    return $this->ghntransporter->ordercode;
                }else{
                    return "Không xác định được mã vận đơn";
                }
            }else{
                return "Chưa vận chuyển";
            }
        }
        
        public function getStatusString(){
            $name = [
                self::NGUOI_MUA_DANG_THANH_TOAN => 'Người mua đang thanh toán',
                self::NGUOI_MUA_THANH_TOAN_THAT_BAI => 'Người mua chưa thanh toán',
                self::CHO_NGUOI_BAN_XAC_NHAN => 'Chờ người bán xác nhận',
                self::HUY_DON_HANG => 'Đơn hàng bị người mua hủy',
                self::KHONG_CON_HANG => 'Không còn hàng',
                self::HUY_DO_HE_THONG => 'Hủy do hệ thống',
                self::DANG_GIAO => 'Đang giao hàng',
                self::GIAO_THAT_BAI => 'Giao thất bại',
                self::DA_GIAO => 'Đã giao',
                self::HOAN_TAT => 'Hoàn tất',
                self::CHO_LAY_HANG => 'Chờ lấy hàng',
                self::HUY_DO_KHONG_LAY_DUOC_HANG => 'Hủy do không lấy được hàng',
                self::DON_HANG_DUOC_TAO => 'Đơn hàng được tạo'
            ];
            
            return isset($name[$this->status]) ? $name[$this->status] : 'trạng thái không xác định';
        }
        
        public function getTransporterServiceName(){
            if($this->transporter_id == TransporterModel::GHN && $this->ghntransporter != null){
                return $this->ghntransporter->servicename;
            }
            
            return "Không rõ";
        }
        
        public function getUserOrderLink(){
            return "/User/Order?ordercode=" . $this->ordercode;
        }
        
        public function add(){
            $this->database->insert(DB_TABLE_ORDER, [
                'ordercode' => new DBString($this->database->escape($this->ordercode)),
                'shop_id' => new DBNumber($this->shop_id),
                'client_id' => new DBNumber($this->client_id),
                'status' => new DBNumber($this->status),
                'note' => new DBString($this->database->escape($this->note)),
                'total_price' => new DBNumber($this->total_price),
                'ship_fee' => new DBNumber($this->ship_fee),
                'paid' => new DBNumber($this->paid),
                'paycomplete' => new DBNumber($this->paycomplete),
                'clientname' => new DBString($this->database->escape($this->clientname)),
                'clientphone' => new DBString($this->database->escape($this->clientphone)),
                'clientaddress' => new DBString($this->database->escape($this->clientaddress)),
                'clientwardname' => new DBString($this->database->escape($this->clientwardname)),
                'clientdistrictname' => new DBString($this->database->escape($this->clientdistrictname)),
                'clientprovincename' => new DBString($this->database->escape($this->clientprovincename)),
                'shopname' => new DBString($this->database->escape($this->shopname)),
                'shopphone' => new DBString($this->database->escape($this->shopphone)),
                'shopaddress' => new DBString($this->database->escape($this->shopaddress)),
                'shopwardname' => new DBString($this->database->escape($this->shopwardname)),
                'shopdistrictname' => new DBString($this->database->escape($this->shopdistrictname)),
                'shopprovincename' => new DBString($this->database->escape($this->shopprovincename)),
                'paymenttype_id' => new DBNumber($this->paymenttype_id),
                'transporter_id' => new DBNumber($this->transporter_id),
                'weight' => new DBNumber($this->weight),
                'length' => new DBNumber($this->length),
                'width' => new DBNumber($this->width),
                'height' => new DBNumber($this->height)
            ]);
            
            $this->id = $this->database->lastInsertId();
            
            return true;
        }
        
        public function updateStatus($status){
            $this->database->update(DB_TABLE_ORDER, [
                'status' => new DBNumber($status)
            ], 'id=' . (int)$this->id);
            $this->status = $status;
            return true;
        }
        
        public function updatePayStatus($paid, $paycomplete){
            $this->database->update(DB_TABLE_ORDER, [
                'paid' => new DBNumber($paid),
                'paycomplete' => new DBNumber($paycomplete)
            ], 'id=' . (int)$this->id);
            
            return true;
        }
    }